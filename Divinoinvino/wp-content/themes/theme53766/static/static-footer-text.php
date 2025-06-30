<?php /* Static Name: Footer text */ ?>
<div id="footer-text" class="footer-text">
	<?php $myfooter_text = apply_filters( 'cherry_text_translate', of_get_option('footer_text'), 'footer_text' ); ?>

	<?php if($myfooter_text){?>
		<?php echo $myfooter_text; ?>
	<?php } else { ?>
		<?php bloginfo('name'); ?> &copy; <?php echo date('Y'); ?>. <a href="<?php echo home_url(); ?>/privacy-policy/" title="Privacy Policy"><?php _e('Privacy Policy', CURRENT_THEME); ?></a>
	<?php } ?>
	<?php if( is_front_page() ) { ?>
		|<a rel="nofollow" href="http://www.titaliaweb.it" target="_blank">Powered by Titaliaweb</a>
	<?php } ?>
</div>