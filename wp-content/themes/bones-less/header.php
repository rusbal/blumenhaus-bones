<?php
require_once 'vendor/autoload.php';
?>
<!doctype html>



<!--[if lt IE 7]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->

<!--[if (IE 7)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8"><![endif]-->

<!--[if (IE 8)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9"><![endif]-->

<!--[if gt IE 8]><!--> <html <?php language_attributes(); ?> class="no-js"><!--<![endif]-->



<head>

	<meta charset="utf-8">



	<title>Blumenhaus Wiedikon <?php wp_title(''); ?></title>



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

			<!-- icons -->
            <link rel="apple-touch-icon" sizes="57x57" href="/apple-icon-57x57.png">
            <link rel="apple-touch-icon" sizes="60x60" href="/apple-icon-60x60.png">
            <link rel="apple-touch-icon" sizes="72x72" href="/apple-icon-72x72.png">
            <link rel="apple-touch-icon" sizes="76x76" href="/apple-icon-76x76.png">
            <link rel="apple-touch-icon" sizes="114x114" href="/apple-icon-114x114.png">
            <link rel="apple-touch-icon" sizes="120x120" href="/apple-icon-120x120.png">
            <link rel="apple-touch-icon" sizes="144x144" href="/apple-icon-144x144.png">
            <link rel="apple-touch-icon" sizes="152x152" href="/apple-icon-152x152.png">
            <link rel="apple-touch-icon" sizes="180x180" href="/apple-icon-180x180.png">
            <link rel="icon" type="image/png" sizes="192x192"  href="/android-icon-192x192.png">
            <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
            <link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
            <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
            <link rel="manifest" href="/manifest.json">
            <meta name="msapplication-TileColor" content="#ffffff">
            <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
            <meta name="theme-color" content="#ffffff">
            <!-- end icons -->

			<!-- drop Google Analytics Here -->

			<!-- end analytics -->
			<!--[if IE]><link rel="stylesheet" type="text/css" href="ie-style.css"/><![endif]-->
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

									<a href="https://www.facebook.com/blumenhaus.wiedikon/" target="_blank" class="fbp ic"></a>
									<a href="https://www.facebook.com/blumenhaus.wiedikon/" target="_blank" href="#">GefÄllt mir </a>
									<a href="http://www.facebook.com/sharer/sharer.php?u=https://www.facebook.com/blumenhaus.wiedikon/">TEILEN</a>

								</div> <!-- end #inner-header -->

							</div>

						</header> <!-- end header -->
					</div>
