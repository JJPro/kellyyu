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

		return "$date / $cats_string / $comment_count";
	}

	public function is_user_from_mainland_china() {
		$origin = geolocator_country();
		return $origin == '86';
	}

	// ** queried in the audio post format ** //
	public function has_ximalaya_audio(){
		$enabled = get_post_meta(get_the_ID(), '_has_ximalaya_track', true);

		return ($enabled == true);
	}

	public function get_ximalaya_audio_html(){
		
		$data = $this->get_ximalaya_audio_data();

		$html = '<div class="ximalaya-audio">
					<div class="row no-gutter">
						<div class="col-xs-4 no-padding-left">
							<div class="album-cover-container">
								<div class="album-cover background-image" style="background-image:url(' . $data['cover'] . ');">
								</div>
							</div>
						</div>
						<div class="col-xs-8 no-padding-right">
							<div class="title">
								<h4>' . $data['title'] . '</h4>
							</div>
							<div class="player">
								<audio src="' . $data['mp3'] . '" preload="auto" />
							</div>
						</div>
					</div>
				</div>';
		return $html;

	}

	// ** Fetches [album image url, mp3 file url, and track name] ** //
	private function get_ximalaya_audio_data(){

		$post_id = get_the_ID();

		$data = get_post_meta($post_id, '_ximalaya_track_data', true);

		return $data;

	}

}


global $jk_utilities;
$jk_utilities = new JKUtilities();