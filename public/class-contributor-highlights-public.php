<?php
/**
 * The public-facing functionality of the plugin.
 *
 * Handles the display of contributor profiles and manages public-facing assets.
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
		wp_enqueue_style( $this->plugin_name, CONTHI_PLUGIN_URL . 'public/css/contributor-highlights-public.css', array('dashicons'), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, CONTHI_PLUGIN_URL . 'public/js/contributor-highlights-public.js', array( 'jquery' ), $this->version, false );
	}

	/**
	 * Display the contributor profile using a shortcode.
	 *
	 * Renders the contributor profile based on the provided shortcode attributes.
	 *
	 * @since    1.0.0
	 * @param    array $atts    Shortcode attributes.
	 * @return   string         The HTML output of the contributor profile.
	 */
	public function display_contributor_profile( $atts ) {
		$atts = shortcode_atts(
			array(
				'username'           => '',
				'compact_version'    => false,
				'show_avatar'        => true,
				'show_bio'           => true,
				'show_contributions' => true,
				'show_badges'        => true,
				'show_meta'          => true,
			),
			$atts,
			'contributor_profile'
		);

		if ( empty( $atts['username'] ) ) {
			return '<p>' . __( 'Please provide a WordPress.org username.', 'contributor-highlights' ) . '</p>';
		}

		// If compact version is true,
		// only show meta and badges, and hide the name
		if ( $atts['compact_version'] ) {
			$atts['show_bio']           = false;
			$atts['show_contributions'] = false;
			$atts['show_avatar']        = true;
			$atts['show_meta']          = true;
			$atts['show_badges']        = true;
		}

		$profile_data = $this->get_profile_data( $atts['username'] );

		if ( is_wp_error( $profile_data ) ) {
			return '<p>' . $profile_data->get_error_message() . '</p>';
		}

		$header      = isset( $profile_data['header'] ) ? $profile_data['header'] : array();
		$current_job = isset( $profile_data['current_job'] ) ? $profile_data['current_job'] : array();
		$has_header_meta = ! empty( $header['location'] )
			|| ! empty( $header['joined'] )
			|| ! empty( $header['links'] )
			|| ! empty( $header['teams'] )
			|| ! empty( $header['languages'] );

		ob_start();
		?>
		<div class="contributor-profile">
			<div class="contributor-header">
				<?php if ( $atts['show_avatar'] && ! empty( $profile_data['avatar'] ) ) : ?>
					<div class="contributor-avatar">
						<?php if ( ! empty( $profile_data['profile_url'] ) ) : ?>
							<a href="<?php echo esc_url( $profile_data['profile_url'] ); ?>" target="_blank" rel="noopener noreferrer">
						<?php endif; ?>
						<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
						<img src="<?php echo esc_url( $this->normalize_avatar_url( $profile_data['avatar'] ) ); ?>" alt="<?php echo esc_attr( $profile_data['name'] ); ?>">
						<?php if ( ! empty( $profile_data['profile_url'] ) ) : ?>
							</a>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<div class="contributor-info">
					<?php if ( ! empty( $profile_data['name'] ) ) : ?>
						<h2 class="contributor-name">
							<?php if ( ! empty( $profile_data['profile_url'] ) ) : ?>
								<a href="<?php echo esc_url( $profile_data['profile_url'] ); ?>" target="_blank" rel="noopener noreferrer">
									<?php echo esc_html( $profile_data['name'] ); ?>
								</a>
							<?php else : ?>
								<?php echo esc_html( $profile_data['name'] ); ?>
							<?php endif; ?>
						</h2>
					<?php endif; ?>

					<?php if ( $atts['compact_version'] && $atts['show_meta'] && ! empty( $current_job['role'] ) ) : ?>
						<p class="contributor-current-job-line">
							<?php echo esc_html( $current_job['role'] ); ?>
							<?php if ( ! empty( $current_job['company'] ) ) : ?>
								<?php esc_html_e( 'at', 'contributor-highlights' ); ?>
								<?php echo esc_html( $current_job['company'] ); ?>
							<?php endif; ?>
						</p>
					<?php endif; ?>

					<?php
					$handle_parts = array();
					if ( ! empty( $profile_data['username'] ) ) {
						$handle_parts[] = array(
							'class' => 'contributor-handle',
							'html'  => ! empty( $profile_data['profile_url'] )
								? '<a href="' . esc_url( $profile_data['profile_url'] ) . '" target="_blank" rel="noopener noreferrer">@' . esc_html( $profile_data['username'] ) . '</a>'
								: '@' . esc_html( $profile_data['username'] ),
						);
					}
					if ( $atts['show_meta'] && ! empty( $header['location'] ) ) {
						$handle_parts[] = array(
							'class' => 'contributor-handle contributor-location',
							'html'  => esc_html( $header['location'] ),
						);
					}
					if ( $atts['show_meta'] && ! empty( $header['joined'] ) ) {
						$handle_parts[] = array(
							'class' => 'contributor-handle contributor-joined',
							'html'  => esc_html( $header['joined'] ),
						);
					}
					?>
					<?php if ( ! empty( $handle_parts ) ) : ?>
						<p class="contributor-handles">
							<?php foreach ( $handle_parts as $index => $handle_part ) : ?>
								<?php if ( $index > 0 ) : ?>
									<span class="contributor-handle-sep" aria-hidden="true">·</span>
								<?php endif; ?>
								<span class="<?php echo esc_attr( $handle_part['class'] ); ?>"><?php echo $handle_part['html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<?php endforeach; ?>
						</p>
					<?php endif; ?>

					<?php if ( $atts['show_meta'] && ! empty( $header['links'] ) ) : ?>
						<div class="contributor-links">
							<?php foreach ( $header['links'] as $link ) : ?>
								<a href="<?php echo esc_url( $link['url'] ); ?>" target="_blank" rel="noopener noreferrer">
									<?php echo esc_html( $link['text'] ); ?>
								</a>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					<?php if ( $atts['show_meta'] && ! empty( $header['teams'] ) ) : ?>
						<div class="contributor-chip-row">
							<span class="contributor-chip-label"><?php esc_html_e( 'Teams', 'contributor-highlights' ); ?></span>
							<?php foreach ( $header['teams'] as $team ) : ?>
								<span class="contributor-chip"><?php echo esc_html( $team ); ?></span>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					<?php if ( $atts['show_meta'] && ! empty( $header['languages'] ) ) : ?>
						<div class="contributor-chip-row">
							<span class="contributor-chip-label"><?php esc_html_e( 'Languages', 'contributor-highlights' ); ?></span>
							<?php foreach ( $header['languages'] as $language ) : ?>
								<span class="contributor-chip"><?php echo esc_html( $language ); ?></span>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					<?php if ( $atts['show_meta'] && ! $has_header_meta && ! empty( $profile_data['user_meta'] ) ) : ?>
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

			<?php if ( ! $atts['compact_version'] && $atts['show_meta'] && ! empty( $current_job['role'] ) ) : ?>
				<section class="contributor-current-job">
					<h3 class="contributor-current-job-title"><?php esc_html_e( 'Current Job', 'contributor-highlights' ); ?></h3>
					<div class="contributor-current-job-card">
						<div class="contributor-current-job-head">
							<span class="contributor-current-job-role"><?php echo esc_html( $current_job['role'] ); ?></span>
							<?php if ( ! empty( $current_job['is_present'] ) ) : ?>
								<span class="contributor-current-job-badge"><?php esc_html_e( 'Present', 'contributor-highlights' ); ?></span>
							<?php elseif ( ! empty( $current_job['dates'] ) ) : ?>
								<span class="contributor-current-job-dates"><?php echo esc_html( $current_job['dates'] ); ?></span>
							<?php endif; ?>
						</div>
						<?php if ( ! empty( $current_job['company'] ) ) : ?>
							<p class="contributor-current-job-company"><?php echo esc_html( $current_job['company'] ); ?></p>
						<?php endif; ?>
					</div>
				</section>
			<?php endif; ?>

			<?php if ( $atts['show_bio'] && ! empty( $profile_data['bio'] ) ) : ?>
				<div class="contributor-bio">
					<?php echo wp_kses_post( $profile_data['bio'] ); ?>
				</div>	
			<?php endif; ?>

			<?php if ( $atts['show_contributions'] && ! empty( $profile_data['contributions'] ) ) : ?>
				<h3><?php esc_html_e( 'Contributions', 'contributor-highlights' ); ?></h3>
				<div class="contributor-contributions">
					<?php echo wp_kses_post( $profile_data['contributions'] ); ?>
				</div>
			<?php endif; ?>

			<?php if ( $atts['show_badges'] && ! empty( $profile_data['badges'] ) ) : ?>
				<div class="contributor-badges">
					<?php if ( ! $atts['compact_version'] ) : ?>
						<h3><?php esc_html_e( 'Badges & Achievements', 'contributor-highlights' ); ?></h3>
					<?php endif; ?>
					<div class="badges-grid">
						<?php foreach ( $profile_data['badges'] as $badge ) : ?>
							<div class="badge-item">
								<span class="dashicons <?php echo esc_html( implode( ' ', $badge['class'] ) ); ?>"></span>
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

	/**
	 * Fetch data from WordPress.org profile page.
	 *
	 * Retrieves and caches the HTML content of a WordPress.org profile page.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    string $username    The WordPress.org username.
	 * @return   string|WP_Error     The HTML content of the profile page or WP_Error on failure.
	 */
	private function get_wp_data( $username ) {
		$transient_key = 'conthi_wp_data_' . sanitize_title( $username );
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

			// Cache the data for 6 hours
			set_transient( $transient_key, $profile_data, 6 * HOUR_IN_SECONDS );
		}
		return $profile_data;
	}

	/**
	 * Get the parsed profile data for a WordPress.org user.
	 *
	 * Retrieves and caches the parsed profile data for a WordPress.org user.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    string $username    The WordPress.org username.
	 * @return   array              The parsed profile data.
	 */
	private function get_profile_data( $username ) {
		$transient_key = 'conthi_profile_data_v4_' . sanitize_title( $username );
		$profile_data  = get_transient( $transient_key );

		if ( false === $profile_data ) {
			$get_data = $this->get_wp_data( $username );

			if ( is_wp_error( $get_data ) ) {
				return $get_data;
			}

			$profile_data = $this->parse_profile_html( $get_data, $username );
			set_transient( $transient_key, $profile_data, 6 * HOUR_IN_SECONDS );
		}

		return $profile_data;
	}

	/**
	 * Parse the HTML content from WordPress.org profile page.
	 *
	 * Extracts relevant information from the WordPress.org profile page HTML.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    string $html       The HTML content to parse.
	 * @param    string $username   The WordPress.org username.
	 * @return   array             The parsed profile data.
	 */
	private function parse_profile_html( $html, $username = '' ) {
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
			'username'      => sanitize_title( $username ),
			'avatar'        => '',
			'profile_url'   => '',
			'header'        => array(
				'jobline'   => '',
				'location'  => '',
				'joined'    => '',
				'links'     => array(),
				'teams'     => array(),
				'languages' => array(),
			),
			'current_job'   => array(
				'role'        => '',
				'company'     => '',
				'is_present'  => false,
				'dates'       => '',
			),
			'bio'           => '',
			'slack'         => '',
			'contributions' => '',
			'badges'        => array(),
			'user_meta'     => array(),
		);

		$header = $this->parse_profile_header( $xpath, $username );
		$profile_data['name']        = $header['name'];
		$profile_data['username']    = $header['username'];
		$profile_data['avatar']      = $header['avatar'];
		$profile_data['profile_url'] = $header['profile_url'];
		$profile_data['header']      = $header['details'];
		$profile_data['current_job'] = $this->parse_current_job( $xpath, $profile_data['header']['jobline'] );

		// Get Slack
		$slack_node = $xpath->query( '//p[@id="slack-username"]//span[contains(@class, "username")]' );
		if ( $slack_node->length > 0 ) {
			$profile_data['slack'] = esc_html( trim( $slack_node->item( 0 )->textContent ) );
		}

		// Get bio
		$bio_nodes = $xpath->query( '//div[@id="content-about"]/div[@class="item-meta-about"]/p' );
		if ( $bio_nodes->length > 0 ) {
			$profile_data['bio'] = wp_kses_post( trim( $dom->saveHTML( $bio_nodes->item( 0 ) ) ) );
		}

		// Get contributions
		$contribution_nodes = $xpath->query( '//div[@id="content-about"]/div[@class="item-meta-contribution"]/p' );
		if ( $contribution_nodes->length > 0 ) {
			$profile_data['contributions'] = wp_kses_post( trim( $dom->saveHTML( $contribution_nodes->item( 0 ) ) ) );
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
						'text' => esc_html( trim( $aTag->textContent ) ),
						'url'  => esc_url( trim( $aTag->getAttribute( 'href' ) ) ),
					);
				} else {
					$profile_data['user_meta'][ $key ] = $strong ? esc_html( trim( $strong->textContent ) ) : '';
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
				'name'  => esc_html( $badge_name ),
				'icon'  => esc_html( $badge_icon ),
				'class' => array_map( 'esc_html', $classes ),
			);
		}

		return $profile_data;
	}

	/**
	 * Parse the contributor's current job from the WordPress.org profile page.
	 *
	 * @since    1.2.0
	 * @access   private
	 * @param    DOMXPath $xpath         XPath instance for the profile document.
	 * @param    string   $jobline_fallback Optional hero jobline used when jobs block is absent.
	 * @return   array                    Parsed current job fields.
	 */
	private function parse_current_job( $xpath, $jobline_fallback = '' ) {
		$current_job = array(
			'role'       => '',
			'company'    => '',
			'is_present' => false,
			'dates'      => '',
		);

		$job_entries = $xpath->query( "//div[@id='wporg-jobs-public']//div[contains(@class, 'job-entry')]" );
		if ( $job_entries->length > 0 ) {
			$entry = $job_entries->item( 0 );

			$role_node = $xpath->query( ".//div[contains(@class, 'role')]", $entry )->item( 0 );
			if ( $role_node ) {
				$current_job['role'] = esc_html( trim( $role_node->textContent ) );
			}

			$company_node = $xpath->query( ".//div[contains(@class, 'company')]", $entry )->item( 0 );
			if ( $company_node ) {
				$current_job['company'] = esc_html( trim( $company_node->textContent ) );
			}

			$dates_node = $xpath->query( ".//div[contains(@class, 'dates')]", $entry )->item( 0 );
			if ( $dates_node ) {
				$dates = trim( preg_replace( '/\s+/', ' ', $dates_node->textContent ) );
				$current_job['dates'] = esc_html( $dates );
				$current_job['is_present'] = ( false !== stripos( $dates, 'present' ) );
			}
		}

		if ( empty( $current_job['role'] ) && ! empty( $jobline_fallback ) ) {
			$current_job = $this->parse_jobline_fallback( $jobline_fallback, $current_job );
		}

		return $current_job;
	}

	/**
	 * Build current job data from the hero jobline string.
	 *
	 * @since    1.2.0
	 * @access   private
	 * @param    string $jobline      Hero jobline text.
	 * @param    array  $current_job  Existing current job defaults.
	 * @return   array                Parsed current job fields.
	 */
	private function parse_jobline_fallback( $jobline, $current_job ) {
		$jobline = trim( $jobline );

		if ( preg_match( '/^(.+?)\s+at\s+(.+)$/i', $jobline, $matches ) ) {
			$current_job['role']       = esc_html( trim( $matches[1] ) );
			$current_job['company']    = esc_html( trim( $matches[2] ) );
			$current_job['is_present'] = true;
		} else {
			$current_job['role']       = esc_html( $jobline );
			$current_job['is_present'] = true;
		}

		return $current_job;
	}

	/**
	 * Parse header identity fields from the WordPress.org profile page.
	 *
	 * Targets the wp-p2-hero header introduced in the 2026 profiles redesign.
	 *
	 * @since    1.2.0
	 * @access   private
	 * @param    DOMXPath $xpath      XPath instance for the profile document.
	 * @param    string   $username   The WordPress.org username.
	 * @return   array                Parsed header fields.
	 */
	private function parse_profile_header( $xpath, $username ) {
		$header = array(
			'name'        => '',
			'username'    => sanitize_title( $username ),
			'avatar'      => '',
			'profile_url' => '',
			'details'     => array(
				'jobline'   => '',
				'location'  => '',
				'joined'    => '',
				'links'     => array(),
				'teams'     => array(),
				'languages' => array(),
			),
		);

		$name_nodes = $xpath->query( '//header[contains(@class, "wp-p2-hero")]//h2[contains(@class, "wp-p2-hero-name")]' );
		if ( $name_nodes->length > 0 ) {
			$header['name'] = esc_html( trim( $name_nodes->item( 0 )->textContent ) );
		}

		if ( empty( $header['name'] ) ) {
			$name_nodes = $xpath->query( '//header[contains(@class, "site-header")]//h2/a' );
			if ( $name_nodes->length > 0 ) {
				$header['name'] = esc_html( trim( $name_nodes->item( 0 )->textContent ) );
			}
		}

		if ( empty( $header['name'] ) ) {
			$name_nodes = $xpath->query( '//h1[contains(@class, "profile-name")]' );
			if ( $name_nodes->length > 0 ) {
				$header['name'] = esc_html( trim( $name_nodes->item( 0 )->textContent ) );
			}
		}

		$avatar_nodes = $xpath->query( '//div[@id="item-header-avatar"]//img[contains(@class, "avatar")]' );
		if ( $avatar_nodes->length > 0 ) {
			$header['avatar'] = $this->sanitize_profile_image_url( $avatar_nodes->item( 0 )->getAttribute( 'src' ) );
		}

		if ( empty( $header['avatar'] ) ) {
			$avatar_nodes = $xpath->query( '//header[contains(@class, "wp-p2-hero")]//img[contains(@class, "avatar")]' );
			if ( $avatar_nodes->length > 0 ) {
				$header['avatar'] = $this->sanitize_profile_image_url( $avatar_nodes->item( 0 )->getAttribute( 'src' ) );
			}
		}

		if ( empty( $header['avatar'] ) ) {
			$avatar_nodes = $xpath->query( '//img[contains(@class, "avatar")]' );
			if ( $avatar_nodes->length > 0 ) {
				$header['avatar'] = $this->sanitize_profile_image_url( $avatar_nodes->item( 0 )->getAttribute( 'src' ) );
			}
		}

		$handle_nodes = $xpath->query( '//span[contains(@class, "wp-p2-handle")]' );
		if ( $handle_nodes->length > 0 ) {
			$handle = trim( $handle_nodes->item( 0 )->textContent );
			$header['username'] = esc_html( ltrim( $handle, '@' ) );
		}

		$profile_url_nodes = $xpath->query( '//meta[@property="og:url"]/@content' );
		if ( $profile_url_nodes->length > 0 ) {
			$header['profile_url'] = esc_url( $profile_url_nodes->item( 0 )->nodeValue );
		}

		if ( empty( $header['profile_url'] ) && ! empty( $header['username'] ) ) {
			$header['profile_url'] = esc_url( 'https://profiles.wordpress.org/' . $header['username'] . '/' );
		} elseif ( empty( $header['profile_url'] ) && ! empty( $username ) ) {
			$header['profile_url'] = esc_url( 'https://profiles.wordpress.org/' . sanitize_title( $username ) . '/' );
		}

		$jobline_nodes = $xpath->query( '//header[contains(@class, "wp-p2-hero")]//p[contains(@class, "wp-p2-jobline")]' );
		if ( $jobline_nodes->length > 0 ) {
			$header['details']['jobline'] = esc_html( trim( $jobline_nodes->item( 0 )->textContent ) );
		}

		$location_nodes = $xpath->query( '//header[contains(@class, "wp-p2-hero")]//span[contains(@class, "wp-p2-loc")]' );
		if ( $location_nodes->length > 0 ) {
			$header['details']['location'] = esc_html( trim( $location_nodes->item( 0 )->textContent ) );
		}

		$joined_nodes = $xpath->query( '//header[contains(@class, "wp-p2-hero")]//span[contains(@class, "wp-p2-joined")]' );
		if ( $joined_nodes->length > 0 ) {
			$header['details']['joined'] = esc_html( trim( $joined_nodes->item( 0 )->textContent ) );
		}

		foreach ( $xpath->query( '//header[contains(@class, "wp-p2-hero")]//div[contains(@class, "wp-p2-links")]//a' ) as $link_node ) {
			$link_text = trim( preg_replace( '/\s+/', ' ', $link_node->textContent ) );
			if ( empty( $link_text ) ) {
				continue;
			}

			$header['details']['links'][] = array(
				'text' => esc_html( $link_text ),
				'url'  => esc_url( $link_node->getAttribute( 'href' ) ),
			);
		}

		foreach ( $xpath->query( '//header[contains(@class, "wp-p2-hero")]//div[contains(@class, "wp-p2-chip-row")]' ) as $chip_row ) {
			$label_node = $xpath->query( './/span[contains(@class, "wp-p2-chip-label")]', $chip_row )->item( 0 );
			if ( ! $label_node ) {
				continue;
			}

			$label = trim( $label_node->textContent );
			$chips = array();

			foreach ( $xpath->query( './/span[contains(@class, "wp-p2-chip") and not(contains(@class, "wp-p2-chip-label"))]', $chip_row ) as $chip_node ) {
				$chip_text = trim( $chip_node->textContent );
				if ( ! empty( $chip_text ) ) {
					$chips[] = esc_html( $chip_text );
				}
			}

			if ( 'Teams' === $label ) {
				$header['details']['teams'] = $chips;
			} elseif ( 'Languages' === $label ) {
				$header['details']['languages'] = $chips;
			}
		}

		return $header;
	}

	/**
	 * Sanitize an image URL extracted from profile HTML.
	 *
	 * @since    1.2.0
	 * @access   private
	 * @param    string $url    Raw image URL from DOM attributes.
	 * @return   string         Escaped image URL.
	 */
	private function sanitize_profile_image_url( $url ) {
		return esc_url( html_entity_decode( $url, ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );
	}

	/**
	 * Normalize a Gravatar URL to a consistent display size.
	 *
	 * @since    1.2.0
	 * @access   private
	 * @param    string $url     Avatar image URL.
	 * @param    int    $size    Desired avatar size in pixels.
	 * @return   string          Normalized avatar URL.
	 */
	private function normalize_avatar_url( $url, $size = 150 ) {
		if ( preg_match( '/([?&])s=\d+/', $url ) ) {
			return preg_replace( '/([?&])s=\d+/', '${1}s=' . absint( $size ), $url );
		}

		return $url;
	}
}
