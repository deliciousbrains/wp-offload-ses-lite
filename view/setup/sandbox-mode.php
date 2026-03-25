<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div id="tab-sandbox-mode"  data-prefix="wposes" class="wposes-tab wposes-content">
	<h2><?php esc_html_e( 'Sandbox Mode', 'wp-offload-ses' ); ?></h2>

	<p><?php esc_html_e( 'By default, all new Amazon SES accounts are placed into sandbox mode, which means that you can only send emails to email addresses and domains that are verified with Amazon. To move out of sandbox mode and send emails to any address, you\'ll need to request production access from Amazon.', 'wp-offload-ses' ); ?></p>

	<ol>
		<li>
			<?php
			echo wp_kses(
				sprintf(
					__( 'Log in to the <a href="%1$s" target="_blank">Amazon SES Console</a> and make sure you are in the region <strong>%2$s</strong> that you selected in the previous step.', 'wp-offload-ses' ),
					'https://console.aws.amazon.com/ses/',
					'<span class="wposes-region"></span>'
				),
				array(
					'a' => array( 'href' => array(), 'target' => array() ),
					'strong' => array(),
					'span' => array( 'class' => array() )
				)
			);
			?>
		</li>
		<li><?php echo wp_kses( __( 'In the left navigation menu, click <strong>Account dashboard</strong>.', 'wp-offload-ses' ), array( 'strong' => array() ) ); ?></li>
		<li><?php echo wp_kses( __( 'In the <strong>Account dashboard</strong> underneath the page header should be a warning notice titled "Your Amazon SES account is in the sandbox", click the <strong>View Get Set up page</strong> button.', 'wp-offload-ses' ), array( 'strong' => array() ) ); ?></li>
		<li><?php echo wp_kses( __( 'On the <strong>Get set up</strong> page, click the <strong>Request production access</strong> button, found in the <strong>Request production access</strong> panel of the <strong>Open tasks</strong> section.', 'wp-offload-ses' ), array( 'strong' => array() ) ); ?></li>
		<li>
			<?php echo wp_kses( __( 'In the <strong>Request details</strong> form within the <strong>Request production access</strong> page, provide the following information:', 'wp-offload-ses' ), array( 'strong' => array() ) ); ?>
			<ul>
				<li><?php echo wp_kses( __( '<strong>Mail Type:</strong> Select "Transactional".', 'wp-offload-ses' ), array( 'strong' => array() ) ); ?></li>
				<li><?php echo wp_kses( __( '<strong>Website URL:</strong> Enter your website URL.', 'wp-offload-ses' ), array( 'strong' => array() ) ); ?></li>
				<li><?php echo wp_kses( __( '<strong>Additional Contacts (optional):</strong> Enter any additional email addresses that should receive notifications about your Amazon SES account.', 'wp-offload-ses' ), array( 'strong' => array() ) ); ?></li>
				<li><?php echo wp_kses( __( '<strong>Preferred Contact Language:</strong> Select your preferred language for receiving notifications from Amazon SES.', 'wp-offload-ses' ), array( 'strong' => array() ) ); ?></li>
			</ul>
		</li>
		<li><?php echo wp_kses( __( 'Review your information, agree to the <strong>AWS Service Terms</strong> and <strong>Acceptable Use Policy</strong>, then submit the request.', 'wp-offload-ses' ), array( 'strong' => array() ) ); ?></li>
	</ol>

	<p><?php esc_html_e( 'Amazon will review your request and typically responds within 24 hours. You will receive an email notification when your request has been processed.', 'wp-offload-ses' ); ?></p>

	<p><strong><?php esc_html_e( 'Note:', 'wp-offload-ses' ); ?></strong> <?php esc_html_e( 'While your account is in sandbox mode, you can still verify senders and complete the plugin setup. You\'ll just need to verify any email address you want to send to until production access is granted.', 'wp-offload-ses' ); ?></p>

	<p><?php esc_html_e( 'You can continue setting up this plugin by proceeding to the next step.', 'wp-offload-ses' ); ?></p>

	<?php
		$args = array(
			'previous_hash' => 'complete-verification',
			'next_hash'     => 'configure-wp-offload-ses',
			'next_title'    => __( 'Next: Configure WP Offload SES', 'wp-offload-ses' ),
			'step'          => 5,
		);
		$this->render_view( 'setup/nav', $args );
	?>
</div>
