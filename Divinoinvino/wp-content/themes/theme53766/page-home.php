<?php
/**
* Template Name: Home Page
*/

get_header(); ?>
<?php if ( of_get_option( 'px_slider_visibility', 'true' ) == "true" ) { get_template_part('parallax-slider/parallaxSlider'); } ?>

<div class="motopress-wrapper content-holder clearfix">
	<div class="container">
		<div class="row">
			<?php do_action( 'cherry_before_home_page_content' ); ?>
			<div class="<?php echo apply_filters( 'cherry_home_layout', 'span12' ); ?>" data-motopress-wrapper-file="page-home.php" data-motopress-wrapper-type="content">
				<div class="row">
					<div class="<?php echo apply_filters( 'cherry_home_layout', 'span12' ); ?>" data-motopress-type="static" data-motopress-static-file="static/static-slider.php">
						<?//php get_template_part("static/static-slider"); ?>
					</div>
				</div>
				<div class="row">
					<div class="<?php echo apply_filters( 'cherry_home_layout', 'span12' ); ?>" data-motopress-type="loop" data-motopress-loop-file="loop/loop-page.php">
						<?php get_template_part("loop/loop-page"); ?>
					</div>
				</div>
			</div>
			<?php do_action( 'cherry_after_home_page_content' ); ?>
		</div>
	</div>
</div>
<script>
 $(document).ready(function(){

 $(window).resize(
    function(){
  $('.post_five_cols').width($(window).width());
  $('.post_five_cols').css({width: $(window).width(), "margin-left": ($(window).width()/-2), "left": "50%"});
    }
   ).trigger('resize');

  
 }); 
</script>

<?php get_footer(); ?>