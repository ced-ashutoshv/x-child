<?php
/**
 * Custom shipping section
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  Ameba
 * @package Youveda/WooCommerce/Templates
 * @version 3.0.9
 */

?>
<div class="row checkout-section-wrapper" id="delivery_details">
	<div class="col-12">
		<hr class="hr-separator">
	</div>
	<div class="col-12">
		<div class="row">
			<div class="col-10 checkout-section-title-collapsable">
				<?php
					/* translators: %s: Billing / Shipping Address */
					echo wp_kses_post( sprintf( '<h4 class="">%s</h4>', __( 'Delivery options', 'woocommerce' ) ) );
				?>
			</div>
			<div class="col-2">
				<a href="#" class="edit-checkout-section toggle-element collapsable collapsed default-hidden">Edit</a>
			</div>
		</div>
	</div>
	<div class="col-12 collapsable collapsed">
		<div>
			<div class="country">
				<span class="flag"></span>
				<span class="country-name"></span>
			</div>			
		</div>
		<?php 
		do_action( 'yv_woocommerce_after_checkout_delivery_section');
		?>
	</div>
</div>
