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
