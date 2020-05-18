<?php
/**
 * My Subscriptions section on the My Account page
 *
 * @author   Prospress
 * @category WooCommerce Subscriptions/Templates
 * @version  2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="woocommerce_account_subscriptions">

	<?php if ( WC_Subscriptions::is_woocommerce_pre( '2.6' ) ) : ?>
	<h2><?php esc_html_e( 'My Subscriptions', 'woocommerce-subscriptions' ); ?></h2>
	<?php endif; ?>

	<?php if ( ! empty( $subscriptions ) ) : ?>
	<table class="shop_table shop_table_responsive my_account_subscriptions my_account_orders">

	<thead>
		<tr>
			<th class="subscription-id order-number"><span class="nobr"><?php esc_html_e( 'Id', 'woocommerce-subscriptions' ); ?></span></th>
			<th class="subscription-products order-products"><span class="nobr"><?php esc_html_e( 'Products', 'woocommerce-subscriptions' ); ?></span></th>
			<th class="subscription-status order-status"><span class="nobr"><?php esc_html_e( 'Status', 'woocommerce-subscriptions' ); ?></span></th>
			<th class="subscription-next-payment order-date"><span class="nobr"><?php echo esc_html_x( 'Next Payment', 'table heading', 'woocommerce-subscriptions' ); ?></span></th>
			<th class="subscription-total order-total"><span class="nobr"><?php echo esc_html_x( 'Total', 'table heading', 'woocommerce-subscriptions' ); ?></span></th>
			<th class="subscription-actions order-actions">&nbsp;</th>
		</tr>
	</thead>

	<tbody>
	<?php /** @var WC_Subscription $subscription */ ?>
	<?php foreach ( $subscriptions as $subscription_id => $subscription ) : ?>
		<tr class="order">
			<td class="subscription-id order-number" data-title="<?php esc_attr_e( 'ID', 'woocommerce-subscriptions' ); ?>">
				<?php 
				if( $subscription->get_total() > 0 ) { ?>
					<a href="<?php echo esc_url( $subscription->get_view_order_url() ); ?>">
						<?php echo esc_html( sprintf( _x( '#%s', 'hash before order number', 'woocommerce-subscriptions' ), $subscription->get_order_number() ) ); ?>
					</a>
				<?php 
				}else{ 
					echo esc_html( sprintf( _x( '#%s', 'hash before order number', 'woocommerce-subscriptions' ), $subscription->get_order_number() ) ); 
				} ?>
				<?php do_action( 'woocommerce_my_subscriptions_after_subscription_id', $subscription ); ?>
			</td>
			<td class="subscription-products order-products" data-title="<?php esc_attr_e( 'Products', 'woocommerce-subscriptions' ); ?>">
				<?php 
					$products_in_order = $subscription->get_items();
					if( count($products_in_order)>0 ){
						foreach( $products_in_order as $product){
							echo $product->get_name() .' x ' . $product->get_quantity();
							echo "<br>";
						}
					}else{
						echo "-";
					}
				?>
			</td>

			<?php 
			$manually_activated = 0 == absint(floor($subscription->get_total()));
			if($manually_activated && 'cancelled' !== $subscription->get_status()){
				$colspan="colspan='4'";
				$statusToPrint= "Manually activated on ". esc_html( wc_format_datetime( $subscription->get_date_created() ) ).". ";
				$statusToPrint.='This subscription was bought through one of our resellers or before 05/20/2018. ';
				$statusToPrint.='To unsubscribe please send an email to <a href="mailto:customerservice@youveda.com">customerservice@youveda.com</a>';
				
			}
			else{
				$colspan="";
				$statusToPrint=esc_attr( wcs_get_subscription_status_name( $subscription->get_status() ) );
			}
			?>

			<td class="subscription-status order-status" <?php echo $colspan; ?> data-title="<?php esc_attr_e( 'Status', 'woocommerce-subscriptions' ); ?>">
				<?php echo $statusToPrint; ?>
			</td>
			<?php if(!$manually_activated) { ?>
			<td class="subscription-next-payment order-date" data-title="<?php echo esc_attr_x( 'Next Payment', 'table heading', 'woocommerce-subscriptions' ); ?>">
			
					<?php if( $subscription->get_date_to_display( 'next_payment' ) =='-'){
							$nextPayment='N/A (see note below*)';
						}else{
	$nextPayment=$subscription->get_date_to_display( 'next_payment' );
}
											
				
				?>
					<?php echo esc_attr( $nextPayment ); ?>
					<?php if ( ! $subscription->is_manual() && $subscription->has_status( 'active' ) && $subscription->get_time( 'next_payment' ) > 0 ) : ?>
						<?php
						// translators: placeholder is the display name of a payment gateway a subscription was paid by
						$payment_method_to_display = sprintf( __( 'Via %s', 'woocommerce-subscriptions' ), $subscription->get_payment_method_to_display() );
						$payment_method_to_display = apply_filters( 'woocommerce_my_subscriptions_payment_method', $payment_method_to_display, $subscription );
						?>
					<br/><small><?php echo esc_attr( $payment_method_to_display ); ?></small>
					<?php endif; ?>
			</td>
			<td class="subscription-total order-total" data-title="<?php echo esc_attr_x( 'Total', 'Used in data attribute. Escaped', 'woocommerce-subscriptions' ); ?>">
				<?php echo wp_kses_post( $subscription->get_formatted_order_total() ); ?>
			</td>
			<td class="subscription-actions order-actions">				
					<a href="<?php echo esc_url( $subscription->get_view_order_url() ) ?>" class="button view"><?php echo esc_html_x( 'View', 'view a subscription', 'woocommerce-subscriptions' ); ?></a>
					<?php do_action( 'woocommerce_my_subscriptions_actions', $subscription ); ?>
		
			</td>
			<?php } ?>
		</tr>
	<?php endforeach; ?>
	</tbody>

	</table>
	
	<small style="margin:30px auto; border:1px solid grey; padding:12px; display:block">
		Kindly note that "Next Payment" only applies to <strong>active subscriptions</strong>. One time purchases are processed as 1-month subscriptions and terminated accordingly in order to provide you exclusive in-app-content. In addition, rest assured that, we will never automatically enroll you into a subscription, without your approval and consent. 
	</small>
	<?php else : ?>

		<p class="no_subscriptions">
			<?php
			// translators: placeholders are opening and closing link tags to take to the shop page
			printf( esc_html__( 'You have no active subscriptions. Find your first subscription in the %sstore%s.', 'woocommerce-subscriptions' ), '<a href="' . esc_url( apply_filters( 'woocommerce_subscriptions_message_store_url', get_permalink( wc_get_page_id( 'shop' ) ) ) ) . '">', '</a>' );
			?>
		</p>

	<?php endif; ?>

</div>

<?php
