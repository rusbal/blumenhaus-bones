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

	</body>

	</html> <!-- end page. what a ride! -->

