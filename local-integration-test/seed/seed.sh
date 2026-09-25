#!/bin/sh
# Seeds the disposable local WordPress with the vehicle test hierarchy
# for the approved editorial batches (Ford/F-150 baseline + Batch 1).
# Run from local-integration-test/ after `docker compose up -d`.
#
# Usage: ./seed/seed.sh
set -eu

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
EDITORIAL_DIR="$SCRIPT_DIR/../../editorial-workspace"
COMPOSE="docker compose"
CLI="$COMPOSE exec -T wpcli wp --path=/var/www/html --allow-root"
IDS_FILE="$SCRIPT_DIR/../artifacts/seed-ids.env"

echo "Waiting for wp-config.php and DB connectivity..."
i=0
until $CLI db check >/dev/null 2>&1; do
  i=$((i + 1))
  if [ "$i" -gt 60 ]; then
    echo "Timed out waiting for WordPress DB. Last attempt output:"
    $CLI db check || true
    exit 1
  fi
  sleep 2
done

if ! $CLI core is-installed >/dev/null 2>&1; then
  echo "Installing WordPress core (local test site only)..."
  $CLI core install \
    --url="http://localhost:8089" \
    --title="Rexroad Vehicle Integration Test (local, disposable)" \
    --admin_user=local_test_admin \
    --admin_password=local_only_test_pw \
    --admin_email=test@example.test \
    --skip-email
fi

echo "Activating rexroad-custom-theme..."
$CLI theme activate rexroad-custom-theme

echo "Setting pretty permalinks (core WP feature, no custom rewrite rules)..."
$CLI rewrite structure '/%postname%/' --hard
$CLI rewrite flush --hard

echo "Removing default sample content that could interfere..."
$CLI post delete 2 --force >/dev/null 2>&1 || true

echo "Creating Vehicles hub page..."
VEHICLES_ID=$($CLI post create \
  --post_type=page \
  --post_title="Cars, Trucks & SUVs We Service" \
  --post_name=vehicles \
  --post_status=publish \
  --page_template=page-vehicles.php \
  --porcelain)
echo "  Vehicles ID=$VEHICLES_ID"

mkdir -p "$SCRIPT_DIR/../artifacts"
: > "$IDS_FILE"
echo "VEHICLES_ID=$VEHICLES_ID" >> "$IDS_FILE"

# Reads editorial the_content() straight from the approved, already-
# committed drafts under editorial-workspace/vehicles/ — never
# duplicated into this script or into PHP.
create_make_page() {
  make_slug="$1"
  make_title="$2"
  content=$(cat "$EDITORIAL_DIR/vehicles/$make_slug/make-$make_slug.html")
  $CLI post create \
    --post_type=page \
    --post_title="$make_title" \
    --post_name="$make_slug" \
    --post_status=publish \
    --post_parent="$VEHICLES_ID" \
    --page_template=page-vehicle-make.php \
    --post_content="$content" \
    --porcelain
}

create_model_page() {
  make_slug="$1"
  model_slug="$2"
  model_title="$3"
  content_file="$4"
  parent_id="$5"
  content=$(cat "$EDITORIAL_DIR/vehicles/$make_slug/$content_file")
  $CLI post create \
    --post_type=page \
    --post_title="$model_title" \
    --post_name="$model_slug" \
    --post_status=publish \
    --post_parent="$parent_id" \
    --page_template=page-vehicle-model.php \
    --post_content="$content" \
    --porcelain
}

echo "Creating Ford (baseline) + F-150..."
FORD_ID=$(create_make_page ford Ford)
echo "  Ford ID=$FORD_ID"
echo "FORD_ID=$FORD_ID" >> "$IDS_FILE"
F150_ID=$(create_model_page ford f-150 "F-150" model-f-150.html "$FORD_ID")
echo "  F-150 ID=$F150_ID"
echo "F150_ID=$F150_ID" >> "$IDS_FILE"

