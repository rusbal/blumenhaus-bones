<?php

/*

Template Name: People

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
	'post_type' => 'people',
	'paged' => $paged,
	'order'   => 'ASC',
	'posts_per_page' => 10) 
);

while ( $the_query->have_posts() ) : $the_query->the_post();

?>


<article id="tip-<?php the_ID(); ?>" class="text-just-centered tip single-user" role="article">


	<?php	if (has_post_thumbnail()) : ?>


		<img src="<?php the_post_thumbnail_url(); ?>" width="500" height="165"></img>

	<?php endif; ?>

	<?php 

	$left = get_field('left_image_people');
	$right = get_field('right_image_people');

	if( !empty($left) && !empty($right)): ?>
	<div class="user-image-wrp clearfix">
		<div class="user-image"><img src="<?php echo $left['url']; ?>" alt="<?php echo $left['alt']; ?>" /></div>
		<div class="user-image"><img src="<?php echo $right['url']; ?>" alt="<?php echo $right['alt']; ?>" /></div>
	</div>
<?php endif; ?>

<h3><?php the_title(); ?></h3>
<p class="user-mail"><?php the_field('email_people'); ?></p>
<div class="entry-content">
	<?php the_content(); ?>
</div>
</article>
<?php endwhile; ?>


<?php wp_reset_postdata(); ?>
<article class="youtube-video">
<?php the_field('youtube_people'); ?>
</article>

<div class="pager">
	<div><?php print get_next_posts_link('Older', $the_query->max_num_pages) ?></div>
	<div><?php print get_previous_posts_link('Newer', $the_query->max_num_pages) ?></div>
</div>
</div>
</div>
</div>
</div>
</div> <!-- end #main -->



</div> <!-- end #inner-content -->


<?php get_sidebar('form-bar'); ?>

</div> <!-- end #content -->



<?php get_footer(); ?>