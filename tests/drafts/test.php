<?php
/**
 * PHPUnit real integration tests for WPConstructor Unlinker.
 *
 * Only tests real uninstall and update flows for plugins and themes.
 *
 * @package WPConstructor\SymlinkCleaner
 */

// phpcs:ignoreFile

use WP_UnitTestCase;

/**
 * Class RealSymlinkCleanerTest
 *
 * Real WordPress uninstall/update tests.
 *
 * @package WPConstructor\SymlinkCleaner
 */
class RealSymlinkCleanerTest extends WP_UnitTestCase {

	/**
	 * Plugin directory path.
	 *
	 * @var string
	 */
	private $plugin_dir;

	/**
	 * Theme directory path.
	 *
	 * @var string
	 */
	private $theme_dir;

	/**
	 * File target for symlink.
	 *
	 * @var string
	 */
	private $target_file;

	/**
	 * Directory target for symlink.
	 *
	 * @var string
	 */
	private $target_dir;

	/**
	 * Sets up plugin/theme directories and symlink targets.
	 */
	public function setUp(): void {
		parent::setUp();

		$this->plugin_dir = WP_PLUGIN_DIR . '/test-plugin';
		$this->theme_dir  = get_theme_root() . '/test-theme';

		if ( ! file_exists( $this->plugin_dir ) ) {
			mkdir( $this->plugin_dir, 0777, true );
		}

		if ( ! file_exists( $this->theme_dir ) ) {
			mkdir( $this->theme_dir, 0777, true );
		}

		$this->target_file = WP_PLUGIN_DIR . '/real-file.txt';
		file_put_contents( $this->target_file, 'test' );

		$this->target_dir = WP_PLUGIN_DIR . '/real-dir';
		if ( ! file_exists( $this->target_dir ) ) {
			mkdir( $this->target_dir, 0777, true );
			file_put_contents( $this->target_dir . '/dummy.txt', 'dummy' );
		}
	}

	/**
	 * Cleans up all created files and directories.
	 */
	public function tearDown(): void {
		if ( is_dir( $this->plugin_dir ) ) {
			rmdir( $this->plugin_dir );
		}

		if ( is_dir( $this->theme_dir ) ) {
			rmdir( $this->theme_dir );
		}

		if ( file_exists( $this->target_file ) ) {
			unlink( $this->target_file );
		}

		if ( is_dir( $this->target_dir ) ) {
			unlink( $this->target_dir . '/dummy.txt' );
			rmdir( $this->target_dir );
		}

		parent::tearDown();
	}

	/**
	 * Real plugin uninstall via WordPress core.
	 */
	public function test_real_plugin_uninstall(): void {
		$plugin_file = 'test-plugin/test-plugin.php';

		// Ensure plugin file exists.
		@mkdir( dirname( WP_PLUGIN_DIR . '/' . $plugin_file ), 0777, true );
		file_put_contents( WP_PLUGIN_DIR . '/' . $plugin_file, '<?php // test plugin' );

		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		// Trigger real uninstall.
		delete_plugins( array( $plugin_file ) );

		$this->assertDirectoryDoesNotExist( $this->plugin_dir );
		$this->assertFileExists( $this->target_file );
		$this->assertDirectoryExists( $this->target_dir );
	}

	/**
	 * Real plugin update via WordPress upgrader.
	 */
	public function test_real_plugin_update(): void {
		$plugin_file = 'test-plugin/test-plugin.php';

		@mkdir( dirname( WP_PLUGIN_DIR . '/' . $plugin_file ), 0777, true );
		file_put_contents( WP_PLUGIN_DIR . '/' . $plugin_file, '<?php // test plugin' );

		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		$upgrader = new Plugin_Upgrader( new Automatic_Upgrader_Skin() );

		do_action(
			'upgrader_process_complete',
			$upgrader,
			array(
				'action'  => 'update',
				'type'    => 'plugin',
				'plugins' => array( $plugin_file ),
			)
		);

		$this->assertFileExists( $this->target_file );
		$this->assertDirectoryExists( $this->target_dir );
	}

	/**
	 * Real theme uninstall via WordPress core.
	 */
	public function test_real_theme_uninstall(): void {
		$theme = 'test-theme';

		// Ensure theme directory exists.
		@mkdir( $this->theme_dir, 0777, true );
		file_put_contents( $this->theme_dir . '/style.css', '/* test theme */' );

		require_once ABSPATH . 'wp-admin/includes/theme.php';
		wp_delete_theme( $theme );

		$this->assertDirectoryDoesNotExist( $this->theme_dir );
		$this->assertFileExists( $this->target_file );
		$this->assertDirectoryExists( $this->target_dir );
	}

	/**
	 * Real theme update via WordPress upgrader.
	 */
	public function test_real_theme_update(): void {
		$theme = 'test-theme';

		// Ensure theme directory exists.
		@mkdir( $this->theme_dir, 0777, true );
		file_put_contents( $this->theme_dir . '/style.css', '/* test theme */' );

		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		$upgrader = new Theme_Upgrader( new Automatic_Upgrader_Skin() );

		do_action(
			'upgrader_process_complete',
			$upgrader,
			array(
				'action' => 'update',
				'type'   => 'theme',
				'themes' => array( $theme ),
			)
		);

		$this->assertFileExists( $this->target_file );
		$this->assertDirectoryExists( $this->target_dir );
	}
}
