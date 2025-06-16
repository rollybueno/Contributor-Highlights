# Contributor Highlights

A WordPress plugin that displays WordPress.org contributor profiles on your site using a Gutenberg block or shortcode.

## Description

Contributor Highlights allows you to showcase WordPress.org contributor profiles on your website. It fetches profile data from WordPress.org profiles and displays it in a clean, organized layout. The plugin supports both a full profile view and a compact version for more subtle integration.

### Features

- Display WordPress.org contributor profiles using a Gutenberg block or shortcode
- Show contributor avatars, bios, and contribution information
- Display badges and achievements
- Show meta information like location, job, company, and website
- Compact version for minimal display
- Responsive design
- Caches profile data for better performance
- Privacy-friendly: only fetches publicly available data from WordPress.org profiles

### Block Editor Features

- Easy-to-use interface for configuring profile display
- Toggle controls for showing/hiding different profile sections
- Compact version toggle
- Alignment options (wide and full width)
- Live preview in the editor

## Installation

1. Upload the `contributor-highlights` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Use the block editor to add a Contributor Profile block or use the shortcode `[contributor_profile]`

## Usage

### Using the Block Editor

1. Add a new block and search for "Contributor Profile"
2. Enter the WordPress.org username
3. Configure display options:
   - Toggle compact version
   - Show/hide avatar
   - Show/hide bio
   - Show/hide contributions
   - Show/hide badges
   - Show/hide meta information
4. Choose alignment (wide or full width)

### Using the Shortcode

Basic usage:
```
[contributor_profile username="username"]
```

Advanced usage with options:
```
[contributor_profile 
    username="username"
    compact_version="yes"
    show_avatar="yes"
    show_bio="yes"
    show_contributions="yes"
    show_badges="yes"
    show_meta="yes"
]
```

### Shortcode Parameters

- `username` (required): The WordPress.org username
- `compact_version` (optional): Set to "yes" for a minimal display
- `show_avatar` (optional): Set to "yes" or "no" to show/hide the avatar
- `show_bio` (optional): Set to "yes" or "no" to show/hide the bio
- `show_contributions` (optional): Set to "yes" or "no" to show/hide contributions
- `show_badges` (optional): Set to "yes" or "no" to show/hide badges
- `show_meta` (optional): Set to "yes" or "no" to show/hide meta information

## Privacy Policy

This plugin only fetches publicly available data from WordPress.org profiles. It does not collect or store any personal information. The plugin uses WordPress.org's public API to display profile information that is already publicly accessible.

## Frequently Asked Questions

### How does the plugin get contributor data?

The plugin fetches publicly available data from WordPress.org profiles. This includes information that contributors have chosen to make public on their WordPress.org profiles.

### Is there a compact version available?

Yes! You can use the compact version either through the block editor toggle or by setting `compact_version="yes"` in the shortcode. The compact version shows a minimal display with just the avatar, badges, and meta information.

### Can I customize the appearance?

Yes, you can customize the appearance using CSS. The plugin uses semantic class names for easy styling.

### Does the plugin cache data?

Yes, the plugin caches profile data for 6 hours to improve performance and reduce server load.

## Changelog

### 1.0.0
- Initial release

### 1.0.1
- Added compact version feature
- Improved block editor interface
- Added alignment options
- Fixed boolean attribute handling in shortcode generation
- Improved error handling and display

## Upgrade Notice

### 1.0.1
This update adds a new compact version feature and improves the block editor interface. It also fixes several bugs and improves overall stability.

## Screenshots

1. Block editor interface
2. Full profile display
3. Compact version display
4. Shortcode usage example

## Credits

Developed by [Your Name/Organization]

## License

This plugin is licensed under the GPL v2 or later.

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA. 
