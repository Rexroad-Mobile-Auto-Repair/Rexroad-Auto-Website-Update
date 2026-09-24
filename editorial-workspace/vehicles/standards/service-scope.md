# Service-scope guardrails

## Source of truth

`rexroad-custom-theme/inc/schema-service.php` →
`rexroad_custom_get_service_slugs()` is the only authoritative list of
services Rexroad performs:

advanced-diagnostics, auto-air-condition-service,
auto-battery-replacement, brakes-service, cooling-system-service,
alternator-and-starter-repair, fuel-pump-replacement,
headlight-restoration, oil-change, window-regulator-repair,
pre-purchase-inspection, roadside-assistance, suspension-and-steering,
tune-up-service, wheel-bearing-and-cv-axle-repair.

Every repair named in editorial copy must map to something on this
list, or be a plainly diagnostic (not repair) statement ("I'll run a
diagnostic scan"). If a symptom naturally leads toward a repair not on
this list, state the diagnosis, not a repair promise.

## Explicitly never advertised

- Transmission repair
- Tire service
- Exhaust repair
- R-1234yf A/C service

## R-134a wording rule

Rexroad currently services R-134a A/C systems only. Any A/C mention in
editorial copy must:

1. State the R-134a limitation as a Business Fact (Category 1, see
   `claim-rules.md`) — this needs no external citation.
2. **Never** imply every vehicle/model year uses R-134a. Pair the
   limitation with a system-type confirmation clause, e.g. "Not every
   [vehicle] uses R-134a, so I'll confirm your system type before
   scheduling any A/C work."
3. **Never** state a specific refrigerant-cutover model year — that's
   a Category 4 claim (see `claim-rules.md`) unless independently
   verified at Category 3.

This is the F-150 draft's exact pattern; reuse it verbatim in shape
for every future model with A/C mentioned.