echo "Creating Chevrolet + Silverado 1500 (Batch 1)..."
CHEVROLET_ID=$(create_make_page chevrolet Chevrolet)
echo "  Chevrolet ID=$CHEVROLET_ID"
echo "CHEVROLET_ID=$CHEVROLET_ID" >> "$IDS_FILE"
SILVERADO_ID=$(create_model_page chevrolet silverado-1500 "Silverado 1500" model-silverado-1500.html "$CHEVROLET_ID")
echo "  Silverado 1500 ID=$SILVERADO_ID"
echo "SILVERADO_ID=$SILVERADO_ID" >> "$IDS_FILE"

echo "Creating Toyota + Camry + Tacoma (Batch 1, shared make draft)..."
TOYOTA_ID=$(create_make_page toyota Toyota)
echo "  Toyota ID=$TOYOTA_ID"
echo "TOYOTA_ID=$TOYOTA_ID" >> "$IDS_FILE"
CAMRY_ID=$(create_model_page toyota camry "Camry" model-camry.html "$TOYOTA_ID")
echo "  Camry ID=$CAMRY_ID"
echo "CAMRY_ID=$CAMRY_ID" >> "$IDS_FILE"
TACOMA_ID=$(create_model_page toyota tacoma "Tacoma" model-tacoma.html "$TOYOTA_ID")
echo "  Tacoma ID=$TACOMA_ID"
echo "TACOMA_ID=$TACOMA_ID" >> "$IDS_FILE"

echo "Creating Honda + Civic (Batch 1)..."
HONDA_ID=$(create_make_page honda Honda)
echo "  Honda ID=$HONDA_ID"
echo "HONDA_ID=$HONDA_ID" >> "$IDS_FILE"
CIVIC_ID=$(create_model_page honda civic "Civic" model-civic.html "$HONDA_ID")
echo "  Civic ID=$CIVIC_ID"
echo "CIVIC_ID=$CIVIC_ID" >> "$IDS_FILE"

echo "Creating Ram + 1500 (Batch 1)..."
RAM_ID=$(create_make_page ram Ram)
echo "  Ram ID=$RAM_ID"
echo "RAM_ID=$RAM_ID" >> "$IDS_FILE"
RAM1500_ID=$(create_model_page ram 1500 "1500" model-1500.html "$RAM_ID")
echo "  Ram 1500 ID=$RAM1500_ID"
echo "RAM1500_ID=$RAM1500_ID" >> "$IDS_FILE"

echo "Creating an UNPUBLISHED model page (Explorer, draft, under Ford) to test plain-text fallback..."
EXPLORER_ID=$($CLI post create \
  --post_type=page \
  --post_title="Explorer" \
  --post_name=explorer \
  --post_status=draft \
  --post_parent="$FORD_ID" \
  --page_template=page-vehicle-model.php \
  --porcelain)
echo "  Explorer (draft) ID=$EXPLORER_ID"
echo "EXPLORER_ID=$EXPLORER_ID" >> "$IDS_FILE"

echo "Creating an INVALID model page (slug not in Ford's catalog) to test graceful fallback..."
INVALID_ID=$($CLI post create \
  --post_type=page \
  --post_title="Not A Real Model" \
  --post_name=not-a-real-model \
  --post_status=publish \
  --post_parent="$FORD_ID" \
  --page_template=page-vehicle-model.php \
  --porcelain)
echo "  Invalid model ID=$INVALID_ID"
echo "INVALID_ID=$INVALID_ID" >> "$IDS_FILE"

echo "Creating a WRONG-PARENT page (slug 'f-150' directly under Vehicles, not Ford) to test hierarchy validation..."
WRONG_PARENT_ID=$($CLI post create \
  --post_type=page \
  --post_title="F-150 Under Wrong Parent" \
  --post_name=f-150 \
  --post_status=publish \
  --post_parent="$VEHICLES_ID" \
  --page_template=page-vehicle-model.php \
  --porcelain)
echo "  Wrong-parent F-150 ID=$WRONG_PARENT_ID"
echo "WRONG_PARENT_ID=$WRONG_PARENT_ID" >> "$IDS_FILE"

$CLI rewrite flush --hard

echo ""
echo "Seed complete. IDs written to $IDS_FILE"
cat "$IDS_FILE"
