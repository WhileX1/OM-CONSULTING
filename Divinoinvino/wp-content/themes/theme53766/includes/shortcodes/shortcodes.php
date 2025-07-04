<?php
//Recent Posts
if (!function_exists('shortcode_recent_posts')) {

	function shortcode_recent_posts( $atts, $content = null, $shortcodename = '' ) {
		extract(shortcode_atts(array(
				'type'             => 'post',
				'category'         => '',
				'custom_category'  => '',
				'tag'              => '',
				'post_format'      => 'standard',
				'num'              => '5',
				'meta'             => 'true',
				'thumb'            => 'true',
				'thumb_width'      => '120',
				'thumb_height'     => '120',
				'more_text_single' => '',
				'excerpt_count'    => '0',
				'custom_class'     => ''
		), $atts));

		$output = '<ul class="recent-posts '.$custom_class.' unstyled">';

		global $post;
		global $my_string_limit_words;
		$item_counter = 0;
		// WPML filter
		$suppress_filters = get_option('suppress_filters');

		if($post_format == 'standard') {

			$args = array(
						'post_type'         => $type,
						'category_name'     => $category,
						'tag'               => $tag,
						$type . '_category' => $custom_category,
						'numberposts'       => $num,
						'orderby'           => 'post_date',
						'order'             => 'DESC',
						'tax_query'         => array(
						'relation'          => 'AND',
							array(
								'taxonomy' => 'post_format',
								'field'    => 'slug',
								'terms'    => array('post-format-aside', 'post-format-gallery', 'post-format-link', 'post-format-image', 'post-format-quote', 'post-format-audio', 'post-format-video'),
								'operator' => 'NOT IN'
							)
						),
						'suppress_filters' => $suppress_filters
					);

		} else {

			$args = array(
				'post_type'         => $type,
				'category_name'     => $category,
				'tag'               => $tag,
				$type . '_category' => $custom_category,
				'numberposts'       => $num,
				'orderby'           => 'post_date',
				'order'             => 'DESC',
				'tax_query'         => array(
				'relation'          => 'AND',
					array(
						'taxonomy' => 'post_format',
						'field'    => 'slug',
						'terms'    => array('post-format-' . $post_format)
					)
				),
				'suppress_filters' => $suppress_filters
			);
		}

		$latest = get_posts($args);

		foreach($latest as $k => $post) {
				//Check if WPML is activated
				if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
					global $sitepress;

					$post_lang = $sitepress->get_language_for_element($post->ID, 'post_' . $type);
					$curr_lang = $sitepress->get_current_language();
					// Unset not translated posts
					if ( $post_lang != $curr_lang ) {
						unset( $latest[$k] );
					}
					// Post ID is different in a second language Solution
					if ( function_exists( 'icl_object_id' ) ) {
						$post = get_post( icl_object_id( $post->ID, $type, true ) );
					}
				}
				setup_postdata($post);
				$excerpt        = get_the_excerpt();
				$attachment_url = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'full' );
				$url            = $attachment_url['0'];
				$image          = aq_resize($url, $thumb_width, $thumb_height, true);

				$post_classes = get_post_class();
				foreach ($post_classes as $key => $value) {
					$pos = strripos($value, 'tag-');
					if ($pos !== false) {
						unset($post_classes[$key]);
					}
				}
				$post_classes = implode(' ', $post_classes);

				$output .= '<li class="recent-posts_li ' . $post_classes . '  list-item-' . $item_counter . ' clearfix">';

				//Aside
				if($post_format == "aside") {

					$output .= the_content($post->ID);

				} elseif ($post_format == "link") {

					$url =  get_post_meta(get_the_ID(), 'tz_link_url', true);

					$output .= '<a target="_blank" href="'. $url . '">';
					$output .= get_the_title($post->ID);
					$output .= '</a>';

				//Quote
				} elseif ($post_format == "quote") {

					$quote =  get_post_meta(get_the_ID(), 'tz_quote', true);

					$output .= '<div class="quote-wrap clearfix">';

							$output .= '<blockquote>';
								$output .= $quote;
							$output .= '</blockquote>';

					$output .= '</div>';

				//Image
				} elseif ($post_format == "image") {

				if (has_post_thumbnail() ) :

					// $lightbox = get_post_meta(get_the_ID(), 'tz_image_lightbox', TRUE);

					$src      = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), array( '9999','9999' ), false, '' );

					$thumb    = get_post_thumbnail_id();
					$img_url  = wp_get_attachment_url( $thumb,'full'); //get img URL
					$image    = aq_resize( $img_url, 200, 120, true ); //resize & crop img


					$output .= '<figure class="thumbnail featured-thumbnail large">';
						$output .= '<a class="image-wrap" rel="prettyPhoto" title="' . get_the_title($post->ID) . '" href="' . $src[0] . '">';
						$output .= '<img src="' . $image . '" alt="' . get_the_title($post->ID) .'" />';
						$output .= '<span class="zoom-icon"></span></a>';
					$output .= '</figure>';

				endif;


				//Audio
				} elseif ($post_format == "audio") {

					$template_url = get_template_directory_uri();
					$id           = $post->ID;

					// get audio attribute
					$audio_title  = get_post_meta(get_the_ID(), 'tz_audio_title', true);
					$audio_artist = get_post_meta(get_the_ID(), 'tz_audio_artist', true);
					$audio_format = get_post_meta(get_the_ID(), 'tz_audio_format', true);
					$audio_url    = get_post_meta(get_the_ID(), 'tz_audio_url', true);

					// Get the URL to the content area.
					$content_url = untrailingslashit( content_url() );

					// Find latest '/' in content URL.
					$last_slash_pos = strrpos( $content_url, '/' );

					// 'wp-content' or something else.
					$content_dir_name = substr( $content_url, $last_slash_pos - strlen( $content_url ) + 1 );

					$pos = strpos( $audio_url, $content_dir_name );

					if ( false === $pos ) {
						$file = $audio_url;
					} else {
						$audio_new = substr( $audio_url, $pos + strlen( $content_dir_name ), strlen( $audio_url ) - $pos );
						$file     = $content_url . $audio_new;
					}

					$output .= '<script type="text/javascript">
						jQuery(document).ready(function(){
							var myPlaylist_'. $id.'  = new jPlayerPlaylist({
							jPlayer: "#jquery_jplayer_'. $id .'",
							cssSelectorAncestor: "#jp_container_'. $id .'"
							}, [
							{
								title:"'. $audio_title .'",
								artist:"'. $audio_artist .'",
								'. $audio_format .' : "'. stripslashes(htmlspecialchars_decode($file)) .'"}
							], {
								playlistOptions: {enableRemoveControls: false},
								ready: function () {jQuery(this).jPlayer("setMedia", {'. $audio_format .' : "'. stripslashes(htmlspecialchars_decode($file)) .'", poster: "'. $image .'"});
							},
							swfPath: "'. $template_url .'/flash",
							supplied: "'. $audio_format .', all",
							wmode:"window"
							});
						});
						</script>';

					$output .= '<div id="jquery_jplayer_'.$id.'" class="jp-jplayer"></div>
								<div id="jp_container_'.$id.'" class="jp-audio">
									<div class="jp-type-single">
										<div class="jp-gui">
											<div class="jp-interface">
												<div class="jp-progress">
													<div class="jp-seek-bar">
														<div class="jp-play-bar"></div>
													</div>
												</div>
												<div class="jp-duration"></div>
												<div class="jp-time-sep"></div>
												<div class="jp-current-time"></div>
												<div class="jp-controls-holder">
													<ul class="jp-controls">
														<li><a href="javascript:;" class="jp-previous" tabindex="1" title="'.__('Previous', CHERRY_PLUGIN_DOMAIN).'"><span>'.__('Previous', CHERRY_PLUGIN_DOMAIN).'</span></a></li>
														<li><a href="javascript:;" class="jp-play" tabindex="1" title="'.__('Play', CHERRY_PLUGIN_DOMAIN).'"><span>'.__('Play', CHERRY_PLUGIN_DOMAIN).'</span></a></li>
														<li><a href="javascript:;" class="jp-pause" tabindex="1" title="'.__('Pause', CHERRY_PLUGIN_DOMAIN).'"><span>'.__('Pause', CHERRY_PLUGIN_DOMAIN).'</span></a></li>
														<li><a href="javascript:;" class="jp-next" tabindex="1" title="'.__('Next', CHERRY_PLUGIN_DOMAIN).'"><span>'.__('Next', CHERRY_PLUGIN_DOMAIN).'</span></a></li>
														<li><a href="javascript:;" class="jp-stop" tabindex="1" title="'.__('Stop', CHERRY_PLUGIN_DOMAIN).'"><span>'.__('Stop', CHERRY_PLUGIN_DOMAIN).'</span></a></li>
													</ul>
													<div class="jp-volume-bar">
														<div class="jp-volume-bar-value"></div>
													</div>
													<ul class="jp-toggles">
														<li><a href="javascript:;" class="jp-mute" tabindex="1" title="'.__('Mute', CHERRY_PLUGIN_DOMAIN).'"><span>'.__('Mute', CHERRY_PLUGIN_DOMAIN).'</span></a></li>
														<li><a href="javascript:;" class="jp-unmute" tabindex="1" title="'.__('Unmute', CHERRY_PLUGIN_DOMAIN).'"><span>'.__('Unmute', CHERRY_PLUGIN_DOMAIN).'</span></a></li>
													</ul>
												</div>
											</div>
											<div class="jp-no-solution">
												<span>'.__('Update Required.', CHERRY_PLUGIN_DOMAIN).'</span>'.__('To play the media you will need to either update your browser to a recent version or update your ', CHERRY_PLUGIN_DOMAIN).'<a href="http://get.adobe.com/flashplayer/" target="_blank">'.__('Flash plugin', CHERRY_PLUGIN_DOMAIN).'</a>
											</div>
										</div>
									</div>
									<div class="jp-playlist">
										<ul>
											<li></li>
										</ul>
									</div>
								</div>';


				$output .= '<div class="entry-content">';
					$output .= get_the_content($post->ID);
				$output .= '</div>';

				//Video
				} elseif ($post_format == "video") {

					$template_url = get_template_directory_uri();
					$id           = $post->ID;

					// get video attribute
					$video_title  = get_post_meta(get_the_ID(), 'tz_video_title', true);
					$video_artist = get_post_meta(get_the_ID(), 'tz_video_artist', true);
					$embed        = get_post_meta(get_the_ID(), 'tz_video_embed', true);
					$m4v_url      = get_post_meta(get_the_ID(), 'tz_m4v_url', true);
					$ogv_url      = get_post_meta(get_the_ID(), 'tz_ogv_url', true);

					// Get the URL to the content area.
					$content_url = untrailingslashit( content_url() );

					// Find latest '/' in content URL.
					$last_slash_pos = strrpos( $content_url, '/' );

					// 'wp-content' or something else.
					$content_dir_name = substr( $content_url, $last_slash_pos - strlen( $content_url ) + 1 );

					$pos1     = strpos($m4v_url, $content_dir_name);
					if ($pos1 === false) {
						$file1 = $m4v_url;
					} else {
						$m4v_new  = substr($m4v_url, $pos1+strlen($content_dir_name), strlen($m4v_url) - $pos1);
						$file1    = $content_url.$m4v_new;
					}

					$pos2     = strpos($ogv_url, $content_dir_name);
					if ($pos2 === false) {
						$file2 = $ogv_url;
					} else {
						$ogv_new  = substr($ogv_url, $pos2+strlen($content_dir_name), strlen($ogv_url) - $pos2);
						$file2    = $content_url.$ogv_new;
					}

					// get thumb
					if(has_post_thumbnail()) {
						$thumb   = get_post_thumbnail_id();
						$img_url = wp_get_attachment_url( $thumb,'full'); //get img URL
						$image   = aq_resize( $img_url, 770, 380, true ); //resize & crop img
					}

					if ($embed == '') {
						$output .= '<script type="text/javascript">
							jQuery(document).ready(function(){
								var
									jPlayerObj = jQuery("#jquery_jplayer_'. $id.'")
								,	jPlayerContainer = jQuery("#jp_container_'. $id.'")
								,	isPause = true
								;
								jPlayerObj.jPlayer({
									ready: function () {
										jQuery(this).jPlayer("setMedia", {
											m4v: "'. stripslashes(htmlspecialchars_decode($file1)) .'",
											ogv: "'. stripslashes(htmlspecialchars_decode($file2)) .'",
											poster: "'. $image .'"
										});
									},
									swfPath: "'. $template_url .'/flash",
									solution: "flash, html",
									supplied: "ogv, m4v, all",
									cssSelectorAncestor: "#jp_container_'. $id.'",
									size: {
										width: "100%",
										height: "100%"
									}
								});
								jPlayerObj.on(jQuery.jPlayer.event.ready + ".jp-repeat", function(event) {
									jQuery("img", this).addClass("poster");
									jQuery("video", this).addClass("video");
									jQuery("object", this).addClass("flashObject");
									jQuery(".video", jPlayerContainer).on("click", function(){
										jPlayerObj.jPlayer("pause");
									})
								})
								jPlayerObj.on(jQuery.jPlayer.event.ended + ".jp-repeat", function(event) {
									isPause = true
									jQuery(".poster", jPlayerContainer).css({display:"inline"});
								    jQuery(".video", jPlayerContainer).css({width:"0%", height:"0%"});
								    jQuery(".flashObject", jPlayerContainer).css({width:"0%", height:"0%"});
								    jPlayerObj.siblings(".jp-gui").find(".jp-video-play").css({display:"block"});
								});
								jPlayerObj.on(jQuery.jPlayer.event.play + ".jp-repeat", function(event) {
								   isPause = false
								   emulSwitch(isPause);
								});
								jPlayerObj.on(jQuery.jPlayer.event.pause + ".jp-repeat", function(event) {
								   isPause = true
								   emulSwitch(isPause);
								});
								function emulSwitch(_pause){
									if(_pause){
										jQuery(".poster", jPlayerContainer).css({display:"none"});
								    	jQuery(".video", jPlayerContainer).css({width:"100%", height:"100%"});
								    	jQuery(".flashObject", jPlayerContainer).css({width:"100%", height:"100%"});
								    	jPlayerObj.siblings(".jp-gui").find(".jp-video-play").css({display:"block"});
									}else{
										jQuery(".poster", jPlayerContainer).css({display:"none"});
								    	jQuery(".video", jPlayerContainer).css({width:"100%", height:"100%"});
								    	jQuery(".flashObject", jPlayerContainer).css({width:"100%", height:"100%"});
								    	jPlayerObj.siblings(".jp-gui").find(".jp-video-play").css({display:"none"});
									}
								}
							});
							</script>';
							$output .= '<div id="jp_container_'. $id .'" class="jp-video fullwidth">';
							$output .= '<div class="jp-type-list-parent">';
							$output .= '<div class="jp-type-single">';
							$output .= '<div id="jquery_jplayer_'. $id .'" class="jp-jplayer"></div>';
							$output .= '<div class="jp-gui">';
							$output .= '<div class="jp-video-play">';
							$output .= '<a href="javascript:;" class="jp-video-play-icon" tabindex="1" title="'.__('Play', CHERRY_PLUGIN_DOMAIN).'">'.__('Play', CHERRY_PLUGIN_DOMAIN).'</a></div>';
							$output .= '<div class="jp-interface">';
							$output .= '<div class="jp-progress">';
							$output .= '<div class="jp-seek-bar">';
							$output .= '<div class="jp-play-bar">';
							$output .= '</div></div></div>';
							$output .= '<div class="jp-duration"></div>';
							$output .= '<div class="jp-time-sep">/</div>';
							$output .= '<div class="jp-current-time"></div>';
							$output .= '<div class="jp-controls-holder">';
							$output .= '<ul class="jp-controls">';
							$output .= '<li><a href="javascript:;" class="jp-play" tabindex="1" title="'.__('Play', CHERRY_PLUGIN_DOMAIN).'"><span>'.__('Play', CHERRY_PLUGIN_DOMAIN).'</span></a></li>';
							$output .= '<li><a href="javascript:;" class="jp-pause" tabindex="1" title="'.__('Pause', CHERRY_PLUGIN_DOMAIN).'"><span>'.__('Pause', CHERRY_PLUGIN_DOMAIN).'</span></a></li>';
							$output .= '<li class="li-jp-stop"><a href="javascript:;" class="jp-stop" tabindex="1" title="'.__('Stop', CHERRY_PLUGIN_DOMAIN).'"><span>'.__('Stop', CHERRY_PLUGIN_DOMAIN).'</span></a></li>';
							$output .= '</ul>';
							$output .= '<div class="jp-volume-bar">';
							$output .= '<div class="jp-volume-bar-value">';
							$output .= '</div></div>';
							$output .= '<ul class="jp-toggles">';
							$output .= '<li><a href="javascript:;" class="jp-mute" tabindex="1" title="'.__('Mute', CHERRY_PLUGIN_DOMAIN).'"><span>'.__('Mute', CHERRY_PLUGIN_DOMAIN).'</span></a></li>';
							$output .= '<li><a href="javascript:;" class="jp-unmute" tabindex="1" title="'.__('Unmute', CHERRY_PLUGIN_DOMAIN).'"><span>'.__('Unmute', CHERRY_PLUGIN_DOMAIN).'</span></a></li>';
							$output .= '</ul>';
							$output .= '</div></div>';
							$output .= '<div class="jp-no-solution">';
							$output .= '<span>'.__('Update Required.', CHERRY_PLUGIN_DOMAIN).'</span>'.__('To play the media you will need to either update your browser to a recent version or update your ', CHERRY_PLUGIN_DOMAIN).'<a href="http://get.adobe.com/flashplayer/" target="_blank">'.__('Flash plugin', CHERRY_PLUGIN_DOMAIN).'</a>';
							$output .= '</div></div></div></div>';
							$output .= '</div>';
					} else {
						$output .= '<div class="video-wrap">' . stripslashes(htmlspecialchars_decode($embed)) . '</div>';
					}

					if($excerpt_count >= 1){
						$output .= '<div class="excerpt">';
							$output .= my_string_limit_words($excerpt,$excerpt_count);
						$output .= '</div>';
				}

				//Standard
				} else {

					if ($thumb == 'true') {
						if ( has_post_thumbnail($post->ID) ){
							$output .= '<figure class="thumbnail featured-thumbnail"><a href="'.get_permalink($post->ID).'" title="'.get_the_title($post->ID).'">';
							$output .= '<img src="'.$image.'" alt="' . get_the_title($post->ID) .'"/>';
							$output .= '</a></figure>';
						}
					}
					
					if ($meta == 'true') {
							$output .= '<span class="meta">';
									$output .= '<span class="post-date">';
										$output .= get_the_date('D j M, Y');
									$output .= '</span>';
									$output .= '<span class="post-comments">';
										$output .= '<a href="'.get_comments_link($post->ID).'">';
											$output .= get_comments_number($post->ID);
										$output .= '</a>';
									$output .= '</span>';
							$output .= '</span>';
					}
					$output .= '<h5><a href="'.get_permalink($post->ID).'" title="'.get_the_title($post->ID).'">';
							$output .= get_the_title($post->ID);
					$output .= '</a></h5>';
					
					$output .= cherry_get_post_networks(array('post_id' => $post->ID, 'display_title' => false, 'output_type' => 'return'));
					if ($excerpt_count >= 1) {
						$output .= '<div class="excerpt">';
							$output .= my_string_limit_words($excerpt,$excerpt_count);
						$output .= '</div>';
					}
					if ($more_text_single!="") {
						$output .= '<a href="'.get_permalink($post->ID).'" class="btn btn-primary" title="'.get_the_title($post->ID).'">';
						$output .= $more_text_single;
						$output .= '</a>';
					}
				}
			$output .= '<div class="clear"></div>';
			$item_counter ++;
			$output .= '</li><!-- .entry (end) -->';
		}
		wp_reset_postdata(); // restore the global $post variable
		$output .= '</ul><!-- .recent-posts (end) -->';

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode('recent_posts', 'shortcode_recent_posts');
}


