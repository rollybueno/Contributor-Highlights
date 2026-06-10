# Contributor Highlights

A WordPress plugin that displays WordPress.org contributor profiles on your site using a Gutenberg block or shortcode.

## Description

Contributor Highlights allows you to showcase WordPress.org contributor profiles on your website. It fetches profile data from [profiles.wordpress.org](https://profiles.wordpress.org) and displays it in a clean, organized card layout. The plugin supports both a full profile view and a compact version for more subtle integration.

### Features

- Display WordPress.org contributor profiles using a Gutenberg block or shortcode
- Support for the redesigned profiles.wordpress.org markup
- Profile header with avatar, name, handle, location, member since, links, teams, and languages
- Current job, bio (with read more/less), recent impact stats, team focus, and WordPress releases
- Grouped contribution badges and achievements
- Team chips color-coded to match contributor badge colors
- Compact version for minimal display
- Responsive design
- Caches profile data for better performance
- Privacy-friendly: only fetches publicly available data from WordPress.org profiles

### Block Editor Features

- Easy-to-use interface for configuring profile display
- **Display Sections** panel with toggles for avatar, profile meta, current job, bio, recent impact, team focus, badges, and WordPress releases
- Compact version toggle
- Alignment options (wide and full width)
- Live preview in the editor

## Installation

1. Upload the `contributor-highlights` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Use the block editor to add a Contributor Highlights block or use the shortcode `[contributor_profile]`

## Usage

### Using the Block Editor

1. Add a new block and search for "Contributor Highlights"
2. Enter the WordPress.org username
3. Configure display options under **Profile Settings** and **Display Sections**:
   - Toggle compact version
   - Show/hide avatar, profile meta, current job, bio, recent impact, team focus, badges, and WordPress releases
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
    compact_version="false"
    show_avatar="true"
    show_meta="true"
    show_current_job="true"
    show_bio="true"
    show_contributions="true"
    show_team_focus="true"
    show_badges="true"
    show_releases="true"
]
```

### Shortcode Parameters

- `username` (required): The WordPress.org username
- `compact_version` (optional): Set to `"true"` or `"false"` for a minimal display
- `show_avatar` (optional): Show or hide the avatar
- `show_meta` (optional): Show or hide profile meta (handle, location, joined, links, teams, languages)
- `show_current_job` (optional): Show or hide the current job section
- `show_bio` (optional): Show or hide the bio
- `show_contributions` (optional): Show or hide recent impact stats
- `show_team_focus` (optional): Show or hide the team focus chart (full card only)
- `show_badges` (optional): Show or hide badges
- `show_releases` (optional): Show or hide WordPress releases (full card only)

Boolean attributes accept `true`, `false`, `yes`, `no`, `1`, and `0`.

## Privacy Policy

This plugin only fetches publicly available data from WordPress.org profiles. It does not collect or store any personal information from your site visitors. Profile data is temporarily cached and cleared on deactivation.

## Frequently Asked Questions

### How does the plugin get contributor data?

The plugin fetches publicly available HTML from WordPress.org profiles and parses the redesigned profile sections into structured data for display.

### Is there a compact version available?

Yes. Use the compact version toggle in the block editor or set `compact_version="true"` in the shortcode. The compact version shows a minimal card with avatar, meta, badges, and optional compact job or impact lines.

### Can I customize the appearance?

Yes. The plugin uses semantic `contributor-*` class names for easy styling in your theme or custom CSS.

### Does the plugin cache data?

Yes. Profile data is cached for 6 hours to improve performance and reduce requests to WordPress.org.

## Changelog

### 1.2.0

- New: Support for the redesigned profiles.wordpress.org markup and profile card sections (header, current job, bio, recent impact, team focus, WordPress releases, and grouped badges)
- New: Block Display Sections panel with toggles for avatar, profile meta, current job, bio, recent impact, team focus, badges, and WordPress releases
- New: Team chips in profile meta color-coded to match contributor badge colors
- New: Collapsible bio with read more/less and spacing between paragraphs
- Fix: Shortcode boolean attributes (e.g. `show_bio="false"`) now parse correctly
- Fix: Unified profile card typography and reduced contributor name size for consistency
- Fix: Badges section moved to the end of the full profile card
- Fix: WordPress releases legend simplified to role labels only

### 1.1.0

- New: Core AI, Playground and Credits Mentor badges
- Fix: Broken dependencies that block the icons from rendering
- Fix: Container alignment on column usage

### 1.0.0

- Initial release

## Upgrade Notice

### 1.2.0

Major update for the redesigned WordPress.org profile layout. Adds new profile sections, block display toggles, and refreshes parsing and styling. Clear profile caches or wait for them to expire after upgrading.

## Screenshots

1. Block editor interface
2. Full profile display
3. Compact version display
4. Shortcode usage example

## Credits

Developed by [Rolly Bueno](https://profiles.wordpress.org/rollybueno)

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
