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

The content comes from the legacy strut.ch database and its media folder. There are two options:

**A. Import locally, then transfer (recommended: no legacy access on the server needed)**
1. Locally: `php artisan strut:import --fresh`, then `php artisan strut:verify` must report "Keine Probleme gefunden".
2. Dump the local `rework_strut` database and import it on the server.
3. Copy `storage/app/public/uploads/` (about 380 MB, 531 files) to the same path on the server.

**B. Import on the server**
1. Set `LEGACY_DB_*` (read-only access to the legacy database) and `LEGACY_MEDIA_PATH` (absolute path to the legacy `storage/app/public/media`).
2. Run `php artisan strut:import --fresh`, then `php artisan strut:verify`.
3. Clear `LEGACY_*` afterwards.

**After either option:** `php artisan strut:check-urls` ☐ must report "87 URLs, 0 fehlerhaft" (every legacy URL answers 200 or redirects to a page that does). Then `php artisan images:warm` ☐ pre-generates every public image variant (legacy sizes × JPEG/PNG, WebP, AVIF; about 4,100 files, ~7 minutes locally). Without it, the first visitor of each image waits for its encode. It can run again at any time (existing variants are skipped).

Notes:
- The import is idempotent. `--dry-run` shows the report without writing anything.
- Legacy source files are only ever copied, never changed.
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

- ☐ Cron entry for the scheduler (§4)
- ☐ Mail configured and "Passwort vergessen" tested
- ☐ `APP_DEBUG=false`, `APP_ENV=production`, HTTPS
- ☐ Content imported and `strut:verify` clean (§5)
- ☐ Passwords of imported users rotated
- ⏳ 301 redirects for changed URLs (`/bauten` → `/werkliste`, old `/storage/media/…` and `/media/…` image/PDF URLs). These are handled inside the app; there are no server rules to add.
- ☐ `robots.txt` and `sitemap.xml` are routes (`SeoController`). Production (`APP_ENV=production`) allows indexing and names the sitemap; any other environment answers `Disallow: /`. **No `public/robots.txt` may exist on the server**, it would shadow the route. Submit `https://strut.ch/sitemap.xml` in the Google Search Console after go-live.
- ⏳ Google Maps API key (`GOOGLE_MAPS_KEY`), restricted to the production domain
- ☐ `GOOGLE_MAPS_KEY` set; check the map on `/kontakt` (styles, marker; never tested with a real key)
- ☐ Legacy URLs: `php artisan strut:check-urls` (87/0), then `php artisan images:warm` (§5)
- ☐ Backups: database, and `storage/app/public/uploads` (the originals). `.glide-cache` does not need backing up; it regenerates.
- ☐ The legacy `/artisan/*` routes must not exist on the new site (they don't). If the old code base stays online anywhere, remove them there.
