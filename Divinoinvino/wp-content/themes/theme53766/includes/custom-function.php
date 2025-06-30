<?php
	// Loads child theme textdomain
	load_child_theme_textdomain( CURRENT_THEME, CHILD_DIR . '/languages' );

	add_filter( 'cherry_stickmenu_selector', 'cherry_change_selector' );
	function cherry_change_selector($selector) {
		$selector = 'header .stuck_menu_wrap';
		return $selector;
	}

		/*   Page_Block_SHORTCODE    */
	function page_block_shortcode($atts, $content = null) {
		extract(shortcode_atts(array(
				'hash_id'  => ''
		), $atts));
		$output = '<div class="hashAncor" id="'.$hash_id.'"></div>';
	    return $output;
	}
	add_shortcode('page_block', 'page_block_shortcode');

//------------------------------------------------------
//  Post Meta
//------------------------------------------------------
	$global_meta_elements = array();
	function get_post_metadata_custom( $args = array() ) {
		global $global_meta_elements;
		if(array_key_exists('meta_elements', $args)){
			$global_meta_elements = array_unique(array_merge($global_meta_elements, $args['meta_elements']));
		}

		$meta_elements_empty  = isset($args['meta_elements']) ? false : true ;
		$defaults = array(
						'meta_elements' =>  array('start_unite', 'date', 'author', 'permalink', 'end_unite', 'start_unite', 'categories', 'tags', 'end_unite', 'start_unite', 'comment', 'views', 'like', 'dislike', 'end_unite'),
						'meta_class' => 'post_meta',
						'meta_before' => '',
						'meta_after'  => '',
						'display_meta_data' => true
					);
		$args = wp_parse_args( $args, $defaults );
		$post_meta_type = (of_get_option('post_meta') == 'true' || of_get_option('post_meta') == '') ? 'line' : of_get_option('post_meta');
		if($meta_elements_empty){
			foreach ($global_meta_elements as $key) {
				if($key != 'end_unite || start_unite'){
				unset($args['meta_elements'][array_search($key, $args['meta_elements'])]);
				}
			}
		}

		if($post_meta_type!='false' && $args['display_meta_data']){
			$post_ID = get_the_ID();
			$post_type = get_post_type($post_ID);
			$icon_tips_before = ($post_meta_type == 'icon') ? '<div class="tips">' : '';
			$icon_tips_after = ($post_meta_type == 'icon') ? '</div>' : '';

			$user_login = is_user_logged_in() ? true : false;
			$user_id = $user_login ? get_current_user_id() : "";
			$voting_class = $user_login ? 'ajax_voting ' : 'not_voting ';
			$voting_url ='../CherryFramework/includes/voting.php?post_ID='.$post_ID.'&amp;get_user_ID='.$user_id;
			$get_voting_array = cherry_getPostVoting($post_ID, $user_id);
			$user_voting = $get_voting_array['user_voting'];

			echo $args['meta_before'].'<div class="'.$args['meta_class'].' meta_type_'.$post_meta_type.'">';
				foreach ($args['meta_elements'] as $value) {
					switch ($value) {
						case 'date':
							if(of_get_option('post_date') != 'no'){ ?>
								<div class="post_date">
									<i class="icon-time"></i>
									<?php echo $icon_tips_before . '<time datetime="' . get_the_time('Y-m-d\TH:i:s') . '">' . get_the_date('D j M, Y') . '</time>' . $icon_tips_after; ?>
								</div>
								<?php
							}
							break;
						case 'author':
							if(of_get_option('post_author') != 'no'){ ?>
								<div class="post_author">
									<i class="icon-user"></i>
									<?php
									echo $icon_tips_before;
									the_author_posts_link();
									echo $icon_tips_after;
									?>
								</div>
								<?php
							}
							break;
						case 'permalink':
							if(of_get_option('post_permalink') != 'no' && !is_singular()){ ?>
								<div class="post_permalink">
									<i class="icon-link"></i>
									<?php echo $icon_tips_before.'<a href="'.get_permalink().'" title="'.get_the_title().'">'.theme_locals('permalink_to').'</a>'.$icon_tips_after; ?>
								</div>
								<?php
							}
							break;
						case 'categories':
							if(of_get_option('post_category') != 'no'){ ?>
								<div class="post_category">
									<i class="icon-bookmark"></i>
									<?php
										echo $icon_tips_before;
										if($post_type != 'post'){
											$custom_category = !is_wp_error(get_the_term_list($post_ID, $post_type.'_category','',', ')) ? get_the_term_list($post_ID, $post_type.'_category','',', ') : theme_locals('has_not_category');
											echo $custom_category;
										}else{
											the_category(', ');
										}
										echo $icon_tips_after;
									?>
								</div>
								<?php
							}
							break;
						case 'tags':
							if(of_get_option('post_tag') != 'no'){ ?>
								<div class="post_tag">
									<i class="icon-tag"></i>
									<?php
										echo $icon_tips_before;
										if(get_the_tags() || has_term('', $post_type.'_tag', $post_ID)){
											echo ($post_type != 'post') ? the_terms($post_ID, $post_type.'_tag','',', ') : the_tags('', ', ');
										} else {
											echo theme_locals('has_not_tags');
										}
										echo $icon_tips_after;
									 ?>
								</div>
								<?php
							}
							break;
						case 'comment':
							if(of_get_option('post_comment') != 'no'){ ?>
								<div class="post_comment">
									<i class="icon-comment-alt"></i>
									<?php
										echo $icon_tips_before;
										comments_popup_link(theme_locals('no_comments'), theme_locals('comment'), '% '.theme_locals('comments'), theme_locals('comments_link'), theme_locals('comments_closed'));
										echo $icon_tips_after;
									 ?>
								</div>
								<?php
							}
							break;
						case 'views':
							if(of_get_option('post_views') != 'no'){ ?>
								<div class="post_views" title="<?php echo theme_locals('number_views'); ?>">
									<i class="icon-eye-open"></i>
									<?php echo $icon_tips_before.cherry_getPostViews($post_ID).$icon_tips_after; ?>
								</div>
								<?php
							}
							break;
						case 'dislike':
							if(of_get_option('post_dislike') != 'no'){
								$dislike_url = ($user_login && $user_voting=='none') ? 'href="'.$voting_url.'&amp;voting=dislike"' : '';
								$dislike_count = $get_voting_array['dislike_count'];
								$dislike_title = $user_login ? theme_locals('dislike') : theme_locals('not_voting');
								$dislike_class = ($user_voting == 'dislike') ? 'user_dislike ' : '';
								if($user_voting!='none'){
									$voting_class = "user_voting ";
								}
							?>
								<div class="post_dislike">
									<a <?php echo $dislike_url; ?> class="<?php echo $voting_class.$dislike_class; ?>" title="<?php echo $dislike_title; ?>" date-type="dislike" >
										<i class="icon-thumbs-down"></i>
										<?php echo $icon_tips_before.'<span class="voting_count">'.$dislike_count.'</span>'.$icon_tips_after; ?>
									</a>
								</div>
								<?php
							}
							break;
						case 'like':
							if(of_get_option('post_like') != 'no'){
								$like_url = ($user_login && $user_voting=='none') ? 'href="'.$voting_url.'&amp;voting=like"' : '';
								$like_count = $get_voting_array['like_count'];
								$like_title = $user_login ? theme_locals('like') : theme_locals('not_voting');
								$like_class = ($user_voting == 'like') ? 'user_like ' : '';
								if($user_voting!='none'){
									$voting_class = "user_voting ";
								}
							?>
								<div class="post_like">
									<a <?php echo $like_url; ?> class="<?php echo $voting_class.$like_class; ?>" title="<?php echo $like_title; ?>" date-type="like" >
										<i class="icon-thumbs-up"></i>
										<?php echo $icon_tips_before.'<span class="voting_count">'.$like_count.'</span>'.$icon_tips_after; ?>
									</a>
								</div>
								<?php
							}
						break;
						case 'start_unite':
							echo '<div class="post_meta_unite clearfix">';
						break;
						case 'end_unite':
							echo '</div>';
						break;
						case 'start_group':
							echo '<div class="meta_group clearfix">';
						break;
						case 'end_group':
							echo '</div>';
						break;
					}
				}
			echo '</div>'.$args['meta_after'];
		}
	}

