<?php

class JKLikeButtonWidget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widget_ops = array( 
			'classname' => 'jk_like_button_widget',
			'description' => 'Like on Facebook and Weibo',
		);
		parent::__construct( 'jk-like-button-widget', 'Like on Facebook and Weibo', $widget_ops );

		$this->style();
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		global $jk_utilities;

		$title = isset($instance['title'])?$instance['title']:'';
		$facebook_code = $instance['facebook-like-code'];
		$weibo_like_code = $instance['weibo-like-code'];
		$weibo_share_code = $instance['weibo-share-code'];

		echo $args['before_widget'];
		echo $args['before_title'] . $title . $args['after_title'];

		if ( ! $jk_utilities->frontend->is_user_from_mainland_china() ) {
			echo $facebook_code;
		}
		echo '<br>';
		echo $weibo_like_code;
		echo '<br>';
		echo $weibo_share_code;

		echo $args['after_widget'];
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		// outputs the options form on admin

		$title = isset($instance['title']) ? $instance['title'] : '';
		$facebook_code = isset($instance['facebook-like-code']) ? $instance['facebook-like-code'] : '';
		$weibo_like_code = isset($instance['weibo-like-code']) ? $instance['weibo-like-code'] : '';
		$weibo_share_code = isset($instance['weibo-share-code']) ? $instance['weibo-share-code'] : '';
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title');?>">Title:</label><br>
			<input type="text" id="<?php echo $this->get_field_id('title');?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" >
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('facebook-like-code');?>">Facebook Like Code:</label><br>
			<textarea id="<?php echo $this->get_field_id('facebook-like-code');?>" name="<?php echo $this->get_field_name('facebook-like-code'); ?>"><?php echo $facebook_code; ?></textarea>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('weibo-like-code');?>">微博'赞'代码:</label><br>
			<textarea id="<?php echo $this->get_field_id('weibo-like-code');?>" name="<?php echo $this->get_field_name('weibo-like-code'); ?>"><?php echo $weibo_like_code; ?></textarea>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('weibo-share-code');?>">微博'分享'代码:</label><br>
			<textarea id="<?php echo $this->get_field_id('weibo-share-code');?>" name="<?php echo $this->get_field_name('weibo-share-code'); ?>"><?php echo $weibo_share_code; ?></textarea>
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
		$instance['title'] = $new_instance['title'];
		$instance['facebook-like-code'] = $new_instance['facebook-like-code'];
		$instance['weibo-like-code'] = $new_instance['weibo-like-code'];
		$instance['weibo-share-code'] = $new_instance['weibo-share-code'];

		return $instance;
	}


	private function style() {

		add_action('wp_footer', function() {
			// ** style of the widget ** //
			?>
			<style>
				.widget-area .fb-like.fb_iframe_widget {
					margin-bottom: 10px;
				}
			</style>
			<?php
		});
	}

}