<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package kellyyu_1.0
 */

?>

	<footer id="colophon" class="site-footer text-center" role="contentinfo">
		<div class="container">
			<div class="row">
		        <div class="col-xs-12">
		            <?php
		            if (has_nav_menu('footer'))
		                wp_nav_menu( array('theme_location' => 'footer') );
		            ?>
		        </div>
		    </div>
	    </div>
		<div class="site-info">
			<?php printf( 'Proudly powered by %s', 'WordPress' ); ?>
			<br>
			<?php printf( 'Theme by %s.', '<a href="http://jjpro.net/" rel="designer">JJPro.net</a>' ); ?>
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
