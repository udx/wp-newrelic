<?php

/**
 * Class WP_NewRelic_Dashboard
 *
 * Class to handle options page and saving options
 */
class WP_NR_Dashboard {

	public function __construct() {

		if ( WP_NR_IS_NETWORK_ACTIVE ) {
			// Network setting
			add_action( 'network_admin_menu', array( $this, 'action_admin_menu' ) );
		} else {
			add_action( 'admin_menu', array( $this, 'action_admin_menu' ) );
		}

		// save settings
		add_action( 'admin_init', array( $this, 'save_settings' ) );
	}

	/**
	 * Save settings
   *
   * wp site option get wp_nr_capture_urls
   * wp site option get wp_nr_browser_snippet
   *
	 */
	public function save_settings() {
		$nonce = filter_input( INPUT_POST, 'wp_nr_settings', FILTER_SANITIZE_STRING );

		if ( wp_verify_nonce( $nonce, 'wp_nr_settings' ) ) {
			$capture_url = filter_input( INPUT_POST, 'wp_nr_capture_urls' );
			$browser_snippet = filter_input( INPUT_POST, 'wp_nr_browser_snippet' );

			if ( ! empty( $capture_url ) ) {
				$capture_url = true;
			} else {
				$capture_url = false;
			}

			if ( ! empty( $browser_snippet ) ) {
        $browser_snippet = true;
			} else {
        $browser_snippet = false;
			}

			//die('$browser_snippet ' . $browser_snippet );
			if ( WP_NR_IS_NETWORK_ACTIVE ) {
				update_site_option( 'wp_nr_capture_urls', $capture_url );
				update_site_option( 'wp_nr_browser_snippet', $browser_snippet );
			} else {
				update_option( 'wp_nr_capture_urls', $capture_url );
				update_option( 'wp_nr_browser_snippet', $browser_snippet );
			}
		}
	}

	/**
	 * Add menu page
	 */
	public function action_admin_menu() {
		if ( WP_NR_IS_NETWORK_ACTIVE ) {
			add_menu_page(
				'New Relic',
				'New Relic',
				'manage_network',
				'wp-nr-settings',
				array( $this, 'dashboard_page' ),
				'',
				20
			);
		} else {
			add_management_page(
				'New Relic',
				'New Relic',
				'manage_options',
				'wp-nr-settings',
				array( $this, 'dashboard_page' )
			);
		}
	}

	/**
	 * Option page
	 */
	public function dashboard_page() {
		$is_capture = WP_NR_Helper::is_capture_url();

    $browser_snippet = (bool) get_site_option( 'wp_nr_browser_snippet', false );

		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'New Relic for WordPress', 'wp-newrelic' ) ?></h1>
			<form method="post" action="">
				<?php
				wp_nonce_field( 'wp_nr_settings', 'wp_nr_settings' );
				?>
				<table class="form-table">
					<tr>
						<th scope="row"><label for="wp_nr_capture_urls"><?php esc_html_e( 'Capture URL Parameters', 'wp-newrelic' ); ?></label></th>
						<td>
							<input type="checkbox" name="wp_nr_capture_urls" <?php checked( true, $is_capture ) ?>>
							<p class="description"><?php esc_html_e( 'Enable this to record parameter passed to PHP script via the URL (everything after the "?" in the URL).', 'wp-newrelic' ) ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="wp_nr_browser_snippet"><?php esc_html_e( 'Track Frontend Performance', 'wp-newrelic' ); ?></label></th>
						<td>
							<input type="checkbox" name="wp_nr_browser_snippet" <?php checked( true, $browser_snippet) ?>>
							<p class="description"><?php esc_html_e( 'Enable this for frontend performance tracking.', 'wp-newrelic' ) ?></p>
						</td>
					</tr>
				</table>
				<?php
				submit_button( esc_html__( 'Save Changes', 'wp-newrelic' ), 'submit primary' );
				?>
			</form>
		</div>
		<?php
	}
}
