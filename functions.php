<?php
/*
 * FUNCTIONS.PHP
 * -----------------------------------------------------------------------------
 * Overwrite or add your own custom functions to X in this file.

	TABLE OF CONTENTS
	-----------------------------------------------------------------------------
	01. Enqueue Parent Stylesheet
	02. Additional Functions

	-------------------------

	CUSTOM FUNCTIONS ADDED BY AMEBA

	03. Shortcode for posts grid on the homepage
	05. Woocomerce -> Add product custom field 'subtitle'
	06. Woocomerce -> Remove Sales Circular Badge that says "SALE"
	07. Woocomerce -> Customize x_woocommerce_shop_product_thumbnails
	08. Woocomerce -> woocommerce_single_product_summary --> edit the hooks
	09. Woocomerce -> shop hooks
	10. User old products info
	11. Product Activation related functions
	12. Login / Logout
	13. Cornerstone Plugin > Classic Recent Posts (modify plugin)
	14. Woocommerce -> Checkout > Ship to a different address?
	15. Woocommerce -> Hide SKU number from single page
	16. Change Woocommerce My Account Tabs -> Re-order items + hide "logout" if I am in app view
	17. TO REFACTOR > REDIRECT AFTER LOGIN if AUTH0 LOGIN SUCCESS
	18. TO BE REMOVED and FIXED BY ASAP AFTER LAUNCH > If user is in the app, add loading while logging the user in and redirect.
		Also set a COOKIE appview used in 16.
	19. TO BE REMOVED and FIXED AFTER LAUNCH > Manually import a user from auth0: Auth0 hits WordPress and sends the needed data over GET params.
	20. Add a prefix to Orders.
	21. Show price (instead of sale price) on the archive (shop)
	22. Remove Company Input Field in Checkout Page
	23. Change cuopons on the fly
	24. Add same SKU to the products than their grouped product.
	25. Checkout actions
	26. Thank you page (after checkout)
	27. Woocommerce -> change product button text on shop (plural -> singular)
	28. Website Optimization
	29. Cart / Checkout customization
		- Remove next recurring order details
		- Update thank you page
		- Show sale price on cart items table
	30. REMOVED Add code snippets to the <head></head>
	31. Multiple Subscriptions options on product page + add to cart update
	32. Change default "Choose an option" text for select input - Paymo task : 14503660
	33. Woocomerce -> remove 'items' word from the navbar cart icon
	34. Graivty Forms -> Customize validation message for "Stay up to Date!" form only
	35. Modify the Woo-only-read user role so they cannot make refunds.
	36. Change Navigation arrows.
	37. Custom text formats for blog single post (or any other type of page) : bigger paragraph, capitulate letter
	38. Change comments input forms order, remove email input form, and add a custom placeholder for the "comments" input form.
	39. Woocommerce -> Add custom css styles for the WooCommerce Emails
	40. Filter WooCommerce Flexslider options - Add Navigation Arrows
	41. WooCommerce Flexslider - Remove Zoom
	42. Add 'Google Ads - Conversion Tracking Tag/Event' Snippets if page is : /order-received, /subscribe/confirm, or /account-confirmation 
	43. Add Google Tracking event on checkout header
	44. Remove afterpay for variable subscriptions (30days/60days/90days) but keep it for one time.
	45. Add description to menu items
	46. Shortcode that displays the amount of completed orders
	47. Fix load Cornerstone 
	48. Product gallery override
	49. WooCommerce variation dropdown override
	50. Modify the main product query so it only displays Supplement Kits
	51. Hide shipping rates when free shipping is available.


	CUSTOM FUNCTIONS ADDED BY MAKEWEBBETTER
	52. Add Product Image to Cart Item Name in Order Review.
	53. Hide Product Quantity to Cart Item in Order Review.
	54. Hide Recurring totals in Order Review.
	55. Default Place Order Button Html Hidden.
 */

/**
 * 1. Enqueue Parent Stylesheet
 * =============================================================================
 */
add_filter( 'x_enqueue_parent_stylesheet', '__return_false' );

/**
 * 2. Additional Functions
 * =============================================================================
 */
function youveda_enqueues() {

	wp_enqueue_script( 'youveda-scripts', get_stylesheet_directory_uri() . '/scripts/scripts.js', array( 'jquery' ), 53, true );

	if ( is_account_page() ) {
		if ( ! wp_script_is( 'youveda-my-account' ) ) {
			$version = filemtime( get_stylesheet_directory() . '/scripts/my-account.js' );
			wp_register_script( 'youveda-my-account', get_stylesheet_directory_uri() . '/scripts/my-account.js', array( 'jquery' ), $version, true );
			// Localize the script with new data.
			$my_account_data = array(
				'ajax_url'             => admin_url( 'admin-ajax.php' ),
				'nonce_reset_pw'       => wp_create_nonce( 'yv_reset_password_nonce' ),
				'nonce_update_profile' => wp_create_nonce( 'yv_update_profile_nonce' ),
			);
			wp_localize_script( 'youveda-my-account', 'my_account_object', $my_account_data );
			wp_enqueue_script( 'youveda-my-account' );
		}
	}
	if ( is_shop() || is_product_category() ) {
		wp_enqueue_script( 'owljs', get_stylesheet_directory_uri() . '/scripts/owl.carousel.min.js', array( 'youveda-scripts' ), '2.3.4', true );
		wp_enqueue_script( 'smooth-scroll', get_stylesheet_directory_uri() . '/scripts/smooth-scroll.min.js', array( 'youveda-scripts' ), '15.2.1', true );
		wp_enqueue_style( 'owl', get_stylesheet_directory_uri() . '/assets/css/owl.carousel.min.css', array(), '2.3.4', 'all' );
	}
	if ( is_shop() || is_product_category() || is_cart() || is_checkout() || is_wc_endpoint_url('order-received') || is_product() ) {
		wp_enqueue_style( 'bootstrap-grid', get_stylesheet_directory_uri() . '/assets/css/bootstrap-grid.min.css', array(), '4.0.0', 'all' );
	}
	if( is_cart()) {
		wp_enqueue_script( 'cart', get_stylesheet_directory_uri() . '/scripts/cart.js', array( 'jquery' ), '1.0.0', true );
	}

	// Remove all CSS from Checkout.
	$child_css_version = ! is_checkout() ? filemtime( get_stylesheet_directory() . '/style.css' ) : false;
	wp_enqueue_style(
		'x-child',
		get_stylesheet_directory_uri() . '/style.css',
		array(),
		strval( $child_css_version ),
		'all'
	);
}

add_action( 'wp_enqueue_scripts', 'youveda_enqueues', 998 );


/**
 * 3. SHORTCODE FOR BLOG POSTS (for "Single post" and "Home" at the time of writting..)
 * This shortcode works with :
 * - template-part on 'x-child/template-parts/content-posts.php'
 * - Related css on 'sass/template-parts/_youveda-posts.scss'
 *
 * @param array $atts Shortcode params.
 * @return void
 */
function youveda_posts_handler( $atts ) {

	$current_post_id = get_the_id();

	$a = shortcode_atts(
		array(
			'posts_per_page' => 3,
			'offset'         => 0,
			'category'       => '',
			'orderby'        => 'post_date',
			'order'          => 'DESC',
			'exclude'        => $current_post_id,
			'meta_key'       => '',
			'meta_value'     => '',
			'post_type'      => 'post',
			'section_title'  => 'You may also like',
		),
		$atts
	);

	$related_posts = get_posts( $a );
	if ( $related_posts ) {
		echo '<div class="youveda-posts" style="' . $a['bg_color'] . '">';
		echo '<div class="x-container max width offset">';
		echo '<h2 class="title">' . $a['section_title'] . '</h2>';

		foreach ( $related_posts as $post ) :
			setup_postdata( $post );
			set_query_var( 'post_id', absint( $post->ID ) );
			get_template_part( 'template-parts/content', 'posts' );
		endforeach;
		wp_reset_postdata();
		echo '</div>';
		echo '</div>';
	}
}
add_shortcode( 'youveda_posts', 'youveda_posts_handler' );

// ends SHORTCODE FOR BLOG POSTS (in Single post or home).



// ============================ WOOCOMMERCE

/**
 * 5. Add product short description
 *
 * Add the product's short description (excerpt) to the WooCommerce shop/category pages.
 * The description displays after the product's name, but before the product's price.
 *
 * Ref: https://gist.github.com/om4james/9883140
 * Put this snippet into a child theme's functions.php file
 *
 * @return void
 */
function woocommerce_after_shop_loop_item_title_short_description() {
	global $product;
	// WC_Product.
	if ( ! has_excerpt( $product->get_id() ) ) {
		return;
	}
	?>
	<div itemprop="subtitle" class="color-grey-50 product-subtitle">
		<?php
			$subtitle = get_post_meta( $product->get_id(), 'product_subtitle', true );
			esc_html_e( $subtitle );
		?>
	</div>
	<?php
}
add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_after_shop_loop_item_title_short_description', 5 );

/**
 * 6. Remove Sales Circular Badge that says "SALE"
 * ===============================================
 */
add_filter( 'woocommerce_sale_flash', '__return_false' );

// 7. Customize x_woocommerce_shop_product_thumbnails
// ==================================================
// BEGIN OF "CUSTOMIZE x_woocommerce_shop_product_thumbnails IN ORDER TO ADD TWO IMAGES FOR EACH PRODCUT, INSTEAD OF ONE, FOR HOVERING SWAP EFFECT".
// Function resides on : \x\framework\functions\global\plugins\woocommerce.php
// Article where we found this solution : https://theme.co/apex/forums/topic/not-possible-to-override-x_woocommerce_shop_product_thumbnails-in-child-theme/
// Above notes by Ameba

/**
 * 1. Declare function to remove the X hook during initialization
 *
 * @return void
 */
function remove_x_action() {
	remove_action( 'woocommerce_before_shop_loop_item_title', 'x_woocommerce_shop_product_thumbnails', 10 );
	remove_action( 'woocommerce_shop_loop_item_title', 'x_woocommerce_template_loop_product_title', 10 );
	remove_action( 'woocommerce_cart_actions', 'x_woocommerce_cart_actions' );
}

/**
 * 2. Register the action that will remove the X hook during initialization
 */
add_action( 'init', 'remove_x_action' );

/**
 * 3. Declare the new custom function
 */
function custom_woocommerce_shop_product_thumbnails() {

	global $product;

	$id     = get_the_ID();
	$thumb  = 'shop_catalog';
	$rating = ( function_exists( 'wc_get_rating_html' ) ) ? wc_get_rating_html( $product->get_average_rating() ) : $product->get_rating_html();
	/**
	 * BEGIN OF 'added by Ameba'
	*/
	// get urls of gallery images (we need two images of the product in order to create the image swap effect on hovering).
	$attachment_ids      = $product->get_gallery_image_ids();
	$gallery_images_urls = array();
	foreach ( $attachment_ids as $attachment_id ) {
		array_push( $gallery_images_urls, $attachment_id );
	}
	/**
	* END OF 'added by Ameba'
	*/

	woocommerce_show_product_sale_flash();
	echo '<div class="entry-featured">';
	echo '<a href="' . get_the_permalink() . '">';

	/**
	 * If the custom fields are set up, we show those images. Otherwise we use the gallery images.
	 */
	$first_image = get_post_meta( $product->get_id(), 'yv_product_shop_product_image', true );
	$second_image = get_post_meta( $product->get_id(), 'yv_product_shop_product_icon', true );

	if( ! empty( $first_image ) ) {
		echo '<img src="' . $first_image . '" alt="" class="">';
		echo file_get_contents( $second_image );
	} else {

		/**
		* BEGIN OF 'added by Ameba'.
		* If the gallery has images, we asume the first image is the product closed, and the second opened.
		*/
	
		if ( $gallery_images_urls ) {

			$first_image_url  = wp_get_attachment_image_src( $gallery_images_urls[0], 'full' )[0];
			$second_image_url = get_attached_file( $gallery_images_urls[1] );
			echo '<img src="' . $first_image_url . '" alt="" class="">';
			if ( $second_image_url && strpos( $second_image_url, '.svg') !== false ) {
				echo file_get_contents( $second_image_url );
			}
			/* END OF 'added by Ameba' */

		} else {
			/* If the gallery has no images, we show the featured image (default behavior). */
			echo get_the_post_thumbnail( $id, 'large', $thumb );
		}
	}

	if ( ! empty( $rating ) ) {
		echo '<div class="star-rating-container aggregate">' . $rating . '</div>';
	}

	echo '</a>';
	echo '</div>';
}

	// 4. Register new action now
	add_action( 'woocommerce_before_shop_loop_item_title', 'custom_woocommerce_shop_product_thumbnails', 10 );

// END OF "// CUSTOMIZE "x_woocommerce_shop_product_thumbnails".


/**
 * 7. Customize x_woocommerce_shop_product_thumbnails
 * ==================================================
 */
// We dont want the title on the single product description.
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
// We dont want the related products.
remove_action( 'woocommerce_after_single_product_summary', 'x_woocommerce_output_related_products', 20 );

/**
 * 8. Woocommerce other hooks
 * ==================================================
 */
// remove default sorting dropdown.
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
// Remove the result count from WooCommerce.
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );

/**
 * 9. Woocommerce shop hooks (to add global block content before and after the shop products list)
 * ================================================================================================
 */
