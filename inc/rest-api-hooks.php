<?php 

//add_action( 'password_reset', 'confirm_user_shopify', 10, 2 );
add_action('wppb_edit_profile_success', 'confirm_user_shopify', 10,3);
if (!function_exists('confirm_user_shopify')){
function confirm_user_shopify( $reqst, $form_name, $user_id ) {
    $userData = get_user_by( 'ID', $user_id );
    if('account-confirmation' == $form_name){
        //send account confirmation email 
    }

	$userConfirm = get_user_meta( $user_id, '_registration_confirm_', true );
	if(empty($userConfirm) || $userConfirm != 'yes'){
		
        $customer_result = spfy_search_customer_by_field($userData->user_email);
        $customerID = $customer_result['customers'][0]['id'];

        $orders_result = spfy_get_orders_by_field($customerID);

		$orders = $orders_result['orders'];

		//get orders IDs
		$spfy_purchased_product_IDs = array();
		$ordersTimestamps = array();
        $ordersIds = array();
        $orderType = array();
        $products = array(); 
		if(!empty($orders)){

			foreach ($orders as $order) {
				$created_at = $order['created_at'];
                $orderTime = getShopifyParams('time', $created_at);
                
                $orderID   = $order['id'];
                $subs_type = strpos( $order['tags'] , 'recurring_order') === false ? 'single' : 'active';
                
				foreach ($order['line_items'] as $products_in_order) {
					
					if(!in_array($products_in_order['product_id'], $spfy_purchased_product_IDs)){
						array_push($spfy_purchased_product_IDs, $products_in_order['product_id']);

						$spfy_product_id                   = "".$products_in_order['product_id']."";
						$ordersTimestamps[$spfy_product_id]= $orderTime;
                        $ordersIds[$spfy_product_id]       = $orderID;
                        $orderType[$spfy_product_id]       = $subs_type;
					}
				}
			}

			$args = array(
				'meta_query' => array(
						array(
							'key'     => '_youveda-product-mb_shopify_id',
							'value'   => $spfy_purchased_product_IDs,
							'compare' => 'IN',
						),
						
					),
				'post_type'        => 'product'
			);
			$products = get_posts( $args );
		}

		

		
		if(!empty($customerID) && !empty($orders)){
            //$productsNEW_array = array();
    		foreach ($products as $prod) {
    			$thisShopifyID = get_post_meta( $prod->ID, '_youveda-product-mb_shopify_id', false );
    			$thisShopifyID = (!empty($thisShopifyID))? $thisShopifyID[0] : false;
    			$newOrder = array(
    				'shopify_product_id'        => $prod->ID,
    				'shopify_product_timestamp' => $ordersTimestamps[$thisShopifyID],
                    'shopify_order_id'          => $ordersIds[$thisShopifyID],
                    'status'   => $orderType[$thisShopifyID], 
    			);
    			yv_add_single_subscription( $user_id, array($prod->ID), $ordersIds[$thisShopifyID], $orderType[$thisShopifyID], $ordersTimestamps[$thisShopifyID]);
                //array_push($productsNEW_array, $newOrder);
    		}
            /*
            yv_add_single_subscription($userID, $productsID_array, $orderID, $subs_type, $orderTime)
                $productsID_array = array( rgar( $entry, '1' ) );
                $orderTime = current_time( 'timestamp' );
                yv_add_single_subscription( rgar( $entry, 'created_by' ) , $productsID_array, '', 'single', $orderTime);
                yv_add_single_subscription( $user_id , $productsID_array, '', 'single', $orderTime);*/
			//$updatedOrder = update_user_meta( $user_id, '_youveda_user_subscriptions', $productsNEW_array );
		}else{
			$admin_email = get_bloginfo('admin_email');
			$headers = 'From: Youveda <'.$admin_email.'>' . "\r\n";
			$messagge = "The user ".$userData->user_email." created an account without shopify orders";
			$sendEmail = wp_mail( $admin_email, 'User Orders Error', $messagge, $headers );
		}
		
		update_user_meta( $user_id, '_registration_confirm_', 'yes' );

	}

}}

