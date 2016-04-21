<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class JKThemeSetup {

	public static function init() {
		self::theme_supports();
		self::customize_title_tag(); // <title> in <head> element
		self::register_nav_menus();
		self::append_search_box_to_primary_menu();

		self::add_sidebars(); 
		self::facebook_integration_js();
		self::widgets();


		self::shortcodes();
		self::embeds();
		self::post_metas();

		self::add_customize_controls(); // customize manager

	}

	private static function theme_supports() {
		add_action( 'after_setup_theme', function(){

			$header_img_defaults = array(
				'default-image' => '', 
				'flex-width' => true, 
				'flex-height' => true, 
				'uploads' => true, 
				'height' => 300, 
			);
			add_theme_support('html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption') );
			add_theme_support('custom-header', $header_img_defaults); 
			add_theme_support('title-tag');
			add_theme_support('post-formats', array('aside', 'gallery', 'image', 'status', 'video', 'audio'));
			add_theme_support('post-thumbnails');
		});
	}

	private static function register_nav_menus() {
		register_nav_menus(array(
		    'primary' => 'Primary Nav Menu', 
		    'primary-visible-xs' => 'Right After Primary Nav Menu, but only shows on small devices',
		    'footer' => 'Footer Menu'
		));
	}

	private static function append_search_box_to_primary_menu(){
		function append_search_box_to_primary_menu( $items, $args ) {
			if ( $args->theme_location == 'primary' ){
				return $items . '<li>' . get_search_form( false ) . '</li>';
			}
			return $items;
		}
		add_filter( 'wp_nav_menu_items', 'append_search_box_to_primary_menu', 10, 2 );
	}

	private static function customize_title_tag() {

		add_filter( 'document_title_separator', function($sep){
			return '•';
		});
	}

	private static function add_sidebars() {
		   /**
			* Creates a sidebar
			* @param string|array  Builds Sidebar based off of 'name' and 'id' values.
			*/
			$args = array(
				'name'          => 'Sidebar Right',
				'id'            => 'sidebar-1',
				'class'         => '',
				'before_widget' => '<li id="%1" class="widget %2">',
				'after_widget'  => '</li>',
				'before_title'  => '<h2 class="widgettitle">',
				'after_title'   => '</h2>'
			);
		
			register_sidebar( $args );
		
	}

	private static function add_customize_controls() {
		require_once( 'class-jk-theme-customization-controls.php' );
		new JKThemeCustomizationControls();
	}

	private static function shortcodes() {
		require_once('class-jk-shortcodes.php');
		new JKShortcodes();
	}

	private static function embeds() {
		require_once('class-jk-embeds.php');
		new JKEmbeds();
	}

	private static function post_metas() {
		require_once('class-jk-meta-boxes.php');
		new JKMetaBoxes();
	}

	private static function facebook_integration_js() {
		add_action('jk_body_start', function(){
			?>
				<div id="fb-root"></div>
				<script>(function(d, s, id) {
				  var js, fjs = d.getElementsByTagName(s)[0];
				  if (d.getElementById(id)) return;
				  js = d.createElement(s); js.id = id;
				  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.6&appId=391602454372029";
				  fjs.parentNode.insertBefore(js, fjs);
				}(document, 'script', 'facebook-jssdk'));
				</script>
			<?php 
		});
	}

	private static function widgets() {
		require_once('class-jk-social-widget.php');
		add_action('widgets_init', function(){
			register_widget('JKSocialWidget');
		});
	}

}