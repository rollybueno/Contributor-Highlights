<?php
/**
 * The blocks-specific functionality of the plugin.
 *
 * @link       https://opensource-media-connect.com
 * @since      1.0.0
 *
 * @package    Contributor_Highlights
 * @subpackage Contributor_Highlights/includes
 */

class Contributor_Highlights_Blocks {
    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {
        // Register block and enqueue block editor assets
        add_action('init', array($this, 'register_block'), 5);
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_block_editor_assets'));
    }

    /**
     * Register the block
     *
     * @since    1.0.0
     */
    public function register_block() {
        error_log('Contributor Highlights: Starting block registration');
        
        if (!function_exists('register_block_type')) {
            error_log('Contributor Highlights: register_block_type function does not exist');
            return;
        }

        $block_name = 'contributor-highlights/profile';
        
        // Check if block is already registered
        if (WP_Block_Type_Registry::get_instance()->is_registered($block_name)) {
            error_log('Contributor Highlights: Block is already registered');
            return;
        }

        error_log('Contributor Highlights: Registering block ' . $block_name);
        
        register_block_type($block_name, array(
            'render_callback' => array($this, 'render_block'),
            'attributes' => array(
                'username' => array(
                    'type' => 'string',
                    'default' => '',
                ),
                'showAvatar' => array(
                    'type' => 'boolean',
                    'default' => true,
                ),
                'showBio' => array(
                    'type' => 'boolean',
                    'default' => true,
                ),
                'showContributions' => array(
                    'type' => 'boolean',
                    'default' => true,
                ),
                'showBadges' => array(
                    'type' => 'boolean',
                    'default' => true,
                ),
                'showMeta' => array(
                    'type' => 'boolean',
                    'default' => true,
                ),
            ),
        ));
        
        error_log('Contributor Highlights: Block registration completed');
    }

    /**
     * Enqueue block editor assets
     *
     * @since    1.0.0
     */
    public function enqueue_block_editor_assets() {
        error_log('Contributor Highlights: Enqueueing block editor assets');
        
        $asset_file = CH_PLUGIN_DIR . 'build/index.asset.php';
        
        if (!file_exists($asset_file)) {
            error_log('Contributor Highlights: Asset file not found at ' . $asset_file);
            return;
        }
        
        $asset = require($asset_file);
        
        error_log('Contributor Highlights: Asset file loaded: ' . print_r($asset, true));
        
        wp_enqueue_script(
            'contributor-highlights-editor',
            CH_PLUGIN_URL . 'build/index.js',
            $asset['dependencies'],
            $asset['version'],
            true
        );

        wp_enqueue_style(
            'contributor-highlights-editor',
            CH_PLUGIN_URL . 'build/index.css',
            array(),
            $asset['version']
        );
        
        error_log('Contributor Highlights: Block editor assets enqueued');
    }

    /**
     * Render the block
     *
     * @since    1.0.0
     * @param    array    $attributes    The block attributes.
     * @return   string                  The rendered block.
     */
    public function render_block($attributes) {
        error_log('Contributor Highlights: Rendering block with attributes: ' . print_r($attributes, true));
        
        $shortcode_atts = array();
        
        if (!empty($attributes['username'])) {
            $shortcode_atts[] = 'username="' . esc_attr($attributes['username']) . '"';
        }
        
        $shortcode_atts[] = 'show_avatar="' . ($attributes['showAvatar'] ? 'true' : 'false') . '"';
        $shortcode_atts[] = 'show_bio="' . ($attributes['showBio'] ? 'true' : 'false') . '"';
        $shortcode_atts[] = 'show_contributions="' . ($attributes['showContributions'] ? 'true' : 'false') . '"';
        $shortcode_atts[] = 'show_badges="' . ($attributes['showBadges'] ? 'true' : 'false') . '"';
        $shortcode_atts[] = 'show_meta="' . ($attributes['showMeta'] ? 'true' : 'false') . '"';
        
        $shortcode = '[contributor_highlights ' . implode(' ', $shortcode_atts) . ']';
        
        error_log('Contributor Highlights: Generated shortcode: ' . $shortcode);
        
        return do_shortcode($shortcode);
    }
} 