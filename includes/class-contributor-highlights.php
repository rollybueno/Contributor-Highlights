<?php

class Contributor_Highlights {
	protected $loader;
	protected $plugin_name;
	protected $version;

	public function __construct() {
		$this->version     = CH_VERSION;
		$this->plugin_name = 'contributor-highlights';

		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	private function load_dependencies() {
		require_once CH_PLUGIN_DIR . 'includes/class-contributor-highlights-loader.php';
		require_once CH_PLUGIN_DIR . 'admin/class-contributor-highlights-admin.php';
		require_once CH_PLUGIN_DIR . 'public/class-contributor-highlights-public.php';

		$this->loader = new Contributor_Highlights_Loader();
	}

	private function define_admin_hooks() {
		$plugin_admin = new Contributor_Highlights_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_admin_menu' );
	}

	private function define_public_hooks() {
		$plugin_public = new Contributor_Highlights_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_shortcode( 'contributor_profile', $plugin_public, 'display_contributor_profile' );
	}

	public function run() {
		$this->loader->run();
	}

	public function get_plugin_name() {
		return $this->plugin_name;
	}

	public function get_version() {
		return $this->version;
	}
}
