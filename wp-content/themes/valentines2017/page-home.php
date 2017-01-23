<?php

/*

Template Name: Homepage

*/

?>



<?php get_header(); ?>


<div id="content">



	<div id="inner-content" class="clearfix">



		<div id="main" class="first clearfix" role="main">



			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>



				<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article">

					<div class="top-image" style="background-image: url('<?php 
						if ( has_post_thumbnail() ) {
							the_post_thumbnail_url( 'full' ); 
						}  ?>');">
						<canvas id="canvas" style="width:100%"></canvas>
						
						<div class="image-above" > 
							<div class="wrap">
								<div class="image-above-content">
									<h1>Schon bald ist Valentinstag!</h1>
									<a href="spezialangebot">ZUM SPEZIALANGEBOT</a>
								</div>
							</div>
						</div>
					</div>

					<section class="entry-content">

						<?php the_content(); ?>

					</section> <!-- end article section -->



					<footer class="article-footer">

						<p class="clearfix"><?php the_tags('<span class="tags">' . __('Tags:', 'bonestheme') . '</span> ', ', ', ''); ?></p>



					</footer> <!-- end article footer -->



					<?php comments_template(); ?>



				</article> <!-- end article -->






			<?php endwhile; ?>	



		<?php else : ?>



			<article id="post-not-found" class="hentry clearfix">

				<header class="article-header">

					<h1><?php _e("Oops, Post Not Found!", "bonestheme"); ?></h1>

				</header>

				<section class="entry-content">

					<p><?php _e("Uh Oh. Something is missing. Try double checking things.", "bonestheme"); ?></p>

				</section>

				<footer class="article-footer">

					<p><?php _e("This is the error message in the page-custom.php template.", "bonestheme"); ?></p>

				</footer>

			</article>



		<?php endif; ?>

		<div class="subheading text-center wrap">
			<h2>Floristen-Tipps</h2>
		</div>
		<div class="wrap">
			<div class="tipps-wrapper clearfix">
				<div class="tipps-inner medium-gap">
					<?php 
					$args = array( 'post_type' => 'tipp', 'posts_per_page' => 2 );
					$loop = new WP_Query( $args );
					while ( $loop->have_posts() ) : $loop->the_post(); ?>


					<article id="tip-<?php the_ID(); ?>" class="text-just-centered tip" role="article">


						<?php	if (has_post_thumbnail()) : ?>


							<img src="<?php the_post_thumbnail_url( 'full' ); ?>" width="500" height="165"></img>

						<?php endif; ?>

						<div class="front-tips-title">
							<h3><?php the_title(); ?></h3>
						</div>
						<div class="entry-content">
							<?php 
							$content = get_the_content();
							$trimmed_content = wp_trim_words( $content, 50, '<a class="read-more-link" href="tipps">mehr</a>' ); 
							echo '<p>'.$trimmed_content.'</p>'; 
							?>
						</div>
					</article>
				<?php endwhile; ?>
			</div>
		</div>
	</div>
</div> <!-- end #main -->



</div> <!-- end #inner-content -->


<?php get_sidebar('form-bar'); ?>

</div> <!-- end #content -->



<?php get_footer(); ?>
