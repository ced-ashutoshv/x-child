<?php
/**
 * Save checkout section button
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  Ameba
 * @package Youveda/WooCommerce/Templates
 * @version 3.0.9
 */

if( 'delivery_details' === $section_id ) {
	echo '<ul id="shipping_method" class="woocommerce-shipping-methods"><li class="loading-text"> Loading shipping options...</li></ul>';
} ?>
<a 	href='#' 
	class='button save-checkout-section' 
	aria-label='Save <?php echo esc_attr( $btn_text ); ?>' 
	rel='nofollow'
	data-yv-validate-section="<?php echo esc_attr( $section_id ) ?>">
	Save <?php echo esc_html( $btn_text ); ?>
</a>
