<?php
/**
 * WooCommerce cart related customization
 *
 * File having all the modified functinoality for the WooCommerce cart page
 *
 * @package WordPress
 * @subpackage Youveda
 * @since 2.0.0
 */

/**
 * Output the shop more banner on cart
 *
 * @return void
 */
function yv_cart_shop_more_banner() {
	if ( 1 === WC()->cart->cart_contents_count ) {

		if ( ! file_exists( 'cart/content-cart-banner.php' ) ) {
			 return;
		}
		wc_get_template( 'cart/content-cart-banner.php' );
	}
}
add_action( 'woocommerce_after_cart_table', 'yv_cart_shop_more_banner', 9 );

/**
 * Custom remove item link.
 *
 * @param  string $str           The generated output.
 * @param  string $cart_item_key The cart item key.
 * @return string                The filtered "remove item link" markup.
 */
function yv_cart_item_remove_link( $str, $cart_item_key ) {
	$cart_content    = WC()->cart->get_cart();
	$cart_item       = $cart_content[ $cart_item_key ];
	$_product        = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
	$product_id      = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
	$svg_remove_icon = get_theme_file_path() . '/images/remove_icon.svg';

	return sprintf(
		'<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">%s</a>',
		esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
		__( 'Remove this item', 'woocommerce' ),
		esc_attr( $product_id ),
		esc_attr( $_product->get_sku() ),
		file_get_contents( $svg_remove_icon ) // phpcs:ignore
	);
}

add_filter( 'woocommerce_cart_item_remove_link', 'yv_cart_item_remove_link', 10, 2 );

/**
 * Filter the coupon label on the cart page
 *
 * @param string    $label  The coupon label.
 * @param WC_Coupon $coupon Coupon data or code.
 * @return string           The filtered coupon label.
 */
function yv_filter_coupon_cart_label( $label, $coupon ) {
	// 'Coupon Discount:  FIRSTTIME - 30%'
	$discount_symbol = 'percent' === $coupon->get_discount_type() ? '%' : '$';
	$amount          = (float) $coupon->get_amount();
	/* translators: 1: coupon code, 2: dicount amount, 3:discount symbol*/
	return sprintf( esc_html__( 'Coupon: %1$s  -%2$s%3$s', 'woocommerce' ), $coupon->get_code(), $amount, $discount_symbol );
}


//add_filter( 'woocommerce_cart_totals_coupon_label', 'yv_filter_coupon_cart_label', 10, 2 );

/**
 * Prepend kits main category to product title on cart page.
 * Removes subscription related part from product name.
 *
 * @param string $product_name The product name.
 * @return string
 */
function yv_add_kit_main_category_to_cart_item( $product_name, $cart_product ) {
	return sprintf( '<h6 class="product-type-category">%s</h6> %s', yv_get_primary_taxonomy_term( $cart_product['product_id'] )['title'], yv_get_product_base_name( $product_name ) );
}

add_filter( 'woocommerce_cart_item_name', 'yv_add_kit_main_category_to_cart_item', 10, 2 );
add_filter( 'woocommerce_order_item_name', 'yv_add_kit_main_category_to_cart_item', 10, 2 );

/**
 * Return the product basename.
 *
 * Removes the "one time" and "every xx months" from the product name
 *
 * @param string $product_name The product full name ex: "My healthy Mood - One time purchase".
 * @return string               Product basename without subscription part.
 */
function yv_get_product_base_name( $product_name ) {
	$name = explode( ' - ', $product_name );
	return trim( reset( $name ) );
}

/**
 * Remove price amount or subscription details from price in cart table
 *
 * @param string $price_string The product price.
 * @param array  $cart_item    Cart item object.
 * @return string              The product price string without the amount if product is subscription.
 */
function yv_cart_item_price_string( $price_string, $cart_item ) {
	$cat = yv_get_primary_taxonomy_term( $cart_item['product_id'] );
	if($cat['slug'] != 'kit-bundle') {
		$product = $cart_item['data'];

		if ( 'variable-subscription' !== $product->get_type() && 'subscription' !== $product->get_type() && 'subscription_variation' !== $product->get_type() ) {
			return $price_string;
		}
		// load the DOM parser lib.
		if ( ! class_exists( 'simple_html_dom' ) ) {
			require_once get_stylesheet_directory() . '/inc/simple_html_dom.php';
		}
		$html = new simple_html_dom();
		$html->load( $price_string );
		if ( 'woocommerce_cart_item_price' === current_filter() ) {
			$dom_elem = 'span.subscription-details';
		} else {
			$dom_elem = 'span.woocommerce-Price-amount';
		}
		return $html->find( $dom_elem, 0 )->plaintext;
	} else {
		return '';
	}
}

add_filter( 'woocommerce_cart_item_price', 'yv_cart_item_price_string', 10, 2 );
add_filter( 'woocommerce_cart_item_subtotal', 'yv_cart_item_price_string', 10, 2 );

/**
 * Output the buttons for the save section action on checkout.
 *
 * @return string
 * @see wc_get_template()
 */