if(!function_exists('yv_apiCall')) {
    function yv_apiCall($url, $httpHeaders = false, $method = 'GET', $body = false, $recursive = false, $from_cache = false) {
        //max execution time in seconds
        $timeout     = 30;
        $cached_data = false;
        $env         = (defined('ENVIRONMENT')) ? ENVIRONMENT : 'DEV-SITE';

        if($from_cache) {
            $pos         = strrpos($url, 'user/') > 0 ? strrpos($url , 'user/') + 5 : strrpos($url , 'company/') + 8;
            $cache_key   = substr($url, $pos);
            //save in cache for 12 hours
            $expiration  = 60*60*12;
            $cached_data = get_transient($cache_key);
        }

        if($cached_data === false) {
            $ssl = true;
            
            //Auth headers if needed
            //$headers = array('Authorization' => 'Basic ' . base64_encode("$username:$password"));

            if($httpHeaders) {
                $headers = array_merge(array(), $httpHeaders);
            }
            $args = array('headers' => $headers, 'timeout' => $timeout, 'sslverify'=>$ssl);
            if(!empty($body)) {
                    $args['body'] = json_encode($body);
            }
            
            if($method == 'POST') {
                $response = wp_remote_post($url, $args );
            } else if($method == 'GET'){
                $response = wp_remote_get($url, $args );
            }else{
                $args['method'] = $method;
                $response = wp_remote_request($url, $args);
            }

            if(!is_wp_error($response)) {
                $response_to_array = json_decode($response['body'], true);
            } else {
                $response_to_array['Error']=true;
            }
            //return json_decode($response['body'], true);
               //if wp error             if response code error                                         
            if( is_wp_error($response) || $response_to_array['Error'] != false )  {
                if($recursive) {
                    yv_apiCall($url, $httpHeaders, $method, $body, false);
                } else {
                    if(is_wp_error($response)) {
                        $log_msj = $response->get_error_message();
                        $log_msj .= ' Url: ' . $url;
                        //log_error_and_notify($log_msj, 'yv_apiCall', __LINE__);
                        error_log(($log_msj. 'yv_apiCall' . __LINE__));
                    } else {
                        $log_msj  = " Url:".$url;
                        $log_msj .= " StatusCode:".$response_to_array['StatusCode'];
                        $log_msj .= " Message:".$response_to_array['Message'];
                        if(!empty($body)) {
                            $log_msj .= "\n".$body."\n".json_encode($body);
                        }
                        //$response['headers']['status_code'] != 200;
                        //error_log((print_r($log_msj, true). 'yv_apiCall' . __LINE__));
                    }
                    //return show_api_error_to_user($log_msj);
                    $response_to_array['Error']=true;
                    return $response_to_array;
                }
            } else {
                if($from_cache) {
                    set_transient($cache_key, $response['body'], $expiration);
                }

                return $response_to_array;
            }
        } else {
            return json_encode($cached_data, true);
        }
    }
}
/*shopify API related*/
if(!function_exists('get_spfy_RESTAPI_URL')) {
    function get_spfy_RESTAPI_URL(){
        return "https://949971c1ccf12c0bbcf400b49a078d23:9b7fe314633ac7b75d94e052cee929c7@www-youveda-com.myshopify.com/admin";
    }
}

if(!function_exists('spfy_search_customer_by_field')) {
    function spfy_search_customer_by_field($value, $field = 'email'){
        $get_url = get_spfy_RESTAPI_URL() . '/customers/search.json';
        $url = add_query_arg( 'query', $field.':'.$value, $get_url );
        return yv_apiCall($url);
    }
}

if(!function_exists('spfy_get_orders_by_field')) {
    function spfy_get_orders_by_field($value, $field = 'customer_id'){
        $get_url = get_spfy_RESTAPI_URL() . '/orders.json?';
        $url = add_query_arg( array(
                    $field => $value,
                    'order' => 'processed_at+asc',
                ), $get_url );
        return yv_apiCall($url);
    }
}

/*iOS API related*/
if(!function_exists('get_iOS_RESTAPI_URL')) {
    function get_iOS_RESTAPI_URL(){
        return "http://youvedabackend-dev-env.us-east-1.elasticbeanstalk.com/api/user";
    }
}
if(!function_exists('get_iOS_auth_header')) {
    function get_iOS_auth_header(){
        $username = 'api@youveda.com';
        $password = '9b7fe314633ac7b75d94e052cee929c7';
        $header = array('Authorization' => 'Basic ' . base64_encode("$username:$password"));
        return $header;
    }
}

