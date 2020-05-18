<?php
/**
 * Product quantity inputs
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/global/quantity-input.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

if ( $max_value && $min_value === $max_value ) {
	?>
	<div class="quantity hidden">
		<input type="hidden" id="<?php echo esc_attr( $input_id ); ?>" class="qty" name="<?php echo esc_attr( $input_name ); ?>" value="<?php echo esc_attr( $min_value ); ?>" />
	</div>
	<?php
} else {
	/* translators: %s: Quantity. */
	$labelledby = ! empty( $args['product_name'] ) ? sprintf( __( '%s quantity', 'woocommerce' ), strip_tags( $args['product_name'] ) ) : '';
	?>
	<div class="quantity_select add_to_cart_select">
		<select 
			name="<?php echo esc_attr( $input_name ); ?>" 
			title="<?php _ex( 'Qty', 'Product quantity input tooltip', 'woocommerce' ) ?>" 
			class="qty"
			id="<?php echo esc_attr( $input_id ); ?>">
		<?php
		$max_value = $max_value ? $max_value : 10;
		for ( $count = $min_value; $count <= $max_value; $count = $count+$step ) {
			if ( $count == $input_value )
				$selected = ' selected';
			else $selected = '';
			echo '<option value="' . esc_attr( $count ) . '"' . $selected . '>' . esc_attr( $count ) . '</option>';
		}
		?>
		</select>
		<?php 
		if (is_product()){
			echo '<i class="fa fa-chevron-down"></i>';
		} 
		?>
	</div>
	<?php
}
