<?php
use DeliciousBrains\WP_Offload_SES\Verified_Senders_List_Table;
?>
<div id="tab-verified-senders" data-prefix="wposes" class="wposes-tab wposes-content">

	<h3><?php _e( 'Verified Senders', 'wp-offload-ses' ); ?></h3>
	<div class="wposes-verified-senders-table-wrap">
		<?php 
			$table = new Verified_Senders_List_Table;
			$table->prepare_items();
			$table->display();
		?>
	</div>

	<?php $this->render_view( 'modals/verify-sender' ); ?>
</div>
