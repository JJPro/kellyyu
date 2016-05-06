<?php

class JKSocialWidget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widget_ops = array( 
			'classname' => 'jk_social_widget',
			'description' => 'My Widget is awesome',
		);
		parent::__construct( 'jk-social-widget', 'Kelly\' Social Network', $widget_ops );

		$this->scripts();
		$this->style();
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		// outputs the content of the widget
		$id = $instance['kelly-image-wall-page-id'];
		?>
			<div id='jk-social-widget'>
				<div id="jk-social-widget-tabs">
					<ul>
						<li><a href="#jk-social-widget-tabs-instagram"><span class="jk-icon icon-instagram" data-toggle="tooltip" data-placement="auto top" title="Instagram"></span></a></li>
						<li><a href="#jk-social-widget-tabs-facebook"><span class="jk-icon icon-facebook-square" data-toggle="tooltip" data-placement="auto top" title="Facebook"></span></a></li>
						<?php if ( $id ): ?>
							<li><a id="jk-social-widget-tabs-images-wall-link" href="<?php echo get_permalink( $id ); ?>" target="_blank"><span class="jk-icon icon-images" data-toggle="tooltip" data-placement="auto top" title="<?php echo get_the_title( $id ); ?> <span class='jk-icon icon-link-external'></span>"></span></a></li>
						<?php endif; ?>
					</ul>

					<div id="jk-social-widget-tabs-instagram">
						<?php echo do_shortcode('[instagram-feed]'); ?>
					</div>

					<div id="jk-social-widget-tabs-facebook">
						
						<div id="official-account" style="width=100%;">
							<div class="fb-page" 
							  data-tabs="timeline,events"
							  data-href="https://www.facebook.com/kellyyuwenwen"
							  data-hide-cover="false"
							  data-height=700>
						    </div>
						</div> <!-- #official-account -->

						<div id="taiwan-fans-account" style="width=100%;">
							<div class="fb-page" 
							  data-tabs="timeline,events"
							  data-href="https://www.facebook.com/KellyYu.fansclub"
							  data-hide-cover="false"
							  data-height=700>
						    </div>
						</div> <!-- #taiwan-fans-account -->

					</div> <!-- #jk-social-widget-tabs-facebook -->
				</div>
			</div>
		<?php
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		// outputs the options form on admin

		$id = isset($instance['kelly-image-wall-page-id']) ? $instance['kelly-image-wall-page-id'] : '';
		?>
			<p>
				<label for="<?php echo $this->get_field_id('kelly-image-wall-page');?>">Page ID of 照片墙</label>
				<input type="text" id="<?php echo $this->get_field_id('kelly-image-wall-page');?>" name="<?php echo $this->get_field_name('kelly-image-wall-page-id'); ?>" value="<?php echo $id; ?>">
			</p>

		<?php
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 * 
	 * @return array updated safe values to be saved. 
	 */
	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
		$instance = array();
		$instance['kelly-image-wall-page-id'] = strip_tags($new_instance['kelly-image-wall-page-id']);

		return $instance;
	}

	private function scripts() {
		add_action('wp_enqueue_scripts', function(){
			wp_enqueue_script('jquery-ui-tabs', false, false, array(), true);
		});

		add_action('wp_footer', function(){
			// ** jQuery control code for the widget ** //
			?>
				<script>
				(function($){
					$('#jk-social-widget-tabs').tabs({ collapsible: true, active: false});
					$('#jk-social-widget-tabs-images-wall-link').unbind('click mouseover');
				})(jQuery);
				</script>
			<?php

		}, 100);
	}

	private function style() {

		add_action('wp_footer', function() {
			// ** style of the widget ** //
			?>
				<style>
					#jk-social-widget {}
				</style>
			<?php
		});
	}

}