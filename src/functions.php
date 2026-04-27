<?php
/**
 * Symlink cleanup helpers for WPConstructor.
 *
 * Provides functions that remove symbolic links from plugin and theme
 * directories during delete and update operations. This prevents
 * dangling symlinks from persisting after WordPress manages files.
 *
 * These functions are intended to be hooked into WordPress lifecycle
 * events such as plugin deletion, plugin updates, theme deletion,
 * and theme updates.
 *
 * @package WPConstructor\SymlinkCleaner
 * @version 1.0.0
 * @since 1.0.0
 */

namespace WPConstructor\SymlinkCleaner;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

/**
 * Handles symlink cleanup when a theme is deleted.
 *
 * @version 1.0.0
 * @since 1.0.0
 *
 * @param string $stylesheet Theme folder being deleted.
 * @return void
 */
function on_theme_delete( string $stylesheet ): void {
	$theme_dir = get_theme_root() . '/' . $stylesheet;
	if ( is_dir( $theme_dir ) ) {
		unlink_symlinks_in_dir( $theme_dir );
	}
}

/**
 * Removes symlinks from theme directories before an update is installed.
 *
 * @version 1.0.0
 * @since 1.0.0
 *
 * @param bool  $reply      Default return value. True allows install.
 * @param array $hook_extra Extra data about the update.
 * @return bool
 */
function before_theme_update( bool $reply, array $hook_extra ): bool {

	// Only run for themes.
	if ( empty( $hook_extra['theme'] ) || empty( $hook_extra['type'] ) || 'theme' !== $hook_extra['type'] ) {
		return $reply;
	}

	$themes = (array) $hook_extra['theme'];

	foreach ( $themes as $theme ) {
		$theme_dir = get_theme_root() . '/' . $theme;
		if ( is_dir( $theme_dir ) ) {
			unlink_symlinks_in_dir( $theme_dir );
		}
	}

	return $reply;
}

/**
 * Recursively scans a directory and returns an array of all symbolic link paths found.
 *
 * @version 1.0.0
 * @since 1.0.0
 *
 * @param string $dir Directory to scan.
 * @return array List of symlink paths.
 */
function find_symlinks( string $dir ): array {
	$symlinks = array();
	if ( ! is_dir( $dir ) ) {
		return $symlinks;
	}

	$iterator = new \RecursiveIteratorIterator(
		new \RecursiveDirectoryIterator( $dir, \RecursiveDirectoryIterator::SKIP_DOTS ),
		\RecursiveIteratorIterator::SELF_FIRST
	);

	foreach ( $iterator as $fileinfo ) {
		$path = $fileinfo->getPathname();
		if ( is_link( $path ) ) {
			$symlinks[] = $path;
		}
	}

	return $symlinks;
}

/**
 * Identifies and deletes all symbolic links within a specified directory.
 *
 * @version 1.0.0
 * @since 1.0.0
 *
 * @param string $dir Directory to clean.
 * @return void
 */
function unlink_symlinks_in_dir( string $dir ): void {
	$symlinks = find_symlinks( $dir );
	foreach ( $symlinks as $link ) {
		if ( is_link( $link ) ) {
            // phpcs:ignore
			unlink( $link );
		}
	}
}

/**
 * Handles symlink cleanup when a plugin is deleted.
 *
 * @version 1.0.0
 * @since 1.0.0
 *
 * @param string $plugin Relative path to plugin being deleted (e.g. my-plugin/my-plugin.php).
 * @return void
 */
function on_plugin_delete( string $plugin ): void {
	$plugin_dir = WP_PLUGIN_DIR . '/' . dirname( $plugin );
	if ( is_dir( $plugin_dir ) ) {
		unlink_symlinks_in_dir( $plugin_dir );
	}
}

/**
 * Removes symlinks from plugin directories before an update is installed.
 *
 * @version 1.0.0
 * @since 1.0.0
 *
 * @param bool  $reply      Default return value. True allows install.
 * @param array $hook_extra Extra data about the update.
 * @return bool
 */
function before_plugin_update( bool $reply, array $hook_extra ): bool {

	// Only run for plugins.
	if ( empty( $hook_extra['plugin'] ) || empty( $hook_extra['type'] ) || 'plugin' !== $hook_extra['type'] ) {
		return $reply;
	}

	$plugins = (array) $hook_extra['plugin'];

	foreach ( $plugins as $plugin ) {
		$plugin_dir = WP_PLUGIN_DIR . '/' . dirname( $plugin );
		if ( is_dir( $plugin_dir ) ) {
			unlink_symlinks_in_dir( $plugin_dir );
		}
	}

	return $reply;
}
