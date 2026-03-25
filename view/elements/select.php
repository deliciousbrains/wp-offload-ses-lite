<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$value    = ( isset( $value ) ) ? $value : $this->settings->get_setting( $key );
$class    = ( isset( $class ) ) ? 'class="' . esc_attr( $class ) . '"' : '';
$disabled = ( isset( $disabled ) && $disabled ) ? ' disabled' : '';
$options  = ( isset( $options ) ) ? $options : array();
?>
<select name="<?php echo esc_attr( $key ); ?>"<?php echo esc_attr( $disabled ); ?> <?php echo wp_kses_post( $class ); ?>>
	<?php
		foreach ( $options as $option_value => $option_text ) {
			?>
			<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $option_value, $value ); ?>><?php echo esc_html( $option_text ); ?></option>
			<?php
		}
	?>
</select>
