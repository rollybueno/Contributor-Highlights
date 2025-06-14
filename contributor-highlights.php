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
define( 'CH_VERSION', '1.0.0' );
define( 'CH_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CH_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 */
function activate_contributor_highlights() {
	require_once CH_PLUGIN_DIR . 'includes/class-contributor-highlights-activator.php';
	Contributor_Highlights_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_contributor_highlights() {
	require_once CH_PLUGIN_DIR . 'includes/class-contributor-highlights-deactivator.php';
	Contributor_Highlights_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_contributor_highlights' );
register_deactivation_hook( __FILE__, 'deactivate_contributor_highlights' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require CH_PLUGIN_DIR . 'includes/class-contributor-highlights.php';

/**
 * Begins execution of the plugin.
 */
function run_contributor_highlights() {
	$plugin = new Contributor_Highlights();
	$plugin->run();
}
run_contributor_highlights();
