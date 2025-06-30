<?php
//FILTRO CUSTOM FIELDS
	// array of filters (field key => field name)
	$GLOBALS['my_query_filters'] = array( 
		'tipologia'	=> 'tipologia', 
		'produttore'	=> 'produttore',
		'vitigno'	=> 'vitigno',
		'luogo'	=> 'luogo'
	);
	// action
	add_action('pre_get_posts', 'my_pre_get_posts', 10, 1);
	
	function my_pre_get_posts( $query ) {
	
	// bail early if is in admin
	if( is_admin() ) {
		
		return;
		
	}
	
	
	// get meta query
	$meta_query = $query->get('meta_query');

	
	// loop over filters
	foreach( $GLOBALS['my_query_filters'] as $key => $name ) {
		
		// continue if not found in url
		if( empty($_GET[ $name ]) ) {
			
			continue;
			
		}
		
		
		// get the value for this filter
		// eg: http://www.website.com/events?city=melbourne,sydney
		$value = explode(',', $_GET[ $name ]);
		
		
		// append meta query
    	$meta_query[] = array(
            'key'		=> $name,
            'value'		=> $value,
            'compare'	=> 'IN',
        );
        
	} 
	/*
	
	if(isset($_GET["tipologia"])){
		
		$tipologia = explode(",", $_GET['tipologia']);
		
		$meta_query[] = array(
			'key' => 'tipologia',
			'value' => $tipologia,
			'compare' => 'IN',
		);
		
	}
	*/
	
	// update meta query
	$query->set('meta_query', $meta_query);
	return;
}

?>