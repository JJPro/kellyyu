<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// ** Adding custom controls to theme customization screen ** //
class JKThemeCustomizationControls {
	
	public function __construct() {
		add_action( 'customize_register', function($wp_customize){

			$this->primary_menu_text_color_control($wp_customize);

		});
	}

	private function primary_menu_text_color_control($wp_customize) {
		// ** add setting ** //
		$wp_customize->add_setting( 'primary_menu_text_color', array(
			'type' => 'theme_mod', 
			'capability' => 'edit_theme_options', 
			'default' => '#F4FBFF', 
			'transport' => 'refresh', 

		));

		// ** add control ** //
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize, 
				'primary_menu_text_color', 
				array(
					'label' => 'Primary Menu Text Color', 
					'section' => 'colors'
				)
			)
		);

		// ** add setting ** //
		$wp_customize->add_setting( 'primary_menu_text_hover_color', array(
			'type' => 'theme_mod', 
			'capability' => 'edit_theme_options', 
			'default' => '#F4FBFF', 
			'transport' => 'refresh', 

		));

		// ** add control ** //
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize, 
				'primary_menu_text_hover_color', 
				array(
					'label' => 'Primary Menu Text Color Hovered', 
					'section' => 'colors'
				)
			)
		);

		// code for adding the style in front end is done by adding inline style in JKKellyYuTheme->enqueue_scripts()
		// Reference functions.php
	}
}