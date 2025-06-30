<article id="post-<?php the_ID(); ?>" <?php post_class('post__holder'); ?>>
		<?php get_template_part('includes/post-formats/post-thumb'); ?>

		<?php if(!is_singular()) : ?>
		<!-- Post Content -->
		<div class="post_content">
			<?php if(!is_singular()) : ?>
				<?php if(is_sticky()) : ?>
					<h5 class="post-label"><?php echo theme_locals("featured");?></h5>
				<?php endif; ?>
				<h2 class="post-title"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
			<?php endif; ?>
			<?php
				if (of_get_option('post_excerpt')=="true" || of_get_option('post_excerpt')=='') { ?>
					<div class="excerpt">
					<?php

					if (has_excerpt()) {
						the_excerpt();
					} else {
						if (!is_search()) {
							$content = get_the_content();
							echo wp_trim_words( $content, 55 );
						} else {
							$excerpt = get_the_excerpt();
							echo wp_trim_words( $excerpt, 55 );
						}
					} ?>
				</div>
			<?php }
				$button_text = of_get_option('blog_button_text') ? apply_filters( 'cherry_text_translate', of_get_option('blog_button_text'), 'blog_button_text' ) : theme_locals("read_more") ;
			?>
			<a href="<?php the_permalink() ?>" class="btn btn-primary"><?php echo $button_text; ?></a>
			<div class="clear"></div>
		</div>

		<?php else :?>
		<!-- Post Content -->
		<div class="post_content">
			<?php the_content(''); ?>
            
            <div class="custom">
            	<br />
                <?php if(get_field('produttore')){?>
                	<p><span class="custom_title">Produttore:</span> <span class="custom_value"><?php the_field('produttore')?></span></p>
                <?php } ?>

				<?php if(get_field('tipologia')){?>
                	<p><span class="custom_title">Tipologia:</span> <span class="custom_value"><?php the_field('tipologia')?></span></p>
                <?php } ?>

				<?php if(get_field('vitigno')){?>
                	<p><span class="custom_title">Vitigno:</span> <span class="custom_value"><?php the_field('vitigno')?></span></p>
                <?php } ?>

				<?php if(get_field('luogo')){?>
                	<p><span class="custom_title">Luogo di produzione:</span> <span class="custom_value"><?php the_field('luogo')?></span></p>
                <?php } ?>

			
            </div>
            
            
			<div class="clear"></div>
		</div>
		<!-- //Post Content -->
		<?php endif; ?>

		<?php //get_template_part('includes/post-formats/post-meta'); ?>

</article>