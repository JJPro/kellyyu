<?php
/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package kellyyu_1.0
 */

get_header(); ?>
<div id="content" class="site-content">
	<div class="container">
		<div class="row">
			<div class="col-md-8">
				
				<div id="primary" class="content-area">
					<main id="main" class="site-main" role="main">

					<?php do_shortcode('[google_ads]'); ?>
					<?php
						while ( have_posts() ) : the_post();

							get_template_part( 'template-parts/content', get_post_format() );

							the_post_navigation();

							do_shortcode('[google_ads]');

							// If comments are open or we have at least one comment, load up the comment template.
							if ( comments_open() || get_comments_number() ) :
								comments_template();
							endif;

						endwhile; // End of the loop.
					?>
					</main><!-- #main -->
				</div><!-- #primary -->

			</div> <!-- .col-sm-9 -->
			
			<div class="col-md-4">
				
				<?php get_sidebar(); ?>
				
			</div> <!-- .col-sm-3 -->
		</div>
	</div>

</div><!-- #content -->
<?php get_footer(); ?>