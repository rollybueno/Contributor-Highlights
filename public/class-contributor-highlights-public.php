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
				'show_releases'      => true,
				'show_team_focus'    => true,
				'show_current_job'   => true,
				'show_meta'          => true,
			),
			$atts,
			'contributor_profile'
		);

		$boolean_keys = array(
			'compact_version',
			'show_avatar',
			'show_bio',
			'show_contributions',
			'show_badges',
			'show_releases',
			'show_team_focus',
			'show_current_job',
			'show_meta',
		);

		foreach ( $boolean_keys as $boolean_key ) {
			$atts[ $boolean_key ] = $this->parse_bool_attr( $atts[ $boolean_key ] );
		}

		if ( empty( $atts['username'] ) ) {
			return '<p>' . __( 'Please provide a WordPress.org username.', 'contributor-highlights' ) . '</p>';
		}

		// If compact version is true,
		// only show meta and badges, and hide the name
		if ( $atts['compact_version'] ) {
			$atts['show_bio']    = false;
			$atts['show_avatar'] = true;
			$atts['show_meta']   = true;
			$atts['show_badges'] = true;
		}

		$profile_data = $this->get_profile_data( $atts['username'] );

		if ( is_wp_error( $profile_data ) ) {
			return '<p>' . $profile_data->get_error_message() . '</p>';
		}

		$header      = isset( $profile_data['header'] ) ? $profile_data['header'] : array();
		$current_job = isset( $profile_data['current_job'] ) ? $profile_data['current_job'] : array();
		$recent_impact = isset( $profile_data['recent_impact'] ) ? $profile_data['recent_impact'] : array();
		$impact_periods = $this->get_ordered_impact_periods( $recent_impact );
		$impact_line    = $this->format_recent_impact_line(
			isset( $recent_impact['12m'] ) ? $recent_impact['12m'] : array()
		);
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

					<?php if ( $atts['compact_version'] && $atts['show_current_job'] && ! empty( $current_job['role'] ) ) : ?>
						<p class="contributor-current-job-line">
							<?php echo esc_html( $current_job['role'] ); ?>
							<?php if ( ! empty( $current_job['company'] ) ) : ?>
								<?php esc_html_e( 'at', 'contributor-highlights' ); ?>
								<?php echo esc_html( $current_job['company'] ); ?>
							<?php endif; ?>
						</p>
					<?php endif; ?>

					<?php if ( $atts['compact_version'] && $atts['show_contributions'] && ! empty( $impact_line ) ) : ?>
						<p class="contributor-recent-impact-line contributor-recent-impact-line--compact"><?php echo esc_html( $impact_line ); ?></p>
					<?php endif; ?>

					<?php
					$handle_parts = array();
					if ( $atts['show_meta'] && ! empty( $profile_data['username'] ) ) {
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
						<div class="contributor-chip-row contributor-chip-row--teams">
							<span class="contributor-chip-label"><?php esc_html_e( 'Teams', 'contributor-highlights' ); ?></span>
							<?php foreach ( $header['teams'] as $team ) : ?>
								<?php
								$team_chip_slug = $this->get_team_chip_slug( $team );
								$chip_classes   = 'contributor-chip';

								if ( $team_chip_slug ) {
									$chip_classes .= ' contributor-chip--' . esc_attr( $team_chip_slug );
								}
								?>
								<span class="<?php echo esc_attr( $chip_classes ); ?>"><?php echo esc_html( $team ); ?></span>
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

			<?php if ( ! $atts['compact_version'] && $atts['show_current_job'] && ! empty( $current_job['role'] ) ) : ?>
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

			<?php
			$bio_display = $this->prepare_bio_display( $profile_data['bio'] );
			if ( $atts['show_bio'] && ( ! empty( $bio_display['preview'] ) || ! empty( $bio_display['full_html'] ) ) ) :
				?>
				<section class="contributor-bio">
					<?php if ( ! $atts['compact_version'] ) : ?>
						<h3 class="contributor-bio-title"><?php esc_html_e( 'Bio', 'contributor-highlights' ); ?></h3>
					<?php endif; ?>
					<div class="contributor-bio-content">
						<?php if ( ! empty( $bio_display['is_collapsible'] ) ) : ?>
							<div class="contributor-bio-preview">
								<p><?php echo esc_html( $bio_display['preview'] ); ?></p>
							</div>
							<div class="contributor-bio-full" hidden>
								<?php echo wp_kses_post( $bio_display['full_html'] ); ?>
							</div>
						<?php else : ?>
							<?php echo wp_kses_post( $bio_display['full_html'] ); ?>
						<?php endif; ?>
					</div>
					<?php if ( ! empty( $bio_display['is_collapsible'] ) ) : ?>
						<button
							type="button"
							class="contributor-bio-toggle"
							aria-expanded="false"
							data-read-more="<?php esc_attr_e( 'Read more', 'contributor-highlights' ); ?>"
							data-read-less="<?php esc_attr_e( 'Read less', 'contributor-highlights' ); ?>"
						>
							<?php esc_html_e( 'Read more', 'contributor-highlights' ); ?>
						</button>
					<?php endif; ?>
				</section>
			<?php endif; ?>

			<?php if ( ! $atts['compact_version'] && $atts['show_contributions'] && ! empty( $impact_periods ) ) : ?>
				<section class="contributor-recent-impact">
					<h3 class="contributor-recent-impact-title"><?php esc_html_e( 'Recent impact', 'contributor-highlights' ); ?></h3>
					<?php if ( ! empty( $recent_impact['legend'] ) ) : ?>
						<p class="contributor-recent-impact-legend"><?php echo esc_html( $recent_impact['legend'] ); ?></p>
					<?php endif; ?>
					<div class="contributor-impact-tiles">
						<?php foreach ( $impact_periods as $period ) : ?>
							<div class="contributor-impact-tile">
								<?php if ( ! empty( $period['period_label'] ) ) : ?>
									<span class="contributor-impact-period"><?php echo esc_html( $period['period_label'] ); ?></span>
								<?php endif; ?>
								<div class="contributor-impact-main">
									<span class="contributor-impact-count"><?php echo esc_html( $period['contributions'] ); ?></span>
									<span class="contributor-impact-unit"><?php esc_html_e( 'contributions', 'contributor-highlights' ); ?></span>
								</div>
								<div class="contributor-impact-breakdown">
									<?php if ( ! empty( $period['high'] ) ) : ?>
										<span class="contributor-impact-metric contributor-impact-metric--high">
											<span class="contributor-impact-metric-label"><?php esc_html_e( 'high', 'contributor-highlights' ); ?></span>
											<span class="contributor-impact-metric-value"><?php echo esc_html( $period['high'] ); ?></span>
										</span>
									<?php endif; ?>
									<?php if ( ! empty( $period['medium'] ) ) : ?>
										<span class="contributor-impact-metric contributor-impact-metric--medium">
											<span class="contributor-impact-metric-label"><?php esc_html_e( 'medium', 'contributor-highlights' ); ?></span>
											<span class="contributor-impact-metric-value"><?php echo esc_html( $period['medium'] ); ?></span>
										</span>
									<?php endif; ?>
									<?php if ( ! empty( $period['score'] ) ) : ?>
										<span class="contributor-impact-metric contributor-impact-metric--score">
											<span class="contributor-impact-metric-label"><?php esc_html_e( 'score', 'contributor-highlights' ); ?></span>
											<span class="contributor-impact-metric-value"><?php echo esc_html( $period['score'] ); ?></span>
										</span>
									<?php endif; ?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</section>
			<?php endif; ?>

			<?php
			$team_focus = isset( $profile_data['team_focus'] ) ? $profile_data['team_focus'] : array();
			?>
			<?php if ( ! $atts['compact_version'] && $atts['show_team_focus'] && ! empty( $team_focus['teams'] ) ) : ?>
				<section class="contributor-team-focus">
					<div class="contributor-spec-card">
						<h3 class="contributor-spec-title"><?php esc_html_e( 'Team focus', 'contributor-highlights' ); ?></h3>
						<?php if ( ! empty( $team_focus['summary'] ) ) : ?>
							<p class="contributor-spec-summary"><?php echo esc_html( $team_focus['summary'] ); ?></p>
						<?php endif; ?>
						<div
							class="contributor-spec-stack"
							role="img"
							aria-label="<?php echo esc_attr( $this->format_team_focus_stack_label( $team_focus['teams'] ) ); ?>"
						>
							<?php foreach ( $team_focus['teams'] as $team ) : ?>
								<span
									class="contributor-spec-seg contributor-spec-seg--<?php echo esc_attr( $team['slug'] ); ?>"
									style="width: <?php echo esc_attr( $team['percent'] ); ?>%;"
									title="<?php echo esc_attr( sprintf( '%1$s (%2$s%%)', $team['name'], $team['percent'] ) ); ?>"
								></span>
							<?php endforeach; ?>
						</div>
						<ul class="contributor-spec-legend">
							<?php foreach ( $team_focus['teams'] as $team ) : ?>
								<li class="contributor-spec-legend-item">
									<span class="contributor-spec-dot contributor-spec-dot--<?php echo esc_attr( $team['slug'] ); ?>" aria-hidden="true"></span>
									<span class="contributor-spec-label"><?php echo esc_html( $team['name'] ); ?></span>
									<span class="contributor-spec-value"><?php echo esc_html( $team['percent'] ); ?>%</span>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				</section>
			<?php endif; ?>

			<?php if ( $atts['show_contributions'] && ! empty( $profile_data['contributions'] ) ) : ?>
				<h3><?php esc_html_e( 'Contributions', 'contributor-highlights' ); ?></h3>
				<div class="contributor-contributions">
					<?php echo wp_kses_post( $profile_data['contributions'] ); ?>
				</div>
			<?php endif; ?>

			<?php
			$wordpress_releases = isset( $profile_data['wordpress_releases'] ) ? $profile_data['wordpress_releases'] : array();
			?>
			<?php if ( ! $atts['compact_version'] && $atts['show_releases'] && ! empty( $wordpress_releases['versions'] ) ) : ?>
				<section class="contributor-releases">
					<div class="contributor-spec-card">
						<h3 class="contributor-spec-title"><?php esc_html_e( 'WordPress releases', 'contributor-highlights' ); ?></h3>
						<?php if ( ! empty( $wordpress_releases['summary'] ) ) : ?>
							<p class="contributor-spec-summary"><?php echo esc_html( $wordpress_releases['summary'] ); ?></p>
						<?php endif; ?>
						<?php if ( ! empty( $wordpress_releases['stack'] ) ) : ?>
							<div
								class="contributor-spec-stack"
								role="img"
								aria-label="<?php echo esc_attr( $this->format_wordpress_releases_stack_label( $wordpress_releases['roles'] ) ); ?>"
							>
								<?php foreach ( $wordpress_releases['stack'] as $segment ) : ?>
									<span
										class="contributor-spec-seg contributor-spec-seg--<?php echo esc_attr( $segment['slug'] ); ?>"
										style="width: <?php echo esc_attr( $segment['width'] ); ?>%;"
										<?php if ( ! empty( $segment['label'] ) ) : ?>
											title="<?php echo esc_attr( $segment['label'] ); ?>"
										<?php endif; ?>
									></span>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
						<?php if ( ! empty( $wordpress_releases['roles'] ) ) : ?>
							<ul class="contributor-spec-legend">
								<?php foreach ( $wordpress_releases['roles'] as $role ) : ?>
									<li class="contributor-spec-legend-item">
										<span class="contributor-spec-dot contributor-spec-dot--<?php echo esc_attr( $role['slug'] ); ?>" aria-hidden="true"></span>
										<span class="contributor-spec-label"><?php echo esc_html( $role['name'] ); ?></span>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
						<ul class="contributor-spec-versions">
							<?php foreach ( $wordpress_releases['versions'] as $release ) : ?>
								<li
									class="contributor-spec-version"
									<?php if ( ! empty( $release['role'] ) ) : ?>
										title="<?php echo esc_attr( $release['role'] ); ?>"
									<?php endif; ?>
								>
									<span class="contributor-spec-dot contributor-spec-dot--<?php echo esc_attr( $release['slug'] ); ?>" aria-hidden="true"></span>
									<span class="contributor-spec-version-number"><?php echo esc_html( $release['version'] ); ?></span>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				</section>
			<?php endif; ?>

			<?php if ( $atts['show_badges'] && $this->badges_have_items( $profile_data['badges'] ) ) : ?>
				<div class="contributor-badges">
					<?php if ( ! $atts['compact_version'] ) : ?>
						<h3 class="contributor-badges-title"><?php esc_html_e( 'Badges & Achievements', 'contributor-highlights' ); ?></h3>
					<?php endif; ?>
					<div class="contributor-badge-groups">
						<?php foreach ( $profile_data['badges']['groups'] as $badge_group ) : ?>
							<section class="contributor-badge-group contributor-badge-group--<?php echo esc_attr( $badge_group['slug'] ); ?>">
								<header class="contributor-badge-group-head">
									<h4 class="contributor-badge-group-title">
										<span class="contributor-badge-group-dot" aria-hidden="true"></span>
										<?php echo esc_html( $badge_group['name'] ); ?>
									</h4>
									<?php if ( ! empty( $badge_group['count'] ) ) : ?>
										<span class="contributor-badge-group-count"><?php echo esc_html( $badge_group['count'] ); ?></span>
									<?php endif; ?>
								</header>
								<div class="contributor-badge-grid">
									<?php foreach ( $badge_group['badges'] as $badge ) : ?>
										<div
											class="badge-item badge-item--<?php echo esc_attr( $badge['slug'] ); ?>"
											<?php if ( ! empty( $badge['title'] ) ) : ?>
												title="<?php echo esc_attr( $badge['title'] ); ?>"
											<?php endif; ?>
										>
											<span class="<?php echo esc_attr( implode( ' ', $badge['icon_classes'] ) ); ?>" aria-hidden="true"></span>
											<span class="badge-name"><?php echo esc_html( $badge['name'] ); ?></span>
											<?php if ( ! empty( $badge['year'] ) ) : ?>
												<span class="badge-year"><?php echo esc_html( $badge['year'] ); ?></span>
											<?php endif; ?>
										</div>
									<?php endforeach; ?>
								</div>
							</section>
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
		$transient_key = 'conthi_profile_data_v12_' . sanitize_title( $username );
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
			'bio'           => array(
				'content' => '',
			),
			'recent_impact' => array(
				'legend' => '',
			),
			'slack'         => '',
			'contributions' => '',
			'badges'              => array(
				'groups' => array(),
			),
			'wordpress_releases'  => array(
				'summary'  => '',
				'stack'    => array(),
				'roles'    => array(),
				'versions' => array(),
			),
			'team_focus'          => array(
				'summary' => '',
				'teams'   => array(),
			),
			'user_meta'           => array(),
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

		$profile_data['bio']           = $this->parse_bio( $xpath, $dom );
		$profile_data['recent_impact'] = $this->parse_recent_impact( $xpath );

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

		$profile_data['badges']              = $this->parse_badges( $xpath );
		$profile_data['wordpress_releases']  = $this->parse_wordpress_releases( $xpath );
		$profile_data['team_focus']          = $this->parse_team_focus( $xpath );

		return $profile_data;
	}

	/**
	 * Parse team focus distribution from the profile page.
	 *
	 * @since    1.2.0
	 * @access   private
	 * @param    DOMXPath $xpath XPath instance for the profile document.
	 * @return   array           Team focus summary and team percentages.
	 */
	private function parse_team_focus( $xpath ) {
		$team_focus = array(
			'summary' => '',
			'teams'   => array(),
		);

		$section = $xpath->query( '//section[contains(@class, "wp-p2-specs") and not(contains(@class, "wp-p2-releases"))]' )->item( 0 );
		if ( ! $section ) {
			return $team_focus;
		}

		$summary_node = $xpath->query( './/div[contains(@class, "sub")]', $section )->item( 0 );
		if ( $summary_node ) {
			$team_focus['summary'] = esc_html( trim( $summary_node->textContent ) );
		}

		foreach ( $xpath->query( './/ul[contains(@class, "spec-legend")]//li', $section ) as $legend_item ) {
			$team_node    = $xpath->query( './/span[contains(@class, "leg-team")]', $legend_item )->item( 0 );
			$percent_node = $xpath->query( './/span[contains(@class, "leg-pct")]', $legend_item )->item( 0 );
			$dot_node     = $xpath->query( './/span[contains(@class, "spec-dot")]', $legend_item )->item( 0 );

			if ( ! $team_node || ! $percent_node ) {
				continue;
			}

			$segment_class = $dot_node ? $this->extract_spec_segment_class( $dot_node->getAttribute( 'class' ) ) : 'seg-stone';
			$percent_value = (float) preg_replace( '/[^0-9.]/', '', $percent_node->textContent );

			$team_focus['teams'][] = array(
				'name'    => esc_html( trim( $team_node->textContent ) ),
				'percent' => $percent_value,
				'slug'    => sanitize_html_class( str_replace( 'seg-', '', $segment_class ) ),
			);
		}

		return $team_focus;
	}

	/**
	 * Extract a segment class from a spec dot element.
	 *
	 * @since    1.2.0
	 * @access   private
	 * @param    string $class_attr Space-separated class list.
	 * @return   string             Segment class name.
	 */
	private function extract_spec_segment_class( $class_attr ) {
		foreach ( explode( ' ', $class_attr ) as $class_name ) {
			if ( 0 === strpos( $class_name, 'seg-' ) ) {
				return sanitize_html_class( $class_name );
			}
		}

		return 'seg-stone';
	}

	/**
	 * Format an accessible label for the team focus stack bar.
	 *
	 * @since    1.2.0
	 * @access   private
	 * @param    array $teams Parsed team focus items.
	 * @return   string        Accessible stack label.
	 */
	private function format_team_focus_stack_label( $teams ) {
		$parts = array();

		foreach ( $teams as $team ) {
			$parts[] = sprintf( '%1$s %2$s%%', $team['name'], $team['percent'] );
		}

		return sprintf(
			/* translators: %s: comma-separated team contribution percentages */
			__( 'Contribution share: %s', 'contributor-highlights' ),
			implode( ', ', $parts )
		);
	}

	/**
	 * Parse WordPress release contributions from the profile page.
	 *
	 * @since    1.2.0
	 * @access   private
	 * @param    DOMXPath $xpath XPath instance for the profile document.
	 * @return   array           Release summary and version list.
	 */
	private function parse_wordpress_releases( $xpath ) {
		$releases = array(
			'summary'  => '',
			'stack'    => array(),
			'roles'    => array(),
			'versions' => array(),
		);

		$section = $xpath->query( '//section[contains(@class, "wp-p2-releases")]' )->item( 0 );
		if ( ! $section ) {
			return $releases;
		}

		$summary_node = $xpath->query( './/div[contains(@class, "sub")]', $section )->item( 0 );
		if ( $summary_node ) {
			$releases['summary'] = esc_html( trim( $summary_node->textContent ) );
		}

		foreach ( $xpath->query( './/div[contains(@class, "spec-stack")]//span[contains(@class, "spec-seg")]', $section ) as $segment_node ) {
			$segment_class = $this->extract_spec_segment_class( $segment_node->getAttribute( 'class' ) );
			$width         = 0;

			if ( preg_match( '/width:\s*([0-9.]+)%/', $segment_node->getAttribute( 'style' ), $matches ) ) {
				$width = (float) $matches[1];
			}

			$releases['stack'][] = array(
				'slug'  => sanitize_html_class( str_replace( 'seg-', '', $segment_class ) ),
				'width' => $width,
				'label' => esc_attr( trim( $segment_node->getAttribute( 'title' ) ) ),
			);
		}

		foreach ( $xpath->query( './/ul[contains(@class, "spec-legend")]//li', $section ) as $legend_item ) {
			$role_node  = $xpath->query( './/span[contains(@class, "leg-team")]', $legend_item )->item( 0 );
			$count_node = $xpath->query( './/span[contains(@class, "leg-pct")]', $legend_item )->item( 0 );
			$dot_node   = $xpath->query( './/span[contains(@class, "spec-dot")]', $legend_item )->item( 0 );

			if ( ! $role_node || ! $count_node ) {
				continue;
			}

			$segment_class = $dot_node ? $this->extract_spec_segment_class( $dot_node->getAttribute( 'class' ) ) : 'seg-stone';

			$releases['roles'][] = array(
				'name'  => esc_html( trim( $role_node->textContent ) ),
				'count' => (int) preg_replace( '/[^0-9]/', '', $count_node->textContent ),
				'slug'  => sanitize_html_class( str_replace( 'seg-', '', $segment_class ) ),
			);
		}

		foreach ( $xpath->query( './/ul[contains(@class, "spec-versions")]//li[contains(@class, "ver-chip")]', $section ) as $chip_node ) {
			$version_node = $xpath->query( './/span[contains(@class, "ver-num")]', $chip_node )->item( 0 );
			$swatch_node  = $xpath->query( './/span[contains(@class, "ver-sw")]', $chip_node )->item( 0 );

			if ( ! $version_node ) {
				continue;
			}

			$segment_class = $swatch_node ? $this->extract_spec_segment_class( $swatch_node->getAttribute( 'class' ) ) : 'seg-stone';

			$releases['versions'][] = array(
				'version' => esc_html( trim( $version_node->textContent ) ),
				'role'    => esc_attr( trim( $chip_node->getAttribute( 'title' ) ) ),
				'slug'    => sanitize_html_class( str_replace( 'seg-', '', $segment_class ) ),
			);
		}

		return $releases;
	}

	/**
	 * Format an accessible label for the WordPress releases stack bar.
	 *
	 * @since    1.2.0
	 * @access   private
	 * @param    array $roles Parsed release role counts.
	 * @return   string       Accessible stack label.
	 */
	private function format_wordpress_releases_stack_label( $roles ) {
		$parts = array();

		foreach ( $roles as $role ) {
			$parts[] = sprintf( '%1$s %2$d', $role['name'], $role['count'] );
		}

		return sprintf(
			/* translators: %s: comma-separated release role counts */
			__( 'Releases by role: %s', 'contributor-highlights' ),
			implode( ', ', $parts )
		);
	}

	/**
	 * Parse badges from the WordPress.org profile page.
	 *
	 * @since    1.2.0
	 * @access   private
	 * @param    DOMXPath $xpath XPath instance for the profile document.
	 * @return   array           Badge groups and items.
	 */
	private function parse_badges( $xpath ) {
		$badges = array(
			'groups' => array(),
		);

		foreach ( $xpath->query( '//section[contains(@class, "wp-p2-badges-block")]//section[contains(@class, "wp-p2-badge-cat")]' ) as $category_node ) {
			$group_slug  = '';
			$group_class = $category_node->getAttribute( 'class' );

			foreach ( explode( ' ', $group_class ) as $class_name ) {
				if ( 0 === strpos( $class_name, 'cat-' ) ) {
					$group_slug = sanitize_title( substr( $class_name, 4 ) );
					break;
				}
			}

			$group_name_node  = $xpath->query( './/header[contains(@class, "wp-p2-badge-cat-head")]//span[contains(@class, "cname")]', $category_node )->item( 0 );
			$count_node = $xpath->query( './/header[contains(@class, "wp-p2-badge-cat-head")]//div[contains(@class, "ccount")]', $category_node )->item( 0 );
			$items      = array();

			foreach ( $xpath->query( './/span[contains(@class, "medal")]', $category_node ) as $medal_node ) {
				$badge_class = $this->extract_badge_class_name( $medal_node->getAttribute( 'class' ) );
				$badge_slug  = $this->extract_badge_slug( $badge_class );
				$badge_name_node = $xpath->query( './/span[contains(@class, "mn")]', $medal_node )->item( 0 );
				$year_node   = $xpath->query( './/span[contains(@class, "myear")]', $medal_node )->item( 0 );
				$icon_node   = $xpath->query( './/span[contains(@class, "mi")]', $medal_node )->item( 0 );

				if ( ! $badge_name_node ) {
					continue;
				}

				$icon_classes = array( 'badge-icon', $badge_class );
				$medal_title  = $medal_node->getAttribute( 'title' );
				$badge_year   = '';

				if ( $year_node ) {
					$badge_year = trim(
						html_entity_decode( $year_node->textContent, ENT_QUOTES, 'UTF-8' )
					);
				}

				if ( $icon_node ) {
					$icon_class_attr = $icon_node->getAttribute( 'class' );
					if ( false !== strpos( $icon_class_attr, 'dashicons' ) ) {
						$icon_classes = array_merge(
							array( 'badge-icon', 'dashicons' ),
							array_filter(
								array_map( 'sanitize_html_class', explode( ' ', $icon_class_attr ) ),
								function ( $class_name ) {
									return 'mi' !== $class_name;
								}
							)
						);
						$icon_classes[] = $badge_class;
					}
				}

				$items[] = array(
					'slug'         => $badge_slug,
					'name'         => esc_html( trim( $badge_name_node->textContent ) ),
					'year'         => $badge_year,
					'title'        => esc_attr( $medal_title ),
					'icon_classes' => array_map( 'esc_attr', array_unique( $icon_classes ) ),
				);
			}

			if ( empty( $items ) ) {
				continue;
			}

			$badges['groups'][] = array(
				'slug'   => $group_slug,
				'name'   => $group_name_node ? esc_html( trim( $group_name_node->textContent ) ) : '',
				'count'  => $count_node ? esc_html( trim( $count_node->textContent ) ) : '',
				'badges' => $items,
			);
		}

		return $badges;
	}

	/**
	 * Extract the badge class name from a medal element.
	 *
	 * @since    1.2.0
	 * @access   private
	 * @param    string $class_attr Space-separated class list.
	 * @return   string             Badge class name.
	 */
	private function extract_badge_class_name( $class_attr ) {
		foreach ( explode( ' ', $class_attr ) as $class_name ) {
			if ( 0 === strpos( $class_name, 'badge-' ) ) {
				return sanitize_html_class( $class_name );
			}
		}

		return 'badge-unknown';
	}

	/**
	 * Extract a badge slug from its class name.
	 *
	 * @since    1.2.0
	 * @access   private
	 * @param    string $badge_class Badge class name.
	 * @return   string              Badge slug.
	 */
	private function extract_badge_slug( $badge_class ) {
		return sanitize_title( str_replace( 'badge-', '', $badge_class ) );
	}

	/**
	 * Check whether parsed badge data contains any items.
	 *
	 * @since    1.2.0
	 * @access   private
	 * @param    array $badges Parsed badge data.
	 * @return   bool          True when at least one badge exists.
	 */
	private function badges_have_items( $badges ) {
		if ( empty( $badges['groups'] ) || ! is_array( $badges['groups'] ) ) {
			return false;
		}

		foreach ( $badges['groups'] as $badge_group ) {
			if ( ! empty( $badge_group['badges'] ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Parse recent impact stats from the WordPress.org profile page.
	 *
	 * @since    1.2.0
	 * @access   private
	 * @param    DOMXPath $xpath XPath instance for the profile document.
	 * @return   array           Impact stats keyed by period slug.
	 */
	private function parse_recent_impact( $xpath ) {
		$impact = array(
			'legend' => '',
		);

		$legend_node = $xpath->query( '//section[contains(@class, "wp-p2-impact")]//p[contains(@class, "wp-p2-impact-legend")]' )->item( 0 );
		if ( $legend_node ) {
			$impact['legend'] = esc_html( trim( $legend_node->textContent ) );
		}

		foreach ( $xpath->query( '//section[contains(@class, "wp-p2-impact")]//div[contains(@class, "impact-tile")]' ) as $tile ) {
			$period_node = $xpath->query( './/div[contains(@class, "win")]//span', $tile )->item( 0 );
			$count_node  = $xpath->query( './/div[contains(@class, "main")]//span[contains(@class, "n")]', $tile )->item( 0 );

			if ( ! $period_node || ! $count_node ) {
				continue;
			}

			$period_label = trim( $period_node->textContent );
			$period_key   = $this->normalize_impact_period_key( $period_label );
			$metrics      = array(
				'high'   => '',
				'medium' => '',
				'score'  => '',
			);

			foreach ( $xpath->query( './/div[contains(@class, "kv")]', $tile ) as $metric_node ) {
				$metric_label_node = $xpath->query( './/span[contains(@class, "k")]', $metric_node )->item( 0 );
				$metric_value_node = $xpath->query( './/b', $metric_node )->item( 0 );

				if ( ! $metric_label_node || ! $metric_value_node ) {
					continue;
				}

				$metric_key = strtolower( trim( $metric_label_node->textContent ) );
				if ( ! array_key_exists( $metric_key, $metrics ) ) {
					continue;
				}

				$metrics[ $metric_key ] = esc_html( trim( $metric_value_node->textContent ) );
			}

			$impact[ $period_key ] = array(
				'contributions' => esc_html( trim( $count_node->textContent ) ),
				'high'          => $metrics['high'],
				'medium'        => $metrics['medium'],
				'score'         => $metrics['score'],
				'period_label'  => esc_html( $this->normalize_impact_period_label( $period_label ) ),
			);
		}

		return $impact;
	}

	/**
	 * Return impact periods in display order.
	 *
	 * @since    1.2.0
	 * @access   private
	 * @param    array $recent_impact Parsed recent impact data.
	 * @return   array                Ordered period data arrays.
	 */
	private function get_ordered_impact_periods( $recent_impact ) {
		$periods = array();

		foreach ( array( '30d', '90d', '12m' ) as $period_key ) {
			if ( empty( $recent_impact[ $period_key ]['contributions'] ) ) {
				continue;
			}

			$periods[] = $recent_impact[ $period_key ];
		}

		return $periods;
	}

	/**
	 * Normalize an impact period label into a stable array key.
	 *
	 * @since    1.2.0
	 * @access   private
	 * @param    string $period_label Raw period label from the profile page.
	 * @return   string               Normalized period key.
	 */
	private function normalize_impact_period_key( $period_label ) {
		$period_label = strtolower( trim( $period_label ) );

		if ( false !== strpos( $period_label, '12 month' ) ) {
			return '12m';
		}

		if ( false !== strpos( $period_label, '90 day' ) ) {
			return '90d';
		}

		if ( false !== strpos( $period_label, '30 day' ) ) {
			return '30d';
		}

		return sanitize_title( $period_label );
	}

	/**
	 * Normalize an impact period label for display.
	 *
	 * @since    1.2.0
	 * @access   private
	 * @param    string $period_label Raw period label from the profile page.
	 * @return   string               Display-friendly period label.
	 */
	private function normalize_impact_period_label( $period_label ) {
		$period_label = trim( $period_label );

		return preg_replace( '/^Last\s+/i', '', $period_label );
	}

	/**
	 * Format a recent impact period into a single summary line.
	 *
	 * @since    1.2.0
	 * @access   private
	 * @param    array $period_data Parsed impact data for one period.
	 * @return   string             Formatted impact summary line.
	 */
	private function format_recent_impact_line( $period_data ) {
		if ( empty( $period_data['contributions'] ) ) {
			return '';
		}

		$line = sprintf(
			/* translators: %s: number of contributions */
			__( '%s contributions', 'contributor-highlights' ),
			$period_data['contributions']
		);

		if ( ! empty( $period_data['score'] ) ) {
			$line .= sprintf(
				/* translators: %s: weighted impact score */
				__( ' · score %s', 'contributor-highlights' ),
				$period_data['score']
			);
		}

		if ( ! empty( $period_data['period_label'] ) ) {
			$line .= sprintf(
				/* translators: %s: impact period label, e.g. 12 months */
				__( ' (%s)', 'contributor-highlights' ),
				$period_data['period_label']
			);
		}

		return $line;
	}

	/**
	 * Parse bio content from the WordPress.org profile page.
	 *
	 * @since    1.2.0
	 * @access   private
	 * @param    DOMXPath    $xpath XPath instance for the profile document.
	 * @param    DOMDocument $dom   Parsed profile document.
	 * @return   array               Parsed bio fields.
	 */
	private function parse_bio( $xpath, $dom ) {
		$bio = array(
			'content' => '',
		);

		$about_nodes = $xpath->query( '//div[@id="content-about"]//div[contains(@class, "item-meta-about")]' );
		if ( 0 === $about_nodes->length ) {
			return $bio;
		}

		$paragraph_nodes = $xpath->query( './/p', $about_nodes->item( 0 ) );
		if ( $paragraph_nodes->length > 0 ) {
			$paragraphs = array();

			foreach ( $paragraph_nodes as $paragraph_node ) {
				$paragraphs[] = wp_kses_post( trim( $dom->saveHTML( $paragraph_node ) ) );
			}

			$bio['content'] = implode( '', $paragraphs );
		}

		return $bio;
	}

	/**
	 * Prepare bio text for display with optional word-based truncation.
	 *
	 * @since    1.2.0
	 * @access   private
	 * @param    array $bio         Parsed bio data.
	 * @param    int   $word_limit  Word limit before truncation.
	 * @return   array             Display-ready bio data.
	 */
	private function prepare_bio_display( $bio, $word_limit = 300 ) {
		$display = array(
			'preview'        => '',
			'full_html'      => '',
			'is_collapsible' => false,
		);

		if ( empty( $bio['content'] ) ) {
			return $display;
		}

		$plain_text = trim( preg_replace( '/\s+/u', ' ', wp_strip_all_tags( $bio['content'] ) ) );
		if ( '' === $plain_text ) {
			return $display;
		}

		$display['full_html'] = $bio['content'];
		$truncated            = $this->truncate_words( $plain_text, $word_limit );

		if ( $truncated['is_truncated'] ) {
			$display['preview']        = $truncated['text'];
			$display['is_collapsible'] = true;
		}

		return $display;
	}

	/**
	 * Truncate plain text to a maximum number of words.
	 *
	 * @since    1.2.0
	 * @access   private
	 * @param    string $text        Plain text to truncate.
	 * @param    int    $word_limit  Maximum number of words.
	 * @return   array               Truncated text and truncation state.
	 */
	private function truncate_words( $text, $word_limit = 300 ) {
		$words = preg_split( '/\s+/u', trim( $text ), -1, PREG_SPLIT_NO_EMPTY );

		if ( count( $words ) <= $word_limit ) {
			return array(
				'text'         => $text,
				'is_truncated' => false,
			);
		}

		return array(
			'text'         => implode( ' ', array_slice( $words, 0, $word_limit ) ) . '…',
			'is_truncated' => true,
		);
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

	/**
	 * Map a profile team label to a chip color slug used in CSS.
	 *
	 * Slugs align with WordPress.org contributor badge colors.
	 *
	 * @since    1.2.0
	 * @access   private
	 * @param    string $team_name Team label from the profile header.
	 * @return   string            Sanitized slug, or empty when unknown.
	 */
	private function get_team_chip_slug( $team_name ) {
		static $team_slug_map = null;

		if ( null === $team_slug_map ) {
			$team_slug_map = array(
				'accessibility'   => 'accessibility',
				'bbpress'         => 'bbpress',
				'buddypress'      => 'buddypress',
				'community'       => 'community',
				'core'            => 'core',
				'core ai'         => 'core-ai',
				'design'          => 'design',
				'documentation'   => 'documentation',
				'docs'            => 'documentation',
				'forums'          => 'support',
				'hosting'         => 'hosting',
				'marketing'       => 'marketing',
				'media corps'     => 'media-corps',
				'meta'            => 'meta',
				'mobile'          => 'mobile',
				'openverse'       => 'openverse',
				'patterns'        => 'patterns',
				'performance'     => 'performance',
				'photos'          => 'photos',
				'playground'      => 'playground',
				'plugins'         => 'plugins',
				'polyglots'       => 'polyglots',
				'security'        => 'security',
				'support'         => 'support',
				'sustainability'  => 'sustainability',
				'test'            => 'test',
				'themes'          => 'themes',
				'tide'            => 'tide',
				'training'        => 'training',
				'translation'     => 'polyglots',
				'tv'              => 'wordpress-tv',
				'wordpress tv'    => 'wordpress-tv',
				'wp-cli'          => 'wp-cli',
			);
		}

		$key = strtolower( trim( wp_strip_all_tags( $team_name ) ) );

		if ( isset( $team_slug_map[ $key ] ) ) {
			return sanitize_html_class( $team_slug_map[ $key ] );
		}

		return '';
	}

	/**
	 * Parse a shortcode boolean attribute value.
	 *
	 * @since    1.2.0
	 * @access   private
	 * @param    mixed $value   Raw attribute value.
	 * @param    bool  $default Default when value is ambiguous.
	 * @return   bool           Parsed boolean.
	 */
	private function parse_bool_attr( $value, $default = false ) {
		if ( is_bool( $value ) ) {
			return $value;
		}

		$value = strtolower( trim( (string) $value ) );

		if ( in_array( $value, array( '1', 'true', 'yes', 'on' ), true ) ) {
			return true;
		}

		if ( in_array( $value, array( '0', 'false', 'no', 'off', '' ), true ) ) {
			return false;
		}

		return $default;
	}
}
