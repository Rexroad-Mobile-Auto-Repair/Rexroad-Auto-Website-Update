<?php
/**
 * Dependency-free check that vehicles.css/vehicles.js are only
 * registered under an is_page_template('page-vehicles.php') guard in
 * inc/enqueue.php.
 *
 * There is no live WordPress asset-registration system to execute
 * outside a real bootstrap, so this is a static-source assertion
 * rather than a functional one: it parses the real enqueue.php source
 * and confirms both enqueue calls sit inside the expected conditional.
 *
 * Usage: php tests/vehicles/test-enqueue-scoping.php
 */

declare( strict_types=1 );

$root = dirname( __DIR__, 2 );
$source = (string) file_get_contents( $root . '/rexroad-custom-theme/inc/enqueue.php' );

$failures = 0;

function rexroad_test_check( string $label, bool $condition ): void {
	global $failures;
	echo ( $condition ? 'PASS' : 'FAIL' ) . ": {$label}\n";
	if ( ! $condition ) {
		++$failures;
	}
}

rexroad_test_check( 'enqueue.php references vehicles.css', false !== strpos( $source, "/assets/css/vehicles.css" ) );
rexroad_test_check( 'enqueue.php references vehicles.js', false !== strpos( $source, "/assets/js/vehicles.js" ) );

rexroad_test_check(
	'vehicles.css registration sits inside an is_page_template(...) guard covering page-vehicles.php',
	1 === preg_match(
		"/if\\s*\\([^{]*is_page_template\\(\\s*'page-vehicles\\.php'\\s*\\)[^{]*\\)\\s*\\{[^}]*vehicles\\.css/s",
		$source
	)
);

rexroad_test_check(
	'vehicles.css registration also covers page-vehicle-make.php in the same guard',
	1 === preg_match(
		"/if\\s*\\([^{]*is_page_template\\(\\s*'page-vehicle-make\\.php'\\s*\\)[^{]*\\)\\s*\\{[^}]*vehicles\\.css/s",
		$source
	)
);

rexroad_test_check(
	'vehicles.css registration also covers page-vehicle-model.php in the same guard',
	1 === preg_match(
		"/if\\s*\\([^{]*is_page_template\\(\\s*'page-vehicle-model\\.php'\\s*\\)[^{]*\\)\\s*\\{[^}]*vehicles\\.css/s",
		$source
	)
);

rexroad_test_check(
	'vehicles.js registration sits inside an is_page_template("page-vehicles.php") guard',
	1 === preg_match(
		"/if\\s*\\(\\s*is_page_template\\(\\s*'page-vehicles\\.php'\\s*\\)\\s*\\)\\s*\\{[^}]*vehicles\\.js/s",
		$source
	)
);

// Neither asset should be enqueued unconditionally (i.e. outside any
// is_page_template guard, such as inside the always-run $styles array
// for non-front pages, or via a second, unguarded wp_enqueue_* call).
// vehicles.css is registered via the shared $styles-array + foreach
// pattern (like every other non-homepage stylesheet), not a direct
// wp_enqueue_style() call — so check the array-key assignment instead.
rexroad_test_check(
	'the "rexroad-custom-vehicles" $styles array key is assigned exactly once',
	1 === preg_match_all( "/\\\$styles\\['rexroad-custom-vehicles'\\]\\s*=/", $source )
);
rexroad_test_check(
	'wp_enqueue_script for vehicles.js is called exactly once in the file',
	1 === preg_match_all( "/wp_enqueue_script\\(\\s*'rexroad-custom-vehicles'/", $source )
);

if ( $failures > 0 ) {
	fwrite( STDERR, "\n{$failures} enqueue-scoping test(s) failed.\n" );
	exit( 1 );
}

echo "\nAll enqueue-scoping tests passed.\n";
exit( 0 );
