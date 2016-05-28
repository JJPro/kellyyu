<?php

class JKJetpackIntegration
{

    public function __construct()
    {
        // only run this class when jetpack plugin is active
        if ($this->jetpack_is_active()){

            $this->reposition_related_posts();
            $this->reposition_sharing_buttons();
            $this->add_sharing_services();
        }
    }

    private function jetpack_is_active(){
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

        return is_plugin_active('jetpack/jetpack.php');
    }

    private function reposition_related_posts(){
        if ( JETPACK_DEV_DEBUG ) return;
        add_filter( 'wp', function(){
            $jrp = Jetpack_RelatedPosts::init();
            $callback = array( $jrp, 'filter_add_target_to_dom');
            remove_filter( 'the_content', $callback, 40);

            add_filter( 'entry_footer', function(){
                if ( is_single() || is_page() ){
                    echo do_shortcode('[jetpack-related-posts]');
                }
            }, 10);

        }, 20);
    }

    private function reposition_sharing_buttons(){

        add_filter( 'wp', function(){

            if ( is_singular() ){
                remove_filter( 'the_content', 'sharing_display', 19 );
                add_filter( 'entry_header', 'sharing_display', 19 );
                add_filter( 'entry_footer', 'sharing_display', 20 );
            }
        }, 20);
    }

    private function add_sharing_services(){

        // jk-font sharing services icons in admin preview screen
        add_action( 'admin_head', 'services_styles');
        add_action( 'wp_head', 'services_styles');

        function services_styles(){
            ?>
            <style>
                /* WECHAT */
                li.service.share-wechat span::before,
                .sd-content ul li.share-wechat a::before {
                    font-family: 'jk-font';
                    content: '\x';
                }
                li.service.share-wechat span::before,
                .sd-content ul li.share-wechat a:not(.no-text)::before {
                    margin-right: 5px;
                }
                .sd-content ul li.share-wechat a.share-wechat.sd-button.share-icon.no-text {
                    background-color: #35b900;
                    color: #FFF !important;
                }


                /* SINA WEIBO */
                li.service.share-sina-weibo span::before,
                .sd-content ul li.share-sina-weibo a:not(.no-text)::before {
                    font-family: 'jk-font';
                    content: '\u';
                }
                li.service.share-sina-weibo span::before,
                .sd-content ul li.share-sina-weibo a:not(.no-text)::before {
                    margin-right: 5px;
                }
                .sd-content ul li.share-sina-weibo a.no-text::before {
                    width: 16px;
                    height: 16px;
                    position: relative;
                    transform: scale(0.132) translateX(-390%) translateY(-360%);
                    content: url(<?php echo get_template_directory_uri() . '/img/sina_weibo.png'; ?>);
                    /*content: url("http://open.weibo.com/favicon.ico");*/
                }
                .sd-content ul li.share-sina-weibo a.share-sina-weibo.sd-button.share-icon.no-text {
                    background-color: #f7e175;
                    color: #d70021 !important;
                }

            </style>
            <?php
        }

        add_filter('sharing_services', function($services){
            require_once('jk-jetpack-sharing-sources.php');
            $services['wechat'] = 'Share_Wechat';
            $services['sina-weibo'] = 'Share_Sina_Weibo';
            return $services;
        });
    }
}