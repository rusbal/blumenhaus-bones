<?php

/*

Template Name: Bestellen

*/





if ($_SERVER['REQUEST_METHOD'] == 'POST'){


	


	$message = '<html><head><title>Blumenbestellung</title></head><body>';
	$message .= '<p>Über das Bestellformular unter www.blumenhaus-wiedikon.ch ging folgende Bestellung ein:g</p>';

	$message.='<strong>BESTELLUNG</strong><br><br>';
	$message.='<hr>'; 
	
	if(isset($_POST['preisrahamen']) && $_POST['preisrahamen'] != '0'){
		
		$message.= '<strong>Preisrahamen:</strong> '.$_POST['preisrahamen'].'<br>';
	}
	if(isset($_POST['blumenart']) && $_POST['blumenart'] != '0'){
		$message.= '<strong>Blumenart:</strong> '.$_POST['blumenart'].'<br>';
	}
	if(isset($_POST['blumenfarbe']) && $_POST['blumenfarbe'] != '0'){
		$message.= '<strong>Blumenfarbe:</strong> '.$_POST['blumenfarbe'].'<br>';
	}
	if(isset($_POST['karte'])){
		$message.= '<strong>karte:</strong> '.$_POST['karte'].'<br>';
	}
	if(isset($_POST['aus_karte']) && $_POST['aus_karte'] != '0'){
		$message.= '<strong>Auswählen:</strong> '.$_POST['aus_karte'].'<br>';
	}
	if(isset($_POST['kartentext']) && $_POST['kartentext'] != ''){
		$message.= '<strong>Kartentext:</strong> '.$_POST['kartentext'].'<br>';
	}
	if(isset($_POST['date']) && $_POST['date'] != ''){
		$message.= '<strong>Lieferdatum:</strong> '.$_POST['date'].'<br>';
	}
	if(isset($_POST['time'])){
		$message.= '<strong>Zeit:</strong> '.$_POST['time'].'<br>';
	}
	if(isset($_POST['anmerkungen']) && $_POST['anmerkungen'] != ''){
		$message.= '<strong>Anmerkungen:</strong> '.$_POST['anmerkungen'].'<br>';
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

	mail('info@blumenhaus-wiedikon.ch', 'Bestellen', $message, $headers);
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


									<h3 class="underline">Ihre Bestellung</h3>

									<div class="clearfix">
										<div class="half-15">
											<div class="input-100 clearfix">
												<div class="input-30">
													<p>PREISRAHMEN</p>
												</div>
												<div class="input-40"> 
													<?php 
													if(isset($_POST['preisrahamen_bar'])){ 
														$preis = $_POST['preisrahamen_bar']; 
													}
													else{
														$preis = 0;

													}
													?>
													<select name="preisrahamen">
														<option value="0" <?php if($preis == '0') print 'selected'; ?>>PREISRAHMEN</option>
														<option value="CHF 30.–" <?php if($preis == '30') print 'selected'; ?>>CHF 30.–</option>	
														<option value="CHF 50.–" <?php if($preis == '50') print 'selected'; ?>>CHF 50.–</option>	
														<option value="CHF 75.–" <?php if($preis == '75') print 'selected'; ?>>CHF 75.–</option>
														<option value="CHF 100.–" <?php if($preis == '100') print 'selected'; ?>>CHF 100.–</option>
														<option value="CHF 150.–" <?php if($preis == '150') print 'selected'; ?>>CHF 150.–</option>
														<option value="CHF 200.–" <?php if($preis == '200') print 'selected'; ?>>CHF 200.–</option>
														<option value="CHF 250.–" <?php if($preis == '250') print 'selected'; ?>>CHF 250.–</option>
														<option value="CHF 300.–" <?php if($preis == '300') print 'selected'; ?>>CHF 300.–</option>
														<option value="CHF 500.–" <?php if($preis == '500') print 'selected'; ?>>CHF 500.–</option>
														<option value="CHF 800.–" <?php if($preis == '800') print 'selected'; ?>>CHF 800.–</option>
														<option value="CHF 1’000.–" <?php if($preis == '1000') print 'selected'; ?>>CHF 1’000.–</option>						
													</select>
												</div>
											</div>
											<div class="input-100 clearfix">
												<div class="input-30">
													<p>Blumenart</p>
												</div>
												<div class="input-40"> 
													<?php 
													if(isset($_POST['blumenschmuck_bar'])){ 
														$blukart = $_POST['blumenschmuck_bar']; 
													}
													else{
														$blukart = 0;

													}
													?>
													<select name="blumenart">
														<option value="0" <?php if($blukart == '0') print 'selected'; ?>>BLUMENSCHMUCK</option>
														<option value="BLUMENSTRASS" <?php if($blukart == 'BLUMENSTRASS') print 'selected'; ?>>BLUMENSTRASS</option>	
														<option value="BLUMENKORB" <?php if($blukart == 'BLUMENKORB') print 'selected'; ?>>BLUMENKORB</option>	
														<option value="BLUMENHERZ" <?php if($blukart == 'BLUMENHERZ') print 'selected'; ?>>BLUMENHERZ</option>	
														<option value="ROSEN" <?php if($blukart == 'ROSEN') print 'selected'; ?>>ROSEN</option>	
														<option value="ORCHIDEEN-PFLANZE" <?php if($blukart == 'ORCHIDEEN-PFLANZE') print 'selected'; ?>>ORCHIDEEN-PFLANZE</option>			
													</select>
												</div>
											</div>
											<div class="input-100 clearfix">
												<div class="input-30">
													<p>Blumenfarbe</p>
												</div>
												<div class="input-40"> 
													<?php 
													if(isset($_POST['blumenfarbe_bar'])){ 
														$farbe = $_POST['blumenfarbe_bar']; 
													}
													else{
														$farbe = 0;

													}
													?>
													<select name="blumenfarbe">
														<option value="0" <?php if($farbe == '0') print 'selected'; ?>>Blumenfarbe</option>
														<option value="WEISS" <?php if($farbe == 'WEISS') print 'selected'; ?>>WEISS</option>	
														<option value="GELB" <?php if($farbe == 'GELB') print 'selected'; ?>>GELB</option>	
														<option value="ROT" <?php if($farbe == 'ROT') print 'selected'; ?>>ROT</option>		
														<option value="ROSA" <?php if($farbe == 'ROSA') print 'selected'; ?>>ROSA</option>					
														<option value="FUCHSIA" <?php if($farbe == 'FUCHSIA') print 'selected'; ?>>FUCHSIA</option>	
														<option value="ORANGE" <?php if($farbe == 'ORANGE') print 'selected'; ?>>ORANGE</option>	
														<option value="OVIOLETT" <?php if($farbe == 'OVIOLETT') print 'selected'; ?>>VIOLETT</option>	
														<option value="BLAU" <?php if($farbe == 'BLAU') print 'selected'; ?>>BLAU</option>	
														<option value="GRÜN" <?php if($farbe == 'GRÜN') print 'selected'; ?>>GRÜN</option>							
													</select>
												</div>
											</div>
											<div class="input-100 clearfix">
												<?php 
												$karte = 'OHNE KARTE';
												if(isset($_POST['karte_bar'])){ 
													$karte = $_POST['karte_bar']; 
												}


												?>
												<div class="input-30 radios">
													<input type="radio" name="karte" value="OHNE KARTE" id="karte" <?php if($karte == 'OHNE KARTE') print 'checked'; ?> > <label for="karte">Ohne Karte</label><div class="check"></div>
												</div>
												<div class="input-30 radios">
													<input type="radio" name="karte" value="MIT KARTE" id="karte2" <?php if($karte == 'MIT KARTE') print 'checked'; ?>> <label for="karte2">Mit Karte</label><div class="check"></div>
												</div>
												<div class="input-40">
													<select name="aus_karte" disabled="disabled">
														<option value="0">Auswählen</option>
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
											<div class="input-100 galerie">
												<div class="input-30"></div>
												<div class="input-70 clearfix">
													<div class="input-13">
														<a href="<?php print get_theme_root_uri(); ?>/bones-less/library/images/g1.jpg" rel="lightbox" title="Sonnenschein"><img src="<?php print get_theme_root_uri(); ?>/bones-less/library/images/g1.jpg"></a>
													</div>
													<div class="input-13">
														<a href="<?php print get_theme_root_uri(); ?>/bones-less/library/images/g2.jpg" rel="lightbox" title="Rosenkavalier"><img src="<?php print get_theme_root_uri(); ?>/bones-less/library/images/g2.jpg"></a>
													</div>
													<div class="input-13">
														<a href="<?php print get_theme_root_uri(); ?>/bones-less/library/images/g3.jpg" rel="lightbox" title="Von Herzen"><img src="<?php print get_theme_root_uri(); ?>/bones-less/library/images/g3.jpg"></a>
													</div>
													<div class="input-13">
														<a href="<?php print get_theme_root_uri(); ?>/bones-less/library/images/g4.jpg" rel="lightbox" title="Happy Day"><img src="<?php print get_theme_root_uri(); ?>/bones-less/library/images/g4.jpg"></a>
													</div>
													<div class="input-13">
														<a href="<?php print get_theme_root_uri(); ?>/bones-less/library/images/g5.jpg" rel="lightbox" title="Viel Glück"><img src="<?php print get_theme_root_uri(); ?>/bones-less/library/images/g5.jpg"></a>
													</div>
													<div class="input-13">
														<a href="<?php print get_theme_root_uri(); ?>/bones-less/library/images/g5.jpg" rel="lightbox" title="Edle Rose"><img src="<?php print get_theme_root_uri(); ?>/bones-less/library/images/g6.jpg"></a>
													</div>
													<div class="input-13">
														<a href="<?php print get_theme_root_uri(); ?>/bones-less/library/images/g7.jpg" rel="lightbox" title="Baby"><img src="<?php print get_theme_root_uri(); ?>/bones-less/library/images/g7.jpg"></a>
													</div>
												</div>
											</div>
											<div class="input-100 clearfix">
												<div class="input-30">
													<p>Kartentext</p>
												</div>
												<div class="input-70"><span class="wpcf7-form-control-wrap k-te IhreNachriht"><textarea name="kartentext" cols="40" rows="9" class="wpcf7-form-control wpcf7-textarea" aria-invalid="false"></textarea></span></div>

											</div>
										</div>
										<div class="half-15">
											<div class="input-100 clearfix">
												<div class="input-30">
													<p>Anlass</p>
												</div>
												<div class="input-70"> 
													<?php 
													if(isset($_POST['anlass_bar'])){ 
														$anlass = $_POST['anlass_bar']; 
													}
													else{
														$anlass = 0;

													}
													?>
													<select name="anlass">
														<option value="0" <?php if($anlass == '0') print 'selected'; ?>>ANLASS</option>
														<option value="GEBURTSTAG" <?php if($anlass == 'GEBURTSTAG') print 'selected'; ?>>GEBURTSTAG</option>	
														<option value="ÜBERRASCHUNG" <?php if($anlass == 'ÜBERRASCHUNG') print 'selected'; ?>>ÜBERRASCHUNG</option>
														<option value="LIEBESERKLÄRUNG" <?php if($anlass == 'LIEBESERKLÄRUNG') print 'selected'; ?>>LIEBESERKLÄRUNG</option>
														<option value="HOCHZEIT" <?php if($anlass == 'HOCHZEIT') print 'selected'; ?>>HOCHZEIT</option>
														<option value="GEBURT" <?php if($anlass == 'GEBURT') print 'selected'; ?>>GEBURT</option>
														<option value="DEKORATION/EVENT/FIRMENGESCHENK" <?php if($anlass == 'DEKORATION/EVENT/FIRMENGESCHENK') print 'selected'; ?>>DEKORATION/EVENT/FIRMENGESCHENK</option>	
														<option value="TRAUERFLORISTIK/GRABSCHMUCK" <?php if($anlass == 'TRAUERFLORISTIK/GRABSCHMUCK') print 'selected'; ?>>TRAUERFLORISTIK/GRABSCHMUCK</option>							
													</select>
												</div>
											</div>
											<div class="input-100 clearfix">
												<div class="input-30">
													<p>Lieferdatum</p>
												</div>
												<div class="input-70"> 
													<?php 
													if(isset($_POST['lieferdatum_bar'])){ 
														$date = $_POST['lieferdatum_bar']; 
													}
													else{
														$date = '';

													}
													?>
													<input type="text" name="date" id="date" placeholder="Tag/Monat/Jahr" value="<?php print $date; ?>">
												</div>
											</div>
											<div class="input-100 clearfix">
												<div class="input-30">
													<p>Zeit</p>
												</div>
												<div class="input-70"> 
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
												<div class="input-70"> 
													<select name="anlass">
														<option value="HAUSLIEFERDIENST (ZÜRICH UND UMGEBUNG)">HAUSLIEFERDIENST (ZÜRICH UND UMGEBUNG)</option>
														<option value="POSTVERSAND">POSTVERSAND</option>	
														<option value="ABHOLUNG IM BLUMENHAUS WIEDIKON">ABHOLUNG IM BLUMENHAUS WIEDIKON</option>							
													</select>
												</div>
											</div>
											<div class="input-100 clearfix">
												<div class="input-30">
													<p>Anmerkungen</p>
												</div>
												<div class="input-70"><span class="wpcf7-form-control-wrap t-ir IhreNachriht"><textarea name="anmerkungen" cols="40" rows="9" class="wpcf7-form-control wpcf7-textarea" aria-invalid="false"></textarea></span></div>
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


</div> <!-- end #content -->



<?php get_footer(); ?>