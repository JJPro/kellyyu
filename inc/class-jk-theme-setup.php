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
		self::wechat_img();
		self::ios_icons();

		self::add_sidebars(); 
		self::facebook_integration_js();
		self::widgets();


		self::embeds();
		self::shortcodes();
		self::post_metas();

		self::add_customize_controls(); // customize manager


		self::google_analytics();
		self::google_page_level_ads();
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

	private static function wechat_img(){
		add_action('jk_body_start', function(){
			global $jk_utilities;
			echo $jk_utilities->frontend->wechat_image_html();
		});
	}

	private static function ios_icons() {
		add_action('wp_head', function(){
			$img_dir = get_template_directory_uri() . '/img/';
			?>
				<link rel="apple-touch-icon" sizes="57x57" href="<?php echo $img_dir; ?>ios-icon-57x57.png" />
				<link rel="apple-touch-icon" sizes="72x72" href="<?php echo $img_dir; ?>ios-icon-72x72.png" />
				<link rel="apple-touch-icon" sizes="114x114" href="<?php echo $img_dir; ?>ios-icon-114x114.png" />
				<link rel="apple-touch-icon" sizes="144x144" href="<?php echo $img_dir; ?>ios-icon-144x144.png" />
			<?php
		});
	}

	private static function google_analytics() {
		add_action('jk_body_start', function() {
			?>
				<!-- Google Analytics -->
				<script>
				  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
				  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
				  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
				  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

				  ga('create', 'UA-38993331-3', 'auto');
				  ga('send', 'pageview');

				</script>
			<?php
		});
	}

	private static function google_page_level_ads(){
		add_action('wp_head', function(){
			?>
			<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
			<script>
				(adsbygoogle = window.adsbygoogle || []).push({
					google_ad_client: "ca-pub-0919081176944377",
					enable_page_level_ads: true
				});
			</script>
			<?php
		});
	}
}