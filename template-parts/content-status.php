<?php /** Status Post Format  **/

global $jk_utilities;
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'kelly-format-status clearfix' ); ?>>

	<div class="status-entry-container">
		
		<div class="post-thumbnail background-image" style="background-image: url(<?php echo get_the_post_thumbnail_url(); ?>);"></div>
		<div class="entry-content">

			<?php the_content( '了解更多' ); ?>

		</div><!-- .entry-content -->

		<footer class="entry-footer entry-meta text-center">
			<?php echo $jk_utilities->frontend->posted_meta(); ?>
		</footer>
	</div> <!-- .status-entry-container -->
	
</article>