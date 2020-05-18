<?php

// =============================================================================
// VIEWS/INTEGRITY/CONTENT.PHP
// -----------------------------------------------------------------------------
// Standard post output for Integrity.
// =============================================================================

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

<a href="<?php the_permalink(); ?>" class="hover-link"></a>
  <div class="entry-featured">

  	<?php

      if(is_archive() || is_home()) {

        if(has_post_thumbnail()) {
          
          x_featured_image();
        } else {
          echo '<img src="/wp-content/uploads/2018/03/post-no-thumb.jpg" alt="default post image">';
        } 
      }
  	?>
    
  </div>
  <div class="entry-wrap">
    <?php x_get_view( 'integrity', '_content', 'post-header' ); ?>
    <?php x_get_view( 'global', '_content' ); ?>
  </div>
  <?php if(is_single()) { /*we added this conditional because we only want tags on the single post*/
    x_get_view( 'integrity', '_content', 'post-footer' );
  } ?>
</article>