function shop_before_content() {
	$shop_page = get_post( wc_get_page_id( 'shop' ) );
	if ( $shop_page ) {
		$description = wc_format_content( $shop_page->post_content );
	}
	?>

<script id="mcjs">!function(c,h,i,m,p){m=c.createElement(h),p=c.getElementsByTagName(h)[0],m.async=1,m.src=i,p.parentNode.insertBefore(m,p)}(document,"script","https://chimpstatic.com/mcjs-connected/js/users/2e8774f004e062b73c45d548b/6f6eb6423fb97a3680078f4f8.js");</script>


	<div class="shop-nav-menu">
		<div class="x-container max width offset  ">
			<div class="flex-align-vertical">
				<div class="col active">
					<a href="#shop-kits-title" data-vc-accordion="true" data-scroll>
						<svg id="2115b6fa-b2ff-48cc-9dde-da12b444a0f6" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" width="22.164" height="23.43" viewBox="0 0 22.164 23.43"><title>supplements-kits-icon</title><path d="M249.615,260.69h-.007a31.784,31.784,0,0,1-8.421-.342V240.921h13.4v7.408a.884.884,0,1,0,1.768,0v-8.292a.884.884,0,0,0-.883-.884H240.3a.885.885,0,0,0-.884.884v21.049a.882.882,0,0,0,.709.867,36.555,36.555,0,0,0,7.064.63c1.141,0,1.276-.043,2.533-.129a.886.886,0,0,0,.71-1.328A.908.908,0,0,0,249.615,260.69Z" transform="translate(-239.418 -239.153)" fill="teal"/><path d="M261.516,254.656a2.912,2.912,0,0,0-1-1.59l-2.573-2.144a2.767,2.767,0,0,0-1.772-.641l-.088,0a2.753,2.753,0,0,0-2.438,1.62,2.836,2.836,0,0,0,.824,3.335l.621.518a1.73,1.73,0,0,0,2.356-.2l1.366-1.608.647.539a1.012,1.012,0,0,1,.288,1.143.9.9,0,0,0,.844,1.191.854.854,0,0,0,.8-.53A2.8,2.8,0,0,0,261.516,254.656Zm-4.065-1.838-1.284,1.54-.644-.538a.994.994,0,0,1,.58-1.762l.068-.008a.992.992,0,0,1,.635.23Zm-1.364-2.262Z" transform="translate(-239.418 -239.153)" fill="teal"/><path d="M260.707,258.33a2.827,2.827,0,0,0-.348-.446,2.749,2.749,0,0,0-1.985-.843h-3.448a2.774,2.774,0,0,0-2.771,2.771,2.8,2.8,0,0,0,2.771,2.771h3.448a2.758,2.758,0,0,0,2.579-3.766A2.849,2.849,0,0,0,260.707,258.33Zm-1.331,1.482a1,1,0,0,1-1,1h-.841v-2l.88,0a.981.981,0,0,1,.378.091,1.02,1.02,0,0,1,.585.907Zm-3.611-1v2h-.839a1,1,0,0,1-1-1,1,1,0,0,1,1-1Z" transform="translate(-239.418 -239.153)" fill="teal"/><path d="M253.425,242.975a.884.884,0,0,0-.883-.884H243a.884.884,0,1,0,0,1.768h9.543A.884.884,0,0,0,253.425,242.975Z" transform="translate(-239.418 -239.153)" fill="teal"/><path d="M243,257.707a.884.884,0,1,0,0,1.768h7.414a.884.884,0,1,0,0-1.768Z" transform="translate(-239.418 -239.153)" fill="teal"/></svg>
						Supplement Kits
					</a>
				</div>
				<div class="col">
					<a  href="#bundles-kits" data-vc-accordion="true" data-scroll>
						<svg id="1e6ec7fe-0a56-446c-be09-97728d3d3ee2" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" width="25.841" height="24.744" viewBox="0 0 25.841 24.744"><title>bundles-kits-icon</title><path d="M265.941,243.647a.849.849,0,0,0-.849.85v5.671l-8.619,3.023v-5.632a.85.85,0,1,0-1.7,0v5.632l-8.619-3.023V244.5a.85.85,0,1,0-1.7,0v6.273a.849.849,0,0,0,.568.8l10.319,3.62.012,0a.87.87,0,0,0,.255.044l.014,0a.84.84,0,0,0,.269-.046l.012,0,10.319-3.62a.849.849,0,0,0,.568-.8V244.5A.849.849,0,0,0,265.941,243.647Z" transform="translate(-242.703 -230.496)" fill="teal"/><path d="M268.466,240.617l-1.752-3.809,0-.007a.841.841,0,0,0-.059-.084c0-.008-.012-.016-.018-.024a.8.8,0,0,0-.077-.11.417.417,0,0,0-.034-.038.847.847,0,0,0-.085-.058l-.03-.02a.892.892,0,0,0-.1-.069l-.053-.022c-.01,0-.017-.012-.026-.015l-5.292-1.856a.85.85,0,0,0-.563,1.6l2.93,1.027-7.675,2.522-7.675-2.522,2.93-1.027a.85.85,0,0,0-.563-1.6l-5.292,1.856c-.009,0-.016.011-.025.015a.509.509,0,0,0-.054.022.875.875,0,0,0-.112.075l-.022.014a.847.847,0,0,0-.085.058c-.012.011-.021.024-.032.036a.955.955,0,0,0-.082.115l-.013.019a.887.887,0,0,0-.061.086l0,.007-1.752,3.809a.853.853,0,0,0,.467,1.149l10.095,3.885a.84.84,0,0,0,.3.057.85.85,0,0,0,.773-.5l1.2-2.622,1.2,2.622a.85.85,0,0,0,.773.5.84.84,0,0,0,.305-.057l10.1-3.885a.853.853,0,0,0,.467-1.149Zm-10.428,3.161-1.245-2.714,7.12-2.34,1.573-.517,1.057,2.3ZM244.7,240.505l1.057-2.3,1.573.517,7.12,2.34-1.245,2.714Z" transform="translate(-242.703 -230.496)" fill="teal"/><path d="M253.6,234.039a.536.536,0,0,1,.154.474l-.2,1.156a.536.536,0,0,0,.777.565l1.038-.546a.535.535,0,0,1,.5,0l1.038.546a.536.536,0,0,0,.777-.565l-.2-1.156a.536.536,0,0,1,.154-.474l.84-.818a.535.535,0,0,0-.3-.913l-1.161-.169a.538.538,0,0,1-.4-.293l-.518-1.052a.536.536,0,0,0-.961,0l-.519,1.052a.538.538,0,0,1-.4.293l-1.161.169a.535.535,0,0,0-.3.913Z" transform="translate(-242.703 -230.496)" fill="teal"/></svg>
						Bundle & Save
					</a>
				</div>
				<div class="col">
					<a  href="#essential-oils-section" data-vc-accordion="true" data-scroll>
						<svg id="508ec187-5fca-44b2-92f1-957c371b2b01" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" width="16.419" height="25" viewBox="0 0 16.419 25"><title>oils-kits-icon</title><path d="M261.657,247.31a.305.305,0,0,1,.3.3v6.132h-3.977v-6.132a.305.305,0,0,1,.3-.3h3.369m0-1.621h-3.369a1.925,1.925,0,0,0-1.925,1.925v7.236a.517.517,0,0,0,.517.518h6.185a.517.517,0,0,0,.517-.518v-7.236a1.925,1.925,0,0,0-1.925-1.925Z" transform="translate(-247.163 -230.368)" fill="teal"/><path d="M254.528,232.2a1.832,1.832,0,0,0-3.664,0Z" transform="translate(-247.163 -230.368)" fill="teal"/><path d="M255.039,234.791h-4.686a.811.811,0,1,1,0-1.622h4.686a.811.811,0,0,1,0,1.622Z" transform="translate(-247.163 -230.368)" fill="teal"/><path d="M255.039,237.483h-4.686a.811.811,0,1,1,0-1.622h4.686a.811.811,0,0,1,0,1.622Z" transform="translate(-247.163 -230.368)" fill="teal"/><path d="M254.328,255.368h-6.354a.811.811,0,0,1-.811-.811v-11.71a3.609,3.609,0,0,1,1.14-3.091,8.876,8.876,0,0,0,.988-1.208.811.811,0,0,1,1.343.91,10.485,10.485,0,0,1-1.15,1.41c-.537.57-.7.742-.7,1.979v10.9h5.543a.811.811,0,0,1,0,1.622Z" transform="translate(-247.163 -230.368)" fill="teal"/><path d="M257.356,244.464a.811.811,0,0,1-.811-.811v-.806c0-1.237-.162-1.409-.7-1.979a10.485,10.485,0,0,1-1.15-1.41.811.811,0,0,1,1.343-.91,8.876,8.876,0,0,0,.988,1.208,3.609,3.609,0,0,1,1.14,3.091v.806A.811.811,0,0,1,257.356,244.464Z" transform="translate(-247.163 -230.368)" fill="teal"/></svg>
						Essential Oils
						<svg version="1.1" id="new-icon" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"width="30px" height="15px" viewBox="0 0 30 15" enable-background="new 0 0 30 15" xml:space="preserve"><g><path fill="#48B7AD" d="M27.9,15H2.1C0.9,15,0,14.1,0,12.9V2.1C0,0.9,0.9,0,2.1,0h25.9C29.1,0,30,0.9,30,2.1v10.9C30,14.1,29.1,15,27.9,15z"/><g><path fill="#FFFFFF" d="M9.4,4.8h1.2v5.5H9.4L6.8,6.8v3.4H5.6V4.8h1.1l2.7,3.5V4.8z"/><path fill="#FFFFFF" d="M15.9,4.8v1.1h-2.7V7h2.4v1h-2.4v1.1H16v1.1h-4V4.8H15.9z"/><path fill="#FFFFFF" d="M18.8,7.9l1-3.1H21l1,3.1l1.1-3.1h1.3l-1.9,5.5h-0.9l-1.2-3.8l-1.2,3.8h-0.9l-1.9-5.5h1.3L18.8,7.9z"/></g></g>
						</svg>
						<div id="oil-notification"></div>
					</a>
				</div>
			</div>
		</div>
	</div>
	<div class="x-container max width offset shop-title-wrapper">
		<div class="x-column x-sm x-1-1 text-center">
			<h1 class="page-title" id="shop-kits-title">
				<?php echo woocommerce_page_title(); ?>
			</h1>
			<?php
			if ( $description ) {
				echo '<div class="page-description">' . $description . '</div>';
			}
			?>
		</div>
	</div>
	<?php
	// The default global block ID (2042) for the shop header.
	$global_block_id = false;

	parse_str( $_SERVER['QUERY_STRING'], $get_query_vars );
	if ( ! empty( $get_query_vars['cr'] ) ) {
		$coupon_name  = sanitize_title( $get_query_vars['cr'] );
		$global_block = get_page_by_title( 'Shop - before products (' . $coupon_name . ')', OBJECT, 'cs_global_block' );
		if ( $global_block ) {
			echo '<div class="entry-content content">';
			$global_block_id = $global_block->ID;
			echo do_shortcode( '[cs_gb id=' . $global_block_id . ']' );
			echo '</div>';
		}
	}
	echo '<div class="x-container products-shop-list max width offset">';
}
add_action( 'woocommerce_before_shop_loop', 'shop_before_content' );


/**
 * Custom markup before WooCommerce notices.
 *
 * @return void
 */
function wc_print_notices_custom_before() {
	echo '<div class="x-container max width mt30px">';
}
add_action( 'woocommerce_before_single_product', 'wc_print_notices_custom_before', 9 );

/**
 * Custom markup after WooCommerce notices.
 *
 * @return void
 */
function wc_print_notices_custom_after() {
	echo '</div>';
}
add_action( 'woocommerce_before_single_product', 'wc_print_notices_custom_after', 10 );

/**
 * 10. User old products info
 * ============================
 * Display old users products (from the old youveda system). "CMB2" and "MC Product custom post" plugins must be activated.
 * We are hidding the "Product" custom post type because we dont need that to be visible on the admin, just activated.
 */

/**
 * Get metabox field options
 *
 * @param  string $post_type
 * @return arr    $options    Options for "select" field
 */
if ( ! function_exists( 'cmb2_get_post_type_options' ) ) {
	function cmb2_get_post_type_options( $post_type ) {

		$posts = get_posts(
			array(
				'post_type'   => $post_type,
				'numberposts' => 50,
				'order'       => 'ASC',
				'orderby'     => 'name',
			)
		);

		// Initate an empty array.
		$options = array();
		foreach ( $posts as $post ) {
			$options[ $post->ID ] = $post->ID . ' - ' . $post->post_title;
		}
		return $options;
	}
}

/**
 * User metaboxes
 */
add_action( 'cmb2_admin_init', 'user_custom_metaboxes' );

/**
 * Hook in and add a metabox to add fields to the user profile pages
 *
 * @return void
 */
if ( ! function_exists( 'user_custom_metaboxes' ) ) {
	function user_custom_metaboxes() {
		$prefix   = '_youveda_user_';
		$cmb_user = new_cmb2_box(
			array(
				'id'               => $prefix . 'user_mb',
				// Doesn't output for user boxes.
				'title'            => __( 'Shopify Data', 'cmb2' ),
				// Tells CMB2 to use user_meta vs post_meta.
				'object_types'     => array( 'user' ),
				'show_names'       => true,
				// where form will show on new user page. 'add-existing-user' is only other valid option.
				'new_user_section' => 'add-new-user',
			)
		);

		$cmb_user->add_field(
			array(
				'name'     => __( 'iOS User ID', 'cmb2' ),
				'desc'     => __( 'iOS User ID', 'cmb2' ),
				'id'       => '_yv_ios_id',
				'type'     => 'text',
				'on_front' => false,
			)
		);

		$user_sus_group = $cmb_user->add_field(
			array(
				'id'          => $prefix . 'subscriptions',
				'type'        => 'group',
				'description' => __( '-', 'cmb2' ),
				'options'     => array(
					'group_title'   => __( 'Subscription {#}', 'cmb2' ), // {#} gets replaced by row number
					'add_button'    => __( 'Add subscription', 'cmb2' ),
					'remove_button' => __( 'Remove subscription', 'cmb2' ),
					'sortable'      => true,
				),
			)
		);

		$cmb_user->add_group_field(
			$user_sus_group,
			array(
				'name' => __( 'Shopify Order ID', 'cmb2' ),
				'id'   => 'shopify_order_id',
				'type' => 'text',
			)
		);

		$cmb_user->add_group_field(
			$user_sus_group,
			array(
				'name'             => __( 'Product', 'cmb2' ),
				'id'               => 'shopify_product_id',
				'type'             => 'select',
				'attributes'       => array(
					'autocomplete' => 'off',
				),
				'show_option_none' => true,
				'options'          => cmb2_get_post_type_options( 'product' ),
			)
		);

		$cmb_user->add_group_field(
			$user_sus_group,
			array(
				'name'             => __( 'Shopify Product Timestamp', 'cmb2' ),
				'id'               => 'shopify_product_timestamp',
				'show_option_none' => true,
				'type'             => 'text_date_timestamp',
			)
		);

		$cmb_user->add_group_field(
			$user_sus_group,
			array(
				'name'       => __( 'Subscription Status', 'cmb2' ),
				'id'         => 'status',
				'type'       => 'select',
				'default'    => 'single',
				'attributes' => array(
					'autocomplete' => 'off',
				),
				'options'    => array(
					'single'    => __( 'Single', 'cmb2' ),
					'active'    => __( 'Active', 'cmb2' ),
					'cancel'    => __( 'Processing cancelation', 'cmb2' ),
					'cancelled' => __( 'Cancelled', 'cmb2' ),
					'expired'   => __( 'Expired', 'cmb2' ),
				),
			)
		);

		$cmb_user->add_group_field(
			$user_sus_group,
			array(
				'name' => 'Migrated',
				'desc' => 'Already migrated to new WC subscription?',
				'id'   => 'migrated',
				'type' => 'checkbox',
			)
		);

		$user_eemails_group = $cmb_user->add_field(
			array(
				'id'          => $prefix . 'emails',
				'type'        => 'group',
				'description' => __( '', 'cmb2' ),
				'options'     => array(
					'group_title'   => __( 'Extra e-mail {#}', 'cmb2' ), // {#} gets replaced by row number
					'add_button'    => __( 'Add e-mail', 'cmb2' ),
					'remove_button' => __( 'Remove e-mail', 'cmb2' ),
					'sortable'      => true, // beta.
				),
			)
		);

		$cmb_user->add_group_field(
			$user_eemails_group,
			array(
				'name' => __( 'e-mail address', 'cmb2' ),
				'id'   => 'email_data',
				'type' => 'text',
			)
		);
	}
}

// 11. Product Activation related functions.
// =========================================
// validate activation code.
add_filter( 'gform_validation_8', 'yv_validate_activation_code', 10, 1 );
if ( ! function_exists( 'yv_validate_activation_code' ) ) {
	/**
	 * Activate kit Validation
	 *
	 * @param  array   $validation_result   GF validation results.
	 * @return array   $validation_result   GF validation results.
	 */
	function yv_validate_activation_code( $validation_result ) {

		$form = $validation_result['form'];
		// Loop through the form fields.
		foreach ( $form['fields'] as &$field ) {
			// 7 - Check if the field is hidden by GF conditional logic.
			$is_hidden = RGFormsModel::is_field_hidden( $form, $field, array() );
			if ( $field_page !== $current_page || $is_hidden ) {
				continue;
			}

			// 9 - Get the submitted value from the $_POST
			$field_value = rgpost( "input_{$field['id']}" );
			if ( '1' === $field->id ) {
				if ( get_post_type( $field_value ) !== 'product' || ! absint( $field_value ) ) {
					$validation_result['is_valid'] = false;
					$field->failed_validation      = true;
					$field->validation_message     = 'Please select a valid product';
				} else {
					continue;
				}
			} elseif ( '2' === $field->id ) {

				$validation_result['is_valid'] = false;
				$field->failed_validation      = true;
				$field->validation_message     = 'Activation code is not valid';

				$product_id      = rgpost( 'input_1' );
				$activation_code = rgpost( 'input_2' );

				$request = new WP_REST_Request( 'POST', '/yv/v1/products/activate' );

				$arr_params = array_filter(
					array(
						'activation_code' => $activation_code,
						'product_id'      => $product_id,
					)
				);

				// add params to request.
				foreach ( $arr_params as $key => $value ) {
					$request->set_param( $key, $value );
				}
				$results = rest_do_request( $request );

				if ( ! $results->is_error() && 201 === $results->get_status() ) {
					$response                      = $results->get_data();
					$validation_result['is_valid'] = true;
					$field->failed_validation      = false;
				} elseif ( $results->is_error() ) {
					$field->validation_message = $results->as_error()->get_error_message();
				}
			}
		}

		// Assign modified $form object back to the validation result.
		$validation_result['form'] = $form;
		return $validation_result;
	}
}


add_filter( 'gform_confirmation_8', 'yv_confirmation_for_program_activation', 10, 4 );
if ( ! function_exists( 'yv_confirmation_for_program_activation' ) ) {
	function yv_confirmation_for_program_activation( $confirmation, $form, $entry, $ajax ) {
		$subscriptions_url = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) . 'subscriptions?newsubscription';
		$confirmation      = array( 'redirect' => $subscriptions_url );
		return $confirmation;
	}
}

