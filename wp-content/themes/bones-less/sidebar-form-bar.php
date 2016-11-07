<?php

require 'includes/html_helper.php';

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
								<?php echo $Html->Form->hidden('form-name', ['value' => 'blumenbestellung']); ?>
								<div class="four-md-col three-xs-col middle-gap">
									<div class="form-items">
										<?php
										echo $Html->Form->select('blumenschmuck_bar', false, [
											"0"=>'BLUMENSCHMUCK',
											"BLUMENSTRASS"=>'BLUMENSTRASS',
											"BLUMENKORB"=>'BLUMENKORB',
											"BLUMENHERZ"=>'BLUMENHERZ',
											"ROSEN"=>'ROSEN',
											"ORCHIDEEN-PFLANZE"=>'ORCHIDEEN-PFLANZE'
										], ['selected' => $_POST['blumenschmuck_bar']]);

										echo $Html->Form->select('preisrahamen_bar', false, [
											"0"=>'PREISRAHMEN',
											"CHF 30.–"=>'CHF 30.–',
											"CHF 50.–"=>'CHF 50.–',
											"CHF 75.–"=>'CHF 75.–',
											"CHF 100.–"=>'CHF 100.–',
											"CHF 150.–"=>'CHF 150.–',
											"CHF 200.–"=>'CHF 200.–',
											"CHF 250.–"=>'CHF 250.–',
											"CHF 300.–"=>'CHF 300.–',
											"CHF 500.–"=>'CHF 500.–',
											"CHF 800.–"=>'CHF 800.–',
											"CHF 1’000.–"=>'CHF 1’000.–',
										], ['selected' => $_POST['preisrahamen_bar']]);
										?>
									</div>


									<div class="form-items">
										<?php
										echo $Html->Form->select('anlass_bar', false, [
											"0"=>'ANLASS',
											"GEBURTSTAG"=>'GEBURTSTAG',
											"ÜBERRASCHUNG"=>'ÜBERRASCHUNG',
											"LIEBESERKLÄRUNG"=>'LIEBESERKLÄRUNG',
											"HOCHZEIT"=>'HOCHZEIT',
											"GEBURT"=>'GEBURT',
											"DEKORATION/EVENT/FIRMENGESCHENK"=>'DEKORATION/EVENT/FIRMENGESCHENK',
											"TRAUERFLORISTIK/GRABSCHMUCK"=>'TRAUERFLORISTIK/GRABSCHMUCK',
										], ['selected' => $_POST['anlass_bar']]);

										echo $Html->Form->select('blumenfarbe_bar', false, [
											"0"=>'Blumenfarbe',
											"WEISS"=>'WEISS',
											"GELB"=>'GELB',
											"ROT"=>'ROT',
											"ROSA"=>'ROSA',
											"FUCHSIA"=>'FUCHSIA',
											"ORANGE"=>'ORANGE',
											"OVIOLETT"=>'VIOLETT',
											"BLAU"=>'BLAU',
											"GRÜN"=>'GRÜN',
										], ['selected' => $_POST['blumenfarbe_bar']]);
										?>
									</div>

									<div class="form-items">
										<?php
										echo $Html->Form->select('karte_bar', false, [
											"MIT KARTE"=>'MIT KARTE',
											"OHNE KARTE"=>'OHNE KARTE'
										], ['selected' => $_POST['karte_bar']]);
										?>

										<input class="date" name="lieferdatum_bar" id="lieferdatum_bar" placeholder="Tag/Monat/Jahr" type="text" value="<?= $_POST['lieferdatum_bar'] ?>">
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