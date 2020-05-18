<?php  
define('SHOPIFY_APP_SECRET', 'e0d10c63f4ad335ca48413bf5a972bcbbf27120887393cbd3042fe506149ea0a');

/** 
 *  ADD Order
 *
 *	recource: /wp-json/v1/order/new
 */
add_action( 'rest_api_init', function () {
	register_rest_route( 'youveda/order', '/new', array(
		'methods' => 'post',
		'callback' => 'addOrder_restApiCustom'
	) );
} );


/** 
 *   DELETE Order
 *
 *	recource: /wp-json/youveda/order/cancel
 */
add_action( 'rest_api_init', function () {
	register_rest_route( 'youveda/order', '/cancel', array(
		'methods' => 'post',
		'callback' => 'cancelOrder_restApiCustom',
        'permission_callback' => function () {
            return strpos( site_url() , 'local.') !== false || current_user_can( 'edit_posts' );
		}
	) );
} );

//New Order func
if (!function_exists('addOrder_restApiCustom')){
function addOrder_restApiCustom( WP_REST_Request $request ) {

	//validation
	$requestHeaders = $request->get_headers();
	$hmac_header = $requestHeaders['x_shopify_hmac_sha256'][0];
	$data = file_get_contents('php://input');
	$verified = verify_shopify_webhook($data, $hmac_header);
    
	if(!$verified){ return "[Error]: not allowed"; }
    
    

    //extract($parameters);
    $parameters = $request->get_json_params();
    $orderID   = $parameters['id'];
    $customer   = $parameters['customer'];
    $line_items = $parameters['line_items'];
    $created_at = $parameters['created_at'];
	$user_email = getShopifyParams('userEmail', $customer);
    //array of shopify products » IDs
	$shopify_product_id = getShopifyParams('productsID', $line_items);
	$orderTime = getShopifyParams('time', $created_at); 
    $subs_type = strpos( $parameters['tags'] , 'recurring_order') === false ? 'single' : 'active';
	$user = get_user_by('email', $user_email);
    
    $admin_email = get_bloginfo('admin_email');
    
	//if user already exist
    if(!empty($user)){ 

		$args = array(
			'meta_query' => array(
					array(
						'key'     => '_youveda-product-mb_shopify_id',
						'value'   => $shopify_product_id,
						'compare' => 'IN',
					),
				),
			'post_type'        => 'product',
		);
        
		$products = get_posts( $args );

		if(empty($products)){  
    		$sendEmail = wp_mail( $admin_email, 'Invalid Request', "Products from the Order: ". $orderID ." doesn't exist in wordpress database" );
            return "[Error]: Product doesn't exist in wordpress database";
		}else{
        	if(count($products) != count($shopify_product_id) ){
    			$sendEmail = wp_mail( $admin_email, 'Invalid Request', "Products from the Order: ". $orderID ." doesn't exist in wordpress database" );        	   
        	}	  
			$productsID_array = array();
			foreach ($products as $prod) {
				array_push($productsID_array, $prod->ID);
			}
		}

        $insert_subscription = yv_add_single_subscription ($user->ID, $productsID_array, $orderID, $subs_type, $orderTime);
        return new WP_REST_Response($insert_subscription);

	}else{ 

		$user_Data = getShopifyParams('userData', $customer);
        $meta = array(
            'user_login' => $user_Data['email'],
            'user_email' => $user_Data['email'],
            'first_name' => $customer['first_name'],
            'last_name'  => $customer['last_name'],
            'role'       => 'subscriber',
        );
        
        $user_id = wppb_signup_user( $user_Data['email'], $user_Data['email'], $meta );

		if ( is_wp_error( $user_id ) ) {
		    return $user_id;
		}

        return new WP_REST_Response("[User Added]");

	}

}}

//Cancel Order func
if (!function_exists('cancelOrder_restApiCustom')){
    function cancelOrder_restApiCustom( WP_REST_Request $request ) {
        
        $parameters = $request->get_json_params();
        $productId  = $parameters['shopify_product_id']; 
        $orderID    = $parameters['shopify_order_id'];
        $userID     = $parameters['userID'];
        $remove     = yv_remove_subscription( $userID, $productId, $orderID );
        return new WP_REST_Response($remove);
    }
}

if (!function_exists('verify_shopify_webhook')){
function verify_shopify_webhook($data, $hmac_header){
    if( strpos( site_url() , 'local.') === false && isset($_ENV['PANTHEON_ENVIRONMENT']) && 'dev' != $_ENV['PANTHEON_ENVIRONMENT'])  {
        $calculated_hmac = base64_encode(hash_hmac('sha256', $data, SHOPIFY_APP_SECRET, true));
        return $hmac_header == $calculated_hmac;
    }else{
        return true;
    }
  
}}

if (!function_exists('getShopifyParams')){
    function getShopifyParams($type, $data){
    	
    	switch ($type) {
    		case 'userEmail':
    			return $data['email'];
    			break;
    
    		case 'time':
    			return strtotime($data);
    			break;
    
    		case 'productsID':
    			$IDs = array();
    			foreach ($data as $item) {
    				array_push($IDs, $item['product_id']);
    			}
    			return $IDs;
    			break;
    
    		case 'userData':
    
    			$userSlug = $data['first_name'].$data['last_name'];
    			$userSlug = strtolower($userSlug);
    			$userSlug = str_replace(' ', '-', $userSlug);
    
    			$user = array(
    				'email' => $data['email'],
    				'user_login' => $userSlug,
    			);
    			return $user;
    			break;
    		
    	}
    
    }
}
