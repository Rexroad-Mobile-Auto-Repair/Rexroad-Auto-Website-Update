# Production checklist

Each stage is a gate — don't move to the next until the current one is
actually true, not "probably fine."

1. **Research** — read the catalog year range for this make/model
   first (sets scope). Attempt primary sources; record every source
   attempted, reachable or not, in the provenance file.
2. **Draft** — write using `make-page-template.md` or
   `model-page-template.md`. Owner-operator voice.
3. **Claim audit** — classify every factual statement per
   `claim-rules.md`. Anything Category 4/6 gets rewritten as durable
   wording or removed. No exceptions for claims that "sound right."
4. **Service-scope audit** — check every repair/service mention
   against `service-scope.md`. Check A/C wording specifically.
5. **Duplication check** — run the "swap test" from `seo-rules.md`.
   Diff against sibling drafts (other models of the same make) for
   copy-pasted paragraphs.
6. **Offline render** — inject the draft into the real template via
   the render harness (content-injecting variant of
   `tests/vehicles/lib/render-preview.php`) and generate HTML output.
7. **Desktop/mobile review** — screenshot both. Check: no overflow, no
   editorial/template duplication, CTA placement, and *specifically*
   whether anything outside the editorial block looks unstyled or
   broken (this caught a real pre-existing `.rr-eyebrow` CSS gap
   during the Ford pass — it won't announce itself, look for it).
   Check browser console/PHP errors.
8. **Editorial approval** — human sign-off that the draft reads as one
   person's writing and is genuinely useful per the swap test.
9. **WordPress publish** — manual, by a human with WP access. Exact
   fields: Title, Slug, Parent, Template ("Vehicle Make Page" /
   "Vehicle Model Page"), Status, and the approved HTML content.
10. **Live verification** — hub auto-links the new page, URL
    hierarchy correct, H1 correct, breadcrumb correct, schema
    restrained to WebPage/BreadcrumbList/AutoRepair only, no
    console/PHP errors on the live URL.

## Hard stop

**If any claim in the draft is still Category 4 (secondary-source,
unverified) or Category 6 (unsupported) at the end of step 3, and
hasn't been rewritten to durable wording or removed, publishing is
blocked.** This applies regardless of how far through the checklist
the draft has otherwise progressed — a draft that reaches step 8 with
an unresolved claim goes back to step 3, not forward to step 9.
