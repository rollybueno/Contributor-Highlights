<?php
/**
 * Plugin Name: Contributor Highlights
 * Plugin URI: https://github.com/rollybueno/contributor-highlights
 * Description: A plugin that pulls and displays information from WordPress.org profiles.
 * Version: 1.0.0
 * Author: Rolly Bueno
 * Author URI: https://profiles.wordpress.org/rollybueno/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: contributor-highlights
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('CH_VERSION', '1.0.0');
define('CH_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CH_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once CH_PLUGIN_DIR . 'includes/class-contributor-highlights.php';

// Initialize the plugin
function run_contributor_highlights() {
    $plugin = new Contributor_Highlights();
    $plugin->run();
}
run_contributor_highlights(); 