<div id="tab-support" class="wposes-content wposes-tab">
<?php
if ( ! $this->is_pro() ) {
	$this->render_view( 'wordpress-org-support' );
}

do_action( 'wposes_support_pre_debug' );

$this->render_view( 'debug-info' );

do_action( 'wposes_support_post_debug' );

?>
</div>