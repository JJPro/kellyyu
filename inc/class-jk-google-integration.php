<?php
/**
 * Created by PhpStorm.
 * User: jjpro
 * Date: 5/24/16
 * Time: 10:13 PM
 */

namespace inc;


class JKGoogleIntegration
{
    public function __construct()
    {
        $this->google_analytics();
        $this->google_page_level_ads();
        $this->google_foot_banner_ads(); // Fixed positioned ads at the bottom Only show on small screens
    }



    private function google_analytics() {
        add_action('jk_body_start', function() {
            ?>
            <!-- Google Analytics -->
            <script>
                (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
                })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

                ga('create', 'UA-38993331-3', 'auto');
                ga('require', 'linkid');
                ga('send', 'pageview');

            </script>
            <?php
        });
    }

    private function google_page_level_ads(){
        add_action('wp_head', function(){
            ?>
            <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <script>
                (adsbygoogle = window.adsbygoogle || []).push({
                    google_ad_client: "ca-pub-0919081176944377",
                    enable_page_level_ads: true
                });
            </script>
            <?php
        });
    }

    private function google_foot_banner_ads(){
        // Only show on small screens
        add_action( 'wp_footer', function(){
            ?>
            <div class="google-foot-banner-ads">
                <?php echo google_foot_banner_ads_code(); ?>
            </div>

            <?php echo google_foot_banner_ads_style(); ?>
            <?php
        });

        function google_foot_banner_ads_code(){
            return '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
					<!-- mobile foot banner -->
					<ins class="adsbygoogle"
						 style="display:inline-block;width:320px;height:50px"
						 data-ad-client="ca-pub-0919081176944377"
						 data-ad-slot="1080114878"></ins>
					<script>
					(adsbygoogle = window.adsbygoogle || []).push({});
					</script>';
        }

        function google_foot_banner_ads_style() {
            return '<style>
						.google-foot-banner-ads {
							text-align: center;
							position: fixed;
							bottom: 0;
							width:100%;
							height:50px;
							display: none;
							z-index: 99;
							/* background: rgba(255, 255, 255, 0.8); */
							/* border-top: solid 1px rgba(0, 0, 0, 0.15); */
						}

						.google-foot-banner-ads ins.adsbygoogle {margin: 0 auto !important;}

						@media screen and (max-width: 991px) {
						  .google-foot-banner-ads {
						  	display: block;
						  }
						  footer {
						  	padding-bottom: 50px;
						  }
						}
					</style>';
        }
    }
}