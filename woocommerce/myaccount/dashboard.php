<?php
/**
 * My Account Dashboard
 *
 * Shows the first intro screen on the account dashboard.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/dashboard.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @author      WooThemes
 * @package     WooCommerce/Templates
 * @version     2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<?php
	/*
	echo '<p>';
	translators: 1: user display name 2: logout url */
	/*
	printf(
		__( 'Hello %1$s (not %1$s? <a href="%2$s">Log out</a>)', 'woocommerce' ),
		'<strong>' . esc_html( $current_user->display_name ) . '</strong>',
		esc_url( wc_logout_url( wc_get_page_permalink( 'myaccount' ) ) )
	);
	echo '</p>';
	*/
?>

<?php
	/*
	echo '<p>';
	printf(
		__( 'From your account dashboard you can view your <a href="%1$s">recent orders</a>, manage your <a href="%2$s">shipping and billing addresses</a> and <a href="%3$s">edit your password and account details</a>.', 'woocommerce' ),
		esc_url( wc_get_endpoint_url( 'orders' ) ),
		esc_url( wc_get_endpoint_url( 'edit-address' ) ),
		esc_url( wc_get_endpoint_url( 'edit-account' ) )
	);
	echo '</p>';
	*/
?>

<?php
	/**
	 * My Account dashboard.
	 *
	 * @since 2.6.0
	 */
	do_action( 'woocommerce_account_dashboard' );

	/**
	 * Deprecated woocommerce_before_my_account action.
	 *
	 * @deprecated 2.6.0
	 */
	do_action( 'woocommerce_before_my_account' );

	/**
	 * Deprecated woocommerce_after_my_account action.
	 *
	 * @deprecated 2.6.0
	 */
	do_action( 'woocommerce_after_my_account' ); ?>

<div class="activate-kit-container">
	<p>Pair up with our mobile app to further holistically support your health inside and out. Once downloaded, you can register your product using the activation code on the card inside your product. Just browse to <em>My Programs</em> in the app main menu.</p>
	<?php //echo do_shortcode('[gravityform id="8" title="false" description="false" ajax="true"]'); ?>
	
</div>

<div class="youvedas-mobile-app">
	<h1 class="entry-title mb10px">YouVeda’s Mobile App</h1>
	<p class="copy">Exercise, meditation, and a balanced diet are the cornerstones of healthy living. Our app gives you all of this information, along with progress insights to track your path to wellness.<br> Give it a try!</p>
	<p class="mb0">
		<a href="https://itunes.apple.com/us/app/youveda/id1394616380?ls=1&mt=8" target="_blank" style="outline: none;">
			<img class="hover mr10px" width="100" src="/wp-content/uploads/2018/04/app-store-badge.svg" alt="'Download on the App Store' button">
		</a>
		<a href="https://play.google.com/store/apps/details?id=com.youveda.app" target="_blank" style="outline: none;">
			<img class="hover" width="100" src="/wp-content/uploads/2018/04/google-play-badge.svg" alt="'Get it on Google Play' button">
		</a>
	</p>
	<img class="cellphone" src="<?php echo get_site_url(); ?>/wp-content/uploads/2018/04/img-youveda-app.png" alt="YouVeda's Mobile App on black cellphone">
</div>