function yv_save_checkout_section_button() {
	$current_action = current_action();

	switch ( $current_action ) {
		case 'yv_woocommerce_after_checkout_shipping_section':
			$btn_text   = __( 'Shipping Information', 'woocommerce' );
			$section_id = 'customer_details';
			break;
		case 'yv_woocommerce_after_checkout_delivery_section':
			$btn_text   = __( 'Delivery options', 'woocommerce' );
			$section_id = 'delivery_details';
			break;
		default:
			$btn_text   = __( 'Section', 'woocommerce' );
			$section_id = 'customer_details';
			break;
	}

	return wc_get_template(
		'checkout/save-section-button.php',
		array(
			'btn_text'   => $btn_text,
			'section_id' => $section_id,
		)
	);
}
add_action( 'yv_woocommerce_after_checkout_shipping_section', 'yv_save_checkout_section_button', 10 );
add_action( 'yv_woocommerce_after_checkout_delivery_section', 'yv_save_checkout_section_button', 10 );

// Add select shipping options custom template to checkout.
// add_action( 'woocommerce_checkout_after_customer_details', 'woocommerce_checkout_yv_shipping', 15 );

// Move payment after billing details.
// remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );
// add_action( 'woocommerce_checkout_before_customer_details', 'woocommerce_checkout_payment', 20 );

/**
 * Filter the gateway icons on checkout page
 *
 * @param  string $icon_html  Gateway icon html.
 * @param  int    $gateway_id Gateway ID.
 * @return string             Custom icon.
 */
function yv_custom_gateway_icons( $icon_html, $gateway_id ) {
	if ( ! is_checkout() ) {
		return $icon_html;
	}
	$html     = false;
	$gateways = WC()->payment_gateways->payment_gateways();
	foreach ( $gateways as $id => $gateway ) {
		if ( $gateway_id === $id ) {
			$img   = esc_attr( get_stylesheet_directory_uri() . '/images/gateways/' . sanitize_title( $gateway->get_title() ) . '.png' );
			$html  = '<span class="d-none">' . $icon_html . '</span>';
			$html .= '<img src="' . $img . '" />';
			break;
		}
	}
	return $html ? $html : $icon_html;
}

add_filter( 'woocommerce_gateway_icon', 'yv_custom_gateway_icons', 99, 2 );


/**
 * Return the Amazon gateway.
 * Used to append the gateway to the checkout templates and show Amazon as an option.
 *
 * @return bool|obj false|WC_Gateway_Amazon_Payments_Advanced|WC_Gateway_Amazon_Payments_Advanced_Subscriptions
 */
function return_amazon_gateway() {
	$available_gateways = WC()->payment_gateways();
	$gateways_by_id     = $available_gateways->get_payment_gateway_ids();
	$amazon_gateway_id  = array_search( 'amazon_payments_advanced', $gateways_by_id, true );
	return false !== $amazon_gateway_id ? $available_gateways->payment_gateways[ $amazon_gateway_id ] : false;
}

/**
 * Load custom template for shipping options.
 *
 * @return string
 * @see wc_get_template()
 */
function woocommerce_checkout_yv_shipping() {

	if ( ! file_exists( 'checkout/shipping-options.php' ) ) {
		 return;
	}
	return wc_get_template( 'checkout/shipping-options.php' );
}

/**
 * Check if the selected payment gateway is Amazon
 *
 * Used on the checkout to hide shipping options.
 *
 * @return boolean
 */
function is_amazon_payment_selected() {
	if ( ! class_exists( 'WC_Amazon_Payments_Advanced_API' ) ) {
		return false;
	}
	return ! empty( WC_Amazon_Payments_Advanced_API::get_access_token() );
}

/**
 * Show shipping sumup
 *
 * @return string
 */
function yv_checkout_show_shipping_sumup() {

	if ( ! file_exists( 'checkout/shipping-details-custom.php' ) ) {
		 return;
	}

	return wc_get_template( 'checkout/shipping-details-custom.php' );
}
add_action( 'woocommerce_review_order_before_order_total', 'yv_checkout_show_shipping_sumup', 10 );

// do not show the coupon form since got moved on the 2019 new layout.
// remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
// add_action( 'woocommerce_review_order_before_order_total', 'woocommerce_checkout_coupon_form', 30 );

// re-order the Amazon widgets.
// add_action(
// 	'woocommerce_checkout_init',
// 	function() {
// 		// Check if "pay with Amazon" plugin is enable.
// 		if ( function_exists( 'wc_apa' ) ) {
// 			if ( has_action( 'woocommerce_checkout_before_customer_details', array( wc_apa(), 'payment_widget' ) ) ) {
// 				remove_action( 'woocommerce_checkout_before_customer_details', array( wc_apa(), 'payment_widget' ), 20 );
// 				add_action( 'yv_review_order_before_payment', 'yv_amazon_payment_widget', 20 );
// 				remove_action( 'woocommerce_checkout_before_customer_details', array( wc_apa(), 'address_widget' ), 10 );
// 				add_action( 'woocommerce_checkout_billing', 'yv_amazon_address_widget', 20 );
// 			}
// 		}
// 	},
// 	99
// );

