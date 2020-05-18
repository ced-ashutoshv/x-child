<?php
/**
 * The template for displaying match messages for the bundle section on shop page.
 *
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;
?>
<div data-matched-ids="<?php echo esc_attr( $combination['ids'] ); ?>">
	<i class="x-icon" data-x-icon="&#xf004;"></i>
	<h6 class="choice-title fontFamilyPrimary">
		<?php echo esc_html( $combination['title'] ); ?>
	</h6>
	<p>
		<?php echo esc_html( $combination['message'] ); ?>		
	</p>
</div>
