<?php
// =============================================================================
// FOOTER.PHP
// -----------------------------------------------------------------------------
// The site footer.
// =============================================================================


if(Is_checkout()){ ?>

	<img class="youveda-loader" src="/wp-content/themes/x-child/images/youveda-loader.svg" alt="Youveda Loader">

	<div class="bgColorWhite wc-checkout-footer">
		<div class="x-container max width offset">
			<div class="row">
				<div class="col-12 col-md-7 left">
					<div class="image-container">
						<img src="/wp-content/themes/x-child/images/authorize-badge.gif" alt="authorize badge">
					</div>
					<div class="cards-container">
						<h4>We Accept the following payment methods</h4>
						<img src="/wp-content/themes/x-child/images/credit-cards-01.png" alt="credit cards 01" class="first-cards cards">
						<img src="/wp-content/themes/x-child/images/credit-cards-02.png" alt="credit cards 02" class="second-cards cards">
					</div>
				</div>	
				<div class="col-12 col-md-5 right">
					<p class="copy">
						By placing your order you agree to our <a href="/terms-and-conditions/">Terms & Conditions</a>, privacy and <a href="/returns/">returns policies</a>. You also consent to some of your data being stored by YouVeda, which may be used to make future shopping experiences better for you.
					</p>
				</div>
			</div>
		</div>
	</div>
<?php }

echo '<div class="entry-content content">';
if ( ! is_shop() && ! is_product_category() && ! is_cart() && ! is_checkout() && ! is_page('affiliates-program') ) {
	echo do_shortcode( '[cs_gb id=184039]' );
	echo '<div class="x-container max width">';
		echo do_shortcode( '[cs_gb id=1856]' );
	echo '</div>';
}
echo '</div>';
x_get_view( 'footer', 'base' );
