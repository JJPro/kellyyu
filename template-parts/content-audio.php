<?php /** Video Post Format  **/

global $jk_utilities;
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'kelly-format-audio' ); ?>>
	
	<header class="entry-header text-left">
		
		<?php if (is_single()): ?>
			<?php the_title( '<h1 class="entry-title">', '</h1>'); ?>
		<?php else: ?>
			<?php the_title( '<h1 class="entry-title"><a href="'. esc_url( get_permalink() ) .'" rel="bookmark">', '</a></h1>'); ?>
		<?php endif; ?>
		
	</header>
	
	<div class="entry-content">
		
		<?php if ($jk_utilities->frontend->has_external_audio()): ?>
			<?php echo $jk_utilities->frontend->get_external_audio_html(); ?>
		<?php endif; ?>
		<?php the_content( '阅读全部' ); ?>
		
	</div><!-- .entry-content -->
	
	<footer class="entry-footer">
		
		<div class="entry-meta">
			<?php echo $jk_utilities->frontend->posted_meta(); ?>
		</div>
		
	</footer>
	
</article>