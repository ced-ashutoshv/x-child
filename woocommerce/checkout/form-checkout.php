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
 
//AMEBA NOTES

// Encapsulé el siguiente codigo con un div, para centrar el texto.: 

	// If checkout registration is disabled and not logged in, the user cannot checkout.
//	if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
//		echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
//		return;
//	} 



//	SUPPER IMPORTANT:
//	'.left-container' and '.right-container' classes have been added in order to apply some css styles AND some js functionality for both containers. 
//	It is supper important to mantain this classes attached if this template is updated. You have been warned.
//	

//	<div class="place-order-footer"></div> and its contents were added by us (ameba)
// '.row-email-address' was added in order to hide the email if user is not logged in




if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( isset( $_GET['default_template'] ) ) {//phpcs:ignore
	wc_get_template( 'checkout/__form-checkout.php', array( 'checkout' => $checkout ) );
	return;
}
do_action( 'woocommerce_before_checkout_form', $checkout );


// If checkout registration is disabled and not logged in, the user cannot checkout.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo '<div class="text-center">';
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	echo '</div>';
	return;
}

?>

<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">
	<div class="left-right-container row mt40px mb40px">
		<div class="col-12 col-md-6 col-lg-7 left-container">
			<div class="card">
				<div class="card-header">
					Checkout Process
				</div>
				<div class="card-body">
					<?php
					$checkout_fields = $checkout->get_checkout_fields();
					if ( $checkout_fields ) :
						?>
						<div class="row row-email-address">
							<div class="col-12 col-lg-6 checkout-section-title-collapsable">
								<?php
									/* translators: %s: Email address */
									echo wp_kses_post( sprintf( '<h4 class="checkout_valid_step">%s</h4>', __( 'Email address', 'woocommerce' ) ) );
								?>
							</div>
							<div class="col-12 col-lg-6 usremail">
								<?php
									echo esc_html( sanitize_email( $checkout->get_value( 'billing_email' ) ) );
								?>
							</div>
						</div>
						
						<?php //do_action( 'woocommerce_checkout_before_customer_details' ); payment options on top, no longer needed. ?>
						<div class="row checkout-section-wrapper active-checkout-section" id="customer_details">
							<div class="col-12">
								<hr class="hr-separator">
							</div>
							<div class="col-12">
								<div class="row">
									<div class="col-10 checkout-section-title-collapsable">
										<?php
											/* translators: %s: Billing / Shipping Address */
											echo wp_kses_post( sprintf( '<h4 class="">%s</h4>', __( 'Shipping Address', 'woocommerce' ) ) );
										?>
									</div>
									<div class="col-2">
										<a href="#" class="edit-checkout-section toggle-element collapsable collapsed">Edit</a>
									</div>
								</div>
							</div>

							<div class="col-12 collapsable">
								<?php do_action( 'woocommerce_checkout_shipping' ); ?>
							</div>

							<?php if ( ! is_amazon_payment_selected() ) { ?>
							<div class="col-12 toggle-element collapsable collapsed">
								<div class="row">
									<div class="col-12 col-sm-6">
										<h3>Billing Address</h3>
										<ul class="saved-checkout-data billing-details">
										</ul>										
									</div>
									<div class="col-12 col-sm-6">
										<h3>Shipping Address</h3>
										<ul class="saved-checkout-data shipping-details">
										</ul>										
									</div>
								</div>
							</div>
							<?php } ?>
						</div>

						<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>
						<?php do_action( 'woocommerce_checkout_before_customer_details' ); /* added payment options at the bottom now */ ?>

						<div class="row checkout-section-wrapper active-checkout-section" id="customer_details_billing">
							<div class="col-12 collapsable">
								<?php do_action( 'woocommerce_checkout_billing' ); ?>
							</div>
						</div>

					<?php endif; ?>
				</div>
			</div>
			<div class="place-order-footer">
				<button type="submit" class="button mbl mtl button-place-order-copy" name="woocommerce_checkout_place_order" id="place_order" value="Place order" data-value="Place order">Place order</button>
			</div>
		</div>
		<div class="col-12 col-md-6 col-lg-5 right-container">
			<div class="card">
				<div class="card-header">
					<div class="row">
						<div class="col-8">
							<h3 id="order_review_heading"><?php esc_html_e( 'Order summary', 'woocommerce' ); ?></h3>
						</div>
						<div class="col-4">
							<span class="count">
								<?php /* translators: %d: number of items in cart */ ?>
								<?php echo wp_kses_data( sprintf( _n( '%d item', '%d items', WC()->cart->get_cart_contents_count(), 'storefront' ), WC()->cart->get_cart_contents_count() ) ); ?>
							</span>
							<?php
							if(wp_is_mobile()){
								//echo '<a href="#" class="view-collapsed-data">View</a>';
							} else { ?>
								<!--<a class="cart-contents" href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php esc_attr_e( 'View your shopping cart', 'storefront' ); ?>">
									Edit
								</a>-->
							<?php } ?>
							
						</div>
					</div>
				</div>
				<div class="card-body">
					<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>
					<div id="order_review" class="woocommerce-checkout-review-order">
						<?php do_action( 'woocommerce_checkout_order_review' ); ?>
					</div>
					<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
				</div>
			</div>
		</div>
	</div>
</form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
