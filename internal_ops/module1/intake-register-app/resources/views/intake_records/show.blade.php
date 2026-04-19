<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Intake Record {{ $record->intake_id }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2rem; max-width: 980px; }
        h1 { margin-bottom: 0.25rem; }
        p.meta { color: #444; margin-top: 0; }
        .banner { background: #f6f8fa; border: 1px solid #d0d7de; padding: 0.75rem; margin-bottom: 1rem; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #d0d7de; text-align: left; vertical-align: top; padding: 0.6rem; }
        th { width: 260px; background: #f6f8fa; }
        .actions { margin-top: 1rem; display: flex; gap: 0.75rem; }
    </style>
</head>
<body>
    <h1>Intake Record Detail</h1>
    <p class="meta">Internal pre-operational module. Localhost-only, non-public.</p>

    @if (session('status'))
        <div class="banner">{{ session('status') }}</div>
    @endif

    <table>
        <tbody>
            <tr><th>Intake ID</th><td>{{ $record->intake_id }}</td></tr>
            <tr><th>Created At UTC</th><td>{{ $record->created_at_utc }}</td></tr>
            <tr><th>Created By</th><td>{{ $record->created_by }}</td></tr>
            <tr><th>Requester Identity</th><td>{{ $record->requester_identity }}</td></tr>
            <tr><th>Requester Contact Channel</th><td>{{ $record->requester_contact_channel }}</td></tr>
            <tr><th>System Class</th><td>{{ $record->system_class }}</td></tr>
            <tr><th>Diagnostic Objective</th><td>{{ $record->diagnostic_objective }}</td></tr>
            <tr><th>Scope Boundary Summary</th><td>{{ $record->scope_boundary_summary }}</td></tr>
            <tr><th>Evidence A Summary</th><td>{{ $record->evidence_a_summary }}</td></tr>
            <tr><th>Evidence B Summary</th><td>{{ $record->evidence_b_summary }}</td></tr>
            <tr><th>Evidence C Summary</th><td>{{ $record->evidence_c_summary }}</td></tr>
            <tr><th>Evidence D Summary</th><td>{{ $record->evidence_d_summary }}</td></tr>
            <tr><th>Constraints (Sensitivity/Availability)</th><td>{{ $record->constraints_sensitivity_availability }}</td></tr>
            <tr><th>Requester Cannot Share</th><td>{{ $record->requester_cannot_share }}</td></tr>
            <tr><th>Triage Status</th><td>{{ $record->triage_status }}</td></tr>
            <tr><th>Triage Rationale</th><td>{{ $record->triage_rationale }}</td></tr>
            <tr><th>Missing Information Notes</th><td>{{ $record->missing_information_notes }}</td></tr>
            <tr><th>Exclusion Reason</th><td>{{ $record->exclusion_reason }}</td></tr>
            <tr><th>Updated At UTC</th><td>{{ $record->updated_at_utc }}</td></tr>
            <tr><th>Updated By</th><td>{{ $record->updated_by }}</td></tr>
        </tbody>
    </table>

    <div class="actions">
        <a href="{{ route('intake-records.index') }}">Back to Queue</a>
        <a href="{{ route('intake-records.edit', $record) }}">Edit Record</a>
        <a href="{{ route('intake-records.create') }}">Create New Record</a>
    </div>
</body>
</html>
