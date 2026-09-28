# Progress log

## 2026-09-27 — Phase 0: Analysis

Done:
- Read all three source projects (Current `strut.ch`, Updated `cms.strut.ch`, Template `forrerzimmermann.ch`) read-only.
- Inspected the Current DB (MySQL 5.7 via the MAMP socket, SELECT only). Schema, row counts, grid usage, orphans and file references were checked against disk.
- Wrote `docs/analysis.md` (versions, template inventory, content types, Updated critique, grid comparison, proposed data model with ERD, field mapping, media inventory, URLs, open questions).
- Generated `docs/legacy-urls.txt` (87 public URLs).

Decisions (proposed, pending approval):
- One shared grid model: `grid_rows` (polymorphic `gridable`, `area`, `layout`) + `grid_items` (media or news). Layouts are defined once in `config/grids.php`.
- `media.collection` column; `legacy_map` table for idempotent import.
- Merge press, awards and lectures into `entries` (Q6).

Open questions: see `docs/analysis.md` §10.

Security notes found in source projects (not carried over):
- Current: public `/artisan/*` routes; admin password in `INSTALL.txt`; Maps API key inline.
- Template: plaintext admin credentials in `app/Console/Commands/SeedUser.php`; production DB credentials in the local `INSTALL.txt`.
- Updated: plaintext admin password in `docs/strut/import.md`.

Password rotation is recommended for all three.

🛑 **Checkpoint 0 — waiting for approval.**

## 2026-09-27 — Checkpoint 0 approved

Decisions (by client):
- Q1: The local legacy DB and storage are a current production copy (imported 2026-09-27). 0 videos is correct.
- Q2: Upgrade to the latest majors (Laravel 13.x, Vite 8, Pinia 4, Pest 5, Tailwind 4.3).
- Q3: German only. No translation columns.
- Q4: Homepage dev/prod staging dropped. Row-level `publish` instead.
- Q5: Preview flags dropped.
- Q6: Press, awards and lectures merged into `entries` (type enum), with three admin menu entries.
- Q7: Werkliste PDFs (8 variants) and merged category PDFs kept 1:1.
- Q8: The 12 unreferenced `stadtterrasse_*` images are attached to project 51 on import.
- Q9: **Legacy users are imported** (existing password hashes kept; the client will rotate passwords).
- Q10: 301 redirects for old `/storage/media/...` and `/media/...` URLs.
- Q11: `/bauten` → 301 to `/werkliste`.
- Q12: Google Maps kept on `/kontakt`, API key via env (`GOOGLE_MAPS_KEY`).

## 2026-09-27 — Phase 1.1: Scaffold

- Copied the Template (without `.git`, `vendor`, `node_modules`, `.env`, build, storage data). FZA seed commands were removed *before* the first commit because `SeedUser` contained plaintext credentials.
  - An rsync exclude of `vendor` also dropped `resources/css/vendor`; this was noticed via the build error and restored.
- Renamed to "Strut Architekten": config (name, locale `de`, timezone `Europe/Zurich`, faker `de_CH`), composer metadata, web manifest, and strut favicons, logo and login splash. The favicon markup was copied in 3 layouts and is now one partial, `components/layout/partials/favicons.blade.php`.
- Created DB `rework_strut` (MAMP MySQL; credentials taken from the Template `.env`). Added `.env.example` (the Template had deleted it) with `LEGACY_DB_*`, `LEGACY_MEDIA_PATH` and `GOOGLE_MAPS_KEY`.
- Herd: `herd link` + `herd secure` → https://rework.strut.ch.test.
- **Upgrades** (approved Q2):

  | Package | From | To |
  |---|---|---|
  | laravel/framework | 13.7.0 | 13.33.0 |
  | pestphp/pest | 4.6.3 | 5.2.1 |
  | phpunit/phpunit | 12.5 | 13.3 |
  | league/glide | 3.2 | **4.1** |
  | vite | 7.3.2 | **8.3.1** |
  | laravel-vite-plugin | 2 | **3** |
  | pinia | 3.0.4 | **4.0.3** |
  | @uppy/core, @uppy/xhr-upload | 5 | **6** |
  | tailwindcss | 4.1.18 | 4.3.3 |
  | vue | 3.5.27 | 3.5.43 |
  | vue-router | 5.0.2 | 5.3.1 |
  | tiptap | 3.19 | 3.31 |

  - Removed the unused `@uppy/drag-drop` and `@uppy/status-bar`.
  - The Glide 4 API used by `ImageController` (`ServerFactory::create`, `makeImage`, `getCache`, `driver` option) is unchanged. Verified: webp and avif resize works over HTTPS.
