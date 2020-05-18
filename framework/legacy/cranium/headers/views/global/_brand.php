<?php

// =============================================================================
// VIEWS/GLOBAL/_BRAND.PHP
// -----------------------------------------------------------------------------
// Outputs the brand.
// =============================================================================

$option_logo_text = x_get_option( 'x_logo_text' );
$option_logo_src  = x_get_option( 'x_logo' );
$logo_text        = ( empty( $option_logo_text ) ) ? get_bloginfo( 'name' ) : $option_logo_text;

if ( empty( $option_logo_src ) ) {
  $logo_output = $logo_text;
} else {
  $logo_src    = x_make_protocol_relative( $option_logo_src );
  $logo_output = '<img id="youveda-logo-main" src="' . $logo_src . '" alt="' . $logo_text . '">';
}

if ( x_get_option( 'x_logo_visually_hidden_h1' ) ) {
  echo '<h1 class="visually-hidden">' . $logo_text . '</h1>';
}

?>

<a id="logo-container" href="<?php echo home_url( '/' ); ?>" class="<?php x_brand_class(); ?>">
	<div class="youveda-logo-container">
 		<?php echo $logo_output; ?>
 		<img id="youveda-logo-slogan" src="/wp-content/uploads/2020/01/youveda-slogan.svg" />
 	</div>
</a>
