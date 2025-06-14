<?php
/**
 * Fired during plugin activation.
 *
 * @since      1.0.0
 * @package    Contributor_Highlights
 * @subpackage Contributor_Highlights/includes
 */
class Contributor_Highlights_Activator {
    /**
     * Activate the plugin.
     *
     * @since    1.0.0
     */
    public static function activate() {
        // Clear any existing transients
        global $wpdb;
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM $wpdb->options WHERE option_name LIKE %s",
                $wpdb->esc_like('_transient_ch_') . '%'
            )
        );
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM $wpdb->options WHERE option_name LIKE %s",
                $wpdb->esc_like('_transient_timeout_ch_') . '%'
            )
        );
    }
} 