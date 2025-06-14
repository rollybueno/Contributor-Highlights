<?php

class Contributor_Highlights_Admin {
	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, CH_PLUGIN_URL . 'admin/css/contributor-highlights-admin.css', array(), $this->version, 'all' );
	}

	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, CH_PLUGIN_URL . 'admin/js/contributor-highlights-admin.js', array( 'jquery' ), $this->version, false );
	}

	public function add_plugin_admin_menu() {
		add_options_page(
			__( 'Contributor Highlights Settings', 'contributor-highlights' ),
			__( 'Contributor Highlights', 'contributor-highlights' ),
			'manage_options',
			$this->plugin_name,
			array( $this, 'display_plugin_setup_page' )
		);
	}

	public function display_plugin_setup_page() {
		include_once CH_PLUGIN_DIR . 'admin/partials/contributor-highlights-admin-display.php';
	}
}
