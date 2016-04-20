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