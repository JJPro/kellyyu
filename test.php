<?php
class Share_Facebook extends Sharing_Source {
	public $shortname = 'facebook';
	public $genericon = '\f204';
	private $share_type = 'default';

	public function __construct( $id, array $settings ) {
		parent::__construct( $id, $settings );

		if ( isset( $settings['share_type'] ) )
			$this->share_type = $settings['share_type'];

		if ( 'official' == $this->button_style )
			$this->smart = true;
		else
			$this->smart = false;
	}

	public function get_name() {
		return __( 'Facebook', 'jetpack' );
	}

	public function display_header() {
	}

	function guess_locale_from_lang( $lang ) {
		if ( 'en' == $lang || 'en_US' == $lang || !$lang ) {
			return 'en_US';
		}

		if ( !class_exists( 'GP_Locales' ) ) {
			if ( !defined( 'JETPACK__GLOTPRESS_LOCALES_PATH' ) || !file_exists( JETPACK__GLOTPRESS_LOCALES_PATH ) ) {
				return false;
			}

			require JETPACK__GLOTPRESS_LOCALES_PATH;
		}

		if ( defined( 'IS_WPCOM' ) && IS_WPCOM ) {
			// WP.com: get_locale() returns 'it'
			$locale = GP_Locales::by_slug( $lang );
		} else {
			// Jetpack: get_locale() returns 'it_IT';
			$locale = GP_Locales::by_field( 'wp_locale', $lang );
		}

		if ( ! $locale ) {
			return false;
		}

		if ( empty( $locale->facebook_locale ) ) {
			if ( empty( $locale->wp_locale ) ) {
				return false;
			} else {
				// Facebook SDK is smart enough to fall back to en_US if a
				// locale isn't supported. Since supported Facebook locales
				// can fall out of sync, we'll attempt to use the known
				// wp_locale value and rely on said fallback.
				return $locale->wp_locale;
			}
		}

		return $locale->facebook_locale;
	}

	public function get_display( $post ) {
		if ( $this->smart ) {
			$share_url = $this->get_share_url( $post->ID );
			$fb_share_html = '<div class="fb-share-button" data-href="' . esc_attr( $share_url ) . '" data-layout="button_count"></div>';
			/**
			 * Filter the output of the Facebook Sharing button.
			 *
			 * @module sharedaddy
			 *
			 * @since 3.6.0
			 *
			 * @param string $fb_share_html Facebook Sharing button HTML.
			 * @param string $share_url URL of the post to share.
			 */
			return apply_filters( 'jetpack_sharing_facebook_official_button_output', $fb_share_html, $share_url );
		}

		/** This filter is already documented in modules/sharedaddy/sharing-sources.php */
		if ( apply_filters( 'jetpack_register_post_for_share_counts', true, $post->ID, 'facebook' ) ) {
			sharing_register_post_for_share_counts( $post->ID );
		}
		return $this->get_link( $this->get_process_request_url( $post->ID ), _x( 'Facebook', 'share to', 'jetpack' ), __( 'Click to share on Facebook', 'jetpack' ), 'share=facebook', 'sharing-facebook-' . $post->ID );
	}

	public function process_request( $post, array $post_data ) {
		$fb_url = $this->http() . '://www.facebook.com/sharer.php?u=' . rawurlencode( $this->get_share_url( $post->ID ) ) . '&t=' . rawurlencode( $this->get_share_title( $post->ID ) );

		// Record stats
		parent::process_request( $post, $post_data );

		// Redirect to Facebook
		wp_redirect( $fb_url );
		die();
	}

	public function display_footer() {
		$this->js_dialog( $this->shortname );
		if ( $this->smart ) {
			$locale = $this->guess_locale_from_lang( get_locale() );
			if ( ! $locale ) {
				$locale = 'en_US';
			}
			/**
			 * Filter the App ID used in the official Facebook Share button.
			 *
			 * @since 3.8.0
			 *
			 * @param int $fb_app_id Facebook App ID. Default to 249643311490 (WordPress.com's App ID).
			 */
			$fb_app_id = apply_filters( 'jetpack_sharing_facebook_app_id', '249643311490' );
			if ( is_numeric( $fb_app_id ) ) {
				$fb_app_id = '&appId=' . $fb_app_id;
			} else {
				$fb_app_id = '';
			}
			?><div id="fb-root"></div>
			<script>(function(d, s, id) { var js, fjs = d.getElementsByTagName(s)[0]; if (d.getElementById(id)) return; js = d.createElement(s); js.id = id; js.src = '//connect.facebook.net/<?php echo $locale; ?>/sdk.js#xfbml=1<?php echo $fb_app_id; ?>&version=v2.3'; fjs.parentNode.insertBefore(js, fjs); }(document, 'script', 'facebook-jssdk'));</script>
			<script>
				jQuery( document.body ).on( 'post-load', function() {
					if ( 'undefined' !== typeof FB ) {
						FB.XFBML.parse();
					}
				} );
			</script>
			<?php
		}
	}
}
