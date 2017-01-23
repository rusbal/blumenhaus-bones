<?php

/*

Template Name: Special

*/





if ($_SERVER['REQUEST_METHOD'] == 'POST'){


	


	$message = '<html><head><title>Spezialangebot</title></head><body>';
	$message .= '<p>Über das Bestellformular unter www.blumenhaus-wiedikon.ch ging folgende Bestellung ein:</p>';

	$message.='<br><strong>SCHON BALD IST VALENTINSTAG</strong><br><br>';
	$message.='<hr>';
	if(isset($_POST['topinf'])){
		$message.= '<strong>Spezialangebot:</strong> '.$_POST['topinf'].'<br>'; 
	}
	$message.='<br><strong>RECHNUNGSADRESSE</strong><br><br>';
	$message.='<hr>';
	if(isset($_POST['private'])){
		$message.= '<strong>Privatperson or firma:</strong> '.$_POST['private'].'<br>'; 
	}
	$message.='<hr>';
	if(isset($_POST['Vorname']) && $_POST['Vorname'] != '' ){
		$message.= '<strong>Name:</strong> '.$_POST['Vorname'].'<br>'; 
	}
	if(isset($_POST['Strasse']) && $_POST['Strasse'] != ''){
		$message.= '<strong>Strasse:</strong> '.$_POST['Strasse'].'<br>'; 
	}
	if(isset($_POST['PlzOrt']) && $_POST['PlzOrt'] != ''){
		$message.= '<strong>Plz, Ort:</strong> '.$_POST['PlzOrt'].'<br>'; 
	}
	if(isset($_POST['Telefon']) && $_POST['Telefon'] != ''){
		$message.= '<strong>Telefon:</strong> '.$_POST['Telefon'].'<br>'; 
	}
	if(isset($_POST['E-mail']) && $_POST['E-mail'] != ''){
		$message.= '<strong>E-mail:</strong> '.$_POST['E-mail'].'<br><br>'; 
	}
	$message.='<strong>LIEFERADRESSE</strong><br><br>';
	$message.='<hr>';
	if(isset($_POST['sameAsBilling'])){
		$message.= '<strong>Same As Billing:</strong> Ja <br>'; 
	}
	$message.='<hr>';

	if(isset($_POST['liefeVorname'])){
		$message.= '<strong>Name:</strong> '.$_POST['liefeVorname'].'<br>'; 
	}

	if(isset($_POST['liefeStrasse'])){
		$message.= '<strong>Strasse:</strong> '.$_POST['liefeStrasse'].'<br>'; 
	}

	if(isset($_POST['liefePlzOrt'])){
		$message.= '<strong>Plz, Ort:</strong> '.$_POST['liefePlzOrt'].'<br>'; 
	}

	if(isset($_POST['liefeTelefon'])){
		$message.= '<strong>Telefon:</strong> '.$_POST['liefeTelefon'].'<br>'; 
	}

	if(isset($_POST['liefeE-mail'])){
		$message.= '<strong>E-mail:</strong> '.$_POST['liefeE-mail'].'<br><br><br>'; 
	}



	$message.='</body></html>';
	
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

	mail('info@blumenhaus-wiedikon.ch', 'Spezialangebot', $message, $headers);
}


?>



<?php get_header(); ?>




