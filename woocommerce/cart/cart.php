<?php
/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
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

wc_print_notices();

do_action( 'woocommerce_before_cart' ); ?>
<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
<div class="row mt25px mb40px general-wrapper">
	<div class="col-12 col-lg-7 left-container">
		<div class="card">
			<div class="card-header">
				<h3><?php the_title(); ?></h3>
			</div>
			<div class="card-body">
				<div class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
					<?php
					do_action( 'woocommerce_before_cart_table' );
					foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
						$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
						$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
						if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
							$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
							$tr_css_class      = apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key );
						}
						?>
						<div class="row woocommerce-cart-form__cart-item <?php echo esc_attr( $tr_css_class ); ?>">
							<div class="col-auto product-thumbnail d-flex align-items-center">
								<?php
								$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
								if ( ! $product_permalink ) {
									echo wp_kses_post( $thumbnail );
								} else {
									printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), wp_kses_post( $thumbnail ) );
								}
								?>
							</div>
							<div class="col col-product-container">
								<div class="product-container">	
									<div class="product-name" data-title="<?php esc_attr_e( 'Product', 'woocommerce' ); ?>">
										<?php
										if ( ! $product_permalink ) {
											echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;' );
										} else {
											echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
										}

										do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );

										// Meta data.
										echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore XSS ok.

										// Backorder notification.
										if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
											echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>' ) );
										}
										?>
									</div>
									<div class="product-price" data-title="<?php esc_attr_e( 'Price', 'woocommerce' ); ?>">
										<?php
											echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );// phpcs:ignore XSS ok.
										?>
									</div>
									<div class="product-quantity" data-title="<?php esc_attr_e( 'Quantity', 'woocommerce' ); ?>">
										<?php
										if ( $_product->is_sold_individually() ) {
											$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
										} else {
											$product_quantity = woocommerce_quantity_input(
												array(
													'input_name'   => "cart[{$cart_item_key}][qty]",
													'input_value'  => $cart_item['quantity'],
													'max_value'    => $_product->get_max_purchase_quantity(),
													'min_value'    => '0',
													'product_name' => $_product->get_name(),
												),
												$_product,
												false
											);
										}
										echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // phpcs:ignore XSS ok.
										?>

										<div class="plus-minus-icons">
											<span class="plus-icon">
												<i class="x-icon x-icon-plus" data-x-icon-s="" aria-hidden="true"></i>
											</span>
											<span class="minus-icon">
												<i class="x-icon x-icon-minus" data-x-icon-s="" aria-hidden="true"></i>
											</span>
										</div>
									</div>
									<div class="product-subtotal" data-title="<?php esc_attr_e( 'Total', 'woocommerce' ); ?>">
										<?php
											echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // phpcs:ignore XSS ok.
										?>
										<span class="product-remove remove-item-youveda block">
											<?php
												// @codingStandardsIgnoreLine
												echo apply_filters( 'woocommerce_cart_item_remove_link', 
													sprintf(
														'<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">[&times;] Remove this item</a>',
														esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
														__( 'Remove this item', 'woocommerce' ),
														esc_attr( $product_id ),
														esc_attr( $_product->get_sku() )
													),
													$cart_item_key
												);
											?>
										</span>
									</div>
								</div>
							</div>
							<div class="w-100"></div>
						</div>
						<?php
					}
					?>
					<?php do_action( 'woocommerce_after_cart_table' ); ?>
				</div>
			</div>
			<div class="button-container" style="padding-top:15px; text-align:right;">
				<button disabled id="updateCartButtonCopy" class="button button-copy">Update cart</button>
			</div>
		</div>
	</div>
	<div class="col-12 col-lg-5 right-container">
		<div class="card">
			<div class="card-header">
				<h3>Cart Totals</h3>
			</div>
			<div class="card-body">
				<?php do_action( 'woocommerce_cart_contents' ); ?>
				<div class="actions">
				<?php if ( wc_coupons_enabled() ) { ?>
					<div class="coupon">
						<label for="coupon_code">
							<?php esc_html_e( 'Got a Coupon Code?', 'woocommerce' ); ?>
							<span class="youveda-tooltip">
								<span class="title">
									<i  class="x-icon x-icon-info-circle" data-x-icon="&#xf05a;" aria-hidden="true"></i>
								</span>
								<span class="description">
									Tooltip content
								</span>
							</span>
						</label>
						<input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Coupon code', 'woocommerce' ); ?>" />
						<button type="submit" class="button" name="apply_coupon" value="<?php esc_attr_e( 'Apply', 'woocommerce' ); ?>">
							<?php esc_attr_e( 'Apply', 'woocommerce' ); ?>
						</button>
						<?php do_action( 'woocommerce_cart_coupon' ); ?>
					</div>
				<?php } ?>

				<button type="submit" class="button" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'woocommerce' ); ?>">
					<?php esc_html_e( 'Update cart', 'woocommerce' ); ?>
				</button>

				<?php do_action( 'woocommerce_cart_actions' ); ?>

				<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
				</div>
				<?php do_action( 'woocommerce_after_cart_contents' ); ?>
				<div class="cart-collaterals">
					<?php
						/**
						 * Cart collaterals hook.
						 *
						 * @hooked woocommerce_cross_sell_display
						 * @hooked woocommerce_cart_totals - 10
						 */
						do_action( 'woocommerce_cart_collaterals' );
					?>
				</div>
			</div>
		</div>
	</div>
</div>

</form>

<?php do_action( 'woocommerce_after_cart' ); ?>
