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
				'editor_style'    => 'contributor-highlights-editor',
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
					'showAvatar'        => array(
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
					'showBadges'        => array(
						'type'    => 'boolean',
						'default' => true,
					),
					'showMeta'          => array(
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
		$asset_file = CH_PLUGIN_DIR . 'build/index.asset.php';

		if ( ! file_exists( $asset_file ) ) {
			return;
		}

		$asset = require $asset_file;

		wp_enqueue_script(
			'contributor-highlights-editor',
			CH_PLUGIN_URL . 'build/index.js',
			$asset['dependencies'],
			$asset['version']
		);

		// Enqueue block editor styles
		wp_enqueue_style(
			'contributor-highlights-editor',
			CH_PLUGIN_URL . 'build/index.css',
			array(),
			$asset['version']
		);

		// Enqueue frontend styles in editor
		wp_enqueue_style(
			'contributor-highlights-public',
			CH_PLUGIN_URL . 'public/css/contributor-highlights-public.css',
			array(),
			CH_VERSION
		);
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
		$shortcode = '[contributor_profile';

		if ( ! empty( $attributes['username'] ) ) {
			$shortcode .= ' username="' . esc_attr( $attributes['username'] ) . '"';
		}

		$shortcode .= ' show_avatar="' . ( $attributes['showAvatar'] ? 'yes' : 'no' ) . '"';
		$shortcode .= ' show_bio="' . ( $attributes['showBio'] ? 'yes' : 'no' ) . '"';
		$shortcode .= ' show_contributions="' . ( $attributes['showContributions'] ? 'yes' : 'no' ) . '"';
		$shortcode .= ' show_badges="' . ( $attributes['showBadges'] ? 'yes' : 'no' ) . '"';
		$shortcode .= ' show_meta="' . ( $attributes['showMeta'] ? 'yes' : 'no' ) . '"';
		$shortcode .= ']';

		$wrapper_attributes = get_block_wrapper_attributes(array(
			'class' => !empty($attributes['align']) ? 'align' . $attributes['align'] : '',
		));
		
		return sprintf(
			'<div %s>%s</div>',
			$wrapper_attributes,
			do_shortcode($shortcode)
		);
	}
}
