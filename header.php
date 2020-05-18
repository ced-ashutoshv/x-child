<?php

// =============================================================================
// HEADER.PHP
// -----------------------------------------------------------------------------
// The site header.
// =============================================================================

x_get_view( 'header', 'base' );  
if(is_shop() && (is_plugin_active('woocommerce-currency-switcher/index.php'))) {echo do_shortcode( '[woocs show_flags=0]' );} 

?>