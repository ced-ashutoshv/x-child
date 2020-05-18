<?php
function ameba_gutenberg_blocks(){
	
	wp_register_script('custom-gutenberg-blocks' get_stylesheet_directory_uri() . 'scripts/gutenberg-cta-blocks.js', array());

	register_block_type( 'ameba/custom-cta', array(
		'editor_script' => 'custom-gutenberg-blocks'
	));
}
?>
