#!/usr/bin/env python3
"""
Real-WordPress integration assertions for the disposable local stack.
Not a production test — reads live HTTP responses from the local Docker
WordPress instance at http://localhost:8089 and checks them against the
seeded hierarchy. Run after seed/seed.sh.
"""
import json
import re
import sys
import urllib.request

BASE = "http://localhost:8089"
results = []


def fetch(path):
    req = urllib.request.Request(BASE + path, headers={"User-Agent": "rexroad-integration-test"})
    try:
        with urllib.request.urlopen(req, timeout=10) as resp:
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


# --- /vehicles/ ---
code, html, headers = fetch("/vehicles/")
check("/vehicles/ returns HTTP 200", code == 200)
check("/vehicles/ H1 present", "<h1>Cars, Trucks &#038; SUVs We Service</h1>" in html or "<h1>Cars, Trucks & SUVs We Service</h1>" in html)
check("/vehicles/ Ford is a link (published child page)", '<a href="http://localhost:8089/vehicles/ford/">Ford</a>' in html)
check("/vehicles/ vehicles.css loaded", "vehicles.css" in html)
check("/vehicles/ vehicles.js loaded", "vehicles.js" in html)
graph = extract_jsonld(html)
vehicles_types = set()
for g in graph:
    if g and "@graph" in g:
        for node in g["@graph"]:
            vehicles_types.add(node.get("@type"))
check("/vehicles/ schema includes CollectionPage", "CollectionPage" in vehicles_types)
check("/vehicles/ schema includes BreadcrumbList", "BreadcrumbList" in vehicles_types)
check("/vehicles/ schema includes AutoRepair", "AutoRepair" in vehicles_types)
check("/vehicles/ schema has no Vehicle/Product/Offer/Review", not (vehicles_types & {"Vehicle", "Product", "Offer", "Review", "AggregateRating"}))

# --- /vehicles/ford/ ---
code, html, headers = fetch("/vehicles/ford/")
check("/vehicles/ford/ returns HTTP 200", code == 200)
check("/vehicles/ford/ H1 is 'Ford Mobile Mechanic Service'", "<h1>Ford Mobile Mechanic Service</h1>" in html)
check("/vehicles/ford/ breadcrumb Home -> Vehicles hub -> Ford", bool(re.search(r'Home</a>.*?Cars, Trucks &#038; SUVs We Service</a>.*?aria-current="page">Ford', html, re.S)))
check("/vehicles/ford/ editorial content appears exactly once", html.count("The lineup covers a lot of ground") == 1)
check("/vehicles/ford/ F-150 is a link (published child page)", '<a class="rr-vehicle-model__name" href="http://localhost:8089/vehicles/ford/f-150/">F-150</a>' in html)
check("/vehicles/ford/ Explorer is plain text (draft, unpublished)", bool(re.search(r'<span class="rr-vehicle-model__name">Explorer</span>', html)) and '<a class="rr-vehicle-model__name" href="http://localhost:8089/vehicles/ford/explorer/">Explorer</a>' not in html)
check("/vehicles/ford/ supported years render from catalog (2000–2026 present)", "2000" in html and "2026" in html)
check("/vehicles/ford/ service links point to /services/", "/services/advanced-diagnostics/" in html)
check("/vehicles/ford/ problem links present", "Common Issues We Diagnose on Ford Vehicles" in html)
check("/vehicles/ford/ service-area content present", "Mobile Service, Wherever You Are" in html and "Frisco" in html)
check("/vehicles/ford/ CTA present", "Request Service for Your Ford" in html)
check("/vehicles/ford/ vehicles.css loaded", "vehicles.css" in html)
ford_graph = extract_jsonld(html)
ford_types = set()
for g in ford_graph:
    if g and "@graph" in g:
        for node in g["@graph"]:
            ford_types.add(node.get("@type"))
