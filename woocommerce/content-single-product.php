<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
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

/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked wc_print_notices - 10
 */
do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
}

$id = get_the_id();

?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class(); ?>>
	<div class="youveda-custom-container x-container max width">
	<?php
		/**
		 * Hook: woocommerce_before_single_product_summary.
		 *
		 * @hooked woocommerce_show_product_sale_flash - 10
		 * @hooked woocommerce_show_product_images - 20
		 */
		do_action( 'woocommerce_before_single_product_summary' );
		// echo '<div class="images">';
		// $image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'single-post-thumbnail' );
		// echo '<img src="'.$image[1].'" alt="">';
		// echo '</div>';
	?>

	<div class="summary entry-summary">
		<div class="product-entry-summary">
			<div class="category-logo">
				<?php
					// display the category image
					global $post;
					$product_icon = get_post_meta( $post->ID, 'yv_product_shop_product_type_icon', true );
					if( empty( $product_icon ) ) {
						$icon = false;
						$terms = get_the_terms( $post->ID, 'product_cat' );
					    $product_cat_id = $terms[0]->term_id;
					    $thumbnail_id = get_woocommerce_term_meta( $product_cat_id, 'thumbnail_id', true ); 
						$image = wp_get_attachment_url( $thumbnail_id ); 
						echo "<img src='{$image}' alt='' width='762' height='365' />";
					} else {
						$icon = true;
						echo "<img src='{$product_icon}' alt='' width='762' height='365' />";
					}
				?>
			</div>
			<div class="product-summary-info">
				<?php
					$product_type = get_post_meta( $post->ID, 'yv_product_shop_product_type', true );
					if( ! empty( $product_type ) ) :
						?>
						<p class="product-type"><?php echo $product_type; ?></p>
						<p class="product-title"><?php echo get_the_title(); ?></p>
						<?php
					endif;
				?>
			</div>
		</div>
		<?php
			/**
			 * Hook: woocommerce_single_product_summary.
			 *
			 * @hooked woocommerce_template_single_title - 5
			 * @hooked woocommerce_template_single_rating - 10
			 * @hooked woocommerce_template_single_price - 10
			 * @hooked woocommerce_template_single_excerpt - 20
			 * @hooked woocommerce_template_single_add_to_cart - 30
			 * @hooked woocommerce_template_single_meta - 40
			 * @hooked woocommerce_template_single_sharing - 50
			 * @hooked WC_Structured_Data::generate_product_data() - 60
			 */
			do_action( 'woocommerce_single_product_summary' );
		?>
	</div>
	</div><!-- .youveda-custom-container -->
	<?php
		/**
		 * Hook: woocommerce_after_single_product_summary.
		 *
		 * @hooked woocommerce_output_product_data_tabs - 10
		 * @hooked woocommerce_upsell_display - 15
		 * @hooked woocommerce_output_related_products - 20
		 */
		do_action( 'woocommerce_after_single_product_summary' );
	?>
</div>

<?php do_action( 'woocommerce_after_single_product' ); ?>