<div id="content">



	<div id="inner-content" class="wrap clearfix">



		<div id="main" class="green-main-content clearfix bestellen" role="main">



			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>



				<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article">



					<section class="entry-content">


						<div class="content-info">

							<?php the_content(); ?>

							<div role="form">

								<form method="post">

									
									<div class="top-spec clearfix text-center">
										<h2>SCHON BALD IST VALENTINSTAG</h2>
										<p>Überraschen Sie Ihre Liebsten mit einem wunderschönen Rosenstrauss.</br>Für den Valentinstag haben wir folgendes Spezialangebot für Sie:</p>
										<div class="top-radios">
											<div class="radios">
												<input type="radio" name="topinf" value="Exlorer Rose (rote Rose, 50 cm lang) à 4.50 CHF" id="privatperson" checked> <label for="privatperson">25× Exlorer Rose (rote Rose, 50 cm lang) à 4.50 CHF</label>
												<div class="check">
												</div>
											</div>
											<div class="radios">
												<input type="radio" name="topinf" value="Exlorer Rose (rote Rose, 50 cm lang) à 4.20 CHF" id="privatperson"> <label for="privatperson">35× Exlorer Rose (rote Rose, 50 cm lang) à 4.20 CHF</label>
												<div class="check">
												</div>
											</div>
											<div class="radios">
												<input type="radio" name="topinf" value="Exlorer Rose (rote Rose, 50 cm lang) à 3.90 CHF" id="privatperson"> <label for="privatperson">50× Exlorer Rose (rote Rose, 50 cm lang) à 3.90 CHF</label>
												<div class="check">	
												</div>
											</div>
										</div>
										<p>Bestellen Sie so schnell wie möglich und bis spätestens am 7.2.2017. 
										</p><p>
										Verpassen Sie unser Angebot nicht, denn über einen solchen Strauss voller roten Rosen wird sich jede/r freuen!</p>
									</div>

									<h3 class="underline">Ihre Daten</h3>

									<div class="clearfix">
										<div class="half-15">
											<p class="gr-title">Rechnungsadresse</p>
											<div class="input-30 radios">
												<input type="radio" name="private" value="Privatperson" id="privatperson" checked> <label for="privatperson">Privatperson</label>
												<div class="check"></div>
											</div>
											<div class="input-70 radios">
												<input type="radio" name="private" value="Firma" id="firma"> <label for="firma">Firma</label>
												<div class="check"></div>
											</div>
											<div class="input-30">
												<p>Vorname, Name</p>
											</div>
											<div class="input-70"> <span class="wpcf7-form-control-wrap Vorname"><input type="text" name="Vorname" value="" size="40" class="wpcf7-form-control wpcf7-text" aria-invalid="false"></span> </div>
											<div class="input-30">
												<p>Strasse</p>
											</div>
											<div class="input-70"> <span class="wpcf7-form-control-wrap Strasse"><input type="text" name="Strasse" value="" size="40" class="wpcf7-form-control wpcf7-text" aria-invalid="false"></span></div>
											<div class="input-30">
												<p>Plz, Ort</p>
											</div>
											<div class="input-70"> <span class="wpcf7-form-control-wrap PlzOrt"><input type="text" name="PlzOrt" value="" size="40" class="wpcf7-form-control wpcf7-text" aria-invalid="false"></span></div>
											<div class="input-30">
												<p>Telefon</p>
											</div>
											<div class="input-70"> <span class="wpcf7-form-control-wrap Telefon"><input type="text" name="Telefon" value="" size="40" class="wpcf7-form-control wpcf7-text wpcf7-tel wpcf7-validates-as-tel" aria-invalid="false"></span></div>
											<div class="input-30">
												<p>e-mail</p>
											</div>
											<div class="input-70"> <span class="wpcf7-form-control-wrap e-mail"><input type="text" name="E-mail" value="" size="40" class="wpcf7-form-control wpcf7-text wpcf7-email wpcf7-validates-as-email" aria-invalid="false"></span> </div>
										</div>
										<div class="half-15">
											<p class="gr-title">Lieferadresse</p>
											<div class="input-70 radios">
												<input type="checkbox" name="sameAsBilling" value="Gleich Wie Rechnungsadresse" id="same"> <label for="same">Gleich Wie Rechnungsadresse</label>
												<div class="check"></div>
											</div>
											<div class="input-30"></div>

											<div class="input-30">
												<p>Vorname, Name</p>
											</div>
											<div class="input-70"> <span class="wpcf7-form-control-wrap Vorname"><input type="text" name="liefeVorname" value="" size="40" class="wpcf7-form-control wpcf7-text" aria-invalid="false"></span> </div>
											<div class="input-30">
												<p>Strasse</p>
											</div>
											<div class="input-70"> <span class="wpcf7-form-control-wrap Strasse"><input type="text" name="liefeStrasse" value="" size="40" class="wpcf7-form-control wpcf7-text" aria-invalid="false"></span></div>
											<div class="input-30">
												<p>Plz, Ort</p>
											</div>
											<div class="input-70"> <span class="wpcf7-form-control-wrap PlzOrt"><input type="text" name="liefePlzOrt" value="" size="40" class="wpcf7-form-control wpcf7-text" aria-invalid="false"></span></div>
											<div class="input-30">
												<p>Telefon</p>
											</div>
											<div class="input-70"> <span class="wpcf7-form-control-wrap Telefon"><input type="text" name="liefeTelefon" value="" size="40" class="wpcf7-form-control wpcf7-text wpcf7-tel wpcf7-validates-as-tel" aria-invalid="false"></span></div>
											<div class="input-30">
												<p>e-mail</p>
											</div>
											<div class="input-70"> <span class="wpcf7-form-control-wrap e-mail"><input type="text" name="liefeE-mail" value="" size="40" class="wpcf7-form-control wpcf7-text wpcf7-email wpcf7-validates-as-email" aria-invalid="false"></span> </div>
										</div>
									</div>



									<h3 class="underline">ALLE FELDER AUSGEFÜLLT, Dann Freuen Wir Uns Auf Ihre...</h3>

									<div class="submit-button submit-button-light"><input type="submit" value="Bestellung" class="wpcf7-form-control wpcf7-submit"><img class="ajax-loader" src="http://dev.bullean.co/blumenhaus/wp-content/plugins/contact-form-7/images/ajax-loader.gif" alt="Sending ..." style="visibility: hidden;">
									</div>
									<div class="wpcf7-response-output wpcf7-display-none"></div>
								</form>
							</div>

						</div>



					</section> <!-- end article section -->




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


	</div> <!-- end #main -->



</div> <!-- end #inner-content -->


</div> <!-- end #content -->



<?php get_footer(); ?>