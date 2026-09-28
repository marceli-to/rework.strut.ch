# Frontend parity checklist (Phase 2)

Every page type and feature of the Current site (`strut.ch.test`), with the new implementation.

- **Scope:** strict 1:1 (F1).
- **Done means:** the visual diff is 0 %, or every remaining difference is explained in `docs/progress.md`, at all 17 viewports.
- **Comparison:** `node tests/visual/compare.js` (see `tests/visual/pages.js` for pages, states and masks).

Status: ☐ open · ◐ built, diff not clean yet · ☑ done.

## 0. Setup

- ☑ Playwright + pixelmatch as dev dependencies, `tests/visual/compare.js`, output git-ignored.
- ◐ Tokens in the Tailwind theme (`resources/css/site.css`): breakpoints 600/900/1200, colours, text scale `text-xs`…`text-6xl` (all 10 legacy sizes, no `text-base`), header heights, page widths. Page-specific values follow with each page.
- ☑ Fonts: Basis Grotesque Pro Regular + Medium, woff2/woff copied from the legacy project (F2).
- ☑ Separate public entry points (`resources/css/site.css`, `resources/js/site.js`); the admin bundle is not loaded on the public site.

## 1. Shell

| # | Feature | Legacy | New | Status |
|---|---|---|---|---|
| 1.1 | Layout: `<head>`, SEO/OG tags, favicons, main wrapper (home gets `site-content--home`) | `web/layout/app.blade.php` | `x-layout.site` | ◐ (SEO tags in step 5) |
| 1.2 | Header: menu button, logo linking to `/` | same | `x-site.header` | ☑ |
| 1.3 | Header on scroll: < 901 px `is-tiny` when scrolling down, reset at top. ≥ 901 px hidden when scrolling down past 170 px, shown tiny when scrolling up | `header.js` | `modules/header.js` | ☑ |
| 1.4 | Navigation, desktop (≥ 901 px): nested dropdowns (Bauten → category → type → projects; Publikationen; Büro). Menu height follows the open list (+30 px). Click outside closes it | `menu.js` | `modules/menu.js` | ☑ |
| 1.5 | Navigation, mobile (≤ 900 px): full menu toggled by the button (`has-menu` on `<html>`), same nested accordions | `menu.js` | `modules/menu.js` | ☑ |
| 1.6 | Menu data: published categories → active types → published projects **with detail**; active states for the current project/category/type and the current page | `NavigationService` | `GetNavigation` action + view composer | ☑ |
| 1.7 | "Werkliste" is only marked active on `/werkliste`, not on `/werkliste/status|jahr|typ` (legacy quirk, kept 1:1) | same | same | ☑ |
| 1.8 | No footer | — | — | ☑ |

## 2. Page types

| # | URL | Content | Notes | Status |
|---|---|---|---|---|
| 2.1 | `/` | Highlight slideshow + homepage grid (3fr, 2fr-1fr, 1fr-2fr in use; all 11 layouts supported) with image, video and news tiles, caption hover overlay | Slideshow is shuffled per request (masked in the diff, checked by hand) | ☑ |
| 2.2 | `/bauten/{id}/{slug?}` | Type heading, prev/next browse with hover labels, title, "Info" toggle for description + info + PDF downloads, project grid (7 layouts) with lightbox, "Nächstes Projekt" teaser | Any slug → 301 to the canonical slug. Projects without a detail are still reachable | ☑ |
| 2.3 | `/werkliste`, `/werkliste/status` | Tabs Status/Jahr/Typ + PDF link; columns Ausgeführt / In Planung + Studie / Wettbewerb (1. Preis, 2. Preis, Andere) | Items link only when the project has a detail. No preview images (Q5) | ☑ |
| 2.4 | `/werkliste/jahr` | Grouped by year, in columns | | ☑ |
| 2.5 | `/werkliste/typ` | Category → types (headings only when `show_types`) | | ☑ |
| 2.6 | `/presse` | Year groups in columns: title (link to file or URL), description + project reference, small image | Shared "entries list" component with 2.10 and 2.11 | ☑ |
| 2.7 | `/buecher` | Masonry, 3 columns ≥ 600 px: title, image, description, "Info" toggle, order link (mailto with subject/body, or external URL) | Masonry → vanilla `modules/masonry.js` (approved) | ☑ |
| 2.8 | `/downloads` | Projektdokumentationen per category ("Alle …" merged PDF + per project), Werkliste PDFs (8), Jobs PDFs or "Zur Zeit sind alle unsere Stellen besetzt." | | ☑ |
| 2.9 | `/ueber-uns` | Intro text + page images (lightbox), team masonry: name (mailto), role, position, portrait, phone, email, "Lebenslauf" toggle | Masonry as 2.7 | ☑ |
| 2.10 | `/auszeichnungen` | Like 2.6 without project reference | | ☑ |
| 2.11 | `/vortraege` | Like 2.10 | | ☑ |
| 2.12 | `/jobs` | Job list (title, lead, info, PDF link) or the page text if there are no jobs; page images with lightbox (gallery if more than one) | Lightbox: step 3 | ☑ |
| 2.13 | `/kontakt` | Contact text, "Impressum" toggle (imprint page), "Datenschutz" toggle (static text, kept 1:1), Google Map (styled, fixed coordinates) + "Auf Google Maps anzeigen" link | No key yet (F3). The map is masked in the diff | ☑ (map: without a key only the container, see step 2e) |
| 2.14 | 404 | Error page in the site layout | 500 follows the same pattern | ☐ |
| 2.15 | PDFs | `/werkliste/pdf/{8 variants}`, `/download/pdf/{id}/{slug}` (merged category PDFs) | Kept 1:1 (Q7); compared with live strut.ch by text and rendered pages | ☑ |

