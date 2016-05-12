<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class JKMetaBoxes {

	public function __construct() {
		require_once( 'metabox/class-jk-metabox-external-audio-track.php' );
        require_once( 'metabox/class-jk-metabox-wechat-image.php');

		add_action('add_meta_boxes', array( $this, 'add_meta_boxes' ), 10);
		add_action('save_post', array($this, 'save_meta_boxes'), 1, 2);

		// save ximalaya track info
		add_action('jk_process_post_meta', 'JKMetaBoxExternalAudioTrack::save', 10, 2);
        add_action('jk_process_post_meta', 'JKMetaBoxWechatImage::save', 10, 2);
        add_action('jk_process_page_meta', 'JKMetaBoxWechatImage::save', 10, 2);
	}

	public function add_meta_boxes() {
		// ximalaya audio meta field
		
		$screen = get_current_screen();

		if ($screen->post_type == 'post') {

            JKMetaBoxExternalAudioTrack::init();
			add_meta_box(JKMetaBoxExternalAudioTrack::$id, '喜馬拉雅或唱吧聲音鏈接', 'JKMetaBoxExternalAudioTrack::output', 'post', 'normal', 'high');

            JKMetaBoxWechatImage::script();
            add_meta_box( JKMetaBoxWechatImage::$id, '微信分享的小图片', 'JKMetaBoxWechatImage::output', 'post', 'normal', 'high');
		}

        if ($screen->post_type == 'page') {
            JKMetaBoxWechatImage::script();
            add_meta_box( JKMetaBoxWechatImage::$id, '微信分享的小图片', 'JKMetaBoxWechatImage::output', 'page', 'normal', 'high');
        }
	}

	public function save_meta_boxes( $post_id, $post ) {
		// $post_id and $post are required
        if ( empty( $post_id ) || empty( $post ) ){
            return;
        }

        // Don't save meta boxes for revisions or autosaves
        if ( defined( 'DOING_AUTOSAVE' ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
            return;
        }

        // Check nonce
        if ( empty( $_POST['jk_meta_nonce'] ) || ! wp_verify_nonce( $_POST['jk_meta_nonce'], 'jk_save_data' ) ) {
            return;
        }

        // Check the post being saved == the $post_id to prevent triggering this call for other save_post events
        if ( empty( $_POST['post_ID'] ) || $_POST['post_ID'] != $post_id ) {
            return;
        }

        // Check user has permission to edit
        if ( ! current_user_can( "edit_{$post->post_type}s" ) ) {
            return;
        }

        // Check the post type
        do_action( "jk_process_{$post->post_type}_meta", $post_id, $post );

	}

}