//------------------------------------------------------
//  Related Posts
//------------------------------------------------------
	if(!function_exists('cherry_related_posts')){
		function cherry_related_posts_exc($args = array()){
			global $post;
			$default = array(
				'post_type' => get_post_type($post),
				'class' => 'related-posts',
				'class_list' => 'related-posts_list',
				'class_list_item' => 'related-posts_item',
				'display_title' => true,
				'display_link' => true,
				'display_thumbnail' => true,
				'width_thumbnail' => 370,
				'height_thumbnail' => 240,
				'before_title' => '<h3 class="related-posts_h">',
				'after_title' => '</h3>',
				'posts_count' => 3
			);
			extract(array_merge($default, $args));

			$post_tags = wp_get_post_terms($post->ID, $post_type.'_tag', array("fields" => "slugs"));
			$tags_type = $post_type=='post' ? 'tag' : $post_type.'_tag' ;
			$suppress_filters = get_option('suppress_filters');// WPML filter
			$blog_related = apply_filters( 'cherry_text_translate', of_get_option('blog_related'), 'blog_related' );
			if ($post_tags && !is_wp_error($post_tags)) {
				$args = array(
					"$tags_type" => implode(',', $post_tags),
					'post_status' => 'publish',
					'posts_per_page' => $posts_count,
					'ignore_sticky_posts' => 1,
					'post__not_in' => array($post->ID),
					'post_type' => $post_type,
					'suppress_filters' => $suppress_filters
					);
				query_posts($args);
				if ( have_posts() ) {
					$output = '<div class="'.$class.'">';
					$output .= $display_title ? $before_title.$blog_related.$after_title : '' ;
					$output .= '<ul class="'.$class_list.' clearfix">';
					while( have_posts() ) {
						the_post();
						$thumb   = has_post_thumbnail() ? get_post_thumbnail_id() : PARENT_URL.'/images/empty_thumb.gif';
						$blank_img = stripos($thumb, 'empty_thumb.gif');
						$img_url = $blank_img ? $thumb : wp_get_attachment_url( $thumb,'full');
						$image   = $blank_img ? $thumb : aq_resize($img_url, $width_thumbnail, $height_thumbnail, true) or $img_url;

						$output .= '<li class="'.$class_list_item.'">';
						$output .= $display_thumbnail ? '<figure class="thumbnail featured-thumbnail"><a href="'.get_permalink().'" title="'.get_the_title().'"><img data-src="'.$image.'" alt="'.get_the_title().'" /></a></figure>': '' ;
						$output .= '<div class="related_content">';
						$output .= $display_link ? '<a href="'.get_permalink().'" >'.get_the_title().'</a>': '' ;
						$output .= '<div class="excerpt">';
						if (has_excerpt()) {
							the_excerpt();
						} else {
							if (!is_search()) {
								$content = get_the_content();
								$output .=  wp_trim_words( $content, 18 );
							} else {
								$excerpt = get_the_excerpt();
								$output .=  wp_trim_words( $excerpt, 18 );
							}
						}
						$output .= '</div>';
						$output .= '</div>';
						$output .= '</li>';
					}
					$output .= '</ul></div>';
					echo $output;
				}
				wp_reset_query();
			}
		}
	}	

