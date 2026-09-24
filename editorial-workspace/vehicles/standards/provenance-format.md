# Provenance file format

One `sources-<model>.md` (or `sources-<make>.md`) file per draft,
alongside its `.html`. Internal reviewer artifact only — citations do
not appear inline in customer-facing copy unless a claim specifically
needs a customer-facing caveat (the R-134a pattern is the model for
that; it's a business-fact disclosure, not a citation).

## Table columns

| Column | Meaning |
|---|---|
| Claim | The exact statement as it would read in copy, or the fact it's based on if the copy uses durable wording instead |
| Claim type | One of the six categories in `claim-rules.md` |
| Source | Where it came from — internal file path, a specific reachable URL actually read, or "WebSearch snippet (not independently fetched)" stated plainly |
| Confidence | High / Medium / Low |
| Source tier | Primary / Secondary / Internal |
| Publishable | Yes / No / Conditional (state the condition) |
| Notes | Why it was included, excluded, or rewritten as durable wording |

## Rules

- Every specific factual claim in the draft has a row. If it doesn't,
  it's Category 6 (unsupported) by definition and must be removed
  before the draft is considered done.
- A row with Source Tier = Secondary and Confidence = Medium/Low is
  never marked Publishable = Yes for a dated/specific claim — only
  Conditional, with the condition being "rewritten as durable wording"
  or "pending primary-source re-verification."
- Removed claims get a row too, under a "Removed / rejected" section —
  this is what keeps the next writer from re-researching and
  re-adding the same unverifiable claim.

See `ford/sources-f150.md` for a worked example of this exact format.
