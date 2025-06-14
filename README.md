# Contributor Highlights

A WordPress plugin that displays WordPress.org contributor profiles on your site.

## Description

Contributor Highlights allows you to showcase WordPress.org contributor profiles on your WordPress site. The plugin fetches and displays contributor information from WordPress.org, including avatars, bios, contributions, badges, and meta information.

## Data Collection & Privacy

### What Data We Collect

This plugin does not collect or store any personal data from your website visitors. However, it does:

1. Cache WordPress.org profile data temporarily (for 6 hours) to improve performance
2. Store this cached data in your WordPress database using WordPress transients
3. Automatically clear cached data when the plugin is deactivated

### External Services

The plugin interacts with the following external services:

1. **WordPress.org API**
   - Used to fetch contributor profile data
   - No authentication required
   - Data is publicly available on WordPress.org
   - Rate limits may apply

2. **Gravatar**
   - Used to display contributor avatars
   - No authentication required
   - Data is publicly available
   - Privacy policy: https://automattic.com/privacy/

### Data Flow

1. When a contributor profile is requested:
   - The plugin first checks for cached data
   - If no cache exists, it fetches fresh data from WordPress.org
   - The data is cached for 6 hours to reduce API calls
   - No personal data is stored permanently

2. When the plugin is deactivated:
   - All cached data is automatically cleared
   - No data remains in your database

## Installation

1. Upload the `contributor-highlights` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Use the block editor to add contributor profiles to your posts or pages

## Usage

### Block Editor

1. Add the "Contributor Highlights" block to your post or page
2. Enter a WordPress.org username
3. Configure display options:
   - Show/hide avatar
   - Show/hide bio
   - Show/hide contributions
   - Show/hide badges
   - Show/hide meta information

### Shortcode

You can also use the shortcode:

```
[contributor_profile username="username" show_avatar="yes" show_bio="yes" show_contributions="yes" show_badges="yes" show_meta="yes"]
```

## Requirements

- WordPress 5.8 or higher
- PHP 7.4 or higher
- Active internet connection (for fetching WordPress.org data)

## Support

For support, please visit the [GitHub repository](https://github.com/rollybueno/contributor-highlights).

## License

This plugin is licensed under the GPL v2 or later.

## Credits

- Developed by [Rolly Bueno](https://github.com/rollybueno)
- Uses WordPress.org API for contributor data
- Uses Gravatar for avatar display