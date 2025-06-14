<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @since      1.0.0
 * @package    Contributor_Highlights
 * @subpackage Contributor_Highlights/public
 */
class Contributor_Highlights_Public {
	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string $plugin_name       The name of the plugin.
	 * @param    string $version           The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, CH_PLUGIN_URL . 'public/css/contributor-highlights-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, CH_PLUGIN_URL . 'public/js/contributor-highlights-public.js', array( 'jquery' ), $this->version, false );
	}

	/**
	 * Display the contributor profile using a shortcode.
	 *
	 * @since    1.0.0
	 * @param    array $atts    Shortcode attributes.
	 * @return   string            The HTML output of the contributor profile.
	 */
	public function display_contributor_profile( $atts ) {
		$atts = shortcode_atts(
			array(
				'username'           => '',
				'show_avatar'        => 'yes',
				'show_bio'           => 'yes',
				'show_contributions' => 'yes',
				'show_badges'        => 'yes',
				'show_meta'          => 'yes',
			),
			$atts,
			'contributor_profile'
		);

		if ( empty( $atts['username'] ) ) {
			return '<p>' . __( 'Please provide a WordPress.org username.', 'contributor-highlights' ) . '</p>';
		}

		$profile_data = $this->get_profile_data( $atts['username'] );

		if ( is_wp_error( $profile_data ) ) {
			return '<p>' . $profile_data->get_error_message() . '</p>';
		}

		ob_start();
		?>
		<div class="contributor-profile">
			<div class="contributor-header">
				<?php if ( $atts['show_avatar'] === 'yes' && ! empty( $profile_data['avatar'] ) ) : ?>
					<div class="contributor-avatar">
						<img src="<?php echo esc_url( str_replace( 's=100', 's=150', $profile_data['avatar'] ) ); ?>" alt="<?php echo esc_attr( $profile_data['name'] ); ?>">
					</div>
				<?php endif; ?>

				<div class="contributor-info">
					<h2 class="contributor-name"><?php esc_html_e( $profile_data['name'], 'contributor-highlights' ); ?></h2>

					<?php if ( $atts['show_meta'] === 'yes' && ! empty( $profile_data['user_meta'] ) ) : ?>
						<div class="contributor-meta">
							<?php if ( ! empty( $profile_data['user_meta']['job'] ) ) : ?>
								<div class="meta-item">
									<span class="dashicons dashicons-businessman"></span>
									<?php esc_html_e( $profile_data['user_meta']['job'], 'contributor-highlights' ); ?>
									<?php if ( ! empty( $profile_data['user_meta']['company'] ) ) : ?>
										at <?php esc_html_e( $profile_data['user_meta']['company'], 'contributor-highlights' ); ?>
									<?php endif; ?>
								</div>
							<?php endif; ?>

							<?php if ( ! empty( $profile_data['user_meta']['location'] ) ) : ?>
								<div class="meta-item">
									<span class="dashicons dashicons-location"></span>
									<?php esc_html_e( $profile_data['user_meta']['location'], 'contributor-highlights' ); ?>
								</div>
							<?php endif; ?>

							<?php if ( ! empty( $profile_data['user_meta']['website'] ) ) : ?>
								<div class="meta-item">
									<span class="dashicons dashicons-admin-site"></span>
									<a href="<?php echo esc_url( $profile_data['user_meta']['website']['url'] ); ?>" target="_blank">
										<?php esc_html_e( $profile_data['user_meta']['website']['text'], 'contributor-highlights' ); ?>
									</a>
								</div>
							<?php endif; ?>

							<?php if ( ! empty( $profile_data['user_meta']['github'] ) ) : ?>
								<div class="meta-item">
									<span class="dashicons dashicons-editor-code"></span>
									<a href="<?php echo esc_url( $profile_data['user_meta']['github']['url'] ); ?>" target="_blank">
										<?php esc_html_e( $profile_data['user_meta']['github']['text'], 'contributor-highlights' ); ?>
									</a>
								</div>
							<?php endif; ?>

							<?php if ( ! empty( $profile_data['user_meta']['member-since'] ) ) : ?>
								<div class="meta-item">
									<span class="dashicons dashicons-calendar-alt"></span>
									Member since <?php esc_html_e( $profile_data['user_meta']['member-since'], 'contributor-highlights' ); ?>
								</div>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>

			<?php if ( $atts['show_bio'] === 'yes' && ! empty( $profile_data['bio'] ) ) : ?>
				<div class="contributor-bio">
					<?php echo wp_kses_post( $profile_data['bio'] ); ?>
				</div>	
			<?php endif; ?>

			<?php if ( $atts['show_contributions'] === 'yes' && ! empty( $profile_data['contributions'] ) ) : ?>
				<h3><?php esc_html_e( 'Contributions', 'contributor-highlights' ); ?></h3>
				<div class="contributor-contributions">
					<?php echo wp_kses_post( $profile_data['contributions'] ); ?>
				</div>
			<?php endif; ?>

			<?php if ( $atts['show_badges'] === 'yes' && ! empty( $profile_data['badges'] ) ) : ?>
				<div class="contributor-badges">
					<h3><?php esc_html_e( 'Badges & Achievements', 'contributor-highlights' ); ?></h3>
					<div class="badges-grid">
						<?php foreach ( $profile_data['badges'] as $badge ) : ?>
							<div class="badge-item">
								<span class="dashicons <?php esc_html_e( implode( ' ', $badge['class'] ) ); ?>"></span>
								<span class="badge-name"><?php esc_html_e( $badge['name'] ); ?></span>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Fetch data from WordPress.org profile page.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    string $username    The WordPress.org username.
	 * @return   string|WP_Error       The HTML content of the profile page or WP_Error on failure.
	 */
	private function get_wp_data( $username ) {
		$transient_key = 'ch_wp_data_' . sanitize_title( $username );
		$profile_data  = get_transient( $transient_key );

		if ( false === $profile_data ) {
			$response = wp_remote_get( 'https://profiles.wordpress.org/' . $username . '/' );

			if ( is_wp_error( $response ) ) {
				return $response;
			}

			$profile_data = wp_remote_retrieve_body( $response );

			if ( empty( $profile_data ) ) {
				return new WP_Error( 'empty_response', __( 'No data received from WordPress.org', 'contributor-highlights' ) );
			}

			if ( is_wp_error( $profile_data ) ) {
				return $profile_data;
			}

			// Cache the data for 6 hour
			set_transient( $transient_key, $profile_data, 6 * HOUR_IN_SECONDS );
		}
		return $profile_data;
	}

	/**
	 * Get the parsed profile data for a WordPress.org user.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    string $username    The WordPress.org username.
	 * @return   array                 The parsed profile data.
	 */
	private function get_profile_data( $username ) {
		$transient_key = 'ch_profile_data_' . sanitize_title( $username );
		$profile_data  = get_transient( $transient_key );

		if ( false === $profile_data ) {
			$get_data     = $this->get_wp_data( $username );
			$profile_data = $this->parse_profile_html( $get_data );
			set_transient( $transient_key, $profile_data, 6 * HOUR_IN_SECONDS );
		}

		return $profile_data;
	}

	/**
	 * Parse the HTML content from WordPress.org profile page.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    string $html    The HTML content to parse.
	 * @return   array             The parsed profile data.
	 */
	private function parse_profile_html( $html ) {
		// Create a DOMDocument object
		$dom = new DOMDocument();

		// Suppress warnings from malformed HTML
		libxml_use_internal_errors( true );
		$dom->loadHTML( $html );
		libxml_clear_errors();

		$xpath = new DOMXPath( $dom );

		// Extract profile data
		$profile_data = array(
			'name'          => '',
			'avatar'        => '',
			'bio'           => '',
			'slack'         => '',
			'contributions' => '',
			'badges'        => array(),
			'user_meta'     => array(),
		);

		// Get name
		$name_nodes = $xpath->query( '//header[contains(@class, "site-header")]//h2/a' );
		if ( $name_nodes->length > 0 ) {
			$profile_data['name'] = trim( $name_nodes->item( 0 )->textContent );
		}

		// Get avatar
		$avatar_nodes = $xpath->query( '//header[contains(@class, "site-header")]//img[contains(@class, "avatar")]' );
		if ( $avatar_nodes->length > 0 ) {
			$profile_data['avatar'] = $avatar_nodes->item( 0 )->getAttribute( 'src' );
		}

		// Get Slack
		$slack_node = $xpath->query( '//p[@id="slack-username"]//span[contains(@class, "username")]' );
		if ( $slack_node->length > 0 ) {
			$profile_data['slack'] = trim( $slack_node->item( 0 )->textContent );
		}

		// Get bio
		$bio_nodes = $xpath->query( '//div[@id="content-about"]/div[@class="item-meta-about"]/p' );
		if ( $bio_nodes->length > 0 ) {
			$profile_data['bio'] = trim( $dom->saveHTML( $bio_nodes->item( 0 ) ) );
		}

		// Get contributions
		$contribution_nodes = $xpath->query( '//div[@id="content-about"]/div[@class="item-meta-contribution"]/p' );
		if ( $contribution_nodes->length > 0 ) {
			$profile_data['contributions'] = trim( $dom->saveHTML( $contribution_nodes->item( 0 ) ) );
		}

		// Get user meta
		$user_meta_nodes = $xpath->query( '//ul[@id="user-meta"]/li[not(@id="user-social-media-accounts-tag")]' );
		if ( $user_meta_nodes->length > 0 ) {
			foreach ( $user_meta_nodes as $li ) {
				$key = $li->getAttribute( 'id' ); // e.g., user-location
				$key = preg_replace( '/^user-/', '', $key ); // Remove "user-" prefix

				$strong = $li->getElementsByTagName( 'strong' )->item( 0 );
				$aTag   = $strong ? $strong->getElementsByTagName( 'a' )->item( 0 ) : null;

				if ( $aTag ) {
					$profile_data['user_meta'][ $key ] = array(
						'text' => trim( $aTag->textContent ),
						'url'  => trim( $aTag->getAttribute( 'href' ) ),
					);
				} else {
					$profile_data['user_meta'][ $key ] = $strong ? trim( $strong->textContent ) : '';
				}
			}
		}

		// Get badges
		foreach ( $xpath->query( '//ul[@id="user-badges"]/li' ) as $li ) {
			$badge_name = trim( $li->textContent );
			$badge_icon = '';
			$classes    = '';
			$badge_div  = $xpath->query( './/div[contains(@class, "badge")]', $li )->item( 0 );
			if ( $badge_div && $badge_div->hasAttributes() ) {
				$classes = explode( ' ', $badge_div->getAttribute( 'class' ) );
				foreach ( $classes as $class ) {
					if ( strpos( $class, 'dashicons-' ) !== false ) {
						$badge_icon = $class;
						break;
					}
				}
			}
			$profile_data['badges'][] = array(
				'name'  => $badge_name,
				'icon'  => $badge_icon,
				'class' => $classes,
			);
		}

		// If we couldn't find the data, try alternative selectors
		if ( empty( $profile_data['name'] ) ) {
			$name_nodes = $xpath->query( '//h1[contains(@class, "profile-name")]' );
			if ( $name_nodes->length > 0 ) {
				$profile_data['name'] = trim( $name_nodes->item( 0 )->textContent );
			}
		}

		if ( empty( $profile_data['avatar'] ) ) {
			$avatar_nodes = $xpath->query( '//img[contains(@class, "avatar")]' );
			if ( $avatar_nodes->length > 0 ) {
				$profile_data['avatar'] = $avatar_nodes->item( 0 )->getAttribute( 'src' );
			}
		}

		return $profile_data;
	}
}
