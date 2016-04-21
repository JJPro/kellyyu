<?php /** Video Post Format  **/

global $jk_utilities;
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'kelly-format-video' ); ?>>
	
	<header class="entry-header text-center">
		
		<!-- <div class="embed-responsive embed-responsive-16by9"> -->
			<?php /*echo sunset_get_embedded_media( array('video','iframe') );*/ ?>
		<!-- </div> -->
		
		<?php the_title( '<h1 class="entry-title"><a href="'. esc_url( get_permalink() ) .'" rel="bookmark">', '</a></h1>'); ?>
		
		<div class="entry-meta">
			<?php echo $jk_utilities->frontend->posted_meta(); ?>
		</div>
		
	</header>
	
	<div class="entry-content">
		
		<?php the_content( '阅读整篇文章' ); ?>
		
	</div><!-- .entry-content -->
	
	<footer class="entry-footer">
		
	</footer>
	
</article>