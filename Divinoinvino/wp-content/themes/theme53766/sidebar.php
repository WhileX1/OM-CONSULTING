<?php

/**

* Sidebar Name: Static Sidebar

*/
?>

<?php 
if (strpos($_SERVER['REQUEST_URI'], "vini")){
	
	echo "<div class='filtro-vini'><h3>Filtro</h3>";

$caratteristiche = array("tipologia", "vitigno", "produttore", "luogo");

foreach($caratteristiche as $c1){
	if(!empty($_GET[$c1])){
		$selezione[$c1] = explode(",", strtolower($_GET[$c1]));
	}
}

foreach($caratteristiche as $c){
	
	$values[$c] = $wpdb->get_col("SELECT meta_value FROM $wpdb->postmeta INNER JOIN $wpdb->term_relationships ON $wpdb->postmeta.post_id=$wpdb->term_relationships.object_id INNER JOIN $wpdb->posts ON $wpdb->postmeta.post_id = $wpdb->posts.ID WHERE meta_key = '{$c}' AND term_taxonomy_id = 35 AND post_status='publish'" );	
	$values[$c] = array_unique($values[$c]);
	$values[$c] = array_filter($values[$c], 'strlen');
	sort($values[$c]);
}

	foreach($values as $scelta_label=>$valori){
		echo '<div id="search-wine-'.$scelta_label.'">';
		echo "<p class='title-filter'>".$scelta_label."</p>";
		echo '<select>
		<option value="">- - seleziona - -</option>
		';
		foreach($valori as $scelta_value){
			if(is_array ($selezione[$scelta_label])){
				 if(in_array(strtolower($scelta_value), $selezione[$scelta_label])){
					 $checked ="selected='selected'";
				 }else{
					 $checked="";
				 }
			}else{
				if(strcasecmp($scelta_value, $selezione[$scelta_label]) == 0){
					$checked = "selected='selected'";
				}else{
					$checked="";
				}
			}
			?>
	        <option value="<?php echo $scelta_value ?>" <?php echo $checked?>><?php echo $scelta_value ?></option>
    	<?php
        }
		echo "</select>";
		echo "</div>";
		?>
        
        <?php 
		if(strpos($_SERVER['REQUEST_URI'], "?") != FALSE){
			
			$urlreplace = $_SERVER['REQUEST_URI']."&".$scelta_label."=";
		}else{
			$urlreplace = home_url('/category/vini/')."?".$scelta_label."=";	
		}
		
		?>		
        
		<script type="text/javascript">
			$( document ).ready(function($){
				$("#search-wine-<?php echo $scelta_label ?>").on('change', 'select', function(){
					// vars
					vals = [];
					vals = ($(this).val());

					<?php if(strpos($_SERVER['REQUEST_URI'], "?") != FALSE){ 
						if(strpos($_SERVER['REQUEST_URI'], $scelta_label)){?>
							var newurl = updateUrlParameter( "<?php echo $scelta_label ?>", vals, "<?php echo $_SERVER['REQUEST_URI']?>");
						<?php }else{ ?>
							var newurl = "<?php echo $_SERVER['REQUEST_URI']."&".$scelta_label."=" ?>"+vals;
						<?php } ?>						
					<?php } else{ ?>
						var newurl = "<?php echo home_url('/category/vini/')."?".$scelta_label."="; ?>"+vals;
					<?php } ?>
					
					window.location.replace(newurl);
					consol.log(vals);				
				});
			});
			
			
			
			function updateUrlParameter(param, value, url){
				if(value == null|| value.length === 0|| value.length === 1){
					var regex = new RegExp('('+param+'=)[^\&]+');
					return url.replace( regex , '');
				}else{
					
					var regex = new RegExp('('+param+'=)[^\&]+');
					return url.replace( regex , '$1' + value);
				}
			}
			
		</script>



		
		
<?php }
echo "</div>";
}
?>


<?php if ( ! dynamic_sidebar( theme_locals("sidebar") )) : ?>


	<div id="sidebar-search" class="widget">

		<?php echo '<h3>' . theme_locals("search") . '</h3>'; ?>

		<?php get_search_form(); ?> <!-- outputs the default Wordpress search form-->

	</div>



	<div id="sidebar-nav" class="widget menu">

		<?php echo '<h3>' . theme_locals("navigation") . '</h3>'; ?>

		<?php wp_nav_menu( array('menu' => 'Sidebar Menu' )); ?> <!-- editable within the Wordpress backend -->

	</div>



	<div id="sidebar-archives" class="widget">

		<?php echo '<h3>' . theme_locals("archives") . '</h3>'; ?>

		<ul>

			<?php wp_get_archives( 'type=monthly' ); ?>

		</ul>

	</div>



	<div id="sidebar-meta" class="widget">

		<?php echo '<h3>' . theme_locals("meta") . '</h3>'; ?>

		<ul>

			<?php wp_register(); ?>

			<li><?php wp_loginout(); ?></li>

			<?php wp_meta(); ?>

		</ul>

	</div>
<?php endif; ?>