- Verified:
  - `/` and `/login` return 200.
  - Admin login → `/dashboard` 200, `/api/dashboard/projects` 200 (throwaway user, deleted afterwards).
  - `php artisan test`: **100 passed**.
  - `npm run build` OK.
- CI workflow now targets `main`. Added `.nvmrc` (22).

## 2026-09-27 — Phase 1.2: Backend and admin

Architecture decisions:
- **Shared CRUD layer instead of per-module copies.** The Template repeats Store/Update/Delete/Reorder actions, identical store/update requests, Pinia stores and index tables in every module.
  - Backend: `ResourceController` (abstract), `ContentRequest` (fields plus German `attributes()`, uuid → id resolution), `Actions/Content/{Save,Delete,Reorder}Action`.
  - Frontend: `createResourceApi`, `defineResourceStore`, `useResourceForm`, `ResourceIndex`, `ResourceForm`, `FormField`, `MediaField`.
  - A content module is now: Model, Factory, Request, Resource, a controller of about 10 lines, and two small views.
- Traits:
  - `HasMedia`: collections `images`, `files`, `og`; media is deleted with its owner.
  - `HasGrid`: polymorphic grid rows.
  - `HasSortOrder`: appends within a group (projects per type, types per category).
- Enums with German labels: `ProjectStatus`, `Competition`, `EntryType`.
- An enforced morph map is used for all polymorphic relations.
- **Shared grid:**
  - `config/grids.php` is the one layout spec for both contexts.
  - `GridContext` handles validation, media scope and admin config.
  - One `GridController` with actions and requests.
  - One `GridEditor.vue` that renders every layout from the spec; layout icons are generated from it.
  - Homepage highlight slideshow = area `highlight` with layout `slideshow`.
  - New compared to Updated: rows can be dragged in the visual view, items can be dragged between slots (swap), a row's layout can be changed, rows have a publish flag, and placed media is marked in the picker.
- Validation messages come from `lang/de/validation.php` plus German attribute names (`ß` → `ss` in the lang file). `lang/de/auth.php` and `passwords.php` were added.
- Project public URL stays `/bauten/{id}/{slug}`: the import keeps legacy ids and the slug uses the legacy algorithm.

Template bugs found and fixed:
- `HasUuid` registered `creating` with an arrow function that returns the uuid. Model events halt on a returned value, so every later `creating` listener was skipped (sort order was always 0, the slug hook never ran). `HasGrid`'s `deleting` listener had the same problem (media of deleted projects stayed).
- `Drawer.vue`: a stale unmount timer closed a drawer that was opened within 200 ms of mounting.
- `DataTable.vue` keyed draggable rows by `id`, but rows only carry `uuid`.
- `AttachAction` dropped `is_teaser`; the teaser and OG flags were not scoped to a collection.
- The media library page (uploads were never attached) was removed; the Glide URL builder was duplicated in two places (now `MediaUrls`).

## 2026-09-27 — Phase 1.3: Import

`php artisan strut:import [--fresh] [--dry-run]`, then `php artisan strut:verify`.

- Read-only legacy connection: `SET SESSION TRANSACTION READ ONLY`; a test write was rejected by MySQL.
- Media is copied (never moved) from `LEGACY_MEDIA_PATH` to `storage/app/public/uploads`, then normalized (maximum 3200 px).
- Idempotent: a second run produced identical counts, file count and `legacy_map`.

### Import report (2026-09-27)

