<?php /** Video Post Format  **/

global $jk_utilities;
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'kelly-format-video' ); ?>>
	
	<header class="entry-header text-center">
		
		<!-- <div class="embed-responsive embed-responsive-16by9"> -->
			<?php /*echo sunset_get_embedded_media( array('video','iframe') );*/ ?>
		<!-- </div> -->
		<?php if (is_single()): ?>
			<?php the_title( '<h1 class="entry-title">', '</h1>'); ?>
		<?php else: ?>
			<?php the_title( '<h1 class="entry-title"><a href="'. esc_url( get_permalink() ) .'" rel="bookmark">', '</a></h1>'); ?>
		<?php endif; ?>

		<div class="entry-meta">
			<?php echo $jk_utilities->frontend->posted_meta(); ?>
		</div>

		<?php echo apply_filters('entry_header', ''); ?>

	</header>
	
	<div class="entry-content">
		
		<?php the_content( '继续阅读' ); ?>
		
	</div><!-- .entry-content -->
	
	<footer class="entry-footer">
		<?php echo apply_filters('entry_footer', ''); ?>
	</footer>
	
</article>