/**
 * Copy of address_widget from Amazon payment plugin
 *
 * @see WC_Amazon_Payments_Advanced::address_widget
 * @return void
 */
function yv_amazon_address_widget() {
		$instance = wc_apa();
		// Skip showing address widget for carts with virtual products only.
		$show_address_widget = apply_filters( 'woocommerce_amazon_show_address_widget', WC()->cart->needs_shipping() );
		$hide_css_style      = ( ! $show_address_widget ) ? 'display: none;' : '';
	?>
		<div id="amazon_customer_details" class="wc-amazon-payments-advanced-populated row"> 
				<div class="col-12" style="<?php echo esc_attr( $hide_css_style ); ?>">
				<div id="amazon_addressbook_widget"></div>
				<?php if ( ! empty( $instance->reference_id ) ) : ?>
					<input type="hidden" name="amazon_reference_id" value="<?php echo esc_attr( $instance->reference_id ); ?>" />
				<?php endif; ?>
				<?php if ( ! empty( $instance->access_token ) ) : ?>
					<input type="hidden" name="amazon_access_token" value="<?php echo esc_attr( $instance->access_token ); ?>" />
				<?php endif; ?>
			</div>
		</div>
	<?php
}

/**
 * Copy of payment_widget from Amazon payment plugin
 *
 * @see WC_Amazon_Payments_Advanced::payment_widget
 * @return void
 */
function yv_amazon_payment_widget() {
	$checkout = WC_Checkout::instance();
	$instance = wc_apa();
	?>
	<div id="amazon_wallet_widget"></div>
	<?php if ( ! empty( $instance->reference_id ) ) : ?>
		<input type="hidden" name="amazon_reference_id" value="<?php echo esc_attr( $instance->reference_id ); ?>" />
	<?php endif; ?>
	<?php if ( ! empty( $instance->access_token ) ) : ?>
		<input type="hidden" name="amazon_access_token" value="<?php echo esc_attr( $instance->access_token ); ?>" />
	<?php endif; ?>
	<div id="amazon_consent_widget" style="display: none;"></div>
	<?php if ( ! is_user_logged_in() && $checkout->enable_signup ) : ?>

		<?php if ( $checkout->enable_guest_checkout ) : ?>

			<p class="form-row form-row-wide create-account">
				<input class="input-checkbox" id="createaccount" <?php checked( ( true === $checkout->get_value( 'createaccount' ) || ( true === apply_filters( 'woocommerce_create_account_default_checked', false ) ) ), true ); ?> type="checkbox" name="createaccount" value="1" /> <label for="createaccount" class="checkbox"><?php _e( 'Create an account?', 'woocommerce-gateway-amazon-payments-advanced' );//phpcs:ignore?></label>
			</p>

		<?php endif; ?>

		<?php do_action( 'woocommerce_before_checkout_registration_form', $checkout ); ?>

		<?php if ( ! empty( $checkout->checkout_fields['account'] ) ) : ?>

			<div class="create-account">

				<h3><?php _e( 'Create Account', 'woocommerce-gateway-amazon-payments-advanced' );//phpcs:ignore ?></h3>
				<p><?php _e( 'Create an account by entering the information below. If you are a returning customer please login at the top of the page.', 'woocommerce-gateway-amazon-payments-advanced' );//phpcs:ignore ?></p>

				<?php foreach ( $checkout->checkout_fields['account'] as $key => $field ) : ?>

					<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>

				<?php endforeach; ?>

				<div class="clear"></div>

			</div>

		<?php endif; ?>

		<?php do_action( 'woocommerce_after_checkout_registration_form', $checkout ); ?>

		<?php
		endif;
}

/**
 * Filter the order thank you message
 *
 * @param string $msg WC thank you message.
 * @return string     Filtered message.
 */
function yv_thankyou_order_received_text( $msg ) {
	return __( 'Your order has been placed.', 'woocommerce' );
}
add_filter( 'woocommerce_thankyou_order_received_text', 'yv_thankyou_order_received_text', 10, 1 );

// do not show related subscriptions on thank you page.
remove_action( 'woocommerce_order_details_after_order_table', 'WC_Subscriptions_Order::add_subscriptions_to_view_order_templates', 10, 1 );
// do not show subscription status link to my account on thank you page.
remove_action( 'woocommerce_thankyou', 'WC_Subscriptions_Order::subscription_thank_you' );

add_filter( 'woocommerce_order_item_quantity_html', 'yv_order_item_quantity_html', 10, 2 );

/**
 * Filter Quantity on order page
 *
 * @param  string        $str  Quantity string.
 * @param  WC_Order_Item $item Order Item.
 * @return string              Filtered value.
 */
function yv_order_item_quantity_html( $str, $item ) {
	return '<strong class="product-quantity">' . sprintf( 'Qty: %s', $item->get_quantity() ) . '</strong>';
}
