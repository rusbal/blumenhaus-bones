<!doctype html>



<!--[if lt IE 7]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->

<!--[if (IE 7)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8"><![endif]-->

<!--[if (IE 8)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9"><![endif]-->

<!--[if gt IE 8]><!--> <html <?php language_attributes(); ?> class="no-js"><!--<![endif]-->



<head>

	<meta charset="utf-8">



	<title><?php wp_title(''); ?></title>



	<!-- Google Chrome Frame for IE -->

	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">



	<!-- mobile meta (hooray!) -->

	<meta name="HandheldFriendly" content="True">

	<meta name="MobileOptimized" content="320">

	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>



	<!-- icons & favicons (for more: http://www.jonathantneal.com/blog/understand-the-favicon/) -->

	<link rel="apple-touch-icon" href="<?php echo get_template_directory_uri(); ?>/library/images/apple-icon-touch.png">

	<link rel="icon" href="<?php echo get_template_directory_uri(); ?>/favicon.png">

		<!--[if IE]>

			<link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/favicon.ico">

			<![endif]-->

			<!-- or, set /favicon.ico for IE10 win -->

			<meta name="msapplication-TileColor" content="#f01d4f">

			<meta name="msapplication-TileImage" content="<?php echo get_template_directory_uri(); ?>/library/images/win8-tile-icon.png">



			<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">



			<!-- wordpress head functions -->

			<?php wp_head(); ?>

			<!-- end of wordpress head -->



			<!-- drop Google Analytics Here -->

			<!-- end analytics -->

			<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/library/js/skrollr.min.js"></script>
			<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/library/js/jquery-ui.js"></script>
			<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/library/js/timepicker.js"></script>
			<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/library/js/custom.js"></script>
			<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/library/js/bestellen.js"></script>
			
		</head>



		<body <?php body_class(); ?>>



			<div id="container">


				<div class="absolute-header">
					<header class="header wrap" role="banner">



						<div id="inner-header" class="clearfix">



							<!-- to use a image just replace the bloginfo('name') with your img src and remove the surrounding <p> -->
							<div class="threecol stat">
								<div id="logo" class="h1">
									<div id="object" data--100-top="background-position:0px 0px;" data-top-bottom="background-position:30px 100px;" class="skrollable skrollable-between" ></div>
									<a href="<?php echo home_url(); ?>" rel="nofollow"><?php bloginfo('name'); ?></a>
								</div>
							</div>


							<!-- if you'd like to use the site description you can un-comment it below -->

							<?php // bloginfo('description'); ?>



							<div class="ninecol main-navigation">

								<nav role="navigation">

									<?php bones_main_nav(); ?>

								</nav>

								<div class="social-icons">

									<a href="#" class="fbp ic"></a>
									<a href="#">GefÄllt mir </a>
									<a href="#">TEILEN</a>

								</div> <!-- end #inner-header -->

							</div>

						</header> <!-- end header -->
					</div>