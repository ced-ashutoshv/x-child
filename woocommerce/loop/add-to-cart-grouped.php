<?php
/**
 * Grouped product add to cart on loop
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce/Templates
 * @version     3.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product, $post;
$yv_ingredients = json_decode(get_post_meta( $product->get_id(), 'yv_ingredients', true ));
echo "<div class='flex-container flex-column subscription-options product-".$product->get_id()."'>";
foreach ($yv_ingredients as $ingredient) {
	echo "<div class='ingredients-flex-container'>";
	echo "<img src='https://stagyouveda.wpengine.com/wp-content/uploads/2019/11/green-check.png' width='15px'>";
	echo "<p>$ingredient</p>";
	echo "</div>";
}
echo "</div>";
return;