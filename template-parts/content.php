<?php
/**
 * Template part for displaying posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package kellyyu_1.0
 */

global $jk_utilities;
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header text-center">



		<?php

			if ( is_single() ) {
				the_title( '<h1 class="entry-title">', '</h1>' );
			} else {
				the_title( '<h1 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h1>' );
			}

		if ( 'post' === get_post_type() ) : ?>
		<div class="entry-meta">
			<?php echo $jk_utilities->frontend->posted_meta(); ?>
		</div><!-- .entry-meta -->
		<?php endif; ?>

		<?php echo apply_filters('entry_header', ''); // sharedaddy uses this hook ?>

	</header><!-- .entry-header -->

	<?php $jk_utilities->frontend->jk_post_thumbnail(); ?>
	<?php if (is_single()) echo do_shortcode('[google_ads]'); ?>

	<div class="entry-content">
		<?php the_content( '继续阅读' ); ?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php echo apply_filters('entry_footer', ''); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->
