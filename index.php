<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package kellyyu_1.0
 */

get_header(); ?>
<div id="content" class="site-content">
	<div class="container">
		<div class="row">
			<div class="col-md-9">
				
				<div id="primary" class="content-area">
					<main id="main" class="site-main" role="main">

						<?php
							if ( have_posts() ) :

								if ( is_home() && ! is_front_page() ) : ?>
									<header>
										<h1 class="page-title screen-reader-text"><?php single_post_title(); ?></h1>
									</header>

								<?php
								endif;

								$count = 0;
								echo do_shortcode('[google_ads]');
								/* Start the Loop */
								while ( have_posts() ) : the_post();

									if ($count == 1){
										echo do_shortcode('[google_ads]');
									}
									$count++;

									/*
									 * Include the Post-Format-specific template for the content.
									 * If you want to override this in a child theme, then include a file
									 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
									 */
									get_template_part( 'template-parts/content', get_post_format() );

								endwhile;

								// Previous/next page navigation.
								the_posts_pagination( array(
									'prev_text'          => '上一页',
									'next_text'          => '下一页',
									'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'twentyfifteen' ) . ' </span>',
								) );

							else :

								get_template_part( 'template-parts/content', 'none' );

							endif;
						?>

					</main><!-- #main -->
				</div><!-- #primary -->

			</div> <!-- .col-sm-9 -->
			
			<div class="col-md-3">
				
				<?php get_sidebar(); ?>
				
			</div> <!-- .col-sm-3 -->
		</div>
	</div>

</div><!-- #content -->
<?php get_footer(); ?>
