<?php
namespace Javorszky\Toolbox;

function generate_option_text( $subscription ) {
	// translators: $1: ID of subscription, $2: recurrence, eg "2 months", $3: next payment date
	$base_text = sprintf( esc_html__( '#%1$d - every %2$s - next: %3$s', 'jg-toolbox' ), $subscription->get_id(), wcs_get_subscription_period_strings( $subscription->get_billing_interval(), $subscription->get_billing_period() ), date_i18n( wc_date_format(), $subscription->get_time( 'next_payment' ) ) );

	return $base_text;
}
if ( ! $product->is_purchasable() ) {
	return;
}
?>
<div id="yv-show-existing-subscriptions">
	<p><i class="fa fa-exchange"></i><span>Do you wish to add this product to your current subscription? </span></p>
</div>
<div class="yv-add-to-subscription">
	<form action="#" method="POST" class="jgtb-add-to-subscription">
		<label for="jgtb_add_to_existing">Select the subscription you would like to add:<br><i>Please note that the new product will follow your current subscription schedule. You can always manage your subscription from <a href="<?php echo home_url('/my-account'); ?>">My Account</a> page.</i></label>
		<select name="jgtb_add_to_existing_subscription" id="jgtb_add_to_existing">
			<option value="null">
				<?php echo esc_html_x( '-- Select an existing subscription --', 'default option in dropdown in add to existing subscription template', 'jg-toolbox' ); ?>
				</option>
			<?php
			foreach ( $subscriptions as $subscription ) {
				$option_text = str_replace('every days', 'every 30 days', wp_kses( sprintf( '<option value="%s">%s</option>', $subscription->get_id(), generate_option_text( $subscription ) ), array( 'option' => array( 'value' => array() ) ) ));
				$option_text = str_replace('every 2 days', 'every 60 days', $option_text);
				$option_text = str_replace('every 3 days', 'every 90 days', $option_text);
				echo $option_text;
			}
			unset( $subscription );
			?>
		</select>
		<?php
		foreach ( $subscriptions as $subscription ) {
			$items_string = Utilities\generate_nonce_on_items( $subscription );
			wp_nonce_field( 'add_to_subscription_' . $items_string, 'jgtbwpnonce_' . $subscription->get_id(), false );
		}
		?>
		<input type="hidden" name="ats_quantity" value="1">
		<input type="hidden" name="ats_product_id" value="<?php echo esc_attr( $product->get_id() ); ?>">
		<input type="hidden" name="ats_variation_id" value="0">
		<input type="hidden" name="ats_variation_attributes" value="">
		<button type="submit" name="add-to-subscription" disabled="disabled" value="<?php echo esc_attr( $product->get_id() ); ?>" class="button alt">
			<?php echo esc_html_x( 'Change kit', 'Text on button for add to existing subscription functionality', 'jg-toolbox' ); ?>
		</button>
	</form>
</div>
<div class="yv-add-to-subscription-separator"></div>