// TO REMOVE or TO KEEP?: do we need this function?
if ( ! function_exists( 'yv_add_single_subscription' ) ) {
	function yv_add_single_subscription( $userID, $productsID_array, $orderID, $subs_type, $orderTime ) {

		$userOrders = get_user_meta( $userID, '_youveda_user_subscriptions', true );
		if( ! empty( $userOrders ) ) {
			foreach ( $userOrders as $index=>$order ) {
				if( in_array( $order['shopify_product_id'], $productsID_array ) ){
					//if is a new recursive order and already has the subscription, keep the original one.
					if ( $order['status'] == 'active' ){
						$elementPos = array_search( $order['shopify_product_id'], $productsID_array, true );
						unset( $productsID_array[$elementPos] );

					} elseif ( 'single' === $order['status'] ) {
						//if is a new recursive order and user has a single subscription, update data.
						if (  $orderID === $order['shopify_order_id'] || $subs_type === 'active' ) {
							unset( $userOrders[$index] );
						}

					} elseif ( 'cancelled' === $order['status'] || 'expired' === $order['status'] ) {
						// if the subscription state is not active, update data.
						unset( $userOrders[ $index ] );
					}
				}
			}
		} else {
			$userOrders = array();
		}

		if ( ! empty( $productsID_array ) ) {
			$new_products_to_iOS = array();
			foreach ( $productsID_array as $productID ) {
				$newOrder = array(
					'shopify_product_id'        => strval( $productID ),
					'shopify_product_timestamp' => $orderTime,
					'shopify_order_id'          => $orderID,
					'status'                    => $subs_type,
				);
				$new_products_to_iOS[] = $newOrder;
				array_push( $userOrders, $newOrder );
			}

			// reset keys.
			$userOrders = array_values( $userOrders );

			sort_multidimensional_by_key( $userOrders, 'shopify_product_timestamp' );
			$updatedOrder = update_user_meta( $userID, '_youveda_user_subscriptions', $userOrders );

			do_action( 'kit_activated_WP', $userID, $productsID_array, $orderID, $subs_type, $orderTime );
			return '[Order-Updated]';

		} else {
			return "[Error]: Order already exists";
		}
	}
}

if ( ! function_exists( 'sort_multidimensional_by_key' ) ) {
	/**
	 * Sort multidimensionl array by a given key
	 *
	 * @param  array  $array    Input array.
	 * @param  string $sort_key Key name to sort by.
	 * @param  string $sort     Sort flag.
	 * @return array  $array    The sorted array.
	 */
	function sort_multidimensional_by_key( &$array, $sort_key, $sort = SORT_DESC ) {

		$key = array();
		foreach ( $array as $index => $row ) {
			$key[ $index ] = $row[ $sort_key ];
		}
		return array_multisort( $key, $sort, $array );
	}
}

/**
 * 12. Login / Logout
 *
 * @param [type] $items [description].
 * @param [type] $args  [description].
 */
function add_login_logout_register_menu( $items, $args ) {
	if ( 'primary' !== $args->theme_location ) {
		return $items;
	}

	if ( is_user_logged_in() ) {
		$items .= '<li class="user mobile"><a href="#"><span class="icon youveda-user-icon"></span></a><div></div></li>';
		$items .= '<li class="user menu-item menu-item-has-children desktop">
					<a href="#"><span class="icon youveda-user-icon"></span></a>
					<ul class="sub-menu">
						<li><a href="' . get_permalink( get_option( 'woocommerce_myaccount_page_id' )) . '">My Account</a></li>';
		if ( ! isset( $_COOKIE['appview'] ) ) {
			$items .= '<li><a href="' . wp_logout_url() . '&returnTo=https://youveda2018.wpengine.com">' . __( 'Log Out' ) . '</a></li>';
		}
		$items .= '</ul></li>';
	} else {
		$items .= '<li class="user menu-item-has-children">
					<a href="' . wp_login_url() . '"><span class="icon youveda-user-icon"></span><i class="fas fa-check"></i></a>
				</li>';
	}

	return $items;
}

add_filter( 'wp_nav_menu_items', 'add_login_logout_register_menu', 199, 2 );

/**
 * 13. Cornerstone Plugin > Classic Recent Posts (modify plugin).
 *
 * @param  [type] $atts [description].
 * @return [type]       [description].
 */
function x_shortcode_recent_posts_v2code( $atts ) {
	extract(
		shortcode_atts(
			array(
				'id'          => '',
				'class'       => '',
				'style'       => '',
				'type'        => 'post',
				'count'       => '',
				'category'    => '',
				'offset'      => '',
				'orientation' => '',
				'no_sticky'   => '',
				'no_image'    => '',
				'fade'        => '',
			),
			$atts,
			'x_recent_posts'
		)
	);

	$allowed_post_types = apply_filters( 'cs_recent_posts_post_types', array( 'post' => 'post' ) );
	$type               = ( isset( $allowed_post_types[ $type ] ) ) ? $allowed_post_types[ $type ] : 'post';

	$id            = ( '' !== $id ) ? 'id="' . esc_attr( $id ) . '"' : '';
	$class         = ( '' !== $class ) ? 'x-recent-posts cf ' . esc_attr( $class ) : 'x-recent-posts cf';
	$style         = ( '' !== $style ) ? 'style="' . $style . '"' : '';
	$count         = ( '' !== $count ) ? $count : 3;
	$category      = ( '' !== $category ) ? $category : '';
	$category_type = ( 'post' === $type ) ? 'category_name' : 'portfolio-category';
	$offset        = ( '' !== $offset ) ? $offset : 0;
	$orientation   = ( '' !== $orientation ) ? ' ' . $orientation : ' horizontal';
	$no_sticky     = ( 'true' === $no_sticky );
	$no_image      = ( 'true' === $no_image ) ? $no_image : '';
	$fade          = ( 'true' === $fade ) ? $fade : 'false';

	$js_params = array(
		'fade' => ( 'true' === $fade ),
	);

	$data = cs_generate_data_attributes( 'recent_posts', $js_params );

	$output = "<div {$id} class=\"{$class}{$orientation}\" {$style} {$data} data-fade=\"{$fade}\" >";

	$q = new WP_Query(
		array(
			'orderby'             => 'date',
			'post_type'           => "{$type}",
			'posts_per_page'      => "{$count}",
			'offset'              => "{$offset}",
			"{$category_type}"    => "{$category}",
			'ignore_sticky_posts' => $no_sticky,
		)
	);

	if ( $q->have_posts() ) :
		while ( $q->have_posts() ) :
			$q->the_post();

			if ( 'true' === $no_image ) {
				$image_output       = '';
				$image_output_class = 'no-image';
			} else {
				$image              = wp_get_attachment_image_src( get_post_thumbnail_id(), 'entry' );
				$bg_image           = ( '' !== $image[0] ) ? ' style="background-image: url(' . $image[0] . ');"' : '';
				$image_output       = '<div class="x-recent-posts-img"' . $bg_image . '></div>';
				$image_output_class = 'with-image';
			}
			$output .= '<a class="x-recent-post' . $count . ' ' . $image_output_class . '" href="' . get_permalink( get_the_ID() ) . '" title="' . esc_attr( sprintf( csi18n( 'shortcodes.recent-posts-permalink' ), the_title_attribute( 'echo=0' ) ) ) . '">'
			. '<article id="post-' . get_the_ID() . '" class="' . implode( ' ', get_post_class() ) . '">'
			. '<div class="entry-wrap">'
			. $image_output
			. '<div class="x-recent-posts-content">'
			. '<h3 class="h-recent-posts">' . get_the_title() . '</h3>'
			. '<span class="x-recent-posts-date">' . get_the_date() . '</span>'
			. '</div>'
			. '</div>'
			. '</article>'
			. '</a>';

		endwhile;
	endif;
	wp_reset_postdata();

	$output .= '</div>';
	return $output;
}


add_action( 'wp_head', 'change_recent_posts_to_v2' );

/**
 * Change_recent_posts_to_v2
 *
 * @return void
 */
function change_recent_posts_to_v2() {
	remove_shortcode( 'x_recent_posts' );
	add_shortcode( 'x_recent_posts', 'x_shortcode_recent_posts_v2code' );
}

// 14. Woocommerce -> Checkout > Ship to a different address?
// ==========================================================
add_filter( 'woocommerce_ship_to_different_address_checked', '__return_false', 99 );

/**
 * 15. Woocommerce -> Hide SKU number from single page
 * Disable SKU support for non Bundle products
 *
 * @return bool $enabled Control field support and visibility
 */
function yv_disable_sku() {
	$enabled = false;
	if ( 'product' === get_post_type( get_the_ID() ) ) {
		$product = wc_get_product( get_the_ID() );
		if ( 'bundle' === $product->get_type() || 'grouped' === $product->get_type() ) {
			$enabled = true;
		}
	}
	return $enabled;
}

add_filter( 'wc_product_sku_enabled', 'yv_disable_sku' );


/**
 * 16. Change Woocommerce My Account Tabs -> Re-order items + hide "logout" if I am in app view
 *
 * @return array $myorder Custom order for the tabs.
 */
function wpb_woo_my_account_order() {
	$myorder = array(
		'dashboard'       => __( 'Dashboard', 'woocommerce' ),
		'orders'          => __( 'Orders', 'woocommerce' ),
		'subscriptions'   => __( 'Subscriptions', 'woocommerce' ),
		'edit-address'    => __( 'Address Book', 'woocommerce' ),
		'payment-methods' => __( 'Payment 	Methods', 'woocommerce' ),
	);

	if ( ! isset( $_COOKIE['appview'] ) ) {
		$myorder['customer-logout'] = __( 'Logout', 'woocommerce' );
	}
	return $myorder;
}

add_filter( 'woocommerce_account_menu_items', 'wpb_woo_my_account_order' );

/**
 * 17. LOGIN CSS (sure needs to be somewhere else...)
 *
 * @return void
 */
function yv_my_login_logo() {
	?>
	<style type="text/css">
		#login h1 a, .login h1 a {
		background-image: url(<?php echo esc_attr( get_stylesheet_directory_uri() ); ?>/images/youveda-logo.svg);
		height:65px;
		width:320px;
		pointer-events:none;
		background-size: 320px 65px;
		background-repeat: no-repeat;
		padding-bottom: 30px;
		}

		#backtoblog{display:none;}
		.login #nav {
		    font-size: 1.2em;
		    text-align: center;
		    font-weight: bold;
		    letter-spacing: 0.025em;
		    word-spacing: 0.1em;
		}
	</style>
	
	<?php
}

add_action( 'login_enqueue_scripts', 'yv_my_login_logo' );

function ts_redirect_login( $redirect, $user ) {

$redirect_page_id = url_to_postid( $redirect );
$checkout_page_id = wc_get_page_id( 'checkout' );

if( $redirect_page_id == $checkout_page_id ) {
return $redirect;
}

return wc_get_page_permalink( 'shop' );
}

add_filter( 'woocommerce_login_redirect', 'ts_redirect_login' );

add_action('template_redirect','check_if_logged_in');
function check_if_logged_in()
{
 if (! is_user_logged_in() &&  !is_wc_endpoint_url( 'lost-password' ) && is_account_page()) {
        wp_redirect( '/wp-login.php' );
        exit;
    }
}


if ( ! function_exists( 'yv_forced_redirect_from_app' ) ) {
	/**
	 * 18. TO BE REMOVED and FIXED BY ASAP AFTER LAUNCH > Detect if user is in the app,
	 * add loading while logging the user in and redirect. Also set a COOKIE appview used in 16.
	 *
	 * @return void
	 */
	function yv_forced_redirect_from_app() {

		if ( isset( $_GET['app-forced-redirect'] ) ) {
			$destination = get_home_url() . $_GET['app-forced-redirect'] . '?=appview';
			echo "<style>body{opacity:1}html:before{ content: '';background:#000;display:block;height:100vh;width:100vw;position:fixed;top:0;opacity:0.5;z-index: 99999;}html:after{content: '';display: block;background-size: contain;top: calc(50vh - 31px);position: absolute;left: calc(50vw - 31px);width: 62px;height: 62px; z-index: 99999;background-image: url(/wp-content/themes/x-child/images/youveda-loader.svg);background-repeat: no-repeat;animation: youveda-ajax-spinner 0.8s linear infinite;background-position: center; }</style>";
			?>
			<script>
				document.addEventListener('DOMContentLoaded', function() {
					window.location.replace('<?php echo $destination; ?>');
				}, 3000);
			</script>
			<?php
		}

		if ( isset( $_GET['appview'] ) ) {
			setcookie( 'appview', 'you are logged in the app =)', strtotime( '+1 day' ) );
		}
	}
}
add_action( 'init', 'yv_forced_redirect_from_app' );

if ( ! function_exists( 'yv_import_user_from_auth0' ) ) {
	/**
	 * 19. TO BE REMOVED and FIXED AFTER LAUNCH > Manually import a user from auth0: Auth0 hits WordPress and sends the needed data over GET params.
	 * Refactor using auth0 WordPress plugin methods for importing the user the right way.
	 *
	 * @return void
	 */
	function yv_import_user_from_auth0() {
		if ( is_admin() || is_user_logged_in() ) {
			return;
		}
		/* CREATE USER FROM AUTH0 */
		if ( isset( $_GET['userC'] ) ) {

			$username = $_GET['username'];
			$email    = $_GET['email'];
			$userdata = array(
				'user_login' => $username,
				'user_email' => $email,
				// When creating an user, `user_pass` is expected.
				'user_pass'  => null,
			);

			$user_id = wp_insert_user( $userdata );

			/* Auth0 metadata */
			$id             = $_GET['id'];
			$connection     = $_GET['connection'];
			$email_verified = $_GET['email_verified'];

			/*clavado */
			$provider  = 'auth0';
			$is_social = false;
			$nonce     = 'nonce';
			$iss       = 'https:\/\/youveda1.auth0.com\/';

			/* clavado y peligroso */
			$aud     = '2VOOTlC8eT06jR0h4cmqXOPctzoHxO47';
			$iat     = 1525625917;
			$exp     = 1525661917;
			$at_hash = '3iky9Q1qNbLs3iAlcD7Q1g';

			if ( 'facebook' === $connection || 'twitter' === $connection || 'google-oauth2' === $connection ) {
				$is_social = true;
				$provider  = $connection;
			}

			$object = '{"https:\/\/youveda.com\/identities":[{"user_id":"' . $id . '","provider":"' . $provider . '","connection":"' . $connection . '","isSocial":"' . $is_social . '""}],"email":' . $email . ',"email_verified":' . $email_verified.',"iss":"' . $iss . '","sub":"' . $provider . '|' . $id . '","aud":"' . $aud . '","iat":' . $iat . ',"exp":' . $exp . ',"at_hash":"' . $at_hash . '","nonce":"' . $nonce . '","user_id":"' . $provider . '|' . $id . '"}';

			add_user_meta( $user_id, 'wp_auth0_id', $provider . '|' . $id );
			add_user_meta( $user_id, 'wp_auth0_obj', $object );

			// On success.
			if ( ! is_wp_error( $user_id ) ) {
				echo 'User created : ' . $user_id;
			} else {
				echo 'error when created : ';
			}
			die;
		}
	}
}

add_action( 'init', 'yv_import_user_from_auth0' );

/**
 * REMOVE? add_filter( 'woocommerce_order_number', 'change_woocommerce_order_number' );
 */
/**
 * 20. Add a prefix to Orders.
 *
 * @param  string $order_id     The WC order ID.
 * @return string $new_order_id
 */
function change_woocommerce_order_number( $order_id ) {
	$prefix = 'WY-';
	$suffix = '';

	$new_order_id = $prefix . $order_id . $suffix;
	return $new_order_id;
}


/**
 * 21. Show price (instead of sale price) on the archive (shop)
 */

/**
 * Define the woocommerce_get_price_html callback
 *
 * @param string     $price     Price HTML string.
 * @param WC_product $instance  Instance of WP_Product.
 * @return string    $price     Price HTML string.
 */
