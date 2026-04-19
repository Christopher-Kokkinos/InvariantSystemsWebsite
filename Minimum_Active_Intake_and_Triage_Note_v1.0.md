# Minimum Active Intake and Triage Note v1.0

## 1. Purpose
Define the minimum active intake and triage layer for diagnostic-only engagements. The note is used to make bounded fit decisions quickly without widening scope.

## 2. Public Intake Requirement
Public intake must include:
- system class
- diagnostic objective (diagnosis, not optimisation or implementation)
- available behavioural evidence across A-D
- constraints (sensitivity and availability)
- anything the requester explicitly cannot share

Evidence classes A-D:
- A. decision trace data
- B. outcome and feedback signals
- C. intervention and change logs
- D. lightweight contextual metadata

## 3. Triage Categories (Accept / Conditional Accept / Pause / Decline)
- Accept: diagnostic-only objective is clear, scope is bounded, and behavioural evidence is sufficient to start.
- Conditional accept: request is likely a fit, but one or more intake elements need confirmation before opening.
- Pause: fit cannot be determined yet because the objective or evidence is currently too incomplete.
- Decline: request depends on excluded scope or seeks non-diagnostic outcomes.

## 4. Fixed Exclusions
- no optimisation (including tuning, prompts, or parameter advice)
- no consulting or implementation disguised as diagnosis
- no model inspection (internals, code, weights, activations, hidden state, training data)
- no prediction of outcomes or performance
- no compliance audit or certification activity

## 5. Evidence-Boundary Rule
All triage and outputs must remain behaviourally grounded. If reliability cannot be validly inferred under current system-environment coupling and available behavioural evidence, that boundary is stated explicitly and claims are not extended beyond it.

## 6. Fit-Positive Reply Template
Thank you for your intake note.

Your request appears to fit our diagnostic-only scope.

Provisional fit summary:
- objective: [diagnostic objective]
- bounded scope: [scope boundary]
- evidence available: [A/B/C/D summary]

If confirmed, the engagement remains diagnostic-only and evidence-bound: regime classification, invalid assumptions, and boundaries of valid inference. No optimisation, implementation, or prediction output is provided.

## 7. Pause Reply Template
Thank you for your intake note.

We are pausing triage until minimum intake clarity is available on:
- [missing objective detail]
- [missing evidence detail]
- [missing scope boundary]

Once these are provided, we can re-run fit triage within the current diagnostic-only boundary.

## 8. Decline Reply Template
Thank you for your intake note.

We cannot accept this request in its current form because it depends on excluded scope: [reason].

Our scope is diagnostic-only and behaviourally grounded. We do not provide optimisation, implementation, prediction, model-internal inspection, or compliance certification activity.

## 9. Deferred Components
The following are explicitly deferred outside this minimum active note:
- delivery workflow beyond intake and triage
- operations and pilot-gated controls
- backend or platform design implications
- SYN002 active-state implications

## 10. Operator Narrowing Rule
When intake is ambiguous, apply the narrowest evidence-grounded interpretation. Do not infer broader authority from intent. If fit is uncertain, move one step narrower in triage (accept -> conditional accept -> pause -> decline) until boundaries are explicit.
