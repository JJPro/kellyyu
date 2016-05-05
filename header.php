<?php
/**
 * The header for our theme.
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package kellyyu_1.0
 */

global $jk_utilities;
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php do_action('jk_body_start'); ?>
<div id="page" class="site">
	<div class="container-fluid">
		<div class="row">
			
			<header id="masthead" class="text-center" role="banner" style="background-image: url(<?php header_image();?>); width: 100%; padding-top: <?php echo $jk_utilities->admin->get_header_img_width_height_rate(); ?>;">
				<div class="nav-container">

					<div class="container no-padding-left no-padding-right">

						<button type="button" class="nav-toggle hidden-lg hidden-md">
							<span></span>
						</button>

						<nav id="site-navigation" class="main-navigation" role="navigation">

							<?php wp_nav_menu( array(
											'theme_location' => 'primary',
											'container' => false,
											'menu_class' => 'nav nav-tabs', 
											'walker' => new Sunset_Walker_Nav_Primary()
							)); ?>
							<?php wp_nav_menu( array(
											'theme_location' => 'primary-visible-xs',
											'container' => false,
											'menu_class' => 'nav nav-tabs', // deleted  'hidden-sm',
											'walker' => new Sunset_Walker_Nav_Primary()
							)); ?>

						</nav><!-- #site-navigation -->

					</div>
				</div> <!-- .nav-container -->
			</header><!-- #masthead -->
		</div><!-- .row  to offset padding in container-fluid -->
	</div>