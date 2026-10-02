# Deployment — rework.strut.ch

Everything the server needs besides the code. Status markers: ☐ to do on the server · ⏳ comes with Phase 2 (frontend).

## 1. Server requirements

| | |
|---|---|
| PHP | **8.4** (composer requires ^8.3) |
| PHP extensions | `imagick` (**required**: Glide image driver, upload normalization; its ImageMagick must be able to write **AVIF and WebP**, check with `php -r 'print_r(array_intersect(["AVIF","WEBP"], Imagick::queryFormats()));'` — a missing format is simply not offered), `pdo_mysql`, `mbstring`, `fileinfo`, `intl`, `exif`, `openssl`, `tokenizer`, `xml`, `ctype`, `curl`, `zip` |
| Database | MySQL 5.7+ / 8 or MariaDB 10.6+, `utf8mb4` |
| Web server | nginx or Apache; document root = `public/` |
| Node | **not needed on the server**: built assets (`public/build`) are committed |
| Composer | 2.x |

### PHP / web server limits (uploads up to 200 MB, videos)
- `upload_max_filesize = 200M`, `post_max_size = 210M`
- `memory_limit = 512M` (Imagick normalizes images up to 3200 px)
- `max_execution_time = 120`
- nginx: `client_max_body_size 210m;`
- **Apache:** the project has **no `public/.htaccess`**. It was never part of the Template, whose `.gitignore` excludes it. On Apache, add Laravel's standard `public/.htaccess` (rewrite to `index.php`) on the server, or remove it from `.gitignore` and commit it. ☐

## 2. Environment (`.env`)

Start from `.env.example`. Production values:

```
APP_NAME="Strut Architekten"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://strut.ch
APP_KEY=                       # php artisan key:generate (once)
APP_LOCALE=de

DB_CONNECTION=mysql            # DB_HOST / DB_PORT / DB_DATABASE / DB_USERNAME / DB_PASSWORD

SESSION_DRIVER=database        # tables created by the migrations
CACHE_STORE=database
QUEUE_CONNECTION=sync          # nothing is queued, no worker needed

MAIL_MAILER=smtp               # required for "Passwort vergessen"
MAIL_HOST= / MAIL_PORT= / MAIL_USERNAME= / MAIL_PASSWORD= / MAIL_SCHEME=
MAIL_FROM_ADDRESS="mail@strut.ch"

GOOGLE_MAPS_KEY=               # ⏳ contact page map (Phase 2); restrict the key to the domain
```

`LEGACY_DB_*` and `LEGACY_MEDIA_PATH` are **only** needed if the import runs on the server (see §5). Leave them empty otherwise.

## 3. First deployment ☐

```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate            # only if APP_KEY is empty
php artisan migrate --force
php artisan storage:link            # public/storage → storage/app/public (media, PDFs)
php artisan optimize                # config, route, event and view cache
```

Permissions: the web server user must be able to write to `storage/` and `bootstrap/cache/`. Uploads live in `storage/app/public/uploads`, the image cache in `storage/app/.glide-cache`.

## 4. Scheduler (cron) ☐

The app uses Laravel's scheduler, which needs **one** cron entry on the server:

```
* * * * * cd /path/to/site && php artisan schedule:run >> /dev/null 2>&1
```

Scheduled tasks (`routes/console.php`, list with `php artisan schedule:list`):

| Task | When | Purpose |
|---|---|---|
| `media:clean-temp` | daily 03:30 | Deletes uploads older than 24 h that were never saved with a form (`storage/app/public/temp`), plus their cached thumbnails |

No queue worker, no Horizon, no websockets.

## 5. Content import (one-time, at go-live) ☐

The content comes from the **live** strut.ch database and its media folder, imported with `strut:import`. The import builds all content from scratch (`--fresh`), so it can run again at any time until go-live.

**Nothing entered in the new admin is lost by `--fresh`**, as long as no editorial content is added there before go-live. As of 2026-09-28 the new database holds only imported content, the system pages (recreated by the import) and a few admin tests. If content is entered in the new admin before go-live, stop and plan the migration again.

### 5.1 Content freeze ☐

Agree a date from which nobody edits the old CMS. Anything entered on the old site after the export is missing on the new one. Keep the gap between export (5.2) and domain switch (5.5) short: the steps take well under an hour.

### 5.2 Export from the live server ☐

- Database dump: `mysqldump --single-transaction --default-character-set=utf8mb4 <live-db> > strut-live.sql`
- Media folder: copy `storage/app/public/media` from the live server, e.g. `rsync -av <live>:<path>/storage/app/public/media/ ./strut-live-media/`. It must include the `downloads/` subfolder (project, press and job PDFs).

### 5.3 Import locally (recommended) ☐

