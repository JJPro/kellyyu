<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class JKUtilities {

	public $admin;
	public $frontend;

	public function __construct() {
		$this->admin = new JKAdminUtilities();
		$this->frontend = new JKFrontendUtilities();
	}

}

class JKAdminUtilities {

	/** String: 
	 * height/width % 
	 */
	public function get_header_img_width_height_rate() {
		$rate = get_custom_header()->height / get_custom_header()->width;
		$rate = sprintf("%.2f%%", $rate * 100);
		return $rate;
	}

	/**
	 * Count views of the image wall page 
	 */
	public function get_post_views($post) {
		$views = get_post_meta($post, '_jk_views_count', true);
		$views = $views ? $views : '0';
		
		return $views;
	}

	/**
	 * increase image wall views count 
	 */ 
	public function increase_post_views($post) {
		$views = $this->get_post_views($post);
		update_post_meta($post, '_jk_views_count', $views + 1 );
	}

}

class JKFrontendUtilities {

	public function get_profile_button( $echo=false ){
		if ( is_user_logged_in() ){
			$output = '';
			// TODO: show user profile image when logged in.
		} else {
			$output = '<a class="btn-login" data-opentab="#tab-user-login">登录</a>';
		}

		if ( $echo )
			echo $output;
		else
			return $output;
	}

	public function get_login_modal( $echo=false ){
		$output = '';
		if ( ! is_user_logged_in() ) {
			$output .= '<div id="modal_login" class="modal fade" role="form">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-body">
										<ul class="nav nav-tabs" role="tablist">
											<li class="active">
												<a href="#tab-admin-login" role="tab" data-toggle="tab">管理员</a>
											</li>
											<li>
												<a href="#tab-user-login" role="tab" data-toggle="tab">用户</a>
											</li>
										</ul>

										<div class="tab-content">
											<div class="tab-pane active" id="tab-admin-login">
												<!-- content of admin login panel -->
												'. $this->admin_login_form( array( 'echo' => false )) .'
											</div> <!-- #tab-admin-login -->

											<div class="tab-pane" id="tab-user-login">
												<!-- content of user login panel -->
												'. $this->user_login_form( array( 'echo' => false )) .'
											</div> <!-- #tab-user-login -->
										</div> <!-- .tab-content -->
									</div> <!-- .modal-body -->
								</div> <!-- .modal-content -->
							</div> <!-- .modal-dialog -->
						</div> <!-- .modal -->
						';
		}

		if ( $echo )
			echo $output;
		else
			return $output;
	}

	public function admin_login_form( $args=array() ){
		$defaults = array(
			'echo' => true,
			// Default 'redirect' value takes the user back to the request URI.
			'redirect' => ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
			'form_id' => 'loginform',
			'label_username' => __( 'Username or Email' ),
			'label_password' => __( 'Password' ),
			'label_remember' => __( 'Remember Me' ),
			'label_log_in' => __( 'Log In' ),
			'id_username' => 'user_login',
			'id_password' => 'user_pass',
			'id_remember' => 'rememberme',
			'id_submit' => 'wp-submit',
			'remember' => true,
			'value_username' => '',
			// Set 'value_remember' to true to default the "Remember me" checkbox to checked.
			'value_remember' => false,
		);

		/**
		 * Filter the default login form output arguments.
		 *
		 * @since 3.0.0
		 *
		 * @see wp_login_form()
		 *
		 * @param array $defaults An array of default login form arguments.
		 */
		$args = wp_parse_args( $args, apply_filters( 'login_form_defaults', $defaults ) );

		/**
		 * Filter content to display at the top of the login form.
		 *
		 * The filter evaluates just following the opening form tag element.
		 *
		 * @since 3.0.0
		 *
		 * @param string $content Content to display. Default empty.
		 * @param array  $args    Array of login form arguments.
		 */
		$login_form_top = apply_filters( 'login_form_top', '', $args );

		/**
		 * Filter content to display in the middle of the login form.
		 *
		 * The filter evaluates just following the location where the 'login-password'
		 * field is displayed.
		 *
		 * @since 3.0.0
		 *
		 * @param string $content Content to display. Default empty.
		 * @param array  $args    Array of login form arguments.
		 */
		$login_form_middle = apply_filters( 'login_form_middle', '', $args );

		/**
		 * Filter content to display at the bottom of the login form.
		 *
		 * The filter evaluates just preceding the closing form tag element.
		 *
		 * @since 3.0.0
		 *
		 * @param string $content Content to display. Default empty.
		 * @param array  $args    Array of login form arguments.
		 */
		$login_form_bottom = apply_filters( 'login_form_bottom', '', $args );

		$form = '
		<form name="' . $args['form_id'] . '" id="' . $args['form_id'] . '" action="' . esc_url( site_url( 'wp-login.php', 'login_post' ) ) . '" method="post">
			' . $login_form_top . '
			<div class="form-group form-group-has-icon">
				<label for="' . esc_attr( $args['id_username'] ) . '" class="sr-only">' . esc_html( $args['label_username'] ) . '</label>
				<label for="' . esc_attr( $args['id_username'] ) . '" class="form-control-icon"><i class="jk-font icon-envelope"></i></label>
				<input type="text" name="log" id="' . esc_attr( $args['id_username'] ) . '" class="form-control" value="' . esc_attr( $args['value_username'] ) . '" placeholder="' . esc_html( $args['label_username']) . '" />
			</div>
			<div class="form-group form-group-has-icon">
				<label for="' . esc_attr( $args['id_password'] ) . '" class="sr-only">' . esc_html( $args['label_password'] ) . '</label>
				<label for="' . esc_attr( $args['id_password'] ) . '" class="form-control-icon"><i class="jk-font icon-key"></i></label>
				<input type="password" name="pwd" id="' . esc_attr( $args['id_password'] ) . '" class="form-control" value="" placeholder="'. esc_html( $args['label_password']) .'" />
			</div>
			' . $login_form_middle . '
			' . ( $args['remember'] ? '<div class="checkbox"><label><input name="rememberme" type="checkbox" id="' . esc_attr( $args['id_remember'] ) . '" value="forever"' . ( $args['value_remember'] ? ' checked="checked"' : '' ) . ' /> ' . esc_html( $args['label_remember'] ) . '</label></div>' : '' ) . '

			<input type="submit" name="wp-submit" id="' . esc_attr( $args['id_submit'] ) . '" class="btn-submit-form btn btn-info btn-block" value="' . esc_attr( $args['label_log_in'] ) . '" />
			<input type="hidden" name="redirect_to" value="' . esc_url( $args['redirect'] ) . '" />

			' . $login_form_bottom . '
		</form>';

		if ( $args['echo'] )
			echo $form;
		else
			return $form;
	}

