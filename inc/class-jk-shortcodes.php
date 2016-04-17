<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class JKShortcodes {

	public function __construct() {
		$this->youku_youtube_mix();
	}

	private function youku_youtube_mix(){ 
		add_shortcode( 'video_mix', function( $atts ) {
			$video = '';
			global $jk_utilities;
			if ($jk_utilities->frontend->is_user_from_mainland_china())
				$video = $atts['youku'];
			else 
				$video = $atts['youtube'];

			return apply_filters( 'the_content', $video);
		} );
	}

}