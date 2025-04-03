=== Gravity Forms Plivo Add-On ===
Contributors: 8amgmbh
Tags: gravity forms, plivo, sms, notifications
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 3.1
Requires PHP: 7.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Integrate Gravity Forms with Plivo for SMS notifications.

== Description ==

This plugin allows you to send SMS notifications via Plivo when a form is submitted in Gravity Forms. Configure multiple feeds with different conditions to send customized SMS messages to different recipients.

**Features:**

* Send SMS notifications when forms are submitted
* Configure multiple feeds with different conditions
* Preview SMS messages in the entry view
* Track SMS message history and statistics
* Resend SMS messages directly from the entry view
* Automatic updates via GitHub repository

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/gravityforms-plivo` directory, or install the plugin through the WordPress plugins screen.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Navigate to Forms → Settings → Plivo to configure your Plivo API credentials.

== Frequently Asked Questions ==

= Does this plugin work with Gravity Forms? =

Yes, this plugin requires Gravity Forms 2.5 or higher.

= Can I send SMS to multiple recipients? =

Yes, you can create multiple feeds with different conditions to send SMS to different recipients.

= How far back can I see SMS statistics? =

The plugin imports historical messages from Plivo API (limited to 90 days by Plivo's API). All new messages sent after installation are tracked indefinitely in the local database.

== Changelog ==

= 3.1 =
* Fixed critical issue with SMS messages not being tracked in the database
* Enhanced error handling for database operations
* Improved logging for better diagnostics
* Optimized dashboard widget data retrieval

= 3.0 =
* Added SMS preview in entry sidebar with dropdown selector
* Added ability to resend SMS messages from the entry view
* Added comprehensive SMS tracking in local database
* Added detailed entry notes for SMS messages with delivery status
* Enhanced dashboard widget with visual statistics and recent messages
* Added import functionality for historical Plivo messages
* Fixed API limitation issue with messages older than 90 days
* Added automatic updates via GitHub repository

= 2.5 =
* Initial release with basic SMS notification functionality

== Upgrade Notice ==

= 3.1 =
This version fixes critical issues with SMS tracking and improves database operations.

= 3.0 =
This version adds SMS preview, resend functionality, and comprehensive tracking. It also includes automatic updates via GitHub.
