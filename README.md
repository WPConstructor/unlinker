# WPConstructor Unlinker

> Safely removes symbolic links (symlinks) inside WordPress plugins or themes during **updates** and **uninstallation**, without touching the original source directories.

---

## 📦 Overview

**WPConstructor Unlinker** is a developer-focused WordPress plugin that automatically detects and removes symbolic links inside plugin or theme directories when they are **updated or uninstalled**.

It is built for modern development workflows using tools like **Composer**, **npm**, or **monorepos**, where symlinks are commonly used to link external packages into WordPress projects.

Instead of deleting real source directories, it safely removes only the symlink references, ensuring external development files remain untouched.

---

## 🚀 Features

* 🔍 Automatically detects symlinks in plugins and themes
* 🧹 Removes symlinks during **plugin update** and **uninstall**
* 🛡️ Preserves original source directories (never deletes them)
* ⚙️ Zero configuration required
* 🧩 Works with both plugins and themes
* 🪶 Lightweight and developer-friendly

---

## 🧑‍💻 Use Cases

* Composer-based WordPress development
* npm / frontend build workflows
* Monorepo architectures
* Local development with shared packages
* Preventing update/uninstall issues caused by symlinked files

---

## 📥 Installation

1. Upload the plugin folder to `/wp-content/plugins/`
2. Activate **WPConstructor Unlinker** from the WordPress admin panel
3. Done — it runs automatically when needed

---

## ❓ FAQ

### Does this delete the original files the symlink points to?

No. Only the symlink is removed. The original directory and files are untouched.

### Do I need to configure anything?

No configuration is required. The plugin works automatically.

### Is it safe for production?

Yes. It only targets symlinks inside plugin or theme directories and does not affect external paths.

### Why do I need this plugin?

WordPress can mishandle symlinked files during updates or uninstall operations. This plugin ensures only the symlinks are removed, preventing accidental issues.

---

## 📜 Changelog

### 1.0.0

* Initial release
* Automatic symlink cleanup on plugin update and uninstall
* Safe handling of symlinked directories

---

## ⬆️ Upgrade Notice

### 1.0.0

Initial release of WPConstructor Unlinker.

---

## 📄 License

Licensed under **GPL-3.0-or-later**
