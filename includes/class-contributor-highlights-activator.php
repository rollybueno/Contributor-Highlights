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
		global $wpdb;

		// Clear any existing transients
		// We are using dynamic transient naming, so we can't use the delete_transients() function
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM $wpdb->options WHERE option_name LIKE %s",
				$wpdb->esc_like( '_transient_conthi_' ) . '%'
			)
		);
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM $wpdb->options WHERE option_name LIKE %s",
				$wpdb->esc_like( '_transient_timeout_conthi_' ) . '%'
			)
		);
	}
}
