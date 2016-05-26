<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class JKThemeSetup {

	public static function init() {
		global $jk_utilities;

		self::user_caps();

		self::theme_supports();
		self::add_sidebars();
		self::customize_title_tag(); // <title> in <head> element
		self::register_nav_menus();
		self::append_search_box_to_primary_menu();
		self::wechat_img();
		self::ios_icons();
		self::widgets();

		self::embeds();
		self::shortcodes();
		self::post_metas(); // meta-boxes
		self::short_nav_text();

		self::add_customize_controls(); // customize manager

		if (! ($jk_utilities->frontend->is_user_from_mainland_china()) ) {
			self::youtube_short_url_oembed_provider();
		}

		self::facebook_integration();
		self::weibo_integration();
		self::google_integration();


		self::rss();

		// ** corner cases ** //
		// swap instagram js for chinese users
		self::swap_instagram_js();


		// ** Posts ** //
		self::hide_unlisted_cat_from_home();
		self::the_more_link();
	}

	private static function user_caps(){
		// give contributor ability to upload files
		$role = get_role( 'contributor' );

		$role->add_cap( 'upload_files' );
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
			add_theme_support('post-formats', array( /* 'aside', 'gallery', */ 'image', 'status', 'video', 'audio'));
			add_theme_support('post-thumbnails');
			/*
			add_theme_support('infinite-scroll', array(
				'container' => 'main',
				'footer' => 'colophon',
				'type' => 'click'
			));
			*/
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



	private static function widgets() {
		require_once('class-jk-social-widget.php');
		require_once('class-jk-like-button-widget.php');
		add_action('widgets_init', function(){
			register_widget('JKSocialWidget');
			register_widget('JKLikeButtonWidget');

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

	private static function youtube_short_url_oembed_provider(){
		add_action('init', function(){
			$endpoint = 'https://www.youtube.com/oembed/';

			wp_oembed_add_provider( '/https?://youtu.be/.*/i', $endpoint, true );
		});
	}

	private static function short_nav_text() {
		add_filter('previous_post_link', 'add_title_tooltip', 10, 3);

		add_filter('next_post_link', 'add_title_tooltip', 10, 3);

		// Ellipsis is controlled by CSS, which is awesome
		add_action('wp_footer', function(){
			?>
			<style>
				.nav-links .nav-previous,
				.nav-links .nav-next {
					max-width: 48%;
				}

				.nav-links .nav-previous span,
				.nav-links .nav-next span {
					display: inline-block;
					max-width: 100%;
					white-space: nowrap;
					overflow: hidden;
					text-overflow: ellipsis;
				}
			</style>
			<?php
		});

		function add_title_tooltip($output, $format, $link){

			preg_match('/(?:<a[^>]+>)(.*)(?=<\/a>)/i', $output, $matches);

			if ($matches) {
				$title = $matches[1];

				$title_with_tooltip = '<span data-toggle="tooltip" title="' . $title . '">' . $title . '</span>';

				$output = preg_replace('/(<a[^>]+>)(.*)(?=<\/a>)/i', '$1' . $title_with_tooltip, $output);
			}

			return $output;
		}
	}

	private static function weibo_integration(){
		add_action( 'wp_head', function(){
			?>
			<html xmlns:wb=“http://open.weibo.com/wb”>
			<script src="http://tjs.sjs.sinajs.cn/open/api/js/wb.js" type="text/javascript" charset="utf-8"></script>
			<?php
		});
	}

	private static function rss() {
		add_filter('the_content_feed', function($content){
			return apply_filters('the_content', $content);
		});
	}

	private static function swap_instagram_js(){

		add_action( 'wp_enqueue_scripts', function(){

			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			global $jk_utilities;

			// check existence of plugin && user from mainland china
			if ( is_plugin_active('instagram-feed-pro/instagram-feed.php') ){
//			if (is_plugin_active('instagram-feed/instagram-feed.php')){
//				error_log('instagram-feed is active');

				if ($jk_utilities->frontend->is_user_from_mainland_china()){

//					error_log('this is mainland china, localizing script ...');
					$proxy_script_url = get_template_directory_uri() . '/inc/instagram-proxy/instagram-proxy.php';
					// localize with 'request' proxy processing url
					wp_localize_script('sb_instagram_scripts', 'jk_proxy_vars', array('proxy_script_url' => $proxy_script_url));

				}
			}

		});


	}

	private static function hide_unlisted_cat_from_home(){
		add_filter('pre_get_posts', function($query){
			if ( $query->is_home ){
				$unlisted_cat = get_category_by_slug('unlisted');
				if ($unlisted_cat){
					$query->set('cat', '-' . $unlisted_cat->term_id);
				}
			}
			return $query;
		});
	}

	private static function facebook_integration(){
		require_once('class-jk-facebook-integration.php');
		new \inc\JKFacebookIntegration();
	}

	private static function google_integration(){
		require_once('class-jk-google-integration.php');
		new \inc\JKGoogleIntegration();
	}

	private static function the_more_link(){
		add_filter( 'the_content_more_link', function( $link ){
			return '<span class="more-link-ellipsis">......</span>' . $link;
		} );
	}
}