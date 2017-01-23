			<footer class="footer" role="contentinfo">
				
				<div id="inner-footer" class="clearfix">
					<div class="wrap">
						<?php if ( is_active_sidebar( 'footer' ) ) : ?>

							<?php dynamic_sidebar( 'footer' ); ?>

						<?php endif; ?>

					</div> 

					<!-- end #inner-footer -->
				</div> 

			</footer> <!-- end footer -->
			<div class="footer-navigation">
				<div class="wrap clearfix">
					<nav role="navigation">

						<?php bones_footer_links(); ?>

					</nav>
				</div>
			</div>
		</div> 


		<!-- end #container -->		

		<!-- all js scripts are loaded in library/bones.php -->

		<?php wp_footer(); ?>

<!-- By Raymond: Falling hearts for Valentines -->
<script src="//cdnjs.cloudflare.com/ajax/libs/gsap/1.18.0/TweenMax.min.js"></script>
<style>
.dot{
  width:35px;
  height:35px;
  position:absolute;
  background: url(http://www.clipartqueen.com/image-files/red-lobed-fall-clipart-leaf.png);
  background-size: 100% 100%;
}
</style>
<!-- By Raymond: Falling hearts for Valentines -->	
		
	</body>

	</html> <!-- end page. what a ride! -->

