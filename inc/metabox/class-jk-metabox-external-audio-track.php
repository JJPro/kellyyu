<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class JKMetaBoxExternalAudioTrack {

	public static $id = 'external-audio-track';

	public static function init(){
		self::scripts();
	}

	private static function scripts() {
		add_action('admin_footer', function(){
			/* Styles */

			/* Scripts */
			?>
				<script>
					(function($){
						/**
						 * hide and show elements according to selection
						 */
						var $enabled = $('#external-track-enabled'),
							$data_panel = $('#data-panel');
							$provider = $data_panel.find('#provider'),

							$url = $data_panel.find('#data-url'),
							$title = $data_panel.find('#data-title'),
							$mp3 = $data_panel.find('#data-mp3'),
							$cover = $data_panel.find('#data-cover');

						$enabled.on('change', function(){
							$data_panel.toggle();
						});

						update_panel();

						$provider.on('change', function(){
							update_panel();
						});

						function update_panel(){
							$provider_val = $provider.val();

							if ($provider_val == 'ximalaya') {
								$url.show();
								$title.hide();
								$mp3.hide();
								$cover.hide();
							} else if ($provider_val == 'changba') {
								$url.show();
								$title.hide();
								$mp3.show();
								$cover.hide();
							} else if ($provider_val == 'qingting') {
								$url.hide();
								$title.show();
								$mp3.show();
								$cover.show();
							} else {
								$url.hide();
								$title.hide();
								$mp3.hide();
								$cover.hide();
							}
						}

						/**
						 * MEDIA UPLOADER
						 */
						var frame,
						      metaBox = $('#<?php echo self::$id; ?>.postbox'), // Your meta box id here
						      addImgLink = metaBox.find('.upload-external-track-cover'),
						      delImgLink = metaBox.find( '.delete-external-track-cover'),
						      imgContainer = metaBox.find( '#data-cover img'),
						      imgIdInput = metaBox.find( '#external-track-cover' );
						  
						  // ADD IMAGE LINK
						  addImgLink.on( 'click', function( event ){
						    
						    event.preventDefault();
						    open_media_manager();
						    
						  });

						  // UPDATE IMAGE ON CLICKING ON EXISTING IMAGE
						  imgContainer.on('click', function( event ){
						  	open_media_manager();
						  });
						  
						  
						  // DELETE IMAGE LINK
						  delImgLink.on( 'click', function( event ){

						    event.preventDefault();

						    // Clear out the preview image
						    imgContainer.html( '' );

						    // Un-hide the add image link
						    addImgLink.removeClass( 'hidden' );

						    // Hide the delete image link
						    delImgLink.addClass( 'hidden' );

						    // Delete the image id from the hidden input
						    imgIdInput.val( '' );

						  });

						  function open_media_manager(){
						  	// If the media frame already exists, reopen it.
						    if ( frame ) {
						      frame.open();
						      return;
						    }
						    
						    // Create a new media frame
						    frame = wp.media({
						      title: 'Select or Upload Cover Image',
						      button: {
						        text: 'Use this image'
						      },
						      multiple: false  // Set to true to allow multiple files to be selected
						    });

						    
						    // When an image is selected in the media frame...
						    frame.on( 'select', function() {
						      
						      // Get media attachment details from the frame state
						      var attachment = frame.state().get('selection').first().toJSON();

						      // Send the attachment URL to our custom image input field.
						      imgContainer.find('img').attr( 'src', attachment.url);

						      // Send the attachment id to our hidden input
						      imgIdInput.val( attachment.id );

						      // Hide the add image link
						      addImgLink.addClass( 'hidden' );

						      // Unhide the remove image link
						      delImgLink.removeClass( 'hidden' );
						    });

						    // Finally, open the modal on click
						    frame.open();
						  }
						
					})(jQuery);

				</script>

			<?php 
		}, 100);
	}

	public static function output($post) {
		wp_nonce_field( 'jk_save_data', 'jk_meta_nonce' );

		$enabled = get_post_meta( $post->ID, '_has_external_track', true);
		$provider = get_post_meta($post->ID, '_external_track_provider', true);

		$track_url = get_post_meta($post->ID, '_external_track_url', true);
		$track_url = $track_url ? $track_url : '';

		$track_data = get_post_meta($post->ID, '_external_track_data', true);
		$track_mp3 = $track_data ? $track_data['mp3'] : ''; 
		$track_cover = $track_data ? $track_data['cover'] : '';
		$track_title = $track_data ? $track_data['title'] : '';

		
		?>
		<div class="hide-if-no-js">
			
			<p>
				<input type="checkbox" id="external-track-enabled" name="have_external_track" value="1" <?php checked($enabled, true); ?> />
				<label for="external-track-enabled">站外音频链接</label>
			</p>
			<div id="data-panel" style="margin-left: 25px; display: <?php echo $enabled ? 'block' : 'none'; ?>;">
				<label for="provider">音频来源: </label>
				<select id="provider" name="provider">
					<option value="0" <?php selected($provider, '0'); ?>>请选择...</option>
					<option value="ximalaya" <?php selected($provider, 'ximalaya'); ?>>喜马拉雅FM</option>
					<option value="changba" <?php selected($provider, 'changba'); ?>>唱吧</option>
					<option value="qingting" <?php selected($provider, 'qingting'); ?>>其他...</option>
				</select>
				
				<p id="data-url">
					<label for="external-track-url">链接地址：</label>
					<input type="text" id="external-track-url" name="external_track_url" value="<?php echo $track_url; ?>" />
				</p>
				<p id="data-title">
					<label for="external-track-title">音频名称：</label>
					<input type="text" id="external-track-title" name="external_track_title" value="<?php echo $track_title; ?>" />
				</p>
				<p id="data-mp3">
					<label for="external-track-mp3">音频mp3地址：</label>
					<input type="text" id="external-track-mp3" name="external_track_mp3" value="<?php echo $track_mp3; ?>" />
				</p>
				<p id="data-cover">

					<?php // ** media uploader ** //
						$upload_link = esc_url( get_upload_iframe_src() );
						$img_id = $track_cover;
						$img_src = wp_get_attachment_image_src( $img_id ); 
						$img_src = $img_src ? $img_src[0] : '';
					?>
					<label>专辑封面：</label>
					<a class="upload-external-track-cover button <?php if ( $img_src  ) { echo 'hidden'; } ?>" href="<?php echo $upload_link ?>">
				        <?php _e('设置专辑封面') ?>
				    </a>
				    <a class="delete-external-track-cover button <?php if ( ! $img_src  ) { echo 'hidden'; } ?>" 
				      href="#">
				        <?php _e('删除专辑封面') ?>
				    </a>

				    <!-- A hidden input to set and post the chosen image id -->
					<input id="external-track-cover" name="external_track_cover" type="hidden" value="<?php echo esc_attr( $img_id ); ?>" />

					<img src="<?php echo $img_src; ?>" style="max-width: 300px;" />
					
					
				</p>
			</div> <!-- #data-inputs -->
		</div><!-- .hide-if-no-js -->

		<?php
	}

	public static function save($post_id, $post) {
		$enabled = isset($_POST['have_external_track']) ? '1' : '0'; 


		// error_log($enabled); error_log($track_url);

		update_post_meta($post_id, '_has_external_track', $enabled);

		if ($enabled) {
			$provider = isset($_POST['provider']) ? $_POST['provider'] : ''; 

			if ($provider && $provider != '0') {
				update_post_meta($post_id, '_external_track_provider', $provider);

				$track_url = isset($_POST['external_track_url']) ? $_POST['external_track_url'] : '';
				// verify the url to conform to http(s) protocol, affix http:// if not
				if ( stripos($track_url, 'http') !== 0 ) $track_url = 'http://' . $track_url;
				$old_track_url = get_post_meta($post_id, '_external_track_url', true);

				$track_mp3 = isset($_POST['external_track_mp3']) ? $_POST['external_track_mp3'] : '';
				$track_title = isset($_POST['external_track_title']) ? $_POST['external_track_title'] : '';
				$track_cover = isset($_POST['external_track_cover']) ? $_POST['external_track_cover'] : '';

				switch ($provider) {
					case 'ximalaya':
						if ($old_track_url != $track_url) {
							update_post_meta($post_id, '_external_track_url', $track_url);
					
							$data = self::fetch_ximalaya_track_data($track_url);

							update_post_meta($post_id, '_external_track_data', $data);
						}
						break;

					case 'changba':
						if ($old_track_url != $track_url) {
							update_post_meta($post_id, '_external_track_url', $track_url);
					
							$data = self::fetch_changba_track_data($track_url, $track_mp3);

							update_post_meta($post_id, '_external_track_data', $data);
						}
						break;

					case 'qingting': // ** qingting and any other providers 
						update_post_meta($post_id, '_external_track_url', '');
						$data = array(
							'title' => $track_title,
							'cover' => $track_cover,
							'mp3'   => $track_mp3
						);
						update_post_meta($post_id, '_external_track_data', $data);
						break;
						
					default:
						# code...
						break;
				}
			}
		}

	}


	/*private static function fetch_external_track_data($url, $mp3 = false) {
		if ( stripos($url, 'ximalaya.com') !== false ) 
			return self::fetch_ximalaya_track_data($url);
		elseif ( stripos($url, 'changba.com') !== false )
			return self::fetch_changba_track_data($url, $mp3);
	}
*/

	private static function fetch_ximalaya_track_data($url) {
		require_once(get_template_directory() . '/inc/lib/simple_html_dom.php');

		$needle = '';

		if ( stripos($url, 'www.ximalaya.com') !== false )
			$needle = 'www.ximalaya.com';
		else 
			$needle = 'ximalaya.com';
		$mobile_url = str_replace( $needle, 'm.ximalaya.com', $url );

		$dom = file_get_html($mobile_url);

		if ($dom == false) {
			add_action('admin_notices', 'ximalaya_error_handler');

			function ximalaya_error_handler(){
				$message = '<div id="message" class="error">
								<p>Error saving 喜馬拉雅FM, check the track URL and save again.</p>
							</div>';
				echo $message;
				remove_action('admin_notices', __FUNCTION__);
			}

			return;
		}

		$container = $dom->find('.container', 0);
		$info_panel = $container->find('div.pl-info-panel', 0);

		$cover = $container->find('.pl-img img.abs', 0)->src;
		$title = $info_panel->find('.pl-info .pl-name', 0)->innertext;
		$mp3 = $info_panel->find('.j-track', 0)->sound_url;

		$data = array(
			'cover' => $cover,
			'title' => $title,
			'mp3'   => $mp3 
		);

		return $data;
	}

	private static function fetch_changba_track_data($url, $mp3) {
		require_once(get_template_directory() . '/inc/lib/simple_html_dom.php');

		$dom = file_get_html($url);

		if ($dom == false) {
			add_action('admin_notices', 'changba_error_handler');

			function changba_error_handler(){
				$message = '<div id="message" class="error">
								<p>Error saving 唱吧, check the track URL and save again.</p>
							</div>';
				echo $message;
				remove_action('admin_notices', __FUNCTION__);
			}

			return;
		}

		$player = $dom->find('#doc #bd .widget-player', 0);

		$title = $player->find('.title', 0)->innertext;
		$cover = $player->find('.player-obj img', 0)->src;

		error_log($mp3);

		$data = array(
			'cover' => $cover,
			'title' => $title,
			'mp3'   => $mp3 
		);

		return $data;
	}	


	

}