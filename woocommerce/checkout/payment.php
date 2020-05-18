<?php
/**
 * Checkout Payment Section
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/payment.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.3
 */


/*
	SOME MODIFICATIONS MADE BY AMEBA :

	1 - removed 'collapsed' class from the '.collapsable' second div inside #payment_details
	2 - added custom button ".save-card-info"
 */

defined( 'ABSPATH' ) || exit;

// Add Amazon to the list of options so we can show the icon next to the other options.
$amazon_gateway = return_amazon_gateway();
if ( $amazon_gateway && ! is_amazon_payment_selected() ) {
	$last_gateway         = end( $available_gateways );
	$available_gateways   = array_slice( $available_gateways, 0, count( $available_gateways ) - 1 );
	$available_gateways[] = $amazon_gateway;
	$available_gateways[] = $last_gateway;
}

if ( ! is_ajax() ) {


	do_action( 'woocommerce_review_order_before_payment' ); ?>

	<div class="row checkout-section-wrapper" id="payment_details">
		<div class="col-12">
			<hr class="hr-separator">
		</div>
		<div class="col-12">
			<div class="row">
				<div class="col-10 checkout-section-title-collapsable">
					<?php
						/* translators: %s: Billing / Shipping Address */
						echo wp_kses_post( sprintf( '<h4 class="">%s</h4>', __( 'Payment method', 'woocommerce' ) ) );
					?>
				</div>
				<div class="col-2">
					<a href="#" class="edit-checkout-section toggle-element collapsable collapsed default-hidden">Edit</a>
				</div>
			</div>
		</div>
		<div class="col-12 collapsable">
		<?php
		do_action( 'yv_review_order_before_payment' );
}
?>
		<div id="payment" class="woocommerce-checkout-payment">
			<?php if ( WC()->cart->needs_payment() ) : ?>
				<ul class="wc_payment_methods payment_methods methods">
					<?php
					if ( ! empty( $available_gateways ) ) {
						foreach ( $available_gateways as $gateway ) {
							wc_get_template( 'checkout/payment-method.php', array( 'gateway' => $gateway ) );
						}
					} else {
						echo '<li class="woocommerce-notice woocommerce-notice--info woocommerce-info">' . apply_filters( 'woocommerce_no_available_payment_methods_message', WC()->customer->get_billing_country() ? esc_html__( 'Sorry, it seems that there are no available payment methods for your state. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce' ) : esc_html__( 'Please fill in your details above to see available payment methods.', 'woocommerce' ) ) . '</li>'; // @codingStandardsIgnoreLine
					}
					?>
				</ul>
				<?php
				if ( ! empty( $available_gateways ) ) {
					foreach ( $available_gateways as $gateway ) {
						wc_get_template( 'checkout/payment-method-content.php', array( 'gateway' => $gateway ) );
					}
				}
				?>
			<?php endif; ?>
			<div class="form-row place-order">
				<noscript>
					<?php
					/* translators: $1 and $2 opening and closing emphasis tags respectively */
					printf( esc_html__( 'Since your browser does not support JavaScript, or it is disabled, please ensure you click the %1$sUpdate Totals%2$s button before placing your order. You may be charged more than the amount stated above if you fail to do so.', 'woocommerce' ), '<em>', '</em>' );
					?>
					<br/><button type="submit" class="button alt" name="woocommerce_checkout_update_totals" value="<?php esc_attr_e( 'Update totals', 'woocommerce' ); ?>"><?php esc_html_e( 'Update totals', 'woocommerce' ); ?></button>
				</noscript>

				<?php wc_get_template( 'checkout/terms.php' ); ?>

				<?php do_action( 'woocommerce_review_order_before_submit' ); ?>

				<?php echo apply_filters( 'woocommerce_order_button_html', '<button type="submit" class="button alt" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr( $order_button_text ) . '" data-value="' . esc_attr( $order_button_text ) . '">' . esc_html( $order_button_text ) . '</button>' ); // @codingStandardsIgnoreLine ?>

				<?php do_action( 'woocommerce_review_order_after_submit' ); ?>

				<?php wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' ); ?>

				<!-- Custom button for saving card information -->
				<a class="button save-card-info" id="save-card-info">Save Payment Information</a>
			</div>
		</div>
<?php
if ( ! is_ajax() ) {
	?>
	</div>
	<?php
	do_action( 'woocommerce_review_order_after_payment' );
	?>
	</div>
	<?php
}