| Content | Legacy | Imported |
|---|---|---|
| Users | 3 | 3 (existing password hashes; please rotate) |
| Categories / types | 3 / 7 | 3 / 7 |
| Projects | 62 | 62 (legacy ids kept; all 62 URLs identical to `docs/legacy-urls.txt`) |
| Project images / videos / PDFs | 431 / 0 / 24 | 431 / 0 / 24 |
| Project grid rows / items | 130 / 314 | 130 / 314 |
| News | 26 | 26 |
| Pages from `content` / page images | 4 / 2 | 4 / 2 (+ 7 further pages created with the legacy SEO texts) |
| Homepage rows (+ highlight) / items | 27 + 1 / 67 | 28 / 66 |
| Team / jobs / books | 13 / 2 / 15 | 13 / 2 / 15 |
| Press / awards / lectures | 37 / 13 / 10 | 37 / 13 / 10 |

Notes on the report:
- **Skipped (1):** `home_grid_elements #143` duplicates #142 (same row, same position 1, same image; a double-click in the old admin). The live site renders #142.
- **Missing file (1):** `jobs.media #1` `5ede2863a5b0a_job_strut_architekten_2020.pdf` (unpublished job).
- **Unused legacy files, not imported (9):** test files and old copies, see §8 of the analysis.
- **Attached as decided (Q8):** 12 Stadtterrasse images → project 51 (unplaced).
- `ß` found: 0.
- Media records and files: 531 / 531 (≈382 MB).
- HTML cleanup: MS-Word markup removed (the worst description went from 40 KB to 808 characters); spans and class/style/lang attributes stripped; entities decoded.

### Verification (`strut:verify`)

All counts match (`jobs.media` 2 → 1 because of the missing file). There are:
- no dangling `legacy_map` entries;
- no media without an owner, no media without a file, and no files without a media record;
- no problems in any of the 158 grid rows / 380 items checked against the layout rules.

Result: **"Keine Probleme gefunden."**

### Admin click-through (headless Chromium, imported data)

- **Pages visited:** all 20 admin screens load without console errors or failing API calls.
- **Real write flows:**
  - News created with an uploaded image (Uppy 6 → temp → attached on save).
  - Grid row added in a project, image placed via the picker.
- **Bugs found and fixed:** the grid config serialization and the Drawer race.
- **Cleanup:** the test data was removed afterwards (`strut:import --fresh`, test user deleted).

Tests: **133 passing** (Pest 5): CRUD for all modules, projects, pages/entries, both grid contexts, media, import cleanup, auth.

## 2026-09-28 — Phase 2: Frontend kick-off

Decisions (by client):
- F1: Strict 1:1 reproduction for now; changes come in a later round.
- F2: The Basis Grotesque Pro web fonts are copied from the legacy project.
- F3: No Google Maps key yet. The map is built, and the key stays empty locally until go-live.
- Masonry (Packery on `/ueber-uns`, `/buecher`) becomes a vanilla module (approved).

## 2026-09-28 — Phase 2, step 1: Shell (tokens, fonts, layout, header, navigation)

- Separate public bundle: `resources/css/site.css` (Tailwind with `source(none)`, only public views scanned) and `resources/js/site.js` with modules `header.js`, `menu.js` and `debounce.js`.
- Tokens from the legacy SCSS config: breakpoints 600/900/1200, colours, and the type scale as utilities (`type-sm` etc., because size and line height change per breakpoint). Also the page block (434 px below 600, 1400 px from 1200) and the link underlines (background line at a fixed offset, as in legacy).
- `GetNavigation` action + view composer on `components.site.header`. Public routes are registered; pages that are not built yet render the empty shell (`pages.shell`). Project routes bind by id (`{project:id}`), because the model's route key is the uuid used by the admin API.
- **Visual result: 0.000 % at all 17 viewports on all 17 pages** (header/nav only, content hidden with `compare.js --shell`), including the states: mobile menu open, nested mobile menu (Büro → Bauten → Wohnen → Wohnhäuser), desktop dropdowns (single, nested, switching sections), header after scrolling down and up (375/900/1280).
- Tests: `tests/Feature/Site/NavigationTest.php` (8).

