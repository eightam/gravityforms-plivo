# Gravity Forms Plivo Add-On

Integrate Gravity Forms with SMS Tools (primary) and Plivo (fallback) for SMS notifications.

## Description

This plugin allows you to send SMS notifications when a form is submitted in Gravity Forms. As of version 3.2, [SMS Tools](https://github.com/Smstools-api/smstools-php-sdk) is used as the default (primary) gateway, and Plivo is used only as a fallback if sending via SMS Tools fails. Configure multiple feeds with different conditions to send customized SMS messages to different recipients.

## Features

- Send SMS notifications when forms are submitted
- SMS Tools as the primary gateway with automatic Plivo fallback
- Configure multiple feeds with different conditions
- Preview SMS messages in the entry view
- Track SMS message history and statistics (including which gateway was used)
- Resend SMS messages directly from the entry view
- Send to a Telegram group instead of SMS, per feed
- Automatic updates via GitHub repository

## Requirements

- WordPress 5.0 or higher
- Gravity Forms 2.5 or higher
- PHP 8.0 or higher (required by the SMS Tools SDK)
- An SMS Tools account (primary) and/or a Plivo account (fallback) with API credentials, and/or a Telegram bot

## Telegram

Each feed can send via SMS or post to a Telegram group instead.

1. Create a bot with [@BotFather](https://t.me/BotFather) and copy its token.
2. Add the bot to your Telegram group.
3. Find the group's chat ID (e.g. `-1001234567890`): send `/start` in the group (bots only see commands by default), then open `https://api.telegram.org/bot<TOKEN>/getUpdates` and look for `chat.id`.
4. Paste the token under Forms → Settings → Plivo SMS → Telegram Bot Settings.
5. In the form feed, choose **Send Via: Telegram** and enter the chat ID.

Telegram messages are tracked alongside SMS (gateway `telegram`) but excluded from the dashboard's cost figures.

Other plugins can add `sendMessage` parameters with the `gf_plivo_telegram_message_params` filter (`$params, $feed, $entry, $form`), e.g. an inline keyboard. `chat_id` and `text` cannot be changed. [Gravity Forms Entry Telegram Mini App](https://github.com/eightam/gravityforms-entry-telegram-miniapp) uses it to add a "Details öffnen" button that opens the entry in a Telegram Mini App. If Telegram rejects a message with `reply_markup`, it is sent again without it.

## Changelog

### 3.7 - October 23, 2026
- Fixed Plivo errors (HTTP 4xx, e.g. 415) being recorded as "Call to undefined method MessageCreateErrorResponse::getMessageUuid()" since the plivo-php 4.71 update in 3.5; the entry note now shows Plivo's HTTP status and error message

### 3.6 - October 13, 2026
- Added the `gf_plivo_telegram_message_params` filter for the Telegram `sendMessage` parameters (used by the Gravity Forms Entry Telegram Mini App plugin to add a "Details öffnen" button)
- If Telegram rejects a message because of an added button, the message is sent again without the button

### 3.5 - October 13, 2026
- Fixed the release build: Composer dependencies are now locked for PHP 8.0 (the 3.3 and 3.4 release builds failed, so they shipped without the bundled `vendor/` folder)
- Updated Guzzle, guzzlehttp/psr7, firebase/php-jwt and plivo-php to versions without known security advisories
- If SMS Tools fails with any PHP error (not just exceptions), Plivo is still tried as the fallback; the same applies to Telegram failures being recorded on the entry
- A missing `vendor/` folder no longer causes a fatal error when sending via Plivo; the failure is recorded as an entry note instead
- Messages stored before 3.2 are now labelled with the `plivo` gateway instead of being left blank
- The SMS Tools message ID is also read from responses that nest it under `message` or `result`

### 3.4 - October 13, 2026
- Added Telegram as a per-feed channel: each feed can now send via SMS or post to a Telegram group through a bot
- Added Telegram Bot Settings (bot token) and a Telegram Chat ID field on feeds
- SMS Tools credentials are now optional, so the plugin can be used with Telegram only
- Dashboard widget excludes Telegram messages from SMS cost figures
- Fixed the dashboard's Recent Messages list not showing recipients

### 3.3 - July 21, 2026
- Added a GitHub Actions release workflow that builds the distributable plugin zip (including Composer dependencies) and publishes a tagged GitHub Release whenever the plugin version changes
- Added a version-bump check workflow that requires the version to be increased when plugin source changes

### 3.2 - July 21, 2026
- Added SMS Tools (smstools-php-sdk) as the default primary SMS gateway
- Plivo is now used only as a fallback when SMS Tools sending fails
- Added SMS Tools account settings (Client ID, Client Secret, Sender ID, API Base URL)
- Plivo credentials are now optional; a feed can be created when either gateway is configured
- Message tracking now records which gateway sent each message
- Bumped minimum PHP requirement to 8.0 (required by the SMS Tools SDK)

### 3.1 - April 3, 2025
- Fixed critical issue with SMS messages not being tracked in the database
- Enhanced error handling for database operations
- Improved logging for better diagnostics
- Optimized dashboard widget data retrieval

### 3.0 - April 3, 2025
- Added SMS preview in entry sidebar with dropdown selector
- Added ability to resend SMS messages from the entry view
- Added comprehensive SMS tracking in local database
- Added detailed entry notes for SMS messages with delivery status
- Enhanced dashboard widget with visual statistics and recent messages
- Added import functionality for historical Plivo messages
- Fixed API limitation issue with messages older than 90 days
- Added automatic updates via GitHub repository

### 2.5 - Previous Version
- Initial release with basic SMS notification functionality

## Author

[8am GmbH](https://8am.ch) - A Swiss web development company specializing in WordPress solutions.

## License

This plugin is licensed under the [GNU General Public License v2.0 (or later)](https://www.gnu.org/licenses/gpl-2.0.html).

Gravity Forms Plivo Add-On
Copyright (C) 2025 8am GmbH

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

## Installation

1. Upload the plugin files to the `/wp-content/plugins/gravityforms-plivo` directory, or install the plugin through the WordPress plugins screen.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Navigate to Forms → Settings → Plivo SMS to configure your SMS Tools credentials (primary gateway) and, optionally, your Plivo credentials (fallback gateway).

## Updates

This plugin supports automatic updates directly from the GitHub repository. When a new version is released on GitHub, you'll receive update notifications in your WordPress admin dashboard just like any plugin from the WordPress.org repository.

## Releases & Versioning

Releases are automated via GitHub Actions:

- **`.github/workflows/release.yml`** runs on every push to `main`. It reads the version from the `Version:` header in `gravityforms-plivo.php` and, if no matching `v<version>` tag exists yet, it installs the Composer dependencies, builds the distributable `gravityforms-plivo.zip` (with `vendor/` bundled so the plugin works out of the box), creates the git tag, and publishes a GitHub Release with the zip attached as a release asset.
- **`.github/workflows/version-bump-check.yml`** runs on pull requests. If a PR changes plugin source (`*.php` or `composer.*`), it fails unless the `Version:` header has been increased above the version on `main`.

Because releases are version-driven, the rule is simple: **always increase the version number after making changes.** Bumping the version (in the plugin header) is what produces a new tag and release; pushing without a version change is a no-op for the release workflow.

When bumping the version, update it in all of these places:

1. The `Version:` header in `gravityforms-plivo.php`
2. The `$_version` property in the `GF_Plivo_AddOn` class
3. The `Stable tag:` line in `readme.txt`
4. The changelog sections in `README.md` and `readme.txt`

## Support

For support, please contact [8am GmbH](https://8am.ch/contact) or open an issue in the plugin repository.
