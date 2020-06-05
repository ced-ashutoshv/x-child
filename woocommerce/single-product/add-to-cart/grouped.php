<?php
/**
 * Grouped product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/grouped.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce/Templates
 * @version     3.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product, $post;

$link = esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) );

do_action( 'woocommerce_before_add_to_cart_form' ); ?>



			<?php
			$quantites_required      = false;
			$previous_post           = $post;
			$grouped_product_columns = apply_filters( 'woocommerce_grouped_product_columns', array(
				'quantity',
				'label',
				'price',
			), $product );

			?>
			<div class="product-add-to-cart-options">
			<?php
			$counter = 1;
			$not_visible = true;
			foreach ( $grouped_products as $grouped_product_child ) {
				
				if(  'subscription' === $grouped_product_child->get_type() && 
					 'month' 		=== WC_Subscriptions_Product::get_period( $grouped_product_child ) &&
					 0 				=== WC_Subscriptions_Product::get_length( $grouped_product_child ) 
				){
					continue;
				}
				

				$post_object        = get_post( $grouped_product_child->get_id() );
				$quantites_required = $quantites_required || ( $grouped_product_child->is_purchasable() && ! $grouped_product_child->has_options() );
				$post               = $post_object; // WPCS: override ok.
				setup_postdata( $post );
				$css_form_class = $grouped_product_child === reset( $grouped_products ) ? 'form-add-to-cart-border-bottom' : '';
				if ( strpos( $grouped_product_child->get_name(), 'One time' ) !== false ) {
					$title = 'One Time Purchase';
					$onetime = true;
				} else {
					$cat = yv_get_primary_taxonomy_term( $grouped_product_child->get_id() );
					if( $cat['slug'] == 'essential-oils') {
						$title = 'Auto-Delivery';
					} else {
						$title = 'Subscribe & Save';
					}
					$onetime = false;
				}

				$price = $grouped_product_child->get_price();

				$price_string = '$' . $price;

				$category = yv_get_primary_taxonomy_term( $product->get_id() );
				if( $onetime ) {
					if( $category['slug'] == 'supplement-kits' ) {
						$price_string .= ' ($3.30/pack)';
					}
				} else {
					if( $category['slug'] == 'supplement-kits' ) {
						$price_string .= ' ($2.20/pack)';
					}
				}
				?>
				<div class="product-option-group-container">
					<div class="product-option-selector">
						<input type="radio" name="product-selected-option" data-id="<?php echo $grouped_product_child->get_id(); ?>" id="product-selected-option_<?php echo $counter; ?>" checked="checked">
						<label class="cart-label <?php if($not_visible) echo "label-unselected"; else echo "label-selected"; ?>" for="product-selected-option_<?php echo $counter; ?>"><?php echo $title; ?> - <?php echo $price_string; ?></label>
					</div>
					<div id="product-cart-form-group_<?php echo $counter; ?>" class="product-cart-form-group <?php if($not_visible) echo 'not-visible'; else echo 'visible';?>">
						<form class="cart grouped_form <?php echo $css_form_class; echo $post_object->post_type; ?>" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data'>
							<?php
							if ( ! $grouped_product_child->is_purchasable() || $grouped_product_child->has_options() || ! $grouped_product_child->is_in_stock() ) {
								//REMOVED: woocommerce_template_loop_add_to_cart();
								//ADDED: "add to cart" button for variation on grouped view
								//do_action( 'woocommerce_before_add_to_cart_quantity' );

								woocommerce_quantity_input( array(
									'input_name'  => 'quantity',//[' . $grouped_product_child->get_id() . ']',
									'input_value' => isset( $_POST['quantity'][ $grouped_product_child->get_id() ] ) ? wc_stock_amount( wc_clean( wp_unslash( $_POST['quantity'][ $grouped_product_child->get_id() ] ) ) ) : 0, // WPCS: CSRF ok, input var okay, sanitization ok.
									'min_value'   => apply_filters( 'woocommerce_quantity_input_min', 0, $grouped_product_child ),
									'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $grouped_product_child->get_max_purchase_quantity(), $grouped_product_child ),
								) );

								do_action( 'woocommerce_after_add_to_cart_quantity' );
							} elseif ( $grouped_product_child->is_sold_individually() ) {

								echo '<input type="checkbox" name="' . esc_attr( 'quantity[' . $grouped_product_child->get_id() . ']' ) . '" value="1" class="wc-grouped-product-add-to-cart-checkbox" />';
							} else {

								//do_action( 'woocommerce_before_add_to_cart_quantity' );

								woocommerce_quantity_input( array(
									'input_name'  => 'quantity[' . $grouped_product_child->get_id() . ']',
									'input_value' => isset( $_POST['quantity'][ $grouped_product_child->get_id() ] ) ? wc_stock_amount( wc_clean( wp_unslash( $_POST['quantity'][ $grouped_product_child->get_id() ] ) ) ) : 0, // WPCS: CSRF ok, input var okay, sanitization ok.
									'min_value'   => apply_filters( 'woocommerce_quantity_input_min', 0, $grouped_product_child ),
									'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $grouped_product_child->get_max_purchase_quantity(), $grouped_product_child ),
								) );

								do_action( 'woocommerce_after_add_to_cart_quantity' );
							}
							?>
						</div>
						<table cellspacing="0" class="woocommerce-grouped-product-list group_table">
							<tbody>
				<?php
				$not_visible = false;
				echo '<tr id="product-' . esc_attr( $grouped_product_child->get_id() ) . '" class="woocommerce-grouped-product-list-item ' . esc_attr( implode( ' ', wc_get_product_class( '', $grouped_product_child->get_id() ) ) ) . '">';
				// Output columns for each product.
				foreach ( $grouped_product_columns as $column_id ) {
					do_action( 'woocommerce_grouped_product_list_before_' . $column_id, $grouped_product_child );

					switch ( $column_id ) {
						case 'quantity':
							ob_start();

							$value = ob_get_clean();
							break;
						case 'label':
							$value  = '<label for="product-' . esc_attr( $grouped_product_child->get_id() ) . '">';
							$value .= $grouped_product_child->is_visible() ? '<a href="' . esc_url( apply_filters( 'woocommerce_grouped_product_list_link', $grouped_product_child->get_permalink(), $grouped_product_child->get_id() ) ) . '">' . $grouped_product_child->get_name() . '</a>' : $grouped_product_child->get_name();
							$value .= '</label>';
							break;
						case 'price':
							//echo $grouped_product_child->get_type();
							$value = $grouped_product_child->get_price_html() . wc_get_stock_html( $grouped_product_child );
							break;
						default:
							$value = '';
							break;
					}

					echo '<td class="woocommerce-grouped-product-list-item__' . esc_attr( $column_id ) . ' x-column x-lg x-1-2">' . apply_filters( 'woocommerce_grouped_product_list_column_' . $column_id, $value, $grouped_product_child ) . '</td>'; // WPCS: XSS ok.

					do_action( 'woocommerce_grouped_product_list_after_' . $column_id, $grouped_product_child );
				}
				echo '</tr>';
				echo '</table>';
				$add_to_cartproduct_id = 'variable-subscription' == $grouped_product_child->get_type() ? $grouped_product_child->get_id() : $product->get_id() ;
				echo '<input type="hidden" name="add-to-cart" value="'.esc_attr( $add_to_cartproduct_id ).'"/>
<button type="submit" class="single_add_to_cart_button button alt mb30px">'.esc_html( $product->single_add_to_cart_text() ).'</button>
</form></div></div>';
				$counter++;
			}
			$post = $previous_post; // WPCS: override ok.
			setup_postdata( $post );
			?>
	</div>

	<?php
	do_action( 'woocommerce_before_add_to_cart_button' );
	?>
	<!--a id="yv-add-to-cart" href="<?php echo $link; ?>" data-url="<?php echo $link; ?>" class="single_add_to_cart_button button alt <?php echo 'single_add_to_cart_button-' . esc_attr( $product->get_type() ); ?>" disabled="disabled">
		<?php echo esc_html( $product->single_add_to_cart_text() ); ?>
	</a-->		
	<?php
	do_action( 'woocommerce_after_add_to_cart_button' );
	?>
	</div></div>

	<!-- <input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" /> -->

	<?php if ( $quantites_required ) : ?>

		<?php 
		/* do_action( 'woocommerce_before_add_to_cart_button' ); ?>

		<button type="submit" class="single_add_to_cart_button button alt"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button> 

		<?php do_action( 'woocommerce_after_add_to_cart_button' ); */
		?>

	<?php endif; ?>


<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>
