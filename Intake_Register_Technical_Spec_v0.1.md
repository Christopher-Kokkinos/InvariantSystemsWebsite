Intake_Register_Technical_Spec_v0.1
1. Module objective
Deliver Module 1 as an internal, pre-operational intake register that supports the active minimum intake/triage model.
Standardize intake capture, triage statusing, and rationale recording.
Provide operational support only; do not replace current operator authority or process ownership.
Remain non-public and non-claim-bearing.
2. Runtime shape
Runtime is local-only on a controlled internal machine.
Data store is a local single-file relational database.
UI is a lightweight web application bound to 127.0.0.1 only.
Intended usage is single-operator or tightly controlled sequential use in v0.1.
No external endpoint, no internet dependency, and no public exposure path in v0.1.
3. Core data model
Primary table: intake_records.
intake_id (text, unique, required).
created_at_utc (timestamp text/UTC, required).
created_by (text, required).
requester_identity (text, required).
requester_contact_channel (text, required).
system_class (text, required).
diagnostic_objective (text, required).
scope_boundary_summary (text, required).
evidence_a_summary (text, optional).
evidence_b_summary (text, optional).
evidence_c_summary (text, optional).
evidence_d_summary (text, optional).
constraints_sensitivity_availability (text, optional).
requester_cannot_share (text, optional).
triage_status (enum text, required).
triage_rationale (text, conditionally required).
missing_information_notes (text, conditionally required).
exclusion_reason (text, conditionally required).
updated_at_utc (timestamp text/UTC, required).
updated_by (text, required).
4. Views and operator interactions
Intake Form View: create record, edit fields, save changes, and set initial triage status.
Triage Queue View: list records, filter by status/date/operator, open record detail, and apply status updates.
Record Detail Panel: view complete intake content and full current triage rationale fields.
Minimum operator actions: create, update, status change, rationale entry, filter, and export filtered result set.
5. Validation logic
Record creation requires: intake_id, created_at_utc, created_by, requester_identity, requester_contact_channel, system_class, diagnostic_objective, scope_boundary_summary, triage_status, updated_at_utc, updated_by.
Any status set to Accept, Conditional Accept, Pause, or Decline requires non-empty triage_rationale.
Conditional Accept and Pause require non-empty missing_information_notes.
Decline requires non-empty exclusion_reason.
Accept is blocked if the recorded objective/scope is explicitly non-diagnostic (optimization, implementation, prediction, model-internal inspection, or compliance/certification activity).
Validation errors are blocking for save/transition and must be shown before completion.
6. Status transition rules
Initial status on new record may be Untriaged.
Allowed transitions from Untriaged: Accept, Conditional Accept, Pause, Decline.
Allowed transitions from Conditional Accept: Accept, Pause, Decline, or remain Conditional Accept with updated rationale.
Allowed transitions from Pause: Conditional Accept, Accept, Decline, or remain Pause with updated rationale.
Allowed transitions from Accept: Conditional Accept, Pause, or Decline when new evidence or boundary clarification requires narrowing.
Allowed transitions from Decline: Pause, Conditional Accept, or Accept only when material new intake information is recorded.
Every transition must update updated_at_utc, updated_by, and triage_rationale.
7. Basic query/export behavior
Query/filter by triage_status, creation/update date window, created_by, updated_by, and free-text match across key summary fields.
Default queue ordering is most recently updated first.
Export produces local CSV for the current filtered set only.
Export includes core intake and triage fields required for internal coordination.
No automated external delivery, no API publish, and no public reporting behavior.
8. Explicit non-goals
No public intake surface, no client portal, and no external user access.
No full platform architecture definition beyond Module 1 boundaries.
No workflow automation beyond basic validation and status transitions.
No broader delivery/operations activation behavior.
No compliance, certification, or business-performance claim generation.
No replacement of the active minimum operating model.
9. Exit condition for implementation readiness
Implementation readiness is reached when the local runtime shape, core schema, two UI views, validation rules, status transitions, and basic query/export behavior are unambiguous and internally agreed.
Implementation readiness requires explicit acknowledgment that v0.1 remains internal, pre-operational, and non-public.
Implementation readiness requires confirmation that Module 1 is assistive to the current minimum model and does not supersede it.