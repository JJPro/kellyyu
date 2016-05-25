<?php
/**
 * Created by PhpStorm.
 * User: jjpro
 * Date: 5/24/16
 * Time: 10:13 PM
 */

namespace inc;


use Facebook\Facebook;
use Facebook\FacebookRequest;

class JKFacebookIntegration
{
    var $fb = null;
    var $app_id = '781693508598758';
    var $app_secret = '0967cf777f8438abe570d09c32b1140a';
    var $app_token = '781693508598758|JAImOUmd5ykbfJA_ilKgDSYm9Pw';
    var $graph_version = 'v2.6';


    public function __construct(){
        global $jk_utilities;

        // ** Back End ** //
        $this->load_facebook_sdk();
        $this->auto_scrape_on_update();

        // ** Front End ** //
        if (! ($jk_utilities->frontend->is_user_from_mainland_china()) ) {
            $this->facebook_integration_js();
            $this->facebook_share_support(); // Facebook Sharing
            $this->facebook_video_oembed_provider();
        }
    }


    private function load_facebook_sdk(){
        require_once('lib/facebook-php-sdk-v4-5.0.0/src/Facebook/autoload.php');

        $this->fb = new Facebook([
            'app_id' => $this->app_id,
            'app_secret' => $this->app_secret,
            'default_graph_version' => $this->graph_version,
        ]);
    }

    private function auto_scrape_on_update(){

        // for posts and pages
        add_action('post_updated', function($post_ID, $post_after, $post_before){
            if ( get_post_status( $post_ID ) == 'publish' ){
                $url = get_permalink($post_ID);

                $this->scrape($url);
            }
        }, 70, 3);


    }

    private function scrape($url){
        $response = $this->fb->sendRequest(
            'POST',
            '/',
            array(
                'scrape' => 'true',
                'id' => $url
            ),
            $this->app_token
        );

        /* handle the result */
//        $graphObject = $response->getGraphObject();
//        error_log('graph api response: ' . print_r($response, true));
    }


    private function facebook_integration_js() {
        add_action('jk_body_start', function(){
            ?>
            <script>
                window.fbAsyncInit = function() {
                    FB.init({
                        appId      : '<?php echo $this->app_id; ?>',
                        xfbml      : true,
                        version    : '<?php echo $this->graph_version; ?>'
                    });
                };

                (function(d, s, id){
                    var js, fjs = d.getElementsByTagName(s)[0];
                    if (d.getElementById(id)) {return;}
                    js = d.createElement(s); js.id = id;
                    js.src = "//connect.facebook.net/en_US/sdk.js";
                    fjs.parentNode.insertBefore(js, fjs);
                }(document, 'script', 'facebook-jssdk'));
            </script>
            <?php
        });
    }

    private function facebook_share_support(){
        add_action('wp_head', function(){
            global $jk_utilities;

            // App ID
            echo $this->facebook_app_id_meta();

            // Image
            echo $this->facebook_image_html();

            // Other: URL, title, description, app_id
            // this is done by Jetpack for you
//			echo $this->facebook_share_meta();
        });
    }

    private function facebook_video_oembed_provider(){
        add_action('init', function(){
            $endpoints = array(
                '#https?://www\.facebook\.com/video.php.*#i'          => 'https://www.facebook.com/plugins/video/oembed.json/',
                '#https?://www\.facebook\.com/.*/videos/.*#i'         => 'https://www.facebook.com/plugins/video/oembed.json/',
            );

            foreach($endpoints as $pattern => $endpoint) {
                wp_oembed_add_provider( $pattern, $endpoint, true );
            }
        });
    }



    /****************************************************************/
    /****************************************************************/
    /********************* Helper functions *************************/
    /****************************************************************/
    /****************************************************************/


    private function facebook_app_id_meta(){
        return '<meta property="fb:app_id" content="' . $this->app_id . '" />';
    }

    private function facebook_image_html(){
        if ( is_home() || is_front_page() ){
            // site icon
            $img_url = get_site_icon_url();

        } else {
            // use page thumbnail if exists
            $img_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
            if ($img_url) {
                $img_url = $img_url[0];
            }
        }

        if ($img_url) {
//			error_log('facebook_img: ' . $img_url);
            return '<link rel="image_src" href="' . $img_url . '" />';
        }
    }

    private function facebook_share_meta(){

        // URL
        $url = home_url( $_SERVER['REQUEST_URI'] );

        // Title
        if ( is_home() || is_front_page() ) {
            $title = get_bloginfo('name');
        } elseif ( is_archive() ) {
            $title = get_bloginfo('name') . ' &raquo; ' . get_the_archive_title();
        } elseif ( is_single() ) {
            $title = get_bloginfo('name') . ' &raquo; ' . get_the_title();
        }

        // Description
        if ( is_home() || is_front_page() ) {
            $description = get_bloginfo('description');
        } elseif ( is_archive() ) {
            $description = get_the_archive_description();
        } elseif ( is_single() ) {
            $description = get_the_excerpt();
        }

        ?>
        <meta property="og:url" content="<?php echo $url; ?>" />
        <meta property="og:title" content="<?php echo $title; ?>" />
        <meta property="og:description" content="<?php echo $description; ?>" />
        <?php
    }

}