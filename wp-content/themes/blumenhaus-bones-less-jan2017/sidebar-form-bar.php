<?php

use Rsu\Validator\Validator;

require __DIR__ . '/vendor/autoload.php';
$Html = htmlHelper();

?>
				<div id="sidebar-form-bar" class="white first-ordering-form " role="complementary">
					<div class="wrap">

						<div class="sidebar-form-info">
							<?php if ( is_active_sidebar( 'form_sidebar' ) ) : ?>

								<?php dynamic_sidebar( 'form_sidebar' ); ?>

							<?php endif; ?>
						</div>

						<div class="sidebar-form">
							<form action="<?php echo get_page_link( get_page_by_title('Bestellen')->ID ); ?>" method="post">
								<div class="four-md-col three-xs-col middle-gap">
									<div class="form-items">
										<?php
										echo $Html->Form->select('blumenart', false, [
											""=>'BLUMENSCHMUCK',
											"Blumenstrauss" => 'Blumenstrauss',
											"Blumenkorb" => 'Blumenkorb',
											"Blumenherz" => 'Blumenherz',
											"Rosen" => 'Rosen',
											"Orchideen-Pflanze" => 'Orchideen-Pflanze',
										]);

										echo $Html->Form->select('preisrahamen', false, [
											""=>'Blumenwert',
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
										]);
										?>
									</div>


									<div class="form-items">
										<?php
										echo $Html->Form->select('anlass', false, [
											""=>'ANLASS',
											"Geburstag" => 'Geburstag',
											"Überraschung" => 'Überraschung',
											"Liebeserklärung" => 'Liebeserklärung',
											"Hochzeit" => 'Hochzeit',
											"Geburt" => 'Geburt',
											"Dekoration/Event/Firmengeschenk" => 'Dekoration/Event/Firmengeschenk',
											"Trauerfloristik/Grabschmuck" => 'Trauerfloristik/Grabschmuck',
										]);

										echo $Html->Form->select('blumenfarbe', false, [
											""=>'Blumenfarbe',
											"Weiss" => 'Weiss',
											"Gelb" => 'Gelb',
											"Rot" => 'Rot',
											"Rosa" => 'Rosa',
											"Fuchsia" => 'Fuchsia',
											"Orange" => 'Orange',
											"Violett" => 'Violett',
											"Blau" => 'Blau',
											"Grün" => 'Grün',
										]);
										?>
									</div>

									<div class="form-items">
										<?php
										echo $Html->Form->select('karte', false, [
											"Mit Karte"=>'MIT KARTE',
											"Ohne Karte"=>'OHNE KARTE'
										]);
										?>

										<input class="date" name="lieferdatum" id="lieferdatum" placeholder="Tag/Monat/Jahr" type="text">
									</div>

									<div class="form-right">
										<div class="submit-button-wrp">
											<input class="submit-button" type="submit" value="ZUR KASSE">
											<i class="submit-button-arrow"></i>
										</div>
									</div>
								</div>

							</form>
						</div>

					<!-- <div class="">
						
				</div> -->
			</div>
			<div id="object2" data-bottom-top="background-position:0px 100px;" data-end="background-position:55px 335px;" class="skrollable skrollable-between" ></div>
			<div id="object3" data-bottom-top="background-position:0px 100px;" data-end="background-position:55px 260px;" class="skrollable skrollable-between" ></div>
		</div>
