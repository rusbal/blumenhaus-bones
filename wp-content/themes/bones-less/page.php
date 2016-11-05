<?php get_header(); ?>



<div id="content">




	<div id="inner-content" class="wrap clearfix">



		<div id="main" class="first clearfix" role="main">

			<div class="simple-page">

				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>



					<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">



						<header class="article-header">




						</header> <!-- end article header -->



						<section class="entry-content clearfix" itemprop="articleBody">

							<?php the_content(); ?>

						</section> <!-- end article section -->



						<footer class="article-footer">

							<?php the_tags('<span class="tags">' . __('Tags:', 'bonestheme') . '</span> ', ', ', ''); ?>



						</footer> <!-- end article footer -->



						<?php comments_template(); ?>



					</article> <!-- end article -->



				<?php endwhile; else : ?>



				<article id="post-not-found" class="hentry clearfix">

					<header class="article-header">

						<h1><?php _e("Oops, Post Not Found!", "bonestheme"); ?></h1>

					</header>

					<section class="entry-content">

						<p><?php _e("Uh Oh. Something is missing. Try double checking things.", "bonestheme"); ?></p>

					</section>

					<footer class="article-footer">

						<p><?php _e("This is the error message in the page.php template.", "bonestheme"); ?></p>

					</footer>

				</article>



			<?php endif; ?>


		</div>
	</div> <!-- end #main -->







</div> <!-- end #inner-content -->

<?php get_sidebar('form-bar'); ?>

</div> <!-- end #content -->



<?php get_footer(); ?>