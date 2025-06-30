<?php
/**
 * Defines an array of options that will be used to generate the settings page and be saved in the database.
 * When creating the "id" fields, make sure to use all lowercase and no spaces.
 *
 */
if(!function_exists('optionsframework_options')) {
	function optionsframework_options() {
// Fonts
			global $typography_mixed_fonts;
			$typography_mixed_fonts = array_merge(options_typography_get_os_fonts() , options_typography_get_google_fonts());
			asort($typography_mixed_fonts);

			$options = array();
// ---------------------------------------------------------
// General
// ---------------------------------------------------------
			$options['general'] = array( "name" => theme_locals('general'),
								"type" => "heading");
			// Background Defaults
			$background_defaults = array(
				'color' => '',
				'image' => '',
				'repeat' => 'repeat',
				'position' => 'top center',
				'attachment'=>'scroll'
			);
			$options['body_background'] = array(
								"id" => "body_background",
								"std" => $background_defaults);

			$options['main_layout'] = array(
								"id" => "main_layout",
								"std" => "fullwidth");

			$options['main_background'] = array(
								"id" => "main_background",
								"std" => "#fafafa");

			$header_bg_defaults = array(
				'color' => '',
				'image' => CHILD_URL . "/images/header_bg_01.png",
				'repeat' => 'repeat',
				'position' => 'top center',
				'attachment'=>'scroll'
			);
			$options['header_background'] = array(
								"id" => "header_background",
								"std" => $header_bg_defaults);

			$options['links_color'] = array(
								"id" => "links_color",
								"std" => "#202020");

			$options['links_color_hover'] = array(
								"id" => "links_color_hover",
								"std" => "#948158");

			$options['google_mixed_3'] = array(
								'id' => 'google_mixed_3',
								'std' => array( 'size' => '15px', 'lineheight' => '22px', 'face' => 'Lato', 'style' => 'normal', 'character'  => 'latin', 'color' => '#888888'));

			$options['h1_heading'] = array(
								'id' => 'h1_heading',
								'std' => array( 'size' => '80px', 'lineheight' => '80px', 'face' => 'Yesteryear', 'style' => 'normal', 'character'  => 'latin', 'color' => '#202020'));

			$options['h2_heading'] = array(
								'id' => 'h2_heading',
								'std' => array( 'size' => '31px', 'lineheight' => '42px', 'face' => 'Lato', 'style' => 'normal', 'character'  => 'latin', 'color' => '#202020'));

			$options['h3_heading'] = array(
								'id' => 'h3_heading',
								'std' => array( 'size' => '18px', 'lineheight' => '22px', 'face' => 'Lato', 'style' => 'normal', 'character'  => 'latin', 'color' => '#202020'));

			$options['h4_heading'] = array(
								'id' => 'h4_heading',
								'std' => array( 'size' => '14px', 'lineheight' => '20px', 'face' => 'Lato', 'style' => 'normal', 'character'  => 'latin', 'color' => '#202020'));

			$options['h5_heading'] = array(
								'id' => 'h5_heading',
								'std' => array( 'size' => '31px', 'lineheight' => '42px', 'face' => 'Lato', 'style' => 'normal', 'character'  => 'latin', 'color' => '#202020'));

			$options['h6_heading'] = array(
								'id' => 'h6_heading',
								'std' => array( 'size' => '40px', 'lineheight' => '42px', 'face' => 'Yesteryear', 'style' => 'normal', 'character'  => 'latin', 'color' => '#948158'));

			$options['g_search_box_id'] = array(
								"id" => "g_search_box_id",
								"std" => "no");

			$options['g_breadcrumbs_id'] = array(
								"id" => "g_breadcrumbs_id",
								"std" => "yes");

			$options['custom_css'] = array(
								"id" => "custom_css",
								"std" => "");
			
			$options['live_chat_id'] = array(
								"id" => "live_chat_id",
								"std" => "7830-582-10-3714");
// ---------------------------------------------------------
// Logo & Favicon
// ---------------------------------------------------------
			$options["logo_favicon"] = array( "name" => theme_locals('logo_favicon'),
								"type" => "heading");

			$options['logo_type'] = array(
								"id" => "logo_type",
								"std" => "image_logo");

			$options['logo_typography'] = array(
								'id' => 'logo_typography',
								'std' => array( 'size' => '40px', 'lineheight' => '48px', 'face' => 'Lato', 'style' => 'normal', 'character'  => 'latin', 'color' => '#ffffff'));

			$options['logo_url'] = array(
								"id" => "logo_url",
								"std" => CHILD_URL . "/images/logo.png");

			$options['favicon'] = array(
								"id" => "favicon",
								"std" => CHILD_URL . "/favicon.ico");

// ---------------------------------------------------------
// Navigation
// ---------------------------------------------------------
			$options['navigation'] = array( "name" => theme_locals('navigation'),
								"type" => "heading");

			$options['menu_typography'] = array(
								'id' => 'menu_typography',
								'std' => array( 'size' => '23px', 'lineheight' => '23px', 'face' => 'Lato', 'style' => 'normal', 'character'  => 'latin', 'color' => '#ffffff'));

			$options['sf_delay'] = array(
								"id" => "sf_delay",
								"std" => "1000");

			$options['sf_f_animation'] = array(
								"id" => "sf_f_animation",
								"std" => "show");

			$options['sf_sl_animation'] = array(
								"id" => "sf_sl_animation",
								"std" => "show");

			$options['sf_speed'] = array(
								"id" => "sf_speed",
								"std" => "normal");

			$options['sf_arrows'] = array(
								"id" => "sf_arrows",
								"std" => "false");

			$options['stickup_menu'] = array(
								"id" => "stickup_menu",
								"std" => "true");

			$options['mobile_menu_label'] = array(
								"id" => "mobile_menu_label",
								"std" => theme_locals('mobile_menu_std'));

// ---------------------------------------------------------
// Slider
// ---------------------------------------------------------
			$options['slider'] = array( "name" => theme_locals('slider'),
								"type" => "heading");

	// Slider type
			$options['sl_type'] = array(
								"id" => "slider_type",
								"std" => "none_slider");
	// ---------------------------------------------------------
	// Camera Slider
	// ---------------------------------------------------------
			$options['sl_effect'] = array(
								"id" => "sl_effect",
								"std" => "simpleFade");

			$options['sl_columns'] = array(
								"id" => "sl_columns",
								"std" => "12");

			$options['sl_rows'] = array(
								"id" => "sl_rows",
								"std" => "8");

			$options['sl_banner'] = array(
								"id" => "sl_banner",
								"std" => "fadeIn");

			$options['sl_pausetime'] = array(
								"id" => "sl_pausetime",
								"std" => "7000");

			$options['sl_animation_speed'] = array(
								"id" => "sl_animation_speed",
								"std" => "1500");

			$options['sl_slideshow'] = array(
								"id" => "sl_slideshow",
								"std" => "true");

			$options['sl_thumbnails'] = array(
								"id" => "sl_thumbnails",
								"std" => "true"); // set "disabled" => "true" when only text in Slider posts

			$options['sl_control_nav'] = array(
								"id" => "sl_control_nav",
								"std" => "true");

			$options['sl_dir_nav'] = array(
								"id" => "sl_dir_nav",
								"std" => "true");

			$options['sl_dir_nav_hide'] = array(
								"id" => "sl_dir_nav_hide",
								"std" => "false");

			$options['sl_play_pause_button'] = array(
								"id" => "sl_play_pause_button",
								"std" => "true");

			$options['sl_pause_on_hover'] = array(
								"id" => "sl_pause_on_hover",
								"std" => "true");

			$options['sl_loader'] = array(
								"id" => "sl_loader",
								"std" => "no");
	// ---------------------------------------------------------
	// Accordion Slider
	// ---------------------------------------------------------
			$multicheck_defaults = array( '43' => 0,  '49' => 0, '50' => 0, '51' => 0, '52' => 0);
			$options['acc_show_post'] = array(
					"id" => "acc_show_post",
					"std" => $multicheck_defaults);

			$options['acc_slideshow'] = array(
								"id" => "acc_slideshow",
								"std" => "false");

			$options['acc_hover_pause'] = array(
								"id" => "acc_hover_pause",
								"std" => "true");

			$options['acc_pausetime'] = array(
								"id" => "acc_pausetime",
								"std" => "6000");

			$options['acc_animation_speed'] = array(
								"id" => "acc_animation_speed",
								"std" => "600");

			$options['acc_easing'] = array(
								"id" => "acc_easing",
								"std" => "easeOutCubic");

			$options['acc_trigger'] = array(
								"id" => "acc_trigger",
								"std" => "mouseover");

			$options['acc_starting_slide'] = array(
								"id" => "acc_starting_slide",
								"std" => "0");
// ---------------------------------------------------------
// Blog
// --------------------------------------------------------
			$options['blog'] = array( "name" => theme_locals('blog'),
								"type" => "heading");

			$options['blog_text'] = array(
								"id" => "blog_text",
								"std" => "Our blog");

			$options['blog_related'] = array(
								"id" => "blog_related",
								"std" => theme_locals('posts_std'));

			$options['blog_sidebar_pos'] = array(
								"id" => "blog_sidebar_pos",
								"std" => "none");

			$options['post_image_size'] = array(
								"id" => "post_image_size",
								"std" => "large");

			$options['single_image_size'] = array(
								"id" => "single_image_size",
								"std" => "large");

			$options['post_meta'] = array(
								"id" => "post_meta",
								"std" => "true");

			$options['post_meta_display'] = array(
								"id" => "post_meta_display",
								"std" => "blog_post");

			$options['post_excerpt'] = array(
								"id" => "post_excerpt",
								"std" => "true");

// ---------------------------------------------------------
// Portfolio
// ---------------------------------------------------------
			$options['portfolio'] = array(
								"name" => theme_locals("portfolio"),
								"type" => "heading");

			$options['folio_filter'] = array(
								"id" => "folio_filter",
								"std" => "cat");

			$options['folio_title'] = array(
								"id" => "folio_title",
								"std" => "yes");

			$options['folio_excerpt'] = array(
								"id" => "folio_excerpt",
								"std" => "yes");

			$options['folio_excerpt_count'] = array(
								"id" => "folio_excerpt_count",
								"std" => "20");

			$options['folio_btn'] = array(
								"id" => "folio_btn",
								"std" => "yes");

			$options['folio_meta'] = array(
								"id" => "folio_meta",
								"std" => "yes");

			$options['layout_mode'] = array(
								"id" => "layout_mode",
								"std" => "fitRows");

			$options['items_count2'] = array(
								"id" => "items_count2",
								"std" => "8");

			$options['items_count3'] = array(
								"id" => "items_count3",
								"std" => "9");

			$options['items_count4'] = array(
								"id" => "items_count4",
								"std" => "12");

			$options['folio_button_text'] = array(
        "id" => "folio_button_text",
        "std" => "Read more");

// ---------------------------------------------------------
// Footer
// ---------------------------------------------------------
			$options['footer'] = array( "name" => theme_locals("footer"),
								"type" => "heading");

			$options['footer_text'] = array(
								"id" => "footer_text",
								"std" => "");

			$options['ga_code'] = array(
								"id" => "ga_code",
								"std" => "");

			$options['feed_url'] = array(
								"id" => "feed_url",
								"std" => "");

			$options['footer_menu'] = array(
								"id" => "footer_menu",
								"std" => "false");

			$options['footer_menu_typography'] = array(
								'id' => 'footer_menu_typography',
								'std' => array( 'size' => '23px', 'lineheight' => '40px', 'face' => 'Lato', 'style' => 'normal', 'character'  => 'latin', 'color' => '#202020'));

// ---------------------------------------------------------
// px_slider
// ---------------------------------------------------------
			$options['px_slider'] = array(
								"name" => __( 'Parallax Slider', CURRENT_THEME ),
								"type" => "heading"
			);

			$options['px_slider_visibility'] = array(
								"name" => __( 'Display Parallax Slider?', CURRENT_THEME ),
								"desc"    => __( 'Display Parallax Slider?', CURRENT_THEME ),
								"id"      => "px_slider_visibility",
								"type"    => "radio",
								"std"     => "true",
								"options" => array(
									"true" => theme_locals("yes"),
									"false" => theme_locals("no"),
								),
			);

			$options['px_slider_parallax_effect'] = array(
								"name" => __( 'Parallax effect', CURRENT_THEME ),
								"desc"    => __( 'Select parallax effect.', CURRENT_THEME ),
								"id"      => "px_slider_parallax_effect",
								"type"    => "select",
								"std"     => "parallax_effect_normal",
								"options" => array(
									"parallax_effect_none" => __( 'None', CURRENT_THEME ),
									"parallax_effect_low" => __( 'Low', CURRENT_THEME ),
									"parallax_effect_normal" => __( 'Normal', CURRENT_THEME ),
									"parallax_effect_high" => __( 'High', CURRENT_THEME ),
									"parallax_effect_fixed" => __( 'Fixed', CURRENT_THEME ),
								),
			);

			$options['px_slider_invert'] = array(
								"name" => __( 'Invert Parallax Slider', CURRENT_THEME ),
								"desc"    => __( 'Invert Parallax Slider', CURRENT_THEME ),
								"id"      => "px_slider_invert",
								"type"    => "radio",
								"std"     => "false",
								"options" => array(
									"true" => theme_locals("yes"),
									"false" => theme_locals("no"),
								),
			);

            $options['px_slider_effect'] = array(
								"name" => __( 'Sliding effect', CURRENT_THEME ),
								"desc"    => __( 'Select your animation type.', CURRENT_THEME ),
								"id"      => "px_slider_effect",
								"type"    => "select",
								"std"     => "simple-fade-eff",
								"options" => array(
									"simple-fade-eff" => __( 'Simple Fade', CURRENT_THEME ),
									"zoom-fade-eff" => __( 'Zoom Fade', CURRENT_THEME ),
									"slide-top-eff" => __( 'Slide Top', CURRENT_THEME ),
								),
			);

			$options['px_slider_auto'] = array(
								"name" => __( 'Slideshow', CURRENT_THEME ),
								"desc"    => __( 'Animate slider automatically?', CURRENT_THEME ),
								"id"      => "px_slider_auto",
								"type"    => "radio",
								"std"     => "true",
								"options" => array(
									"true" => theme_locals("yes"),
									"false" => theme_locals("no"),
								),
			);

			$options['px_slider_pause'] = array(
								"name" => __( 'Pause time', CURRENT_THEME ),
								"desc"    => __( 'Pause time (ms).', CURRENT_THEME ),
								"id"      => "px_slider_pause",
								"type"    => "text",
								"std"     => "10000",
			);

			$options['px_slider_speed'] = array(
								"name" => __( 'Animation speed', CURRENT_THEME ),
								"desc"    => __( 'Animation speed (ms).', CURRENT_THEME ),
								"id"      => "px_slider_speed",
								"type"    => "text",
								"std"     => "1500",
			);

			$options['px_slider_scrolling_description'] = array(
								"name" => __( 'Scrolling description', CURRENT_THEME ),
								"desc"    => __( 'Scrolling description', CURRENT_THEME ),
								"id"      => "px_slider_scrolling_description",
								"type"    => "radio",
								"std"     => "true",
								"options" => array(
									"true" => theme_locals("yes"),
									"false" => theme_locals("no"),
								),
			);

			$options['px_slider_pags'] = array(
								"name" => __( 'Pagination', CURRENT_THEME ),
								"desc"    => __( 'Display pagination?', CURRENT_THEME ),
								"id"      => "px_slider_pags",
								"type"    => "radio",
								"std"     => "none_pagination",
								"options" => array(
												"buttons_pagination" => __( 'Buttons Pagination', CURRENT_THEME ),
												"images_pagination" => __( 'Images Pagination', CURRENT_THEME ),
												"none_pagination" => theme_locals("no"),
											),
			);

			$options['px_slider_navs'] = array(
								"name" => __( 'Next & Prev navigation', CURRENT_THEME ),
								"desc"    => __( 'Display next & prev navigation?', CURRENT_THEME ),
								"id"      => "px_slider_navs",
								"type"    => "radio",
								"std"     => "true",
								"options" => array(
									"true" => theme_locals("yes"),
									"false" => theme_locals("no"),
								),
			);
		return $options;
	}
}
?>
