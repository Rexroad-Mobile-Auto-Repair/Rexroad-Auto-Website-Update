# Local WordPress integration-test environment (disposable)

A throwaway, fully local Docker stack that runs the real
`rexroad-custom-theme` inside real WordPress + MariaDB, so the vehicle
SEO page architecture can be exercised against actual WordPress
behavior (permalinks, page hierarchy, `wp_head`, real HTTP requests)
instead of only the PHP-mock test suite in `tests/vehicles/`.

**This stack has no route to `rexroadauto.com` or
`staging.rexroadauto.com`.** It only talks to itself over an isolated
Docker bridge network, binds its single port to `127.0.0.1` only, uses
throwaway local credentials, and makes no outbound API/write calls to
anything production. `DISALLOW_FILE_MODS` is set so the local WP
instance can't reach out to wordpress.org either.

## Architecture

- `db` — MariaDB 11.4, disposable named volume, throwaway credentials.
- `wordpress` — official `wordpress:6.6-php8.3-apache` image. The
  real `rexroad-custom-theme/` directory is bind-mounted **read-only**
  into `wp-content/themes/rexroad-custom-theme` — this proves the
  actual production theme code, never a copy.
- `wpcli` — official `wordpress:cli-php8.3` image, sharing the same
  `wp_html` volume as `wordpress` (so it operates on the same site,
  not a separate one), used only for scripted setup (`seed/seed.sh`).
  Not exposed on any port.

## Start / seed / destroy

```sh
cd local-integration-test
docker compose up -d       # starts db + wordpress + wpcli
./seed/seed.sh              # installs WP, activates the theme, seeds pages
# ... test at http://localhost:8089/vehicles/ ...
docker compose down -v      # destroys everything, including the DB volume
```

## Seeded hierarchy

```
Vehicles (page-vehicles.php)                         published
└── Ford (page-vehicle-make.php)                      published
    ├── F-150 (page-vehicle-model.php)                 published
    ├── Explorer (page-vehicle-model.php)               DRAFT   — proves unpublished models render as plain text
    └── Not A Real Model (page-vehicle-model.php)       published, invalid catalog slug — proves graceful fallback
"F-150 Under Wrong Parent" (page-vehicle-model.php), slug "f-150", parented directly under Vehicles — proves hierarchy validation rejects a valid model slug under the wrong parent
```

Ford's and F-150's `post_content` are the **approved editorial drafts**
committed at `editorial-workspace/vehicles/ford/make-ford.html` and
`model-f-150.html` — read from the repo at seed time, never copied
into PHP.

## Tests

- `tests/run-integration-tests.py` — fetches the real HTTP responses
  and asserts status codes, H1/breadcrumb correctness, editorial
  content appearing exactly once, catalog-derived years, published
  vs. unpublished linking, service/problem links, CTA, JSON-LD schema
  restraint (no `Vehicle`/`Product`/`Offer`/`Review`), and native
  `?page_id=` routing (no CPT/rewrite-rule dependency).
- `tests/browser-qa.js` — Playwright script; loads all three pages at
  desktop (1440×900) and mobile (390×844), checks for horizontal
  overflow, console/page errors, and exercises the live JS filter on
  `/vehicles/`. Screenshots go to `/tmp/rexroad-qa-artifacts/` (outside
  the repo) — never committed.

Run both after seeding:

```sh
python3 tests/run-integration-tests.py
node tests/browser-qa.js
```

## What this harness is not

It doesn't replace `tests/vehicles/` (still the fast, dependency-free
CI-friendly suite) — it's a slower, heavier real-WordPress check for
things PHP mocks can't catch: actual permalink resolution, real
`wp_head` output, real theme asset enqueuing, and real page hierarchy
validation. Run it before any WordPress publish, not on every commit.