function filter_woocommerce_get_price_html( $price, $instance ) {
	$is_shop = is_shop();
	if ( $is_shop && 'grouped' === $instance->get_type() ) {
		$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );
		$child_prices     = array();
		$children         = array_filter( array_map( 'wc_get_product', $instance->get_children() ), 'wc_products_array_filter_visible_grouped' );

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
		if ( '' !== $min_price ) {
			if( floor( $min_price ) == $min_price ) {
				$price = wc_price( $min_price, array( 'decimals' => 0 ) );
			} else {
				$price = wc_price( $min_price, array( 'decimals' => 2 ) );
			}
		} else {
			$price = $product->get_price();
		}
		$str_price = is_shop() || is_product_category() ? 'From' : 'Regular Price';
		return '<span class="from">' . $str_price . ': </span><span class="woocommerce-Price-amount amount">' . $price . '</span>';
	} else {
		return $price;
	}
};

// add the filter.
add_filter( 'woocommerce_get_price_html', 'filter_woocommerce_get_price_html', 10, 2 );

/**
 * 22. Remove Company Input Field in Checkout Page
 *
 * @param array $fields  Checkout fields.
 * @return array $fields
 */
function custom_override_checkout_fields( $fields ) {
	unset( $fields['billing']['billing_company'] );
	return $fields;
}
add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields' );

if ( ! function_exists( 'yv_calculate_coupon_discount_totals' ) ) {
	/**
	 * 23.   Coupons change on the fly
	 * Function to re-calculate the total discount applied by coupons on recurring subscriptions
	 *
	 * @param  float      $discount           Calculated discount from product price + coupon %.
	 * @param  float      $discounting_amount Amount the coupon is being applied to.
	 * @param  array|null $cart_item          Cart item being discounted if applicable.
	 * @param  boolean    $single             True if discounting a single qty item, false if its the line.
	 * @param  object     $wc_coupon          Instance of WC_Coupon.
	 * @return float      $discount           The calculation after aplying a discount for the difference between.
	 *                                        subscription discount and coupon discount.
	 */
	function yv_calculate_coupon_discount_totals( $discount, $discounting_amount, $cart_item, $single, $wc_coupon ) {

		// do not apply coupons if added product belongs to a bundle.
		if ( array_key_exists( 'bundled_item_id', $cart_item ) && ! empty( $cart_item['bundled_item_id'] ) ) {
			return 0;
		}
		$product = wc_get_product( $cart_item['product_id'] );

		if ( 'variable-subscription' === $product->get_type() ) {
			$reg_price  = $cart_item['data']->get_regular_price();
			$sale_price = $cart_item['data']->get_sale_price();
			$qty        = isset( $cart_item['quantity'] ) && ! empty( $cart_item['quantity'] ) ? $cart_item['quantity'] : 1;
			$amount     = (float) $wc_coupon->get_amount();

			if ( 'percent' === $wc_coupon->get_discount_type() ) {
				if ( ! empty( $sale_price ) && ! empty( $reg_price ) ) {
					$amount   = $amount - ( ( $reg_price - $sale_price ) * 100 / $reg_price );
					$amount   = $amount < 0 ? 0 : $amount;
					$discount = ( $reg_price * $amount / 100 ) * $qty;
				}
			}
		}
		return $discount;
	}
}

add_filter( 'woocommerce_coupon_get_discount_amount', 'yv_calculate_coupon_discount_totals', 10, 5 );


// When a product is saved, after it is saved we check replace its SKU for its parent SKU.
add_action( 'added_post_meta', 'mp_sync_on_product_save', 10, 4 );
add_action( 'updated_post_meta', 'mp_sync_on_product_save', 10, 4 );

/**
 * 24.   Add same SKU to the products than their grouped product.
 *
 * @param  int    $meta_id     Description.
 * @param  int    $post_id     Description.
 * @param  string $meta_key    Description.
 * @param  string $meta_value  Description.
 * @return void
 */
function mp_sync_on_product_save( $meta_id, $post_id, $meta_key, $meta_value ) {
	if ( '_edit_lock' === $meta_key ) {

		// we've been editing the post.
		if ( get_post_type( $post_id ) === 'product' ) {

			// we've been editing a product.
			$product  = wc_get_product( $post_id );
			$children = get_post_meta( $post_id, '_children', true );
			if ( 'bundle' === $product->get_type() || 'grouped' === $product->get_type()  ) {return;}
			
			// this is not a grouped nor bundle product 
			$group_parent_id = $product->get_attribute( 'group_id' );
			$parent_sku      = get_post_meta( $group_parent_id, '_sku', true );
			update_post_meta( $post_id, '_sku', $parent_sku );
			
		}
	}
}


/**
 * Disable SKU field if shown
 *
 * @return void
 */
function remove_sku() {
	$post_id = get_the_ID();
	if ( 'product' === get_post_type( $post_id ) ) {
		$children = get_post_meta( $post_id, '_children', true );
		$product  = wc_get_product( get_the_ID() );
		if ( 'bundle' === $product->get_type() || empty( $children ) ) {
			return;
		}
		if ( '' === $children[0] ) {
			//echo "<script>jQuery( document ).ready(function() {jQuery('#_sku').attr('placeholder',jQuery('#_sku').val());jQuery('#_sku').val('');jQuery('#_sku').attr('readonly','readonly');});</script>";
		}
	}
}
add_action( 'admin_head', 'remove_sku' );

// 25. Checkout actions
/**
 * Default woocommerce action "woocommerce_checkout_order_review" includes two functions:
 *  1. woocommerce_order_review
 *  2. woocommerce_checkout_payment
 * Because we need those two functions on separate columns, we are adding two new actions, addin one funtion to one of those actions, and the other function, to the other action:
 */

// add_action( 'woocommerce_checkout_order_review_review_only', 'woocommerce_order_review', 10 );
// add_action( 'woocommerce_checkout_order_review_checkout_payment_only', 'woocommerce_checkout_payment', 20 );
// We are calling these actions on x/child/woocommerce/checkout/form-checkout.php.
// We don't want form login, because we are using Oauth.
// remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10 );
// We don't want the coupon on the checkout page.
// remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );.

// 26. Thank You Page.
add_filter( 'the_title', 'woo_title_order_received', 10, 2 );

/**
 * Filter the title for the thank you page
 *
 * @param string $title   Title.
 * @param int    $id      Orde id.
 * @return string $title  Title filtered.
 */
function woo_title_order_received( $title, $id ) {
	if ( function_exists( 'is_order_received_page' ) &&
		is_order_received_page() && get_the_ID() === $id ) {
		$title = 'Thank you!';
	}
	return $title;
}

add_filter( 'woocommerce_product_add_to_cart_text', 'sm_woocommerce_product_add_to_cart_text' );

/**
 * Description
 *
 * @return string  $text  The add to cart text.
 */
function sm_woocommerce_product_add_to_cart_text() {
	global $product;

	$product_type = $product->get_type();

	$id      = $product->get_id();
	$product = wc_get_product( $id );

	switch ( $product_type ) {
		case 'external':
			$text = __( 'Buy product', 'woocommerce' );
			break;
		case 'grouped':
			$text = __( 'View Product', 'woocommerce' );
			break;
		case 'simple':
		case 'subscription':
			$text = __( 'Add to cart', 'woocommerce' );
			break;
		case 'variable':
			$text = __( 'Select options', 'woocommerce' );
			break;
		default:
			$text = __( 'Read more', 'woocommerce' );
	}
	return $text;
}

/**
 * 28. Website Optimization
 */

// Do not load Emoji detection  JS.
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'embed_head', 'print_emoji_detection_script' );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );

/**
 * Custom Scripting to Move JavaScript from the Head to the Footer
 */
function remove_head_scripts() {
	if ( is_admin() ) {
		return;
	}
	remove_action( 'wp_head', 'wp_print_scripts' );
	remove_action( 'wp_head', 'wp_enqueue_scripts', 1 );
	remove_action( 'wp_head', 'wp_print_head_scripts', 9 );

	add_action( 'wp_footer', 'wp_print_scripts' );
	add_action( 'wp_footer', 'wp_enqueue_scripts', 1 );
	add_action( 'wp_footer', 'wp_print_head_scripts', 9 );

	// Truspot js.
	remove_action( 'wp_head', 'trustspot_wp_head' );
	add_action( 'wp_footer', 'trustspot_wp_head' );

	// x_after_site_end.
	remove_action( 'x_after_site_end', 'x_scroll_top_anchor' );
	add_action( 'wp_footer', 'x_scroll_top_anchor', 999 );

	// Google Analytics +.
	global $google_analytics_async;
	remove_action( 'wp_head', array( $google_analytics_async, 'tracking_code_output' ) );
	if ( strpos( get_site_url(), 'www.youveda.com' ) !== false ) {
		add_action( 'wp_footer', array( $google_analytics_async, 'tracking_code_output' ) );
	}

	// Disable Ajax Call from WooCommerce in home page.
	if ( is_front_page() ) {
		wp_dequeue_script( 'wc-cart-fragments' );
	}

	// Do not load Truspot assets outside products.
	if ( ! is_product() && ! is_shop() ) {
		wp_dequeue_style( 'trustspot_widget_css' );
		wp_dequeue_style( 'trustspot_font_css' );
		wp_dequeue_script( 'trustspot_reviews_js' );
	}
}

add_action( 'wp_enqueue_scripts', 'remove_head_scripts', 999 );

/**
 * Function to defer or asynchronously load scripts
 *
 * @param string $tag  HTML tag.
 * @return string
 */
function js_async_attr( $tag ) {
	if ( is_admin() || is_account_page() || is_checkout() ) {
		return $tag;
	}
	// Do not add defer or async attribute to these scripts.
	$scripts_to_exclude = array(
		'jquery.js',
	);

	foreach ( $scripts_to_exclude as $exclude_script ) {
		if ( false !== strpos( $tag, $exclude_script ) ) {
			return $tag;
		}
	}
	// Defer or async all remaining scripts not excluded above.
	return str_replace( ' src', ' defer src', $tag );
}
//add_filter( 'script_loader_tag', 'js_async_attr', 10 );


// do not load cornerstone Google fonts since they are already loaded manually.
add_filter( 'cs_load_google_fonts', '__return_false' );
// Gravity Forms js to footer.
add_filter( 'gform_init_scripts_footer', '__return_true' );

/**
 * Wrap gform cdataopen
 *
 * @param string $content  The form HTML output.
 * @return string $content
 */
function wrap_gform_cdata_open( $content = '' ) {
	$content = 'document.addEventListener( "DOMContentLoaded", function() { ';
	return $content;
}


/**
 * Wrap gform cdata close
 *
 * @param string $content  The form HTML output.
 * @return string $content
 */
function wrap_gform_cdata_close( $content = '' ) {
	$content = ' }, false );';
	return $content;
}

// solution to move remaining JS from https://bjornjohansen.no/load-gravity-forms-js-in-footer.
add_filter( 'gform_cdata_open', 'wrap_gform_cdata_open' );
add_filter( 'gform_cdata_close', 'wrap_gform_cdata_close' );

/**
 * 29. Cart / Checkout customization
 * =============================================================================
 */

/**
 * Do not display the recurring totals details on the cart page
 */
remove_action( 'woocommerce_cart_totals_after_order_total', 'WC_Subscriptions_Cart::display_recurring_totals' );

if ( ! function_exists( 'yv_cart_item_price' ) ) {
	/**
	 * Show sale price and regular on cart items table
	 *
	 * @param string $price         html price.
	 * @param array  $cart_item     The cart item.
	 * @param string $cart_item_key Cart item key.
	 * @return string               The regular price or the sale price if exist.
	 */
	function yv_cart_item_price( $price, $cart_item, $cart_item_key ) {
		$product = wc_get_product( $cart_item['product_id'] );
		return ! empty( $product->get_sale_price() ) ? $product->get_price_html() : $price;
	}
}
add_filter( 'woocommerce_cart_item_price', 'yv_cart_item_price', 10, 3 );

if ( ! function_exists( 'tooltip' ) ) {
	/**
	 * Function to output Shipping information Tooltip on cart and checkout.
	 *
	 * @return void
	 */
	function tooltip() {
		if ( 'woocommerce_after_shipping_rate' === current_filter() && is_checkout() ) {
			return;
		}

		$css_class = 'ic_tooltip';
		if ( is_cart() ) {
			$css_class .= ' right';
		}
		if ( empty( WC()->customer->get_shipping_country() ) || 'US' !== WC()->customer->get_shipping_country() ) {

			$tooltip = "<div class='" . $css_class . "'><p class='ic_tooltip_btn'><i class='fa ic_warning_icon'>&#xf071;</i>For international customers</p><div class='ic_tooltip_txt visible' style='background: #ffffff;color: #333333;line-height: 2;font-size: 0.8em;'><p><strong>Due to government regulations in different countries, international customers are subject to import duties and taxes.</strong></p><p>YouVeda will not be subject to refund or assume liability for any import duties and taxes on any products shipped outside of the US. We believe in full transparency with our customers and would like to share that import duties and taxes can be up to 20% of your order purchase.</p>
<p>YouVeda takes pride in the quality of our products. However, we cannot offer a replacement or refund should your international order be in any way lost, delayed, or damaged.</p></div></div>";
			echo $tooltip;
		}
	}
}

// add_action( 'woocommerce_after_shipping_rate', 'tooltip', 10 );
if ( function_exists( 'wp_is_mobile' ) && wp_is_mobile() ) {
	// add_action( 'woocommerce_checkout_order_review_extended', 'tooltip', 20 );
} else {
	add_action( 'woocommerce_checkout_order_review_extended', 'tooltip', 20 );
}

/**
 * 30. // Add code snipets to the <head></head>
 * =============================================================================
 */

// add_action( 'wp_head', 'head_information', 9999 ); Removed due to global tag manager in framework/legacy/cranium/headers/views/global/_header.php
/**
 * Add GTAG code
 *
 * @return void
 */
function head_information() {
	if ( strpos( get_site_url(), 'www.youveda.com' ) !== false ) {
		?>
			<script async src="https://www.googletagmanager.com/gtag/js?id=AW-719971251"></script> <script> window.dataLayer = window.dataLayer || []; function gtag(){dataLayer.push(arguments);} gtag('js', new Date()); gtag('config', 'AW-719971251'); </script>
		<?php
		$c_url = $_SERVER['REQUEST_URI'];
		if ($c_url == '/subscribe/confirm') {
			echo "<script>
						gtag('event', 'conversion', {'send_to': 'AW-719971251/8oWeCNS8gKcBELPHp9cC'});
					</script>";
		}
	}
	
	 global $post;
	 
	 

    if( is_page() || is_single() )
    {
        switch($post->post_name) // post_name is the post slug which is more consistent for matching to here
        {
            case 'checkout':
                echo "<script>
					  gtag('event', 'conversion', {
						  'send_to': 'AW-719971251/Q6DeCMHLjKcBELPHp9cC',
						  'value': 90.0,
						  'currency': 'USD',
						  'transaction_id': ''
					  });
					</script>";
                break;
           
            case 'account-confirmation':
               echo "<script>
						gtag('event', 'conversion', {'send_to': 'AW-719971251/FQDLCIvBgKcBELPHp9cC'});
					</script>";
                break;
        }
		
		
    } 
}

/**
 * 31. // Multiple Subscriptions options on product page + add to cart update
 * =============================================================================
 */

if ( ! function_exists( 'yv_woocommerce_grouped_product_columns' ) ) {
	/**
	 * Filter columns for add to cart template
	 *
	 * @param array $columns   The WC columns.
	 * @return array           The column values to show.
	 */
	function yv_woocommerce_grouped_product_columns( $columns ) {
		return array( 'price', 'quantity' );
	}
}
add_action( 'woocommerce_grouped_product_columns', 'yv_woocommerce_grouped_product_columns', 10, 1 );

