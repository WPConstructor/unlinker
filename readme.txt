=== WPConstructor Unlinker ===
Contributors: wpconstructor
Tags: symlink, cleanup, deployment, composer, npm, developer-tools
Requires at least: 5.6
Tested up to: 6.9
Requires PHP: 8.0
Stable tag: 1.0.0
License: GPL-3.0-or-later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Safely removes all symlinks inside a plugin or theme when it is updated or uninstalled, without touching the original source directories.

== Description ==

**WPConstructor Unlinker** automatically removes symbolic links located inside a plugin or theme directory when that plugin or theme is **updated or uninstalled**.

This is especially useful for developers who use **Composer**, **npm**, or similar workflows that create symlinks inside WordPress plugin or theme directories.

Instead of deleting the real source directories those links point to, the plugin safely removes **only the symlink itself**, ensuring that external development directories remain untouched.

Typical use cases:

* Composer-based WordPress development
* npm-based frontend build systems
* Monorepo setups
* Local development environments using symlinked packages
* Preventing update/uninstall errors caused by symlinked files

The plugin runs automatically and requires **no configuration**.

== Features ==

* Automatically detects and removes symlinks
* Runs during **plugin update** and **plugin uninstall**
* Ensures **original source directories are never deleted**
* Safe for development environments
* Lightweight and developer-friendly
* Works with plugins and themes

== Installation ==

1. Upload the plugin folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. That's it — the plugin runs automatically when needed.

== Frequently Asked Questions ==

= Does this delete the real files my symlink points to? =

No. The plugin only removes the **symlink itself**, never the original directory or files.

= Do I need to configure anything? =

No configuration is required. Once activated, the plugin automatically handles symlink cleanup during plugin updates or uninstall events.

= Is this safe to use in production? =

Yes. The plugin only targets symlinks inside plugin or theme directories and avoids touching external paths.

= Why would I need this plugin? =

When using Composer, npm, or monorepos, plugin directories may contain symlinks. During updates or uninstall operations WordPress may attempt to remove these directories incorrectly. This plugin ensures only the symlink is removed.

== Screenshots ==

1. Automatic cleanup during plugin update
2. Safe removal of symlinks without affecting original directories

== Changelog ==

= 1.0.0 =
* Initial release
* Automatic symlink cleanup on plugin update and uninstall
* Safe handling of symlinked directories

== Upgrade Notice ==

= 1.0.0 =
Initial release of WPConstructor Unlinker.

== License ==

This plugin is licensed under the GPLv3 or later.