if(!function_exists('yv_insert_user_to_iOS')){
    function yv_insert_user_to_iOS($userID, $fb_id){
        $get_url = get_iOS_RESTAPI_URL() ;
        $auth_header = get_iOS_auth_header();
        $auth_header['content-type'] = 'application/json';
        
        $user_info = get_userdata($userID);

        $data = array('users'=>array(
                    array(
                    "firstName" => $user_info->first_name,
            		"lastName"  => $user_info->last_name,
            		"email"     => $user_info->user_email,
            		"facebook"  => $fb_id
                    )
        ));
        
     $api_response = yv_apiCall($get_url, $auth_header, 'POST', $data);
     $iOS_ID = isset($api_response['Data']['ID']) ? $api_response['Data']['ID'] : false;
     if( false === $api_response['Error'] && $iOS_ID && is_numeric(absint($iOS_ID)) ){
        update_user_meta($userID, '_yv_ios_id', $iOS_ID);
        yv_all_subscriptions_to_iOS($userID, $iOS_ID);
     }else{
        error_log('Unable to insert user '.$userID. ' Response: '.print_r($api_response, true). ' - Line: ' . __LINE__);
     }
    }
}


if(!function_exists('yv_all_subscriptions_to_iOS')){
    function yv_all_subscriptions_to_iOS($userID, $iOS_ID){
     
        $userOrders = get_user_meta( $userID, '_youveda_user_subscriptions', true);
        if(!empty($userOrders)){
            foreach($userOrders as $order){
                if($order['status'] == 'active' || $order['status'] == 'single'){
                    iOS_insert_single_subscription($userID, $iOS_ID, $order);       
                }
            }
        }
    }
}

if(!function_exists('iOS_insert_single_subscription')) {
    function iOS_insert_single_subscription($userID, $iOS_ID, $order){
        $get_url = get_iOS_RESTAPI_URL() .'/'.$iOS_ID.'/subscription' ;
        $auth_header = get_iOS_auth_header();
        $auth_header['content-type'] = 'application/json';

        // '$shopify_product_id' | 'shopify_product_timestamp' | 'shopify_order_id' | 'status'
        extract($order);
        
        $ios_program_id     = get_post_meta($shopify_product_id, '_youveda-product-mb_ios_app_program_id', true);
        $ios_sub_program_id = get_post_meta($shopify_product_id, '_youveda-product-mb_ios_app_sub_program_id', true);
        
        $data = array(
            'subprogramId'=> $ios_sub_program_id,
            'programId'=> $ios_program_id,
        );
        
        $api_response = yv_apiCall($get_url, $auth_header, 'POST', $data);
        if( true == $api_response['Error'] ){
            error_log('Unable to insert subscriptions '.$userID. ' Response: '.print_r($api_response, true). ' - Line: ' . __LINE__);
        }else{
            do_action("kit_activated_iOS", $userID, $shopify_product_id, $shopify_order_id, $status, $shopify_product_timestamp );
        }
        return;
    }
}

if(!function_exists('iOS_delete_single_subscription')) {
    function iOS_delete_single_subscription($user_id, $order){
        
        $iOS_userID = get_user_meta( $user_id, '_yv_ios_id', true);
		if(empty($iOS_userID)){
            return;
		}
        $post_id = $order['shopify_product_id'];
        $ios_program_id     = get_post_meta($post_id, '_youveda-product-mb_ios_app_program_id', true);
        
        $get_url = get_iOS_RESTAPI_URL() .'/'.$iOS_userID.'/subscription/'. $ios_program_id ;
        $auth_header = get_iOS_auth_header();
        $auth_header['content-type'] = 'application/json';

        
        
        $api_response = yv_apiCall($get_url, $auth_header, 'DELETE');
        if( true == $api_response['Error'] ){
            error_log('Unable to insert subscriptions '.$user_id. ' Response: '.print_r($api_response, true). ' - Line: ' . __LINE__);
        }
        return;

    }
}
if(!function_exists('pretty_debug')) {
    function pretty_debug($arr){
            echo "<pre>";
            print_r($arr);
            echo "</pre>";
    }
}

