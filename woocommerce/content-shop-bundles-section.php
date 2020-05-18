<?php
/**
 * The template for displaying bundles section on the shop page
 *
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

// TODO: add setting to WooCommerce to set the bundle ID so we can avoid calling the wc_get_products.
$args = array(
	'limit'   => 1,
	'orderby' => 'date',
	'order'   => 'DESC',
	'type'    => 'bundle',
);

$get_bundle_products = wc_get_products( $args );
if ( empty( $get_bundle_products ) ) {
	return;
}
// array to group products like we use on the store.
$grouped_products = array();
foreach ( $get_bundle_products as $bundle ) {
	$bundle_product_id = $bundle->get_id();
	$bundled_items     = $bundle->get_bundled_items( 'query' );

	$get_prices = array();
	// legacy product found?
	// This is defined to save queries for subscription.
	$found_legacy = false;
	foreach ( $bundled_items as $bundled_item ) {

		$bundled_product    = $bundled_item->product;
		$bundled_product_id = $bundled_product->get_id();

		// if is single subscription and has no expiration (legacy product) skip the product.
		if ( 'subscription' === $bundled_product->get_type() && ! $found_legacy ) {
			if ( 'month' === WC_Subscriptions_Product::get_period( $bundled_product ) && 0 === WC_Subscriptions_Product::get_length( $bundled_product ) ) {
				$found_legacy = true;
				continue;
			}
		}
		if ( ! $bundled_product->is_purchasable() ) {
			continue;
		}

		$parent_group_id = $bundled_product->get_attribute( 'group_id' );
		if ( ! empty( $parent_group_id ) ) {
			$item_price = $bundled_item->get_price( 'min', true, 1 );
			// store all the prices so we can easily get min and max.
			if ( ! in_array( $item_price, $get_prices, true ) ) {
				$get_prices[] = $item_price;
			}
			if ( ! array_key_exists( $parent_group_id, $grouped_products ) ) {
				$grouped_products[ $parent_group_id ] = array(
					'product_id'    => $parent_group_id,
					'product_title' => get_the_title( $parent_group_id ),
					'bundled_items' => array(),
				);
			}
			$bundle_internal_id = $bundled_item->get_id();

			$grouped_products[ $parent_group_id ]['bundled_items'][ $bundle_internal_id ] = array(
				'id'    => $bundle_internal_id,
				'price' => $item_price,
			);

			if ( 'variable-subscription' === $bundled_product->get_type() ) {
				$available_variations = $bundled_product->get_available_variations();
				$get_variations       = wp_list_pluck( $available_variations, 'attributes', 'variation_id' );
				// map variation attribute to variation ID.
				$variations = array_flip( wp_list_pluck( $get_variations, 'attribute_pa_subscription-period' ) );

			} elseif ( 'subscription' === $bundled_product->get_type() ) {
				// map variation attribute to variation ID.
				$variations = array(
					'no' => $bundled_product_id,
				);
			} else {
				continue;
			}
			$grouped_products[ $parent_group_id ]['bundled_items'][ $bundle_internal_id ]['attrs'] = $variations;
		}
	}
}

$get_prices = array_unique( $get_prices );
$price_min  = min( $get_prices );
$price_max  = max( $get_prices );

$bundle_combinations_messages = array(
	array(
		'ids'     => '1998,2033',
		'title'   => 'Great choice!',
		'message' => 'Excellent for supporting the gut-mind connection',
	),
	array(
		'ids'     => '2033,2047',
		'title'   => 'Awesome combination!',
		'message' => 'Digestion is key for overall health and pairing it with joints supports a healthy inflammatory response',
	),
	array(
		'ids'     => '2033,2046',
		'title'   => 'Excellent choice!',
		'message' => 'As healthy digestion is key for overall health, as you balance digestion, body supports further overall health',
	),
	array(
		'ids'     => '2033,2036',
		'title'   => 'Great immune supportive choice!',
		'message' => 'As you support your digestion, you support your immunity which leads to overall immune support',
	),
	array(
		'ids'     => '1998,2046',
		'title'   => 'Wonderful choice!',
		'message' => 'This is excellent for people in high stress states',
	),
);
?>
</div>

<div class="entry-content content full-height" id="bundles-kits">
	<div class="container max width offset">
		<div class="row d-none d-md-block">
			<div class="col-12">
				<img src="/wp-content/themes/x-child/images/img-bundlebox.jpg" alt="" title="" class="bundle-box-bg">
			</div>
		</div>
		<div class="row">
			<div class="col-12 col-md-4 offset-md-1 selected-product-wrapper">				
				<div class="row">
					<div class="col-6">
						<img alt="" src="">
					</div>
					<div class="col-6">
						<img alt="" src="">
					</div>
				</div>

			</div>
			<div class="col-12 col-md-6 offset-md-1 col-lg-5 offset-lg-2">
				<div class="bundles-product-options">
					<span class="ui-feature-text">NEW!</span>
					<h3>Bundle &amp; Save $10</h3>
					<p class="tagline">Choose two supplement kits for maximum benefits at a special value!</p>
					<div class="row product-buttons-wrapper">
					<?php
					foreach ( $grouped_products as $product_id => $data ) {
						wc_get_template(
							'single-product/bundle-selection-button.php',
							array(
								'grouped_product_id' => $product_id,
								'products_data'      => $data,
							)
						);
					}
					?>
					</div>
					<div class="row" id="bundle-messages-product-match">
						<div class="col-12 col-md-10 col-lg-11 offset-lg-1">
							<div class="flex-container">
								<div class="choice-message">
									<i class="x-icon" data-x-icon="&#xf004;"></i>
									<?php
									foreach ( $bundle_combinations_messages as $combination ) {
										wc_get_template(
											'single-product/bundle-matched-message.php',
											array(
												'combination' => $combination,
											)
										);
									}
									?>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-12">
							<?php
							$input_name     = 'add_to_cart_radio_bundle_subscription_option';
							$esc_value      = 'no';
							$checked        = checked( 'yes', $esc_value, false );
							$filtered_label = 'One Time Purchase - ' . wc_price( $price_max * 2 );

							printf(
								'<div class="radio one-time-radio-option">
									<input type="radio" 
									name="%1$s" 
									value="%2$s"
									id="%3$s" %4$s>
									<label  class="radio-label" for="%3$s">%5$s</label>
									</div>',
								esc_attr( $input_name ),
								esc_attr( $esc_value ),
								esc_attr( 'add_to_cart_bundle_item_' . $esc_value ),
								esc_attr( $checked ),
								wp_kses_post( $filtered_label )
							);
							?>
						</div>
						<div class="col-12">
								<?php
								$input_name     = 'add_to_cart_radio_bundle_subscription_option';
								$esc_value      = 'yes';
								$checked        = checked( 'yes', $esc_value, false );
								$filtered_label = 'Subscribe & Save 33% More! - ' . wc_price( $price_min * 2 );

								printf(
									'<div class="radio">
									<input type="radio" 
									name="%1$s" 
									value="%2$s"
									id="%3$s" %4$s>
									<label  class="radio-label" for="%3$s">%5$s</label>
									</div>',
									esc_attr( $input_name ),
									esc_attr( $esc_value ),
									esc_attr( 'add_to_cart_bundle_item_' . $esc_value ),
									esc_attr( $checked ),
									wp_kses_post( $filtered_label )
								);
								?>
							<div class="row">
								<div class="col-12 bundle-subscription-details justify-content-start">
									<span>
										Delivers Every 
									</span>
										<?php
										echo '<div class="position-relative inline-block">';
										wc_dropdown_variation_attribute_options(
											array(
												'class'    => 'yv_custom_attribute_select',
												'options'  => array( 'every-30-days', 'every-60-days', 'every-90-days' ),
												'attribute' => 'pa_subscription-period',
												'id'       => 'bundle-pa_subscription-period',
												'product'  => false,
												'selected' => 'every-30-days',
												'show_option_none' => '-',
											)
										);
										echo '<i class="fa fa-chevron-down"></i>';
										echo '</div>';
										?>
									<span>
									Days 
									</span> 
									<span class="pipe d-md-none d-xl-inline ">|
									</span> 
										<span class="youveda-tooltip">
										<span class="title">Cancel any time</span>
										<span class="description">Save 33% when you sign up for our Subscribe &amp; Save option. Order's will be shipped automatically to you every month! Our doctor's recommend following this program for a minimum of 3 months to receive maximum benefits.</span>
									</span>
								</div>
								<div class="col-12">
									<p class="subscription-details">
										Our doctors recommend using this kit for a minimum of 3 months to receive maximum benefits.
									</p>
								</div>
								<div class="col-12">
									<a 	href="#" 
										data-quantity="1" 
										class="button product_type_bundle add_to_cart_button disabled ajax_add_to_cart" 
										data-product_id="<?php echo esc_attr( $bundle_product_id ); ?>" 
										aria-label="Add “A Bundle kit” to your cart" rel="nofollow">Add to cart</a>
								</div>
							</div>

						</div>
					</div>
				</div>
			</div>				
		</div>
	</div>
</div>
<div>