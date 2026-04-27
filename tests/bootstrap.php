<?php
/**
 * PHPUnit bootstrap file for WPConstructor Unlinker tests.
 *
 * This file is loaded before running any tests. It sets up:
 * - Global functions needed by the code under test
 * - Required plugin files
 *
 * @package WPConstructor\SymlinkCleaner\Tests
 */

error_reporting( E_ALL );
ini_set( 'display_errors', '1' );

if ( ! function_exists( 'get_theme_root' ) ) {
	/**
	 * Returns the root directory for themes.
	 *
	 * This is a stub function for PHPUnit tests so that the test
	 * environment does not require a full WordPress installation.
	 *
	 * @return string Absolute path to the temporary test theme directory.
	 */
	function get_theme_root(): string {
		return sys_get_temp_dir() . '/themes';
	}
}

// ---------------------------
// Include the plugin file being tested
// ---------------------------
if ( ! defined( 'WPINC' ) ) {
	define( 'WPINC', true );
}
if ( ! defined( 'WP_PLUGIN_DIR' ) ) {
	define( 'WP_PLUGIN_DIR', sys_get_temp_dir() . '/plugins' );
}

require_once __DIR__ . '/../src/functions.php';

// Ensure temporary folders exist.
@mkdir( get_theme_root(), 0777, true );
@mkdir( WP_PLUGIN_DIR, 0777, true );
