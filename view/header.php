<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="wrap wposes-main <?php echo esc_attr( $this->is_pro() ? 'wposes-pro' : 'wposes-lite' ); ?>" data-view="<?php echo esc_attr( $page ); ?>">

	<h1><?php echo esc_html( $page_title ); ?></h1>