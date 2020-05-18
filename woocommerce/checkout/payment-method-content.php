<?php
/**
 * Output a single payment method related content
 *
 * The WooCommerce payment-method template has been splited in 2 templates
 * One for the button/option and the second for the option content itself
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce/Templates
 * @version     3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="payment_box payment_method_<?php echo esc_attr( $gateway->id ); ?>" <?php if ( ! $gateway->chosen ) : /* phpcs:ignore Squiz.ControlStructures.ControlSignature.NewlineAfterOpenBrace */ ?>style="display:none;"<?php endif; /* phpcs:ignore Squiz.ControlStructures.ControlSignature.NewlineAfterOpenBrace */ ?>>
	<?php
	if ( $gateway->has_fields() || $gateway->get_description() ) {
			$gateway->payment_fields();

	} else {
		do_action( 'yv_payment_gategay_content' . $gateway->id, $gateway );
	}
	?>
	</div>
<?php
