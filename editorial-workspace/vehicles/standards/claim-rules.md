# Claim classification and publishability rules

Every factual statement in a draft belongs to exactly one of six
categories. The category decides whether it may reach customer-facing
copy, and in what form.

| # | Category | Definition | Example | Publishable as a specific claim? |
|---|---|---|---|---|
| 1 | **Business fact** | A fact about Rexroad itself — services offered, service area, capability limits, hours, pricing approach | "Rexroad services R-134a A/C systems" | Yes, directly |
| 2 | **Catalog-derived fact** | Anything computable from `vehicle-data.php` | "This model's catalog years are 2000–2026" | Yes, directly (template already renders this; editorial copy should reference it in prose, not restate exact numbers redundantly) |
| 3 | **Primary-source vehicle fact** | Verified by actually reading a reachable manufacturer document (official spec/history page, owner's manual), or NHTSA data | A confirmed engine-introduction model year | Yes, directly — **only if actually read**, not inferred from a search-result snippet |
| 4 | **Secondary-source research** | Enthusiast/press/aggregator sources (Wikipedia, dealer blogs, forums), including WebSearch snippets not independently fetched and read | Generation-boundary years surfaced via search | **No** as a specific/dated fact. May only shape *durable, non-dated* wording (see below). Record in the provenance file; never state the specific year/spec in customer copy from this tier alone |
| 5 | **General automotive explanation** | Non-dated, non-model-specific mechanical/diagnostic reasoning true of vehicles generally | "The same symptom can have different causes depending on the vehicle" | Yes, directly — not a falsifiable per-vehicle claim |
| 6 | **Unsupported claim** | Anything asserted without a traceable basis — assumption, inference, "sounds right" | Any claim with no row in the provenance file | **Never.** Remove or send back for research |

## Durable wording — the fallback for Category 4

When real research only reaches Category 4 (the expected outcome in
this environment, since WebFetch to Ford/NHTSA/Wikipedia primary pages
is currently blocked — see `PRODUCTION-CHECKLIST.md`), write the point
generically instead of stating the unverified specific:

> Not: "Ford dropped the 4.2L V6 after 2008 and both V8s after 2010."
> Instead: "Engine, drivetrain, model year, cab/bed configuration, and
> installed equipment can affect diagnosis, parts, and service
> procedures."

This is not a downgrade in usefulness — it's the difference between a
claim that can be wrong and one that can't.

## Explicitly prohibited unless verified at Category 3

These may **never** appear as stated fact unless backed by an actually-read,
reachable primary source (not a search snippet):

- Reliability claims
- Failure-rate claims
- "Common problem" claims presented as fact
- Horsepower / towing / specification dumps
- Refrigerant cutover assumptions (which model years use R-134a vs.
  R-1234yf)
- Production dates / cutover dates
- Generation boundaries
- Engine availability by year
- Recalls
- Safety claims

If a claim in this list can only be sourced to Category 4, it is
dropped or rewritten as durable wording — never published as stated
fact, never softened with hedge words ("likely," "typically") to sneak
it through.

## Precedent

The F-150 draft's v1→v2 hardening pass is the worked example: v1's
nine-engine list and refrigerant-cutover-year claim were Category 4
throughout (WebFetch to Ford/NHTSA/Wikipedia was blocked every attempt
this session) and were removed; v2 replaced them with Category 5
durable wording and a Category 1 business-fact caveat. See
`ford/sources-f150.md` for the full before/after table.
