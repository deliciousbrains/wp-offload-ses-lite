<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div id="tab-setup-access-keys"  data-prefix="wposes" class="wposes-tab wposes-content">

	<?php $this->render_view( 'access-keys' ); ?>

	<form id="wposes-setup-region">
		<h2><?php esc_html_e( 'Region', 'wp-offload-ses' ); ?></h2>
		<table class="form-table">
			<?php $this->render_view( 'settings/region' ); ?>
		</table>
	</form>

	<?php
		$args = array(
			'previous_hash' => 'create-iam-user',
			'next_hash'     => 'verify-sender',
			'next_title'    => __( 'Next: Verify Sender', 'wp-offload-ses' ),
			'step'          => 2,
		);
		$this->render_view( 'setup/nav', $args );
	?>
</div>