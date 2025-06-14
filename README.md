# Contributor Highlights

A WordPress plugin that pulls and displays information from WordPress.org profiles.

## Description

Contributor Highlights allows you to display WordPress.org contributor profiles on your website using a simple shortcode. The plugin fetches profile information including avatar, bio, and contributions from WordPress.org profiles and displays them in a clean, responsive layout.

## Features

- Display WordPress.org contributor profiles using a shortcode
- Show/hide avatar, bio, and contributions
- Responsive design
- Caching for improved performance
- Easy to customize with CSS

## Installation

1. Upload the `contributor-highlights` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Use the shortcode `[contributor_profile username="username"]` to display a profile

## Usage

### Basic Usage

```
[contributor_profile username="rollybueno"]
```

### Advanced Usage

```
[contributor_profile username="rollybueno" show_avatar="yes" show_bio="yes" show_contributions="yes"]
```

### Parameters

- `username` (required): The WordPress.org username
- `show_avatar` (optional): Show the contributor avatar (yes/no, default: yes)
- `show_bio` (optional): Show the contributor bio (yes/no, default: yes)
- `show_contributions` (optional): Show the contributor contributions (yes/no, default: yes)

## Customization

The plugin's output can be customized using CSS. The main classes are:

- `.contributor-profile`: The main container
- `.contributor-avatar`: The avatar container
- `.contributor-info`: The information container
- `.contributor-name`: The name heading
- `.contributor-bio`: The bio section
- `.contributor-contributions`: The contributions section

## Cache

Profile data is cached for 1 hour to improve performance. You can clear the cache by deactivating and reactivating the plugin.

## Requirements

- WordPress 5.0 or higher
- PHP 7.2 or higher

## License

This plugin is licensed under the GPL v2 or later.

## Author

[Rolly Bueno](https://profiles.wordpress.org/rollybueno/)