=== Gravity Forms Plivo Add-On ===
Contributors: 8amgmbh
Tags: gravity forms, sms tools, plivo, sms, notifications
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 3.2
Requires PHP: 8.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Integrate Gravity Forms with SMS Tools (default) and Plivo (fallback) for SMS notifications.

== Description ==

This plugin allows you to send SMS notifications when a form is submitted in Gravity Forms. Messages are sent through SMS Tools as the default gateway and automatically fall back to Plivo if a send via SMS Tools fails or SMS Tools is not configured. Configure multiple feeds with different conditions to send customized SMS messages to different recipients.

**Features:**

* Send SMS notifications when forms are submitted
* SMS Tools used as the default gateway with automatic Plivo fallback
* Configure multiple feeds with different conditions
* Preview SMS messages in the entry view
* Track SMS message history and statistics (including which gateway was used)
* Resend SMS messages directly from the entry view
* Automatic updates via GitHub repository

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/gravityforms-plivo` directory, or install the plugin through the WordPress plugins screen.
2. Run `composer install` in the plugin directory to install the SMS Tools and Plivo SDK dependencies.
3. Activate the plugin through the 'Plugins' screen in WordPress.
4. Navigate to Forms → Settings → Plivo SMS to configure your SMS Tools credentials (default gateway) and, optionally, your Plivo credentials (fallback gateway).

== Frequently Asked Questions ==

= Does this plugin work with Gravity Forms? =

Yes, this plugin requires Gravity Forms 2.5 or higher.

= Can I send SMS to multiple recipients? =

Yes, you can create multiple feeds with different conditions to send SMS to different recipients.

= How far back can I see SMS statistics? =

The plugin imports historical messages from Plivo API (limited to 90 days by Plivo's API). All new messages sent after installation are tracked indefinitely in the local database.

== Changelog ==

= 3.2 =
* Added SMS Tools as the default SMS gateway (smstools-api/smstools-php-sdk)
* Plivo is now used only as a fallback when SMS Tools is unavailable or a send fails
* Added a gateway column to the SMS tracking table to record which gateway sent each message
* Entry notes now record which gateway was used
* Raised the minimum PHP requirement to 8.0 (required by the SMS Tools SDK)

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

= 3.2 =
This version adds SMS Tools as the default gateway with Plivo as a fallback. It requires PHP 8.0 or higher and running "composer install" to fetch the SMS Tools SDK.

= 3.1 =
This version fixes critical issues with SMS tracking and improves database operations.

= 3.0 =
This version adds SMS preview, resend functionality, and comprehensive tracking. It also includes automatic updates via GitHub.
