<?php
/**
 * The blocks-specific functionality of the plugin.
 *
 * Handles the registration and rendering of the Contributor Highlights block.
 *
 * @since      1.0.0
 * @package    Contributor_Highlights
 * @subpackage Contributor_Highlights/includes
 */

class Contributor_Highlights_Blocks {
	/**
	 * Initialize the class and set its properties.
	 *
	 * Registers the block and enqueues block editor assets.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		// Register block and enqueue block editor assets
		add_action( 'init', array( $this, 'register_block' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );
	}

	/**
	 * Register the Contributor Highlights block.
	 *
	 * Registers the block type with WordPress and sets up its attributes and supports.
	 *
	 * @since    1.0.0
	 */
	public function register_block() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		register_block_type(
			'contributor-highlights/profile',
			array(
				'editor_script'   => 'contributor-highlights-editor',
				'style'           => 'contributor-highlights-public',
				'view_script'     => 'contributor-highlights-public',
				'render_callback' => array( $this, 'render_block' ),
				'supports'        => array(
					'align' => array( 'wide', 'full' ),
					'html'  => false,
				),
				'attributes'      => array(
					'username'          => array(
						'type'    => 'string',
						'default' => '',
					),
					'compactVersion'    => array(
						'type'    => 'boolean',
						'default' => false,
					),
					'showAvatar'        => array(
						'type'    => 'boolean',
						'default' => true,
					),
					'showMeta'          => array(
						'type'    => 'boolean',
						'default' => true,
					),
					'showCurrentJob'    => array(
						'type'    => 'boolean',
						'default' => true,
					),
					'showBio'           => array(
						'type'    => 'boolean',
						'default' => true,
					),
					'showContributions' => array(
						'type'    => 'boolean',
						'default' => true,
					),
					'showTeamFocus'     => array(
						'type'    => 'boolean',
						'default' => true,
					),
					'showBadges'        => array(
						'type'    => 'boolean',
						'default' => true,
					),
					'showReleases'      => array(
						'type'    => 'boolean',
						'default' => true,
					),
					'align'             => array(
						'type'    => 'string',
						'default' => '',
					),
				),
			)
		);
	}

	/**
	 * Enqueue block editor assets.
	 *
	 * Loads the necessary JavaScript and CSS files for the block editor.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_block_editor_assets() {
		$asset_file = CONTHI_PLUGIN_DIR . 'build/index.asset.php';

		if ( ! file_exists( $asset_file ) ) {
			return;
		}

		$asset = require $asset_file;

		wp_enqueue_script(
			'contributor-highlights-editor',
			CONTHI_PLUGIN_URL . 'build/index.js',
			$asset['dependencies'],
			$asset['version'],
			true
		);

		wp_enqueue_style( 'contributor-highlights-public' );
	}

	/**
	 * Render the block content.
	 *
	 * Converts block attributes to shortcode attributes and renders the shortcode.
	 *
	 * @since    1.0.0
	 * @param    array $attributes    The block attributes.
	 * @return   string               The rendered block content.
	 */
	public function render_block( $attributes ) {
		$attributes = wp_parse_args(
			$attributes,
			array(
				'username'          => '',
				'compactVersion'    => false,
				'showAvatar'        => true,
				'showMeta'          => true,
				'showCurrentJob'    => true,
				'showBio'           => true,
				'showContributions' => true,
				'showTeamFocus'     => true,
				'showBadges'        => true,
				'showReleases'      => true,
				'align'             => '',
			)
		);

		$shortcode = '[contributor_profile';

		if ( ! empty( $attributes['username'] ) ) {
			$shortcode .= ' username="' . esc_attr( $attributes['username'] ) . '"';
		}

		$shortcode .= ' compact_version="' . $this->bool_attr( $attributes['compactVersion'] ) . '"';
		$shortcode .= ' show_avatar="' . $this->bool_attr( $attributes['showAvatar'] ) . '"';
		$shortcode .= ' show_meta="' . $this->bool_attr( $attributes['showMeta'] ) . '"';
		$shortcode .= ' show_current_job="' . $this->bool_attr( $attributes['showCurrentJob'] ) . '"';
		$shortcode .= ' show_bio="' . $this->bool_attr( $attributes['showBio'] ) . '"';
		$shortcode .= ' show_contributions="' . $this->bool_attr( $attributes['showContributions'] ) . '"';
		$shortcode .= ' show_team_focus="' . $this->bool_attr( $attributes['showTeamFocus'] ) . '"';
		$shortcode .= ' show_badges="' . $this->bool_attr( $attributes['showBadges'] ) . '"';
		$shortcode .= ' show_releases="' . $this->bool_attr( $attributes['showReleases'] ) . '"';
		$shortcode .= ']';

		$wrapper_attributes = get_block_wrapper_attributes(
			array(
				'class' => ! empty( $attributes['align'] ) ? 'align' . $attributes['align'] : '',
			)
		);

		return sprintf(
			'<div %s>%s</div>',
			$wrapper_attributes,
			do_shortcode( $shortcode )
		);
	}

	/**
	 * Convert a boolean block attribute to a shortcode-friendly string.
	 *
	 * @since    1.2.0
	 * @access   private
	 * @param    mixed $value Attribute value.
	 * @return   string       "true" or "false".
	 */
	private function bool_attr( $value ) {
		return ! empty( $value ) ? 'true' : 'false';
	}
}
