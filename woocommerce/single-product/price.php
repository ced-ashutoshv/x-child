<?php
/**
 * Single Product Price
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/price.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;
$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );
$child_prices     = array();
$children         = array_filter( array_map( 'wc_get_product', $product->get_children() ), 'wc_products_array_filter_visible_grouped' );

foreach ( $children as $child ) {
	if ( '' !== $child->get_price() ) {
		$child_prices[] = 'incl' === $tax_display_mode ? wc_get_price_including_tax( $child ) : wc_get_price_excluding_tax( $child );
	}
	if ( ! empty( $child_prices ) ) {
		$min_price = min( $child_prices );
		$max_price = max( $child_prices );
	} else {
		$min_price = '';
		$max_price = '';
	}
}
if( ''!== $max_price ){
	$price = wc_price( $max_price, array('decimals' => 0));
}else{
	$price = $product->get_price();
}
?>

<p class="price blbal">
	<?php
		//echo $product->get_price_html();	//We want to show the regular price, instead of the subscription sale price
		//echo '<span class="from">Regular Price: </span><span class="woocommerce-Price-amount amount">'.$price.'</span>';
	?>
</p>
