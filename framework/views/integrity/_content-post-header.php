	<?php

// =============================================================================
// VIEWS/INTEGRITY/_CONTENT-POST-HEADER.PHP
// -----------------------------------------------------------------------------
// Standard <header> output for various posts.
// =============================================================================

?>

<header class="entry-header">
	<?php if ( is_single() ) : ?>
	<!-- <h1 class="entry-title"><?php /* the_title(); We don't want the tithle here in the sigle */ ?></h1> -->
	<?php else : ?>
	<?php x_integrity_entry_meta(); ?>
	<h2 class="entry-title">
		<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to: "%s"', '__x__' ), the_title_attribute( 'echo=0' ) ) ); ?>"><?php x_the_alternate_title(); ?></a>
	</h2>
	<time datetime="<?php echo get_the_date("Y-d-m")?>" class="time"><?php echo get_the_date(); ?></time>
	<?php endif; ?>
</header>