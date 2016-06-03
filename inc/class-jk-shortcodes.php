<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class JKShortcodes {

	public function __construct() {
		$this->video_mix();
		$this->text_mix();
		$this->jk_views(); // views count
		$this->jk_playlist();
		$this->google_ads();
	}

	private function video_mix(){ 
		add_shortcode( 'video_mix', function( $atts ) {
			global $jk_utilities;
			$defaults = array(
				'youku' => '',
				'qq' => '',
				'youtube' => '',
				'facebook' => '',
				'responsive_class' => 'embed-responsive-16by9',
				'container_max_width' => '',
			);
			$atts = shortcode_atts($defaults, $atts);

			if ( $jk_utilities->frontend->is_user_from_mainland_china() ) {
				$video = $atts['youku'] ? $atts['youku'] : $atts['qq'];
			} else {
				$video = $atts['youtube'] ? $atts['youtube'] : $atts['facebook'];
			}

			// Check for a cached result (stored in the post meta)
			$key_suffix = md5( $video . $atts['responsive_class'] . $atts['container_max_width'] );
			$cachekey = '_oembed_' . $key_suffix;
			$cachekey_time = '_oembed_time_' . $key_suffix;
			$post_ID = get_the_ID();

			/**
			 * Filter the oEmbed TTL value (time to live).
			 *
			 * Default is 2 days.
			 */
			$ttl = apply_filters( 'oembed_ttl', 2 * DAY_IN_SECONDS, $video, false, $post_ID );

			$cache = get_post_meta( $post_ID, $cachekey, true );
			$cache_time = get_post_meta( $post_ID, $cachekey_time, true);

			if (! $cache_time ) {
				$cache_time = 0;
			}

			$cached_recently = ( time() - $cache_time ) < $ttl; // bool

			if ($cached_recently) {
				if (! empty( $cache ) ) {
					return $cache;
				}
			}

			// If any of the above fails, get the HTML from the oEmbed provider
			if ( $video == $atts['youku'] ) {
				$matches = array();
				preg_match('/v_show\/id_(.*?).html/i', $video, $matches);

				if ($matches) {
					// compose the HTML
					$src = 'http://player.youku.com/embed/' . $matches[1];
					$embed_html = '<iframe width=800 height=450 src="' . esc_attr($src) . '" frameorder=0 allowfullscreen></iframe>';
				}

			} elseif ( $video == $atts['qq'] ) {
				$matches = array();
				if ( stripos($video, 'vid=') !== false ){
					preg_match( '/(?<=vid=)\w+/', $video, $matches );
				} else {
					preg_match( '/[^\/]+(?=\.html)/', $video, $matches );
				}

				if ($matches) {
					// compose the HTML
					$vid = $matches[0];
					$embed_html = '<iframe frameborder="0" width="640" height="498" src="http://v.qq.com/iframe/player.html?vid=' . $vid . '&tiny=0&auto=0" allowfullscreen></iframe>';
				}
			} elseif ( $video == $atts['facebook'] ) {
				$embed_html = wp_oembed_get( $video, array('width' => '800', 'height' => '450'));
			} else { // youtube
				$embed_html = wp_oembed_get( $video, array('width' => '800', 'height' => '450'));
				$embed_html = preg_replace('/(src="[^"]+)/i', '$1&rel=0&showinfo=0', $embed_html);
			}

			$embed_html = '<div class="embed-responsive ' . $atts['responsive_class'] . '">' . $embed_html . '</div>';

			if ( $atts['container_max_width'] ){
				$embed_html = '<div class="embed-container" style="max-width: ' . $atts['container_max_width'] . 'px;">' . $embed_html . '</div>';
			}



			// Cache the result
			update_post_meta( $post_ID, $cachekey, $embed_html );
			update_post_meta( $post_ID, $cachekey_time, time() );

			// return the result
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

	private function jk_views(){
		add_shortcode( 'jk_views', function( $atts ) {
			global $jk_utilities;
			if ( !current_user_can('administrator') )
				$jk_utilities->admin->increase_post_views(get_the_ID());
			$views = $jk_utilities->admin->get_post_views(get_the_ID());

			return '<span class="page-views-container"><span class="page-views">' . $views . '</span></span>';
		});
	}

	private function google_ads() {
		add_shortcode( 'google_ads', function($atts){
			return '<div class="text-center">
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
					</div>';
		});
	}

	private function jk_playlist() {
		add_shortcode( 'jk_playlist', function( $atts, $content='' ) {
			global $jk_utilities;

			// do nothing if input is empty
			if ( empty( $content ) )
				return '';

			// Compose playlist
			$playlist = array();
			$ids = array_map( 'trim', explode(',', $content) );

			foreach( $ids as $post_id ){
				// get mp3, title, cover from the post
				if ( $jk_utilities->frontend->has_external_audio( $post_id ) ){

					$data = $jk_utilities->frontend->get_audio_data($post_id);

					if ($data){
						$title = $data['title'];
						$mp3 = $data['mp3'];
						$cover = is_numeric($data['cover']) ? wp_get_attachment_image_src( $data['cover'], 'full' )[0] : $data['cover'];

						$playlist[] = array(
							'mp3' => $mp3,
							'title' => $title,
							'cover' => $cover,
							'artist' => 'KELLY于文文',
						);
					}
				}
			}

			$swfPath = get_template_directory_uri() . '/js/lib/ttw-music-player/jquery-jplayer';
			$output = '<div class="jk_playlist" data-playlist-id="' . get_the_ID() . '" data-swfPath="' . $swfPath . '"></div>';
			$output .= '<script>var playlist_' . get_the_ID() . ' = ' . json_encode($playlist) . ';</script>';

			// error_log(print_r($playlist, true));
			// error_log($output);

			return $output;


		});

	}
}