# strut.ch

[![Tests](https://github.com/marceli-to/strut.ch/actions/workflows/tests.yml/badge.svg)](https://github.com/marceli-to/strut.ch/actions/workflows/tests.yml)

Rebuild of [strut.ch](https://strut.ch) (Strut Architekten, Winterthur): a Laravel site with a Vue admin, replacing the legacy strut.ch code base. The public site matches the legacy site 1:1; content and media are imported from the legacy database.

## Stack

- **Backend:** Laravel 13, PHP 8.4, MySQL
- **Admin:** Vue 3 SPA (Vue Router, Pinia), TipTap, Uppy, vuedraggable, vue-advanced-cropper, Phosphor Icons
- **Public site:** Blade, Tailwind CSS 4, a few vanilla JS modules (header, menu, masonry, lightbox), Swiper for the homepage slideshow
- **Images:** Glide + Imagick, delivered as AVIF/WebP/original through `<picture>`
- **PDFs:** dompdf (Werkliste) and TCPDI (merged project documentation per category)
- **Tests:** Pest; Playwright for the visual comparison with the legacy site and the accessibility audit

## Admin (`/dashboard`)

- **Startseite:** homepage grid with the highlight slideshow
- **Projekte:** data, images & videos, documentation PDFs, project grid ("Raster")
- **News, Seiten** (content and SEO per page), **Jobs, Team, Auszeichnungen, Vorträge, Bücher, Presse**
- **Kategorien** (with their types), **Benutzer**

Shared building blocks: `ResourceController` / `ContentRequest` / `Content\*Action` on the backend; `ResourceIndex`, `useResourceForm`, `FormField` and `MediaField` in the admin. Grids for projects and the homepage share one editor (`config/grids.php`, `GridContext`, `GridEditor`); media fields are configured per profile in `config/media.php` (file types, crop ratios).

## Setup

```bash
composer install
npm install
cp .env.example .env        # DB, LEGACY_DB_*, LEGACY_MEDIA_PATH, GOOGLE_MAPS_KEY
php artisan key:generate
php artisan migrate
php artisan strut:import    # content and media from the legacy database (idempotent)
php artisan strut:verify    # compare with the legacy data
php artisan app:create-user
npm run build
```

Locally the site runs on Herd at https://strut.ch.test (PHP 8.4); the legacy site for the visual comparison runs at https://legacy.strut.ch.test (PHP 8.2, isolated). `public/build` is committed: after `npm run build`, commit the changed assets.

## Commands

| Command | Purpose |
|---|---|
| `strut:import [--fresh] [--dry-run]` | Import content and media from the legacy strut.ch database |
| `strut:verify` | Check the imported data against the legacy database and the file system |
| `strut:check-urls` | Check that all legacy strut.ch URLs still work (200, or 301 → 200) |
| `strut:legacy-files` | Fill in the legacy file names of imported media (old media URL redirects) |
| `images:warm` | Generate all public image variants into the Glide cache |
| `images:clear` | Clear the Glide image cache |
| `images:normalize` | Downsize stored images above the upload threshold |
| `media:clean-temp` | Delete abandoned temporary uploads (scheduled daily) |
| `app:create-user` | Create an admin user |

## Tests

```bash
php artisan test                          # feature tests (sqlite in memory)
node tests/visual/compare.js [--page=…]   # screenshots vs. the legacy site, report in tests/visual/output/
node tests/visual/a11y.mjs                # axe accessibility audit of the public pages
```

The acceptance checklist with the results of each test run is [Strut Rework Test Run](https://claude.ai/artifact/CsE2CtPCRRbENGX5LqgthW).

## Documentation

- `docs/deployment.md` — server requirements, environment, go-live checklist
- `docs/analysis.md` — legacy analysis, data model, field mapping, legacy URLs and breakpoints
- `docs/progress.md` — decisions, import report, per-step notes
- `docs/frontend-checklist.md` — public site parity checklist
- `docs/changes-01.md` … `docs/changes-03.md` — feedback rounds
- `docs/legacy-urls.txt` — legacy URLs checked by `strut:check-urls`
