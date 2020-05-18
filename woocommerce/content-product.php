<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
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
// Get the catalog product ids
$the_ids = wp_list_pluck ( $woocommerce->query->get_main_query()->posts, 'ID');
$product_color = get_post_meta( $product->get_id(), 'yv_product_color', true );
$category = yv_get_primary_taxonomy_term( $product->get_id() );
//if($category['slug'] == 'supplement-kits') :
?>
<li <?php wc_product_class('glide__slide'); ?> data-product-color="<?php  echo $product_color;?>" data-parent-product-link="<?php echo get_permalink( $product->get_id() ); ?>">
	<?php

	/**
	 * Hook: woocommerce_before_shop_loop_item.
	 *
	 * @hooked woocommerce_template_loop_product_link_open - 10
	 */
	do_action( 'woocommerce_before_shop_loop_item' );

	/**
	 * Hook: woocommerce_before_shop_loop_item_title.
	 *
	 * @hooked woocommerce_show_product_loop_sale_flash - 10
	 * @hooked woocommerce_template_loop_product_thumbnail - 10
	 */
	do_action( 'woocommerce_before_shop_loop_item_title' );

	/**
	 * Hook: woocommerce_shop_loop_item_title.
	 *
	 * @hooked woocommerce_template_loop_product_title - 10
	 */
	do_action( 'woocommerce_shop_loop_item_title' );

	/**
	 * Hook: woocommerce_after_shop_loop_item_title.
	 *
	 * @hooked woocommerce_template_loop_rating - 5
	 * @hooked woocommerce_template_loop_price - 10
	 */
	do_action( 'woocommerce_after_shop_loop_item_title' );

	/**
	 * Hook: woocommerce_after_shop_loop_item.
	 *
	 * @hooked woocommerce_template_loop_product_link_close - 5
	 * @hooked woocommerce_template_loop_add_to_cart - 10
	 */
	do_action( 'woocommerce_after_shop_loop_item' );
	?>
</li>
<?php 
//endif;
	// add banner after 5th element on loop
	if( $the_ids[4] == $product->get_id() ){
		/**
		 * Hook: yv_woocommerce_shop_loop_item_banner.
		 *
		 * @hooked yv_woocommerce_template_loop_product_banner - 10
		 */
		do_action( 'yv_woocommerce_shop_loop_item_banner' );		

	} 
?>