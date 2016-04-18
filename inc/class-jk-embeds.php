<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class JKEmbeds {

	public function __construct() {
		$this->neat_youtube();
		$this->responsive_vimeo();
		$this->youku_video();
		// $this->ximalaya();
	}

	// ** YouTube video without title ** //
	private function neat_youtube() {	
		add_filter('oembed_result', function($html, $src, $args){


			if ( strpos( $src, 'www.youtube.com') != false ) {
				$html = preg_replace( '/(src=(\S+))(?=")/', '$1&showinfo=0', $html);
				$html = '<div class="embed-responsive embed-responsive-16by9">' . $html . '</div>';
			}

			return $html;
		}, 10, 3);
	}

	private function responsive_vimeo() {
		add_filter('oembed_result', function($html, $src, $args){
			if ( strpos( $src, 'vimeo.com') != false ){
				$html = preg_replace( '/(src=(\S+))(?=")/', '$1?title=0&byline=0', $html);
				$html = '<div class="embed-responsive embed-responsive-16by9">' . $html . '</div>';
			}
			return $html;
		}, 10, 3);
	}

	// ** Embed YouKu video responsive ** //
	private function youku_video() {
		wp_embed_register_handler( 'embed_handler_youku', '#http://v.youku.com/v_show/id_(.*?).html#i', function($matches, $attr, $url, $rawattr){
			// error_log(print_r($matches, true));error_log(print_r($attr, true));error_log(print_r($url, true));error_log(print_r($rawattr, true));

			$src = 'http://player.youku.com/embed/' . $matches[1];

			if ( !empty($rawattr['width']) && !empty($rawattr['height']) ) {
				$width  = (int) $rawattr['width'];
				$height = (int) $rawattr['height'];
			} else {
				list( $width, $height ) = wp_expand_dimensions( 622, 350, $attr['width'], $attr['height'] );
			}

			$html = '<div class="embed-responsive embed-responsive-16by9"><iframe height=' . esc_attr($height) . ' width=' . esc_attr($width) . ' src="' . esc_attr($src) . '" frameborder=0 allowfullscreen></iframe></div>';

			return apply_filters( 'embed_youku', $html, $matches, $attr, $url, $rawattr);
		});
	}

	// ** Embed Ximalaya audio ** //
	/*private function ximalaya() {
		wp_embed_register_handler( 'embed_handler_ximalaya', '/http:\/\/.*ximalaya.com\/\d+\/sound\/\d+/i', function($matches, $attr, $url, $rawattr){
//			 error_log(print_r($matches, true));error_log(print_r($attr, true));error_log(print_r($url, true));error_log(print_r($rawattr, true));

			global $jk_utilities;

			$html = $jk_utilities->frontend->get_ximalaya_audio_html( $matches[0] );

			return apply_filters('embed_ximalaya', $html, $matches, $attr, $url, $rawattr);
		});
	}*/
}