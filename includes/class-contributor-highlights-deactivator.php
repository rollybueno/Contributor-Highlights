<?php
/**
 * Fired during plugin deactivation.
 *
 * @since      1.0.0
 * @package    Contributor_Highlights
 * @subpackage Contributor_Highlights/includes
 */
class Contributor_Highlights_Deactivator {
	/**
	 * Deactivate the plugin.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		global $wpdb;
		// Clear any existing transients
		// We are using dynamic transient naming, so we can't use the delete_transients() function
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching		
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM $wpdb->options WHERE option_name LIKE %s",
				$wpdb->esc_like( '_transient_ch_' ) . '%'
			)
		);
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching	
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM $wpdb->options WHERE option_name LIKE %s",
				$wpdb->esc_like( '_transient_timeout_ch_' ) . '%'
			)
		);
	}
}
