<?php

/*

Template Name: Bestellen

*/

use Rsu\ContactForm\DbWriter\DbWriterLogger;
use Rsu\EmailBuilder\SimpleEmailBuilder;
use Rsu\Mail\MailHelper;
use Rsu\Slugify\Slugify;
use Rsu\Validator\Validator;

$val = new Validator();

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
	if ($_POST['form-name'] == 'bestellen') {

		/**
		 * Validation
		 */
		$validationRule = [
			'Vorname' => 'required',
			'Strasse' => 'required',
			'Plz' => 'required',
            'Ort' => 'required',
			'Telefon' => 'required',
			'E-mail' => 'required',

			'liefeVorname' => 'required|ifnotset:sameAsBilling',
			'liefeStrasse' => 'required|ifnotset:sameAsBilling',
			'liefePlz' => 'required|ifnotset:sameAsBilling',
            'liefeOrt' => 'required|ifnotset:sameAsBilling',
			'liefeTelefon' => 'required|ifnotset:sameAsBilling',
			'liefeE-mail' => 'required|ifnotset:sameAsBilling',

			'preisrahamen' => 'required',
			'blumenart' => 'required',
			'blumenfarbe' => 'required',
			'karte' => 'required',
			'aus_karte' => 'required|ifeq:karte=Mit Karte',
			'kartentext' => 'required|ifeq:karte=Mit Karte',
			'anlass' => 'required',
			'lieferdatum' => 'required',
			'time' => 'required',
		];

		$val = new Validator($validationRule, $_POST);

		if ($val->success()) {
		    $logger = new DbWriterLogger('Order form', $wpdb, (new Slugify()));

			$simpleMail = new SimpleEmailBuilder($_POST, $logger);
			$simpleMail->header('Bestellen');

			$simpleMail->sectionTitle('Rechnungsadresse');
			$simpleMail->line('Privatperson oder Firma', 'private');
			$simpleMail->line('Name', 'Vorname');
			$simpleMail->line('Strasse', 'Strasse');
			$simpleMail->line('Plz', 'Plz');
            $simpleMail->line('Ort', 'Ort');
			$simpleMail->line('Telefon', 'Telefon');
			$simpleMail->line('E-mail', 'E-mail');
			$simpleMail->addLineBreak(2);

			$simpleMail->sectionTitle('Lieferadresse');
			$simpleMail->line('wie Rechnungsadresse', [
				'sameAsBilling', 'Ja', 'Nein'
			]);

			if ( ! isset( $_POST['sameAsBilling'] ) ) {
				$simpleMail->line('Name', 'liefeVorname');
				$simpleMail->line('Strasse', 'liefeStrasse');
				$simpleMail->line('Plz', 'liefePlz');
                $simpleMail->line('Ort', 'liefeOrt');
				$simpleMail->line('Telefon', 'liefeTelefon');
				$simpleMail->line('E-mail', 'liefeE-mail');
			}
			$simpleMail->addLineBreak(2);

			$simpleMail->sectionTitle('Ihre Bestellung');
			$simpleMail->line('Preisrahmen', 'preisrahamen', ['!=' => 0]);
			$simpleMail->line('Blumenart', 'blumenart', ['!=' => 0]);
            $simpleMail->line('Rote Rosen Angebot', 'rote_rosen', ['!=' => '']);
			$simpleMail->line('Blumenfarbe', 'blumenfarbe', ['!=' => 0]);
			$simpleMail->line('Karte', 'karte');
			$simpleMail->line('Auswählen', 'aus_karte', ['!=' => 0]);
			$simpleMail->line('Kartentext', 'kartentext');
			$simpleMail->line('Anlass', 'anlass', ['!=' => 0]);
			$simpleMail->line('Lieferdatum', 'lieferdatum');
			$simpleMail->line('Zeit', 'time');
			$simpleMail->line('Zustellung', 'zustellung');
			$simpleMail->line('Anmerkungen', 'anmerkungen');

            $simpleMail->addLineBreak(2);
            $simpleMail->sectionTitle('Cart: Berechnung');
            $simpleMail->line('Cart: Blumenwert', 'flower-cost');
            $simpleMail->line('Cart: Karte', 'karte-cost');
            $simpleMail->line('Cart: Lieferkosten', 'delivery-cost');
            $simpleMail->line('Cart: Total', 'cart-total');

			MailHelper::customerOrder('Bestellen', $simpleMail->render(), $_POST['E-mail'], $_POST['Vorname']);

			header("Location: " . $_SERVER['HTTP_HOST'] . "/danke-fur-ihre-bestellung");
			exit;
		}
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
												<input type="radio" name="private" value="Privatperson" id="privatperson" <?= $_POST['private'] != 'Firma' ? 'checked' : null ?>> <label for="privatperson">Privatperson</label>
												<div class="check"></div>
											</div>
											<div class="input-70 radios">
												<input type="radio" name="private" value="Firma" id="firma" <?= $_POST['private'] == 'Firma' ? 'checked' : null ?>> <label for="firma">Firma</label>
												<?= $val->error('Firma') ?>
												<div class="check"></div>
											</div>
											<div class="input-30">
												<p>Vorname, Name</p>
											</div>
											<div class="input-70"> <span class="wpcf7-form-control-wrap Vorname">
												<?= $Html->Form->input('Vorname', false, ['size' => '40', 'class' => 'wpcf7-form-control wpcf7-text', 'aria-invalid' => 'false']) ?>
												<?= $val->error('Vorname') ?>
											</span> </div>
											<div class="input-30">
												<p>Strasse</p>
											</div>
											<div class="input-70"> <span class="wpcf7-form-control-wrap Strasse">
												<?= $Html->Form->input('Strasse', false, ['size' => '40', 'class' => 'wpcf7-form-control wpcf7-text', 'aria-invalid' => 'false']) ?>
												<?= $val->error('Strasse') ?>
											</span></div>

                                            <div class="input-30"> <p>Plz</p> </div>
                                            <div class="input-70"> <span class="wpcf7-form-control-wrap Plz">
												<?= $Html->Form->input('Plz', false, ['size' => '40', 'class' => 'wpcf7-form-control wpcf7-text', 'aria-invalid' => 'false']) ?>
                                                <?= $val->error('Plz') ?>
											</span></div>

											<div class="input-30"> <p>Ort</p> </div>
											<div class="input-70"> <span class="wpcf7-form-control-wrap Ort">
												<?= $Html->Form->input('Ort', false, ['size' => '40', 'class' => 'wpcf7-form-control wpcf7-text', 'aria-invalid' => 'false']) ?>
												<?= $val->error('Ort') ?>
											</span></div>

											<div class="input-30">
												<p>Telefon</p>
											</div>
											<div class="input-70"> <span class="wpcf7-form-control-wrap Telefon">
												<?= $Html->Form->input('Telefon', false, ['type' => 'text', 'size' => '40', 'class' => 'wpcf7-form-control wpcf7-text wpcf7-tel wpcf7-validates-as-tel', 'aria-invalid' => 'false']) ?>
												<?= $val->error('Telefon') ?>
											</span></div>
											<div class="input-30">
												<p>e-mail</p>
											</div>
											<div class="input-70"> <span class="wpcf7-form-control-wrap e-mail">
												<?= $Html->Form->input('E-mail', false, ['type' => 'email', 'size' => '40', 'class' => 'wpcf7-form-control wpcf7-text wpcf7-email wpcf7-validates-as-email', 'aria-invalid' => 'false']) ?>
												<?= $val->error('E-mail') ?>
											</span> </div>
										</div>
										<div class="half-15">
											<p class="gr-title">Lieferadresse</p>
											<div class="input-70 radios">
												<input type="checkbox" name="sameAsBilling" value="Gleich Wie Rechnungsadresse" id="same" <?= $_POST['sameAsBilling'] ? 'checked' : null ?>> <label for="same">Gleich Wie Rechnungsadresse</label>
												<div class="check"></div>
											</div>
											<div class="input-30"></div>

											<div class="input-30">
												<p>Vorname, Name</p>
											</div>
											<div class="input-70"> <span class="wpcf7-form-control-wrap Vorname">
												<?= $Html->Form->input('liefeVorname', false, ['size' => '40', 'class' => 'whenNotSameAsBilling wpcf7-form-control wpcf7-text', 'aria-invalid' => 'false']) ?>
												<?= $val->error('liefeVorname') ?>
											</span> </div>
											<div class="input-30">
												<p>Strasse</p>
											</div>
											<div class="input-70"> <span class="wpcf7-form-control-wrap Strasse">
												<?= $Html->Form->input('liefeStrasse', false, ['size' => '40', 'class' => 'whenNotSameAsBilling wpcf7-form-control wpcf7-text', 'aria-invalid' => 'false']) ?>
												<?= $val->error('liefeStrasse') ?>
											</span></div>

                                            <div class="input-30"> <p>Plz</p> </div>
                                            <div class="input-70"> <span class="wpcf7-form-control-wrap liefePlz">
												<?= $Html->Form->input('liefePlz', false, ['size' => '40', 'class' => 'whenNotSameAsBilling wpcf7-form-control wpcf7-text', 'aria-invalid' => 'false']) ?>
                                                <?= $val->error('liefePlz') ?>
											</span></div>

											<div class="input-30"> <p>Ort</p> </div>
											<div class="input-70"> <span class="wpcf7-form-control-wrap liefeOrt">
												<?= $Html->Form->input('liefeOrt', false, ['size' => '40', 'class' => 'whenNotSameAsBilling wpcf7-form-control wpcf7-text', 'aria-invalid' => 'false']) ?>
												<?= $val->error('liefeOrt') ?>
											</span></div>

											<div class="input-30">
												<p>Telefon</p>
											</div>
											<div class="input-70"> <span class="wpcf7-form-control-wrap Telefon">
												<?= $Html->Form->input('liefeTelefon', false, ['size' => '40', 'class' => 'whenNotSameAsBilling wpcf7-form-control wpcf7-text wpcf7-tel wpcf7-validates-as-tel', 'aria-invalid' => 'false']) ?>
												<?= $val->error('liefeTelefon') ?>
											</span></div>
											<div class="input-30">
												<p>e-mail</p>
											</div>
											<div class="input-70"> <span class="wpcf7-form-control-wrap e-mail">
												<?= $Html->Form->input('liefeE-mail', false, ['size' => '40', 'class' => 'whenNotSameAsBilling wpcf7-form-control wpcf7-text wpcf7-email wpcf7-validates-as-email', 'aria-invalid' => 'false']) ?>
												<?= $val->error('liefeE-mail') ?>
											</span> </div>
										</div>
									</div>


									<h3 class="underline">Ihre Bestellung</h3>

									<div class="clearfix">
										<div class="half-15">
											<div class="input-100 clearfix">
												<div class="input-30">
													<p>PREISRAHMEN</p>
												</div>
												<div class="input-70">
												<?php
												echo $Html->Form->select('preisrahamen', false, [
													"" => 'Blumenwert',
													"Chf. 30.-" => 'Chf. 30.- Blumenwert',
													"Chf. 50.-" => 'Chf. 50.- Blumenwert',
													"Chf. 75.-" => 'Chf. 75.- Blumenwert',
													"Chf. 100.-" => 'Chf. 100.- Blumenwert',
													"Chf. 150.-" => 'Chf. 150.- Blumenwert',
													"Chf. 200.-" => 'Chf. 200.- Blumenwert',
													"Chf. 250.-" => 'Chf. 250.- Blumenwert',
													"Chf. 300.-" => 'Chf. 300.- Blumenwert',
													"Chf. 500.-" => 'Chf. 500.- Blumenwert',
													"Chf. 800.-" => 'Chf. 800.- Blumenwert',
													"Chf. 1000.-" => 'Chf. 1000.- Blumenwert',
												], [
													'selected' => $_POST['preisrahamen'],
												]);
												?>
												<?= $val->error('preisrahamen') ?>
												</div>
											</div>
											<div class="input-100 clearfix">
												<div class="input-30">
													<p>Blumenart</p>
												</div>
												<div class="input-70">
												<?php
												echo $Html->Form->select('blumenart', false, [
													"" => 'BLUMENSCHMUCK',
													"Blumenstrauss" => 'Blumenstrauss',
													"Blumenkorb" => 'Blumenkorb',
													"Blumenherz" => 'Blumenherz',
													"Rosen" => 'Rosen',
													"Orchideen-Pflanze" => 'Orchideen-Pflanze',
												], [
													'selected' => $_POST['blumenart'],
												]);
												?>
												<?= $val->error('blumenart') ?>
												</div>
											</div>
                                            <div class="input-100 clearfix">
                                                <div class="input-30">
                                                    <p>Rote Rosen</p>
                                                </div>
                                                <div class="input-70">
                                                    <?php
                                                    echo $Html->Form->select('rote_rosen', false, [
                                                        "" => 'Rote Rosen Angebot',
                                                        "1x langstielige rote Rose à 6.50" => '1x langstielige rote Rose à 6.50',
                                                        "10x langstielige rote Rose à 6.20" => '10x langstielige rote Rose à 6.20',
                                                        "30x langstielige rote Rose à 5.80" => '30x langstielige rote Rose à 5.80',
                                                    ], [
                                                        'selected' => $_POST['rote_rosen'],
                                                    ]);
                                                    ?>
                                                    <?= $val->error('rote_rosen') ?>
                                                </div>
                                            </div>
											<div class="input-100 clearfix">
												<div class="input-30">
													<p>Blumenfarbe</p>
												</div>
												<div class="input-70">
												<?php
												echo $Html->Form->select('blumenfarbe', false, [
													"" => 'Blumenfarbe',
													"Weiss" => 'Weiss',
													"Gelb" => 'Gelb',
													"Rot" => 'Rot',
													"Rosa" => 'Rosa',
													"Fuchsia" => 'Fuchsia',
													"Orange" => 'Orange',
													"Violett" => 'Violett',
													"Blau" => 'Blau',
													"Grün" => 'Grün',
												], [
													'selected' => $_POST['blumenfarbe'],
												]);
												?>
												<?= $val->error('blumenfarbe') ?>
												</div>
											</div>
											<div class="input-100 clearfix">
												<div class="input-30 radios">
													<input type="radio" name="karte" value="Ohne Karte" id="karte"
														<?= ($_POST['karte'] == 'Ohne Karte') ? 'checked' : '' ?>
													> <label for="karte">Ohne Karte</label><div class="check"></div>
												</div>
												<div class="input-30 radios">
													<input type="radio" name="karte" value="Mit Karte" id="karte2"
														<?= ($_POST['karte'] != 'Ohne Karte') ? 'checked' : '' ?>
													> <label for="karte2">Mit Karte</label><div class="check"></div>
												</div>
												<div class="input-40">
												<?php
												echo $Html->Form->select('aus_karte', false, [
													"" => 'Auswählen',
													"Sonnenschein" => 'Sonnenschein',
													"Rosenkavalier" => 'Rosenkavalier',
													"Von Herzen" => 'Von Herzen',
													"Happy Day" => 'Happy Day',
													"Viel Glück" => 'Viel Glück',
													"Edle Rose" => 'Edle Rose',
													"Baby" => 'Baby',
												], [
													'selected' => $_POST['aus_karte'],
												]);
												?>
												<?= $val->error('aus_karte') ?>
												</div>
											</div>
											<div class="input-100 galerie sh">
												<div class="input-30"></div>
												<div class="input-70 clearfix">
													<div class="input-13">
														<a href="/wp-content/themes/bones-less/library/images/g1.jpg" rel="lightbox" title="Sonnenschein"><img src="/wp-content/themes/bones-less/library/images/g1.jpg"></a>
													</div>
													<div class="input-13">
														<a href="/wp-content/themes/bones-less/library/images/g2.jpg" rel="lightbox" title="Rosenkavalier"><img src="/wp-content/themes/bones-less/library/images/g2.jpg"></a>
													</div>
													<div class="input-13">
														<a href="/wp-content/themes/bones-less/library/images/g3.jpg" rel="lightbox" title="Von Herzen"><img src="/wp-content/themes/bones-less/library/images/g3.jpg"></a>
													</div>
													<div class="input-13">
														<a href="/wp-content/themes/bones-less/library/images/g4.jpg" rel="lightbox" title="Happy Day"><img src="/wp-content/themes/bones-less/library/images/g4.jpg"></a>
													</div>
													<div class="input-13">
														<a href="/wp-content/themes/bones-less/library/images/g5.jpg" rel="lightbox" title="Viel Glück"><img src="/wp-content/themes/bones-less/library/images/g5.jpg"></a>
													</div>
													<div class="input-13">
														<a href="/wp-content/themes/bones-less/library/images/g5.jpg" rel="lightbox" title="Edle Rose"><img src="/wp-content/themes/bones-less/library/images/g6.jpg"></a>
													</div>
													<div class="input-13">
														<a href="/wp-content/themes/bones-less/library/images/g7.jpg" rel="lightbox" title="Baby"><img src="/wp-content/themes/bones-less/library/images/g7.jpg"></a>
													</div>
												</div>
											</div>
											<div class="input-100 clearfix">
												<div class="input-30">
													<p>Kartentext</p>
												</div>
												<div class="input-70"><span class="wpcf7-form-control-wrap k-te IhreNachriht">
													<?= $Html->Form->textarea('kartentext', false, ['cols' => '40', 'rows' => '9', 'class' => 'wpcf7-form-control wpcf7-textarea', 'aria-invalid' => 'false' ]) ?>
													<?= $val->error('kartentext') ?>
												</span></div>

											</div>
										</div>
										<div class="half-15">
											<div class="input-100 clearfix">
												<div class="input-30">
													<p>Anlass</p>
												</div>
												<div class="input-70">
												<?php
												echo $Html->Form->select('anlass', false, [
													"" => 'Anlass',
													"Geburtstag" => 'Geburtstag',
													"Überraschung" => 'Überraschung',
													"Liebeserklärung" => 'Liebeserklärung',
													"Hochzeit" => 'Hochzeit',
													"Geburt" => 'Geburt',
													"Dekoration/Event/Firmengeschenk" => 'Dekoration/Event/Firmengeschenk',
													"Trauerfloristik/Grabschmuck" => 'Trauerfloristik/Grabschmuck',
												], [
													'selected' => $_POST['anlass'],
												]);
												?>
												<?= $val->error('anlass') ?>
												</div>
											</div>
											<div class="input-100 clearfix">
												<div class="input-30">
													<p>Lieferdatum</p>
												</div>
												<div class="input-70">
													<?= $Html->Form->input('lieferdatum', false, ['placeholder' => 'Tag/Monat/Jahr', 'class' => 'date' ]) ?>
													<?= $val->error('lieferdatum') ?>
												</div>
											</div>
											<div class="input-100 clearfix">
												<div class="input-30">
													<p>Zeit</p>
												</div>
												<!-- <input type="text" name="time" id="timepicker" placeholder="H:M"> -->
												<?php
												echo $Html->Form->select('time', false, [
													"VORMITTAG" => 'VORMITTAG',
													"NACHMITTAG" => 'NACHMITTAG',
												], [
													'selected' => $_POST['time'],
													'wrapper' => ['tag' => 'div', 'class' => 'input-70']
												]);
												?>
												<?= $val->error('time') ?>
											</div>
											<div class="input-100 clearfix">
												<div class="input-30">
													<p>ZUSTELLUNG</p>
												</div>
												<?php
												echo $Html->Form->select('zustellung', false, [
                                                    "HAUSLIEFERDIENST (ZÜRICH UND UMGEBUNG)" => 'HAUSLIEFERDIENST (ZÜRICH UND UMGEBUNG)',
                                                    "POSTVERSAND" => 'POSTVERSAND',
                                                    "ABHOLUNG IM BLUMENHAUS WIEDIKON" => 'ABHOLUNG IM BLUMENHAUS WIEDIKON'
												], [
													'selected' => $_POST['zustellung'],
													'wrapper' => ['tag' => 'div', 'class' => 'input-70']
												]);
												?>
											</div>
											<div class="input-100 clearfix">
												<div class="input-30">
													<p>Anmerkungen</p>
												</div>
												<div class="input-70"><span class="wpcf7-form-control-wrap t-ir IhreNachriht huge">
													<?= $Html->Form->textarea('anmerkungen', false, ['cols' => '40', 'rows' => '9', 'class' => 'wpcf7-form-control wpcf7-textarea', 'aria-invalid' => 'false' ]) ?>
												</span></div>
											</div>
										</div>
                                    </div>

                                    <div class="clearfix">
                                        <div class="half-15">
                                            <div class="input-100 clearfix">&nbsp;</div>
                                        </div>
                                        <div class="half-15">
                                            <div class="delivery-cost">
                                                <table>
                                                    <thead>
                                                        <tr>
                                                            <td colspan="3">IHRE BESTELLUNG:</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>CHF</td>
                                                            <td id="flower-cost-td">
                                                                <?= $Html->Form->input('flower-cost', false, ['class' => 'cart-cost', 'value' => '-']) ?>
                                                            </td>
                                                            <td>BLUMENWERT +</td>
                                                        </tr>
                                                        <tr>
                                                            <td>CHF</td>
                                                            <td id="karte-cost-td">
                                                                <?= $Html->Form->input('karte-cost', false, ['class' => 'cart-cost', 'value' => '-']) ?>
                                                            </td>
                                                            <td>KARTE</td>
                                                        </tr>
                                                        <tr>
                                                            <td>CHF</td>
                                                            <td id="delivery-cost-td">
                                                                <?= $Html->Form->input('delivery-cost', false, ['class' => 'cart-cost', 'value' => '-']) ?>
                                                            </td>
                                                            <td id="delivery-cost-caption">LIEFERUNG</td>
                                                        </tr>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <td>CHF</td>
                                                            <td id="cart-total-td">
                                                                <?= $Html->Form->input('cart-total', false, ['class' => 'cart-cost', 'value' => '-']) ?>
                                                            </td>
                                                            <td>TOTAL</td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
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

<?php //get_sidebar('form-bar'); ?>

</div> <!-- end #content -->



<?php get_footer(); ?>
<script>
jQuery(function($){
	if ($('input[name=sameAsBilling]').is(':checked')) {
		$('.whenNotSameAsBilling').attr('disabled', 'disabled');
	}

	if ($('#karte').is(':checked')) {
		$('#Aus_karte').attr('disabled', 'disabled');
	}
});
</script>