Decisions and details:
- Toggles are `<button>` elements (keyboard accessible) with the underline on an inner inline `<span>`. A button is inline-block, so an underline on the button itself sat 2 px lower than on the legacy inline `<a>`.
- The logo is the legacy SVG file as `<img>`. The inline SVG component rasterised with slightly different anti-aliasing.
- Legacy quirks kept for parity:
  - The JS switches at 901 px, the CSS at 900 px.
  - The lists of the current project stay open even when their toggle is clicked (legacy `display: block !important`).
  - A category without type headings opens only the first type's list (legacy `next('ul')`). This has no effect today: Gewerbe and Öffentlich have one type each.
  - The desktop header stays hidden if the page jumps straight to the top (legacy only clears `is-hidden` while scrolling up past 170 px).
- `compare.js` changes: `--shell` mode (viewport only, content hidden, tall body so scroll states work), gradual scroll back to the top before screenshots, and states with `viewportOnly`. Nav states run on the pages flagged `navStates` (home, first sample project).

### CSS review (client feedback, 2026-09-28)

The step 1 CSS was reworked after the client's review:
- **Text scale:** `text-xs` … `text-6xl` covers all 10 legacy font sizes (15–51 px), with no `text-base`. Line heights are inline arbitrary values.
- **No custom utilities** (`type-*`, `link-*` and `page-block` removed). Underlines and the page block are inline in the Blade components; `html`/`body` classes sit on the elements. `base.css` only holds global element rules, written with `@apply`.
- **JS state as data attributes** with named variants: `header[data-scroll=tiny|hidden]` → `header-tiny:` / `header-hidden:`, and `html[data-menu-open]` → `menu-open:`. Lists keep `data-open` / `data-current`, and entries `data-active`.
- **Navigation split into components** `x-site.nav.item|toggle|list|link`; no class strings in `@php` any more.
- **Header heights as tokens** (`h-header`, `pt-header-md`, …).

