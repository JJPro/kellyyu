<?php /** Status Post Format  **/

global $jk_utilities;
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'kelly-format-status clearfix' ); ?>>

	<?php if ( is_home() || is_archive() || is_search() ): ?>
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
	<?php endif; ?>

	<?php if ( is_single() ): ?>
		<header class="entry-header text-center">
			<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
			<div class="entry-meta">
				<?php echo $jk_utilities->frontend->posted_meta(); ?>
			</div><!-- .entry-meta -->
			<?php echo apply_filters('entry_header', ''); ?>
		</header><!-- .entry-header -->

		<div class="entry-content">
			<div class="row">
				<div class="col-lg-3 col-sm-3 col-xs-4">
					<div class="post-thumbnail background-image " style="background-image: url(<?php echo get_the_post_thumbnail_url(); ?>);"></div>

				</div> <!-- left -->

				<div class="col-lg-9 col-sm-9 col-xs-8">
					<?php the_content(); ?>
				</div> <!-- right -->

			</div> <!--.row-->

		</div> <!--.entry-content-->

		<footer class="entry-footer">
			<?php echo apply_filters('entry_footer', ''); ?>
		</footer><!-- .entry-footer -->
	<?php endif; ?>

</article>