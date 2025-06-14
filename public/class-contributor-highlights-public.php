<?php

class Contributor_Highlights_Public {
	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, CH_PLUGIN_URL . 'public/css/contributor-highlights-public.css', array(), $this->version, 'all' );
	}

	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, CH_PLUGIN_URL . 'public/js/contributor-highlights-public.js', array( 'jquery' ), $this->version, false );
	}

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
						<img src="<?php echo esc_url( $profile_data['avatar'] ); ?>" alt="<?php echo esc_attr( $profile_data['name'] ); ?>">
					</div>
				<?php endif; ?>

				<div class="contributor-info">
					<h2 class="contributor-name"><?php echo esc_html( $profile_data['name'] ); ?></h2>
					
					<?php if ( ! empty( $profile_data['slack'] ) ) : ?>
						<div class="contributor-slack">
							<span class="dashicons dashicons-slack"></span>
							<?php echo esc_html( $profile_data['slack'] ); ?>
						</div>
					<?php endif; ?>

					<?php if ( $atts['show_meta'] === 'yes' && ! empty( $profile_data['user_meta'] ) ) : ?>
						<div class="contributor-meta">
							<?php if ( ! empty( $profile_data['user_meta']['job'] ) ) : ?>
								<div class="meta-item">
									<span class="dashicons dashicons-businessman"></span>
									<?php echo esc_html( $profile_data['user_meta']['job'] ); ?>
									<?php if ( ! empty( $profile_data['user_meta']['company'] ) ) : ?>
										at <?php echo esc_html( $profile_data['user_meta']['company'] ); ?>
									<?php endif; ?>
								</div>
							<?php endif; ?>

							<?php if ( ! empty( $profile_data['user_meta']['location'] ) ) : ?>
								<div class="meta-item">
									<span class="dashicons dashicons-location"></span>
									<?php echo esc_html( $profile_data['user_meta']['location'] ); ?>
								</div>
							<?php endif; ?>

							<?php if ( ! empty( $profile_data['user_meta']['website'] ) ) : ?>
								<div class="meta-item">
									<span class="dashicons dashicons-admin-site"></span>
									<a href="<?php echo esc_url( $profile_data['user_meta']['website']['url'] ); ?>" target="_blank">
										<?php echo esc_html( $profile_data['user_meta']['website']['text'] ); ?>
									</a>
								</div>
							<?php endif; ?>

							<?php if ( ! empty( $profile_data['user_meta']['github'] ) ) : ?>
								<div class="meta-item">
									<span class="dashicons dashicons-editor-code"></span>
									<a href="<?php echo esc_url( $profile_data['user_meta']['github']['url'] ); ?>" target="_blank">
										<?php echo esc_html( $profile_data['user_meta']['github']['text'] ); ?>
									</a>
								</div>
							<?php endif; ?>

							<?php if ( ! empty( $profile_data['user_meta']['member-since'] ) ) : ?>
								<div class="meta-item">
									<span class="dashicons dashicons-calendar-alt"></span>
									Member since <?php echo esc_html( $profile_data['user_meta']['member-since'] ); ?>
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
				<div class="contributor-contributions">
					<?php echo wp_kses_post( $profile_data['contributions'] ); ?>
				</div>
			<?php endif; ?>

			<?php if ( $atts['show_badges'] === 'yes' && ! empty( $profile_data['badges'] ) ) : ?>
				<div class="contributor-badges">
					<h3><?php _e( 'Badges & Achievements', 'contributor-highlights' ); ?></h3>
					<div class="badges-grid">
						<?php foreach ( $profile_data['badges'] as $badge ) : ?>
							<div class="badge-item">
								<?php if ( ! empty( $badge['icon'] ) ) : ?>
									<span class="dashicons <?php echo esc_attr( $badge['icon'] ); ?>"></span>
								<?php endif; ?>
								<span class="badge-name"><?php echo esc_html( $badge['name'] ); ?></span>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}

	private function get_wp_data( $username ) {
		$transient_key = 'ch_wp_data_' . sanitize_title( $username );
		$profile_data  = get_transient( $transient_key );

		if ( false === $profile_data ) {
			$response = wp_remote_get( 'https://profiles.wordpress.org/' . $username . '/' );

			if ( is_wp_error( $response ) ) {
				error_log( 'Contributor Highlights - Error fetching profile: ' . $response->get_error_message() );
				return $response;
			}

			$profile_data = wp_remote_retrieve_body( $response );

			if ( empty( $profile_data ) ) {
				error_log( 'Contributor Highlights - Empty response from WordPress.org' );
				return new WP_Error( 'empty_response', __( 'No data received from WordPress.org', 'contributor-highlights' ) );
			}

			if ( is_wp_error( $profile_data ) ) {
				return $profile_data->get_error_message();
			}

			// Cache the data for 6 hour
			set_transient( $transient_key, $profile_data, 6 * HOUR_IN_SECONDS );
		}
		return $profile_data;
	}

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
			$profile_data['bio'] = trim( $bio_nodes->item( 0 )->textContent );
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
				'name' => $badge_name,
				'icon' => $badge_icon,
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
