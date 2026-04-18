# AGENTS.md

Repository-specific operating instructions for Codex and other AI coding agents working in this repository.

This repository contains the public-facing Invariant Systems website.
It is a bounded static-site repository.
Unless explicitly authorized by the user, agents must preserve the current site architecture, styling system, and public positioning.

---

## 1. Authority Order

Agents must follow this instruction order:

1. direct user instruction in the active task
2. this `AGENTS.md`
3. other repository documents explicitly referenced by the task
4. existing repository structure and conventions

If instructions conflict, follow the higher authority and report the conflict clearly.

---

## 2. Repository Purpose

This repository is for the Invariant Systems public website only.

It is not the main research repository.
It is not a backend application repository.
It is not a client portal repository.
It is not an experimentation or sandbox repository.

The purpose of this repository is to maintain a clean, static, public-facing website for Invariant Systems.

---

## 3. Default Operating Posture

Unless explicitly authorized otherwise, agents must operate in:

- non-destructive mode
- bounded-scope mode
- no-restructure mode
- no-stack-change mode
- no-backend-invention mode

This means agents must not:

- redesign the site without instruction
- replace the static architecture with a framework
- introduce React, Next.js, Vue, Laravel, PHP, Node, or build tooling
- add package managers, dependency manifests, or bundlers
- introduce databases, APIs, auth, dashboards, or form backends
- widen scope beyond the active request
- rewrite large parts of copy unless asked
- change the public positioning of the business
- weaken or blur the diagnostic-only posture
- make legal claims or compliance claims that are not explicitly approved

---

## 4. Stack Constraints

The current repository is a static website.

Agents must preserve the current stack unless explicitly instructed otherwise:

- HTML
- CSS
- minimal JS only where already appropriate

Do not add:

- npm
- package.json
- framework scaffolding
- build steps
- deployment platform lock-in code
- unnecessary libraries
- tracking scripts
- analytics scripts
- third-party embeds

If a proposed change would require a stack change, stop and report that clearly before proceeding.

---

## 5. Design and Styling Constraints

The site already has an established visual system.
Agents must preserve that system unless explicitly told to revise it.

Default rule:
- maintain existing page layout patterns
- maintain existing header/nav/footer structure
- maintain existing panel/card style
- maintain existing color palette and tone
- maintain current spacing rhythm unless fixing a clear issue

Do not:
- silently restyle the site
- introduce new design systems
- add animation libraries
- add visual clutter
- add decorative elements that dilute the existing restrained tone

Visual refinement is allowed only when directly requested or clearly necessary to fix a defect.

---

## 6. Copy and Messaging Constraints

This site has a deliberate commercial and epistemic posture.

Agents must preserve the following message boundaries:

- Invariant Systems is diagnostic-only
- the site must not drift into optimisation, consulting, implementation, or prediction language
- the site must not imply model-internal inspection if that is outside scope
- the site must not imply guarantees, certification, or compliance authority
- the site must not overclaim capabilities or evidence

When editing copy:
- preserve clarity
- preserve brevity
- preserve boundary discipline
- prefer sharpening over expansion
- do not introduce hype language
- do not make the tone casual, promotional, or inflated

If a requested copy change would materially alter commercial positioning, flag it explicitly.

---

## 7. Naming Rules

Use the following naming model unless explicitly instructed otherwise:

- public-facing brand: `Invariant Systems`
- formal business/legal identity: `Invariant Systems Research and Analysis`

Apply the short brand to:
- headers
- navigation
- main headings
- general body copy
- page titles

Apply the full formal identity to:
- legal/privacy wording
- formal business identity references
- footer if requested by the active task
- other formal/legal contexts

Do not silently replace all short-form brand usage with the full legal name.

---

## 8. Legal and Privacy Constraints

Agents may improve clarity and structure of legal/privacy copy, but must not:

- claim legal compliance certification
- claim regulatory status not provided by the user
- invent registration numbers
- invent company numbers, addresses, or policies
- assert legal sufficiency beyond the wording provided
- present generated text as legal advice

If legal text is being revised:
- keep changes conservative
- avoid overclaiming
- prefer explicit placeholders over invented details
- preserve the distinction between public site wording and formal engagement documents

---

## 9. File and Repository Hygiene

Agents should keep the repository clean and website-focused.

Allowed:
- HTML page edits
- CSS refinements
- asset organization
- metadata improvements
- accessibility improvements
- broken link fixes
- footer/header consistency fixes

Not allowed without instruction:
- introducing unrelated files
- storing working scratch files in repo root
- adding large binary design files unless explicitly requested
- renaming core pages without reason
- changing URL/file structure in a way that breaks navigation

Prefer:
- small targeted edits
- minimal diffs
- preserving stable filenames
- preserving simple deployability on static hosting platforms

---

## 10. Deployment Assumptions

Assume this repo is intended for static deployment, including platforms such as Netlify.

Agents must preserve deployability as a simple static site.

Do not:
- require a build step unless explicitly approved
- require environment variables unless explicitly approved
- add platform-specific complexity unless explicitly requested

---

## 11. Change Discipline

For any non-trivial task, agents should:

1. inspect the relevant files first
2. identify the minimum necessary edits
3. keep changes bounded to the active task
4. show or summarize diffs clearly
5. stop after completing the requested scope

For multi-file edits, avoid opportunistic rewrites.

Do not fix unrelated issues unless:
- they are blocking the requested task, or
- the user explicitly asks for a broader cleanup pass

---

## 12. Safe Default Behavior

If the request is ambiguous, agents should prefer:

- preserving existing structure
- making the smallest viable change
- asking for clarification only when necessary
- avoiding speculative enhancements
- avoiding silent commercial or legal repositioning

If uncertain whether a change affects:
- public positioning
- legal meaning
- service boundaries
- deployment architecture

then stop and report the uncertainty before making the change.

---

## 13. Typical Allowed Tasks

Examples of tasks generally allowed under this file:

- refine homepage copy
- align footer naming
- improve privacy page wording conservatively
- fix broken navigation links
- clean root file organization
- improve mobile spacing
- improve metadata and title tags
- add missing favicon or simple static assets
- tighten CTA wording without changing service scope

---

## 14. Typical Disallowed Tasks Without Explicit Approval

Examples of tasks not allowed unless explicitly authorized:

- redesign the whole site
- migrate to a framework
- add forms with backend processing
- add user accounts or client login
- add analytics or tracking
- add cookie banners tied to tracking scripts
- add chatbot widgets
- add payment systems
- add dashboards or internal tools
- convert the site into a SaaS application
- rewrite the commercial model

---

## 15. Output Expectation for Agents

Unless instructed otherwise, agents should return:

- a short summary of what was changed
- any assumptions made
- any risks or follow-up items
- a clear diff or patch summary for code/document edits

Do not claim tasks were completed if they were only partially completed.

Be explicit about uncertainty.