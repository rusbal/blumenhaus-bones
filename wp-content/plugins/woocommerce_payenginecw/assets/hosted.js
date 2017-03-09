var payengineFlexCheckout =  new function () {
	
	this.includeCss = function(url){
		var cssId = 'payengine-overlay';
		if (!document.getElementById(cssId))
		{
		    var head  = document.getElementsByTagName('head')[0];
		    var link  = document.createElement('link');
		    link.id   = cssId;
		    link.rel  = 'stylesheet';
		    link.type = 'text/css';
		    link.href = url;
		    link.media = 'all';
		    head.appendChild(link);
		}
	}
	
	this.createIframe = function(url, jQ){
		var over = 
			'<div id="payengine-flex-overlay" class="payengine-flex-overlay">'+
				
					'<iframe src="'+url+'" class="payengine-flex-content"></iframe>'+
			'</div>';
		jQ(over).appendTo('body');
		jQ('body').addClass('payengine-flex-noscroll');
	}

}