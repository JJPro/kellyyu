<?php /** Status Post Format  **/

global $jk_utilities;
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'kelly-format-status clearfix' ); ?>>

	<div class="status-entry-container">
		<div class="row no-gutter">
			<div class="post-thumbnail-container col-lg-3 col-md-4 col-sm-4 col-xs-5">
				<div class="post-thumbnail background-image " style="background-image: url(<?php echo get_the_post_thumbnail_url(); ?>);"></div>
			</div>
			<div class="entry-content col-lg-9 col-lg-offset-3 col-md-8 col-md-offset-4 col-sm-8 col-sm-offset-4 col-xs-8 col-xs-offset-4">

				<?php the_content( '了解更多' ); ?>

			</div><!-- .entry-content -->
			<footer class="entry-footer entry-meta col-xs-12 text-center">
				<?php echo $jk_utilities->frontend->posted_meta(); ?>
			</footer>
		</div> <!--.row-->

	</div> <!-- .status-entry-container -->

</article>