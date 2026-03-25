<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div id="tab-complete-verification" data-prefix="wposes" class="wposes-tab wposes-content">
	<h2><?php esc_html_e( 'Complete Verification', 'wp-offload-ses' ); ?></h2>

	<div class="wposes-show-domain">
		<p>
			<?php
			echo wp_kses(
				sprintf(
					__( 'The domain %1$s has been added to your Amazon SES account and is pending verification. To complete verification, you will need to update the DNS for %2$s with the following CNAME records:', 'wp-offload-ses' ),
					'<span class="wposes-sender"></span>',
					'<span class="wposes-sender"></span>'
				),
				array( 'span' => array( 'class' => array() ) )
			);
			?>
		</p>

		<ol>
			<li><?php esc_html_e( 'Log in to your DNS provider and navigate to the DNS records.', 'wp-offload-ses' ); ?></li>
			<li><?php echo wp_kses( __( 'Add new <strong>CNAME</strong> records with the below names and values.', 'wp-offload-ses' ), array( 'strong' => array() ) ); ?></li>
		</ol>

		<table class="wposes-domain-dns"></table>

		<p>
			<?php
			echo wp_kses(
				sprintf(
					__( 'For more information on updating the DNS, please see <a href="%s" target="_blank">Amazon SES Domain Verification CNAME Records</a>.', 'wp-offload-ses' ),
					esc_url( 'https://docs.aws.amazon.com/ses/latest/dg/creating-identities.html#just-verify-domain-proc' )
				),
				array( 'a' => array( 'href' => array(), 'target' => array() ) )
			);
			?>
		</p>

		<p><?php esc_html_e( 'Once updated, it can take some time for the DNS to propagate and for Amazon to verify the domain. Feel free to finish configuring the plugin in the meantime.', 'wp-offload-ses' ); ?></p>
	</div>

	<div class="wposes-show-email" style="display:none">
		<p><?php echo wp_kses( sprintf( __( 'A confirmation email has been sent from Amazon to %s.', 'wp-offload-ses' ), '<span class="wposes-sender"></span>' ), array( 'span' => array( 'class' => array() ) ) ); ?></p>
		<p><?php esc_html_e( 'Click the link in the email to add this email address as a verified sender.', 'wp-offload-ses' ); ?></p>
	</div>

	<?php
	$args = array(
		'previous_hash' => 'verify-sender',
		'next_hash'     => 'sandbox-mode',
		'next_title'    => __( 'Next: Move out of Sandbox Mode', 'wp-offload-ses' ),
		'step'          => 4,
	);
	$this->render_view( 'setup/nav', $args );
	?>
</div>
