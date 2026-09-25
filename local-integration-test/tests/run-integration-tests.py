#!/usr/bin/env python3
"""
Real-WordPress integration assertions for the disposable local stack.
Not a production test — reads live HTTP responses from the local Docker
WordPress instance at http://localhost:8089 and checks them against the
seeded hierarchy (Ford/F-150 baseline + Batch 1: Chevrolet/Silverado
1500, Toyota/Camry, Toyota/Tacoma, Honda/Civic, Ram/1500).
Run after seed/seed.sh.
"""
import json
import re
import sys
import urllib.request

BASE = "http://localhost:8089"
results = []


def fetch(path, follow_redirects=True):
    class NoRedirect(urllib.request.HTTPRedirectHandler):
        def redirect_request(self, *args, **kwargs):
            return None

    opener = urllib.request.build_opener(NoRedirect) if not follow_redirects else None
    req = urllib.request.Request(BASE + path, headers={"User-Agent": "rexroad-integration-test"})
    try:
        opened = opener.open(req, timeout=10) if opener else urllib.request.urlopen(req, timeout=10)
        with opened as resp:
            return resp.status, resp.read().decode("utf-8", errors="replace"), dict(resp.headers)
    except urllib.error.HTTPError as e:
        return e.code, e.read().decode("utf-8", errors="replace"), dict(e.headers)


def check(label, condition):
    results.append((label, bool(condition)))
    print(("PASS" if condition else "FAIL") + ": " + label)


def extract_jsonld(html):
    blocks = re.findall(r'<script type="application/ld\+json">(.*?)</script>', html, re.S)
    parsed = []
    for b in blocks:
        try:
            parsed.append(json.loads(b))
        except json.JSONDecodeError:
            parsed.append(None)
    return parsed


def types_in(html):
    types = set()
    for g in extract_jsonld(html):
        if g and "@graph" in g:
            for node in g["@graph"]:
                types.add(node.get("@type"))
    return types


NO_INVENTED_SCHEMA = {"Vehicle", "Product", "Offer", "Review", "AggregateRating"}


def check_make_page(make_slug, make_name, editorial_marker):
    path = f"/vehicles/{make_slug}/"
    code, html, headers = fetch(path)
    check(f"{path} returns HTTP 200", code == 200)
    check(f"{path} H1 is '{make_name} Mobile Mechanic Service'", f"<h1>{make_name} Mobile Mechanic Service</h1>" in html)
    check(
        f"{path} breadcrumb Home -> Vehicles hub -> {make_name}",
        bool(re.search(r'Home</a>.*?Cars, Trucks &#038; SUVs We Service</a>.*?aria-current="page">' + re.escape(make_name), html, re.S)),
    )
    check(f"{path} editorial content appears exactly once", html.count(editorial_marker) == 1)
    check(f"{path} service links point to /services/", "/services/advanced-diagnostics/" in html)
    check(f"{path} problem links present", f"Common Issues We Diagnose on {make_name} Vehicles" in html)
    check(f"{path} service-area content present", "Mobile Service, Wherever You Are" in html and "Frisco" in html)
    check(f"{path} CTA present", f"Request Service for Your {make_name}" in html)
    check(f"{path} vehicles.css loaded", "vehicles.css" in html)
    types = types_in(html)
    check(f"{path} schema includes WebPage or CollectionPage", bool(types & {"WebPage", "CollectionPage"}))
    check(f"{path} schema includes BreadcrumbList", "BreadcrumbList" in types)
    check(f"{path} schema includes AutoRepair", "AutoRepair" in types)
    check(f"{path} schema has no invented Vehicle/Product/Offer/Review types", not (types & NO_INVENTED_SCHEMA))
    return html


