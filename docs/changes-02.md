# Admin changes — round 2 (client feedback 2026-09-28)

Source: annotated screenshots and chat feedback. Status per item: ☐ open · ☑ done.

## A. Pages and SEO
- ☑ **A3** *(superseded by A7)* SEO is a list of the 7 listing pages (Seite, Meta Description, OG-Bild) instead of one form with 7 fields. Each record opens its own form with meta description and OG image in the main column (no sidebar). API: `/api/dashboard/seo` is now a resource (index, show, update; listing pages only, content pages → 404), replacing the bulk `PUT`. Replaces the SEO screen from round 1 (Q1).
- ☑ **A4** Page form ("Seiten"): tabs Inhalt · SEO instead of the sidebar; meta description and OG image are on the SEO tab. Both tabs are limited to the form width (`max-w-[48rem]`) instead of full width.
- ☑ **A5** SEO and Seiten lists: meta description shortened to 60 characters (column `limit`, full text on hover); the CSS `truncate` had no effect in table cells.
- ☑ **A6** Label "Opengraph Image" everywhere instead of "Social-Media-Bild (OG)" / "OG-Bild" (form fields, SEO list column, image action tooltip, crop preset name).
- ☑ **A7** SEO merged into "Seiten". The list shows all 11 pages in site order (Seite, Meta Description, Opengraph Image). Content pages (Über uns, Jobs, Kontakt, Impressum) have the tabs Inhalt · SEO and a publish toggle; listing pages (Startseite, Werkliste, …) only have the SEO tab and are always online (toggle hidden, `PATCH …/publish` → 404). `PageRequest` accepts only the SEO fields for listing pages. The separate SEO entry, controller, request and views are removed (`/api/dashboard/seo` is gone).
- ☑ **A8** Page image (Über uns, Jobs): one image (`maxFiles: 1`, label "Bild"), like the legacy pages. Once an image is there, the uploader is hidden (as for the Opengraph Image); single-file uploaders say "Bild hinzufügen".

## C. Badges
- ☑ **C2** *(superseded by C4)* Badge style: `rounded-md` instead of `rounded-full`, bold text with normal tracking (`tracking-normal`, the body is `tracking-wide`), less horizontal padding (`px-6`).
- ☑ **C3** Kategorien list: the types are shown as badges in a "Typen" column (instead of "5 Typen"), alternating per category like in the project list.
- ☑ **C4** Badge redesign: quiet, borderless chip (light gray fill, regular weight, `rounded-sm`) instead of solid black bold, which felt out of place in the otherwise quiet admin. One variant only; the solid/outline alternation per category is dropped (the `variant` prop is removed). Replaces the black/white badge decision from round 1 (Q3).

## E. Sidebar navigation
- ☑ **E2** "Einstellungen" is now a sidebar group title (like Büro, Publikationen) with its own entries: Kategorien, SEO, Benutzer (SEO moved into Seiten with A7). Each is its own page with its own title; the tab bar (`SettingsTabs`) is gone. Replaces the tab layout from round 1 (Q4).
- ☑ **E3** "Seiten" moves up, directly below Projekte.
- ☑ **E4** First sidebar group order: Startseite, Projekte, News, Seiten.

## F. Grid (Raster), projects and homepage
- ☑ **F5** Existing grid rows are collapsed when the editor opens (both contexts, all areas). Rows added afterwards stay open so they can be filled right away; "Alle ausklappen" opens them all.
- ☑ **F6** More breathing room in the row header: height 40 → 52 px (`h-52`).
- ☑ **F7** Project grid tab: removed the hint text above the grid ("Neu hochgeladene Bilder … sofort gespeichert.").
