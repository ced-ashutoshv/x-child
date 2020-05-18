<?php
/**
 * My Account page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/my-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}



wc_print_notices();

/**
 * My Account navigation.
 * @since 2.6.0
 */

$user_details=wp_get_current_user();
$pretty_name = $user_details->first_name . " " . $user_details->last_name;
wp_update_user( array ('ID' => $user_details->ID, 'display_name'=> $pretty_name) ) ;

do_action( 'woocommerce_account_navigation' ); 
global $wp;
$is_my_account_subscriptions_page = array_key_exists('subscriptions', $wp->query_vars);
?>
<style> 	
	/* TODO: Remove from here and place in the right sass */
	.woocommerce-MyAccount-navigation h1.entry-title{color: #607D8B;}
	.entry-content .woocommerce-MyAccount-content{max-width: 100%;}
	.entry-content .woocommerce-MyAccount-content .subscription-products.order-products{width: 300px;}
	.entry-content .woocommerce-MyAccount-content .subscription-total.order-total{width: 130px;}
	.entry-content .woocommerce-MyAccount-content td {font-size: 0.9em;}
	.entry-content .woocommerce-MyAccount-content td .button{color: #008080;background: transparent;border: 1px solid #008080;min-width: 50px;padding: 5px 15px;font-size: 1.3rem;margin: 10px 0;}
	.entry-content .woocommerce-MyAccount-content .subscription-actions.order-actions{width: 50px;font-size: 0em;}
	.entry-content .woocommerce-MyAccount-content tr:nth-child(even){background: #3298980d;}
	.woocommerce-account .addresses .col-1 address, .woocommerce-account .addresses .col-2 address, .woocommerce-checkout .addresses .col-1 address, .woocommerce-checkout .addresses .col-2 address{padding-left:10px;}
	.woocommerce-columns.woocommerce-columns--2.woocommerce-columns--addresses.col2-set.addresses{margin-top:60px;}
	.woocommerce-page .entry-wrap h2{font-size: 2rem;color: #607D8B;}
	.x-alert-danger, .buddypress #message.error, .bbp-template-notice.error{color: #b94a48;border: 1px solid #b94a48;background: none;box-shadow: none;border-radius: 0;width: fit-content;}
	.social_notice, .reset_email{display:none;padding: 1em 1.15em;border: 1px solid teal;margin: 20px 0;color:teal}
</style>
<div class="woocommerce-MyAccount-content <?php echo $is_my_account_subscriptions_page ? 'my-subscriptions' : ''; ?>">
	<?php
		/**
		 * My Account content.
		 * @since 2.6.0
		 */
		do_action( 'woocommerce_account_content' );
	?>
</div>
