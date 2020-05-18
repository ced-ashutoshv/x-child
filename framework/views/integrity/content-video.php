<?php

// =============================================================================
// VIEWS/INTEGRITY/CONTENT-VIDEO.PHP
// -----------------------------------------------------------------------------
// Video post output for Integrity.
// =============================================================================

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<a href="<?php the_permalink(); ?>" class="hover-link"></a>
	<div class="entry-featured">
	<?php //x_featured_video(); ?>

	<?php
	    if(is_archive() || is_home()) {
	      if(has_post_thumbnail()) {
	        x_featured_image();
	      } else {
	        echo '<img src="/wp-content/uploads/2018/03/post-no-thumb.jpg" alt="default post image">';
	      } 
	    } else {

	    	/* We want to customize the way the "Embedded Video Code" is embedded. Instead of having to pass <iframe...> to the 'Embedded Video Code' field we want to pass only the video url, in order for the youveda app to use that same url to show the videos. In order to make that work, we are outputing the iframe here, instead
	    	of only calling 'x_featured_video()' but only for the 'Embedded Video Code' field.. for 'M4V File URL' and 'OGV File URL' we still want the default behaivor, that is, call 'x_featured_video()'.
	    	*/

	    	$embeddedVideoFieldhasUrl = get_post_meta( get_the_ID(), '_x_video_embed', true );
			
	    	if($embeddedVideoFieldhasUrl){ 
	    		$aspectRatio = get_post_meta( get_the_ID(), '_x_video_aspect_ratio', true );
				/* Fix if user inputs the iframe instead of the video url */
				if(strpos($embeddedVideoFieldhasUrl,'embed')>0){
					
				$video_parts=explode("embed/",$embeddedVideoFieldhasUrl);

					if(!empty($embeddedVideoFieldhasUrl)){
						// if the video is already in the right format, just use the value
						if(empty($video_parts[1])){
							$embeddedVideoFieldhasUrl = $video_url;
						}else{
						// if the video uses embed, transform to watch?v=
							$video_code=explode('?rel=0',$video_parts[1]);
							$embeddedVideoFieldhasUrl = 'https://www.youtube.com/embed/'.$video_code[0].'?rel=0';
						}		
					}
				}
				else{
					$video_parts=explode("watch?v=",$embeddedVideoFieldhasUrl);
					$embeddedVideoFieldhasUrl = 'https://www.youtube.com/embed/'.$video_parts[1];
				}
				
				
	    		echo do_shortcode( '[x_video_embed type="' . $aspectRatio . '" no_container="true" class="mvn"]<iframe width="560" height="315" src="' . $embeddedVideoFieldhasUrl . '" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>[/x_video_embed]' );
	    	} else {
	    		x_featured_video();
	    	}

	    	$text = get_post_meta( get_the_ID(), '_yourprefix_text', true );
	    }
	?>

	</div>
  <div class="entry-wrap">
    <?php x_get_view( 'integrity', '_content', 'post-header' ); ?>
    <?php x_get_view( 'global', '_content' ); ?>
  </div>
  <?php x_get_view( 'integrity', '_content', 'post-footer' ); ?>
</article>