<?php
/**
 * Provide a admin area view for the plugin
 */
?>

<div class="wrap">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
	
	<div class="contributor-highlights-admin-content">
		<div class="contributor-highlights-admin-section">
			<h3><?php _e( 'How to Use', 'contributor-highlights' ); ?></h3>
			<p><?php _e( 'Use the shortcode below to display a WordPress.org contributor profile:', 'contributor-highlights' ); ?></p>
			<code>[contributor_profile username="username"]</code>
			
			<h4><?php _e( 'Available Parameters:', 'contributor-highlights' ); ?></h4>
			<ul>
				<li><code>username</code> - <?php _e( 'The WordPress.org username (required)', 'contributor-highlights' ); ?></li>
				<li><code>show_avatar</code> - <?php _e( 'Show the contributor avatar (yes/no, default: yes)', 'contributor-highlights' ); ?></li>
				<li><code>show_bio</code> - <?php _e( 'Show the contributor bio (yes/no, default: yes)', 'contributor-highlights' ); ?></li>
				<li><code>show_contributions</code> - <?php _e( 'Show the contributor contributions (yes/no, default: yes)', 'contributor-highlights' ); ?></li>
			</ul>
			
			<h4><?php _e( 'Example:', 'contributor-highlights' ); ?></h4>
			<code>[contributor_profile username="rollybueno" show_avatar="yes" show_bio="yes" show_contributions="yes"]</code>
		</div>
		
		<div class="contributor-highlights-admin-section">
			<h3><?php _e( 'Cache Information', 'contributor-highlights' ); ?></h3>
			<p><?php _e( 'Profile data is cached for 1 hour to improve performance. You can clear the cache by deactivating and reactivating the plugin.', 'contributor-highlights' ); ?></p>
		</div>
	</div>
</div> 
