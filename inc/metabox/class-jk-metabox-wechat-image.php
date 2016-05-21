<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class JKMetaBoxWechatImage {

	public static $id = 'wechat-img';

	public static function init(){
		self::script();
	}

	public static function script(){
		add_action('admin_footer', function(){
			?>

				<script>

					(function($){
						// Set all variables to be used in scope
						  var frame,
						      metaBox = $('#<?php echo self::$id; ?>.postbox'), // Your meta box id here
						      addImgLink = metaBox.find('.upload-wechat-img'),
						      delImgLink = metaBox.find( '.delete-wechat-img'),
						      imgContainer = metaBox.find( '.wechat-img'),
						      imgIdInput = metaBox.find( '.wechat-img-id' );
						  
						  // ADD IMAGE LINK
						  addImgLink.on( 'click', function( event ){
						    
						    event.preventDefault();
						    open_media_manager();
						    
						  });

						  // UPDATE IMAGE ON CLICKING ON EXISTING IMAGE
						  imgContainer.on('click', function( event ){
						  	console.log('hhhh');
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
						      title: 'Select or Upload Image Of Wechat Sharing',
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

		// Get WordPress' media upload URL
		$upload_link = esc_url( get_upload_iframe_src( 'image', $post->ID ) );
		$img_id = get_post_meta($post->ID, '_wechat_img', true);
		$img_src = wp_get_attachment_image_src( $img_id, 'full' ); // default is thumbnail size, change to 'full' if too small and doesn't show in wechat
		$img_src = $img_src ? $img_src[0] : '';
		
		?>
		
		<!-- image container -->
		<div class="wechat-img" style="display:inline-block;">
			<img src="<?php echo $img_src; ?>" style="max-width: 300px; "/>
		</div>
		
		<!-- add & remove image links -->
		<p class="hide-if-no-js">

			图片的高和宽至少要大于500px.
			<br>
		    <a class="upload-wechat-img button <?php if ( $img_src  ) { echo 'hidden'; } ?>" href="<?php echo $upload_link ?>">
		        <?php _e('设置微信图片') ?>
		    </a>
		    <a class="delete-wechat-img button <?php if ( ! $img_src  ) { echo 'hidden'; } ?>" 
		      href="#">
		        <?php _e('删除微信图片') ?>
		    </a>
		</p>

		<!-- A hidden input to set and post the chosen image id -->
		<input class="wechat-img-id" name="wechat-img-id" type="hidden" value="<?php echo esc_attr( $img_id ); ?>" />

		<?php
	}

	public static function save($post_id, $post) {
		$img_id = isset($_POST['wechat-img-id']) ? $_POST['wechat-img-id'] : '';

		update_post_meta($post_id, '_wechat_img', $img_id);

	}
	

}