if ( ! function_exists( 'yv_after_add_to_cart_quantity' ) ) {
	/**
	 * Adds support for "add to cart" variable subscription on product page.
	 *
	 * @return void
	 */
	function yv_after_add_to_cart_quantity() {
		global $product;
		if ( 'variable-subscription' === $product->get_type() ) {
			?>
			<!--<input type="hidden" name="add-to-cart" value="<?php echo absint( $product->get_id() ); ?>" />
			<input type="hidden" name="product_id" value="<?php echo absint( $product->get_id() ); ?>" />-->
			<input type="hidden" name="variation_id" class="variation_id" value="0" />
			<span class="subscription-details">Delivers every </span>
			<?php
			$attributes          = $product->get_variation_attributes();
			$selected_attributes = $product->get_default_attributes();
			$attribute_keys      = array_keys( $attributes );

			foreach ( $attributes as $attribute_name => $options ) {
				//echo '<div class="position-relative floatLeft width-75">';
				wc_dropdown_variation_attribute_options(
					array(
						'class'     => 'yv_custom_attribute_select',
						'options'   => $options,
						'attribute' => $attribute_name,
						'product'   => $product,
						'selected'	=> 'Choose an option'
					)
				);
				//echo '</div>';
				$cat = yv_get_primary_taxonomy_term( $product->get_id() );
				if( $cat['slug'] == 'essential-oils' ) {
					echo '<p class="subscription-details">Cancel Anytime</p>';
				} else {
					echo '<p class="subscription-details">Cancel Anytime • Free Shipping within the U.S</p>';
				}
				echo '<p class="subscription-details">90 Day Money Back Guarantee</p>';
			}
		} else {
			echo '<span class="subscription-details">Deliver one time only</span>';
			$cat = yv_get_primary_taxonomy_term( $product->get_id() );
			if( $cat['slug'] != 'essential-oils' ) {
				echo '<p class="subscription-details">Free Shipping within the U.S</p>';
			}
			echo '<p class="subscription-details">90 Day Money Back Guarantee</p>';
		}
		echo '<div class="clear></div>"';
	}
}

add_action( 'woocommerce_after_add_to_cart_quantity', 'yv_after_add_to_cart_quantity', 10, 1 );

if ( ! function_exists( 'yv_get_subscription_price_html' ) ) {
	/**
	 * Filter the price string for subscriptions on product page
	 *
	 * @param string     $subscription_string    Price string.
	 * @param WC_Product $product                instance of WC_Product.
	 * @return string    $subscription_string    Replaced | original string.
	 */
	function yv_get_subscription_price_html( $subscription_string, $product ) {
		if ( 'variable-subscription' === $product->get_type() || 'subscription' === $product->get_type() || 'subscription_variation' === $product->get_type() ) {
			if ( is_product() ) {
				$subscription_string = str_replace( 'for 1 month', '- Deliver one time only', $subscription_string);
				$subscription_string = str_replace( 'From', '', $subscription_string);

				$pattern             = '/every (\d)? (day+s?|month+s?)/';
				$replace             = '- Subscribe and save 33%<br><span class="cancel-anytime subs-extra-description">Cancel anytime!</span>';
				$subscription_string = preg_replace( $pattern, $replace, $subscription_string );
				$subscription_string = str_replace(': ', '', $subscription_string);

				return '';

			} elseif ( is_shop() || is_product_category() || is_cart() || is_checkout() || is_wc_endpoint_url('order-received')) {

				$subscription_string = str_replace( 'for 1 month', 'One time purchase', $subscription_string );
				$subscription_string = str_replace( 'From', '', $subscription_string );
				$pattern             = '/every(\s)?(\d)?(\s)?(day+s?|month+s?)/';

				$cat = yv_get_primary_taxonomy_term( $product->parent_id );
				if( $cat['slug'] == 'essential-oils' ) {
					$replace             = is_checkout() ? '' : 'Auto-Delivery';
				} else {
					$replace             = is_checkout() ? '' : 'Subscribe and save 33%';
				}

				// 1 has expiration | 0 never expire.
				if ( 0 === absint( WC_Subscriptions_Product::get_length( $product ) ) ) {
					$subscription_string = str_replace( '/ month', 'every month', $subscription_string );
				}

				if ( is_account_page() || is_cart() || is_checkout() || is_wc_endpoint_url('order-received') ) {
				
					$subscription_string = str_replace( '/ days', 'Delivers every month', $subscription_string );
					$subscription_string = str_replace( 'every 2 days', 'Delivers every 60 days', $subscription_string );
					$subscription_string = str_replace( 'every 3 days', 'Delivers every 90 days', $subscription_string );
				
					preg_match( $pattern, $subscription_string, $matches );
					if ( ! empty( $matches ) ) {
						if ( ! is_checkout() ) {
							$replace .= ' | ' ;
						}
						$replace .= 'Delivers ' . $matches[0];
					}
				}
				$subscription_string = preg_replace( $pattern, $replace, $subscription_string );
				$subscription_string = str_replace('Delivers Subscribe', 'Subscribe', $subscription_string);
				$subscription_string = str_replace('Delivers Auto', 'Auto', $subscription_string);
				$subscription_string = str_replace('Delivers Delivers', 'Delivers', $subscription_string);

				// load the DOM parser lib.
				require_once get_stylesheet_directory() . '/inc/simple_html_dom.php';
				$html = new simple_html_dom();
				$html->load( $subscription_string );
				$spans = $html->childNodes();
				// Re order price string "from" "price" and "subscription details".
				$price_reordered = array();
				foreach ( $spans as $span ) {

					if ( 'from' === $span->class ) {
						continue;
					}
					if ( is_wc_endpoint_url('order-received') ) {
						if ( 'woocommerce-Price-amount amount' === $span->class || 'del' === $span->tag || 'ins' === $span->tag ) {
							continue;
						}
					}
					$price_reordered[] = $span->outertext;
				}

				$subscription_string = implode( ' - ', array_merge( array_slice( $price_reordered, -1, 1 ), array_slice( $price_reordered, 0, -1 ) ) );

			}
		}
		return $subscription_string;
	}
}

add_filter( 'woocommerce_subscriptions_product_price_string', 'yv_get_subscription_price_html', 99, 2 );

if ( ! function_exists( 'yv_dropdown_variation_replace_value_with_variation_id' ) ) {
	/**
	 * Filter the variation dropdown html so we can include custom data for the add to cart functionality on product page
	 *
	 * @param string  $html  The dropdown HTML.
	 * @param array   $args  Arguments.
	 * @return string  $html  The dropdown HTML within the extra needed data.
	 */
	function yv_dropdown_variation_replace_value_with_variation_id( $html, $args ) {
		global $product;
		if ( is_product( 'grouped' ) && 'variable-subscription' === $product->get_type() ) {

			$attributes_in_variations = $product->get_variation_attributes();
			$available_variations     = $product->get_available_variations();

			$variations_by_attr = array();

			foreach ( $available_variations as $variation ) {
				if ( ! empty( $variation['attributes'] ) && isset( $variation['attributes']['attribute_pa_subscription-period'] ) && ! empty( $variation['attributes']['attribute_pa_subscription-period'] ) ) {
					if ( in_array( $variation['attributes']['attribute_pa_subscription-period'], $attributes_in_variations['pa_subscription-period'], true ) ) {
						$variations_by_attr[ $variation['attributes']['attribute_pa_subscription-period'] ] = $variation['variation_id'];
						$input_val      = 'value="' . $variation['attributes']['attribute_pa_subscription-period'] . '"';
						$replace        = $input_val . ' data-variation-id="' . $variation['variation_id'] . '"';
						$attr_names[]   = $input_val;
						$variation_id[] = $replace;
					}
				}
			}
			$html = str_replace( $attr_names, $variation_id, $html );
		}
		return $html;
	}
}
add_filter( 'woocommerce_dropdown_variation_attribute_options_html', 'yv_dropdown_variation_replace_value_with_variation_id', 99, 2 );


if ( ! function_exists( 'yv_filter_coupon_redirect_query_args' ) ) {
	/**
	 * Add custom query var after coupon redirect
	 *
	 * @param array     $query_vars    Allowed Query vars.
	 * @param WC_Coupon $wc_coupon     WC coupon.
	 * @return array     $query_vars   Appended query vars to show a specific header on the shop page.
	 */
	function yv_filter_coupon_redirect_query_args( $query_vars, $wc_coupon ) {

		// Add coupon referer.
		$query_vars['cr'] = strtolower( get_the_title( $wc_coupon->get_id() ) );
		return $query_vars;
	}
}

add_filter( 'wc_url_coupons_redirect_query_args', 'yv_filter_coupon_redirect_query_args', 10, 3 );

if ( ! function_exists( 'yv_woocommerce_cart_coupon_add_disclaimer_text' ) ) {
	/**
	 * Function to add the discalimer text when a coupon was applied to the cart
	 *
	 * @return void || str
	 */
	function yv_woocommerce_cart_coupon_add_disclaimer_text() {
		if ( yv_cart_has_recurring_subscription_product() ) {
			echo '<div class="disclaimer_coupon right">';
			echo 'Coupons used on Subscribe and Save will be credited the difference ';
			echo 'between the coupon and discounted subscription price. ';
			echo 'Coupons do not apply to the Bundle Kits.';
			echo '</div>';
		}
	}
}

add_action( 'yv_woocommerce_cart_coupon_disclaimer', 'yv_woocommerce_cart_coupon_add_disclaimer_text', 10 );

if ( ! function_exists( 'yv_cart_has_recurring_subscription_product' ) ) {
	/**
	 * Helper function to detect if recurring subscriptions are part of the cart contents
	 *
	 * @return bool true|false Cart has recurring subscription product?
	 */
	function yv_cart_has_recurring_subscription_product() {
		$cart_items = WC()->cart->get_cart();
		foreach ( $cart_items as $item ) {
			$product      = wc_get_product( $item['product_id'] );
			$product_type = $product->get_type();
			if ( 'variable-subscription' === $product_type || 'bundle' === $product_type ) {
				return true;
			}
		}
		return false;
	}
}

if ( ! function_exists( 'pretty_debug' ) ) {
	/**
	 * Custom debug function
	 *
	 * @param arr     $arr  Input array.
	 * @param boolean $die  Stop execution control.
	 * @return void
	 */
	function pretty_debug( $arr, $die = false ) {
		echo '<pre>';
		$out = print_r( $arr, true );
		echo htmlentities( $out );
		echo '</pre>';
		if ( $die ) {
			die();
		}
	}
}

if ( ! function_exists( 'my_wc_filter_dropdown_args' ) ) {
	/**
	 * 32. Change default "Choose an option" text for select input - Paymo task : 14503660
	 *
	 * @param array $args   Args for the WC dropdown.
	 * @return array $args  New args.
	 */
	function my_wc_filter_dropdown_args( $args ) {
		if ( ! empty( $args['show_option_none'] ) && '-' !== $args['show_option_none'] ) {
			$args['show_option_none'] = 'Frequency';
		}
		return $args;
	}
}
add_filter( 'woocommerce_dropdown_variation_attribute_options_args', 'my_wc_filter_dropdown_args', 10 );

if ( ! function_exists( 'x_woocommerce_navbar_cart' ) ) :
	/**
	 * 33. remove 'items' word from the navbar cart icon.
	 *
	 * @return [type] [description]
	 */
	function x_woocommerce_navbar_cart() {

		$cart_info   = x_get_option( 'x_woocommerce_header_cart_info', 'outer-inner' );
		$cart_layout = x_get_option( 'x_woocommerce_header_cart_layout', 'inline' );
		$cart_style  = x_get_option( 'x_woocommerce_header_cart_style', 'square' );
		$cart_outer  = x_get_option( 'x_woocommerce_header_cart_content_outer', 'total' );
		$cart_inner  = x_get_option( 'x_woocommerce_header_cart_content_inner', 'count' );

		$data = array(
			'icon'  => '<i class="x-icon-shopping-cart" data-x-icon="&#xe918;"></i>',
			'total' => WC()->cart->get_cart_total(),
			'count' => sprintf( _n( '%d', '%d', WC()->cart->cart_contents_count, '__x__' ), WC()->cart->cart_contents_count ),
		);

		$modifiers = array(
			$cart_info,
			strpos( $cart_info, '-' ) === false ? 'inline' : $cart_layout,
			$cart_style,
		);

		$cart_output = '<div class="x-cart ' . implode( ' ', $modifiers ) . '">';

		foreach ( explode( '-', $cart_info ) as $info ) {
			$key          = ( 'outer' === $info ) ? $cart_outer : $cart_inner;
			$cart_output .= '<span class="' . $info . '">' . $data[ $key ] . '</span>';
		}

		$cart_output .= '</div>';

		return $cart_output;

	}
endif;

if ( ! function_exists( 'change_message' ) ) {
	/**
	 * 34. Graivty Forms -> Customize validation message for "Stay up to Date!" form only
	 *
	 * @param  string $message Error message for form validation.
	 * @param  array  $form    GF form object.
	 * @return string          The custom message
	 */
	function change_message( $message, $form ) {
		return "<div class='validation_error'>Please enter a valid email address.</div>";
	}
}
add_filter( 'gform_validation_message_11', 'change_message', 10, 2 );

if ( ! function_exists( 'hide_wc_refund_button' ) ) {
	/**
	 * 35.Modify the Woo-only-read user role so they cannot make refunds.
	 *
	 * @return void
	 */
	function hide_wc_refund_button() {
		global $post;

		$user = wp_get_current_user();
		// if the user has the "author" role.
		if ( in_array( 'woo-only-ready', (array) $user->roles, true ) ) {
			?>
			<script>
				jQuery(function () {
				jQuery('.refund-items').hide();
				jQuery(document).ajaxComplete(function() {
					jQuery('.refund-items').css('display','none');});
				});
				jQuery('.order_actions option[value=send_email_customer_refunded_order]').remove();
					if (jQuery('#original_post_status').val() === 'wc-refunded') {
						jQuery('#s2id_order_status').html('Refunded');
					} else {
						jQuery('#order_status option[value=wc-refunded]').remove();
					}            
			</script>
			<?php
		}
	}
}
add_action( 'admin_head', 'hide_wc_refund_button' );

if ( ! function_exists( 'yv_x_woocommerce_template_loop_product_title' ) ) {
	/**
	 * Output the product title
	 *
	 * @return void
	 */
	function yv_x_woocommerce_template_loop_product_title() {
		echo '<h3 class="screen-reader-text"><a href="' . get_the_permalink() . '">' . get_the_title() . '</a></h3>';
	}
}
add_action( 'woocommerce_shop_loop_item_title', 'yv_x_woocommerce_template_loop_product_title', 10 );

remove_action( 'woocommerce_before_shop_loop_item_title', 'trustspot_inline_ratings', 15 );
add_action( 'woocommerce_after_shop_loop_item_title', 'trustspot_inline_ratings', 10 );

remove_action( 'woocommerce_archive_description', 'woocommerce_product_archive_description', 10 );

add_action( 'woocommerce_after_shop_loop_item_title', 'price_flex_container_open', 6 );

if ( ! function_exists( 'price_flex_container_open' ) ) {
	/**
	 * Open flex container on shop listing price row
	 *
	 * @return void
	 */
	function price_flex_container_open() {
		echo '<div class="price-row">';
	}
}

if ( ! function_exists( 'price_flex_container_close' ) ) {
	/**
	 * Close flex container on shop listing price row
	 *
	 * @return void
	 */
	function price_flex_container_close() {
		echo '</div>';
	}
}
add_action( 'woocommerce_after_shop_loop_item_title', 'price_flex_container_close', 10 );

if ( ! function_exists( 'yv_woocommerce_loop_add_to_cart_link' ) ) {
	/**
	 * Custom rewrite for add to cart button on loop » shop and category pages
	 *
	 * @param string     $add_to_cart_btn  Add to cart button HTML.
	 * @param WC_Product $product          Instance of WC_Product.
	 * @param array      $args             Add to cart args.
	 * @return string    $add_to_cart_btn  Custom HTML for add to cart button.
	 */
	function yv_woocommerce_loop_add_to_cart_link( $add_to_cart_btn, $product, $args ) {
		if ( is_shop() && 'grouped' === $product->get_type() ) {
			yv_loop_grouped_add_to_cart();
			$button_text = "Learn More";
			$add_to_cart_btn = '<a class="button" href="' . $product->get_permalink() . '">' . $button_text . '</a>';
		}
		return $add_to_cart_btn;
	}
}
add_filter( 'woocommerce_loop_add_to_cart_link', 'yv_woocommerce_loop_add_to_cart_link', 99, 3 );

//add_filter( 'woocommerce_loop_add_to_cart_link', 'yv_add_cart_link_atts', 10, 2 );

if ( ! function_exists( 'yv_loop_grouped_add_to_cart' ) ) {
	/**
	 *  Helper function to output the add to cart grouped button on the shop and category pages
	 *
	 * @return void
	 */
	function yv_loop_grouped_add_to_cart() {
		global $product;
		$products = array_filter( array_map( 'wc_get_product', $product->get_children() ), 'wc_products_array_filter_visible_grouped' );

		if ( $products ) {
			wc_get_template(
				'loop/add-to-cart-grouped.php',
				array(
					'grouped_product'    => $product,
					'grouped_products'   => $products,
					'quantites_required' => false,
				)
			);
		}
	}
}


