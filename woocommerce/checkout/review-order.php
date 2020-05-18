<?php
/**
 * Review order table
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/review-order.php.
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
 * @version     3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="shop_table woocommerce-checkout-review-order-table <?php if ( wp_is_mobile() ) {echo ' mobile-data-hidden';} ?>">
	<div class="container-fluid">
		<?php
			do_action( 'woocommerce_review_order_before_cart_contents' );

			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

				if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
					?>

					<?php
					/*By Ameba*/
					if ( wp_is_mobile() ) {
						$left_col_classes = 'col-3 col-lg-5 product-thumbnail d-flex align-items-center';
						$right_col_classes = 'col-8 col-lg-7 col-xl-6';
					} else {
						$left_col_classes = 'col-6 col-lg-5 product-thumbnail d-flex align-items-center';
						$right_col_classes = 'col-6 col-lg-7 col-xl-6';
					}
					?>

					<div class="row <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
						<div class="<?php echo $left_col_classes; ?>">

							<?php
								$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
								echo wp_kses_post( $thumbnail );
							?>
						</div>
						<div class="<?php echo $right_col_classes; ?>">
							<div class="row">
								<div class="col-12 product-name">
									<?php echo apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;'; ?>

									<?php echo wc_get_formatted_cart_item_data( $cart_item ); ?>
								</div>
								<div class="col-12 product-total">
									<?php
										echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );// phpcs:ignore XSS ok.
										echo "<br>";
										echo apply_filters( 'woocommerce_checkout_cart_item_quantity', ' <span class="product-quantity">Qty: ' . sprintf( _n( '%s item', '%s items', $cart_item['quantity'], 'woocommerce' ), $cart_item['quantity'] ) . '</span>', $cart_item, $cart_item_key );
										echo "<br>";
										echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );
									?>
								</div>
							</div>
						</div>
					</div>
					<?php
				}
			}

			do_action( 'woocommerce_review_order_after_cart_contents' );
		?>
	</div>
	<div class="mobile-data-container">
		<div class="row cart-subtotal hey">
			<div class="col-6">
				<?php _e( 'Subtotal', 'woocommerce' ); ?>
					
			</div>
			<div class="col-6">
				<?php wc_cart_totals_subtotal_html(); ?>
			</div>
		</div>
		<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
			<div class="row cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
				<div class="col-6">
					<?php wc_cart_totals_coupon_label( $coupon ); ?>
				</div>
				<div class="col-6">
					<?php wc_cart_totals_coupon_html( $coupon ); ?>
				</div>
			</div>
		<?php endforeach; ?>
		<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
			<div class="row fee">
				<div class="col-6">
					<?php echo esc_html( $fee->name ); ?>
				</div>
				<div class="col-6">
					<?php wc_cart_totals_fee_html( $fee ); ?>
				</div>
			</div>
		<?php endforeach; ?>
		<?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
			<?php if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
				<?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : ?>
					<div class="row tax-rate tax-rate-<?php echo sanitize_title( $code ); ?>">
						<div class="col-6">
							<?php echo esc_html( $tax->label ); ?>
						</div>
						<div class="col-6">
							<?php echo wp_kses_post( $tax->formatted_amount ); ?>
						</div>
					</div>
				<?php endforeach; ?>
			<?php else : ?>
				<div class="row tax-total">
					<div class="col-6">
						<?php echo esc_html( WC()->countries->tax_or_vat() ); ?>
					</div>
					<div class="col-6">
						<?php wc_cart_totals_taxes_total_html(); ?>
					</div>
				</div>
			<?php endif; ?>
		<?php endif; ?>
		<?php do_action( 'woocommerce_review_order_before_order_total' ); ?>
		<table class="yv_shipping_options d-none">
			<tfoot>
				<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>

					<?php do_action( 'woocommerce_review_order_before_shipping' ); ?>

					<?php wc_cart_totals_shipping_html(); ?>

					<?php do_action( 'woocommerce_review_order_after_shipping' ); ?>

				<?php endif; ?>
			</tfoot>
		</table>
	</div> <!-- /.mobile-data-container -->
	<div class="row order-total">
		<div class="col-6">
			<?php _e( 'Total', 'woocommerce' ); ?>
		</div>
		<div class="col-6">
			<?php wc_cart_totals_order_total_html(); ?>
		</div>
	</div>
	 
	<table class="d-none">
	<?php do_action( 'woocommerce_review_order_after_order_total' ); ?>
	</table>
</div>