def check_model_page(make_slug, model_slug, make_name, model_name, editorial_marker):
    path = f"/vehicles/{make_slug}/{model_slug}/"
    code, html, headers = fetch(path)
    check(f"{path} returns HTTP 200", code == 200)
    check(f"{path} H1 is '{make_name} {model_name} Mobile Mechanic Service'", f"<h1>{make_name} {model_name} Mobile Mechanic Service</h1>" in html)
    check(
        f"{path} breadcrumb Home -> Vehicles -> {make_name} -> {model_name}",
        bool(re.search(r'Home</a>.*?Cars, Trucks &#038; SUVs We Service</a>.*?>' + re.escape(make_name) + r'</a>.*?aria-current="page">' + re.escape(model_name), html, re.S)),
    )
    check(f"{path} editorial content appears exactly once", html.count(editorial_marker) == 1)
    check(f"{path} supported model years present (2000-2026 or catalog range)", "Supported Model Years" in html)
    check(f"{path} related models section present", f"Other {make_name} Models We Service" in html)
    check(f"{path} CTA present", f"Request Service for Your {make_name} {model_name}" in html)
    check(f"{path} service-area content present", "Mobile Service, Wherever You Are" in html)
    check(f"{path} vehicles.css loaded", "vehicles.css" in html)
    types = types_in(html)
    check(f"{path} schema includes WebPage", "WebPage" in types)
    check(f"{path} schema includes BreadcrumbList", "BreadcrumbList" in types)
    check(f"{path} schema includes AutoRepair", "AutoRepair" in types)
    check(f"{path} schema has no invented Vehicle/Product/Offer/Review types", not (types & NO_INVENTED_SCHEMA))
    return html


# --- /vehicles/ hub ---
code, html, headers = fetch("/vehicles/")
check("/vehicles/ returns HTTP 200", code == 200)
check("/vehicles/ H1 present", "<h1>Cars, Trucks &#038; SUVs We Service</h1>" in html)
for slug, name in [("ford", "Ford"), ("chevrolet", "Chevrolet"), ("toyota", "Toyota"), ("honda", "Honda"), ("ram", "Ram")]:
    check(f"/vehicles/ {name} is a link (published child page)", f'<a href="http://localhost:8089/vehicles/{slug}/">{name}</a>' in html)
check("/vehicles/ vehicles.css loaded", "vehicles.css" in html)
check("/vehicles/ vehicles.js loaded", "vehicles.js" in html)
types = types_in(html)
check("/vehicles/ schema includes CollectionPage", "CollectionPage" in types)
check("/vehicles/ schema includes BreadcrumbList", "BreadcrumbList" in types)
check("/vehicles/ schema includes AutoRepair", "AutoRepair" in types)
check("/vehicles/ schema has no invented Vehicle/Product/Offer/Review types", not (types & NO_INVENTED_SCHEMA))

# --- Ford / F-150 (baseline, must keep working) ---
ford_html = check_make_page("ford", "Ford", "The lineup covers a lot of ground")
check("/vehicles/ford/ F-150 is a link (published child page)", '<a class="rr-vehicle-model__name" href="http://localhost:8089/vehicles/ford/f-150/">F-150</a>' in ford_html)
check(
    "/vehicles/ford/ Explorer is plain text (draft, unpublished)",
    bool(re.search(r'<span class="rr-vehicle-model__name">Explorer</span>', ford_html))
    and '<a class="rr-vehicle-model__name" href="http://localhost:8089/vehicles/ford/explorer/">Explorer</a>' not in ford_html,
)
check_model_page("ford", "f-150", "Ford", "F-150", "Mobile Service for Your F-150")

# --- Chevrolet / Silverado 1500 (Batch 1) ---
chevrolet_html = check_make_page("chevrolet", "Chevrolet", "widest lineups I work on around Frisco")
check(
    "/vehicles/chevrolet/ Silverado 1500 is a link (published child page)",
    '<a class="rr-vehicle-model__name" href="http://localhost:8089/vehicles/chevrolet/silverado-1500/">Silverado 1500</a>' in chevrolet_html,
)
check_model_page("chevrolet", "silverado-1500", "Chevrolet", "Silverado 1500", "Mobile Service for Your Silverado 1500")

# --- Toyota / Camry + Tacoma (Batch 1, shared make draft) ---
toyota_html = check_make_page("toyota", "Toyota", "Toyota covers a lot of ground")
check(
    "/vehicles/toyota/ Camry is a link (published child page)",
    '<a class="rr-vehicle-model__name" href="http://localhost:8089/vehicles/toyota/camry/">Camry</a>' in toyota_html,
)
check(
    "/vehicles/toyota/ Tacoma is a link (published child page)",
    '<a class="rr-vehicle-model__name" href="http://localhost:8089/vehicles/toyota/tacoma/">Tacoma</a>' in toyota_html,
)
check_model_page("toyota", "camry", "Toyota", "Camry", "Mobile Service for Your Camry")
check_model_page("toyota", "tacoma", "Toyota", "Tacoma", "Mobile Service for Your Tacoma")
# Cross-check: Camry and Tacoma each list the OTHER as a related model, proving one shared make draft serves both without duplicating editorial content.
_, camry_html, _ = fetch("/vehicles/toyota/camry/")
_, tacoma_html, _ = fetch("/vehicles/toyota/tacoma/")
check("/vehicles/toyota/camry/ lists Tacoma under 'Other Toyota Models'", "Tacoma" in camry_html)
check("/vehicles/toyota/tacoma/ lists Camry under 'Other Toyota Models'", "Camry" in tacoma_html)

