<?php
/**
 * Plugin Name: WPConstructor Unlinker
 * Plugin URI:  https://wpconstructor.com/plugins/wpconstructor-unlinker/
 * Description: Safely removes all symlinks inside a plugin or a theme when it is updated or uninstalled, without touching the original source directories. Useful when working with symlinks in composer or npm.
 * Version:     1.0.1
 * Requires at least: 5.5
 * Requires PHP: 7.1
 * Author:      WPConstructor <https://wpconstructor.com/contact>
 * Author URI:  https://wpconstructor.com
 * License:     GPL-3.0-or-later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package   WPConstructor\Unlinker
 * @copyright 2026 WPConstructor
 */

/**
 * Main plugin bootstrap file.
 *
 * Loads the plugin and initializes functionality.
 *
 * @version 1.0.0
 * @since 1.0.0
 */

namespace WPConstructor\Unlinker;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

require_once __DIR__ . '/src/functions.php';

// Hook into plugin deletion.
add_action( 'delete_plugin', __NAMESPACE__ . '\\on_plugin_delete', 10, 1 );

// Hook into plugin upgrader.
add_filter( 'upgrader_pre_install', __NAMESPACE__ . '\\before_plugin_update', 10, 2 );

// Hook into theme deletion.
add_action( 'delete_theme', __NAMESPACE__ . '\\on_theme_delete', 10, 1 );

// Hook into theme updates.
add_filter( 'upgrader_pre_install', __NAMESPACE__ . '\\before_theme_update', 10, 2 );
