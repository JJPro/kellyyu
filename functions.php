<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}


if ( ! class_exists( 'JKKellyYuTheme' ) ): 
class JKKellyYuTheme {

	public function __construct() {
		$this->includes();
		JKThemeSetup::init();
		$this->enqueue_scripts();

		new JKJetpackIntegration();


		$this->code_cleaning(); // clean up html code for final output: strip off versions
	}

	private function includes() {
		require_once('inc/globals.php');
		require_once('inc/walker.php');
		require_once('inc/class-jk-utilities.php');
		require_once('inc/class-jk-theme-setup.php'); // structure setup
		require_once('inc/class-jk-jetpack-integration.php');

	}

	private function code_cleaning() {
		require_once ('inc/cleanup.php');
	}

	// ** enqueue scripts, styles, and fonts ** //
	private function enqueue_scripts() {
		add_action( 'admin_enqueue_scripts', function(){

			// ** iCon Fonts ** //
			wp_enqueue_style( 'jk-font', get_template_directory_uri() . '/fonts/jk-font/styles.css', false, '1.0', 'all');

		});


		add_action( 'wp_enqueue_scripts', function(){
			// ** Scripts ** //
			wp_enqueue_script( 'bootstrap', get_template_directory_uri() . '/js/bootstrap.min.js', ['jquery-core'], '3.3.6', true );
//			wp_enqueue_script( 'audiojs', get_template_directory_uri() . '/js/lib/audiojs/audio.min.js', false, false, true);
			wp_enqueue_script( 'app_script', get_template_directory_uri() . '/js/app.js', ['jquery-core', 'bootstrap'], false, true);

			// ** Styles ** //
			wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/css/bootstrap.min.css', false, '3.3.6', 'all' );
			wp_enqueue_style( 'jk-style', get_template_directory_uri() . '/css/theme-style.css', false, '1.0', 'all' );

			// ** Fonts ** //
			wp_enqueue_style( 'jk-font', get_template_directory_uri() . '/fonts/jk-font/styles.css', false, '1.0', 'all');


			// ** Inline Styles for customizable primary menu text color ** //
			$primary_menu_text_color = get_theme_mod('primary_menu_text_color' );
			$primary_menu_text_hover_color = get_theme_mod('primary_menu_text_hover_color' );
			wp_add_inline_style(
				'jk-style',
				"nav.main-navigation li a,
				 nav.main-navigation .search-form {
					color: $primary_menu_text_color;
				 }

				 nav.main-navigation .nav-tabs>li>a:visited {
				 	color: $primary_menu_text_color;
				 }
				 nav.main-navigation .nav-tabs>li.active>a:hover,
				 nav.main-navigation .nav-tabs>li.active>a:visited,
				 nav.main-navigation .nav-tabs>li>a:hover {
				 	color: $primary_menu_text_hover_color;
				 }
				 "
			);

		});

	}

}
endif; 

new JKKellyYuTheme();

/**
 * use this function to fetch the main instance when you need the instance. 
 */ 
function JK() {
	return JKKellyYuTheme::instance();
}