if ( ! function_exists( 'get_variation_data_for_subscriptions' ) ) {
	/**
	 * Get variation data for a variable product having a given attribute
	 *
	 * @param mixed  $product         Post object or post ID of the product.
	 * @param string $attribute_slug  The attribute slug to search.
	 * @return array                  Matches » Array with product ID as key and attributes and price as values.
	 */
	function get_variation_data_for_subscriptions( $product = false, $attribute_slug ) {
		global $post;
		if ( false === $product && isset( $post, $post->ID ) && 'product' === get_post_type( $post->ID ) ) {
			$prod_id = get_the_ID();
		} elseif ( is_numeric( $product ) ) {
			$prod_id = $product;
		} elseif ( $product instanceof WC_Product ) {
			$prod_id = $product->get_id();
		} elseif ( ! empty( $product->ID ) ) {
			$prod_id = $product->ID;
		} else {
			return array();
		}

		$product = wc_get_product( $prod_id );

		$available_variations = $product->get_available_variations();
		$get_variations       = wp_list_pluck( $available_variations, 'attributes', 'variation_id' );
		$prices               = wp_list_pluck( $available_variations, 'display_price', 'variation_id' );

		foreach ( $get_variations as $variation_id => $attribute ) {
			if ( array_key_exists( $variation_id, $prices ) ) {
				$get_variations[ $variation_id ]['display_price'] = $prices[ $variation_id ];
			}
		}
		$callback = 'hp_get_variation_data_for_every_month_subscription';
		$result   = array_filter( $get_variations, $callback, ARRAY_FILTER_USE_BOTH );
		return empty( $result ) ? false : $result;
	}
}

if ( ! function_exists( 'hp_get_variation_data_for_every_month_subscription' ) ) {
	/**
	 * Helper function used in get_variation_prices_for_subscriptions
	 *
	 * @param array $attributes    Array of product attribute names.
	 * @param int   $k             Key of the array.
	 * @return bool
	 */
	function hp_get_variation_data_for_every_month_subscription( $attributes, $k ) {
		return 'every-30-days' === $attributes['attribute_pa_subscription-period'];
	}
}

if ( ! function_exists( 'yv_filter_add_to_cart_url' ) ) {
	/**
	 * Filter the add to cart URL if grouped product
	 * We are using add to cart ajax with custom code
	 *
	 * @param string     $url      Add to cart URL, ajax URL or full url.
	 * @param WC_Product $product  Instance of WC_Product.
	 * @return string     $url     The new URL.
	 */
	function yv_filter_add_to_cart_url( $url, $product ) {
		if ( 'grouped' !== $product->get_type() && ( ! is_shop() || ! is_product_category() ) ) {
			return $url;
		}
		$variation_30_days = return_variation_from_grouped_product( $product, 'attribute_pa_subscription-period', 'every-30-days' );
		if ( $variation_30_days ) {
			$url = $product->is_purchasable() && $product->is_in_stock() ? remove_query_arg( 'added-to-cart', add_query_arg( 'add-to-cart', $variation_30_days->get_id() ) ) : get_permalink( $product->get_id() );
		}
		return $url;
	}
}
add_filter( 'woocommerce_product_add_to_cart_url', 'yv_filter_add_to_cart_url', 99, 2 );

if ( ! function_exists( 'yv_grouped_product_is_purchasable' ) ) {
	/**
	 * Helper function to allow grouped products to link to the add to cart instead of the product page on shop and category pages
	 *
	 * @param bool       $is_purchasable   Flag for purchasable control.
	 * @param WC_Product $product          Instance of WC_Product.
	 * @return bool      $is_purchasable   Flag for purchasable control.
	 */
	function yv_grouped_product_is_purchasable( $is_purchasable, $product ) {
		if ( 'grouped' === $product->get_type() && ( is_shop() || is_product_category() ) ) {
			$is_purchasable = true;
		}
		return $is_purchasable;
	}
}
add_filter( 'woocommerce_is_purchasable', 'yv_grouped_product_is_purchasable', 10, 2 );


if ( ! function_exists( 'yv_add_to_cart_grouped_text' ) ) {
	/**
	 * Update add to cart button on shop and category pages
	 *
	 * @param string     $text    Add to cart button text.
	 * @param WC_Product $product Instance of WC_Product.
	 * @return string    $text    The new button value.
	 */
	function yv_add_to_cart_grouped_text( $text, $product ) {
		if ( 'grouped' === $product->get_type() && ( is_shop() || is_product_category() ) ) {
			$text = $product->is_purchasable() && $product->is_in_stock() ? __( 'Learn more', 'woocommerce' ) : __( 'Read more', 'woocommerce' );
		}
		return $text;
	}
}
add_filter( 'woocommerce_product_add_to_cart_text', 'yv_add_to_cart_grouped_text', 10, 2 );

if ( ! function_exists( 'yv_grouped_product_add_to_cart_ajax_support' ) ) {
	/**
	 * Filter the built in WooCommerce product supports check so we can add grouped products via ajax
	 *
	 * @param bool       $supports Product supports.
	 * @param string     $feature  Feature.
	 * @param WC_Product $product  Current product.
	 * @return bool
	 */
	function yv_grouped_product_add_to_cart_ajax_support( $supports, $feature, $product ) {
		if ( 'ajax_add_to_cart' !== $feature ) {
			return $supports;
		}
		if ( 'grouped' === $product->get_type() && ( is_shop() || is_product_category() ) ) {
			$supports = $product->is_purchasable() && $product->is_in_stock();
		}
		return $supports;
	}
}
add_filter( 'woocommerce_product_supports', 'yv_grouped_product_add_to_cart_ajax_support', 10, 3 );


if ( ! function_exists( 'filter_add_to_cart_button_args' ) ) {
	/**
	 * Filter the args for the add to cart button
	 * Removing product_id to prevent ajax execution
	 *
	 * @param  array      $args    Args passed to the button.
	 * @param  WC_Product $product Current Product.
	 * @return array      $args    New Args.
	 */
	function filter_add_to_cart_button_args( $args, $product ) {
		if ( 'grouped' === $product->get_type() && ( is_shop() || is_product_category() ) ) {

			// get the variation having a renewal period of 30 days.
			$variation_30_days = return_variation_from_grouped_product( $product, 'attribute_pa_subscription-period', 'every-30-days' );
			if ( $variation_30_days ) {
				$args['class']                          = $args['class'] . ' product_type_simple';
				$args['attributes']['data-product_id']  = $variation_30_days->get_id();
				$args['attributes']['data-product_sku'] = $variation_30_days->get_sku();
				$args['attributes']['aria-label']       = $variation_30_days->add_to_cart_description();
			}
		}
		return $args;
	}
}
add_filter( 'woocommerce_loop_add_to_cart_args', 'filter_add_to_cart_button_args', 10, 2 );

if ( ! function_exists( 'return_variation_from_grouped_product' ) ) {
	/**
	 * Helper function to return the variation from a grouped product given a particular attribute
	 *
	 * @param WC_Product $product          Instance of WC product.
	 * @param string     $attribute_slug   Attribute slug.
	 * @param string     $attribute_value  Attribute value.
	 * @return WC_Product | bool
	 */
	function return_variation_from_grouped_product( $product, $attribute_slug, $attribute_value ) {
		if ( 'grouped' !== $product->get_type() ) {
			return false;
		}
		$found_product_id = false;
		$child_products   = array_filter( array_map( 'wc_get_product', $product->get_children() ), 'wc_products_array_filter_visible_grouped' );
		foreach ( $child_products as $product_child ) {
			if ( 'variable-subscription' === $product_child->get_type() ) {
				$get_variations = get_variation_data_for_subscriptions( $product_child, 'every-30-days' );
				if ( $get_variations ) {
					$keys             = array_keys( $get_variations );
					$found_product_id = reset( $keys );
					break;
				}
			}
		}

		return $found_product_id ? wc_get_product( $found_product_id ) : false;
	}
}


if ( ! function_exists( 'yv_get_shop_section' ) ) {
	/**
	 * Show a given section mostly used for the shop page
	 *
	 * @param  string $section_name Section template slug to load.
	 * @return void
	 */
	function yv_get_shop_section( $section_name ) {
		wc_get_template_part( 'content', $section_name );
	}
}

if ( ! function_exists( 'yv_woocommerce_template_loop_product_banner' ) ) {
	/**
	 * Show the banner on the shop loop items
	 *
	 * @see  yv_get_shop_section()
	 * @return void
	 */
	function yv_woocommerce_template_loop_product_banner() {
		yv_get_shop_section( 'product-banner' );
	}
}

add_action( 'yv_woocommerce_shop_loop_item_banner', 'yv_woocommerce_template_loop_product_banner', 10 );

if ( ! function_exists( 'yv_woocommerce_after_shop_loop' ) ) {
	/**
	 * Show the bundles section on the shop page
	 *
	 * @see  yv_get_shop_section()
	 * @return void
	 */
	function yv_woocommerce_after_shop_loop() {
		yv_get_shop_section( 'shop-bundles-section' );
		yv_get_shop_section( 'shop-oils-section' );
	}
}

if ( ! function_exists( 'shop_after_content' ) ) {
	/**
	 * Add custom content to the shop page after the content
	 *
	 * @return void
	 */
	function shop_after_content() {
		echo '</div>';

		echo '<div class="entry-content content">';
			echo do_shortcode( '[cs_gb id=2043]' );
		echo '</div>';
	}
}
add_action( 'woocommerce_after_shop_loop', 'yv_woocommerce_after_shop_loop' );


if ( ! function_exists( 'wcslider_add_section' ) ) {
	/**
	 * Create the section beneath the products tab
	 *
	 * @param array $sections WCSlider sections.
	 * @return array $sections
	 */
	function wcslider_add_section( $sections ) {
		$sections['wcslider'] = __( 'WC Slider', 'text-domain' );
		return $sections;
	}
}
add_filter( 'woocommerce_get_sections_products', 'wcslider_add_section' );


if ( ! function_exists( 'yv_custom_settings_for_bundle_display' ) ) {
	/**
	 * Define custom settings for bundles on HP for grouped product
	 *
	 * @return void
	 */
	function yv_custom_settings_for_bundle_display() {
		$bundles_settings_mb = new_cmb2_box(
			array(
				'id'           => '_yv_bundles_display_settings',
				'title'        => __( 'Bundles settings for the Shop', 'cmb2' ),
				// Post type.
				'object_types' => array( 'product' ),
				'context'      => 'normal',
				'priority'     => 'high',
				// Show field names on the left.
				'show_names'   => true,
				'show_on_cb'   => array(
					'Yv_Admin',
					'cmb_only_show_for_grouped_products',
				),
			)
		);

		$bundles_settings_mb->add_field(
			array(
				'name'         => 'Pack image',
				'desc'         => 'Upload an image or browser in media library',
				'id'           => 'yv_product_bundle_product_image',
				'type'         => 'file',
				// Optional.
				'options'      => array(
					// Hide the text input for the url.
					'url' => false,
				),
				'text'         => array(
					// Change upload button text. Default: "Add or Upload File".
					'add_upload_file_text' => 'Add File',
				),
				// Image size to use when previewing in the admin.
				'preview_size' => array( 100, 100 ),
			)
		);

		$bundles_settings_mb->add_field(
			array(
				'name'         => 'Button Icon',
				'desc'         => 'Upload an image or browser in media library',
				'id'           => 'yv_product_bundle_button_icon',
				'type'         => 'file',
				// Optional.
				'options'      => array(
					// Hide the text input for the url.
					'url' => false,
				),
				'text'         => array(
					// Change upload button text. Default: "Add or Upload File".
					'add_upload_file_text' => 'Add File',
				),
				// Image size to use when previewing in the admin.
				'preview_size' => array( 100, 100 ),
			)
		);

		$bundles_settings_mb->add_field(
			array(
				'name'         => 'Button Icon for mobile devices',
				'desc'         => 'Upload an image or browser in media library',
				'id'           => 'yv_product_bundle_button_icon_mobile',
				'type'         => 'file',
				// Optional.
				'options'      => array(
					// Hide the text input for the url.
					'url' => false,
				),
				'text'         => array(
					// Change upload button text. Default: "Add or Upload File".
					'add_upload_file_text' => 'Add File',
				),

				// Image size to use when previewing in the admin.
				'preview_size' => array( 100, 100 ),
			)
		);
	}
}
add_action( 'cmb2_admin_init', 'yv_custom_settings_for_bundle_display' );


if ( ! function_exists( 'yv_woocommerce_variation_option_name' ) ) {
	/**
	 * Filter options name for wc_dropdown_variation_attribute_options
	 *
	 * @param string $option_name     The term name | custom option name.
	 * @return string $option_name    Updated value for the shop.
	 */
	function yv_woocommerce_variation_option_name( $option_name ) {
		if ( is_shop() || is_product_category() ) {
			$pattern     = '/([a-z]*)-(\d*)-([a-z]*)/';
			$replace     = '${2}';
			$option_name = preg_replace( $pattern, $replace, $option_name );
		}
		return $option_name;
	}
}

add_filter( 'woocommerce_variation_option_name', 'yv_woocommerce_variation_option_name', 10, 1 );


if ( ! function_exists( 'yv_disable_gf_submission_anchor' ) ) {
	/**
	 * Avoid browser jump to anchor after GF form submission
	 *
	 * @param bool  $anchor  Scroll to anchor.
	 * @param array $form    GF form object.
	 * @return bool
	 */
	function yv_disable_gf_submission_anchor( $anchor, $form ) {
		if ( 'oils pre-sales' === strtolower( $form['title'] ) ) {
			$anchor = false;
		}
		return $anchor;
	}
}
add_filter( 'gform_confirmation_anchor', 'yv_disable_gf_submission_anchor', 10, 2 );

/**
 * Require custom helper functions
 */
require get_theme_file_path() . '/inc/functions-helpers.php';


/**
 * WooCommerce cart customization
 */
require get_theme_file_path() . '/inc/woocommerce/cart-functions.php';



/* 36. Change Navigation arrows
============================================================================= */

