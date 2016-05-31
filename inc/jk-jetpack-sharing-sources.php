<?php
/**
 * Additional sharing sources to Jetpack sharing
 */
class Share_Wechat extends Sharing_Source {

    public function __construct($id, array $settings) {
        parent::__construct($id, $settings);


        if ( 'official' == $this->button_style )
            $this->smart = true;
        else
            $this->smart = false;
    }

    public function get_name(){
        return '微信';
    }

    public function display_footer(){
    }

    public function display_header(){
    }

    /**
     * Output of the sharing button. return, DO NOT echo.
     * @param $post
     * @return string
     */
    public function get_display($post)
    {
        return $this->get_link( $this->get_process_request_url( $post->ID ), '微信', '点击以分享到微信', 'share=wechat', 'sharing-wechat-' . $post->ID );
    }

    /**
     * Return of Ajax request
     * @param $post
     * @param array $post_data
     */
    public function process_request($post, array $post_data)
    {
        $qr_code_url = get_template_directory_uri() . '/inc/share-to-wechat.php';
        $qr_code_url .= '?url=' . get_permalink( $post );

        // Record stats
        parent::process_request($post, $post_data);

        // Redirect to QR Code script
        wp_redirect( $qr_code_url );
        die();
    }


}

class Share_Sina_Weibo extends Sharing_Source {

    public function __construct($id, array $settings) {
        parent::__construct($id, $settings);


        if ( 'official' == $this->button_style )
            $this->smart = true;
        else
            $this->smart = false;
    }

    public function get_name(){
        return '新浪微博';
    }

    public function display_header(){
    }

    /**
     * Output of the sharing button. return, DO NOT echo.
     * @param $post
     * @return string
     */
    public function get_display($post)
    {
        return $this->get_link( $this->get_process_request_url( $post->ID ), '新浪微博', '点击以分享到新浪微博', 'share=sina-weibo', 'sharing-sina-weibo-' . $post->ID );
    }

    /**
     * Return of Ajax request
     * @param $post
     * @param array $post_data
     */
    public function process_request($post, array $post_data)
    {
        $weibo_url = sprintf( $this->http() . '://service.weibo.com/share/share.php?url=%s&title=%s', get_permalink($post), $post->post_title);

        // Record stats
        parent::process_request($post, $post_data);

        // Redirect to QR Code script
        wp_redirect( $weibo_url );
        die();
    }

    public function display_footer()
    {
        $this->js_dialog( $this->get_id() );
    }


}