1. Load the dump into a **separate** local database, so the existing legacy copy (`strut.ch`) stays untouched as the comparison reference for the visual tests:
   ```
   mysql -h127.0.0.1 -uroot -e "CREATE DATABASE strut_prod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
   mysql -h127.0.0.1 -uroot strut_prod < strut-live.sql
   ```
2. Point the import at the fresh copy in `.env`: `LEGACY_DB_DATABASE=strut_prod`, `LEGACY_MEDIA_PATH=/absolute/path/to/strut-live-media`.
3. Run:
   ```
   php artisan strut:import --dry-run    # report only, writes nothing: check counts and missing files
   php artisan strut:import --fresh      # deletes imported content and files, imports again
   php artisan strut:verify              # must report "Keine Probleme gefunden"
   php artisan strut:check-urls          # all legacy URLs must answer 200 or 301 → 200
   ```
   `--fresh` also rebuilds `legacy_map`, including the legacy file names that old image and PDF URLs redirect with.
4. If projects were added on the live site since 2026-09-27, extend `docs/legacy-urls.txt` with their URLs (format `kind<TAB>path`, see the file) before `strut:check-urls`.
5. Set `.env` back to `LEGACY_DB_DATABASE=strut.ch` and the old `LEGACY_MEDIA_PATH` afterwards (the visual comparison uses the local legacy copy).

### 5.4 Transfer to the server ☐

1. Dump the local `rework_strut` database and import it on the server:
   ```
   mysqldump -h127.0.0.1 -uroot --single-transaction rework_strut > rework-strut.sql
   ```
2. Copy `storage/app/public/uploads/` (about 380 MB, 531 files as of 2026-09-28) to the same path on the server.
3. On the server: `php artisan optimize`, then `php artisan images:warm` ☐ (every public image variant: legacy sizes × JPEG/PNG, WebP, AVIF; about 4,100 files, ~7 minutes locally; existing variants are skipped, so it can run again at any time). Without it, the first visitor of each image waits for its encode.
4. On the server: `php artisan strut:check-urls` ☐ must report "0 fehlerhaft".

**Alternative: import directly on the server.** If the server can reach the live database (read-only user) and the media folder: set `LEGACY_DB_*` and `LEGACY_MEDIA_PATH` there, run the commands of 5.3 step 3, then `images:warm`, and clear all `LEGACY_*` values afterwards. This saves the transfer, but needs access to both systems at the same time.

### 5.5 Switch ☐

Point the domain to the new server (see §7), then check a few pages, a PDF and an old image URL (e.g. `/storage/media/large/…` from a search engine result) on the live domain.

Notes:
- The import is idempotent; `--dry-run` shows the report without writing anything.
- Legacy source files are only ever copied, never changed.
- New categories on the live site: the Werkliste PDFs "Wohnen", "Gewerbe", "Öffentlich" are tied to the legacy category ids 1–3 and keep working; a new category appears in the "Gesamt" and "Typ" PDFs and on the Downloads page.
- **Users** are imported with their old passwords. **Rotate all passwords after go-live** ☐ (the admin's "Passwort vergessen" flow works once mail is configured).

## 6. Every further deployment

```bash
git pull
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize
```

After changing image processing (sizes, qualities, crops of existing media): `php artisan images:clear`, then `php artisan images:warm`.

## 7. Go-live checklist

The same items are in the Go-live section of the acceptance checklist ([Strut Rework Test Run](https://claude.ai/artifact/CsE2CtPCRRbENGX5LqgthW), `live-1` … `live-12`); record the results there too.

- ☐ Cron entry for the scheduler (§4)
- ☐ Mail configured and "Passwort vergessen" tested
- ☐ `APP_DEBUG=false`, `APP_ENV=production`, HTTPS
- ☐ Content imported and `strut:verify` clean (§5)
- ☐ Passwords of imported users rotated
- ⏳ 301 redirects for changed URLs (`/bauten` → `/werkliste`, old `/storage/media/…` and `/media/…` image/PDF URLs). These are handled inside the app; there are no server rules to add.
- ☐ `robots.txt` and `sitemap.xml` are routes (`SeoController`). Production (`APP_ENV=production`) allows indexing and names the sitemap; any other environment answers `Disallow: /`. **No `public/robots.txt` may exist on the server**, it would shadow the route. Submit `https://strut.ch/sitemap.xml` in the Google Search Console after go-live.
- ⏳ Google Maps API key (`GOOGLE_MAPS_KEY`), restricted to the production domain
- ☐ `GOOGLE_MAPS_KEY` set; check the map on `/kontakt` (styles, marker; never tested with a real key)
- ☐ Legacy URLs: `php artisan strut:check-urls` (0 fehlerhaft), then `php artisan images:warm` (§5.4)
- ☐ Backups: database, and `storage/app/public/uploads` (the originals). `.glide-cache` does not need backing up; it regenerates.
- ☐ The legacy `/artisan/*` routes must not exist on the new site (they don't). If the old code base stays online anywhere, remove them there.
