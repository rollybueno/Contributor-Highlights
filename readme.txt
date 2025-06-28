=== Contributor Highlights ===
Contributors: rollybueno
Tags: contributor, profile, wordpress.org, badges, block
Requires at least: 5.0
Tested up to: 6.8
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Display WordPress.org contributor profiles on your site using a block or shortcode.

== Description ==

Contributor Highlights is a lightweight plugin designed for individuals and organizations who want to display their WordPress.org contribution journey or highlight the efforts of others in the WordPress community.

With this plugin, you can easily fetch and present detailed contributor information from public WordPress.org profiles. The plugin pulls in and displays:

- Contributor name and avatar
- Contribution weekly pledge
- Short biography
- Contribution badges (e.g., Core Contributor, Plugin Developer, Translation Editor, etc.)
- Meta information such as member since date, location, website, GitHub, employer, and job title

Perfect for personal portfolios, agency team pages, or contributor showcases on community sites.

This plugin does not require any API keys or manual data entry — just input a WordPress.org username, and the profile will be automatically rendered in a card format.

Whether you're celebrating your own WordPress journey or recognizing others, Contributor Highlights makes it easy to bring open-source contributions front and center.

== External Services ==

The plugin interacts with the following external services:

* **WordPress.org Profiles**
  * **Purpose**: To fetch and display contributor profile data within the plugin interface.
  * **What is fetched?**: Publicly available data such as display name, avatar, badges, and contribution activity.
  * **Authentication**: No authentication or login is required.
  * **Privacy Impact**: The plugin only reads data that is already publicly accessible on profiles.wordpress.org. It does not store, share, or transmit this data beyond displaying it to site users.

For more information, see [WordPress.org Privacy Policy](https://wordpress.org/about/privacy/).

== Privacy & Data Collection ==

This plugin does not collect or store any personal data from your website visitors. It fetches publicly available contributor information from WordPress.org Profiles (including avatars) without requiring authentication. To improve performance, the data is temporarily cached and automatically cleared upon deactivation.
== Installation ==

1. Upload the `contributor-highlights` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Use the block editor to add contributor profiles to your posts or pages

== Frequently Asked Questions ==

= Does this plugin collect any personal data? =

No, this plugin does not collect any personal data from your website visitors. It only temporarily caches publicly available data from WordPress.org profiles to improve performance.

= What happens to the cached data when I deactivate the plugin? =

All cached data is automatically cleared when you deactivate the plugin. No data remains in your database.

= Does this plugin require any API keys? =

No, this plugin does not require any API keys. It uses publicly available data from WordPress.org profiles.

= Is there a limit to how many profiles I can display? =

There are no hard limits, but please be mindful of request frequency to WordPress.org. The plugin implements a 6-hour cache to help manage requests.

== Screenshots ==

1. Contributor Highlights block in the editor, displaying a profile with block settings visible in the sidebar.
2. The block inserter in the WordPress editor, demonstrating how to find the Contributor Highlights block by typing "/Contributor" in a post or page.
3. Block settings panel showing options to configure the Contributor Highlights block, including WordPress.org username and display preferences.
4. Front-end view of a Contributor Highlights card on a live page, showing contributor avatar, name, bio, contribution badges, and meta information styled within the site's theme.
5. Compact version, omitting bio, contributions and badge heading

== Changelog ==

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.0.0 =
Initial release