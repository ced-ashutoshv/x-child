<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see       https://docs.woocommerce.com/document/template-structure/
 * @author    WooThemes
 * @package   WooCommerce/Templates
 * @version     2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

wc_print_notices();

do_action( 'woocommerce_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, the user cannot checkout
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
  echo apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) );
  return;
}

?>

<h2 class="mb40px">Checkout</h2>

<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

  <div class="x-column x-sm x-1-2 your-order mb50px">
    <h3 id="order_review_heading"><?php _e( 'Your order', 'woocommerce' ); ?></h3>

    <?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

    <div id="order_review" class="woocommerce-checkout-review-order">
      <?php //do_action( 'woocommerce_checkout_order_review' ); ?>
      <?php 
        //do_action( 'woocommerce_before_checkout_form_coupon_form_only' );
        //do_action( 'woocommerce_checkout_order_review_review_only' );
        do_action( 'woocommerce_checkout_order_review_review_only' );
       ?>
    </div>

    <?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
  </div>
  
  <!-- SEPARATOR -->
  
  <div class="x-column x-sm x-1-2 billing-details-credit-card" style="z-index: 2;">
    <?php if ( $checkout->get_checkout_fields() ) : ?>

      <?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>
    
    <?php 
    
  /*Custom buttons for paying with amazon and paypal : id tarea paymo: 14442555

  Queremos ocultar los botones por defecto para el payment de Amazon y de Paypal, y crear nuestros propios botones.
  Ocutlamos por css los que vienen por defecto, y agregamos aca los nuestros propios. 
  En el click de cada uno, simulamos el click del boton original. 
  en sass/_checkout.scss y _sass/cart.scss estan los estilos que ocultan los botones originales.

  */
    
	/* Remove afterpay option if a variable subscription is present in the cart */
	    
	$showAfterPay=true;
	foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
		$productChecked = $cart_item['data'];
		if(!empty($productChecked->variation_id)){$showAfterPay=false; $reason="Afterpay payment method is only available for one time purchases";}
	}
	global $woocommerce;
	if (!empty( $woocommerce->cart->applied_coupons ) ) {$showAfterPay= false; $reason="Afterpay payment method is not available when using coupons";}     
	/* End of Remove afterpay logic. If sentence below this line if($showAfterPay) */

	//$current_user = wp_get_current_user();
	//$userMail=$current_user->user_email;
	//if((strrpos($userMail, "afterpay.com.au") > 0  || strrpos($userMail, "ameba.com.uy") > 0) && $current_user->ID > 0 ){}else{$showAfterPay= false;}
	$showAmazon=true;
	?>
		<div id="toHide">
			<h3>Paying options</h3>
			<div class="method selected authorizePayBtn">
				<img  src="https://www.youveda.com/wp-content/uploads/2018/11/credit-card-youveda.png" style="width: 27px;float: left;margin-right: 5px;"> Credit Card
		    </div>
		    <!-- Custom Button for paying with Paypal -->
		    <div class="method payPalBtn" id="custom-paypal-payment-button">
		    <!--svg xmlns="http://www.w3.org/2000/svg" width="24" height="32" viewBox="0 0 24 32" preserveAspectRatio="xMinYMin meet">
		  <path fill="#009cde" d="M 20.905 9.5 C 21.185 7.4 20.905 6 19.782 4.7 C 18.564 3.3 16.411 2.6 13.697 2.6 L 5.739 2.6 C 5.271 2.6 4.71 3.1 4.615 3.6 L 1.339 25.8 C 1.339 26.2 1.62 26.7 2.088 26.7 L 6.956 26.7 L 6.675 28.9 C 6.581 29.3 6.862 29.6 7.236 29.6 L 11.356 29.6 C 11.825 29.6 12.292 29.3 12.386 28.8 L 12.386 28.5 L 13.228 23.3 L 13.228 23.1 C 13.322 22.6 13.79 22.2 14.258 22.2 L 14.821 22.2 C 18.845 22.2 21.935 20.5 22.871 15.5 C 23.339 13.4 23.153 11.7 22.029 10.5 C 21.748 10.1 21.279 9.8 20.905 9.5 L 20.905 9.5"/>
		  <path fill="#012169" d="M 20.905 9.5 C 21.185 7.4 20.905 6 19.782 4.7 C 18.564 3.3 16.411 2.6 13.697 2.6 L 5.739 2.6 C 5.271 2.6 4.71 3.1 4.615 3.6 L 1.339 25.8 C 1.339 26.2 1.62 26.7 2.088 26.7 L 6.956 26.7 L 8.267 18.4 L 8.173 18.7 C 8.267 18.1 8.735 17.7 9.296 17.7 L 11.636 17.7 C 16.224 17.7 19.782 15.7 20.905 10.1 C 20.812 9.8 20.905 9.7 20.905 9.5"/>
		  <path fill="#003087" d="M 9.485 9.5 C 9.577 9.2 9.765 8.9 10.046 8.7 C 10.232 8.7 10.326 8.6 10.513 8.6 L 16.692 8.6 C 17.442 8.6 18.189 8.7 18.753 8.8 C 18.939 8.8 19.127 8.8 19.314 8.9 C 19.501 9 19.688 9 19.782 9.1 C 19.875 9.1 19.968 9.1 20.063 9.1 C 20.343 9.2 20.624 9.4 20.905 9.5 C 21.185 7.4 20.905 6 19.782 4.6 C 18.658 3.2 16.506 2.6 13.79 2.6 L 5.739 2.6 C 5.271 2.6 4.71 3 4.615 3.6 L 1.339 25.8 C 1.339 26.2 1.62 26.7 2.088 26.7 L 6.956 26.7 L 8.267 18.4 L 9.485 9.5 Z"/>
		  </svg-->
		    <svg xmlns="http://www.w3.org/2000/svg" width="70" height="20" viewBox="0 0 100 20" preserveAspectRatio="xMinYMin meet"><path fill="#003087" d="M 12 4.917 L 4.2 4.917 C 3.7 4.917 3.2 5.317 3.1 5.817 L 0 25.817 C -0.1 26.217 0.2 26.517 0.6 26.517 L 4.3 26.517 C 4.8 26.517 5.3 26.117 5.4 25.617 L 6.2 20.217 C 6.3 19.717 6.7 19.317 7.3 19.317 L 9.8 19.317 C 14.9 19.317 17.9 16.817 18.7 11.917 C 19 9.817 18.7 8.117 17.7 6.917 C 16.6 5.617 14.6 4.917 12 4.917 Z M 12.9 12.217 C 12.5 15.017 10.3 15.017 8.3 15.017 L 7.1 15.017 L 7.9 9.817 C 7.9 9.517 8.2 9.317 8.5 9.317 L 9 9.317 C 10.4 9.317 11.7 9.317 12.4 10.117 C 12.9 10.517 13.1 11.217 12.9 12.217 Z"/><path fill="#003087" d="M 35.2 12.117 L 31.5 12.117 C 31.2 12.117 30.9 12.317 30.9 12.617 L 30.7 13.617 L 30.4 13.217 C 29.6 12.017 27.8 11.617 26 11.617 C 21.9 11.617 18.4 14.717 17.7 19.117 C 17.3 21.317 17.8 23.417 19.1 24.817 C 20.2 26.117 21.9 26.717 23.8 26.717 C 27.1 26.717 29 24.617 29 24.617 L 28.8 25.617 C 28.7 26.017 29 26.417 29.4 26.417 L 32.8 26.417 C 33.3 26.417 33.8 26.017 33.9 25.517 L 35.9 12.717 C 36 12.517 35.6 12.117 35.2 12.117 Z M 30.1 19.317 C 29.7 21.417 28.1 22.917 25.9 22.917 C 24.8 22.917 24 22.617 23.4 21.917 C 22.8 21.217 22.6 20.317 22.8 19.317 C 23.1 17.217 24.9 15.717 27 15.717 C 28.1 15.717 28.9 16.117 29.5 16.717 C 30 17.417 30.2 18.317 30.1 19.317 Z"/><path fill="#003087" d="M 55.1 12.117 L 51.4 12.117 C 51 12.117 50.7 12.317 50.5 12.617 L 45.3 20.217 L 43.1 12.917 C 43 12.417 42.5 12.117 42.1 12.117 L 38.4 12.117 C 38 12.117 37.6 12.517 37.8 13.017 L 41.9 25.117 L 38 30.517 C 37.7 30.917 38 31.517 38.5 31.517 L 42.2 31.517 C 42.6 31.517 42.9 31.317 43.1 31.017 L 55.6 13.017 C 55.9 12.717 55.6 12.117 55.1 12.117 Z"/><path fill="#009cde" d="M 67.5 4.917 L 59.7 4.917 C 59.2 4.917 58.7 5.317 58.6 5.817 L 55.5 25.717 C 55.4 26.117 55.7 26.417 56.1 26.417 L 60.1 26.417 C 60.5 26.417 60.8 26.117 60.8 25.817 L 61.7 20.117 C 61.8 19.617 62.2 19.217 62.8 19.217 L 65.3 19.217 C 70.4 19.217 73.4 16.717 74.2 11.817 C 74.5 9.717 74.2 8.017 73.2 6.817 C 72 5.617 70.1 4.917 67.5 4.917 Z M 68.4 12.217 C 68 15.017 65.8 15.017 63.8 15.017 L 62.6 15.017 L 63.4 9.817 C 63.4 9.517 63.7 9.317 64 9.317 L 64.5 9.317 C 65.9 9.317 67.2 9.317 67.9 10.117 C 68.4 10.517 68.5 11.217 68.4 12.217 Z"/><path fill="#009cde" d="M 90.7 12.117 L 87 12.117 C 86.7 12.117 86.4 12.317 86.4 12.617 L 86.2 13.617 L 85.9 13.217 C 85.1 12.017 83.3 11.617 81.5 11.617 C 77.4 11.617 73.9 14.717 73.2 19.117 C 72.8 21.317 73.3 23.417 74.6 24.817 C 75.7 26.117 77.4 26.717 79.3 26.717 C 82.6 26.717 84.5 24.617 84.5 24.617 L 84.3 25.617 C 84.2 26.017 84.5 26.417 84.9 26.417 L 88.3 26.417 C 88.8 26.417 89.3 26.017 89.4 25.517 L 91.4 12.717 C 91.4 12.517 91.1 12.117 90.7 12.117 Z M 85.5 19.317 C 85.1 21.417 83.5 22.917 81.3 22.917 C 80.2 22.917 79.4 22.617 78.8 21.917 C 78.2 21.217 78 20.317 78.2 19.317 C 78.5 17.217 80.3 15.717 82.4 15.717 C 83.5 15.717 84.3 16.117 84.9 16.717 C 85.5 17.417 85.7 18.317 85.5 19.317 Z"/><path fill="#009cde" d="M 95.1 5.417 L 91.9 25.717 C 91.8 26.117 92.1 26.417 92.5 26.417 L 95.7 26.417 C 96.2 26.417 96.7 26.017 96.8 25.517 L 100 5.617 C 100.1 5.217 99.8 4.917 99.4 4.917 L 95.8 4.917 C 95.4 4.917 95.2 5.117 95.1 5.417 Z"/></svg>
		    </div>
			
			<?php if($showAmazon){ ?>
		
		    <!-- Custom Button for paying with Amazon -->
		    <div class="method amazonPayBtn" style="width: 122px;" id="custom-amazon-payment-button">
			    <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
			  viewBox="0 0 176.5 33.9" style="enable-background:new 0 0 176.5 33.9;" xml:space="preserve">
			  <path class="st0" d="M69.7,26.5c-6.5,4.8-16,7.4-24.1,7.4c-11.4,0-21.7-4.2-29.5-11.2c-0.6-0.6-0.1-1.3,0.7-0.9
			  c8.4,4.9,18.8,7.8,29.5,7.8c7.2,0,15.2-1.5,22.5-4.6C69.8,24.5,70.7,25.7,69.7,26.5z"/>
			  <path class="st0" d="M72.4,23.4c-0.8-1.1-5.5-0.5-7.6-0.3c-0.6,0.1-0.7-0.5-0.2-0.9c3.7-2.6,9.9-1.9,10.6-1c0.7,0.9-0.2,7-3.7,9.9
			  c-0.5,0.5-1.1,0.2-0.8-0.4C71.4,28.9,73.2,24.5,72.4,23.4z"/>
			  <path class="st0" d="M64.9,3.7V1.2c0-0.4,0.3-0.6,0.6-0.6l11.4,0c0.4,0,0.7,0.3,0.7,0.6v2.2c0,0.4-0.3,0.8-0.9,1.6l-5.9,8.4
			  c2.2-0.1,4.5,0.3,6.5,1.4c0.4,0.3,0.6,0.6,0.6,1v2.7c0,0.4-0.4,0.8-0.8,0.6c-3.5-1.8-8.2-2-12.1,0c-0.4,0.2-0.8-0.2-0.8-0.6V16
			  c0-0.4,0-1.1,0.4-1.8l6.9-9.8l-6,0C65.2,4.4,64.9,4.1,64.9,3.7z"/>
			  <path class="st0" d="M23.2,19.6h-3.5c-0.3,0-0.6-0.3-0.6-0.6l0-17.8c0-0.4,0.3-0.6,0.7-0.6l3.2,0c0.3,0,0.6,0.3,0.6,0.6v2.3h0.1
			  c0.8-2.3,2.4-3.3,4.6-3.3c2.2,0,3.5,1.1,4.5,3.3c0.8-2.3,2.8-3.3,4.8-3.3c1.5,0,3,0.6,4,2c1.1,1.5,0.9,3.7,0.9,5.6l0,11.3
			  c0,0.4-0.3,0.6-0.7,0.6h-3.5c-0.4,0-0.6-0.3-0.6-0.6l0-9.5c0-0.8,0.1-2.6-0.1-3.3c-0.3-1.2-1-1.5-2-1.5c-0.8,0-1.7,0.6-2.1,1.5
			  c-0.4,0.9-0.3,2.4-0.3,3.4V19c0,0.4-0.3,0.6-0.7,0.6h-3.5c-0.3,0-0.6-0.3-0.6-0.6l0-9.5c0-2,0.3-4.9-2.1-4.9
			  c-2.5,0-2.4,2.9-2.4,4.9l0,9.5C23.9,19.4,23.6,19.6,23.2,19.6z"/>
			  <path class="st0" d="M87.5,3.8c-2.6,0-2.7,3.5-2.7,5.7s0,6.8,2.7,6.8c2.7,0,2.8-3.8,2.8-6c0-1.5-0.1-3.3-0.5-4.7
			  C89.4,4.3,88.6,3.8,87.5,3.8z M87.5,0.2c5.2,0,7.9,4.4,7.9,10.1c0,5.4-3.1,9.8-7.9,9.8c-5.1,0-7.8-4.4-7.8-9.9
			  C79.6,4.5,82.4,0.2,87.5,0.2z"/>
			  <path class="st0" d="M102.1,19.6h-3.5c-0.3,0-0.6-0.3-0.6-0.6l0-17.8c0-0.3,0.3-0.6,0.7-0.6l3.2,0c0.3,0,0.6,0.2,0.6,0.5v2.7h0.1
			  c1-2.4,2.3-3.6,4.7-3.6c1.6,0,3.1,0.6,4.1,2.1c0.9,1.4,0.9,3.8,0.9,5.6v11.2c0,0.3-0.3,0.6-0.7,0.6h-3.5c-0.3,0-0.6-0.3-0.6-0.6
			  V9.4c0-2,0.2-4.8-2.2-4.8c-0.8,0-1.6,0.6-2,1.4c-0.5,1.1-0.6,2.2-0.6,3.4V19C102.8,19.4,102.5,19.6,102.1,19.6z"/>
			  <path class="st0" d="M159.8,26c0-0.5,0-0.9,0-1.3c0-0.4,0.2-0.6,0.6-0.6c0.7,0.1,1.8,0.2,2.5,0.1c1-0.2,1.6-0.9,2-1.8
			  c0.6-1.3,0.9-2.4,1.2-3l-7.2-17.9c-0.1-0.3-0.2-0.9,0.4-0.9h2.5c0.5,0,0.7,0.3,0.8,0.6l5.2,14.5l5-14.5c0.1-0.3,0.3-0.6,0.8-0.6
			  h2.4c0.6,0,0.6,0.6,0.4,0.9l-7.2,18.5c-0.9,2.5-2.2,6.4-4.9,7.1c-1.4,0.4-3.2,0.2-4.2-0.2C159.9,26.6,159.8,26.3,159.8,26z"/>
			  <path class="st0" d="M156.4,18.5c0,0.3-0.3,0.6-0.6,0.6H154c-0.4,0-0.6-0.3-0.7-0.6l-0.2-1.2c-0.8,0.7-1.8,1.3-2.9,1.7
			  c-2.1,0.8-4.5,0.9-6.6-0.3c-1.5-0.9-2.3-2.7-2.3-4.5c0-1.4,0.4-2.8,1.4-3.8c1.3-1.4,3.2-2.1,5.4-2.1c1.4,0,3.3,0.2,4.7,0.6V6.5
			  c0-2.5-1-3.6-3.8-3.6c-2.1,0-3.7,0.3-6,1c-0.4,0-0.6-0.3-0.6-0.6V2c0-0.3,0.3-0.7,0.6-0.8c1.6-0.7,3.9-1.1,6.3-1.2
			  c3.1,0,6.9,0.7,6.9,5.5V18.5z M153,15v-3.7c-1.2-0.3-3.2-0.5-3.9-0.5c-1.2,0-2.5,0.3-3.2,1c-0.5,0.5-0.8,1.3-0.8,2.1
			  c0,1,0.3,2,1.1,2.4c0.9,0.6,2.3,0.5,3.7,0.2C151.2,16.2,152.4,15.6,153,15z"/>
			  <path class="st0" d="M131.1,0c-2.1,0-4.3,1-6,2.5l-0.2-1.3c0-0.3-0.3-0.6-0.7-0.6h-1.8c-0.3,0-0.6,0.3-0.6,0.6c0,8.3,0,16.6,0,24.9
			  c0,0.3,0.3,0.6,0.6,0.6h2.4c0.3,0,0.6-0.3,0.6-0.6v-8.6c1.5,1.4,3.5,2.1,5.6,2.1c5,0,8-4.3,8-9.7C138.9,4.9,137,0,131.1,0z
			  M133.5,15.4c-0.9,0.9-2,1.2-3.5,1.2c-1.4,0-3.2-0.7-4.6-1.7V4.7c1.4-1.1,3.2-1.7,4.8-1.7c4,0,5,3.1,5,6.7
			  C135.2,12.1,134.7,14.2,133.5,15.4z"/>
			  <path class="st0" d="M16.4,16.4c-0.6-0.9-1.3-1.6-1.3-3.2V7.7c0-2.3,0.2-4.4-1.5-6C12.3,0.5,10.1,0.1,8.5,0H8.2
			  c-3.3,0-6.9,1.3-7.6,5.3C0.5,5.8,0.8,6,1.1,6.1l3.4,0.4c0.3,0,0.5-0.3,0.6-0.6c0.3-1.4,1.5-2.1,2.8-2.1c0.7,0,1.5,0.3,2,0.9
			  c0.5,0.7,0.4,1.7,0.4,2.5v0.5C8.2,7.8,5.6,8,3.7,8.8C1.5,9.7,0,11.7,0,14.5c0,3.6,2.3,5.4,5.2,5.4c2.5,0,3.8-0.6,5.7-2.5
			  c0.6,0.9,0.8,1.4,2,2.3c0.3,0.1,0.6,0.1,0.8-0.1l0,0c0.7-0.6,2-1.7,2.7-2.3C16.7,17.1,16.6,16.7,16.4,16.4z M9.6,14.8
			  c-0.6,1-1.4,1.6-2.4,1.6c-1.3,0-2.1-1-2.1-2.5c0-3,2.7-3.5,5.2-3.5v0.8C10.2,12.5,10.3,13.6,9.6,14.8z"/>
			  <path class="st0" d="M62,16.4c-0.6-0.9-1.3-1.6-1.3-3.2V7.7c0-2.3,0.2-4.4-1.5-6C57.8,0.5,55.7,0.1,54,0h-0.3
			  c-3.3,0-6.9,1.3-7.6,5.3c-0.1,0.4,0.2,0.7,0.5,0.7L50,6.4c0.3,0,0.5-0.3,0.6-0.6c0.3-1.4,1.5-2.1,2.8-2.1c0.7,0,1.5,0.3,2,0.9
			  c0.5,0.7,0.4,1.7,0.4,2.5v0.5c-2,0.2-4.6,0.4-6.5,1.2c-2.2,0.9-3.7,2.9-3.7,5.7c0,3.6,2.3,5.4,5.2,5.4c2.5,0,3.8-0.6,5.7-2.5
			  c0.6,0.9,0.8,1.4,2,2.3c0.3,0.1,0.6,0.1,0.8-0.1l0,0c0.7-0.6,2-1.7,2.7-2.3C62.3,17.1,62.2,16.7,62,16.4z M55.1,14.8
			  c-0.6,1-1.4,1.6-2.4,1.6c-1.3,0-2.1-1-2.1-2.5c0-3,2.7-3.5,5.2-3.5v0.8C55.8,12.5,55.8,13.6,55.1,14.8z"/>
			  </svg>
			</div>
        <?php } ?>  
		<?php if($showAfterPay){ ?>
			<div class="method afterPayBtn"><?php echo do_shortcode('[afterpay_product_logo theme=colour]') ?></div>
		<?php } else{ ?>
			<span class="youveda-tooltip" style="display:inline-block"><div class="method title" style="opacity:0.2; cursor: not-allowed;"><?php echo do_shortcode('[afterpay_product_logo theme=colour]') ?></div><span class="description"><?php echo $reason ?></span></span>
		<?php } ?>  
			<hr>
		</div><!-- fin toHide -->

		<div class="col2-set" id="customer_details" style="display:none">
			<div class="col-12">
			<?php do_action( 'woocommerce_checkout_billing' ); ?>
			</div>
			<div class="col-12">
				<?php do_action( 'woocommerce_checkout_shipping' ); ?>
			</div>
		</div>

		<div id="paymentMethods" style="display:none">
			<div class="payment_method_authorize_net_cim_credit_card"  style="display:none">
				<!-- (c) 2005, 2018. Authorize.Net is a registered trademark of CyberSource Corporation -->
				<div class="AuthorizeNetSeal"><script type="text/javascript" language="javascript">var ANS_customer_id="16570418-38de-4d68-b004-ebafb8f8adaf";</script> <script type="text/javascript" language="javascript" src="//verify.authorize.net:443/anetseal/seal.js" ></script></div>
		    </div>

		    <?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

		<?php endif; ?>
		<?php do_action( 'woocommerce_checkout_order_review_checkout_payment_only' ); ?>
		</div>
	</div>
