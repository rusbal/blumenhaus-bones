jQuery(document).ready(function($) {
	$(".radios .check").click(function () {

		$(this).siblings('input').click();

	});
$( "#date" ).datepicker({ dateFormat: 'dd/mm/yy' });
	var head = $(".header.wrap").width();
	$("#object").css('width', head + 160);

	$(window).on('resize', function () {
		var head = $(".header.wrap").width();
		$("#object").css('width', head + 160);

	});


	$(function () {

		if ($(window).width() > 767) {
			skrollr.init();
		}


		$(window).on('resize', function () {
			if ($(window).width() <= 767) {
				skrollr.init().destroy();
			}
		});
	});
});