// Recent Comments
if (!function_exists('shortcode_recent_comments')) {

	function shortcode_recent_comments( $atts, $content = null, $shortcodename = '' ) {
		extract(shortcode_atts(array(
			'num'          => '5',
			'custom_class' => ''
		), $atts));

		global $wpdb;
		$itemcounter = 0;

		if ( function_exists( 'wpml_get_language_information' ) ) {
			global $sitepress;
			$sql = "
				SELECT * FROM {$wpdb->comments}
				JOIN {$wpdb->prefix}icl_translations
				ON {$wpdb->comments}.comment_post_id = {$wpdb->prefix}icl_translations.element_id
				AND {$wpdb->prefix}icl_translations.element_type='post_post'
				WHERE comment_approved = '1'
				AND language_code = '".$sitepress->get_current_language()."'
				ORDER BY comment_date_gmt DESC LIMIT {$num}";
		} else {
			$sql = "
				SELECT * FROM $wpdb->comments
				LEFT OUTER JOIN $wpdb->posts
				ON ($wpdb->comments.comment_post_ID = $wpdb->posts.ID)
				WHERE comment_approved = '1'
				AND comment_type = ''
				AND post_password = ''
				ORDER BY comment_date_gmt DESC LIMIT {$num}";
		}

		$comment_len = 100;
		$comments = $wpdb->get_results($sql);

		$output = '<ul class="recent-comments unstyled">';

		foreach ($comments as $comment) {
			$output .= '<li class="list-item-'.$itemcounter.'">';
				$output .= '<a href="'.get_comment_link($comment->comment_ID).'" title="on '.get_the_title($comment->comment_post_ID).'">';
					$output .= strip_tags($comment->comment_author).' : ' . strip_tags(substr(apply_filters('get_comment_text', $comment->comment_content), 0, $comment_len));
					if (strlen($comment->comment_content) > $comment_len) $output .= '...';
				$output .= '</a>';
			$output .= '</li>';
			$itemcounter++;
		}

		$output .= '</ul>';

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode('recent_comments', 'shortcode_recent_comments');
}


//Recent Testimonials
if (!function_exists('shortcode_recenttesti')) {

	function shortcode_recenttesti( $atts, $content = null, $shortcodename = '' ) {
		extract(shortcode_atts(array(
				'num'           => '5',
				'thumb'         => 'true',
				'excerpt_count' => '30',
				'custom_class'  => '',
		), $atts));

		// WPML filter
		$suppress_filters = get_option('suppress_filters');

		$args = array(
				'post_type'        => 'testi',
				'numberposts'      => $num,
				'orderby'          => 'post_date',
				'suppress_filters' => $suppress_filters
			);
		$testi = get_posts($args);

		$itemcounter = 0;

		$output = '<div class="testimonials '.$custom_class.'">';

		global $post;
		global $my_string_limit_words;

		foreach ($testi as $k => $post) {
			//Check if WPML is activated
			if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
				global $sitepress;

				$post_lang = $sitepress->get_language_for_element($post->ID, 'post_testi');
				$curr_lang = $sitepress->get_current_language();
				// Unset not translated posts
				if ( $post_lang != $curr_lang ) {
					unset( $testi[$k] );
				}
				// Post ID is different in a second language Solution
				if ( function_exists( 'icl_object_id' ) ) {
					$post = get_post( icl_object_id( $post->ID, 'testi', true ) );
				}
			}
			setup_postdata( $post );
			$post_id = $post->ID;
			$excerpt = get_the_excerpt();

			// Get custom metabox value.
			$testiname  = get_post_meta( $post_id, 'my_testi_caption', true );
			$testiurl   = esc_url( get_post_meta( $post_id, 'my_testi_url', true ) );
			$testiinfo  = get_post_meta( $post_id, 'my_testi_info', true );
			$testiemail = sanitize_email( get_post_meta( $post_id, 'my_testi_email', true ) );

			$attachment_url = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'full' );
			$url            = $attachment_url['0'];
			$image          = aq_resize($url, 280, 240, true);

			$output .= '<div class="testi-item list-item-'.$itemcounter.'">';
				$output .= '<blockquote class="testi-item_blockquote">';
					if ($thumb == 'true') {
						if ( has_post_thumbnail( $post_id ) ){
							$output .= '<figure class="featured-thumbnail">';
							$output .= '<img src="'.$image.'" alt="" />';
							$output .= '</figure>';
						}
					}
					$output .= '<a href="'.get_permalink( $post_id ).'">';
						$output .= my_string_limit_words($excerpt,$excerpt_count);
					$output .= '</a><div class="clear"></div>';

				$output .= '</blockquote>';

				$output .= '<small class="testi-meta">';
					if ( !empty( $testiname ) ) {
						$output .= '<span class="user">';
							$output .= $testiname;
						$output .= '</span>';
					}

					if ( !empty( $testiinfo ) ) {
						$output .= ' <span class="info">';
							$output .= $testiinfo;
						$output .= '</span><br>';
					}

					if ( !empty( $testiurl ) ) {
						$output .= '<a class="testi-url" href="'.$testiurl.'">';
							$output .= $testiurl;
						$output .= '</a><br>';
					}

					if ( !empty( $testiemail ) && is_email( $testiemail ) ) {
						$output .= '<a class="testi-email" href="mailto:' . antispambot( $testiemail, 1 ) . '" >' . antispambot( $testiemail ) . ' </a>';
					}

				$output .= '</small>';

			$output .= '</div>';
			$itemcounter++;

		}
		wp_reset_postdata(); // restore the global $post variable
		$output .= '</div>';

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode('recenttesti', 'shortcode_recenttesti');

}


