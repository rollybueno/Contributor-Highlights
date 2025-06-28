<?php
/**
 * Plugin Name:       Contributor Highlights
 * Plugin URI:        https://www.rollybueno.com/plugins/contributor-highlights/
 * Description:       Showcase your WordPress.org contributions in style. This plugin pulls your public profile data—such as bio, contributions, and badges, and displays it beautifully on your site.
 * Version:           1.0.0
 * Author:            Rolly Bueno
 * Author URI:        https://rollybueno.com
 * Requires at least: 5.0
 * Requires PHP:      7.4
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       contributor-highlights
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define plugin constants
 *
 * @since 1.0.0
 */
define( 'CONTHI_VERSION', '1.0.0' );
define( 'CONTHI_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CONTHI_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 */
function conthi_activate() {
	require_once CONTHI_PLUGIN_DIR . 'includes/class-contributor-highlights-activator.php';
	Contributor_Highlights_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function conthi_deactivate() {
	require_once CONTHI_PLUGIN_DIR . 'includes/class-contributor-highlights-deactivator.php';
	Contributor_Highlights_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'conthi_activate' );
register_deactivation_hook( __FILE__, 'conthi_deactivate' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require CONTHI_PLUGIN_DIR . 'includes/class-contributor-highlights.php';

/**
 * Begins execution of the plugin.
 */
function conthi_run() {
	$plugin = new Contributor_Highlights();
	$plugin->run();
}
conthi_run();
