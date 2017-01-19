jQuery(document).ready(function($) {
	
	
	$('#timepicker').timepicker();

    $("input[name='sameAsBilling']").change(function(){
    	
    	if($(this).is(':checked')){
    		$("input[name='liefeVorname']").val($("input[name='Vorname']").val());
    		$("input[name='liefeVorname']").prop('disabled', true);

    		$("input[name='liefeStrasse']").val($("input[name='Strasse']").val());
    		$("input[name='liefeStrasse']").prop('disabled', true);

    		$("input[name='liefePlzOrt']").val($("input[name='PlzOrt']").val());
    		$("input[name='liefePlzOrt']").prop('disabled', true);

    		$("input[name='liefeTelefon']").val($("input[name='Telefon']").val());
    		$("input[name='liefeTelefon']").prop('disabled', true);

    		$("input[name='liefeE-mail']").val($("input[name='E-mail']").val());
    		$("input[name='liefeE-mail']").prop('disabled', true);
    	}
    	else{
    		$("input[name='liefeVorname']").val('');
    		$("input[name='liefeVorname']").prop('disabled', false);

    		$("input[name='liefeStrasse']").val('');
    		$("input[name='liefeStrasse']").prop('disabled', false);

    		$("input[name='liefePlzOrt']").val('');
    		$("input[name='liefePlzOrt']").prop('disabled', false);

    		$("input[name='liefeTelefon']").val('');
    		$("input[name='liefeTelefon']").prop('disabled', false);

    		$("input[name='liefeE-mail']").val('');
    		$("input[name='liefeE-mail']").prop('disabled', false);
    	}
    });

    if($('#karte2').is(':checked')) { 
       $("select[name='aus_karte']").prop('disabled', false);
       $(".input-100.galerie").addClass( "sh" );
       $(".t-ir").addClass( "huge" );
   }
   $('input[name=karte]').change(function() {
    if (this.value == 'OHNE KARTE') {
        $("select[name='aus_karte']").prop('disabled', true);
        $(".input-100.galerie").removeClass( "sh" );
        $(".t-ir").removeClass( "huge" );

    }
    else if (this.value == 'MIT KARTE') {
        $("select[name='aus_karte']").prop('disabled', false);
        $(".input-100.galerie").addClass( "sh" );
        $(".t-ir").addClass( "huge" );
    }
});


});