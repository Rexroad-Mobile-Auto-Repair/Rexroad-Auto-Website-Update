# Model-page editorial template

Target: ~350–600 useful words where the subject supports it. Word
count is a quality guide, not a requirement — a model with a thinner
legitimate story (short production run, few real configuration
differences) should be shorter rather than padded. Never write to hit
a number.

Do not restate what the PHP template already renders: H1, breadcrumb,
hero lead, "Supported Model Years", service-links partial, problem-links
partial, "What We Can Diagnose and Repair at Your Location" capability
list, "Other {Make} Models We Service", service-area paragraph, CTA.

## Section guide

1. **Model-specific introduction.** What makes this model, specifically,
   worth its own page — not interchangeable with any other model's
   intro if you swapped the name (per `EDITORIAL-CHECKLIST.md`).
2. **Why configuration/year matters.** Durable wording only unless
   every specific (engine year, generation boundary) is primary-source
   verified — see `claim-rules.md`. Default to: "engine, drivetrain,
   model year, [cab/bed, trim, or body style as applicable], and
   installed equipment can affect diagnosis, parts, and service
   procedures."
3. **Symptoms/problems Rexroad can diagnose.** Symptom-based, not
   failure-based — "brake noise or reduced stopping performance," never
   "known for premature brake wear." Cross-check every item against a
   real service in `service-scope.md` before including it.
4. **Repairs appropriate for mobile service.** Only list what Rexroad
   actually does (`inc/schema-service.php` slug list). State plainly
   when something is out of scope rather than omitting it silently —
   the R-134a caveat is the model for this.
5. **What information the customer should provide.** Concrete,
   actionable list (year, engine/drivetrain if known, symptoms,
   warning lights, cranks or not, location). This is the section most
   likely to be genuinely identical in shape across models — that's
   fine, the shape is a UX pattern, not "duplicate content."
6. **Request Service transition.** Close on next action, complementing
   the template's own CTA.

## Voice

First person, owner-operator, same as the make-page standard.

## Reference

`editorial-workspace/vehicles/ford/model-f-150.html` (482 words,
hardened v2) is the baseline example. Its v1 (616 words, since
replaced) is the cautionary example — it opened with a nine-engine,
dated spec list sourced only from WebSearch snippets; the hardened v2
replaced it with durable wording per `claim-rules.md`. Start from v2's
shape, not v1's.
