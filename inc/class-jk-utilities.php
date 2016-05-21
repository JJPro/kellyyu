<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class JKUtilities {

	public $admin;
	public $frontend;

	public function __construct() {
		$this->admin = new JKAdminUtilities();
		$this->frontend = new JKFrontendUtilities();
	}

}

class JKAdminUtilities {

	/** String: 
	 * height/width % 
	 */
	public function get_header_img_width_height_rate() {
		$rate = get_custom_header()->height / get_custom_header()->width;
		$rate = sprintf("%.2f%%", $rate * 100);
		return $rate;
	}

	/**
	 * Count views of the image wall page 
	 */
	public function get_post_views($post) {
		$views = get_post_meta($post, '_jk_views_count', true);
		$views = $views ? $views : '0';
		
		return $views;
	}

	/**
	 * increase image wall views count 
	 */ 
	public function increase_post_views($post) {
		$views = $this->get_post_views($post);
		update_post_meta($post, '_jk_views_count', $views + 1 );
	}

}

class JKFrontendUtilities {

	// ** Inside the Loop ** //
	public function posted_meta() {
		$date = get_the_date();
		$comment_count = get_comments_number() . ' comments';
		$cats  = get_the_category();

		$i = 0; $separator = ', '; $cats_string = '';
		foreach( $cats as $cat ) {
			if ($i > 0)
				$cats_string .= $separator;
			$cats_string .= '<a href="' . esc_url(get_category_link($cat->term_id)) . '" alt="' . esc_attr('View all posts in %s', $cat->name) . '">' . esc_html($cat->name) . '</a>';
			$i++;
		}

		return "$date / $cats_string"; // removed $comment_count
	}

	public function wechat_image_html(){

		$img_url = '';

		// on the blog page
		if ( is_home() || is_front_page() ){
			// site icon
			$img_url = get_site_icon_url();

		} elseif ( is_single() || is_page() ) {
			// post wechat img
			$post = get_post();

			$img_id = get_post_meta( $post->ID, '_wechat_img', true);

			$img_url = wp_get_attachment_image_src( $img_id, 'full' );
			if ($img_url) {
				$img_url = $img_url[0];
			}

		}


		$html = '<div style="display: none;">
					<img src="' . $img_url . '">
				</div>';

		return $html;
	}

	public function facebook_app_id_meta(){
		return '<meta property="fb:app_id" content="879068418870248" />';
	}

	public function facebook_image_html(){
		if ( is_home() || is_front_page() ){
			// site icon
			$img_url = get_site_icon_url();

		} else {
			// use page thumbnail if exists
			$img_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
			if ($img_url) {
				$img_url = $img_url[0];
			}
		}

		if ($img_url) {
//			error_log('facebook_img: ' . $img_url);
			return '<link rel="image_src" href="' . $img_url . '" />';
		}
	}

	public function facebook_share_meta(){

		// URL
		$url = home_url( $_SERVER['REQUEST_URI'] );

		// Title
		if ( is_home() || is_front_page() ) {
			$title = get_bloginfo('name');
		} elseif ( is_archive() ) {
			$title = get_bloginfo('name') . ' &raquo; ' . get_the_archive_title();
		} elseif ( is_single() ) {
			$title = get_bloginfo('name') . ' &raquo; ' . get_the_title();
		}

		// Description
		if ( is_home() || is_front_page() ) {
			$description = get_bloginfo('description');
		} elseif ( is_archive() ) {
			$description = get_the_archive_description();
		} elseif ( is_single() ) {
			$description = get_the_excerpt();
		}

		?>
		<meta property="og:url" content="<?php echo $url; ?>" />
		<meta property="og:title" content="<?php echo $title; ?>" />
		<meta property="og:description" content="<?php echo $description; ?>" />
		<?php
	}

	// ** unused ** //
	public function featured_image_html(){
		if ( is_home() || is_front_page() ){
			// site icon
			$img_url = get_site_icon_url();

		} else {
			// use page thumbnail if exists
			$img_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
			if ($img_url) {
				$img_url = $img_url[0];
			}
		}

		if ($img_url) {
			return '<div class="featured-image" style="position: absolute; left: -10000px;"><img src="' . $img_url . '"/></div>';
		}
	}


