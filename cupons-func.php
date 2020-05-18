
function mysite_box_discount( $cart) {
  
    global $woocommerce;
 
    /* Grab current total number of products */
    $number_products = $cart->cart_contents_count;
	echo '<hr><pre>';
	//var_dump($cart);
    echo '</pre><hr>';
    $total_discount = 0;
    $my_increment = 15; // Apply another discount every 15 products
    $discount = 8.85;
	
	echo '<pre>';
	$cart=$woocommerce->session->cart;
	var_dump($cart);
	echo '</pre>';
    
    /*if ($number_products >= $my_increment) {
        
      // Loop through the total number of products
      foreach ( range(0, $number_cards, $my_increment) as $val ) {
        if ($val != 0) {
      		$total_discount += $discount;
      	}
      }*/
   
      // Alter the cart discount total
      $cart->discount_total = '50%';
    //}   
}
//add_action('woocommerce_calculate_totals', 'mysite_box_discount');


/* Mod: Remove 10% Discount for weight less than or equal to 100 lbs */
//add_action('woocommerce_before_cart_table', 'remove_coupon_if_weight_100_or_less');
function remove_coupon_if_weight_100_or_less( ) {
    global $woocommerce;
    global $total_weight;
    /*if( $total_weight <= 100 ) {
        $coupon_code = 'miki99';
        $woocommerce->cart->get_applied_coupons();
        if (!$woocommerce->cart->remove_coupons( sanitize_text_field( $coupon_code ))) {
            $woocommerce->show_messages();
        }
        $woocommerce->cart->calculate_totals();
    }*/
	
	//var_dump($woocommerce['applied_coupons']);
	//var_dump($applied_coupons);
	//
	echo '<pre><hr>';
	$cart=$woocommerce->session->cart;
	//var_dump($cart);
	echo '</pre><hr>';
	$cart_total = 0;
			foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $cart_item ) {

			 echo '<pre>';
	         var_dump($cart_item["line_subtotal"]);
	         var_dump($cart_item["line_total"]);
	         var_dump($cart_item["product_id"]);
	         $title=(get_the_title( $cart_item['product_id']) );
			 var_dump($title);	
			 var_dump(get_post_meta( $cart_item["product_id"], '_regular_price', true));
			 var_dump(strpos($title, 'Subscription' ));	
			if(strpos($title, 'Subscription' ) != false){
				echo 'Never ending subscription';
				$prod_price=get_post_meta( $cart_item["product_id"], '_regular_price', true);
				
			}else{
				$prod_price=get_post_meta( $cart_item["product_id"], '_regular_price', true);
			}
	         echo '</pre>';

			 $cart_total += $prod_price * $cart_item['quantity'];
			} 
	$discount = round( ( $cart_total / 100 ) * $coupon->amount, $woocommerce->cart->dp );
	 var_dump($cart_total);	 var_dump($discount);	
	
	return $discount;
	
	
	
	$products= wp_list_pluck( $cart, 'product_id');
	echo '<pre>';
	//var_dump($products);
	echo '</pre>';
	
	echo '<pre>';
	$coupon_name_applied=$woocommerce->session->applied_coupons;
	
	foreach($coupon_name_applied as $coupon_app){
		var_dump($coupon_app);
	}
	
	echo '</pre>';
	
	

	echo '<pre>';
	//var_dump($woocommerce);
	echo '</pre>';
}

function add_extra_discount( $cart ) {
	  global $woocommerce;
	  global $coupon;
	echo '<pre>';
	$coupon_name_applied=$woocommerce->session->applied_coupons;
	
	foreach($coupon_name_applied as $coupon_app){
		var_dump($coupon_app);
	}
	
	echo '</pre>';
if ($coupon_name_applied[0] == 'miki') {
			$cart_total = 0;
		
			foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $cart_item ) {
			 $prod_price=get_post_meta( $cart_item["product_id"], '_regular_price', true);
				
			 echo '<pre>';
	         var_dump($cart_item["line_total"]);
			 var_dump($cart_item["product_id"]);
			 var_dump($prod_price);
	         echo '</pre>';
             
			 $cart_total += $prod_price  * $cart_item['quantity'];
			 var_dump($cart_total);
			} 
			 var_dump($coupon->amount);
			 var_dump($woocommerce->cart->dp);
			$discount = round( ( $cart_total / 100 ) * $coupon->amount, $woocommerce->cart->dp );

			var_dump($cart_total);
			var_dump($discount);
			var_dump(5555555);
			
			$woocommerce->cart->discount_total = $discount;
			
			// $discount;
		}
  //$discount = $cart->subtotal * 0.35;
  //$total = $cart->subtotal * 0.35;

  //$cart->add_fee( __( 'Extra Discount', 'twentyseventeen' ) , -$discount );

}
//add_action( 'woocommerce_cart_calculate_fees', 'add_extra_discount',10,2 );

//add_filter('woocommerce_coupon_get_discount_amount', 'woocommerce_coupon_get_discount_amount', 10, 5 );
	function woocommerce_coupon_get_discount_amount( $discount, $discounting_amount, $cart_item, $single, $coupon ) {
		if ($coupon->type == 'percent_product' || $coupon->type == 'percent') {
			global $woocommerce;
			$cart_total = 0;
		
			foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $cart_item ) {
			 $prod_price=get_post_meta( $cart_item["product_id"], '_regular_price', true);
				
			 echo '<pre>';
	         var_dump($cart_item["line_total"]);
			 var_dump($cart_item["product_id"]);
			 var_dump($prod_price);
	         echo '</pre>';
             
			 $cart_total += $prod_price  * $cart_item['quantity'];
			 var_dump($cart_total);
			} 
			 var_dump($coupon->amount);
			 var_dump($woocommerce->cart->dp);
			$discount = round( ( $cart_total / 100 ) * $coupon->amount, $woocommerce->cart->dp );

			var_dump($cart_total);
			var_dump($discount);
			var_dump(5555555);
			
			$woocommerce->cart->discount_total = $discount;
			
			return $discount;
		}
		
		$woocommerce->cart->discount_total = $discount;
		return $discount;
		

	}
