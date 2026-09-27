# Admin changes — round 1 (client feedback 2026-09-27)

Source: `changes.txt` + 4 annotated screenshots. Status per item: ☐ open · ☑ done.

## A. Pages
- ☑ **A1** The "Seiten" section only lists the content pages: Kontakt, Über uns, Jobs, Impressum.
- ☑ **A2** Meta descriptions of the other pages (Startseite, Werkliste, Presse, Bücher, Downloads, Auszeichnungen, Vorträge) move to a config file (see question Q1).

## B. Media
- ☑ **B1** Image cropping: define the crop ratios per image field instead of the same generic presets everywhere (Q2).
- ☑ **B2** Allowed file types per field instead of one global list. Today every image field also accepts videos; only project images and grids need video.

## C. Badges
- ☑ **C1** Show category/type as black/white badges in lists (projects list: "Typ" column) (Q3).

## D. Listings
- ☑ **D1** Vorträge, Auszeichnungen, Presse: remove the "Beschreibung" column.
- ☑ **D2** Presse: limit the length of the project title in the list (truncate).
- ☑ **D3** News: remove the "Untertitel" column.
- ☑ **D4** Team: remove the "Position" column.
- ☑ **D5** Bücher: remove the "Bestellung" column.
- ☑ **D6** Projects list:
  - move the type filter up into the header (next to "Neues Projekt");
  - remove the help text "Zum Sortieren einen Typ wählen.";
  - remove the "Detailseite" column;
  - show the type as a badge (→ C1).

## E. Sidebar navigation
- ☑ **E1** Structure as in the screenshot (cms.strut.ch), with group titles:
  - Startseite, News, Projekte
  - **Büro:** Stellen, Team, Auszeichnungen, Vorträge
  - **Publikationen:** Bücher, Presse
  - Inhalte, Einstellungen

  Startseite is added as the first entry (Q4).

## F. Grid (Raster), projects and homepage
- ☑ **F1** Rows are collapsible, which makes sorting easier (both contexts).
- ☑ **F2** "Neue Zeile": instead of the list of layout buttons below the grid, a button at the top opens a sidebar (drawer) with the layouts.
- ☑ **F3** Project form: hide the form sidebar while the "Raster" tab is active.

## Questions / decisions
Recorded below once answered.
- **Q1 (A2): editable in the admin.** Meta descriptions of the listing pages are edited under **Einstellungen → SEO** (one field per page). They stay on the `pages` records; "Seiten" only lists the 4 content pages.
- **Q2 (B1/B2): per-field configuration.** Each image field declares its crop ratios and allowed file types; the ratios are taken from the legacy frontend.
- **Q3 (C1): one type badge per project, styled by category.** Categories alternate black (filled) and white (outlined).
- **Q4 (E1): structure and groups as in the screenshot, current labels kept** ("Jobs", "Seiten"). **Einstellungen** = tabs Kategorien · SEO · Benutzer (Benutzer moves there).

## Implementation notes
- **B1/B2 media profiles** are defined in `config/media.php` (`project`, `portrait`, `entry`, `page`, `cover`, `news`, `og`, `document`).
  - The upload endpoint validates the file type per profile.
  - The admin gets extensions, hint and crop ratios via `/api/dashboard/options`; each `MediaField` declares its profile.
  - A single ratio locks the crop (portrait 432×500, entry 3:2, OG 1200×630).
- **Einstellungen** tabs are Kategorien · SEO · Benutzer; the sidebar entry stays active on all three.
- **Verified** in headless Chromium:
  - project list badges and header filter;
  - collapsed grid rows and the layout drawer;
  - grid tab without the form sidebar;
  - SEO screen (7 fields);
  - "Seiten" lists 4 pages;
  - portrait crop locked;
  - PDF upload with the `document` profile accepted by the server.
- **Found along the way:** unattached temp uploads were never cleaned up (Template gap). Added `media:clean-temp` (daily 03:30, see `docs/deployment.md`).
