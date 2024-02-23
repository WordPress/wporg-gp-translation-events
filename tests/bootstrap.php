<?php

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

function _glotpress_path( string $path ): string {
	$glotpress_path = dirname( __DIR__, 2 ) . '/glotpress/';
	if ( getenv( 'GITHUB_ACTIONS' ) ) {
		$glotpress_path = '/tmp/wordpress/wp-content/plugins/glotpress/';
	}
	return $glotpress_path . $path;
}

// Forward custom PHPUnit Polyfills configuration to PHPUnit bootstrap file.
$_phpunit_polyfills_path = getenv( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH' );
if ( false !== $_phpunit_polyfills_path ) {
	define( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH', $_phpunit_polyfills_path );
}

if ( ! file_exists( "$_tests_dir/includes/functions.php" ) ) {
	echo "Could not find $_tests_dir/includes/functions.php, have you run bin/install-wp-tests.sh ?" . PHP_EOL; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	exit( 1 );
}

// Give access to tests_add_filter() function.
require_once "$_tests_dir/includes/functions.php";

/**
 * Manually load the plugin being tested.
 */
function _manually_load_plugin() {
	require_once _glotpress_path( '/tests/phpunit/includes/loader.php' );
	require_once dirname( __DIR__ ) . '/wporg-gp-translation-events.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

global $wp_tests_options;
$wp_tests_options['permalink_structure'] = '/%postname%';

// Start up the WP testing environment.
require "$_tests_dir/includes/bootstrap.php";

// Require GlotPress test code.
require_once _glotpress_path( '/tests/phpunit/lib/testcase.php' );
require_once _glotpress_path( '/tests/phpunit/lib/testcase-route.php' );
require_once _glotpress_path( '/tests/phpunit/lib/testcase-request.php' );