	public function is_user_from_mainland_china() {
		static $result = null;

		if ($result === null ) {

			if ( isset($_GET['youku']) )
				$result = true;
			else{
				$origin = geoip_detect2_get_info_from_current_ip(NULL);
				$result = ($origin->country->isoCode == 'CN');
			}
		}

		return $result;
	}

	// ** queried in the audio post format ** //
	// ** for ximalaya and changba audio ** //
	public function has_external_audio(){
		$enabled = get_post_meta(get_the_ID(), '_has_external_track', true);

		return ($enabled);
	}


	// ** ximalaya and changba ** //
	public function get_external_audio_html(){
		

		$data = $this->get_audio_data();

		$title = $data['title'];
		$mp3 = $data['mp3'];
		$cover = is_numeric($data['cover']) ? wp_get_attachment_image_src( $data['cover'], 'full' )[0] : $data['cover'];

		// error_log(print_r($cover, true));

		$html = '<div class="external-audio">
					<div class="row no-gutter">
						<div class="col-xs-4 col-md-3 no-padding-left">
							<div class="album-cover-container">
								<div class="album-cover background-image" style="background-image:url(' . $cover . ');">
								</div>
							</div>
						</div>
						<div class="col-xs-8 col-md-9 no-padding-right">

							<div class="meta-container">
								<span class="play-pause jk-icon icon-play"></span>

								<span class="title">
									' . $title . '
								</span>
							</div>

						</div> <!-- .col-xs-8 -->
					</div> <!-- .row -->
					<div class="player col-sm-8 col-sm-offset-4">
						<audio src="' . $mp3 . '" preload="metadata" ></audio>
						
						<div class="row">
							<div class="col-xs-2 no-padding-right no-padding-left">
								<div class="current-time text-center">00:00</div>
							</div>
							<div class="col-xs-8 no-padding-left no-padding-right">
								<div class="scrubber">
									<div class="loaded"></div>
									<div class="cursor-move"></div>
									<div class="progress"></div>
									<div class="error-message text-center"></div>
								</div> 
							</div>
							<div class="col-xs-2 no-padding-left no-padding-right">
								<div class="duration text-center">00:00</div>
							</div>

						</div>

					</div> <!-- .player -->
				</div>';
		return $html;

	}

	/*public function get_ximalaya_audio_html(){
		
		$data = $this->get_ximalaya_audio_data();

		$html = '<div class="ximalaya-audio">
					<div class="row no-gutter">
						<div class="col-xs-4 col-md-3 no-padding-left">
							<div class="album-cover-container">
								<div class="album-cover background-image" style="background-image:url(' . $data['cover'] . ');">
								</div>
							</div>
						</div>
						<div class="col-xs-8 col-md-9 no-padding-right">

							<div class="meta-container">
								<span class="play-pause jk-icon icon-play"></span>

								<span class="title">
									' . $data['title'] . '
								</span>
							</div>

						</div> <!-- .col-xs-8 -->
					</div> <!-- .row -->
					<div class="player col-sm-8 col-sm-offset-4">
						<audio src="' . $data['mp3'] . '" preload="metadata" ></audio>
						
						<div class="row">
							<div class="col-xs-2 no-padding-right no-padding-left">
								<div class="current-time text-center">00:00</div>
							</div>
							<div class="col-xs-8 no-padding-left no-padding-right">
								<div class="scrubber">
									<div class="loaded"></div>
									<div class="cursor-move"></div>
									<div class="progress"></div>
									<div class="error-message text-center"></div>
								</div> 
							</div>
							<div class="col-xs-2 no-padding-left no-padding-right">
								<div class="duration text-center">00:00</div>
							</div>

						</div>

					</div> <!-- .player -->
				</div>';
		return $html;

	}*/

	// ** [album image url, mp3 file url, and track name] for audio from ximalaya or changba ** //
	private function get_audio_data(){

		$post_id = get_the_ID();

		$data = get_post_meta($post_id, '_external_track_data', true);

		return $data;

	}

	public function jk_post_thumbnail() {
		if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
			return;
		} ?>


		<div class="post-thumbnail background-image" style="background-image: url(<?php the_post_thumbnail_url(); ?>)">
		</div><!-- .post-thumbnail -->
		<?php
	}

}


global $jk_utilities;
$jk_utilities = new JKUtilities();