	public function user_login_form( $args=array() ){

		$defaults = array(
			'echo' => true,
		);

		$args = wp_parse_args( $args, $defaults );

		$output = '';

		$output .= '<p class="description">登录之后可以参与站内的留言讨论,我们会逐渐加入更多功能,详细情况请看<a href="'. esc_url( site_url('about#features-and-plans') ) .'">这里</a></p>';
		$output .= '<div class="bg-danger" style="padding: 10px; font-size: 1.5em; font-weight: bolder;">该功能还在开发测试阶段,暂时不可用</div>';
		$output .= '<a class="login-option btn btn-primary btn-block" onclick="loginToFacebook()">
						<i class="jk-font icon-facebook-official"></i>
						<span>使用Facebook登录</span>
					</a>
					';
		$output .= '<a class="login-option btn btn-block btn-warning btn-sina-weibo" onclick="loginToSinaWeibo()">
						<i class="jk-font icon-sina-weibo"></i>
						<span>使用新浪微博登录</span>
					</a>
					';

		$output .= '<p class="privacy-claim">我们绝不会未经您的允许发帖或泄露您的任何信息</p>';

		if ( $args['echo'] )
			echo $output;
		else
			return $output;
	}

	// ** Inside the Loop ** //
	public function posted_meta() {
		$date = get_the_date();
		$comment_count = get_comments_number() . ' comments';
		$cats  = get_the_category();

		$i = 0; $separator = ', '; $cats_string = '';
		foreach( $cats as $cat ) {
			if ($i > 0)
				$cats_string .= $separator;
			$cats_string .= '<a href="' . esc_url(get_category_link($cat->term_id)) . '" alt="' . esc_attr('View all posts in %s', $cat->name) . '">' . esc_html($cat->name) . '</a>';
			$i++;
		}

		return "$date / $cats_string"; // removed $comment_count
	}

	public function wechat_image_html(){

		$img_url = '';

		// on the blog page
		if ( is_home() || is_front_page() ){
			// site icon
			$img_url = get_site_icon_url();

		} elseif ( is_single() || is_page() ) {
			// post wechat img
			$post = get_post();

			$img_id = get_post_meta( $post->ID, '_wechat_img', true);

			$img_url = wp_get_attachment_image_src( $img_id, 'full' );
			if ($img_url) {
				$img_url = $img_url[0];
			}

		}


		$html = '<div style="display: none;">
					<img src="' . $img_url . '">
				</div>';

		return $html;
	}