function pagenavi($before = '', $after = '') {

    global $wpdb, $wp_query;
    $pagenavi_options = array();
    $pagenavi_options['pages_text']                   = ('Page %CURRENT_PAGE% of %TOTAL_PAGES%');
    $pagenavi_options['current_text']                 = '%PAGE_NUMBER%';
    $pagenavi_options['page_text']                    = '%PAGE_NUMBER%';
    $pagenavi_options['first_text']                   = ('First Page');
    $pagenavi_options['last_text']                    = ('Last Page');
    $pagenavi_options['next_text']                    = '<i class="x-icon x-icon-angle-right" data-x-icon-s="" aria-hidden="true"></i>';
    $pagenavi_options['prev_text']                    = '<i class="x-icon x-icon-angle-left" data-x-icon-s="" aria-hidden="true"></i>';
    $pagenavi_options['dotright_text']                = '...';
    $pagenavi_options['dotleft_text']                 = '...';
    $pagenavi_options['num_pages']                    = 3;
    $pagenavi_options['always_show']                  = 0;
    $pagenavi_options['num_larger_page_numbers']      = 0;
    $pagenavi_options['larger_page_numbers_multiple'] = 3;

    if ( ! is_single() ) {
      $request        = $wp_query->request;
      $posts_per_page = intval( get_query_var( 'posts_per_page' ) );
      $paged          = intval( get_query_var( 'paged' ) );
      $numposts       = $wp_query->found_posts;
      $max_page       = $wp_query->max_num_pages;

      if( empty($paged) || $paged == 0 ) {
        $paged = 1;
      }

      $pages_to_show         = intval( $pagenavi_options['num_pages'] );
      $larger_page_to_show   = intval( $pagenavi_options['num_larger_page_numbers'] );
      $larger_page_multiple  = intval( $pagenavi_options['larger_page_numbers_multiple'] );
      $pages_to_show_minus_1 = $pages_to_show - 1;
      $half_page_start       = floor( $pages_to_show_minus_1 / 2 );
      $half_page_end         = ceil($pages_to_show_minus_1/2);
      $start_page            = $paged - $half_page_start;

      if( $start_page <= 0 ) {
        $start_page = 1;
      }

      $end_page = $paged + $half_page_end;

      if ( ( $end_page - $start_page ) != $pages_to_show_minus_1 ) {
        $end_page = $start_page + $pages_to_show_minus_1;
      }

      if ( $end_page > $max_page ) {
        $start_page = $max_page - $pages_to_show_minus_1;
        $end_page   = $max_page;
      }

      if ( $start_page <= 0 ) {
        $start_page = 1;
      }

      $larger_per_page         = $larger_page_to_show * $larger_page_multiple;
      $larger_start_page_start = ( x_round_nearest( $start_page, 10 ) + $larger_page_multiple ) - $larger_per_page;
      $larger_start_page_end   = x_round_nearest( $start_page, 10 ) + $larger_page_multiple;
      $larger_end_page_start   = x_round_nearest( $end_page, 10 ) + $larger_page_multiple;
      $larger_end_page_end     = x_round_nearest( $end_page, 10 ) + $larger_per_page;

      if ( $larger_start_page_end - $larger_page_multiple == $start_page ) {
        $larger_start_page_start = $larger_start_page_start - $larger_page_multiple;
        $larger_start_page_end   = $larger_start_page_end - $larger_page_multiple;
      }

      if ( $larger_start_page_start <= 0 ) {
        $larger_start_page_start = $larger_page_multiple;
      }

      if ( $larger_start_page_end > $max_page ) {
        $larger_start_page_end = $max_page;
      }

      if ( $larger_end_page_end > $max_page ) {
        $larger_end_page_end = $max_page;
      }

      if ( $max_page > 1 || intval( $pagenavi_options['always_show'] ) == 1 ) {
        $pages_text = str_replace( "%CURRENT_PAGE%", number_format_i18n( $paged ) , $pagenavi_options['pages_text'] );
        $pages_text = str_replace( "%TOTAL_PAGES%", number_format_i18n( $max_page ) , $pages_text );
        echo $before . '<div class="x-pagination"><ul class="center-list center-text">' . "\n";

        if ( ! empty( $pages_text ) ) {
          echo '<li><span class="pages">' . $pages_text . '</span></li>';
        }

        echo '<li>'; previous_posts_link( $pagenavi_options['prev_text'] ); echo '</li>';

        if ( $start_page >= 2 && $pages_to_show < $max_page ) {
          $first_page_text = str_replace( "%TOTAL_PAGES%", number_format_i18n( $max_page ), $pagenavi_options['first_text'] );
          echo '<li><a href="' . esc_url( get_pagenum_link() ) . '" class="first" title="' . $first_page_text . '">1</a></li>';
          if ( ! empty( $pagenavi_options['dotleft_text'] ) ) {
            echo '<li><span class="expand">' . $pagenavi_options['dotleft_text'] . '</span></li>';
          }
        }

        if ( $larger_page_to_show > 0 && $larger_start_page_start > 0 && $larger_start_page_end <= $max_page ) {
          for ( $i = $larger_start_page_start; $i < $larger_start_page_end; $i += $larger_page_multiple ) {
            $page_text = str_replace( "%PAGE_NUMBER%", number_format_i18n( $i ), $pagenavi_options['page_text'] );
            echo '<li><a href="' . esc_url( get_pagenum_link( $i ) ) . '" class="single_page" title="' . $page_text . '">' . $page_text . '</a></li>';
          }
        }

        for ( $i = $start_page; $i  <= $end_page; $i++ ) {
          if ( $i == $paged ) {
            $current_page_text = str_replace( "%PAGE_NUMBER%", number_format_i18n( $i ), $pagenavi_options['current_text'] );
            echo '<li><span class="current">' . $current_page_text . '</span></li>';
          } else {
            $page_text = str_replace( "%PAGE_NUMBER%", number_format_i18n( $i ), $pagenavi_options['page_text'] );
            echo '<li><a href="' . esc_url( get_pagenum_link( $i ) ) . '" class="single_page" title="' . $page_text . '">' . $page_text . '</a></li>';
          }
        }

        if ( $end_page < $max_page ) {
          if ( ! empty( $pagenavi_options['dotright_text'] ) ) {
            echo '<li><span class="expand">' . $pagenavi_options['dotright_text'] . '</span></li>';
          }
          $last_page_text = str_replace( "%TOTAL_PAGES%", number_format_i18n( $max_page ), $pagenavi_options['last_text'] );
          echo '<li><a href="' . esc_url( get_pagenum_link( $max_page ) ) . '" class="last" title="' . $last_page_text . '">' . $max_page . '</a></li>';
        }
        echo '<li>'; next_posts_link( $pagenavi_options['next_text'], $max_page ); echo '</li>';

        if ( $larger_page_to_show > 0 && $larger_end_page_start < $max_page ) {
          for ( $i = $larger_end_page_start; $i <= $larger_end_page_end; $i += $larger_page_multiple ) {
            $page_text = str_replace( "%PAGE_NUMBER%", number_format_i18n( $i ), $pagenavi_options['page_text'] );
            echo '<li><a href="' . esc_url( get_pagenum_link( $i ) ) . '" class="single_page" title="' . $page_text . '">' . $page_text . '</a></li>';
          }
        }
        echo '</ul></div>' . $after . "\n";
      }
    }

  }


	
/* 37. Custom text format for blog single post (bigger paragraph, capitulate letter)
============================================================================= */



function add_style_select_buttons( $buttons ) {
    array_unshift( $buttons, 'styleselect' );
    return $buttons;
}
// Register our callback to the appropriate filter
add_filter( 'mce_buttons_2', 'add_style_select_buttons' );

//add custom styles to the WordPress editor
function my_custom_styles( $init_array ) {  
 
    $style_formats = array(  
        // These are the custom styles 
        array(  
            'title' => 'Big Paragraph',  
            'block' => 'span',  
            'classes' => 'big-paragraph',
            'wrapper' => true,
        ),
        array(  
            'title' => 'Capitulate Letter',  
            'block' => 'span',  
            'classes' => 'capitulate-letter',
            'wrapper' => true,
        ),
        array(  
            'title' => 'Relate-Article Link',  
            'block' => 'span',  
            'classes' => 'related-article',
            'wrapper' => true,
        )
    );  
    // Insert the array, JSON ENCODED, into 'style_formats'
    $init_array['style_formats'] = json_encode( $style_formats );  
    
    return $init_array;  
  
} 
// Attach callback to 'tiny_mce_before_init' 
add_filter( 'tiny_mce_before_init', 'my_custom_styles' );



	
/* 38. Change comments input forms order, remove email input form, and add a custom placeholder for the "comments" input form.
=========================================================================================================================== */

function wpb_move_comment_field_to_bottom( $fields ) {
	// Change comments comment input field, to appear on the bottom
	$comment_field = $fields['comment'];
	unset( $fields['comment'] );
	$fields['comment'] = $comment_field;

	// Remove "website" input
	unset( $fields['url'] );
	return $fields;
}
add_filter( 'comment_form_fields', 'wpb_move_comment_field_to_bottom' );

/**
 * Comment Form Placeholder Comment Field
 */
 function placeholder_comment_form_field($fields) {
    $replace_comment = __('Your Comment', '__x__');
     
    $fields['comment_field'] = '<p class="comment-form-comment"><label for="comment">' . _x( 'Comment', 'noun' ) .
    '</label><textarea id="comment" name="comment" cols="45" rows="8" placeholder="'.$replace_comment.'" aria-required="true"></textarea></p>';
    
    return $fields;
 }
add_filter( 'comment_form_defaults', 'placeholder_comment_form_field' );




/* 39. Woocommerce -> Add custom css styles for the WooCommerce Emails
=================================================================== */

/**
 * @snippet       Add CSS to WooCommerce Emails
 * @how-to        Watch tutorial @ https://businessbloomer.com/?p=19055
 * @author        Rodolfo Melogli
 * @compatible    Woo 3.6.2
 * @donate $9     https://businessbloomer.com/bloomer-armada/
 */
 
add_filter( 'woocommerce_email_styles', 'bbloomer_add_css_to_emails', 9999, 2 );
 
function bbloomer_add_css_to_emails( $css) { 
	$css .= '
	#header_wrapper {
		background-color: #fff;
	}
	#header_wrapper h1 {
		color: #3C3C3B;
		font-family: Montserrat, arial, sans-serif;
		font-weight: 600;
		text-shadow: none;
		text-align: center;
	}
	#body_content td {
		padding-top: 10px !important;
	}
	#template_header_image p {
		margin-bottom: 0;
	}
	#template_header_image p img {
		margin-right: 0;
	}
	#template_footer td {
		padding-left: 0 !important;
		padding-right: 0 !important;
	}
	#credit {
		padding-bottom: 0 !important;
	}
	#template_container {
		box-shadow: none !important;
		border: none;
	}
	';
	return $css;
}

remove_action('woocommerce_after_add_to_cart_form', 'Javorszky\Toolbox\add_to_existing_sub_markup', 21);
add_action('woocommerce_before_add_to_cart_form', 'Javorszky\Toolbox\add_to_existing_sub_markup', 21);

function yv_add_grouped_products_to_subscription($types) {
	$types[] = 'grouped';
	return $types;
}
add_filter('jgtb_available_product_types_for_add_to_subscription', 'yv_add_grouped_products_to_subscription', 10, 1);

function yv_check_if_purchasable($product) {
	return true;
	if($product->exisst() && $product->get_status === 'publish') {
		return true;
	} else {
		return false;
	}
}
add_filter('woocommerce_is_purchasable', 'yv_check_if_purchasable', 10, 1);

function yv_add_addtocart_js_products() {
	global $product, $post;

	if ( ! is_product() ) {
		return;
	}

	$product = wc_get_product( $post->ID );

	$type = $product->get_type();

	if ( 'grouped' == $type) {
		wp_register_script( 'jgtbatss', get_stylesheet_directory_uri() . '/assets/js/jgtbatss.js', array( 'jquery', 'jquery-ui-datepicker', 'wc-add-to-cart' ) );
		wp_enqueue_script( 'jgtbatss' );
	}
}
add_action( 'wp_enqueue_scripts', 'yv_add_addtocart_js_products');

function yv_add_to_subscription_grouped()  {
	if (
		   isset( $_REQUEST['jgtb_add_to_existing_subscription'] )
		&& isset( $_REQUEST['jgtbwpnonce_' . $_REQUEST['jgtb_add_to_existing_subscription'] ] )
		&& isset( $_REQUEST['add-to-subscription'] )
		&& isset( $_REQUEST['ats_product_id'] )
		&& isset( $_REQUEST['ats_variation_id'] )
		&& isset( $_REQUEST['ats_quantity'] )
		&& isset( $_REQUEST['ats_variation_attributes'] )
	) {
		$user_id = get_current_user_id();
		$subscription = wcs_get_subscription( $_REQUEST['jgtb_add_to_existing_subscription'] );
		$nonce = $_REQUEST['jgtbwpnonce_' . $_REQUEST['jgtb_add_to_existing_subscription'] ];
		if ( validate_subscription_ownership( $user_id, $subscription ) && validate_add_to_subscription_request( $subscription, $nonce ) ) {

			$product_id  = $_REQUEST['ats_variation_id'] ? $_REQUEST['ats_variation_id'] : $_REQUEST['ats_product_id'];
			$attributes  = [];
			$item_to_add = sanitize_text_field( $product_id );

			// Find the item
			if ( ! is_numeric( $item_to_add ) ) {
				return;
			}

			$product = wc_get_product( $item_to_add );

			if ( ! $product || ( 'grouped' !== $product->get_type() ) ) {
				return;
			}

			$quantity = absint( $_REQUEST['ats_quantity'] );

			$subscription->add_product( $product, $quantity, array( 'variation' => $attributes ) );

			$subscription->calculate_totals();
			$subscription->add_order_note( 'Customer added a new line item to the subscription: ' . PHP_EOL . $product->get_name() . ' x ' . $quantity . ' (id: ' . $product_id . ')' );
			$subscription->save();

			wc_add_notice( 'The item has been added to subscription #' . $subscription->get_id() );

			do_action( 'jgtb_added_item_to_subscription', $subscription, $product, $quantity );
		}

		do_action( 'jgtb_adding_item_to_subscription_failed', $subscription );
	}
}
add_action( 'wp_loaded', 'yv_add_to_subscription_grouped' );

function validate_subscription_ownership( $user_id, $subscription ) {
	if ( ! wcs_is_subscription( $subscription ) ) {
		wc_add_notice( 'That subscription does not exist. Please contact us if you need assistance.', 'error' );
		return false;

	} elseif ( ! user_can( $user_id, 'edit_shop_subscription_status', $subscription->get_id() ) ) {
		wc_add_notice( 'That doesn\'t appear to be one of your subscriptions.', 'error' );
		return false;
	}

	return true;
}

function validate_add_to_subscription_request( $subscription, $nonce ) {
	$items_string = generate_nonce_on_items( $subscription );
	if ( ! empty( $nonce ) && wp_verify_nonce( $nonce, 'add_to_subscription_' . $items_string ) === false ) {
		wc_add_notice( 'Security error. Please contact us if you need assistance. E06.', 'error' );
		return false;
	}

	return true;
}

function generate_nonce_on_items( $subscription ) {
	$string = '';
	foreach ( $subscription->get_items() as $item ) {
		$string .= $item['product_id'] . '-' . $item['qty'] . '-' . $item['variation_id'];
	}
	return $string;
}

function yv_custom_subscription_periods( $periods, $number ) {
	unset($periods['day']);
	unset($periods['week']);
	unset($periods['year']);
	$periods['month'] = sprintf( _nx( 'days', '%s days', $number, 'Subscription billing period.', 'woocommerce-subscriptions' ), $number );
	return $periods;
}
add_filter('woocommerce_subscription_periods', 'yv_custom_subscription_periods', 10, 2);

function yv_custom_subscription_intervals( $intervals ) {
	$intervals = array();
	$intervals[1] = 'Every 30';
	$intervals[2] = 'Every 60';
	$intervals[3] = 'Every 90';
	return $intervals;
}
add_filter('woocommerce_subscription_period_interval_strings', 'yv_custom_subscription_intervals');

function yv_custom_formatted_order_total( $total ) {
	$total = str_replace('/ days', 'every 30 days', $total);
	$total = str_replace('every 2 days', 'every 60 days', $total);
	$total = str_replace('every 3 days', 'every 90 days', $total);
	return $total;
}
add_filter('woocommerce_get_formatted_subscription_total', 'yv_custom_formatted_order_total');

function yv_custom_formatted_order_subtotal( $total ) {
	$total = str_replace('/ days', 'every 30 days', $total);
	$total = str_replace('every 2 days', 'every 60 days', $total);
	$total = str_replace('every 3 days', 'every 90 days', $total);
	return $total;
}
add_filter('woocommerce_order_formatted_line_subtotal', 'yv_custom_formatted_order_subtotal');

function yv_add_link_to_edit_page( $actions, $subscription ) {
	$actions['edit-subscription'] = array(
		'url' => wc_get_endpoint_url( 'edit-subscription', $subscription->get_id(), wc_get_page_permalink( 'myaccount' ) ),
		'name' => 'Edit Shipping Frequency',
	);
	$actions['download-instructions'] = array(
		'url' => 'https://www.youveda.com/wp-content/uploads/2019/08/User-change-your-product-subscription.pdf' ,
		'name' => 'Change your product subscription',
	);
	return $actions;
}
remove_filter( 'wcs_view_subscription_actions', 'Javorszky\Toolbox\add_link_to_edit_page', 11 );
add_filter( 'wcs_view_subscription_actions', 'yv_add_link_to_edit_page', 11, 2 );



/** 
 * 40. Filter WooCommerce Flexslider options - Add Navigation Arrows
 */
add_filter( 'woocommerce_single_product_carousel_options', 'sf_update_woo_flexslider_options' );

function sf_update_woo_flexslider_options( $options ) {

	//$options['directionNav'] = true;
	$options['controlNav'] = true;
	$options['slideshow'] = true;

    return $options;
}

/** 
 * 41. WooCommerce Flexslider - Remove Zoom
 */
add_action( 'after_setup_theme', 'remove_zoom_theme_support', 99 );

function remove_zoom_theme_support() { 
	remove_theme_support( 'wc-product-gallery-zoom' );
}


