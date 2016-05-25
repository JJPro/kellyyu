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

		<?php echo apply_filters('entry_header', ''); ?>

	</header>
	
	<div class="entry-content">

		<?php if ( !post_password_required() && $jk_utilities->frontend->has_external_audio()): ?>
			<?php echo $jk_utilities->frontend->get_external_audio_html(); ?>
		<?php endif; ?>
		<?php the_content( '继续阅读' ); ?>
		
	</div><!-- .entry-content -->
	
	<footer class="entry-footer">
		
		<div class="entry-meta">
			<?php echo $jk_utilities->frontend->posted_meta(); ?>
		</div>

		<?php echo apply_filters('entry_footer', ''); ?>

	</footer>
	
</article>