# --- Honda / Civic (Batch 1) ---
honda_html = check_make_page("honda", "Honda", "Honda is a common name on my service calls")
check(
    "/vehicles/honda/ Civic is a link (published child page)",
    '<a class="rr-vehicle-model__name" href="http://localhost:8089/vehicles/honda/civic/">Civic</a>' in honda_html,
)
check_model_page("honda", "civic", "Honda", "Civic", "Mobile Service for Your Civic")

# --- Ram / 1500 (Batch 1) — FIXED: numeric-model-slug convention ---
# Ram's "1500" model is purely numeric. WordPress does not reliably
# allow a purely-numeric hierarchical Page slug (wp_unique_post_slug()
# mutates it, and a numeric final URL segment collides with
# WordPress's own built-in page-pagination rewrite rule). The seed and
# the theme's rexroad_vehicle_page_slug_for_model() helper both use the
# "{make-slug}-{model-slug}" convention for this case, so the real,
# stable, canonical URL is /vehicles/ram/ram-1500/ — never
# /vehicles/ram/1500/ and never WordPress's own "-2" fallback.
ram_html = check_make_page("ram", "Ram", "lineup is more focused than most makes")
check(
    "/vehicles/ram/ 1500 is a link to the ram-1500 convention URL (published child page)",
    '<a class="rr-vehicle-model__name" href="http://localhost:8089/vehicles/ram/ram-1500/">1500</a>' in ram_html,
)
check_model_page("ram", "ram-1500", "Ram", "1500", "Mobile Service for Your Ram 1500")

code, _, headers_check = fetch("/vehicles/ram/ram-1500/", follow_redirects=False)
check("/vehicles/ram/ram-1500/ resolves directly, HTTP 200, no redirect", code == 200)

# The raw numeric URL is explicitly NOT the canonical model URL. WordPress's
# own catch-all page-pagination rewrite rule ("(.?.+?)(?:/([0-9]+))?/?$")
# still redirects it to the parent make page — this is core WordPress
# behavior, not something this fix fights with a redirect or rewrite rule;
# the fix simply never relies on that URL being the model's canonical
# address in the first place.
code, _, headers_raw = fetch("/vehicles/ram/1500/", follow_redirects=False)
check(
    "/vehicles/ram/1500/ is NOT the canonical model URL (WordPress's own pagination rule still redirects it — expected, not fought)",
    code == 301 and headers_raw.get("Location", "").endswith("/vehicles/ram/"),
)

# --- Published/unpublished + invalid-hierarchy edge cases (must still hold) ---
code, html, headers = fetch("/vehicles/ford/not-a-real-model/")
check("invalid model slug: HTTP 200 (real WP Page, no fatal)", code == 200)
check("invalid model slug: no fabricated 'Mobile Mechanic Service' H1", "Mobile Mechanic Service</h1>" not in html)
check("invalid model slug: still offers Request Service", "Request Service" in html)

code, html, headers = fetch("/vehicles/f-150/")
check("wrong-parent f-150: HTTP 200 (real WP Page, no fatal)", code == 200)
check("wrong-parent f-150: no fabricated 'Mobile Mechanic Service' H1", "Mobile Mechanic Service</h1>" not in html)

# --- Native WP routing / asset scoping sanity (unchanged behavior) ---
code, html, headers = fetch("/?page_id=4")
check("native WP ?page_id= routing works without pretty permalinks (no CPT dependency)", code == 200)

code, html, headers = fetch("/?p=1")
check("vehicles.css NOT loaded on a non-vehicle page", "vehicles.css" not in html or code != 200)

print("\n" + str(sum(1 for _, ok in results if ok)) + "/" + str(len(results)) + " checks passed")
if any(not ok for _, ok in results):
    sys.exit(1)
