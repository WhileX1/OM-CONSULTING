<?php
	function posts_meta() {

		 if ( !is_admin() ) {
		  return;
		 }		 

		$meta_atts = array(
		  'id'   => 'post-meta',
		  'title'   => __('Menu options', CURRENT_THEME),
		  'description' => '',
		  'page'   => 'menu',
		  'context'  => 'normal',
		  'priority'  => 'high',
		  'fields'  => array(
		   array(
		    'name'   => __('Price', CURRENT_THEME),
		    'desc'   => '',
		    'id'   => 'menu_price',
		    'type'   => 'text',
		    'std'   => ''
		   ),
		  )
		);

		$meta_box = new posts_meta;
		$meta_box->set_meta_atts($meta_atts);

	}

	add_action('init', 'posts_meta');
?>