add_action( 'wp_enqueue_scripts', 'yv_profile_scripts', 20 );
if(!function_exists('yv_profile_scripts')) {
    function yv_profile_scripts(){
        
        if(is_user_logged_in()){
           
            wp_enqueue_script( 'bootstrap-js', get_stylesheet_directory_uri().'/js/bootstrap.min.js', array('jquery'), "3.3.7", true );
            wp_enqueue_script( 'bootbox-js', get_stylesheet_directory_uri().'/js/bootbox.min.js', array('jquery', 'bootstrap-js'), "4.4.0", true );
            
            $main_js_version = filemtime(get_stylesheet_directory() . '/js/profile.js');
            wp_register_script( 'profile-script', get_stylesheet_directory_uri().'/js/profile.js', array('jquery', 'bootbox-js'), $main_js_version, true );
            
            wp_enqueue_script( 'profile-script' );
            $params = array(
              'ajaxurl' => admin_url('admin-ajax.php'),
              'userID' => get_current_user_id(),
              'ajax_nonce' => wp_create_nonce('yv_cancel_subs'),
            );
            wp_localize_script( 'profile-script', 'yv_ajax_profile_object', $params );
            
            
        }
    }
}



if(!function_exists('yv_cancel_subscription')){
    function yv_cancel_subscription(){
          check_ajax_referer( 'yv_cancel_subs', 'security' );
          $response = array(
            'message' => "There was an error, please try again or email us to customerservice@youveda.com",
            'error'  => 'error');
          if(is_user_logged_in() && get_current_user_id() == sanitize_text_field( $_POST['userID'] )){

            $productId = sanitize_text_field( $_POST['productId'] ) ;
            $orderID   = sanitize_text_field( $_POST['orderId'] );
            $remove    = yv_remove_subscription(get_current_user_id(), $productId, $orderID);
            
            $response ['message'] = $remove['message'];
            $response ['error']   = $remove['error'];
                        
          }
          echo json_encode($response);
          die();
    }
}
add_action('wp_ajax_yv_cancel_subscription', 'yv_cancel_subscription');

if(!function_exists('yv_remove_subscription')){
    function yv_remove_subscription($userID, $productID, $orderID){
        $return = array(
            'message'=> "There was an error deleting your subscription, if the problem persists, please contact us at gunny@youveda.com",
            'error' => 'error'
        );
        if( empty($userID) || empty($productID) || empty($orderID)){
            $return['message'].="(Missing parameter)";
            return $return;
        }
        $user_info = get_userdata($userID);
        if(false == $user_info){
            return $return;
        }
        $product = get_post($productID);
		if(empty($product)){  
            $return['message'] = "Looks like you are trying to delete a non existing product.";
            return $return;
		}else{
			$productID = $product->ID;
		}

		$userOrders = get_user_meta( $userID, '_youveda_user_subscriptions', true);
		$orderExist = yv_user_has_order($userID, $orderID);
		if (false !== $orderExist){
		  $old_status = $userOrders[$orderExist]['status']; 
		  
          $userOrders[$orderExist]['status'] = 'active' == $old_status ? 'cancel' : 'expired';	
		} 
        
		if(false !== $orderExist){
            
            iOS_delete_single_subscription($userID, $userOrders[$orderExist]);
            
            sort_multidimensional_by_key($userOrders, 'shopify_product_timestamp');
            $updatedOrder = update_user_meta( $userID, '_youveda_user_subscriptions', $userOrders );
            //if real site and user had active subscription, send notification
            if( strpos( site_url() , 'local.') === false && 'active' == $old_status ) {
                // Send notificatoin to the site admin to run this manually                        
    			$admin_email = get_bloginfo('admin_email');
    			$message = "On ". current_time( 'm-d-Y h:i A' ) .", ".$user_info->user_email.' has deleted his subscription to '.$product->post_title."(".$productID.")<br>";
                $message.= "Next action is to cancel the order in the shop and unlink it from the WP user profile<br>";
                $message.= "(The user will receive an email notification after order cancelation in the shop)";
                $sendEmail = wp_mail( $admin_email, 'Order Deleted', $message );
    			$return['message'] = "Your subscription is being cancelled, you will receive an email confirmation soon.";
                $return['error'] = 'success';
            }else{
                $return['message'] = "Single subscription is cancelled.";
                $return['error'] = 'success';
            }
            return $return;
		}else{
			$return['message'] = "Looks like you are trying to delete a non existing product. Cod. 2";
            return $return;
		}       
    }
}

