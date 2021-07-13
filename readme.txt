=== Lightweight Sidebar Manager ===
Contributors: BrainstormForce
Donate link: https://www.paypal.me/BrainstormForce
Tags: custom sidebar, sidebar manager, custom widget areas, widgets, conditional sidebar
Requires at least: 4.0
Tested up to: 5.8
Stable tag: 1.1.7
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Create new sidebar areas and display them conditionally on certain pages. Works with all themes.

== Description ==

Normally you have a sidebar that appears throughout the website. However, sometimes it is necessary to display a relevant, different sidebar on certain pages on the website. For an example: on WooCommerce pages, a sidebar with related or top seller products would be more relevant than latest comments, blog posts right?

This plugin helps you solve that problem as it allows you to create new sidebars and display them conditionally on certain locations of the website easily. Once the sidebar is created and displayed on pages you like, you can add relevant widgets in it.

Some of the Features:

1. Create unlimited sidebars
2. Place them any location your theme has defined (Footer Widgets / Left or Right Sidebar)
3. Works with any theme
4. Conditionally display sidebars on specific posts, pages, taxonomies or custom post types
5. Display sidebars based on user roles

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Go to Appearance->Sidebars to create new sidebars
4. In Appearance->Widgets, you can add widgets in the newly added sidebar area.

== Frequently Asked Questions ==

= Which themes does this work with? =

This plugin works with all well coded themes that have sidebar locations defined.

= There are many other similar plugins. Why this? =

Other plugins we found are heavy with ugly interface, non supported, developed only for specific themes or affecting performance. So we wanted to develop something simple & straightforward so we can recommend it users of our Astra Theme.


== Screenshots ==

1. Add a New Sidebar from Appearance -> Sidebars -> Add New.
1. Give sidebar a name, Select a sidebar that is to be replaced and locations where the sidebar should appear.
1. Add Content to the newly created sidebar.


== Changelog ==

= 1.1.7 =
- Fix: Added compatibility with WordPress v5.7 for jQuery migration warnings on admin page.

= 1.1.6 =
- Improvement: Hardened the security of plugin.

= 1.1.5 =
- Fix: Security hardening.

= 1.1.4 =
- Fix: Fixed compatibility with other plugins with respect to the admin notice.

= 1.1.3 =
- New: Users can now share non-personal usage data to help us test and develop better products. ( https://store.brainstormforce.com/usage-tracking/?utm_source=wp_dashboard&utm_medium=general_settings&utm_campaign=usage_tracking )

= 1.1.2 =
- Improvement: Hardened the security of plugin
- Improvement: Compatibility with latest WordPress PHP_CodeSniffer rules

= 1.1.1 =
- Fix: Fixes a fatal error on Sidebar list page.

= 1.1.0 =
- New: Target rules appearing in Display Rules column for sidebars.
- Improvement: White Label can be set from wp-config.php file.

= 1.0.2 =
- Improvement: Update target rules with support for targeting all posts inside taxonomies and terms.
- Fix: If a taxonomy is used for multiple post types, it was not displayed in target rules.
- Fix: Load correct textdomain and allow the plugin to be translated from translate.W.org

= 1.0.1 =
- White Label support added from the [Astra Pro](https://wpastra.com/pro/) plugin.
- Optimized target rules query to be even more lightweight.

= 1.0.0 =
- Initial release
