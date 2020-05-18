<?php
/**
 * =============================================================================
 * Bootstrap for Integrity.
 * =============================================================================
 */

get_header();
?>

	<div class="container mt40px">
		<div class="<?php x_main_content_class(); ?>" role="main">

			<?php while ( have_posts() ) : the_post(); ?>
				<?php x_get_view( 'integrity', 'content', 'page' ); ?>
				<?php x_get_view( 'global', '_comments-template' ); ?>
			<?php endwhile; ?>

		</div>

	<?php get_sidebar(); ?>

	</div>

<?php get_footer(); ?>