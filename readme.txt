=== Gravity Forms Plivo Add-On ===
Contributors: 8amgmbh
Tags: gravity forms, sms tools, plivo, sms, notifications
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 3.6
Requires PHP: 8.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Integrate Gravity Forms with SMS Tools (primary) and Plivo (fallback) for SMS notifications.

== Description ==

This plugin allows you to send SMS notifications when a form is submitted in Gravity Forms. As of version 3.2, SMS Tools is used as the default (primary) gateway, and Plivo is used only as a fallback if sending via SMS Tools fails. Configure multiple feeds with different conditions to send customized SMS messages to different recipients.

**Features:**

* Send SMS notifications when forms are submitted
* SMS Tools as the primary gateway with automatic Plivo fallback
* Configure multiple feeds with different conditions
* Preview SMS messages in the entry view
* Track SMS message history and statistics (including which gateway was used)
* Resend SMS messages directly from the entry view
* Send to a Telegram group instead of SMS, per feed
* Automatic updates via GitHub repository

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/gravityforms-plivo` directory, or install the plugin through the WordPress plugins screen.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Navigate to Forms → Settings → Plivo SMS to configure your SMS Tools credentials (primary gateway) and, optionally, your Plivo credentials (fallback gateway).

== Frequently Asked Questions ==

= Does this plugin work with Gravity Forms? =

Yes, this plugin requires Gravity Forms 2.5 or higher.

= Can I send SMS to multiple recipients? =

Yes, you can create multiple feeds with different conditions to send SMS to different recipients.

= How far back can I see SMS statistics? =

The plugin imports historical messages from Plivo API (limited to 90 days by Plivo's API). All new messages sent after installation are tracked indefinitely in the local database.

== Changelog ==

= 3.6 =
* Added the `gf_plivo_telegram_message_params` filter for the Telegram sendMessage parameters (used by the Gravity Forms Entry Telegram Mini App plugin to add a "Details öffnen" button)
* If Telegram rejects a message because of an added button, the message is sent again without the button

= 3.5 =
* Fixed the release build: Composer dependencies are now locked for PHP 8.0 (the 3.3 and 3.4 release builds failed, so they shipped without the bundled `vendor/` folder)
* Updated Guzzle, guzzlehttp/psr7, firebase/php-jwt and plivo-php to versions without known security advisories
* If SMS Tools fails with any PHP error (not just exceptions), Plivo is still tried as the fallback; the same applies to Telegram failures being recorded on the entry
* A missing `vendor/` folder no longer causes a fatal error when sending via Plivo; the failure is recorded as an entry note instead
* Messages stored before 3.2 are now labelled with the `plivo` gateway instead of being left blank
* The SMS Tools message ID is also read from responses that nest it under `message` or `result`

= 3.4 =
* Added Telegram as a per-feed channel: each feed can now send via SMS or post to a Telegram group through a bot
* Added Telegram Bot Settings (bot token) and a Telegram Chat ID field on feeds
* SMS Tools credentials are now optional, so the plugin can be used with Telegram only
* Dashboard widget excludes Telegram messages from SMS cost figures
* Fixed the dashboard's Recent Messages list not showing recipients

= 3.3 =
* Added GitHub Actions release workflow that builds the distributable plugin zip (with Composer dependencies) and publishes a tagged GitHub Release whenever the plugin version changes
* Added a version-bump check workflow that requires the version to be increased when plugin source changes

= 3.2 =
* Added SMS Tools (smstools-php-sdk) as the default primary SMS gateway
* Plivo is now used only as a fallback when SMS Tools sending fails
* Added SMS Tools account settings (Client ID, Client Secret, Sender ID, API Base URL)
* Plivo credentials are now optional; a feed can be created when either gateway is configured
* Message tracking now records which gateway sent each message
* Bumped minimum PHP requirement to 8.0 (required by the SMS Tools SDK)

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

= 3.5 =
Fixes the release package, which was missing its bundled dependencies in 3.3 and 3.4 (SMS Tools and Plivo sending could fail). Also includes security updates for bundled libraries. Recommended for all users.

= 3.4 =
Adds Telegram as an alternative to SMS. Existing feeds keep sending SMS; switch a feed to Telegram under its settings after adding a bot token.

= 3.3 =
Adds automated release builds and version-bump enforcement via GitHub Actions. No functional changes to SMS sending.

= 3.2 =
This version adds SMS Tools as the primary SMS gateway with Plivo as an automatic fallback. Requires PHP 8.0 or higher. Configure your SMS Tools credentials under Forms → Settings → Plivo SMS.

= 3.1 =
This version fixes critical issues with SMS tracking and improves database operations.

= 3.0 =
This version adds SMS preview, resend functionality, and comprehensive tracking. It also includes automatic updates via GitHub.