if(!function_exists('yv_add_single_subscription')){
    function yv_add_single_subscription($userID, $productsID_array, $orderID, $subs_type, $orderTime){
        
        $userOrders = get_user_meta( $userID, '_youveda_user_subscriptions', true);
        if(!empty($userOrders)){ 
			foreach ($userOrders as $index=>$order) {
				if( in_array($order['shopify_product_id'], $productsID_array) ){
					//if is a new recursive order and already has the subscription, keep the original one 
                    if($order['status'] == 'active'){
    					$elementPos = array_search($order['shopify_product_id'], $productsID_array);
    					unset($productsID_array[$elementPos]);   
					//if is a new recursive order and user has a single subscription, update data
                    }else if( $order['status'] == 'single' ){
                        if($order['shopify_order_id'] == $orderID || 'active' == $subs_type){
                            unset($userOrders[$index]); 
                        }
					//if the subscription state is not active, update data
                    }else if( $order['status'] == 'cancelled' || $order['status'] == 'expired'){
					   unset($userOrders[$index]);
					}
                    
				}
			}
		}else{
			$userOrders = array();
		}
        
        if(!empty($productsID_array)){
			$new_products_to_iOS = array();
            foreach ($productsID_array as $productID) {
				$newOrder = array(
					'shopify_product_id' => strval($productID),
					'shopify_product_timestamp' => $orderTime,
                    'shopify_order_id'   => $orderID, 
                    'status'   => $subs_type, 
				);
                $new_products_to_iOS[] = $newOrder;
				array_push($userOrders, $newOrder);
			}
            
            //reset keys
            $userOrders= array_values($userOrders);
            
            sort_multidimensional_by_key($userOrders, 'shopify_product_timestamp');
			$updatedOrder = update_user_meta( $userID, '_youveda_user_subscriptions', $userOrders );
            
            do_action("kit_activated_WP", $userID, $productsID_array, $orderID, $subs_type, $orderTime );
            
			$userFacebookID = get_user_meta( $userID, '_wppb_facebook_connect_id', true);
            $iOS_userID = get_user_meta( $userID, '_yv_ios_id', true);

			if(!empty($userFacebookID)){
			    
                if(!empty($iOS_userID)){
                    foreach ($new_products_to_iOS as $order){
                        iOS_insert_single_subscription($userID, $iOS_userID, $order);     
			        }
                }else{
                    yv_insert_user_to_iOS($userID, $userFacebookID);
                }
				return '[Order-Updated]: user has facebook id';
			}

			return '[Order-Updated]';

		}else{
			return "[Error]: Order already exists";
		}
        
    }
}
if(!function_exists('yv_user_has_order')){
    function yv_user_has_order($userID, $spfy_orderID){
        $orders = get_user_meta( $userID, '_youveda_user_subscriptions', true);
        return array_search($spfy_orderID, array_column($orders, 'shopify_order_id'));
    }
}

//Facebook login plugin - link facebook process
add_action( 'fbl/before_login', 'FL_insert_user_to_iOS',10,1);
if(!function_exists('FL_insert_user_to_iOS')){
    function FL_insert_user_to_iOS($user){
        $fb_id = $user['fb_user_id'];
        yv_insert_user_to_iOS(get_current_user_id(), $fb_id);
    }
}
//profile builder plugin - link facebook process
add_filter( 'wppb_sc_process_facebook_response', 'PB_insert_user_to_iOS', 100, 1 );
if(!function_exists('PB_insert_user_to_iOS')){
    function PB_insert_user_to_iOS($response){
        
        //first_name, last_name, email, id
        if( ! empty( $_POST['wppb_sc_security_token'] ) ) {
                $received_token = $_POST['wppb_sc_security_token'];
                if( ! isset( $_COOKIE['wppb_sc_security_token'] ) || wp_unslash( $_COOKIE['wppb_sc_security_token'] ) != urldecode( $received_token ) ) {
					return $response;
                }
            yv_insert_user_to_iOS(get_current_user_id(), $response['id']);
        }
        return $response;
    }
}



add_filter( 'http_response', 'wp_log_http_requests', 10, 3 );

if (!function_exists('wp_log_http_requests')){
    function wp_log_http_requests($response, $args, $url){
        if(!is_plugin_active('wp-rest-api-log/wp-rest-api-log.php')) {
            return $response;
        }
        if(strpos($url, 'youvedabackend') !== false || strpos($url, 'zapier.com') !== false) {
            $args = array(
				'ip_address'            => '',
				'http_x_forwarded_for'  => '',
				'route'                 => $url,
				'method'                => $args['method'],
				'status'                => $response['response']['code'],
				'request'               => array(
					'body'                 => $args['body'],
					'headers'              => $args['headers'],
					'query_params'         => '',
					'body_params'          => '',
					),
				'response'              => array(
					'body'                 => $response['body'],
					'headers'              => $response['headers'],
					),
				);
            $args['source'] = $url == get_iOS_RESTAPI_URL() ? 'Insert User to iOS' : 'Insert subscription to iOS';
			do_action( WP_REST_API_Log_Common::PLUGIN_NAME . '-insert', $args );
        }
        return $response;
    }
}