## 3. Shared pieces

- ☑ **One grid for both contexts**, driven by `config/grids.php` (`GridContext::fill()`): `x-site.grid.project` (flex stacks, layout ratios) and `x-site.grid.home` (ratio boxes), with `x-site.grid.media`, `x-site.news` and `x-site.caption`.
- ◐ Images through the Glide pipeline (`/img/...`) at the exact legacy sizes (`Media::imageUrl()`); alt texts from the media records. `srcset`/`sizes` and `loading="lazy"` are not in legacy and would change nothing visible: step 5 (performance), after parity.
- ☑ Lightbox (replaces Fancybox): single image and gallery, custom close/prev/next buttons with an inactive state at the ends, caption. Vanilla module, no dependency.
- ☑ Slideshow: Swiper fade (4.5 s, 1.5 s, loop). Swiper is already a dependency and works without jQuery. A video slide pauses autoplay until it ends.
- ☑ Toggles: one `modules/toggle.js` for Impressum/Datenschutz, book info, team CV (the masonry re-layouts on `toggle:change`) and project info (`data-toggle="open"`, closes on outside click).
- ☑ Masonry: `modules/masonry.js`, port of the legacy Packery placement; runs after images and fonts load, on width change, and re-packs after a toggle. One column below 600 px.
- ◐ Map: `modules/map.js`, Google Maps JS API, styles and coordinates from the legacy code, key from `GOOGLE_MAPS_KEY`; renders nothing without a key.

## 4. SEO

- ☑ Titles and descriptions per page (legacy texts; listing pages from `pages.meta_description`), OG tags, OG image (project: first image; default `strut.ch-og.png`).
- ☑ Canonical URLs: `/werkliste/status` → canonical `/werkliste`; projects → canonical slug.
- ☑ `sitemap.xml` (route: 12 pages + published projects with a detail page), `robots.txt` (route: production allows all and names the sitemap, every other environment `Disallow: /`).

## 5. URL continuity

- ☑ All 87 URLs in `docs/legacy-urls.txt` return 200 or 301 → 200: `php artisan strut:check-urls` (against the real data; the Pest suite runs on an empty test database). Redirect logic covered by `LegacyRedirectTest`.
- ☑ `/bauten` → 301 `/werkliste` (Q11). Old `/storage/media/…` and `/media/…` → 301 to the new media URLs via `legacy_map.legacy_file` (Q10). Old category PDF ids (1–3) → 301 to the new ones.
- ☑ Dropped (404): `/bauten/vorschau/{id}`, `/404`, `/500`, `/artisan/*`.

## 6. Accessibility

- ☐ Semantic landmarks and headings; buttons instead of `href="javascript:;"` (styled identically); `aria-expanded` on toggles and menus; focus states; menu usable with the keyboard; Escape closes the menu and lightbox.

## 7. Tests

- ☐ Feature test for every public route (status + key content).
- ☑ Redirect test (5).
- ☐ Visual report: all pages × all viewports (+ states) in `tests/visual/output/report.md`, remaining differences listed in `docs/progress.md`.

## Build order

1. Tokens, fonts, layout, header, navigation (desktop + mobile) → compare on `/presse` (simplest content page).
2. Entries lists (`/presse`, `/auszeichnungen`, `/vortraege`), then `/werkliste` (3 views), `/downloads`, `/jobs`, `/kontakt`.
3. Masonry pages (`/ueber-uns`, `/buecher`).
4. Grid component → project detail, then the homepage (slideshow, news tiles).
5. PDFs, SEO, redirects, 404, accessibility pass, tests, full report.

## Legacy behaviour kept on purpose (worth a later round)

- The Datenschutz text is hard-coded in the view and describes Google Analytics, which the site doesn't use.
- "Werkliste" menu item not active on its sub-views (1.7).
- `/werkliste` and `/werkliste/status` show the same page (handled with a canonical tag).
- **Not kept:** the legacy Werkliste sorts same-year projects by the raw JSON of the name (`{"de": "…"}`), so escaped umlauts and JSON spacing decide the order. The rework sorts by name (see `docs/progress.md`, step 2b).
