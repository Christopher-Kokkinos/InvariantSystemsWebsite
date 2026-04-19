<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $isEdit ? 'Edit Intake Record' : 'Create Intake Record' }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2rem; max-width: 980px; }
        h1 { margin-bottom: 0.25rem; }
        p.meta { color: #444; margin-top: 0; }
        .banner { background: #f6f8fa; border: 1px solid #d0d7de; padding: 0.75rem; margin-bottom: 1rem; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .field { display: flex; flex-direction: column; margin-bottom: 0.75rem; }
        .field label { font-weight: 600; margin-bottom: 0.35rem; }
        .field input, .field textarea, .field select { padding: 0.45rem; font-size: 0.95rem; }
        .field textarea { min-height: 100px; resize: vertical; }
        .full { grid-column: 1 / -1; }
        .errors { color: #b91c1c; margin: 0.75rem 0; }
        .actions { margin-top: 1rem; display: flex; gap: 0.75rem; align-items: center; }
        @media (max-width: 800px) { .grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <h1>{{ $isEdit ? 'Edit Intake Record' : 'Create Intake Record' }}</h1>
    <p class="meta">Internal pre-operational module. Localhost-only, non-public.</p>

    @if (session('status'))
        <div class="banner">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="errors">
            <strong>Please correct the following:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $value = static function (string $field, string $fallback = '') use ($formData): mixed {
            return old($field, $formData[$field] ?? $fallback);
        };
    @endphp

    <form method="post" action="{{ $isEdit ? route('intake-records.update', $record) : route('intake-records.store') }}">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="grid">
            <div class="field">
                <label for="intake_id">Intake ID</label>
                <input
                    id="intake_id"
                    name="intake_id"
                    type="text"
                    value="{{ $value('intake_id') }}"
                    @if($isEdit) readonly @endif
                    required
                >
            </div>
            <div class="field">
                <label for="triage_status">Triage Status</label>
                <select id="triage_status" name="triage_status" required>
                    @foreach ($triageStatuses as $status)
                        <option value="{{ $status }}" @selected($value('triage_status', 'Untriaged') === $status)>{{ $status }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label for="created_at_utc">Created At UTC</label>
                <input id="created_at_utc" name="created_at_utc" type="text" placeholder="YYYY-MM-DD HH:MM:SS" value="{{ $value('created_at_utc') }}" required>
            </div>
            <div class="field">
                <label for="created_by">Created By</label>
                <input id="created_by" name="created_by" type="text" value="{{ $value('created_by') }}" required>
            </div>

            <div class="field">
                <label for="requester_identity">Requester Identity</label>
                <input id="requester_identity" name="requester_identity" type="text" value="{{ $value('requester_identity') }}" required>
            </div>
            <div class="field">
                <label for="requester_contact_channel">Requester Contact Channel</label>
                <input id="requester_contact_channel" name="requester_contact_channel" type="text" value="{{ $value('requester_contact_channel') }}" required>
            </div>

            <div class="field">
                <label for="system_class">System Class</label>
                <input id="system_class" name="system_class" type="text" value="{{ $value('system_class') }}" required>
            </div>
            <div class="field">
                <label for="updated_by">Updated By</label>
                <input id="updated_by" name="updated_by" type="text" value="{{ $value('updated_by') }}" required>
            </div>

            <div class="field">
                <label for="updated_at_utc">Updated At UTC</label>
                <input id="updated_at_utc" name="updated_at_utc" type="text" placeholder="YYYY-MM-DD HH:MM:SS" value="{{ $value('updated_at_utc') }}" required>
            </div>

            <div class="field full">
                <label for="diagnostic_objective">Diagnostic Objective</label>
                <textarea id="diagnostic_objective" name="diagnostic_objective" required>{{ $value('diagnostic_objective') }}</textarea>
            </div>
            <div class="field full">
                <label for="scope_boundary_summary">Scope Boundary Summary</label>
                <textarea id="scope_boundary_summary" name="scope_boundary_summary" required>{{ $value('scope_boundary_summary') }}</textarea>
            </div>

            <div class="field full">
                <label for="evidence_a_summary">Evidence A Summary</label>
                <textarea id="evidence_a_summary" name="evidence_a_summary">{{ $value('evidence_a_summary') }}</textarea>
            </div>
            <div class="field full">
                <label for="evidence_b_summary">Evidence B Summary</label>
                <textarea id="evidence_b_summary" name="evidence_b_summary">{{ $value('evidence_b_summary') }}</textarea>
            </div>
            <div class="field full">
                <label for="evidence_c_summary">Evidence C Summary</label>
                <textarea id="evidence_c_summary" name="evidence_c_summary">{{ $value('evidence_c_summary') }}</textarea>
            </div>
            <div class="field full">
                <label for="evidence_d_summary">Evidence D Summary</label>
                <textarea id="evidence_d_summary" name="evidence_d_summary">{{ $value('evidence_d_summary') }}</textarea>
            </div>
            <div class="field full">
                <label for="constraints_sensitivity_availability">Constraints (Sensitivity/Availability)</label>
                <textarea id="constraints_sensitivity_availability" name="constraints_sensitivity_availability">{{ $value('constraints_sensitivity_availability') }}</textarea>
            </div>
            <div class="field full">
                <label for="requester_cannot_share">Requester Cannot Share</label>
                <textarea id="requester_cannot_share" name="requester_cannot_share">{{ $value('requester_cannot_share') }}</textarea>
            </div>
            <div class="field full">
                <label for="triage_rationale">Triage Rationale</label>
                <textarea id="triage_rationale" name="triage_rationale">{{ $value('triage_rationale') }}</textarea>
            </div>
            <div class="field full">
                <label for="missing_information_notes">Missing Information Notes</label>
                <textarea id="missing_information_notes" name="missing_information_notes">{{ $value('missing_information_notes') }}</textarea>
            </div>
            <div class="field full">
                <label for="exclusion_reason">Exclusion Reason</label>
                <textarea id="exclusion_reason" name="exclusion_reason">{{ $value('exclusion_reason') }}</textarea>
            </div>
        </div>

        <div class="actions">
            <button type="submit">{{ $isEdit ? 'Save Changes' : 'Create Record' }}</button>
            <a href="{{ route('intake-records.index') }}">Back to Queue</a>
            @if ($isEdit)
                <a href="{{ route('intake-records.show', $record) }}">Back to Detail</a>
            @endif
            @if (!$isEdit)
                <a href="{{ route('intake-records.create') }}">Reset</a>
            @endif
        </div>
    </form>
</body>
</html>
