<?php
/**
 * Helper functions.
 *
 * @package Ameba theme helper functions
 * @version 1.0
 */

/**
 * Return the svg tag from the theme images folder.
 *
 * @param string  $svg_filename The SVG filename.
 * @param boolean $echo         Echo flag.
 * @return string $svg         The SVG tag for outputting.
 */
function print_svg_from_theme( $svg_filename, $echo = true ) {
	$svg_remove_icon = get_theme_file_path() . '/images/' . $svg_filename;
	$svg             = '';
	if ( file_exists( $svg_remove_icon ) ) {
		$svg = file_get_contents( $svg_remove_icon ); // phpcs:ignore
	}

	if ( $echo ) {
		echo $svg;// phpcs:ignore
	} else {
		return $svg;
	}
}

/**
 * Check against product category if a given product is a kit
 *
 * @param int|WC_Product $product Product ID | instance of WC_Product.
 * @return boolean
 */
function is_product_kit( $product ) {
	if ( is_numeric( $product ) ) {
		$prod_id = $product;
	} elseif ( $product instanceof WC_Product ) {
		$prod_id = $product->get_id();
	} elseif ( ! empty( $product->ID ) ) {
		$prod_id = $product->ID;
	} else {
		return false;
	}

	return has_term( return_kits_main_term(), 'product_cat', $prod_id );
}

/**
 * Return the kits main category slug used on the cart products list
 *
 * @param  string $output Return type: slug|name|id.
 * @return string $return The term requested property | empty string if term does not exist.
 */
function return_kits_main_term( $output = 'slug' ) {
	$kits_term_slug = 'supplement-kits';
	$return         = '';
	if ( term_exists( $kits_term_slug, 'product_cat' ) ) {
		if ( 'slug' === $output ) {
			$return = $kits_term_slug;
		} else {
			$main_term = get_term_by( 'slug', $kits_term_slug, 'product_cat', ARRAY_A );
			$return    = $main_term[ $output ];
		}
	}
	return $return;
}

function yv_get_primary_taxonomy_term( $id ) {
	//return 'entro en la funcion';
	$category = get_the_terms($id, 'product_cat');
	$useCatLink = true;
	// If post has a category assigned.
	if ($category){
		$category_display = array();
		$category_link = '';
		if ( class_exists('WPSEO_Primary_Term') )
		{
			// Show the post's 'Primary' category, if this Yoast feature is available, & one is set
			$wpseo_primary_term = new WPSEO_Primary_Term( 'product_cat', $id );
			$wpseo_primary_term = $wpseo_primary_term->get_primary_term();
			$term = get_term( $wpseo_primary_term );
			if (is_wp_error($term)) { 
				// Default to first category (not Yoast) if an error is returned
				$category_display['title'] = $category[0]->name;
				$category_display['slug'] = $category[0]->slug;
			} else { 
				// Yoast Primary category
				$category_display['title'] = $term->name;
				$category_display['slug'] = $term->slug;
			}
		} 
		else {
			// Default, display the first category in WP's list of assigned categories
			$category_display['title'] = $category[0]->name;
			$category_display['slug'] = $category[0]->slug;
		}
		return $category_display;
	} else {
		$category_display = array(
			'title' => '',
			'slug' => ''
		);
	}
}