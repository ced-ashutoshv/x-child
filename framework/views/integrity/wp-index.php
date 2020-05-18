<?php

// =============================================================================
// VIEWS/INTEGRITY/WP-INDEX.PHP
// -----------------------------------------------------------------------------
// Index page output for Integrity.
// =============================================================================

?>

<?php get_header(); ?>

	<?php 
		if (is_category() || is_tag()): 
			echo do_shortcode('[rev_slider blog]');
		endif; 
	?>

	<div class="filter-mobile">
		<div class="x-container width">
			<i class="x-icon x-icon-filter" data-x-icon-s="" aria-hidden="true"></i>
			Select Categories	
		</div>
	</div>
	<ul class="categories-list top">

		<?php
		$body_classes = get_body_class();
		$classes = '';
		if (in_array('blog',$body_classes)) {
		    $classes = 'current-cat';
		}
		?>

		<li class="all <?php echo $classes; ?>"><a href="/knowledge-center/" style="outline: none;">All</a></li>

	    <?php wp_list_categories( array(
	        'title_li' => '',
	    ) ); ?> 
	</ul>

		
	<div class="x-container max width offset main-container">
		
		<div class="<?php x_main_content_class(); ?>" role="main">
			<div>
				<?php x_get_view( 'global', '_index' ); ?>
			</div>
		</div>

		<?php get_sidebar(); ?>

	</div>

<?php get_footer(); ?>