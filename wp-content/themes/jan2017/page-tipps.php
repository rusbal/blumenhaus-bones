<?php

/*

Template Name: Tipps

*/

?>



<?php get_header(); ?>


<div id="content">



	<div id="inner-content" class="clearfix">



		<div id="main" class="first clearfix" role="main">



			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>



				<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article">



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

		<div class="wrap">
			<div class="tipps-wrapper clearfix">
				<div class="tipps-inner medium-gap">
					<?php 
$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1; // setup pagination

$the_query = new WP_Query( array( 
	'post_type' => 'tipp',
	'paged' => $paged,
	'posts_per_page' => 10) 
);

while ( $the_query->have_posts() ) : $the_query->the_post();

?>


<article id="tip-<?php the_ID(); ?>" class="text-just-centered tip" role="article">


	<?php	if (has_post_thumbnail()) : ?>


		<img src="<?php the_post_thumbnail_url(); ?>" height="250"></img>

	<?php endif; ?>


	<h3><?php the_title(); ?></h3>
	<div class="entry-content">
		<?php the_content(); ?>
	</div>
</article>
<?php endwhile; ?>
</div>


<div class="pager">
	<div><?php print get_next_posts_link('Older', $the_query->max_num_pages) ?></div>
	<div><?php print get_previous_posts_link('Newer', $the_query->max_num_pages) ?></div>
</div>


<?php wp_reset_postdata(); ?>


</div>
</div>
</div>
</div> <!-- end #main -->



</div> <!-- end #inner-content -->


<?php get_sidebar('form-bar'); ?>

</div> <!-- end #content -->



<?php get_footer(); ?>