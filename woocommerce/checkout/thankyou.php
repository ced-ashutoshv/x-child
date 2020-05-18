<?php
/**
 * Thankyou page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/thankyou.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$show_customer_details = is_user_logged_in() && $order->get_user_id() === get_current_user_id();
?>
<div class="woocommerce-order">
	<div class="row">
		<?php if ( $order ) : ?>

			<?php if ( $order->has_status( 'failed' ) ) : ?>
				<div class="col-12 col-lg-7">
					<div class="card">
						<div class="card-body">
							<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed">
								<?php _e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce' ); ?>
							</p>
							<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
								<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?php _e( 'Pay', 'woocommerce' ) ?></a>
								<?php if ( is_user_logged_in() ) : ?>
									<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button pay"><?php _e( 'My account', 'woocommerce' ); ?></a>
								<?php endif; ?>
							</p>
						</div>
					</div>
				</div>

			<?php else : ?>
				
				<div class="col-12 col-lg-7 left-col">
					<div class="card">
						<div class="card-header thank-you">
							Thank you!
						</div>
						<div class="card-body">
							<p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received mb0">
								<strong>
									<?php echo apply_filters( 'woocommerce_thankyou_order_received_text', __( 'Thank you. Your order has been received.', 'woocommerce' ), $order ); ?>
								</strong>
							</p>
							<p class="shorter-p">You will receive an email shortly with your order details or check it status on your <a href="<?php echo wc_get_page_permalink( 'myaccount' ); ?>">Account Page</a>.</p>

							<div class="woocommerce-order-overview woocommerce-thankyou-order-details order_details">
								<?php if ( is_user_logged_in() && $order->get_user_id() === get_current_user_id() && $order->get_billing_email() ) : ?>
									<div class="woocommerce-order-overview__email email col-12">
										<h2 class="woocommerce-column__title">
											<?php _e( 'Email:', 'woocommerce' ); ?>
										</h2>
										<strong><?php echo $order->get_billing_email(); ?></strong>
									</div>
								<?php endif; ?>
								<?php
								if ( $show_customer_details ) {
									wc_get_template( 'order/order-details-customer.php', array( 'order' => $order ) );
								}
								?>
								<div class="row">
									<div class="woocommerce-order-overview__order order col-6">
										<h2 class="woocommerce-column__title">
											<?php _e( 'Order number:', 'woocommerce' ); ?>
										</h2>
										<strong><?php echo $order->get_order_number(); ?></strong>
									</div>
									<div class="woocommerce-order-overview__date date col-6">
										<h2 class="woocommerce-column__title">
											<?php _e( 'Date:', 'woocommerce' ); ?>
										</h2>
										<strong><?php echo wc_format_datetime( $order->get_date_created() ); ?></strong>
									</div>
									
								</div>
								<div class="woocommerce-order-overview__total total">
									<h2 class="woocommerce-column__title">
										<?php _e( 'Total:', 'woocommerce' ); ?>
									</h2>
									<strong><?php echo $order->get_formatted_order_total(); ?></strong>
								</div>

								<?php if ( $order->get_payment_method_title() ) : ?>
									<div class="woocommerce-order-overview__payment-method method">
										<h2 class="woocommerce-column__title">
											<?php _e( 'Payment method:', 'woocommerce' ); ?>
										</h2>
										<strong><?php echo wp_kses_post( $order->get_payment_method_title() ); ?></strong>
									</div>
								<?php endif; ?>
							</div>
							<?php do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() ); ?>
						</div>
					</div>
				</div>
			<?php endif; ?>
				<div class="col-12 col-lg-5 right-col">
					<?php do_action( 'woocommerce_thankyou', $order->get_id() ); ?>
				</div>
		<?php else : ?>
			<p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received">
				<?php echo apply_filters( 'woocommerce_thankyou_order_received_text', __( 'Thank you. Your order has been received.', 'woocommerce' ), null ); ?>
			</p>
		<?php endif; ?>
	</div>
</div>

<p class="before-you-go">
	Wait! Before You Go...
</p>

<div class="banners">
	<div class="banner left-banner">
		<a href="/knowledge-center/" class="link"></a>
		<div class="content">
			<h3 class="title" class="link">Get started before<br> your order arrives!</h3>
			<p style="font-weight: 600;">Explore Knowledge Center</p>
		</div>
	</div>
	<div class="banner right-banner">
		<a href="/#onetreeplantedpartnership" class="link"></a>
 		<div class="content">
			<h3 class="title">Every supplement kit sold<br> helps plant a tree!</h3>
			<p>Learn about YouVeda's partnership with <br><strong>One Tree Planted</strong></p>
			<p style="font-weight: 600;color: #008080;">Read More</p>
		</div>
	</div>
</div>

<div class="text-center">
	<a href="/" class="take-me-back">Take Me Back Home</a>
</div>
