<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use DeliciousBrains\WP_Offload_SES\Activity_List_Table;
$table = new Activity_List_Table();
?>

<div id="tab-activity" data-prefix="wposes" class="wposes-tab wposes-content">
	<span class="helper-message bottom wposes-upgrade-helper">
		<?php echo wp_kses( sprintf( __( '<a href="%s">Upgrade</a> to WP Offload SES to search, view, cancel, and resend emails.', 'wp-offload-ses' ), $this->dbrains_url( '/wp-offload-ses/' ) ), array( 'a' => array( 'href' => array() ) ) ); ?>
	</span>
	<div id="wposes-views-wrap"><?php $table->render_views(); ?></div>
	<?php
	$table->prepare_items();
	$table->display();
	$this->render_view( 'modals/view-email' );
	$this->render_view( 'modals/delete-email' );
	?>
</div>