/*-----------------------------------------------------------------------------------*/
/* Custom Comments Structure
/*-----------------------------------------------------------------------------------*/
if ( !function_exists( 'mytheme_comment' ) ) {
	function mytheme_comment_custom($comment, $args, $depth) {
		$GLOBALS['comment'] = $comment;
	?>
	<li <?php comment_class('clearfix'); ?> id="li-comment-<?php comment_ID() ?>">
		<div id="comment-<?php comment_ID(); ?>" class="comment-body clearfix">
			<div class="wrapper">
				<div class="comment-author vcard">
					<?php echo get_avatar( $comment->comment_author_email, 65 ); ?>
				</div>
				<?php printf('<span class="author">%1$s</span>', get_comment_author_link()) ?>
				<div class="comment-meta commentmetadata"><?php printf('%1$s', get_comment_date('D j M, Y')) ?></div>
				<?php if ($comment->comment_approved == '0') : ?>
					<em><?php echo theme_locals("your_comment") ?></em>
				<?php endif; ?>
				<div class="reply">
					<?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
				</div>
				<div class="extra-wrap">
					<?php echo esc_html( get_comment_text() ); ?>
				</div>
			</div>
		</div>
<?php }
}

	require_once('shortcodes/shortcodes.php' );
	//require_once('includes/post-formats/related-posts.php' );
	//require_once('meta-box/meta-manager.php');
	//require_once('meta-box/post-meta-init.php');
	// Loads custom scripts.
	require_once( 'custom-js.php' );
?>