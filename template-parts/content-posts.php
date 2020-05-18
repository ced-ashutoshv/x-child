<?php 
  /*
    this template content works with :
    - "SHORTCODE FOR BLOG POSTS" shortcode on x-child/functions.php
    - Related css on 'sass/template-parts/_youveda-posts.scss'
  */

  $post_link = get_the_permalink($post_id);
  $post_featured_image_url = get_the_post_thumbnail_url($post_id,'medium');
  $post_title = get_the_title($post_id);
  $categories_id = wp_get_post_categories($post_id);
  $category_id = get_the_category()[0]->term_id;
  $category_url = get_category_link( $category_id );

  if(!$post_featured_image_url) {
    $post_featured_image_url = '/wp-content/uploads/2018/03/post-no-thumb.jpg';
  }
?>

<article class="youveda-post x-column x-md x-1-3">
  <a class="link-layer" href="<?php echo $post_link ?>"></a> 
    <div class="post-thumb">
      <img src="<?php echo $post_featured_image_url; ?>" alt="post featured image">
    </div>
    <div class="post-content bgColorWhite">
      <p class="p-meta">
        <?php foreach ($categories_id as $key => $value) {
          echo '<a class="category mb10px" href="'.get_category_link($value).'">'.get_cat_name($value).', </a>';
        } ?>
      </p>
      <h1 class="title">
        <?php echo $post_title; ?>
      </h1>
    </div>
    <div class="post-footer bgColorWhite">
      <a class="view-more" href="<?php echo $post_link; ?>">Read More</a>
    </div>
</article>