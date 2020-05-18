<?php
/**
 * Order details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! $order = wc_get_order( $order_id ) ) {
	return;
}

$order_items           = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
$show_purchase_note    = $order->has_status( apply_filters( 'woocommerce_purchase_note_order_statuses', array( 'completed', 'processing' ) ) );
$show_customer_details = is_user_logged_in() && $order->get_user_id() === get_current_user_id();
$downloads             = $order->get_downloadable_items();
$show_downloads        = $order->has_downloadable_item() && $order->is_download_permitted();

if ( $show_downloads ) {
	wc_get_template( 'order/order-downloads.php', array( 'downloads' => $downloads, 'show_title' => true ) );
}
?>
<div class="card woocommerce-order-details">
	<div class="card-body">
		<?php
		do_action( 'woocommerce_order_details_before_order_table_items', $order );

		foreach ( $order_items as $item_id => $item ) {
			$product = $item->get_product();

			wc_get_template( 'order/order-details-item.php', array(
				'order'			     => $order,
				'item_id'		     => $item_id,
				'item'			     => $item,
				'show_purchase_note' => $show_purchase_note,
				'purchase_note'	     => $product ? $product->get_purchase_note() : '',
				'product'	         => $product,
			) );
		}

		do_action( 'woocommerce_order_details_after_order_table_items', $order );
		?>
		<?php do_action( 'woocommerce_order_details_before_order_table', $order ); ?>
		<div class="woocommerce-table woocommerce-table--order-details shop_table order_details">
			<?php
				foreach ( $order->get_order_item_totals() as $key => $total ) {
					if ( 'payment_method' === $key ) {
						continue;
					}
					?>
					<div class="row">
						<div class="col-6"><?php echo 'order_total' === $key ? 'Total paid' : $total['label']; ?></div>
						<div class="col-6"><?php echo ( 'payment_method' === $key ) ? esc_html( $total['value'] ) : $total['value']; ?></div>
					</div>
					<?php
				}
			?>
			<?php if ( $order->get_customer_note() ) : ?>
				<div class="row">
					<div class="col-6"><?php _e( 'Note:', 'woocommerce' ); ?></div>
					<<div class="col-6">><?php echo wptexturize( $order->get_customer_note() ); ?></div>
				</tr>
			<?php endif; ?>

		</div>

		<?php do_action( 'woocommerce_order_details_after_order_table', $order ); ?>

	</div>
</div>
<?php
