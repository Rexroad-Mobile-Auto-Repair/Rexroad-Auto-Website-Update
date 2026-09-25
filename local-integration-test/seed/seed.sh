#!/bin/sh
# Seeds the disposable local WordPress with the minimum vehicle test
# hierarchy. Run from local-integration-test/ after `docker compose up -d`.
#
# Usage: ./seed/seed.sh
set -eu

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
EDITORIAL_DIR="$SCRIPT_DIR/../../editorial-workspace"
COMPOSE="docker compose"
CLI="$COMPOSE exec -T wpcli wp --path=/var/www/html --allow-root"

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

FORD_CONTENT=$(cat "$EDITORIAL_DIR/vehicles/ford/make-ford.html")
FORD_ID=$($CLI post create \
  --post_type=page \
  --post_title="Ford" \
  --post_name=ford \
  --post_status=publish \
  --post_parent="$VEHICLES_ID" \
  --page_template=page-vehicle-make.php \
  --post_content="$FORD_CONTENT" \
  --porcelain)
echo "  Ford ID=$FORD_ID"

F150_CONTENT=$(cat "$EDITORIAL_DIR/vehicles/ford/model-f-150.html")
F150_ID=$($CLI post create \
  --post_type=page \
  --post_title="F-150" \
  --post_name=f-150 \
  --post_status=publish \
  --post_parent="$FORD_ID" \
  --page_template=page-vehicle-model.php \
  --post_content="$F150_CONTENT" \
  --porcelain)
echo "  F-150 ID=$F150_ID"

echo "Creating an UNPUBLISHED model page (Explorer, draft) to test plain-text fallback..."
EXPLORER_ID=$($CLI post create \
  --post_type=page \
  --post_title="Explorer" \
  --post_name=explorer \
  --post_status=draft \
  --post_parent="$FORD_ID" \
  --page_template=page-vehicle-model.php \
  --porcelain)
echo "  Explorer (draft) ID=$EXPLORER_ID"

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

$CLI rewrite flush --hard

echo ""
echo "Seed complete. IDs: vehicles=$VEHICLES_ID ford=$FORD_ID f-150=$F150_ID explorer(draft)=$EXPLORER_ID invalid=$INVALID_ID wrong-parent=$WRONG_PARENT_ID"
mkdir -p "$SCRIPT_DIR/../artifacts"
cat > "$SCRIPT_DIR/../artifacts/seed-ids.env" <<EOF
VEHICLES_ID=$VEHICLES_ID
FORD_ID=$FORD_ID
F150_ID=$F150_ID
EXPLORER_ID=$EXPLORER_ID
INVALID_ID=$INVALID_ID
WRONG_PARENT_ID=$WRONG_PARENT_ID
EOF
