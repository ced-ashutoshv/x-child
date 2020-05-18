<?php
/**
 * Output a single payment method
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/payment-method.php.
 *
 * The WooCommerce payment-method template has been splited in 2 templates
 * One for the button/option and the second for the option content itself
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce/Templates
 * @version     3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$is_selected_css_class = $gateway->chosen ? 'checked' : '';
?>
<li class="wc_payment_method payment_method_<?php echo esc_attr( $gateway->id ) . ' ' . $is_selected_css_class; ?>">
	<div class="d-none">
		<input 	id="payment_method_<?php echo esc_attr( $gateway->id ); ?>" 
				type="radio" 
				class="input-radio" 
				name="payment_method" 
				value="<?php echo esc_attr( $gateway->id ); ?>" 
				<?php checked( $gateway->chosen, true ); ?> 
				data-order_button_text="<?php echo esc_attr( $gateway->order_button_text ); ?>" />
		<?php echo $gateway->get_title(); /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
	</div>
	<label for="payment_method_<?php echo esc_attr( $gateway->id ); ?>">
		<?php echo $gateway->get_icon(); /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
	</label>
</li>
