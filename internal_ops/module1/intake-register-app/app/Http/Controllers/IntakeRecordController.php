<?php

namespace App\Http\Controllers;

use App\Models\IntakeRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class IntakeRecordController extends Controller
{
    /**
     * @var string[]
     */
    private const TRIAGE_STATUSES = [
        'Untriaged',
        'Accept',
        'Conditional Accept',
        'Pause',
        'Decline',
    ];

    /**
     * @var array<string, string[]>
     */
    private const ALLOWED_STATUS_TRANSITIONS = [
        'Untriaged' => ['Accept', 'Conditional Accept', 'Pause', 'Decline'],
        'Conditional Accept' => ['Accept', 'Pause', 'Decline'],
        'Pause' => ['Conditional Accept', 'Accept', 'Decline'],
        'Accept' => ['Conditional Accept', 'Pause', 'Decline'],
        'Decline' => ['Pause', 'Conditional Accept', 'Accept'],
    ];

    /**
     * @var string[]
     */
    private const MATERIAL_REOPEN_FIELDS = [
        'system_class',
        'diagnostic_objective',
        'scope_boundary_summary',
        'evidence_a_summary',
        'evidence_b_summary',
        'evidence_c_summary',
        'evidence_d_summary',
        'constraints_sensitivity_availability',
        'requester_cannot_share',
    ];

    /**
     * @var string[]
     */
    private const ACCEPTANCE_BOUNDARY_PATTERNS = [
        '/\b(optimi[sz](?:e|ation|ing|ed|er)?|tuning|tune|prompt(?:\s|-)?engineering|parameter(?:\s|-)?advice|performance(?:\s|-)?improv(?:e|ement|ing))\b/i',
        '/\b(consult(?:ing)?|implement(?:ation|ing|ed)?|deploy(?:ment|ing|ed)?|integration)\b/i',
        '/\b(predict(?:ion|ive|ing)?|forecast(?:ing)?)\b/i',
        '/\b(model(?:\s|-)?internal|weights?|activations?|hidden\s+state|training\s+data)\b/i',
        '/\b(compliance|certif(?:ication|y)|audit)\b/i',
    ];

    public function index(Request $request): View
    {
        $filters = $this->parseQueueFilters($request);

        return view('intake_records.queue', [
            'records' => $this->buildQueueQuery($filters)->get(),
            'triageStatuses' => self::TRIAGE_STATUSES,
            'filters' => $filters,
        ]);
    }

    public function exportFilteredCsv(Request $request): StreamedResponse
    {
        $filters = $this->parseQueueFilters($request);
        $records = $this->buildQueueQuery($filters)->get();

        $columns = [
            'intake_id',
            'created_at_utc',
            'created_by',
            'requester_identity',
            'requester_contact_channel',
            'system_class',
            'diagnostic_objective',
            'scope_boundary_summary',
            'evidence_a_summary',
            'evidence_b_summary',
            'evidence_c_summary',
            'evidence_d_summary',
            'constraints_sensitivity_availability',
            'requester_cannot_share',
            'triage_status',
            'triage_rationale',
            'missing_information_notes',
            'exclusion_reason',
            'updated_at_utc',
            'updated_by',
        ];

        $filename = 'intake_queue_export_' . now()->utc()->format('Ymd_His') . '_utc.csv';

        return response()->streamDownload(
            static function () use ($records, $columns): void {
                $handle = fopen('php://output', 'wb');
                if ($handle === false) {
                    return;
                }

                fputcsv($handle, $columns);
                foreach ($records as $record) {
                    $row = [];
                    foreach ($columns as $column) {
                        $row[] = (string) ($record->{$column} ?? '');
                    }

                    fputcsv($handle, $row);
                }
                fclose($handle);
            },
            $filename,
            [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            ]
        );
    }

    public function create(): View
    {
        $now = now()->utc()->format('Y-m-d H:i:s');

        return view('intake_records.form', [
            'isEdit' => false,
            'record' => new IntakeRecord(),
            'formData' => [
                'triage_status' => 'Untriaged',
                'created_at_utc' => $now,
                'updated_at_utc' => $now,
            ],
            'triageStatuses' => self::TRIAGE_STATUSES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePayload($request, true);
        $this->enforceStatusAndBoundaryGuards($validated, null);

        $record = IntakeRecord::create($validated);

        return redirect()
            ->route('intake-records.show', $record)
            ->with('status', 'Intake record created.');
    }

    public function show(IntakeRecord $intakeRecord): View
    {
        return view('intake_records.show', [
            'record' => $intakeRecord,
        ]);
    }

    public function edit(IntakeRecord $intakeRecord): View
    {
        return view('intake_records.form', [
            'isEdit' => true,
            'record' => $intakeRecord,
            'formData' => $intakeRecord->getAttributes(),
            'triageStatuses' => self::TRIAGE_STATUSES,
        ]);
    }

    public function update(Request $request, IntakeRecord $intakeRecord): RedirectResponse
    {
        $validated = $this->validatePayload($request, false);
        $this->enforceStatusAndBoundaryGuards($validated, $intakeRecord);

        $intakeRecord->fill($validated);
        $intakeRecord->save();

        return redirect()
            ->route('intake-records.show', $intakeRecord)
            ->with('status', 'Intake record updated.');
    }

    public function updateStatus(Request $request, IntakeRecord $intakeRecord): RedirectResponse
    {
        $validated = $request->validate([
            'triage_status' => ['required', 'in:' . implode(',', self::TRIAGE_STATUSES)],
            'triage_rationale' => ['nullable', 'string', 'required_unless:triage_status,Untriaged'],
            'missing_information_notes' => ['nullable', 'string', 'required_if:triage_status,Conditional Accept,Pause'],
            'exclusion_reason' => ['nullable', 'string', 'required_if:triage_status,Decline'],
            'updated_by' => ['required', 'string', 'max:255'],
            'filter_triage_status' => ['nullable', 'in:' . implode(',', self::TRIAGE_STATUSES)],
            'filter_q' => ['nullable', 'string', 'max:255'],
            'filter_created_by' => ['nullable', 'string', 'max:255'],
            'filter_updated_by' => ['nullable', 'string', 'max:255'],
            'filter_created_at_from' => ['nullable', 'date'],
            'filter_created_at_to' => ['nullable', 'date', 'after_or_equal:filter_created_at_from'],
            'filter_updated_at_from' => ['nullable', 'date'],
            'filter_updated_at_to' => ['nullable', 'date', 'after_or_equal:filter_updated_at_from'],
        ]);

        $candidate = array_merge(
            $intakeRecord->getAttributes(),
            [
                'triage_status' => $validated['triage_status'],
                'triage_rationale' => $validated['triage_rationale'] ?? null,
                'missing_information_notes' => $validated['missing_information_notes'] ?? null,
                'exclusion_reason' => $validated['exclusion_reason'] ?? null,
                'updated_by' => $validated['updated_by'],
                'updated_at_utc' => now()->utc()->format('Y-m-d H:i:s'),
            ]
        );

        $this->enforceStatusAndBoundaryGuards($candidate, $intakeRecord);

        $intakeRecord->fill([
            'triage_status' => $validated['triage_status'],
            'triage_rationale' => $validated['triage_rationale'] ?? null,
            'missing_information_notes' => $validated['missing_information_notes'] ?? null,
            'exclusion_reason' => $validated['exclusion_reason'] ?? null,
            'updated_by' => $validated['updated_by'],
            'updated_at_utc' => now()->utc()->format('Y-m-d H:i:s'),
        ]);
        $intakeRecord->save();

        return redirect()
            ->route(
                'intake-records.index',
                array_filter([
                    'triage_status' => $validated['filter_triage_status'] ?? null,
                    'q' => $validated['filter_q'] ?? null,
                    'created_by' => $validated['filter_created_by'] ?? null,
                    'updated_by' => $validated['filter_updated_by'] ?? null,
                    'created_at_from' => $validated['filter_created_at_from'] ?? null,
                    'created_at_to' => $validated['filter_created_at_to'] ?? null,
                    'updated_at_from' => $validated['filter_updated_at_from'] ?? null,
                    'updated_at_to' => $validated['filter_updated_at_to'] ?? null,
                ], static fn ($value): bool => $value !== null && $value !== '')
            )
            ->with('status', 'Triage status updated.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatePayload(Request $request, bool $isCreate): array
    {
        $rules = [
            'created_at_utc' => ['required', 'date'],
            'created_by' => ['required', 'string', 'max:255'],
            'requester_identity' => ['required', 'string', 'max:255'],
            'requester_contact_channel' => ['required', 'string', 'max:255'],
            'system_class' => ['required', 'string', 'max:255'],
            'diagnostic_objective' => ['required', 'string'],
            'scope_boundary_summary' => ['required', 'string'],
            'evidence_a_summary' => ['nullable', 'string'],
            'evidence_b_summary' => ['nullable', 'string'],
            'evidence_c_summary' => ['nullable', 'string'],
            'evidence_d_summary' => ['nullable', 'string'],
            'constraints_sensitivity_availability' => ['nullable', 'string'],
            'requester_cannot_share' => ['nullable', 'string'],
            'triage_status' => ['required', 'in:' . implode(',', self::TRIAGE_STATUSES)],
            'triage_rationale' => ['nullable', 'string', 'required_unless:triage_status,Untriaged'],
            'missing_information_notes' => ['nullable', 'string', 'required_if:triage_status,Conditional Accept,Pause'],
            'exclusion_reason' => ['nullable', 'string', 'required_if:triage_status,Decline'],
            'updated_at_utc' => ['required', 'date'],
            'updated_by' => ['required', 'string', 'max:255'],
        ];

        if ($isCreate) {
            $rules['intake_id'] = ['required', 'string', 'max:255', 'unique:intake_records,intake_id'];
        }

        return $request->validate($rules);
    }

    /**
     * @param array<string, mixed> $candidate
     *
     * @throws ValidationException
     */
    private function enforceStatusAndBoundaryGuards(array $candidate, ?IntakeRecord $existingRecord): void
    {
        $errors = [];
        $nextStatus = (string) ($candidate['triage_status'] ?? '');
        $previousStatus = $existingRecord?->triage_status;
        $isTransition = $existingRecord !== null && $previousStatus !== $nextStatus;

        if ($isTransition) {
            $allowedTargets = self::ALLOWED_STATUS_TRANSITIONS[$previousStatus] ?? [];
            if (!in_array($nextStatus, $allowedTargets, true)) {
                $errors['triage_status'][] = "Transition from {$previousStatus} to {$nextStatus} is not allowed.";
            }

            if ($this->isBlank($candidate['triage_rationale'] ?? null)) {
                $errors['triage_rationale'][] = 'triage_rationale is required for any status transition.';
            }

            if ($this->isBlank($candidate['updated_by'] ?? null)) {
                $errors['updated_by'][] = 'updated_by is required for any status transition.';
            }

            if ($this->isBlank($candidate['updated_at_utc'] ?? null)) {
                $errors['updated_at_utc'][] = 'updated_at_utc is required for any status transition.';
            }
        }

        if (
            $existingRecord !== null
            && $previousStatus === 'Decline'
            && $nextStatus === 'Accept'
            && !$this->hasMaterialNewIntakeInformation($existingRecord, $candidate)
        ) {
            $errors['triage_status'][] = 'Decline to Accept requires material new intake information to be recorded.';
        }

        if ($nextStatus === 'Accept' && $this->violatesAcceptanceBoundary($candidate)) {
            $errors['triage_status'][] = 'Accept cannot be selected when objective/scope contains explicit non-diagnostic intent (optimization, implementation, prediction, model-internal inspection, or compliance/certification activity).';
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }
    }

    /**
     * @param array<string, mixed> $candidate
     */
    private function violatesAcceptanceBoundary(array $candidate): bool
    {
        $objective = (string) ($candidate['diagnostic_objective'] ?? '');
        $scope = (string) ($candidate['scope_boundary_summary'] ?? '');
        $combined = $objective . "\n" . $scope;

        foreach (self::ACCEPTANCE_BOUNDARY_PATTERNS as $pattern) {
            if ($this->containsNonNegatedPattern($combined, $pattern)) {
                return true;
            }
        }

        return false;
    }

    private function containsNonNegatedPattern(string $text, string $pattern): bool
    {
        if (!preg_match_all($pattern, $text, $matches, PREG_OFFSET_CAPTURE)) {
            return false;
        }

        foreach ($matches[0] as $match) {
            $matched = $match[0];
            $offset = $match[1];
            $start = max(0, $offset - 32);
            $length = strlen((string) $matched) + 64;
            $context = strtolower(substr($text, $start, $length));

            if (!preg_match('/\b(no|not|without|exclude(?:d|s|ing)?|non)\b/i', $context)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<string, mixed> $candidate
     */
    private function hasMaterialNewIntakeInformation(IntakeRecord $existingRecord, array $candidate): bool
    {
        foreach (self::MATERIAL_REOPEN_FIELDS as $field) {
            $previousValue = $this->normalizeForCompare($existingRecord->{$field});
            $newValue = $this->normalizeForCompare($candidate[$field] ?? null);

            if ($previousValue !== $newValue) {
                return true;
            }
        }

        return false;
    }

    private function isBlank(mixed $value): bool
    {
        return trim((string) ($value ?? '')) === '';
    }

    private function normalizeForCompare(mixed $value): string
    {
        $normalized = strtolower(trim((string) ($value ?? '')));
        return preg_replace('/\s+/', ' ', $normalized) ?? '';
    }

    /**
     * @return array{
     *     triage_status: string,
     *     q: string,
     *     created_by: string,
     *     updated_by: string,
     *     created_at_from: string,
     *     created_at_to: string,
     *     updated_at_from: string,
     *     updated_at_to: string
     * }
     */
    private function parseQueueFilters(Request $request): array
    {
        $validated = $request->validate([
            'triage_status' => ['nullable', 'in:' . implode(',', self::TRIAGE_STATUSES)],
            'q' => ['nullable', 'string', 'max:255'],
            'created_by' => ['nullable', 'string', 'max:255'],
            'updated_by' => ['nullable', 'string', 'max:255'],
            'created_at_from' => ['nullable', 'date'],
            'created_at_to' => ['nullable', 'date', 'after_or_equal:created_at_from'],
            'updated_at_from' => ['nullable', 'date'],
            'updated_at_to' => ['nullable', 'date', 'after_or_equal:updated_at_from'],
        ]);

        return [
            'triage_status' => (string) ($validated['triage_status'] ?? ''),
            'q' => (string) ($validated['q'] ?? ''),
            'created_by' => (string) ($validated['created_by'] ?? ''),
            'updated_by' => (string) ($validated['updated_by'] ?? ''),
            'created_at_from' => (string) ($validated['created_at_from'] ?? ''),
            'created_at_to' => (string) ($validated['created_at_to'] ?? ''),
            'updated_at_from' => (string) ($validated['updated_at_from'] ?? ''),
            'updated_at_to' => (string) ($validated['updated_at_to'] ?? ''),
        ];
    }

    /**
     * @param array{
     *     triage_status: string,
     *     q: string,
     *     created_by: string,
     *     updated_by: string,
     *     created_at_from: string,
     *     created_at_to: string,
     *     updated_at_from: string,
     *     updated_at_to: string
     * } $filters
     */
    private function buildQueueQuery(array $filters): Builder
    {
        $recordsQuery = IntakeRecord::query()
            ->orderByDesc('updated_at_utc')
            ->orderBy('intake_id');

        if ($filters['triage_status'] !== '') {
            $recordsQuery->where('triage_status', $filters['triage_status']);
        }

        if ($filters['created_by'] !== '') {
            $recordsQuery->where('created_by', $filters['created_by']);
        }

        if ($filters['updated_by'] !== '') {
            $recordsQuery->where('updated_by', $filters['updated_by']);
        }

        if ($filters['created_at_from'] !== '') {
            $recordsQuery->where('created_at_utc', '>=', $filters['created_at_from'] . ' 00:00:00');
        }

        if ($filters['created_at_to'] !== '') {
            $recordsQuery->where('created_at_utc', '<=', $filters['created_at_to'] . ' 23:59:59');
        }

        if ($filters['updated_at_from'] !== '') {
            $recordsQuery->where('updated_at_utc', '>=', $filters['updated_at_from'] . ' 00:00:00');
        }

        if ($filters['updated_at_to'] !== '') {
            $recordsQuery->where('updated_at_utc', '<=', $filters['updated_at_to'] . ' 23:59:59');
        }

        if ($filters['q'] !== '') {
            $term = trim($filters['q']);
            $like = '%' . $term . '%';

            $recordsQuery->where(function (Builder $query) use ($like): void {
                $query
                    ->where('intake_id', 'like', $like)
                    ->orWhere('requester_identity', 'like', $like)
                    ->orWhere('diagnostic_objective', 'like', $like)
                    ->orWhere('scope_boundary_summary', 'like', $like)
                    ->orWhere('created_by', 'like', $like)
                    ->orWhere('updated_by', 'like', $like);
            });
        }

        return $recordsQuery;
    }
}
