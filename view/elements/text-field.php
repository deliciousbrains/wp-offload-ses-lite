<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$type     = ( isset( $type ) ) ? $type : 'text';
$multiple = ( isset( $multiple ) && $multiple ) ? 'multiple' : '';
$required = ( isset( $required ) && $required ) ? 'required="required"' : '';
$class    = ( isset( $class ) ) ? 'class="' . esc_attr( $class ) . '"' : '';
$disabled = ( isset( $disabled ) && $disabled ) ? ' disabled' : '';
$prefix   = $this->get_plugin_prefix_slug();
$value    = isset( $value ) ? $value : '';
$saved    = $this->settings->get_setting( $key, false );
$value    = $saved ? $saved : $value;
?>

<input name="<?php echo esc_attr( $key ); ?>" type="<?php echo esc_attr( $type ); ?>" <?php echo wp_kses_post( $class ); ?> value="<?php echo esc_attr( $value ); ?>" <?php echo esc_attr( $disabled ); ?> <?php echo esc_attr( $multiple ); ?> <?php echo wp_kses_post( $required ); ?> />