if(!function_exists('iOS_delete_user')) {
    function iOS_delete_user($user_id){
        
        $iOS_userID = get_user_meta( $user_id, '_yv_ios_id', true);
		if(empty($iOS_userID)){
            return;
		}
        
        $get_url = get_iOS_RESTAPI_URL() .'/'.$iOS_userID ;
        $auth_header = get_iOS_auth_header();
        $auth_header['content-type'] = 'application/json';
        $api_response = yv_apiCall($get_url, $auth_header, 'DELETE');
        if( true == $api_response['Error'] ){
            error_log('Unable to delete user '.$user_id. ' Response: '.print_r($api_response, true). ' - Line: ' . __LINE__);
        }
        return;

    }
}
add_action( 'delete_user', 'iOS_delete_user' );


//$userID, $shopify_product_id, $shopify_order_id, $status, $shopify_product_timestamp 
add_action("kit_activated_iOS", "schedule_kit_removal", 10, 5);

if(!function_exists('schedule_kit_removal')) {
    function schedule_kit_removal($userID, $shopify_product_id, $shopify_order_id, $status, $shopify_product_timestamp){
        if('single'!=$status){
            return;
        }
        $kit_expiration_time = strtotime('today', $shopify_product_timestamp) + 46 * DAY_IN_SECONDS ;
        $kit_expiration_date =  date_i18n("m-d-Y", $kit_expiration_time);
        
        $today = strtotime('today', current_time( 'timestamp' ) );
        if( $kit_expiration_time < $today){
            return;
        }
        
        $subs_meta = compact("userID", "shopify_product_id", "shopify_order_id", "status", "shopify_product_timestamp");
        
        //get the post for the day or create a new one
        $post_title = 'exp-' . $kit_expiration_date;
        $subscription_post = get_page_by_title( $post_title, 'OBJECT', 'kit_subscriptions');
        if(NULL == $subscription_post){
            $post_args    = array(
                'ID'            => NULL,
                'post_title'    => $post_title,
                'post_status'   => 'future',
                'post_type'     => 'kit_subscriptions',
                'post_date'     => gmdate( 'Y-m-d H:i:s', ( $kit_expiration_time ) ),
                'meta_input'    => array ('_yv_kit_to_expire' => $subs_meta),
            );
            $post_id= wp_insert_post($post_args);
        }else{
            $post_id = $subscription_post -> ID;
            add_post_meta($post_id, '_yv_kit_to_expire', $subs_meta, false);
        }
    }
}



add_action( 'publish_future_post', 'my_test_future_post' );

if(!function_exists('my_test_future_post')){
    function my_test_future_post( $post_id ) {
        //return if diff type of post
        if ( get_post_type($post_id) != 'kit_subscriptions'){
            return;
        }
        $post_title = 'exp-' . date_i18n("m-d-Y");
        $subscription_post = get_page_by_title( $post_title, 'OBJECT', 'kit_subscriptions');
        // return if no expirations for today
        if(NULL == $subscription_post){
            return;
        }
        
        $postmeta = get_post_meta( $subscription_post->ID, '_yv_kit_to_expire');
        if(empty($postmeta)){
            return;
        }else{
            $addheaders = array(
				'content-type' => 'application/json'
			);
            //if test env use test zapier hook URL
            if( strpos( site_url() , 'www.youveda.com') === false ) {
                yv_apiCall('https://hooks.zapier.com/hooks/catch/1605770/m0cvxv/', $addheaders, 'POST', $postmeta);
                yv_apiCall('https://requestb.in/1j4p16c1', $addheaders, 'POST', $postmeta);                
            }else{
                //if PROD use PROD zapier hook URL
                yv_apiCall('https://hooks.zapier.com/hooks/catch/1605770/9s2xq0/', $addheaders, 'POST', $postmeta);
                yv_apiCall('https://requestb.in/1j4p16c1', $addheaders, 'POST', $postmeta);
            }
            
            
        }
    }
}
