# Changes — round 3 (acceptance test 2026-10-02)

Source: acceptance test run (186 checks), failures triaged by Marcel. Status per item: ☐ open · ☑ done · ❓ client decision.

## Fixed
- ☑ **media-2** Wrong file type: the uploader now shows "Hier sind nur diese Dateitypen erlaubt: …" as an error toast (same wording as the server rule). Before, Uppy rejected the file silently. Server errors on upload (422, network) are shown too.
- ☑ **proj-10** Meta description fallback: paragraphs and line breaks become spaces before the tags are stripped ("…kursiv Linktext" instead of "…kursivLinktext").
- ☑ **team-4** Über uns: the "Lebenslauf" toggle only appears when the member has a CV (an emptied editor's `<p></p>` counts as empty).
- ☑ **cats-6** A category or type that still has projects can't be deleted: the API answers 422 "Die Kategorie / Der Typ enthält noch N Projekte. …", shown as an error toast. Before, the database refused with a 500 and the admin showed nothing. Generic `preventDelete()` hook in `ResourceController`; list deletes now show the error message on failure.
- ☑ **users-5** The own account can't be deleted: the API answers 403, the trash icon is hidden on the own row (`is_self` in `UserResource`). This also means the last account can never be deleted.
- ☑ **Team crop** Portrait crop preset 2:3 instead of 432 × 500, the same ratio legacy delivers (all 13 portraits are 667 × 1000 or 300 × 450, served at 334 × 500). The 432 × 500 came from the legacy `width`/`height` attributes, which don't match the images. The attributes stay as they are (pixel parity, the image is `w-full h-auto`).
- ☑ **Teaser flag removed** The star "Als Teaser setzen" on project images came from the Template and had no effect on the site (no view used it, no image had it). Legacy had no such flag. Removed from the image card, media store, API (`PATCH media/{uuid}/teaser`), model and DB (`is_teaser` dropped by migration). The Opengraph flag stays: legacy used the first published image as the OG image (`Frontend\ProjectsController`); the rework does the same unless an image is flagged.

## Open
- ❓ **entries-5** Presse: the project reference ("…, Leimenegg im Park Winterthur (2023)") is plain text, exactly as on the legacy site. The checklist expected a link. Question for the client: should it link to the project? Proposal: link only when the project has a detail page (projects without one have no page to show).
