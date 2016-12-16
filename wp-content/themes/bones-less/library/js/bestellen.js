if(typeof(String.prototype.trim) === "undefined")
{
    String.prototype.trim = function() {
        return String(this).replace(/^\s+|\s+$/g, '');
    };
}

/**
 * Usage: (123456789.12345).formatMoney(2, '.', ',');
 * @param c Number    Decimal Places
 * @param d String    Decimal Separator
 * @param t String    Thousand Separator
 * @returns {string}
 */
Number.prototype.formatMoney = function(c, d, t){
    var n = this,
        c = isNaN(c = Math.abs(c)) ? 2 : c,
        d = d == undefined ? "." : d,
        t = t == undefined ? "," : t,
        s = n < 0 ? "-" : "",
        i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))),
        j = (j = i.length) > 3 ? j % 3 : 0;
    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};

jQuery(document).ready(function($) {
	
	
	$('#timepicker').timepicker();

    $("input[name='sameAsBilling']").change(function(){
    	
    	if($(this).is(':checked')){
    		$("input[name='liefeVorname']").val($("input[name='Vorname']").val())
                .prop('disabled', true);

    		$("input[name='liefeStrasse']").val($("input[name='Strasse']").val())
                .prop('disabled', true);

            $("input[name='liefePlz']").val($("input[name='Plz']").val())
                .prop('disabled', true);

    		$("input[name='liefeOrt']").val($("input[name='Ort']").val())
                .prop('disabled', true);

    		$("input[name='liefeTelefon']").val($("input[name='Telefon']").val())
                .prop('disabled', true);

    		$("input[name='liefeE-mail']").val($("input[name='E-mail']").val())
                .prop('disabled', true);
    	}
    	else{
    		$("input[name='liefeVorname']").val('')
                .prop('disabled', false);

    		$("input[name='liefeStrasse']").val('')
                .prop('disabled', false);

            $("input[name='liefePlz']").val('')
                .prop('disabled', false);

    		$("input[name='liefeOrt']").val('')
                .prop('disabled', false);

    		$("input[name='liefeTelefon']").val('')
                .prop('disabled', false);

    		$("input[name='liefeE-mail']").val('')
                .prop('disabled', false);
    	}
        printCartCost();
    });

    if($('#karte2').is(':checked')) { 
       $("select[name='aus_karte']").prop('disabled', false);
       $(".input-100.galerie").addClass( "sh" );
       $(".t-ir").addClass( "huge" );
    }

    $('input[name=karte]').change(function() {
        if (this.value.toUpperCase() == 'OHNE KARTE') {
            $("select[name='aus_karte']").prop('disabled', true);
            $(".input-100.galerie").removeClass( "sh" );
            $(".t-ir").removeClass( "huge" );

        }
        else if (this.value.toUpperCase() == 'MIT KARTE') {
            $("select[name='aus_karte']").prop('disabled', false);
            $(".input-100.galerie").addClass( "sh" );
            $(".t-ir").addClass( "huge" );
        }
    });

    /**
     * By Raymond
     * Cart Cost Computation
     * Date: 16 December 2016
     */
    var delivery_cost_table = eval($APP_DATA.delivery_cost);
    var delivery_cost_on_request = $APP_DATA.delivery_cost_on_request;
    var card_cost = parseFloat($APP_DATA.card_cost);

    var getDeliveryPlz = function() {
        var plz = $('input[name=Plz]').val().trim(),
            liefePlz = $('input[name=liefePlz]').val().trim();
        return liefePlz ? liefePlz : plz;
    };

    var getCardCost = function() {
        if ($('#Aus_karte').is(':enabled') && $('#Aus_karte').val()) {
           return card_cost.formatMoney();
        }
        return '-';
    };

    var delivery_cost = 0;
    var isDeliveryCostFound = function(plz) {
        if (plz) {
            var plzIndex = 0,
                costIndex = 2;
            for (var x = 0; x < delivery_cost_table.length; x += 1) {
                if (delivery_cost_table[x][plzIndex] == plz) {
                    delivery_cost = parseFloat(delivery_cost_table[x][costIndex]);
                    return true;
                }
            }
        }
        return false;
    };

    var addCartTotal = function() {
        var flowerCost = parseFloat($('#flower-cost').text().replace(',', '')),
            karteCost = parseFloat($('#karte-cost').text().replace(',', '')),
            deliveryCost = parseFloat($('#delivery-cost').text().replace(',', ''));

        flowerCost = isNaN(flowerCost) ? 0 : flowerCost;
        karteCost = isNaN(karteCost) ? 0 : karteCost;
        deliveryCost = isNaN(deliveryCost) ? 0 : deliveryCost;

        return flowerCost + karteCost + deliveryCost;
    };

    var getFlowerCost = function(){
        var price_str = $('#Preisrahamen').val(),
            price = '-';
        if (price_str) {
            price = parseFloat(price_str.match(/\d+/)[0]).formatMoney();
        }
        return price;
    };

    var printCartCost = function() {
        $('#flower-cost').text(getFlowerCost());
        $('#karte-cost').text(getCardCost());

        if (isDeliveryCostFound(getDeliveryPlz())) {
            $('#delivery-cost').text(delivery_cost.formatMoney());
            $('#delivery-cost-caption').text('LIEFERUNG');
        } else {
            $('#delivery-cost').text('-');
            $('#delivery-cost-caption').text(delivery_cost_on_request);
        }

        $('#cart-total').text(addCartTotal().formatMoney());
    };

    printCartCost();

    $('#Preisrahamen, #Aus_karte, input[name=karte], input[name=karte2]').on('change', function(){
        printCartCost();
    });
    $('input[name=Plz], input[name=liefePlz]').on('keyup', function(){
        printCartCost();
    });
});