</form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>

<script>
	function payPal(event){
		jQuery('.method.selected').removeClass('selected');
		jQuery(event.target).addClass('selected');
		jQuery('#payment_method_paypal').trigger( "click" );
		jQuery('.wc_payment_method, .payment_method_authorize_net_cim_credit_card').hide();
		jQuery('#customer_details, #paymentMethods').slideDown(500, function(){
			setTimeout(function(){ jQuery('#place_order').trigger('click'); },1);
            jQuery('#ship-to-different-address-checkbox').removeAttr('checked');
		});
	}
	function amazonPay(event){
		jQuery('.method.selected').removeClass('selected');
		jQuery(event.target).addClass('selected');
		jQuery('#pay_with_amazon img').click();
	}
	function authorizePay(event){
		if(event){jQuery('.method.selected').removeClass('selected');jQuery(event.target).addClass('selected');}
		jQuery('.wc_payment_method').hide();
		jQuery('#customer_details, #paymentMethods, .payment_method_authorize_net_cim_credit_card').slideDown(500, function(){
			jQuery('.payment_method_authorize_net_cim_credit_card input').click();
			jQuery('#ship-to-different-address-checkbox').removeAttr('checked');
		});
	}
	function afterPay(event){
		jQuery('.wc_payment_method').hide();
		jQuery('.method.selected').removeClass('selected');
		jQuery(event.target).addClass('selected');
		jQuery("html,body").animate({scrollTop: jQuery("#payment_method_afterpay").offset().top}, 500, function(){
			jQuery('#customer_details, .payment_method_authorize_net_cim_credit_card').slideUp(500);
            jQuery('#paymentMethods').slideDown(500, function(){
				jQuery('.payment_method_afterpay').slideDown(500,function(){
					jQuery('#payment_method_afterpay').click();
				});
			});
		});
	}

	document.addEventListener("DOMContentLoaded", function(){
		authorizePay();
		<?php if(!$showAfterPay){?> 
			jQuery('.payment_method_afterpay').hide();
		<?php } ?>
		jQuery('.amazonPayBtn').on("click",amazonPay);
		jQuery('.payPalBtn').on("click",payPal);
		jQuery('.authorizePayBtn').on("click",authorizePay);
		jQuery('.afterPayBtn').on("click",afterPay);

		if(jQuery('#amazon_addressbook_widget').length>0){
			jQuery('.wc-amazon-checkout-message.wc-amazon-payments-advanced-populated').css({'position':'absolute','bottom':0,'display':'block','z-indez':100})
			jQuery('#customer_details, #toHide, .credit-card-title, .AuthorizeNetSeal').hide();
		}
	});
  
    </script>
    
<style>
input#payment_method_afterpay{display:none}
.method{transition:all 0.3s;border: 2px solid #fad676;display: inline-block;background: #fad676!important;padding: 8px;margin: 5px;cursor: pointer;font-weight: bold;} 
.method.selected,.method:hover{background: #ffe9b0!important;border-color: #42a5f5;}
</style>
