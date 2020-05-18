<?php
/**
 * Banner for cart page if only one product on cart
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce/Templates
 * @version     3.5.0
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="flex-container flex-column cart-shop-more-banner">
	<?php
	print_svg_from_theme( 'cart-banner-shop-more.svg' );
	?>
	<h3>Plenty of Room for More!</h3>
	<p>Add more products to your shopping cart</p>
	<?php if ( wc_get_page_id( 'shop' ) > 0 ) { ?>
		<p class="return-to-shop">
			<a class="wc-backward" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
				<?php esc_html_e( 'Continue shopping', 'woocommerce' ); ?>
			</a>
		</p>
	<?php } ?>

</div>