Result unchanged: 0.000 % on all shell comparisons (319; the 136 errors are page states that don't exist yet).

## 2026-09-28 — Phase 2, step 2a: Presse, Auszeichnungen, Vorträge

- One page for all three: `Site\EntryController` (type from the route defaults) → `GetEntries` action → `pages.entries`. `GetEntries` reproduces the legacy `AppHelper::partition`: years newest first, split into three columns of whole years (`ceil(years / 3)` per column).
- Shared Blade components, reusable for the Werkliste (legacy `%card` / `%card-group`): `x-site.heading` (page title), `x-site.card` (dash, or arrow when linked), `x-site.card.heading` (year/group heading with the rule), `x-site.link` (green link, underline on hover), `x-site.entry`.
- The title links to the PDF if there is one, else to the URL (as legacy). Images: Glide `?w=500` (legacy "xsmall" = 500 px wide), admin crop applied (`Media::imageUrl()`); files: `Media::url()`.
- **Visual result: 0.000 % at all 17 viewports on all three pages** (51 comparisons). Link hover state checked separately at 375 and 1440 px: 0 px difference.
- Tests: `tests/Feature/Site/EntriesTest.php` (column split, published/type filter per page, PDF-before-URL link, project reference).

## 2026-09-28 — Phase 2, step 2b: Werkliste (Status, Jahr, Typ)

- `Site\WorksController` (status / year / type) → `GetWorks` action → `pages.works.{status,year,type}` inside `x-site.works` (tabs + PDF link). `/werkliste` and `/werkliste/status` render the same view, as in legacy.
- Orders as legacy: all published projects by year desc, then name. Status: Ausgeführt | In Planung + Studie | Wettbewerb (1. Preis, 2. Preis, Andere; each ordered by status, then year desc). Year: three columns of whole years (`App\Support\Columns`, now also used by `GetEntries`). Type: published categories → published types → projects, empty ones left out.
- New shared components: `x-site.article` (rule + larger text, legacy `.content article`), `x-site.file-link` (document icon link). `x-site.card` / `x-site.card.heading` got a `dense` variant for the Werkliste. No preview images (Q5).
- PDF links point to the legacy URLs `/werkliste/pdf/{status|jahr|typ}` (route `pdf.works`, returns 404 until step 5).
- **Visual result:** layout 0.000 % at all 17 viewports on all three views, verified by temporarily sorting with the legacy order (51 comparisons, 0 differences). Tab and PDF-link hover states: 0 px difference.
- **Explained difference (order):** legacy sorts by the raw JSON `name` column, e.g. `{"de":"Dürrenrain"}` sorts after `{"de": "Schollenholzstrasse…"}` and `Ärgete` after `Sky-Frame`. That is a storage artefact, not an intended order, so the rework sorts alphabetically by name. It moves a few projects within the same year (2018, 2015, 2014, 2008, 2005, 1997 and the related status/type lists), so the plain comparison shows 0.1–2.5 % on the Werkliste pages.
- Legacy details kept: the PDF link label is underlined only while the pointer is on the label itself (`.icon-file:hover span:hover`); the year headings keep the 24 px top margin from 900 px (the legacy `> div + h2` rule outranks the Werkliste override).
- Tests: `tests/Feature/Site/WorksTest.php` (status/competition grouping and order, year columns, empty types/categories, active tab + PDF link + detail links per view).

## 2026-09-28 — Phase 2, step 2c: Downloads

- `Site\DownloadsController` → `pages.downloads`. Projektdokumentationen reuse `GetWorks::byType(withFiles: true)`: only projects with PDFs, and only types and categories that have some (as legacy). Werkliste: the 8 PDF links. Jobs: published job PDFs, else "Zur Zeit sind alle unsere Stellen besetzt."
- `x-site.article` got `large` (off here: the Downloads page keeps the body text size).
- Category PDF links use the new route `pdf.category` (`/download/pdf/{id}/{slug}`, 404 until step 5). The category ids changed in the import (legacy 1–3, now 14–16), so the old links need a redirect in step 5.
- **Grid columns:** Tailwind's `grid-cols-3` is `repeat(3, minmax(0, 1fr))`, legacy uses `repeat(3, 1fr)`, whose tracks grow to fit the longest word. "Projektdokumentationen" at 31 px widens the first column at 900–1024 px. All list grids now use the legacy definition (`grid-cols-[repeat(3,1fr)]`).
- **Visual result: 0.000 % at all 17 viewports.** Presse/Auszeichnungen/Vorträge re-checked after the grid change: still 0.000 %.
- Tests: `tests/Feature/Site/DownloadsTest.php`.

## 2026-09-28 — Phase 2, step 2d: Jobs

- `Site\JobsController` → `pages.jobs`: published job listings (title, lead, info, PDF link), else the page text; page images in the right column.
- New shared components: `x-site.prose` (admin rich text; links get the green hover underline), `x-site.page-images` (page images with `data-lightbox="single|gallery"`, reused by Über uns).
- `Media::imageUrl('xs|sm|md|lg')`: the legacy image sizes. Legacy scales landscape images to the max width and all others to the max height (500/350, 900/500, 1200/800, 1600/1100), without upscaling; the Glide params reproduce that.
- **Fix in the global CSS:** legacy removes the bottom margin of `p:last-child` **and** `p:last-of-type`; `base.css` only had `last-child`. A job text ending in `<p>` + link was 16 px taller.
- **Visual result: 0.000 % at all 17 viewports** (no job is published, so the page text shows). The job listing branch was checked separately: jobs temporarily published in the new DB, and the legacy template's markup for the same jobs injected into the legacy page (the legacy DB stays read-only). 0 px difference at 11 widths from 375 to 2560. Jobs unpublished again afterwards.
- The lightbox itself (Fancybox replacement) is not built yet; without JS the image link opens the large image. It follows with `/ueber-uns` in step 3.
- Legacy job 1's PDF is missing in the legacy storage, so the import has no file for it; the rework shows no download link for that job (legacy would link to a missing file).
- Tests: `tests/Feature/Site/JobsTest.php`.

## 2026-09-28 — Phase 2, step 2e: Kontakt

- `Site\ContactController` → `pages.contact`: contact page text, "Impressum" toggle (imprint page, only when published), "Datenschutz" toggle (the legacy hard-coded text, verbatim in `pages/partials/privacy.blade.php`), map and "Auf Google Maps anzeigen".
- `modules/toggle.js`: a `[data-toggle]` button shows/hides the element in its `aria-controls` (`hidden` attribute) and sets `aria-expanded`. Component `x-site.toggle` (green, chevron down/up). The chevron sits on an inner inline span: on the `<button>` itself it was 2 px lower than on the legacy inline `<a>`, whose box is the font's content area, not the line box.
- `modules/map.js`: legacy styles, centre, zoom and marker. Loads the Maps API only when `GOOGLE_MAPS_KEY` is set (`data-map-key`); without a key the container stays empty (F3). **Not seen with a real key yet** — check once the key exists.
- Legacy detail: the legacy CSS does not reset `ul`, so the Datenschutz list has the browser defaults (1em margins, 40 px indent); reproduced on that list.
- **Visual result: 0.000 % at all 17 viewports**, also with Impressum open and with Datenschutz open (new state `datenschutz-open` in `tests/visual/pages.js`; map masked). Toggle hover, contact link hover and open → close: 0 px difference at 375 and 1440.
- Tests: `tests/Feature/Site/ContactTest.php`.

## 2026-09-28 — Phase 2, step 3: Über uns, Bücher, masonry, lightbox

Client decisions: alphabetical Werkliste order approved (see step 2b); custom lightbox instead of fancyBox (approved).

- `Site\AboutController` → `pages.about` (page text + images, team with CV toggle), `Site\BooksController` → `pages.books` (cover, description, "Info" toggle, order link by mail or URL).
- **`modules/masonry.js`** is a port of the legacy Packery 2.1.2 placement, not an approximation: free spaces sorted top-to-bottom then left-to-right, first fit with 1 px tolerance, gutter added to each item, x as a percentage of the grid width, grid height = lowest edge − gutter. Layout after all images **and web fonts** have loaded (with images cached, the first layout ran before the font and measured one team card shorter), again when the width changes, and a column-keeping re-pack after a toggle (Packery `shiftLayout`, as legacy). `modules/toggle.js` now fires `toggle:change`.
- **`modules/lightbox.js`** + `x-site.lightbox` (in the site layout) replace fancyBox 3.5.7 (jQuery). Native `<dialog>` (focus trap, Escape, inert page). fancyBox 3 geometry: image fitted to the viewport minus 44 px top/bottom (6 px when the viewport is ≤ 576 px high), not enlarged, centred and rounded down; zoom from the thumbnail on open and close, fade between slides, white background fading in, all 366 ms. Gallery: left/right half of the screen browse, with the legacy arrow cursors (grey at the ends; PNGs copied from legacy); arrow keys and swipe. Single view: click outside the image closes.
  - Not reproduced: fancyBox's click-to-zoom to the image's natural size in the single view.
- New components: `x-site.masonry` / `x-site.masonry.item`, `x-site.arrow-link` (legacy `.icon-arrow`); `x-site.toggle` got `reverse` (chevron left, legacy `.is-reverse`); `x-site.page-images` takes `gallery` explicitly (Über uns always opens single images, as legacy; Jobs a gallery when there are several).
- **Visual result:**
  - Über uns: 0.000 % at all 17 viewports and with a CV open, except: one pixel inside the photo at 901 px (resampling), and the `cv-open` state at 1199 px, where the fixed header is drawn 1 px apart. That comes from the test click: Playwright scrolls the clicked toggle into view, and the legacy inline `<a>` and our `<button>` boxes differ by 1 px, so the page ends at scrollY 1441 vs 1442. The page itself is identical.
  - Bücher: 2–39 scattered pixels per viewport, all inside the book covers (JPEG re-encoding: Glide vs the legacy GD files). Layout identical, also with "Info" open.
  - Lightbox open vs legacy fancyBox: 0 px at 1440×900, 375×700 and 900×500. Gallery behaviour checked in the browser (arrows, grey end cursors, keys, click areas, caption, close; no JS errors). The real galleries (project pages) follow in step 4.
- All earlier pages re-checked: unchanged.
- Tests: `tests/Feature/Site/MasonryPagesTest.php`.

## 2026-09-28 — Phase 2, step 4: grid, project detail, homepage

- **Grid:** `GridContext::fill($layout, $items)` turns a row into its layout's columns and cells. Legacy behaviour: items sorted by position and **re-indexed**, so an empty slot leaves no gap (one live row, project 8, has a single item at position 1; it shows on the left, as in legacy). Rendering per context: `x-site.grid.project` (legacy grid-2x1fr + grid-stack: a single-cell column shows the media directly, stacked cells keep 687×458 / 687×940, spacers fill; empty columns left out) and `x-site.grid.home` (legacy ratio boxes, every box rendered even when empty).
- **Project detail:** `Site\ProjectController` + `GetProject`. 301 to the canonical slug for a missing or wrong slug; unpublished → 404; projects without a detail page stay reachable. Prev/next in menu order, wrapping (`GetNavigation::projectIds()`); legacy quirk kept: a project not in the menu counts as the first one. Hover labels "Vorheriges/Nächstes Projekt" in CSS (`group-has-[…]`), no JS. "Info" panel: `modules/toggle.js` got `data-toggle="open"` (sets `data-open`, the text stays in the layout below 900 px) and `data-toggle-dismiss` (outside click closes, as legacy). Next-project teaser below 600 px. Images open in the lightbox gallery.
- **Homepage:** `Site\HomeController` + `GetHome`. Highlight slideshow `modules/slideshow.js` (Swiper 12 with the legacy options: fade, 4.5 s, 1.5 s, loop; a video slide stops autoplay until it has played), shuffled per request. Grid tiles link to their project with the hover caption; news tiles (`x-site.news`) with the legacy per-breakpoint type sizes and date offsets; unpublished news are not shown (none is in the grid today).
- **Image sizes, exact:** Glide's `fit=max` rounds the height first and then recomputes the width, so a 2500×1667 image became 499×333 instead of legacy's 500×333, which moved two news tiles by 0.1 px. `Media::imageUrl()` now computes the legacy target size itself (landscape ≥ max width → width, else height ≥ max height → height, other side rounded; smaller images unscaled) and passes exact `w`/`h` with `fit=stretch`.
- Bug found and fixed during the comparison: the first `fill()` captured its counter in an arrow function (by value), so every cell got the column's first item.
- `pages/shell.blade.php` removed: every public route has its page now.
- **Visual result:**
  - 5 sample projects × 17 viewports, plus "Info" open (900 px and up) and the navigation states: layout identical. What remains is scattered pixels inside photos (at most 253 px per page).
  - Homepage × 17 viewports plus the navigation states (slideshow masked): layout identical; up to ~3,800 scattered pixels per page, all inside photos.
  - Checked with a script that maps every diff pixel against the image boxes of the page: 0 diff pixels outside images on the homepage (5 widths), project 60 and Bücher.
  - Interactions: browse hover labels, Info open → cross / outside click / inside click, news link hover: 0 px. Lightbox on a real project gallery vs fancyBox: 0–1 px; after browsing only the close button differs, because our dialog shows a `:focus-visible` ring after keyboard use (legacy hides all focus outlines). Kept on purpose for keyboard users (checklist §6).
  - Slideshow vs legacy: same box (16:10, same position), autoplay advances, cross-fade, hover caption identical (40 % white, 51 px), no JS errors.
- **All projects** (`--all-projects`, 47 projects × 375/600/900/1440): layout identical except project 11 below 900 px (16 px shorter). The legacy description ends in an empty `<p></p>`, so its last real paragraph keeps a 16 px bottom margin; the import cleaned the empty paragraph out of the HTML. Content artefact, left as is.
- **Data note:** the highlight row had 7 slides, legacy 6. The extra one (HB-Therm, grid item 1526, added in the admin on 2026-09-27 17:54) was removed on the client's request; the image itself stays with its project.
- Tests: `tests/Feature/Site/ProjectTest.php`, `tests/Feature/Site/HomeTest.php`.
