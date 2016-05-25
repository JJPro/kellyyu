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
        add_filter('sharing_services', function($services){
//            $services['kk'] = 'wljlkjechat';
            return $services;
        });
    }
}