/* 43. Add Google Tracking event on checkout header
=================================================================== */
function event_snippet() {
	if(is_checkout()) {
	echo "<!-- Event snippet for Checkout Page conversion page --><script>gtag('event', 'conversion', {'send_to': 'AW-719971251/b8e-CJP6sagBELPHp9cC'});</script>";
}
}

// Add hook for front-end <head></head>
add_action( 'wp_head', 'event_snippet' );

// 44. Remove afterpay for variable subscriptions (30days/60days/90days) but keep it for one time.

add_filter('woocommerce_available_payment_gateways', 'conditional_payment_gateways', 10, 1);
function conditional_payment_gateways( $available_gateways ) {
	// Not in backend (admin)
	if( is_admin() ) {
		return $available_gateways;
	}
	
	// Added by MWB.	
	if ( ! function_exists( 'WC' ) || WC()->cart->is_empty() ) {

		return $available_gateways;
	}

	foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$prod_variable = $prod_simple = $prod_subscription = false;
			// Get the WC_Product object
			$product = wc_get_product($cart_item['product_id']);
			// Get the product types in cart (example)
			if($product->is_type('simple')) $prod_simple = true;
			if($product->is_type('variable')) $prod_variable = true;
			if($product->is_type('subscription')) $prod_subscription = true;
	}
	
	if($prod_variable){unset(
	$available_gateways['afterpay']); // unset 'afterpay'
}     

	return $available_gateways;
}


/* 45. Add description to menu items */
add_filter( 'walker_nav_menu_start_el', 'wpstudio_add_description', 10, 2 );
function wpstudio_add_description( $item_output, $item ) {
    $description = $item->post_content;
    if (' ' !== $description ) {
        return preg_replace( '/(<a.*)</', '$1' . '<small class="menu-description" style="display: block;margin-bottom: 0.3rem;">' . $description . '</small><', $item_output) ;
    }
    else {
        return $item_output;
    };
}

/* 46. Shortcode that displays the amount of completed orders */
function youveda_customer_count() {
	global $wpdb;
	$order_count = $wpdb->get_var("SELECT count(*) AS 'count' FROM `wp_posts` WHERE post_type = 'shop_order' AND post_status = 'wc-completed'");
	$order_count = $order_count + 4343;
	$order_count = number_format($order_count);
	return $order_count;
}
add_shortcode( 'customer-count', 'youveda_customer_count' );


/**
 * Product category image for the Shop
 */
if ( ! function_exists( 'yv_custom_settings_for_shop_display' ) ) {
	/**
	 * Define custom settings for bundles on HP for grouped product
	 *
	 * @return void
	 */
	function yv_custom_settings_for_shop_display() {
		$bundles_settings_mb = new_cmb2_box(
			array(
				'id'           => '_yv_shop_display_settings',
				'title'        => __( 'Shop images', 'cmb2' ),
				// Post type.
				'object_types' => array( 'product' ),
				'context'      => 'normal',
				'priority'     => 'high',
				// Show field names on the left.
				'show_names'   => true,
				'show_on_cb'   => array(
					'Yv_Admin',
					'cmb_only_show_for_grouped_products',
				),
			)
		);

		$bundles_settings_mb->add_field(
			array(
				'name'         => 'Product Image',
				'desc'         => 'Upload an image or browser in media library',
				'id'           => 'yv_product_shop_product_image',
				'type'         => 'file',
				// Optional.
				'options'      => array(
					// Hide the text input for the url.
					'url' => false,
				),
				'text'         => array(
					// Change upload button text. Default: "Add or Upload File".
					'add_upload_file_text' => 'Add File',
				),
				// Image size to use when previewing in the admin.
				'preview_size' => array( 100, 100 ),
			)
		);

		$bundles_settings_mb->add_field(
			array(
				'name'         => 'Product Icon',
				'desc'         => 'Upload an image or browser in media library',
				'id'           => 'yv_product_shop_product_icon',
				'type'         => 'file',
				// Optional.
				'options'      => array(
					// Hide the text input for the url.
					'url' => false,
				),
				'text'         => array(
					// Change upload button text. Default: "Add or Upload File".
					'add_upload_file_text' => 'Add File',
				),
				// Image size to use when previewing in the admin.
				'preview_size' => array( 100, 100 ),
			)
		);

		$bundles_settings_mb->add_field(
			array(
				'name'         => 'Product Type',
				'desc'         => 'Type to display on product page (e.g. \'Ayurvedic Supplement Kit\')',
				'id'           => 'yv_product_shop_product_type',
				'type'         => 'text',
				// Optional.
				'options'      => array(
					// Hide the text input for the url.
					'url' => false,
				),
			)
		);

		$bundles_settings_mb->add_field(
			array(
				'name'         => 'Product Type Icon',
				'desc'         => 'Upload an image or browser in media library',
				'id'           => 'yv_product_shop_product_type_icon',
				'type'         => 'file',
				// Optional.
				'options'      => array(
					// Hide the text input for the url.
					'url' => false,
				),
				'text'         => array(
					// Change upload button text. Default: "Add or Upload File".
					'add_upload_file_text' => 'Add File',
				),
				// Image size to use when previewing in the admin.
				'preview_size' => array( 100, 100 ),
			)
		);
	}
}
add_action( 'cmb2_admin_init', 'yv_custom_settings_for_shop_display' );

/**
 * 48. Product gallery override
 */
function yv_get_gallery_image_html( $attachment_id, $main_image = false ) {
	$flexslider        = (bool) apply_filters( 'woocommerce_single_product_flexslider_enabled', get_theme_support( 'wc-product-gallery-slider' ) );
	$gallery_thumbnail = wc_get_image_size( 'gallery_thumbnail' );
	$thumbnail_size    = apply_filters( 'woocommerce_gallery_thumbnail_size', array( $gallery_thumbnail['width'], $gallery_thumbnail['height'] ) );
	$image_size        = apply_filters( 'woocommerce_gallery_image_size', $flexslider || $main_image ? 'woocommerce_single' : $thumbnail_size );
	$full_size         = apply_filters( 'woocommerce_gallery_full_size', apply_filters( 'woocommerce_product_thumbnails_large_size', 'full' ) );
	$thumbnail_src     = wp_get_attachment_image_src( $attachment_id, $thumbnail_size );
	$full_src          = wp_get_attachment_image_src( $attachment_id, $full_size );
	$alt_text          = trim( wp_strip_all_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) );
	$image             = wp_get_attachment_image(
		$attachment_id,
		$image_size,
		false,
		apply_filters(
			'woocommerce_gallery_image_html_attachment_image_params',
			array(
				'title'                   => _wp_specialchars( get_post_field( 'post_title', $attachment_id ), ENT_QUOTES, 'UTF-8', true ),
				'data-caption'            => _wp_specialchars( get_post_field( 'post_excerpt', $attachment_id ), ENT_QUOTES, 'UTF-8', true ),
				'data-src'                => esc_url( $full_src[0] ),
				'data-large_image'        => esc_url( $full_src[0] ),
				'data-large_image_width'  => esc_attr( $full_src[1] ),
				'data-large_image_height' => esc_attr( $full_src[2] ),
				'class'                   => esc_attr( $main_image ? 'wp-post-image' : '' ),
			),
			$attachment_id,
			$image_size,
			$main_image
		)
	);

	return '<div data-thumb="' . esc_url( $thumbnail_src[0] ) . '" data-thumb-alt="' . esc_attr( $alt_text ) . '" class="woocommerce-product-gallery__image">' . $image . '</div>';
}

/**
 * 49. WooCommerce variation dropdown override
 */
function wc_dropdown_variation_attribute_options( $args = array() ) {
	$args = wp_parse_args(
		apply_filters( 'woocommerce_dropdown_variation_attribute_options_args', $args ),
		array(
			'options'          => false,
			'attribute'        => false,
			'product'          => false,
			'selected'         => false,
			'name'             => '',
			'id'               => '',
			'class'            => '',
			'show_option_none' => __( 'Choose an option', 'woocommerce' ),
		)
	);

	// Get selected value.
	if ( false === $args['selected'] && $args['attribute'] && $args['product'] instanceof WC_Product ) {
		$selected_key     = 'attribute_' . sanitize_title( $args['attribute'] );
		$args['selected'] = isset( $_REQUEST[ $selected_key ] ) ? wc_clean( wp_unslash( $_REQUEST[ $selected_key ] ) ) : $args['product']->get_variation_default_attribute( $args['attribute'] ); // WPCS: input var ok, CSRF ok, sanitization ok.
	}

	$options               = $args['options'];
	$product               = $args['product'];
	$attribute             = $args['attribute'];
	$name                  = $args['name'] ? $args['name'] : 'attribute_' . sanitize_title( $attribute );
	$id                    = $args['id'] ? $args['id'] : sanitize_title( $attribute );
	$class                 = $args['class'];
	$show_option_none      = (bool) $args['show_option_none'];
	$show_option_none_text = $args['show_option_none'] ? $args['show_option_none'] : __( 'Choose an option', 'woocommerce' ); // We'll do our best to hide the placeholder, but we'll need to show something when resetting options.

	if ( empty( $options ) && ! empty( $product ) && ! empty( $attribute ) ) {
		$attributes = $product->get_variation_attributes();
		$options    = $attributes[ $attribute ];
	}

	$html  = '<select id="' . esc_attr( $id ) . '" class="' . esc_attr( $class ) . '" name="' . esc_attr( $name ) . '" data-attribute_name="attribute_' . esc_attr( sanitize_title( $attribute ) ) . '" data-show_option_none="' . ( $show_option_none ? 'yes' : 'no' ) . '">';

	if ( ! empty( $options ) ) {
		if ( $product && taxonomy_exists( $attribute ) ) {
			// Get terms if this is a taxonomy - ordered. We need the names too.
			$terms = wc_get_product_terms(
				$product->get_id(),
				$attribute,
				array(
					'fields' => 'all',
				)
			);

			foreach ( $terms as $term ) {
				if ( in_array( $term->slug, $options, true ) ) {
					$html .= '<option value="' . esc_attr( $term->slug ) . '" ' . selected( sanitize_title( $args['selected'] ), $term->slug, false ) . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name, $term, $attribute, $product ) ) . '</option>';
				}
			}
		} else {
			foreach ( $options as $option ) {
				// This handles < 2.4.0 bw compatibility where text attributes were not sanitized.
				$selected = sanitize_title( $args['selected'] ) === $args['selected'] ? selected( $args['selected'], sanitize_title( $option ), false ) : selected( $args['selected'], $option, false );
				$html    .= '<option value="' . esc_attr( $option ) . '" ' . $selected . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $option, null, $attribute, $product ) ) . '</option>';
			}
		}
	}

	$html .= '</select>';

	echo apply_filters( 'woocommerce_dropdown_variation_attribute_options_html', $html, $args ); // WPCS: XSS ok.
}

/**
 * 50. Modify the main product query so that it only displays Supplement Kits
 */
function yv_change_main_product_query( $query ) {
	$tax_query = array(
		'relation' => 'AND',
		array(
			'taxonomy' => 'product_cat',
			'field' => 'slug',
			'terms' => 'supplement-kits'
		),
		array(
			'taxonomy' => 'product_type',
			'field' => 'slug',
			'terms' => 'grouped'
		)
	);
	$query->set( 'tax_query', $tax_query );
	return $query;
}
add_filter( 'woocommerce_product_query', 'yv_change_main_product_query', 10, 1 );


/**
 * 51. Hide shipping rates when free shipping is available.
 * Updated to support WooCommerce 2.6 Shipping Zones.
 *
 * @param array $rates Array of rates found for the package.
 * @return array
 */
 function my_hide_shipping_when_free_is_available( $rates ) {
	$free = array();

	foreach ( $rates as $rate_id => $rate ) {
		if ( 'free_shipping' === $rate->method_id ) {
			$free[ $rate_id ] = $rate;
			break;
		}
	}

	return ! empty( $free ) ? $free : $rates;
}


add_filter( 'woocommerce_package_rates', 'my_hide_shipping_when_free_is_available', 100 );
/* Add message above login form */
function wpsd_add_login_message() {
	return '<p class="message">For security reasons all passwords must be reset (unless you are using Google/Facebook to log). Please use <a href="/my-account/lost-password/">this link</a> to reset your password.</p>';
}
add_filter('login_message', 'wpsd_add_login_message');

/**
 * Changes 'Username' to 'Email Address' on wp-admin login form
 * and the forgotten password form
 *
 * @return null
 */
function yv_login_head() {
    function yv_username_label( $translated_text, $text, $domain ) {
        if ( 'Username or Email Address' === $text || 'Username' === $text ) {
            $translated_text = __( 'Email' , 'youveda' );
        }
		
		if ( 'Lost your password?' === $text  ) {
            $translated_text = __( 'Forgot your password?' , 'youveda' );
        }
		
        return $translated_text;
    }
    add_filter( 'gettext', 'yv_username_label', 20, 3 );
}
add_action( 'login_head', 'yv_login_head' );

/* try to make PHPSESSID secure */
session_start();
$params = session_get_cookie_params();
setcookie("PHPSESSID", session_id(), 0, $params["path"], $params["domain"],
    false,  // this is the secure flag you need to set. Default is false.
    true  // this is the httpOnly flag you need to set
);

// include custom jQuery
function shapeSpace_include_custom_jquery() {

	wp_deregister_script('jquery');
	wp_enqueue_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js', array(), null, true);

}
//add_action('wp_enqueue_scripts', 'shapeSpace_include_custom_jquery');

// Change woo text in buttons
add_filter( 'gettext', 'change_woocommerce_return_to_shop_text', 9999, 3 );
function change_woocommerce_return_to_shop_text( $translated_text, $text, $domain ) {
	
        switch ( $translated_text ) {
  	     case 'every 2 days' :$translated_text = __( 'every 60 days', 'woocommerce' );break;
  	     case 'every 1 day' : $translated_text = __( 'every 30 days', 'woocommerce' );break;
	 }

 return $translated_text; 

}



/*===========================================
	MakeWebBetter Functions Here
============================================*/


/**
 * 52. Add Product Image to Cart Item Name in Order Review.
 *
 * @param string 	$cart_item_name 	Cart Item Name.
 * @param object 	$cart_item 			Cart Item.
 * @param string 	$cart_item_key 		Cart Item Key.
 * @return string
 */
add_filter( 'woocommerce_cart_item_name', 'display_product_image_in_order_item', 99, 3 );
function display_product_image_in_order_item( $cart_item_name, $cart_item, $cart_item_key ) {

	// Targeting Checkout page only.
	if( is_checkout() ) {

	    $product   = ! empty( $cart_item['data'] ) ? $cart_item['data'] : false; 
	    $cart_item_quantity   = ! empty( $cart_item['quantity'] ) ? $cart_item['quantity'] : false; 
	    $thumbnail = ! empty( $product ) ? $product->get_image( array( 75, 75 ) ) : false;

	    $get_variation_attributes_html = false;

	    if ( ! empty( $product ) && in_array( $product->get_type(), array( 'subscription_variation', 'variation' ) ) ) {
	    	
    		$_cart_item_name = get_the_title( $product->get_id() );
    		if ( ! empty( $_cart_item_name ) ) {

				$_cart_item_attr = explode( 'Every', $_cart_item_name );
				$_cart_item_attr = ! empty( $_cart_item_attr[1] ) ? $_cart_item_attr[1] : false;

				if ( ! empty( $_cart_item_attr  ) ) {
					$cart_item_name = $cart_item_name . ' – Subscription ( Every ' . $_cart_item_attr . ' ) ';
				}
    		}
	    }

	    if( $product->get_image_id() > 0 ) {
	        $cart_item_name = '<div class="mwb-custom-item-thumbnail">' . $thumbnail . '<span class=mwb-custom-item-quantity>' . $cart_item_quantity . '</span></div>' . '<div class="mwb-custom-item-name">' . $cart_item_name . '</div>';
	    }
	}

	return $cart_item_name;
}


/**
 * 53. Hide Product Quantity to Cart Item in Order Review.
 *
 * @return false
 */
add_filter( 'woocommerce_checkout_cart_item_quantity', '__return_false' );


/**
 * 54. Hide Recurring totals in Order Review.
 *
 * @return false
 */
remove_action( 'woocommerce_review_order_after_order_total', 'WC_Subscriptions_Cart::display_recurring_totals' );

/**
 * 55. Default Place Order Button Html Hidden.
 *
 * @return false
 */
add_filter( 'woocommerce_order_button_html', '__return_false' );