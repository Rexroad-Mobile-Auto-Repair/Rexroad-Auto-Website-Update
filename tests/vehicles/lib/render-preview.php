<?php
/**
 * NOT a test. One-off generator used only for manual/browser QA of the
 * rendered /vehicles/ output — captures the REAL page-vehicles.php
 * output via the same WP stubs the test suite uses, and wraps it with
 * the real theme CSS/JS so it can be opened in an actual browser.
 *
 * Usage: php tests/vehicles/lib/render-preview.php > /path/to/preview.html
 */

declare( strict_types=1 );

$root = dirname( __DIR__, 3 );

require __DIR__ . '/wp-stubs.php';

function get_template_directory(): string {
	global $root;
	return $root . '/rexroad-custom-theme';
}

function get_template_directory_uri(): string {
	return '/rexroad-custom-theme';
}

require $root . '/rexroad-custom-theme/inc/vehicles/vehicles.php';
require $root . '/rexroad-custom-theme/inc/schema-service.php';

$_GET = array();
$GLOBALS['rexroad_test_have_posts_remaining'] = 1;

ob_start();
require $root . '/rexroad-custom-theme/page-vehicles.php';
$body = (string) ob_get_clean();

$css = array(
	'/rexroad-custom-theme/assets/css/base.css',
	'/rexroad-custom-theme/assets/css/content.css',
	'/rexroad-custom-theme/assets/css/header.css',
	'/rexroad-custom-theme/assets/css/footer.css',
	'/rexroad-custom-theme/assets/css/services.css',
	'/rexroad-custom-theme/assets/css/forms.css',
	'/rexroad-custom-theme/assets/css/vehicles.css',
	'/rexroad-custom-theme/assets/css/responsive.css',
);

$links = '';
foreach ( $css as $href ) {
	$links .= '<link rel="stylesheet" href="' . htmlspecialchars( $href ) . '">' . "\n";
}

echo '<!doctype html><html lang="en"><head><meta charset="utf-8">';
echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
echo '<title>Vehicles Directory Preview</title>';
echo $links;
echo '<style>.rexroad-preview-header-mock{position:sticky;top:0;z-index:999;min-height:126px;display:flex;align-items:center;justify-content:center;background:#111827;color:#fff;font:700 14px sans-serif;letter-spacing:.05em;text-transform:uppercase;}</style>';
echo '</head><body>';
echo '<div class="rexroad-preview-header-mock">Visual mock of the real sticky site header (not rendered here — get_header() is stubbed)</div>';
echo $body;
echo '<script src="/rexroad-custom-theme/assets/js/vehicles.js"></script>';
echo '</body></html>';
