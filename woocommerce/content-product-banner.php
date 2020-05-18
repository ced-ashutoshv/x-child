<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product-banner.php.
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

global $product, $woocommerce;

// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
?>
<li <?php wc_product_class( 'catalog-shipping-banner glide__slide' ); ?> >
	<div>
		<p class="shipping-summary">
			FREE SHIPPING WITHIN THE US
		</p>
		<svg id="SVG_layer_shipping" data-name="SVG Layer shipping" xmlns="http://www.w3.org/2000/svg" width="85.85" height="74.13" viewBox="0 0 85.85 74.13"><title>shipping_icon</title><path d="M285,213.44l-44.69,25.77a5.16,5.16,0,0,0,.62,9.23l12.27,5.08v9.06a5.17,5.17,0,0,0,9.31,3.08l4.7-6.35,12,5a5.17,5.17,0,0,0,7.07-4l6.38-41.6a5.17,5.17,0,0,0-7.68-5.25ZM258.4,262.58v-6.93l3.93,1.62Zm22.84-3.08-16.52-6.82L279.78,231a1.73,1.73,0,0,0-2.55-2.28l-22.72,19.8-11.58-4.79,44.69-25.78Z" transform="translate(-206.93 -212.75)" fill="#48b7ad"/><path d="M243.22,270.55A19.51,19.51,0,0,1,216.4,277a15.61,15.61,0,0,1-5.17-21.47,12.49,12.49,0,0,1,17.17-4.13,10,10,0,0,1,3.31,13.74,8,8,0,0,1-11,2.64A6.4,6.4,0,0,1,218.6,259" transform="translate(-206.93 -212.75)" fill="none" stroke="#48b7ad" stroke-linecap="round" stroke-miterlimit="10" stroke-width="4"/><path d="M270.47,282.32c.38-.32,4-2.59,11.23-3.12-2-1.35-8-2.46-12.91-.06,4.64-8.25,14.46-3.62,18.79,1.61C281.46,289.94,275.18,287.36,270.47,282.32Z" transform="translate(-206.93 -212.75)" fill="#48b7ad"/><path d="M232.37,232.8a30.46,30.46,0,0,0-4.29-11.16c2.07.56,5.56,5,6.61,9.52,4.87-6.86-4.28-15.24-10.58-15C221.26,225.64,226.33,231.45,232.37,232.8Z" transform="translate(-206.93 -212.75)" fill="#48b7ad"/></svg>
		<h3>
			International Shipping for just
		</h3>
		<span class="woocommerce-Price-amount amount">
			<span class="woocommerce-Price-currencySymbol">$</span>9<span class="super">.99</span>
		</span>
		<p class="banner-cta">
			<a href="/frequently-asked-questions/?free-shipping">
				<i class="x-icon x-icon-arrow-right" data-x-icon="&#xf105;" aria-hidden="true"></i> 
				<span>See the destination list</span>
			</a>
		<p>
	</div>
</li>