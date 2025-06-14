<?php
/**
 * The plugin bootstrap file
 *
 * @link              https://github.com/rollybueno
 * @since             1.0.0
 * @package           Contributor_Highlights
 *
 * @wordpress-plugin
 * Plugin Name:       Contributor Highlights
 * Plugin URI:        https://github.com/rollybueno/contributor-highlights
 * Description:       Display WordPress.org contributor profiles on your site.
 * Version:           1.0.0
 * Author:            Rolly Bueno
 * Author URI:        https://github.com/rollybueno
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       contributor-highlights
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Define plugin constants
 *
 * @since 1.0.0
 */
define('CH_VERSION', '1.0.0');
define('CH_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CH_PLUGIN_URL', plugin_dir_url(__FILE__));

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

register_activation_hook(__FILE__, 'activate_contributor_highlights');
register_deactivation_hook(__FILE__, 'deactivate_contributor_highlights');

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
