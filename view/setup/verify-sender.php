<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div id="tab-verify-sender"  data-prefix="wposes" class="wposes-tab wposes-content">
	<h2><?php esc_html_e( 'Verify Email Sender', 'wp-offload-ses' ); ?></h2>

	<div class="wposes-verification-errors updated error" style="display: none;">
		<p><?php esc_html_e( 'There was an error verifying the provided sender. Please make sure that your access keys are correct and try again.', 'wp-offload-ses' ); ?><?php echo ' ' . wp_kses_post( $this->more_info_link( 'wp-offload-ses/doc/sender-verification-errors' ) ); ?></p>
	</div>

	<p><?php esc_html_e( 'Now you\'ll need to verify a sender that you want to send mail through Amazon SES. A sender is simply any email address you\'ll be using in the "From" address of outbound emails.', 'wp-offload-ses' ); ?></p>
	<p><?php esc_html_e( 'You can verify individual email addresses, but if you have multiple email addresses on the same domain that will be sending mail, we recommend verifying a domain.', 'wp-offload-ses' ); ?></p>
	<p><?php esc_html_e( 'How would you like to verify your sender? You can always add more senders later.', 'wp-offload-ses' ); ?></p>

	<form id="wposes-setup-verify-senders-form">
		<p class="wposes-sender-type-toggle">
			<input id="wposes-domain" type="radio" name="sender-type" value="domain" checked="checked" />
			<label for="wposes-domain"><?php esc_html_e( 'Verify Domain (recommended)', 'wp-offload-ses' ); ?></label>
			<input id="wposes-email" type="radio" name="sender-type" value="email" />
			<label for="wposes-email"><?php esc_html_e( 'Verify Email Address', 'wp-offload-ses' ); ?></label>
		</p>

		<p class="wposes-show-domain"><strong><?php esc_html_e( 'What domain would you like to verify?', 'wp-offload-ses' ); ?></strong></p>
		<p class="wposes-show-email" style="display: none;"><strong><?php esc_html_e( 'What email address would you like to verify?', 'wp-offload-ses' ); ?></strong></p>

		<?php $invalid_domain = __( 'Please enter a valid domain name (without http:// or https://).', 'wp-offload-ses' ); ?>
		<input id="wposes-setup-verify-domain" class="wposes-show-domain wposes-setup-verify-input" type="text" value="<?php echo esc_attr( DeliciousBrains\WP_Offload_SES\Utils::current_domain() ); ?>" pattern="^(?!http:|https:|www.*$).*" title="<?php echo esc_attr( $invalid_domain ); ?>" required="required" />
		<input id="wposes-setup-verify-email" class="wposes-show-email wposes-setup-verify-input" type="email" style="display:none;" value="<?php echo esc_attr( get_option( 'admin_email' ) ); ?>" required="required" />
	</form>

	<?php
		$args = array(
			'previous_hash' => 'setup-access-keys',
			'next_hash'     => 'complete-verification',
			'next_title'    => __( 'Next: Complete Verification', 'wp-offload-ses' ),
			'step'          => 3,
		);
		$this->render_view( 'setup/nav', $args );
	?>
</div>