//Tag Cloud
if (!function_exists('shortcode_tags')) {

	function shortcode_tags( $atts, $content = null, $shortcodename = '' ) {
		$output = '<div class="tags-cloud clearfix">';
		$tags = wp_tag_cloud('smallest=8&largest=8&format=array');

		foreach($tags as $tag){
			$output .= $tag.' ';
		}

		$output .= '</div><!-- .tags-cloud (end) -->';

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode('tags', 'shortcode_tags');

}

//video preview
if (!function_exists('shortcode_video_preview')) {
	function shortcode_video_preview( $atts, $content = null, $shortcodename = '' ) {
		extract(shortcode_atts(
			array(
				'title' => '',
				'post_url' => '',
				'date' => '',
				'author' => '',
				'custom_class' => '',
			), $atts));
		$output_title = '';
		$output_author = '';
		$output_date = '';
		$post_ID = url_to_postid($post_url);
		$get_post = get_post($post_ID);
		$get_user = get_userdata($get_post->post_author);
		$user_url = get_bloginfo('url').'/author/'.$get_user->user_nicename;
		$video_url = parser_video_url(get_post_meta($post_ID, 'tz_video_embed', true));
		$get_image_url = video_image($video_url);
		$img='';

		if($title=="yes"){
			$output_title = '<h4><a href="'.$post_url.'" title="'.$get_post->post_title.'">'.$get_post->post_title.'</a></h4>';
		}
		if($author=="yes"){
			$output_author = '<span class="post_author">Posts by <a href="'.$user_url.'" title="Posts by '.$get_user->user_nicename.'"  rel="author">'.$get_user->user_nicename.'</a></span>';
		}
		if($date=="yes"){
			$output_date = '<span class="post_date"><time datetime="'.$get_post->post_date.'"> '.get_the_date().'</time></span>';
		}
		if($get_image_url!=false && $get_image_url!=''){
			$img = '<a class="preview_image"  href="'.$post_url.'" title="'.$get_image_url.'"><img src="'.$get_image_url.'" alt=""><span class="icon-play-circle hover"></span></a>';
		}
		$output ='<figure class="featured-thumbnail thumbnail video_preview clearfix'.$custom_class.'"><div>'.$img.'<figcaption>'.$output_title.$output_author.$output_date.'</figcaption></div></figure>';

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
		}
	add_shortcode('video_preview', 'shortcode_video_preview');
}

// Content Box
if ( !function_exists( 'content_box_shortcode' ) ) {
	function content_box_shortcode( $atts, $content = null, $shortcodename = '' ) {
		extract( shortcode_atts(
			array(
				'custom_class' => '',
		), $atts ) );

		$output = '<div class="content_box ' . $custom_class . '">';
			$output .= do_shortcode( $content );
			$output .= '<div class="clear"></div>';
		$output .= '</div><!-- .content_box (end) -->';

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode( 'content_box', 'content_box_shortcode' );
}

if (!function_exists('parser_video_url')) {
	function parser_video_url($video_url){
		$video_url = explode(" ", $video_url);
		foreach ($video_url as $item) {
			if(stripos($item, 'src')!==false){
				$url_array = parse_url($item);
				$video_url = $url_array["path"];
				$video_url = stripcslashes($video_url);
				$video_url = strip_tags($video_url);
				$video_url = str_replace('&quot;', '', $video_url);
				break;
			}
		}
		return $video_url;
	}
}
if (!function_exists('video_image')) {
	function video_image($url){
		if($url[0]!==''){
			$image_id = basename($url);
			if(stripos($url, "youtube")!==false){
				return "http://img.youtube.com/vi/".$image_id."/0.jpg";
			} else if(stripos($url, "vimeo")!==false){
				$get_header = @get_headers("http://vimeo.com/api/v2/video/".$image_id.".php");
				if(stripos($get_header[0],'200 OK')){
					$hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".$image_id.".php"));
					return $hash[0]["thumbnail_large"];
				}else{
					return false;
				}
			}
		}else{
			return false;
		}
	}
}
?>