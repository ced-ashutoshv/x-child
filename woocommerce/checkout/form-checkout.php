<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* Add order review for mobile view here */
if( function_exists( 'wp_is_mobile' ) && wp_is_mobile() ) : ?>

	<div id="order_review" class="woocommerce-checkout-review-order">
		<a class="order_review_heading_toggle" title="Click here to show order details." status="hidden" href="javascript:void(0);">
			<h3 id="order_review_heading">
				<span class="order_review_text">
					<?php esc_html_e( 'Order Summary', 'woocommerce' ); ?>
				</span>
				<span class="order_review_arrow_icon">
					<img class="order_review_arrow_img" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAABmJLR0QA/wD/AP+gvaeTAAAAb0lEQVRIie2PsQ2AMAwELyyWcVIyCqSCaQmNI1kIRwEF0fjq998bHMf5lQDEjlyU7OPyDBzA3MglyeyWZGocFzlaDEkCNqu4hwCsIrp+UpcXGuvfSoaVa0lWklqeR5RrSf1k2HJL8km5lnxW7jj3nAwCHOMFhaBLAAAAAElFTkSuQmCC"/>
				</span>
				<span class="order_review_cart_total">
					<?php echo function_exists( 'WC' ) && ! empty( WC()->cart ) ? WC()->cart->get_cart_total() :false; ?>
				</span>
			</h3>
		</a>
		<?php do_action( 'woocommerce_checkout_order_review' ); ?>
		<?php do_action( 'woocommerce_checkout_order_review_extended' ); ?>
		</div>
	<?php do_action( 'woocommerce_checkout_after_order_review' );

endif;

do_action( 'woocommerce_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, the user cannot checkout.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}

?>

<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

	<?php if ( $checkout->get_checkout_fields() ) : ?>

		<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

		<div class="col2-set" id="customer_details">
			<div class="col-1">
				<?php do_action( 'woocommerce_checkout_shipping' ); ?>
			</div>

			<div class="payment-fields-wrap">
				<h3 id="order_review_heading"><?php esc_html_e( 'Payment Methods', 'woocommerce' ); ?></h3>
				<?php do_action( 'hook_woocommerce_checkout_payment' ); ?>
			</div>

			<div class="col-2">
				<?php do_action( 'woocommerce_checkout_billing' ); ?>
			</div>
		</div>

		<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

	<?php endif; ?>
	
	<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>
	
	<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

	<?php if ( ! function_exists( 'wp_is_mobile' ) || ! wp_is_mobile() ) : ?>
		<div id="order_review" class="woocommerce-checkout-review-order">
			<h3 id="order_review_heading">
				<span class="order_review_text">
					<?php esc_html_e( 'Order Summary', 'woocommerce' ); ?>
				</span>
				<span class="order_review_cart_total">
					<?php echo function_exists( 'WC' ) && ! empty( WC()->cart ) ? WC()->cart->get_cart_total() :false; ?>
				</span>
			</h3>
			<?php do_action( 'woocommerce_checkout_order_review' ); ?>
		</div>
		<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
	<?php endif; ?>

	<div class="form-row place-order mwb_youveda_place_order">
		<noscript>
			<?php
			/* translators: $1 and $2 opening and closing emphasis tags respectively */
			printf( esc_html__( 'Since your browser does not support JavaScript, or it is disabled, please ensure you click the %1$sUpdate Totals%2$s button before placing your order. You may be charged more than the amount stated above if you fail to do so.', 'woocommerce' ), '<em>', '</em>' );
			?>
			<br/><button type="submit" class="button alt" name="woocommerce_checkout_update_totals" value="<?php esc_attr_e( 'Update totals', 'woocommerce' ); ?>"><?php esc_html_e( 'Update totals', 'woocommerce' ); ?></button>
		</noscript>

		<?php wc_get_template( 'checkout/terms.php' ); ?>

		<?php do_action( 'woocommerce_review_order_before_submit' ); ?>

		<?php $order_button_text = 'Place order'; ?>
		<?php echo apply_filters( 'woocommerce_order_button_html_custom', '<button type="submit" class="button alt mwb_youveda_place_order_button" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr( $order_button_text ) . '" data-value="' . esc_attr( $order_button_text ) . '">' . esc_html( $order_button_text ) . '</button>' ); // @codingStandardsIgnoreLine ?>

		<?php do_action( 'woocommerce_review_order_after_submit' ); ?>

		<?php wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' ); ?>
	</div>

</form>
<div class="mwb_yv_extended_sections">
	<?php do_action( 'woocommerce_after_order_review_section' ); ?>
	<?php do_action( 'woocommerce_checkout_order_review_extended' ); ?>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
