<?php

// =============================================================================
// VIEWS/INTEGRITY/WP-SINGLE.PHP
// -----------------------------------------------------------------------------
// Single post output for Integrity.
// =============================================================================

$fullwidth = get_post_meta( get_the_ID(), '_x_post_layout', true );

?>

<?php get_header(); ?>
<?php 
  setup_postdata($post);
  $category_name = get_the_category()[0]->name;
  $this_post_id = get_the_id();
  $categories_id = wp_get_post_categories($this_post_id);
  $category_id = get_the_category()[0]->term_id;
  $category_url = get_category_link( $category_id );
  $published_date = get_the_date(); 
  $author = get_the_author($this_post_id);
  $postUrl = get_permalink();
  $title = get_the_title();
?>
  <header class="main-header flex-align-vertical text-center animated fadeInDown duration3">
    <div class="x-container max width offset">
      <hgroup>
        <h1 class="main-title noMargin animated fadeInDown duration3 delay3">
          <?php the_title(); ?>
        </h1>
        <?php foreach ($categories_id as $key => $value) {
          echo '<a class="category mb10px animated fadeInDown duration2 delay'.($key+5).'" href="'.get_category_link($value).'">'.get_cat_name($value).'</a>';
        } ?>
      </hgroup>
    </div>
  </header>

  <div class="x-container max width offset animated fadeInUp duration4">

    <header class="post-meta border-bottom">
      <ul class="social"> 
        <li class="share-whatsapp"><a href="whatsapp://send?text=<?php echo get_the_title('','',true) . ' - ' . get_the_permalink(); ?>" data-action="share/whatsapp/share" data-action="share/whatsapp/share">
          <i class="x-icon x-icon-whatsapp" data-x-icon-b="" aria-hidden="true"></i>
        </li>
        <li><a href="mailto:type email address here?subject=I wanted to share this post with you from <?php bloginfo('name'); ?>&body=<?php the_title('','',true); ?>&#32;&#32;<?php the_permalink(); ?>" title="Email to a friend/colleague" target="_blank">
          <i class="x-icon x-icon-envelope" data-x-icon-s="" aria-hidden="true"></i>
        </li>
        <li><a href="https://pinterest.com/pin/create/button/?url=<?php echo $postUrl;?>">
          <i class="x-icon x-icon-pinterest-p" data-x-icon-b="" aria-hidden="true"></i></a>
        </li>
        <li><a class="openOnModalWindow" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $postUrl;?>">
          <i class="x-icon x-icon-facebook-f" data-x-icon-b="" aria-hidden="true" style="font-weight:400"></i></a>
        </li>
        <li><a class="openOnModalWindow" target="_blank" href="https://twitter.com/home?status=<?php echo $postUrl; ?>">
          <i class="x-icon x-icon-twitter" data-x-icon-b="" aria-hidden="true" style="font-weight:400"></i>
        </a></li>
      </ul>
      <div class="metadata">
        <time class="date" datetime="<?php echo $published_date;?>"><?php echo $published_date; ?></time>
      </div>
    </header>

    <div class="<?php x_main_content_class(); ?>" role="main">
      <?php while ( have_posts() ) : the_post(); ?>
        <?php x_get_view( 'integrity', 'content', get_post_format() ); ?>
        
      <?php endwhile; ?>
    </div>

    <?php if ( $fullwidth != 'on' ) : ?>
      <?php get_sidebar(); ?>
    <?php endif; ?>

  </div><!-- .x-container -->

  <div class="x-container max width offset">
    <?php x_get_view( 'global', '_comments-template' ); ?>
  </div><!-- .x-container -->
    
  
  <?php echo do_shortcode("[youveda_posts category='".$category_id."']"); ?>


<?php get_footer(); ?>