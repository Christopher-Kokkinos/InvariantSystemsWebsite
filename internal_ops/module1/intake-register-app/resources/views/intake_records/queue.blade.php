<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Intake Queue</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2rem; max-width: 1240px; }
        h1 { margin-bottom: 0.25rem; }
        p.meta { color: #444; margin-top: 0; }
        .banner { background: #f6f8fa; border: 1px solid #d0d7de; padding: 0.75rem; margin-bottom: 1rem; }
        .errors { color: #b91c1c; margin: 0.75rem 0; }
        .filters { display: flex; gap: 0.75rem; align-items: end; margin-bottom: 1rem; flex-wrap: wrap; }
        .filters label { display: flex; flex-direction: column; font-weight: 600; gap: 0.25rem; }
        input, select, button { padding: 0.4rem; font-size: 0.95rem; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #d0d7de; padding: 0.55rem; vertical-align: top; text-align: left; }
        th { background: #f6f8fa; }
        .row-actions { display: flex; gap: 0.5rem; margin-top: 0.35rem; flex-wrap: wrap; }
        .status-form { display: grid; gap: 0.35rem; min-width: 260px; }
        .status-form input, .status-form select { width: 100%; }
        .small { font-size: 0.85rem; color: #444; }
    </style>
</head>
<body>
    <h1>Intake Queue</h1>
    <p class="meta">Internal pre-operational queue. Localhost-only, non-public.</p>

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

    <form class="filters" method="get" action="{{ route('intake-records.index') }}">
        <label>
            Triage Status
            <select name="triage_status">
                <option value="">All</option>
                @foreach ($triageStatuses as $status)
                    <option value="{{ $status }}" @selected($filters['triage_status'] === $status)>{{ $status }}</option>
                @endforeach
            </select>
        </label>
        <label>
            Search
            <input type="text" name="q" value="{{ $filters['q'] }}" placeholder="ID, requester, created_by, updated_by">
        </label>
        <label>
            Created By
            <input type="text" name="created_by" value="{{ $filters['created_by'] }}" placeholder="created_by">
        </label>
        <label>
            Updated By
            <input type="text" name="updated_by" value="{{ $filters['updated_by'] }}" placeholder="updated_by">
        </label>
        <label>
            Created From
            <input type="date" name="created_at_from" value="{{ $filters['created_at_from'] }}">
        </label>
        <label>
            Created To
            <input type="date" name="created_at_to" value="{{ $filters['created_at_to'] }}">
        </label>
        <label>
            Updated From
            <input type="date" name="updated_at_from" value="{{ $filters['updated_at_from'] }}">
        </label>
        <label>
            Updated To
            <input type="date" name="updated_at_to" value="{{ $filters['updated_at_to'] }}">
        </label>
        <button type="submit">Apply</button>
        <a href="{{ route('intake-records.export', array_filter([
            'triage_status' => $filters['triage_status'],
            'q' => $filters['q'],
            'created_by' => $filters['created_by'],
            'updated_by' => $filters['updated_by'],
            'created_at_from' => $filters['created_at_from'],
            'created_at_to' => $filters['created_at_to'],
            'updated_at_from' => $filters['updated_at_from'],
            'updated_at_to' => $filters['updated_at_to'],
        ], static fn ($value): bool => $value !== '')) }}">Export CSV</a>
        <a href="{{ route('intake-records.index') }}">Clear</a>
        <a href="{{ route('intake-records.create') }}">Create Intake</a>
    </form>

    <table>
        <thead>
            <tr>
                <th>Intake ID</th>
                <th>Requester</th>
                <th>Status</th>
                <th>Updated</th>
                <th>Queue Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($records as $record)
                <tr>
                    <td>{{ $record->intake_id }}</td>
                    <td>{{ $record->requester_identity }}</td>
                    <td>{{ $record->triage_status }}</td>
                    <td>
                        <div>{{ $record->updated_at_utc }}</div>
                        <div class="small">by {{ $record->updated_by }}</div>
                    </td>
                    <td>
                        <form class="status-form" method="post" action="{{ route('intake-records.status', $record) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="filter_triage_status" value="{{ $filters['triage_status'] }}">
                            <input type="hidden" name="filter_q" value="{{ $filters['q'] }}">
                            <input type="hidden" name="filter_created_by" value="{{ $filters['created_by'] }}">
                            <input type="hidden" name="filter_updated_by" value="{{ $filters['updated_by'] }}">
                            <input type="hidden" name="filter_created_at_from" value="{{ $filters['created_at_from'] }}">
                            <input type="hidden" name="filter_created_at_to" value="{{ $filters['created_at_to'] }}">
                            <input type="hidden" name="filter_updated_at_from" value="{{ $filters['updated_at_from'] }}">
                            <input type="hidden" name="filter_updated_at_to" value="{{ $filters['updated_at_to'] }}">
                            <select name="triage_status" required>
                                @foreach ($triageStatuses as $status)
                                    <option value="{{ $status }}" @selected($record->triage_status === $status)>{{ $status }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="updated_by" placeholder="updated_by" value="{{ old('updated_by') }}" required>
                            <input type="text" name="triage_rationale" placeholder="triage_rationale">
                            <input type="text" name="missing_information_notes" placeholder="missing_information_notes">
                            <input type="text" name="exclusion_reason" placeholder="exclusion_reason">
                            <button type="submit">Update Status</button>
                        </form>
                        <div class="row-actions">
                            <a href="{{ route('intake-records.show', $record) }}">Detail</a>
                            <a href="{{ route('intake-records.edit', $record) }}">Edit</a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No intake records found for current filters.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
