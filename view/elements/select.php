<?php
$value    = ( isset( $value ) ) ? $value : $this->settings->get_setting( $key );
$class    = ( isset( $class ) ) ? 'class="' . $class . '"' : '';
$disabled = ( isset( $disabled ) && $disabled ) ? ' disabled' : '';
$options  = ( isset( $options ) ) ? $options : array();
?>
<select name="<?php echo $key; ?>"<?php echo $disabled; ?>>
	<?php
		foreach ( $options as $option_value => $option_text ) {
			$selected = '';

			if ( $option_value === $value ) {
				$selected = 'selected';
			}

			echo '<option value="' . esc_attr( $option_value ) . '" ' . $selected . '>' . esc_attr( $option_text ) . '</option>';
		}
	?>
</select>
