<?php
/**
 * Template Name: Image Wall Template
 *
 * @package kellyyu_1.0
 */

global $jk_utilities;

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main image-wall-page" role="main">
			<div class="container-fluid">

				<?php do_shortcode('[google_ads]'); ?>

				<?php
				while ( have_posts() ) : the_post();

					if ($jk_utilities->frontend->is_user_from_mainland_china()) {
						echo '<div class="warning text-center"><strong>注意：</strong>本页照片引自Kelly的Instagram，大陆使用者可能无法正常显示，很抱歉。</div>';
					}
					get_template_part( 'template-parts/content', 'page' );

					// If comments are open or we have at least one comment, load up the comment template.
					if ( comments_open() || get_comments_number() ) :
						comments_template();
					endif;

				endwhile; // End of the loop.
				?>

				<?php do_shortcode('[google_ads]'); ?>
			</div>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();
