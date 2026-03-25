<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$aws                  = $this->get_aws();
$key_constant         = $aws->access_key_id_constant();
$secret_constant      = $aws->secret_access_key_constant();
$any_constant_defined = (bool) $key_constant || $secret_constant;
$hide_form_initially  = false;
$database_warning_url = $this->dbrains_url( '/wp-offload-ses/doc/quick-start-guide/#save-access-keys', array(
	'utm_campaign' => 'support+docs',
) );
?>

<section class="wposes-access-keys">
	<img class="wposes-aws-logo alignleft" src="<?php echo esc_url( $this->plugins_url( 'assets/img/aws-logo.svg' ) ); ?>" alt="" width="75" height="75">
	<h3 class="wposes-section-heading"><?php esc_html_e( 'AWS Access Keys', 'wp-offload-ses' ) ?></h3>

	<?php if ( $aws->use_ec2_iam_roles() ) : ?>
		<p>
			<?php esc_html_e( 'You have enabled the use of IAM roles for Amazon EC2 instances.', 'wp-offload-ses' ) ?>
		</p>
	<?php elseif ( $any_constant_defined ) : ?>

		<?php if ( ! $aws->are_access_keys_set() ) : ?>
			<div class="notice-error notice">
				<p>
					<?php esc_html_e( 'Please check your wp-config.php file as it looks like one of your defines is missing or incorrect.', 'wp-offload-ses' ) ?>
				</p>
			</div>
		<?php endif ?>

		<p>
			<?php echo wp_kses( sprintf( __( 'You&#8217;ve already defined your AWS access keys in your wp-config.php. If you&#8217;d prefer to manage them here and store them in the database (<a href="%s">not recommended</a>), simply remove the lines from your wp-config.', 'wp-offload-ses' ), $database_warning_url ), array( 'a' => array( 'href' => array() ) ) ); ?>
		</p>

	<?php else : // no access keys defined & not using IAM roles ?>

		<p>
			<?php esc_html_e( 'We recommend defining your Access Keys in wp-config.php so long as you don&#8217;t commit it to source control (you shouldn&#8217;t be). Simply copy the following snippet and replace the stars with the keys.', 'wp-offload-ses' ) ?>
		</p>

		<textarea rows="2" class="wposes-access-key-constants-snippet code clear" readonly>
define( 'WPOSES_AWS_ACCESS_KEY_ID',     '********************' );
define( 'WPOSES_AWS_SECRET_ACCESS_KEY', '**************************************' );
		</textarea>

		<?php if ( $aws->get_access_key_id() || $aws->get_secret_access_key() ) : ?>
			<p>
				<?php echo wp_kses( sprintf( __( 'You&#8217;re storing your Access Keys in the database (<a href="%s">not recommended</a>).</a>', 'wp-offload-ses' ), esc_url( $database_warning_url ) ), array( 'a' => array( 'href' => array() ) ) ); ?>
			</p>
		<?php else : $hide_form_initially = true ?>
			<p class="reveal-form">
				<?php echo wp_kses( __( 'If you&#8217;d rather store your Access Keys in the database, <a href="#" data-wposes-toggle-access-keys-form>click here to reveal a form.</a>', 'wp-offload-ses' ), array( 'a' => array( 'href' => array(), 'data-wposes-toggle-access-keys-form' => array() ) ) ); ?>
			</p>
		<?php endif ?>

	<?php endif ?>

	<div id="wposes_access_keys" style="<?php echo $hide_form_initially ? 'display: none;' : '' ?>">
		<form method="post">
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Access Key ID', 'wp-offload-ses' ) ?></th>
					<td>
						<div class="wposes-field-wrap <?php echo $key_constant ? 'wposes-defined' : '' ?>">
							<input type="text"
									name="aws-access-key-id"
									value="<?php echo esc_attr( $aws->get_access_key_id() ) ?>"
									autocomplete="off"
								<?php echo $key_constant ? 'disabled' : '' ?>
							>
							<?php if ( $key_constant ) : ?>
								<span class="wposes-defined-in-config"><?php esc_html_e( 'defined in wp-config.php', 'wp-offload-ses' ) ?></span>
							<?php endif ?>
						</div>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Secret Access Key', 'wp-offload-ses' ) ?></th>
					<td>
						<div class="wposes-field-wrap <?php echo $secret_constant ? 'wposes-defined' : '' ?>">
							<input type="text"
									name="aws-secret-access-key"
									value="<?php echo esc_attr( $aws->get_secret_access_key() ? _x( '-- not shown --', 'placeholder for hidden access key, 39 char max', 'wp-offload-ses' ) : '' ); ?>"
									autocomplete="off"
								<?php echo $secret_constant ? 'disabled' : '' ?>
							>
							<?php if ( $secret_constant ) : ?>
								<span class="wposes-defined-in-config"><?php esc_html_e( 'defined in wp-config.php', 'wp-offload-ses' ) ?></span>
							<?php endif ?>
						</div>
					</td>
				</tr>
			</table>

			<?php if ( ! $any_constant_defined ) : ?>
				<div class="notice inline wposes-notice-warning">
					<p>
						<?php esc_html_e( 'This will store your AWS access keys in the database (not recommended).', 'wp-offload-ses' ) ?>
						<?php echo wp_kses_post( $this->more_info_link( '/wp-offload-ses/doc/quick-start-guide/#save-access-keys' ) ); ?>
					</p>
				</div>

				<div data-wposes-aws-keys-feedback class="notice inline" style="display: none;">
					<!-- response message filled here by JS -->
				</div>

				<button id="save-aws-access-keys" type="submit" class="button button-primary" data-wposes-aws-keys-action="set"><?php esc_html_e( 'Save Access Keys', 'wp-offload-ses' ) ?></button>
				<?php if ( $aws->get_access_key_id() || $aws->get_secret_access_key() ) : ?>
					<button class="button remove-keys" data-wposes-aws-keys-action="remove"><?php esc_html_e( 'Remove Keys', 'wp-offload-ses' ) ?></button>
				<?php endif ?>

				<span data-wposes-aws-keys-spinner class="spinner"></span>
			<?php endif ?>
		</form>
	</div>
</section>