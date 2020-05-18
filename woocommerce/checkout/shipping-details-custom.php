<?php
/**
 * Output shipping details on chackout
 *
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce/Templates
 * @version     3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$packages        = WC()->shipping->get_packages();
$chosen_method   = isset( WC()->session->chosen_shipping_methods[ 0 ] ) ? WC()->session->chosen_shipping_methods[ 0 ] : '';
$selected_method =  false;
$label         = '';
$price         = '-';
$has_calculated_shipping  = ! empty( WC()->customer->has_calculated_shipping() );

foreach ( $packages as $index => $package ) {
	if ( ! empty( $package['rates'] ) ) {
		foreach ( $package['rates'] as $method ) {
			if ( $method->id === $chosen_method ) {
				$price = sprintf( '%1$s', wc_cart_totals_shipping_method_label( $method ) ); // WPCS: XSS ok.
				$label = $method->get_label();
				$selected_method = $method;
				
				break;
			}
		}
	}
}
?>
<div class="row shipping_fee">
	<div class="col-6">
		<?php
			printf( '%1$s: %2$s', __( 'Shipping', 'woocommerce' ), $label );
		?>
	</div>
	<div class="col-6">
		<?php
		if( ! $has_calculated_shipping ) {
			echo wp_kses_post( apply_filters( 'woocommerce_shipping_may_be_available_html', __( 'Enter your address to view shipping options.', 'woocommerce' ) ) );
		} else { 
			if ( $selected_method && in_array( $selected_method->get_method_id(), array( 'free_shipping', 'local_pickup' ), true ) ) {
					$price = wc_price( $method->cost );
			}  else {
				$price = trim( str_replace( $label . ': ', '', $price ) );
			}
			echo wp_kses_post( $price );
		}
		?>
	</div>
</div>



