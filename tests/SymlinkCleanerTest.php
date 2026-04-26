<?php
use PHPUnit\Framework\TestCase;

// phpcs:ignoreFile

/**
 * Class SymlinkCleanerTest
 *
 * PHPUnit tests for WPConstructor Unlinker.
 *
 * @package WPConstructor\SymlinkCleaner
 */
class SymlinkCleanerTest extends TestCase {

	/**
	 * Path to the temporary plugin directory used in tests.
	 *
	 * @var string
	 */
	private $plugin_dir;

	/**
	 * Path to the temporary theme directory used in tests.
	 *
	 * @var string
	 */
	private $theme_dir;

	/**
	 * Path to the real file that symlinks point to.
	 *
	 * @var string
	 */
	private $target_file;

	/**
	 * Path to the real directory that symlinks point to.
	 *
	 * @var string
	 */
	private $target_dir;

	/**
	 * Path to the file symlink created inside plugin directories.
	 *
	 * @var string
	 */
	private $file_symlink;

	/**
	 * Path to the directory symlink created inside plugin directories.
	 *
	 * @var string
	 */
	private $dir_symlink;

	/**
	 * Sets up temporary plugin/theme directories and creates test symlinks.
	 *
	 * @return void
	 */
	public function setUp(): void {
		// Plugin directory
		$this->plugin_dir = WP_PLUGIN_DIR . '/test-plugin';
		if ( ! is_dir( $this->plugin_dir ) ) {
			mkdir( $this->plugin_dir, 0777, true );
		}

		// Theme directory
		$this->theme_dir = get_theme_root() . '/test-theme';
		if ( ! is_dir( $this->theme_dir ) ) {
			mkdir( $this->theme_dir, 0777, true );
		}

		// Add a dummy file so WordPress-style detection works
		file_put_contents( $this->theme_dir . '/style.css', '/* dummy theme */' );

		// Create a real file to symlink to
		$this->target_file = sys_get_temp_dir() . '/real-file.txt';
		file_put_contents( $this->target_file, 'test' );

		// Create a real directory to symlink to
		$this->target_dir = sys_get_temp_dir() . '/real-dir';
		if ( ! is_dir( $this->target_dir ) ) {
			mkdir( $this->target_dir, 0777, true );
			file_put_contents( $this->target_dir . '/dummy.txt', 'dummy' );
		}

		// Create symlink to file inside plugin directory
		$this->file_symlink = $this->plugin_dir . '/link-file.txt';
		symlink( $this->target_file, $this->file_symlink );

		// Create symlink to directory inside plugin directory
		$this->dir_symlink = $this->plugin_dir . '/link-dir';
		symlink( $this->target_dir, $this->dir_symlink );

		// Create theme symlinks
		symlink( $this->target_file, $this->theme_dir . '/link-file.txt' );
		symlink( $this->target_dir, $this->theme_dir . '/link-dir' );
	}

	/**
	 * Cleans up temporary files and directories.
	 *
	 * @return void
	 */
	public function tearDown(): void {
		// Plugin symlinks
		if ( is_link( $this->file_symlink ) ) {
			unlink( $this->file_symlink );
		}

		if ( is_link( $this->dir_symlink ) ) {
			unlink( $this->dir_symlink );
		}

		// Theme symlinks
		$theme_file_symlink = $this->theme_dir . '/link-file.txt';
		$theme_dir_symlink  = $this->theme_dir . '/link-dir';

		if ( is_link( $theme_file_symlink ) ) {
			unlink( $theme_file_symlink );
		}

		if ( is_link( $theme_dir_symlink ) ) {
			unlink( $theme_dir_symlink );
		}

		// Remove plugin directory
		if ( is_dir( $this->plugin_dir ) ) {
			rmdir( $this->plugin_dir );
		}

		// Remove theme directory and dummy file
		if ( is_file( $this->theme_dir . '/style.css' ) ) {
			unlink( $this->theme_dir . '/style.css' );
		}

		if ( is_dir( $this->theme_dir ) ) {
			rmdir( $this->theme_dir );
		}

		// Remove target file and directory
		if ( file_exists( $this->target_file ) ) {
			unlink( $this->target_file );
		}

		if ( is_dir( $this->target_dir ) ) {
			unlink( $this->target_dir . '/dummy.txt' );
			rmdir( $this->target_dir );
		}

		// Optional: remove WP_PLUGIN_DIR root if empty
		if ( is_dir( WP_PLUGIN_DIR ) && count( scandir( WP_PLUGIN_DIR ) ) === 2 ) {
			rmdir( WP_PLUGIN_DIR );
		}
	}

	/**
	 * @covers WPConstructor\SymlinkCleaner\on_plugin_delete
	 */
	public function test_plugin_uninstall_removes_symlinks(): void {
		WPConstructor\SymlinkCleaner\on_plugin_delete( 'test-plugin/test-plugin.php' );

		$this->assertFalse( is_link( $this->file_symlink ) );
		$this->assertFalse( is_link( $this->dir_symlink ) );
		$this->assertFileExists( $this->target_file );
		$this->assertDirectoryExists( $this->target_dir );
	}

	/**
	 * @covers WPConstructor\SymlinkCleaner\before_plugin_update
	 */
	public function test_plugin_update_removes_symlinks(): void {
		$hook_extra = array(
			'plugin' => 'test-plugin/test-plugin.php',
			'type'   => 'plugin',
		);

		WPConstructor\SymlinkCleaner\before_plugin_update( true, $hook_extra );

		$this->assertFalse( is_link( $this->file_symlink ) );
		$this->assertFalse( is_link( $this->dir_symlink ) );
	}

	/**
	 * @covers WPConstructor\SymlinkCleaner\on_theme_delete
	 */
	public function test_theme_uninstall_removes_symlinks(): void {
		WPConstructor\SymlinkCleaner\on_theme_delete( 'test-theme' );

		$this->assertFalse( is_link( $this->theme_dir . '/link-file.txt' ) );
		$this->assertFalse( is_link( $this->theme_dir . '/link-dir' ) );
	}

	/**
	 * @covers WPConstructor\SymlinkCleaner\before_theme_update
	 */
	public function test_theme_update_removes_symlinks(): void {
		$hook_extra = array(
			'theme' => 'test-theme',
			'type'  => 'theme',
		);

		WPConstructor\SymlinkCleaner\before_theme_update( true, $hook_extra );

		$this->assertFalse( is_link( $this->theme_dir . '/link-file.txt' ) );
		$this->assertFalse( is_link( $this->theme_dir . '/link-dir' ) );
	}
}
