<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class JKShortcodes {

	public function __construct() {
		$this->youku_youtube_mix();
		$this->text_mix();
		$this->views_count();
		$this->google_ads();
	}

	private function youku_youtube_mix(){ 
		add_shortcode( 'video_mix', function( $atts ) {
			$video = '';
			global $jk_utilities;
			$defaults = array(
				'youtube' => '',
				'youku' => '',
				'facebook' => '',
				'responsive_class' => 'embed-responsive-16by9',
			);
			$atts = shortcode_atts($defaults, $atts);

			if (isset($_GET['youku']) || $jk_utilities->frontend->is_user_from_mainland_china() ) { // for testing youku service from outside mainland
				$video = $atts['youku'];

				$matches = array();
				preg_match('/v_show\/id_(.*?).html/i', $video, $matches);
				$src = 'http://player.youku.com/embed/' . $matches[1];

				$embed_html = '<iframe width=800 height=450 src="' . esc_attr($src) . '" frameorder=0 allowfullscreen></iframe>';

			} else {
				$video = $atts['youtube'] ? $atts['youtube'] : $atts['facebook'];

				error_log( $video );

				$embed_html = wp_oembed_get( $video, array('width' => '800', 'height' => '450'));
				if ($video == $atts['youtube'])
					$embed_html = preg_replace('/(src="[^"]+)/i', '$1&rel=0&showinfo=0', $embed_html);
			}

			$embed_html = '<div class="embed-responsive ' . $atts['responsive_class'] . '">' . $embed_html . '</div>';
//			error_log($embed_html);
			return $embed_html;
		} );
	}

	private function text_mix() {
		add_shortcode( 'text_mix', function($atts){
			global $jk_utilities;
			$defaults = array(
				'china' => '',
				'other' => ''
			);
			$atts = shortcode_atts($defaults, $atts);

			if (isset($_GET['youku']) || $jk_utilities->frontend->is_user_from_mainland_china() ) { // for testing youku service from outside mainland
				$html = $atts['china'];
			} else {
				$html = $atts['other'];
			}

			return $html;
		});
	}

	private function views_count(){
		add_shortcode( 'jk_views', function( $atts ) {
			global $jk_utilities;
			if ( !current_user_can('administrator') )
				$jk_utilities->admin->increase_post_views(get_the_ID());
			$views = $jk_utilities->admin->get_post_views(get_the_ID());

			echo '<div class="page-views-container"><span class="page-views">' . $views . '</span></div>';

		});
	}

	private function google_ads() {
		add_shortcode( 'google_ads', function($atts){
			?>
			<div class="text-center">
				<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
				<!-- everkellyyuvideo - responsive -->
				<ins class="adsbygoogle"
					 style="display:block"
					 data-ad-client="ca-pub-0919081176944377"
					 data-ad-slot="1012846478"
					 data-ad-format="auto"></ins>
				<script>
					(adsbygoogle = window.adsbygoogle || []).push({});
				</script>
			</div>
			<?php
		});
	}
}