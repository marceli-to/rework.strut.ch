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
