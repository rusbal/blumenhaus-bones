<?php

/*

Template Name: Bestellen

*/

require_once 'includes/mail_helper.php';



if ($_SERVER['REQUEST_METHOD'] == 'POST'){

	if ($_POST['form-name'] == 'blumenbestellung') {

		$simpleMail = new SimpleEmailBuilder;
		$simpleMail->header('Blumenbestellung');
		$simpleMail->line('Blumenschmuck', 'blumenschmuck_bar', 'Bestellinformationen');
		$simpleMail->line('Preisrahmen', 'preisrahamen_bar');
		$simpleMail->line('Anlass', 'anlass_bar');
		$simpleMail->line('Blumenfarbe', 'blumenfarbe_bar');
		$simpleMail->line('Karte', 'karte_bar');
		$simpleMail->line('Lieferdatum', 'lieferdatum_bar');
		$message = $simpleMail->render();

		$recipients = [];
//			'ingo.grunig@gmail.com',
//		];

		send_mail($recipients, 'Blumenbestellung', $message);
	}

	if ($_POST['form-name'] == 'bestellen') {
		$message = '<html><head><title>Bestellen</title></head><body>';

		$message .= '<strong>Rechnungsadresse</strong><br><br>';
		if ( isset( $_POST['private'] ) ) {
			$message .= '<strong>Privatperson or firma:</strong> ' . $_POST['private'] . '<br>';
		}

		if ( isset( $_POST['Vorname'] ) ) {
			$message .= '<strong>Name:</strong> ' . $_POST['Vorname'] . '<br>';
		}

		if ( isset( $_POST['Strasse'] ) ) {
			$message .= '<strong>Strasse:</strong> ' . $_POST['Strasse'] . '<br>';
		}

		if ( isset( $_POST['PlzOrt'] ) ) {
			$message .= '<strong>Plz, Ort:</strong> ' . $_POST['PlzOrt'] . '<br>';
		}

		if ( isset( $_POST['Telefon'] ) ) {
			$message .= '<strong>Telefon:</strong> ' . $_POST['Telefon'] . '<br>';
		}

		if ( isset( $_POST['E-mail'] ) ) {
			$message .= '<strong>E-mail:</strong> ' . $_POST['E-mail'] . '<br><br><br>';
		}


		$message .= '<strong>Lieferadresse</strong><br><br>';
		if ( isset( $_POST['sameAsBilling'] ) ) {
			$message .= '<strong>Same As Billing:</strong> Ja<br><br><br>';
		} else {
			$message .= '<strong>Same As Billing:</strong> Nein<br>';

			if ( isset( $_POST['liefeVorname'] ) ) {
				$message .= '<strong>Name:</strong> ' . $_POST['liefeVorname'] . '<br>';
			}

			if ( isset( $_POST['liefeStrasse'] ) ) {
				$message .= '<strong>Strasse:</strong> ' . $_POST['liefeStrasse'] . '<br>';
			}

			if ( isset( $_POST['liefePlzOrt'] ) ) {
				$message .= '<strong>Plz, Ort:</strong> ' . $_POST['liefePlzOrt'] . '<br>';
			}

			if ( isset( $_POST['liefeTelefon'] ) ) {
				$message .= '<strong>Telefon:</strong> ' . $_POST['liefeTelefon'] . '<br>';
			}

			if ( isset( $_POST['liefeE-mail'] ) ) {
				$message .= '<strong>E-mail:</strong> ' . $_POST['liefeE-mail'] . '<br><br><br>';
			}

		}

		$message .= '<strong>Ihre Bestellung</strong><br><br>';
		if ( isset( $_POST['preisrahamen'] ) && $_POST['preisrahamen'] != 0 ) {
			$message .= '<strong>Preisrahamen:</strong> ' . $_POST['preisrahamen'] . '<br>';
		}

		if ( isset( $_POST['blumenart'] ) && $_POST['blumenart'] != 0 ) {
			$message .= '<strong>Blumenart:</strong> ' . $_POST['blumenart'] . '<br>';
		}

		if ( isset( $_POST['blumenfarbe'] ) && $_POST['blumenfarbe'] != 0 ) {
			$message .= '<strong>Blumenfarbe:</strong> ' . $_POST['blumenfarbe'] . '<br>';
		}

		if ( isset( $_POST['karte'] ) ) {
			$message .= '<strong>karte:</strong> ' . $_POST['karte'] . '<br>';
		}

		if ( isset( $_POST['aus_karte'] ) && $_POST['aus_karte'] != 0 ) {
			$message .= '<strong>Auswählen:</strong> ' . $_POST['aus_karte'] . '<br>';
		}

		if ( isset( $_POST['kartentext'] ) ) {
			$message .= '<strong>Kartentext:</strong> ' . $_POST['kartentext'] . '<br>';
		}

		if ( isset( $_POST['anlass'] ) && $_POST['anlass'] != 0 ) {
			$message .= '<strong>Anlass:</strong> ' . $_POST['anlass'] . '<br>';
		}

		if ( isset( $_POST['date'] ) ) {
			$message .= '<strong>Lieferdatum:</strong> ' . $_POST['date'] . '<br>';
		}

		if ( isset( $_POST['time'] ) ) {
			$message .= '<strong>Zeit:</strong> ' . $_POST['time'] . '<br>';
		}

		if ( isset( $_POST['anmerkungen'] ) ) {
			$message .= '<strong>Anmerkungen:</strong> ' . $_POST['anmerkungen'] . '<br>';
		}


		$message .= '</body></html>';

		$recipients = [
			'ingo.grunig@gmail.com',
		];
		send_mail( $recipients, 'Bestellen', $message );
	}
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

							<h3 class="underline">Ihre Daten</h3>

							<div role="form">

								<form method="post">
									<?php echo $Html->Form->hidden('form-name', ['value' => 'bestellen']); ?>

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
											<div class="input-70"> <span class="wpcf7-form-control-wrap Telefon"><input type="tel" name="Telefon" value="" size="40" class="wpcf7-form-control wpcf7-text wpcf7-tel wpcf7-validates-as-tel" aria-invalid="false"></span></div>
											<div class="input-30">
												<p>e-mail</p>
											</div>
											<div class="input-70"> <span class="wpcf7-form-control-wrap e-mail"><input type="email" name="E-mail" value="" size="40" class="wpcf7-form-control wpcf7-text wpcf7-email wpcf7-validates-as-email" aria-invalid="false"></span> </div>
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
											<div class="input-70"> <span class="wpcf7-form-control-wrap Telefon"><input type="tel" name="liefeTelefon" value="" size="40" class="wpcf7-form-control wpcf7-text wpcf7-tel wpcf7-validates-as-tel" aria-invalid="false"></span></div>
											<div class="input-30">
												<p>e-mail</p>
											</div>
											<div class="input-70"> <span class="wpcf7-form-control-wrap e-mail"><input type="email" name="liefeE-mail" value="" size="40" class="wpcf7-form-control wpcf7-text wpcf7-email wpcf7-validates-as-email" aria-invalid="false"></span> </div>
										</div>
									</div>


									<h3 class="underline">Ihre Bestellung</h3>

									<div class="clearfix">
										<div class="half-15">
											<div class="input-100 clearfix">
												<div class="input-30">
													<p>PREISRAHMEN</p>
												</div>
												<div class="input-40"> 
													<select name="preisrahamen">
<option value="">...</option>
<option value="Chf. 30.-">Chf. 30.-</option>
<option value="Chf. 50.-">Chf. 50.-</option>
<option value="Chf. 75.-">Chf. 75.-</option>
<option value="Chf. 100.-">Chf. 100.-</option>
<option value="Chf. 150.-">Chf. 150.-</option>
<option value="Chf. 200.-">Chf. 200.-</option>
<option value="Chf. 250.-">Chf. 250.-</option>
<option value="Chf. 300.-">Chf. 300.-</option>
<option value="Chf. 500.-">Chf. 500.-</option>
<option value="Chf. 800.-">Chf. 800.-</option>
<option value="Chf. 1000.-">Chf. 1000.-</option>
<option value="Tel. besprechen">Tel. besprechen</option>						
													</select>
												</div>
											</div>
											<div class="input-100 clearfix">
												<div class="input-30">
													<p>Blumenart</p>
												</div>
												<div class="input-40"> 
													<select name="blumenart">
<option value="">...</option>
<option value="Blumenstrauss">Blumenstrauss</option>
<option value="Blumenkorb">Blumenkorb</option>
<option value="Blumenherz">Blumenherz</option>
<option value="Rosen">Rosen</option>
<option value="Orchideen-Pflanze">Orchideen-Pflanze</option>
<option value="Tel. besprechen">Tel. besprechen</option>
													</select>
												</div>
											</div>
											<div class="input-100 clearfix">
												<div class="input-30">
													<p>Blumenfarbe</p>
												</div>
												<div class="input-40"> 
													<select name="blumenfarbe">
<option value="">...</option>
<option value="Weiss">Weiss</option>
<option value="Gelb">Gelb</option>
<option value="Rot">Rot</option>
<option value="Rosa">Rosa</option>
<option value="Fuchsia">Fuchsia</option>
<option value="Orange">Orange</option>
<option value="Violett">Violett</option>
<option value="Blau">Blau</option>
<option value="Grün">Grün</option>							
													</select>
												</div>
											</div>
											<div class="input-100 clearfix">
												<div class="input-30 radios">
													<input type="radio" name="karte" value="Onhe Karte" id="karte" checked> <label for="karte">Ohne Karte</label><div class="check"></div>
												</div>
												<div class="input-30 radios">
													<input type="radio" name="karte" value="Mit Karte" id="karte2"> <label for="karte2">Mit Karte</label><div class="check"></div>
												</div>
												<div class="input-40">
													<select name="aus_karte">
<option value="">...</option>
<option value="Keine">Keine</option>
<option value="Sonnenschein">Sonnenschein</option>
<option value="Rosenkavalier">Rosenkavalier</option>
<option value="Von Herzen">Von Herzen</option>
<option value="Happy Day">Happy Day</option>
<option value="Viel Glück">Viel Glück</option>
<option value="Edle Rose">Edle Rose</option>
<option value="Baby">Baby</option>
													</select>
												</div>
											</div>
											<div class="input-100 clearfix">
												<div class="input-30">
													<p>Kartentext</p>
												</div>
												<div class="input-70"><span class="wpcf7-form-control-wrap IhreNachriht"><textarea name="kartentext" cols="40" rows="9" class="wpcf7-form-control wpcf7-textarea" aria-invalid="false"></textarea></span></div>

											</div>
										</div>
										<div class="half-15">
											<div class="input-100 clearfix">
												<div class="input-30">
													<p>Anlass</p>
												</div>
												<div class="input-40"> 
													<select name="anlass">
<option value="">...</option>
<option value="Geburstag">Geburstag</option>
<option value="Überraschung">Überraschung</option>
<option value="Liebeserklärung">Liebeserklärung</option>
<option value="Hochzeit">Hochzeit</option>
<option value="Geburt">Geburt</option>
<option value="Dekoration - Event - Firmengeschenk">Dekoration - Event - Firmengeschenk</option>
<option value="Trauerfloristik - Grabschmuck">Trauerfloristik - Grabschmuck</option>
<option value="Tel. besprechen">Tel. besprechen</option>
													</select>
												</div>
											</div>
											<div class="input-100 clearfix">
												<div class="input-30">
													<p>Lieferdatum</p>
												</div>
												<div class="input-40"> 
													<input type="text" name="date" id="date" placeholder="Tag/Monat/Jahr">
												</div>
											</div>
											<div class="input-100 clearfix">
												<div class="input-30">
													<p>Zeit</p>
												</div>
												<div class="input-40"> 
													<!-- 													<input type="text" name="time" id="timepicker" placeholder="H:M"> -->
													<select name="time">
														<option value="VORMITTAG">VORMITTAG</option>
														<option value="NACHMITTAG">NACHMITTAG</option>	

													</select>
												</div>
											</div>
											<div class="input-100 clearfix">
												<div class="input-30">
													<p>ZUSTELLUNG</p>
												</div>
												<div class="input-40"> 
													<select name="anlass">
														<option value="0">ZUSTELLUNG</option>
														<option value="1">Test</option>	
														<option value="2">Test 2</option>							
													</select>
												</div>
											</div>
											<div class="input-100 clearfix">
												<div class="input-30">
													<p>Anmerkungen</p>
												</div>
												<div class="input-70"><span class="wpcf7-form-control-wrap IhreNachriht"><textarea name="anmerkungen" cols="40" rows="9" class="wpcf7-form-control wpcf7-textarea" aria-invalid="false"></textarea></span></div>
											</div>
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


<?php get_sidebar('form-bar'); ?>

</div> <!-- end #content -->



<?php get_footer(); ?>