<?php

class Contributor_Highlights_Public {
    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function enqueue_styles() {
        wp_enqueue_style($this->plugin_name, CH_PLUGIN_URL . 'public/css/contributor-highlights-public.css', array(), $this->version, 'all');
    }

    public function enqueue_scripts() {
        wp_enqueue_script($this->plugin_name, CH_PLUGIN_URL . 'public/js/contributor-highlights-public.js', array('jquery'), $this->version, false);
    }

    public function display_contributor_profile($atts) {
        $atts = shortcode_atts(array(
            'username' => '',
            'show_avatar' => 'yes',
            'show_bio' => 'yes',
            'show_contributions' => 'yes'
        ), $atts, 'contributor_profile');

        if (empty($atts['username'])) {
            return '<p>' . __('Please provide a WordPress.org username.', 'contributor-highlights') . '</p>';
        }

        $profile_data = $this->get_profile_data($atts['username']);
        
        if (is_wp_error($profile_data)) {
            return '<p>' . $profile_data->get_error_message() . '</p>';
        }

        ob_start();
        ?>
        <div class="contributor-profile">
            <?php if ($atts['show_avatar'] === 'yes' && !empty($profile_data['avatar'])) : ?>
                <div class="contributor-avatar">
                    <img src="<?php echo esc_url($profile_data['avatar']); ?>" alt="<?php echo esc_attr($profile_data['name']); ?>">
                </div>
            <?php endif; ?>

            <div class="contributor-info">
                <h2 class="contributor-name"><?php echo esc_html($profile_data['name']); ?></h2>
                
                <?php if ($atts['show_bio'] === 'yes' && !empty($profile_data['bio'])) : ?>
                    <div class="contributor-bio">
                        <?php echo wp_kses_post($profile_data['bio']); ?>
                    </div>
                <?php endif; ?>

                <?php if ($atts['show_contributions'] === 'yes' && !empty($profile_data['contributions'])) : ?>
                    <div class="contributor-contributions">
                        <h3><?php _e('Contributions', 'contributor-highlights'); ?></h3>
                        <ul>
                            <?php foreach ($profile_data['contributions'] as $contribution) : ?>
                                <li><?php echo esc_html($contribution); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    private function get_profile_data($username) {
        $transient_key = 'ch_profile_' . sanitize_title($username);
        $profile_data = get_transient($transient_key);

        if (false === $profile_data) {
            $response = wp_remote_get('https://profiles.wordpress.org/' . $username . '/');

            if (is_wp_error($response)) {
                return $response;
            }

            $body = wp_remote_retrieve_body($response);
            
            if (empty($body)) {
                return new WP_Error('empty_response', __('No data received from WordPress.org', 'contributor-highlights'));
            }

            // Parse the HTML response
            $profile_data = $this->parse_profile_html($body);
            
            if (is_wp_error($profile_data)) {
                return $profile_data;
            }

            // Cache the data for 1 hour
            set_transient($transient_key, $profile_data, HOUR_IN_SECONDS);
        }

        return $profile_data;
    }

    private function parse_profile_html($html) {
        // Create a DOMDocument object
        $dom = new DOMDocument();
        
        // Suppress warnings from malformed HTML
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);

        // Extract profile data
        $profile_data = array(
            'name' => '',
            'avatar' => '',
            'bio' => '',
            'contributions' => array()
        );

        // Get name
        $name_nodes = $xpath->query('//h1[@class="profile-name"]');
        if ($name_nodes->length > 0) {
            $profile_data['name'] = trim($name_nodes->item(0)->textContent);
        }

        // Get avatar
        $avatar_nodes = $xpath->query('//img[@class="avatar"]');
        if ($avatar_nodes->length > 0) {
            $profile_data['avatar'] = $avatar_nodes->item(0)->getAttribute('src');
        }

        // Get bio
        $bio_nodes = $xpath->query('//div[@class="profile-bio"]');
        if ($bio_nodes->length > 0) {
            $profile_data['bio'] = trim($bio_nodes->item(0)->textContent);
        }

        // Get contributions
        $contribution_nodes = $xpath->query('//div[@class="contribution-history"]//li');
        foreach ($contribution_nodes as $node) {
            $profile_data['contributions'][] = trim($node->textContent);
        }

        return $profile_data;
    }
} 