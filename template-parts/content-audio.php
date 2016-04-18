<?php /** Video Post Format  **/

global $jk_utilities;
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'kelly-format-audio' ); ?>>
	
	<header class="entry-header text-left">
		
		<?php the_title( '<h1 class="entry-title"><a href="'. esc_url( get_permalink() ) .'" rel="bookmark">', '</a></h1>'); ?>
		
	</header>
	
	<div class="entry-content">
		
		<?php if ($jk_utilities->frontend->has_ximalaya_audio()): ?>
			<?php echo $jk_utilities->frontend->get_ximalaya_audio_html(); ?>
		<?php endif; ?>
		<?php the_content( '阅读整篇文章' ); ?>
		
	</div><!-- .entry-content -->
	
	<footer class="entry-footer">
		
		<div class="entry-meta">
			<?php echo $jk_utilities->frontend->posted_meta(); ?>
		</div>
		
	</footer>
	
</article>