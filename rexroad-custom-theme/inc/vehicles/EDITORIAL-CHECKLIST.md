# Vehicle Make/Model page editorial checklist

Not enforced in code. This is guidance for whoever creates and publishes
Vehicle Make Page / Vehicle Model Page Pages in `wp-admin` — a page
existing in the catalog (`inc/vehicles/vehicle-data.php`) never implies
it should get a live page. Publishing is a deliberate editorial choice.

> **A page that contains only template-generated sections is not
> publishable.** The templates provide structure, not content — every
> section below that isn't explicitly "automatic" must come from real,
> reviewed `the_content()`.

## Before publishing a Make page

- [ ] Unique, make-specific introduction written in `the_content()` —
      **at least ~150 words of genuinely make-specific content**, not
      copied from another make page, not left blank, and not just the
      make name repeated in different sentences
- [ ] The introduction contains real context about this specific make
      (not interchangeable with any other make's intro if you swapped
      the name)
- [ ] Model directory reviewed and looks meaningfully populated
- [ ] "Common {Make} Services" section reviewed — the links are
      automatic, but confirm they're actually relevant to this make
- [ ] "Common Issues We Diagnose on {Make} Vehicles" section reviewed;
      if this make genuinely has distinctive, verified issues worth
      calling out, that goes in `the_content()` — never invent a claim
      just to fill the section
- [ ] Page title in `wp-admin` matches the catalog make name (the H1,
      breadcrumb, and schema all use the catalog name regardless, but a
      mismatched title is confusing in the admin list and search results)
- [ ] **Not directory-only:** a make page consisting of nothing but the
      automatic model directory, services, and problem sections — with
      no real `the_content()` — is not publishable

## Before publishing a Model page

- [ ] Unique intro paragraph — **at least ~150 words of genuinely
      model-specific content**, not generic boilerplate reused across
      models, not just the make/model name substituted into a template
      sentence
- [ ] Real model-specific context: ownership/repair notes, what makes
      this model distinct to service, or similar — not interchangeable
      with any other model's intro if you swapped the name
- [ ] Supported years double-checked against the catalog section on the
      page itself
- [ ] Relevant services selected/reviewed — the shared section is
      automatic; confirm the links actually make sense for this model
- [ ] At least one genuinely useful diagnostic/problem-context section —
      either the automatic "Problems We Diagnose" section, or
      model-specific content in `the_content()` if there's something
      real and verified to say
- [ ] **Not boilerplate-only:** years + generic service list + generic
      problem list + generic capability list, with no real
      `the_content()`, is **not** sufficient to publish — this is the
      default state of an unedited page and must not go live as-is
- [ ] Parent Page is the correct Make page (wrong parent silently falls
      back to a minimal page, not a hard error — verify by viewing it live)

## General

- Never write or imply a make/model is unusually failure-prone unless
  that claim is deliberately researched and true.
- Never claim a specific popularity ranking ("most popular", "customer
  favorite") — no popularity data exists in the catalog.
- If in doubt, don't publish yet. A missing page costs nothing; a thin
  or inaccurate one costs trust and SEO quality.
