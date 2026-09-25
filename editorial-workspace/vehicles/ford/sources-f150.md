# F-150 / Ford editorial drafts — claim provenance (hardened pass)

Revision history: v1 included a nine-engine list, generation-boundary
years, and an R-134a/R-1234yf cutover claim, all sourced only from
WebSearch snippets over secondary sources (WebFetch to Ford's own
domains, NHTSA, and Wikipedia was blocked by this environment's egress
proxy on every attempt — never independently reachable). Per
instruction, v2 below removes every claim that isn't verifiable against
a reachable authoritative primary source, in favor of durable,
config-agnostic wording.

## Claims remaining in the v2 drafts

| Claim | Basis | Type | Confidence |
|---|---|---|---|
| F-150 catalog range is 2000–2026, one continuous range | `rexroad-custom-theme/inc/vehicles/vehicle-data.php` (this repo's own generated catalog) | Primary (internal) | High |
| Ford model years 2000–2026 catalog spans cars, SUVs/crossovers, and trucks (Escape, Edge, Explorer, Bronco, F-150, Super Duty, etc.) | Same internal catalog | Primary (internal) | High |
| "The F-150 platform has changed over time — different frames, different electronics, different engine and drivetrain options" (no dates, no specific engines named) | General, uncontested automotive-industry knowledge about a 25+ year production run undergoing multiple redesigns; not tied to any specific year, count, or spec | Common knowledge / not a discrete verifiable claim | N/A — deliberately non-specific so it needs no citation |
| "Engine, drivetrain, model year, cab/bed configuration, and installed equipment can affect diagnosis, parts, and service procedures" | General automotive-repair principle (any make/model with option variation), not F-150-specific | Common knowledge | N/A |
| Rexroad currently services R-134a A/C systems; not every F-150 uses R-134a, so system type is confirmed before scheduling | Business/service-capability fact stated in the task brief (Rexroad services R-134a only), not a claim about which model years use which refrigerant | Business fact (internal), not externally sourced | High (as a statement of Rexroad's own capability) — deliberately makes no claim about *which* model years use which refrigerant |
| Symptom list (no-start, charging concerns, warning lights, overheating, brake noise, suspension/steering noise, electrical issues) | Generic diagnostic categories, not F-150-specific failure claims | Common knowledge | N/A |

## Removed in this pass (previously Medium confidence, not reachable against primary sources)

- Full nine-engine list (4.2L Essex V6, 4.6L/5.4L Triton V8, 3.7L V6, 5.0L V8, 6.2L V8, 3.5L EcoBoost, 2.7L EcoBoost, 3.0L Power Stroke, PowerBoost) and every introduction/discontinuation year attached to it.
- Generation-boundary years (10th–14th gen ranges).
- F-150 nameplate/1975 origin claim.
- Any horsepower, torque, or towing figure.
- The R-134a→R-1234yf refrigerant cutover year (sources conflicted 2017 vs. 2020/2021 and could not be checked against a Ford primary source).
- Any reliability/failure-proneness claim.

None of the removed items appear in the v2 Ford or F-150 draft. **No claim in the current drafts depends on an external secondary source** — the only externally-flavored statements left are deliberately generic (no dates, no model names, no numbers) automotive-repair truisms that don't require sourcing, plus the internal vehicle catalog and Rexroad's own stated service capability.

## If engine-level detail is wanted later

Re-add it only after confirming against a reachable Ford primary
source (fromtheroad.ford.com spec/history pages, an owner's manual, or
NHTSA) — this environment's egress proxy blocked all three on every
attempt during this pass. Until then, the durable wording above is the
ceiling for how specific this content should get.
