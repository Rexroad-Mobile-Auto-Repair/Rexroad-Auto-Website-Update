# Make-page editorial template

Target: ~150–300 useful words in `the_content()`. Word count is a
quality guide, not a checkbox — stop when the section has said
something real, don't pad to a number.

Do not restate what the PHP template already renders: H1, breadcrumb,
hero lead ("Rexroad services {Make} vehicles..."), "Models We Service" /
full directory, "Common {Make} Services", "Common Issues We Diagnose",
service-area paragraph, CTA. Editorial content sits between the hero
and the directory — it earns its place by saying something the
template structurally cannot.

## Section guide (prose, not literal subheadings — see Ford for tone)

1. **Make-specific introduction.** What does Rexroad actually see of
   this make locally — breadth of body types/segments represented in
   the catalog, not brand history or marketing copy.
2. **Why exact year/model/configuration matters.** State plainly that
   this make's vehicles vary across the catalog's years — durable
   wording only (see `claim-rules.md`), no specific engine/date list
   unless every item is primary-source verified.
3. **How Rexroad approaches diagnosis/service.** One or two sentences
   on asking for specifics before quoting a repair — ties the make
   page to the owner-operator voice, not a policy statement.
4. **Directory transition.** A natural sentence handing off to the
   "Models We Service" section below — don't describe the directory
   mechanically ("catalog", "dataset" — banned words, see
   `seo-rules.md`).
5. **Request Service transition.** Close on how to get a specific
   vehicle confirmed — complements, doesn't duplicate, the template's
   own CTA copy.

## Voice

First person, owner-operator ("I", "me", "my") — matches
`home.php`/`front-page.php`. The surrounding template CTA blocks use
"we/our"; that's a known, accepted seam (see README "Known
limitations"), not something to fix by mimicking it in editorial copy.

## Reference

`editorial-workspace/vehicles/ford/make-ford.html` (151 words) is the
baseline example — structurally reusable, factually Ford-specific in
its details only.
