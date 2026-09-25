<?php
/**
 * NOT a test. One-off generator for manual/browser QA of
 * page-vehicle-model.php.
 *
 * Usage: php tests/vehicles/lib/render-model-preview.php <make-slug> <model-slug> <make-title> <model-title> > preview.html
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

$make_slug   = $argv[1] ?? 'ford';
$model_slug  = $argv[2] ?? 'f-150';
$make_title  = $argv[3] ?? 'Ford';
$model_title = $argv[4] ?? 'F-150';

$GLOBALS['rexroad_test_pages'] = array(
	1 => array( 'ID' => 1, 'post_name' => 'vehicles', 'post_parent' => 0, 'post_title' => 'Cars, Trucks & SUVs We Service', 'post_status' => 'publish', 'template' => 'page-vehicles.php' ),
	2 => array( 'ID' => 2, 'post_name' => $make_slug, 'post_parent' => 1, 'post_title' => $make_title, 'post_status' => 'publish', 'template' => 'page-vehicle-make.php' ),
	3 => array( 'ID' => 3, 'post_name' => $model_slug, 'post_parent' => 2, 'post_title' => $model_title, 'post_status' => 'publish' ),
);
$GLOBALS['rexroad_test_current_post_id']      = 3;
$GLOBALS['rexroad_test_have_posts_remaining'] = 1;

ob_start();
require $root . '/rexroad-custom-theme/page-vehicle-model.php';
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
echo '<title>Vehicle Model Page Preview &mdash; ' . htmlspecialchars( $make_title . ' ' . $model_title ) . '</title>';
echo $links;
echo '<style>.rexroad-preview-header-mock{position:sticky;top:0;z-index:999;min-height:126px;display:flex;align-items:center;justify-content:center;background:#111827;color:#fff;font:700 14px sans-serif;letter-spacing:.05em;text-transform:uppercase;}</style>';
echo '</head><body>';
echo '<div class="rexroad-preview-header-mock">Visual mock of the real sticky site header (not rendered here)</div>';
echo $body;
echo '</body></html>';