	// ** unused ** //
	public function featured_image_html(){
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
			return '<div class="featured-image" style="position: absolute; left: -10000px;"><img src="' . $img_url . '"/></div>';
		}
	}


	public function is_user_from_mainland_china() {
		static $result = null;

		if ($result === null ) {

			if ( isset($_GET['youku']) )
				$result = true;
			else{
				$origin = geoip_detect2_get_info_from_current_ip(NULL);
				$result = ($origin->country->isoCode == 'CN');
			}
		}

		return $result;
	}

	// ** queried in the audio post format ** //
	// ** for ximalaya and changba audio ** //
	public function has_external_audio( $post_id=null ){
		if ( is_null($post_id) )
			$post_id = get_the_ID();
		$enabled = get_post_meta($post_id, '_has_external_track', true);

		return ($enabled);
	}


	// ** ximalaya and changba ** //
	public function get_external_audio_html(){
		

		$data = $this->get_audio_data();

		$title = $data['title'];
		$mp3 = $data['mp3'];
		$cover = is_numeric($data['cover']) ? wp_get_attachment_image_src( $data['cover'], 'full' )[0] : $data['cover'];

		$autoplay = $loop = '';
		if ( is_single() ) {
			$autoplay = 'autoplay="true"';
			$loop = 'loop="loop"';
		}

		// error_log(print_r($cover, true));

		$html = '<div class="external-audio">
					<div class="row no-gutter">
						<div class="col-xs-4 col-md-3 no-padding-left">
							<div class="album-cover-container">';
		if ( !is_singular() )
			$html .= '<a href="' . get_the_permalink() . '">';
		$html .=				'<div class="album-cover background-image" style="background-image:url(' . $cover . ');">
								</div>';
		if ( !is_singular() )
			$html .= '</a>';
		$html .=			'</div>
						</div>
						<div class="col-xs-8 col-md-9 no-padding-right">

							<div class="meta-container">
								<span class="play-pause jk-icon icon-play"></span>

								<span class="title">
									' . $title . '
								</span>
							</div>

						</div> <!-- .col-xs-8 -->
					</div> <!-- .row -->
					<div class="player col-sm-8 col-sm-offset-4">
						<audio src="' . $mp3 . '" preload="metadata" ' . $autoplay . ' ' . $loop . '></audio>
						
						<div class="row">
							<div class="col-xs-2 no-padding-right no-padding-left">
								<div class="current-time text-center">00:00</div>
							</div>
							<div class="col-xs-8 no-padding-left no-padding-right">
								<div class="scrubber">
									<div class="loaded"></div>
									<div class="cursor-move"></div>
									<div class="progress"></div>
									<div class="error-message text-center"></div>
								</div> 
							</div>
							<div class="col-xs-2 no-padding-left no-padding-right">
								<div class="duration text-center">00:00</div>
							</div>

						</div>

					</div> <!-- .player -->
				</div>';
		return $html;

	}

	/*public function get_ximalaya_audio_html(){
		
		$data = $this->get_ximalaya_audio_data();

		$html = '<div class="ximalaya-audio">
					<div class="row no-gutter">
						<div class="col-xs-4 col-md-3 no-padding-left">
							<div class="album-cover-container">
								<div class="album-cover background-image" style="background-image:url(' . $data['cover'] . ');">
								</div>
							</div>
						</div>
						<div class="col-xs-8 col-md-9 no-padding-right">

							<div class="meta-container">
								<span class="play-pause jk-icon icon-play"></span>

								<span class="title">
									' . $data['title'] . '
								</span>
							</div>

						</div> <!-- .col-xs-8 -->
					</div> <!-- .row -->
					<div class="player col-sm-8 col-sm-offset-4">
						<audio src="' . $data['mp3'] . '" preload="metadata" ></audio>
						
						<div class="row">
							<div class="col-xs-2 no-padding-right no-padding-left">
								<div class="current-time text-center">00:00</div>
							</div>
							<div class="col-xs-8 no-padding-left no-padding-right">
								<div class="scrubber">
									<div class="loaded"></div>
									<div class="cursor-move"></div>
									<div class="progress"></div>
									<div class="error-message text-center"></div>
								</div> 
							</div>
							<div class="col-xs-2 no-padding-left no-padding-right">
								<div class="duration text-center">00:00</div>
							</div>

						</div>

					</div> <!-- .player -->
				</div>';
		return $html;

	}*/

	// ** [album image url, mp3 file url, and track name] for audio from ximalaya or changba ** //
	public function get_audio_data( $post_id = null ){

		if ( is_null($post_id) )
			$post_id = get_the_ID();

		$data = get_post_meta($post_id, '_external_track_data', true);

		return $data;

	}

	public function jk_post_thumbnail() {
		if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
			return;
		} ?>

		<?php if ( ! is_singular() ): ?>
			<a href="<?php the_permalink(); ?>">
		<?php endif; ?>
		<div class="post-thumbnail background-image" style="background-image: url(<?php the_post_thumbnail_url(); ?>)">
		</div><!-- .post-thumbnail -->
		<?php if ( ! is_singular() ): ?>
			</a>
		<?php endif; ?>
		<?php
	}

}


global $jk_utilities;
$jk_utilities = new JKUtilities();