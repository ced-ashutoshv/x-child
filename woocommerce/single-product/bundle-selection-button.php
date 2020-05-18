<?php 
/**
 * The template for displaying buttons to add to the bundle
 *
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

// wc_get_template( 'single-product/bundle-button.php', array(
// 								'grouped_product_id'  => $id,
// 								'products_data'   => $data
// 							) );
// 'yv_product_bundle_button_icon',
// 'yv_product_bundle_button_icon_mobile',
// $product_icon = get_post_meta( $grouped_product_id, 'yv_product_bundle_button_icon', true); 
$product_icon = get_post_meta( $grouped_product_id, 'yv_product_bundle_button_icon_id', true);
$icon_server_path = !empty($product_icon) ? get_attached_file( $product_icon ) : '';

$product_icon_mobile = get_post_meta( $grouped_product_id, 'yv_product_bundle_button_icon_mobile_id', true);
$icon_mobil_server_path = !empty($product_icon_mobile) ? get_attached_file( $product_icon_mobile ) : '';

$product_image = get_post_meta( $grouped_product_id, 'yv_product_bundle_product_image', true);
$product_image = str_replace('http://youveda.docker', get_site_url(), $product_image);
$product_title = ucfirst( trim( str_replace('my healthy', '', strtolower($products_data['product_title']) ) ) );
?>
<div class="col col-md-6">
	<a 	href="#" 
		class="yv-select-bundle" 
		data-bundle-data="<?php echo htmlspecialchars(wp_json_encode($products_data)); ?>" 
		data-bundle-product-image="<?php  echo esc_attr($product_image);?>"
		data-product-name="<?php  echo esc_attr( $product_title );?>"
		>
		<?php 
		if( !empty($icon_server_path) && strpos( $icon_server_path , '.svg') !== false){
			echo  "<span class='d-none d-md-block'>" ;
			echo  "<input type='radio' id='bundle-Section-RadioButton-". esc_attr(trim($product_title)) ."'>";
			echo  file_get_contents( $icon_server_path ) ;
			echo '</span>';
		}
		if( !empty($icon_mobil_server_path) && strpos( $icon_mobil_server_path , '.svg') !== false){
			echo  "<span class='d-block d-md-none'>" ;
			echo  file_get_contents( $icon_mobil_server_path ) ;
			echo '</span>';
		}
 		?>
	</a>
</div>