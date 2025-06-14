<?php
/**
 * The blocks functionality of the plugin.
 *
 * @since      1.0.0
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
        add_action('init', array($this, 'register_block'));
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_block_editor_assets'));
    }

    /**
     * Register the block.
     *
     * @since    1.0.0
     */
    public function register_block() {
        if (!function_exists('register_block_type')) {
            return;
        }

        register_block_type(
            'contributor-highlights/profile',
            array(
                'editor_script' => 'contributor-highlights-blocks',
                'editor_style'  => 'contributor-highlights-blocks-editor',
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
            )
        );
    }

    /**
     * Enqueue block editor assets.
     *
     * @since    1.0.0
     */
    public function enqueue_block_editor_assets() {
        wp_enqueue_script(
            'contributor-highlights-blocks',
            CH_PLUGIN_URL . 'build/index.js',
            array('wp-blocks', 'wp-i18n', 'wp-element', 'wp-block-editor', 'wp-components'),
            CH_VERSION,
            true
        );

        wp_enqueue_style(
            'contributor-highlights-blocks-editor',
            CH_PLUGIN_URL . 'build/index.css',
            array(),
            CH_VERSION
        );
    }

    /**
     * Render the block content.
     *
     * @since    1.0.0
     * @param    array    $attributes    The block attributes.
     * @return   string                  The rendered block content.
     */
    public function render_block($attributes) {
        $shortcode_atts = array(
            'username' => $attributes['username'],
            'show_avatar' => $attributes['showAvatar'] ? 'yes' : 'no',
            'show_bio' => $attributes['showBio'] ? 'yes' : 'no',
            'show_contributions' => $attributes['showContributions'] ? 'yes' : 'no',
            'show_badges' => $attributes['showBadges'] ? 'yes' : 'no',
            'show_meta' => $attributes['showMeta'] ? 'yes' : 'no',
        );

        return do_shortcode('[contributor_profile ' . $this->build_shortcode_attributes($shortcode_atts) . ']');
    }

    /**
     * Build shortcode attributes string.
     *
     * @since    1.0.0
     * @param    array    $attributes    The attributes to convert.
     * @return   string                  The attributes string.
     */
    private function build_shortcode_attributes($attributes) {
        $atts = array();
        foreach ($attributes as $key => $value) {
            $atts[] = $key . '="' . esc_attr($value) . '"';
        }
        return implode(' ', $atts);
    }
} 