# Gravity Forms Plivo Add-On

Integrate Gravity Forms with SMS Tools (default gateway) and Plivo (fallback) for SMS notifications.

## Description

This plugin allows you to send SMS notifications when a form is submitted in Gravity Forms. Messages are sent through [SMS Tools](https://github.com/Smstools-api/smstools-php-sdk) as the default gateway, and automatically fall back to Plivo if a send via SMS Tools fails or SMS Tools is not configured. Configure multiple feeds with different conditions to send customized SMS messages to different recipients.

## Gateways

- **SMS Tools (default):** Configure your SMS Tools Client ID, Client Secret, and Sender ID under Forms → Settings → Plivo SMS.
- **Plivo (fallback):** Configure your Plivo Auth ID, Auth Token, and Sender ID in the same settings screen. Plivo is only used when SMS Tools is not configured or a send via SMS Tools fails.

Only one gateway needs to be configured for the plugin to work; configure both to enable automatic fallback.

## Features

- Send SMS notifications when forms are submitted
- SMS Tools used as the default gateway with automatic Plivo fallback
- Configure multiple feeds with different conditions
- Preview SMS messages in the entry view
- Track SMS message history and statistics (including which gateway was used)
- Resend SMS messages directly from the entry view
- Automatic updates via GitHub repository

## Requirements

- WordPress 5.0 or higher
- Gravity Forms 2.5 or higher
- PHP 8.0 or higher
- An SMS Tools account and/or a Plivo account with API credentials

## Changelog

### 3.2 - July 21, 2026
- Added SMS Tools as the default SMS gateway (`smstools-api/smstools-php-sdk`)
- Plivo is now used only as a fallback when SMS Tools is unavailable or a send fails
- Added a `gateway` column to the SMS tracking table to record which gateway sent each message
- Entry notes now record which gateway was used
- Raised the minimum PHP requirement to 8.0 (required by the SMS Tools SDK)

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
2. Run `composer install` in the plugin directory to install the SMS Tools and Plivo SDK dependencies.
3. Activate the plugin through the 'Plugins' screen in WordPress.
4. Navigate to Forms → Settings → Plivo SMS to configure your SMS Tools credentials (default gateway) and, optionally, your Plivo credentials (fallback gateway).

## Updates

This plugin supports automatic updates directly from the GitHub repository. When a new version is released on GitHub, you'll receive update notifications in your WordPress admin dashboard just like any plugin from the WordPress.org repository.

To ensure updates work correctly:

1. Make sure your GitHub repository is public
2. Tag releases using semantic versioning (e.g., v3.0, v3.1)
3. Include a `readme.txt` file in the repository with version information

## Support

For support, please contact [8am GmbH](https://8am.ch/contact) or open an issue in the plugin repository.