check("/vehicles/ford/ schema includes WebPage", "WebPage" in ford_types)
check("/vehicles/ford/ schema includes BreadcrumbList", "BreadcrumbList" in ford_types)
check("/vehicles/ford/ schema includes AutoRepair", "AutoRepair" in ford_types)
check("/vehicles/ford/ schema has no Vehicle/Product/Offer/Review", not (ford_types & {"Vehicle", "Product", "Offer", "Review", "AggregateRating"}))

# --- /vehicles/ford/f-150/ ---
code, html, headers = fetch("/vehicles/ford/f-150/")
check("/vehicles/ford/f-150/ returns HTTP 200", code == 200)
check("/vehicles/ford/f-150/ H1 is 'Ford F-150 Mobile Mechanic Service'", "<h1>Ford F-150 Mobile Mechanic Service</h1>" in html)
check("/vehicles/ford/f-150/ breadcrumb Home -> Vehicles -> Ford -> F-150", bool(re.search(r'Home</a>.*?Cars, Trucks &#038; SUVs We Service</a>.*?>Ford</a>.*?aria-current="page">F-150', html, re.S)))
check("/vehicles/ford/f-150/ editorial content appears exactly once", html.count("Mobile Service for Your F-150") == 1)
check("/vehicles/ford/f-150/ supported model years correct (2000–2026)", "2000" in html and "2026" in html and "Supported Model Years" in html)
check("/vehicles/ford/f-150/ related models section present", "Other Ford Models We Service" in html)
check("/vehicles/ford/f-150/ CTA present", "Request Service for Your Ford F-150" in html)
check("/vehicles/ford/f-150/ service-area content present", "Mobile Service, Wherever You Are" in html)
check("/vehicles/ford/f-150/ vehicles.css loaded", "vehicles.css" in html)
f150_graph = extract_jsonld(html)
f150_types = set()
for g in f150_graph:
    if g and "@graph" in g:
        for node in g["@graph"]:
            f150_types.add(node.get("@type"))
check("/vehicles/ford/f-150/ schema includes WebPage", "WebPage" in f150_types)
check("/vehicles/ford/f-150/ schema includes BreadcrumbList", "BreadcrumbList" in f150_types)
check("/vehicles/ford/f-150/ schema includes AutoRepair", "AutoRepair" in f150_types)
check("/vehicles/ford/f-150/ schema has no Vehicle/Product/Offer/Review", not (f150_types & {"Vehicle", "Product", "Offer", "Review", "AggregateRating"}))

# --- invalid model slug under valid make (graceful fallback) ---
code, html, headers = fetch("/vehicles/ford/not-a-real-model/")
check("invalid model slug: HTTP 200 (real WP Page, no fatal)", code == 200)
check("invalid model slug: no fabricated 'Mobile Mechanic Service' H1", "Mobile Mechanic Service</h1>" not in html)
check("invalid model slug: still offers Request Service", "Request Service" in html)

# --- valid model slug under WRONG parent (hierarchy validation) ---
code, html, headers = fetch("/vehicles/f-150/")
check("wrong-parent f-150: HTTP 200 (real WP Page, no fatal)", code == 200)
check("wrong-parent f-150: no fabricated 'Mobile Mechanic Service' H1", "Mobile Mechanic Service</h1>" not in html)

# --- Un-pretty page-id fallback still resolves (native WP routing, no CPT/rewrite dependency) ---
code, html, headers = fetch("/?page_id=4")
check("native WP ?page_id= routing works without pretty permalinks (no CPT dependency)", code == 200)

# --- vehicles.css/js scoping: NOT loaded on an unrelated page (front page) ---
code, html, headers = fetch("/?p=1")
check("vehicles.css NOT loaded on a non-vehicle page", "vehicles.css" not in html or code != 200)

print("\n" + str(sum(1 for _, ok in results if ok)) + "/" + str(len(results)) + " checks passed")
if any(not ok for _, ok in results):
    sys.exit(1)
