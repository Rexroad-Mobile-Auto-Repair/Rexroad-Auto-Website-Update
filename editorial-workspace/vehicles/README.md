# Vehicle make/model editorial workspace

Offline drafting area for `the_content()` copy on Vehicle Make Page and
Vehicle Model Page WordPress Pages. Nothing here is published or wired
into the theme — see `EDITORIAL-CHECKLIST.md` in
`rexroad-custom-theme/inc/vehicles/` for what has to be true before an
editor actually publishes a page using this content.

## Layout

```
editorial-workspace/vehicles/
  standards/                  the production standard (read this first)
    make-page-template.md     make-page draft template/checklist
    model-page-template.md    model-page draft template/checklist
    claim-rules.md            claim classification + publishability
    service-scope.md          what Rexroad actually does, R-134a rule
    seo-rules.md               banned patterns, "swap test"
    provenance-format.md      sources-<model>.md table format
    production-checklist.md   Research -> ... -> Live verification, with a hard stop
  <make>/
    make-<make>.html          draft the_content() for the make page
    model-<model>.html        draft the_content() for one model page
    sources-<model>.md        provenance table for that draft (see provenance-format.md)
```

`ford/` is the worked baseline — both the finished drafts and, in
`sources-f150.md`, a real before/after hardening pass (a claim set that
started too specific and was pulled back to durable wording).

## Known limitations (carry these into every future draft)

- **WebFetch to manufacturer/NHTSA/Wikipedia primary pages was blocked
  by this environment's egress proxy on every attempt during the Ford
  pass.** Until that changes, "primary-source vehicle fact" (Category 3
  in `claim-rules.md`) is effectively unreachable from this session —
  default to durable wording, don't treat a WebSearch snippet as if it
  were a read primary source.
- **Template/editorial voice seam:** the PHP templates' CTA and
  structural copy use "we/our"; editorial `the_content()` uses the
  site's established first-person "I/my" (`home.php`/`front-page.php`).
  This is a known, accepted tradeoff at the template/editorial
  boundary, not a defect for a new draft to try to fix.
- **`.rr-eyebrow` has no bare CSS rule** in `vehicles.css` — only
  `.entry-content .rr-eyebrow` variants exist elsewhere in the theme —
  so eyebrow labels outside the editorial block (hero, section
  headings) render unstyled. Pre-existing, out of scope for editorial
  work; `production-checklist.md` step 7 calls this out explicitly so
  it's checked for on purpose rather than found by accident again.

## Recommended next make/model pairs (not started — standard only)

High-volume, diagnostically-distinct from Ford/F-150, good breadth
across body styles: **Chevrolet Silverado 1500** (direct full-size
truck peer), **Toyota Camry** (top-volume sedan), **Honda Civic**
(top-volume compact), **Toyota Tacoma** (high-volume mid-size truck),
**Ram 1500** (full-size truck, different platform lineage than
Ford/Chevrolet). Suggestion only — no drafts created.
