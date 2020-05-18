<?php
/**
 * The template for displaying oils section on the shop page
 *
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;
?>
</div>

<div class="x-container max width offset shop-title-wrapper">
	<div class="x-column x-sm x-1-1 text-center">
		<h1 class="page-title" id="shop-oils-title">
			Ayurvedic Essential Oils 10ml
			<svg version="1.1" id="oils-title-icon" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
				 width="30px" height="15px" viewBox="0 0 30 15" enable-background="new 0 0 30 15" xml:space="preserve">
				<g>
					<path fill="#48B7AD" d="M27.9,15H2.1C0.9,15,0,14.1,0,12.9V2.1C0,0.9,0.9,0,2.1,0h25.9C29.1,0,30,0.9,30,2.1v10.9
						C30,14.1,29.1,15,27.9,15z"/>
					<g>
						<path fill="#FFFFFF" d="M9.4,4.8h1.2v5.5H9.4L6.8,6.8v3.4H5.6V4.8h1.1l2.7,3.5V4.8z"/>
						<path fill="#FFFFFF" d="M15.9,4.8v1.1h-2.7V7h2.4v1h-2.4v1.1H16v1.1h-4V4.8H15.9z"/>
						<path fill="#FFFFFF" d="M18.8,7.9l1-3.1H21l1,3.1l1.1-3.1h1.3l-1.9,5.5h-0.9l-1.2-3.8l-1.2,3.8h-0.9l-1.9-5.5h1.3L18.8,7.9z"/>
					</g>
				</g>
			</svg>
		</h1>
	</div>
</div>

<div class="x-container products-shop-list max width offset" id="essential-oils-section">

	<?php $columns      = x_get_option( 'x_woocommerce_shop_columns' ); ?>
	<?php $column_class = ( is_shop() || is_product_category() || is_product_tag() ) ? ' cols-' . $columns : ''; ?>
	<div class="x-container products-shop-list max offset">
		<ul class="products<?php echo $column_class; ?> owl-carousel" id="essential-oils">

			<?php

			$atts = array(
				'post_type' => 'product',
				'post_status' => 'publish',
				'posts_per_page' => -1,
				'tax_query' => array(
					'relation' => 'AND',
					array(
						'taxonomy' => 'product_cat',
						'field' => 'slug',
						'terms' => 'essential-oils'
					),
					array(
						'taxonomy' => 'product_type',
						'field' => 'slug',
						'terms' => 'grouped'
					)
				)
			);

			woocommerce_reset_loop();

			$query = new WP_Query( $atts );

			if($query->have_posts()) :
				while($query->have_posts()) : $query->the_post();
					wc_get_template_part( 'content', 'product' );
				endwhile;
			endif;

			wp_reset_query();

			?>

		</ul>
	</div>
</div>