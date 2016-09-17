function btc_ticker() {
    var t=this;
    t.siteURL='http://bravenewcoin.com/ticker/',
    t.apiURL='http://api.bravenewcoin.com/ticker/',
    t.defaultCoin='BTC',
    t.refreshRate=500;
       
    t.startTicker=function(){
    	window.jQuery&&(
    	t._ticker=document.getElementById("bnc-ticker-8000000000001"),
		t._ticker.coin=t._ticker.getAttribute("data-coin"),
//		t._ticker.coin||console.warn("No coin specified; defaulting to 'BTC'."),
		t._ticker.coin=(t._ticker.coin?t._ticker.coin.toUpperCase():'BTC'),
		t.initTicker(),
		t.createTicker(),
		t.getTickerData(),       
		t.autoRefresh()
		)    	
    }
    
    t.initTicker=function(){
        t._html=['<div id="ticker" style="background-image: url(\'' + t.siteURL + 'bnc-ticker-background.png\'); background-color: #transparent; height: 247px; width: 300px; position: relative;"> \
	                 <div class="coin-icon"> \
                 		<img src="http://bravenewcoin.com/images/coins/' + t._ticker.coin.toLowerCase() + '.png" title="" alt="" style="box-shadow: 0; position: absolute; left: 25px; top: 15px; width: 50px; border: 0; outline: 0; margin: 0px; padding: 0px;"> \
	                 </div> \
      	             <div style="left: 230px; position: absolute; top: 26px;"> \
                 		<p style="font-family: Verdana,Helvetica; color: #eab020; font-size: 11px; font-weight: bold; margin: 0px; padding: 0px; border: 0;"><span style="border: 0; padding: 0px; margin: 0px;" id="coin-pcnt"></span></p> \
                 	</div> \
	                 <div style="position: absolute; left: 86px; top: 14px;"> \
                 		<h2 style="font-family: Verdana,Helvetica; color: #FFF; font-size: 30px; font-weight: bold; margin: 0px; padding: 0px; border: 0;"><span style="border: 0; padding: 0px; margin: 0px;" id="coin-last" >$</span></h2> \
                 	 </div> \
	                 <div id="ticker-chart" style="position: absolute; left: -4px; top: 87px; height: 115px; width: 300px;"><span style="border: 0; padding: 0px; margin: 0px;" id="spark-line">Loading..</span></div> \
          	        <div style="position: absolute; left: 90px; top: 50px; color: #999999;"> \
                 		<p style="font-family: Verdana,Helvetica; font-size: 10px; font-weight: bold; padding: 0px; margin: 0px; line-height: 10px; border: 0;">Last 24hr Vol:</p> \
                 	</div> \
                    <div style="position: absolute; left: 90px; top: 50px; color: #999999;"> \
                        <p style="font-family: Verdana,Helvetica; font-size: 10px; font-weight: bold; padding: 0px; margin: 0px; line-height: 10px; border: 0;"></p> \
                    </div> \
                    <div style="color: #999; font-size: 10px; font-weight: bold; left: 173px; position: absolute; top: 50px;"> \
                 		<p style="font-family: Verdana,Helvetica; font-size: 10px; font-weight: bold; padding: 0px; margin: 0px; border: 0; line-height: 10px;"><span style="border: 0; padding: 0px; margin: 0px;" id="coin-vol"></span></p> \
                 	</div> \
                    <div class="bravenewcoin-ticker-logo"> \
                        <a href="http://bravenewcoin.com" title="BraveNewCoin - Home of Crypto" target="_blank"><img src="' + t.siteURL + '/bnc-ticker-logo.png" title="" alt="" style="box-shadow: 0; border: 0; outline: 0; margin: 0px; padding: 0px; left: 94px; position: absolute; top: 207px; width: 195px height: 34px;"></a> \
                    </div> \
                 </div><style type="text/css">.jqstooltip {-webkit-box-sizing: content-box;-moz-box-sizing: content-box;box-sizing: content-box;}</style>'
                 ];
    }
    
    t.createTicker=function(){
    	t._ticker.innerHTML=t._html;
    }
    
    t.get_JSON = function(url, callback){
	    if (window.XDomainRequest) { 
	        var request = new window.XDomainRequest();
	        request.open('GET', url, true);
	        request.onload = function() {
		    	callback(JSON.parse(request.responseText));
	        };
	        request.send();
	    } else {
	    	jQuery.getJSON(url, callback);
	    }	
    };

    t.roundValue = function(value) {
    	var str = "";
    	var dec = 2;
    	if (value < 1) { dec = 5;}
        if (value < .01) { dec = 6;}
    	if (value < .0001) { dec = 7;}
    	if (value < .00001) { dec = 8;}
        if (value < .000001) { dec = 9;}
    	var newnumber = new Number(value+'').toFixed(parseInt(dec));
    	if (newnumber==0) {
    		str += '0.00';
    	} else {
    		str +=  ''+ newnumber;
    	}
    	return str;
    }

    t.formatCurrency = function(id_currency, amount, showDecimals) {
    	if (typeof showDecimals == 'undefined'){
    		showDecimals = true;
    	}
    	var currencyStr = ''; 
    	currencyStr += roundValue(amount);
    	x = currencyStr.split('.');
    	x1 = x[0];
    	x2 = x.length > 1 ? '.' + x[1] + (x[1].length==1?'0':''): '';
    	var rgx = /(\d+)(\d{3})/;
    	while (rgx.test(x1)) {
    	  x1 = x1.replace(rgx, '$1' + ',' + '$2');
    	}
    	if(id_currency>'') {
    		return x1 + (showDecimals?x2:'') + '&nbsp;<small>' + id_currency + '</small>';
    	} else {
    		return x1 + (showDecimals?x2:'');
    	}  
    };
    
    t.tooltipNumberFormat=function(value){
    	return  t.roundValue(value)   	
    };

	t.getTickerData = function() {
		var url = t.apiURL + 'bnc_ticker_' + t._ticker.coin.toLowerCase() + '_data.json';
		t.get_JSON(url, function(data){

            var currrencyCurrent = data.ticker_currency;
            var currencySymbol = '$';
            if(currrencyCurrent == 'BTC') {
                currencySymbol = '\u0E3F';
            }

	        jQuery('#spark-line').sparkline(data['ticker_data'], { 
	        	type:'line',
                lineWidth: 1,
	        	lineColor:'#FFF',
                fillColor:'transparent', 
	        	spotColor:null, 
	        	width: '305px', 
	        	height: '100px', 
	        	highlightSpotColor: '#e5b13b',
	        	highlightLineColor: '#e5b13b',
	        	minSpotColor: '#e5b13b',
	        	maxSpotColor: '#e5b13b',	        	
	        	spotRadius: 4.0,
	        	tooltipPrefix: currencySymbol,
	        	numberFormatter: t.tooltipNumberFormat
	        });


	        jQuery('#coin-name').html(data.coin_name);
	        jQuery('#coin-last').html(currencySymbol+t.formatCurrency('', data.last_price));
	       	//jQuery('#periodhigh').html('$'+t.formatCurrency('', data.high_price));
	       	//jQuery('#periodlow').html('$'+t.formatCurrency('', data.low_price)); 
	       	var pcnt = new Number((data.last_price-data.open_price)/data.open_price*100+'').toFixed(parseInt(2));
	       	jQuery('#coin-pcnt').html(((pcnt>0)?'+':'') + pcnt + '%'); 
	       	jQuery('#coin-vol').html(currencySymbol + data.volume_24hr); 
	        jQuery('.coin-icon img').attr('title', data.coin_name);
	        jQuery('.coin-icon img').attr('alt',t._ticker.coin);  

            t.cleanUp();     
		});
	}

	t.autoRefresh = function() {
		reloadTimer = setInterval(function() {
			t.getTickerData();
		}, t.refreshRate);
	}

    t.cleanUp = function() {
        /* Post render cleanup */
        bnctargeth2 = jQuery("#bnc-ticker-8000000000001 #ticker h2 span").html();
        bnctargeth2length = bnctargeth2.length;
        bnctargetsize = 30;
        if(bnctargeth2length > 7) {
            bnctargetsize = 25;
        }
        if(bnctargeth2length > 8) {
            bnctargetsize = 22;
        }
        if(bnctargeth2length > 9) {
            bnctargetsize = 18;
        }
        if(bnctargeth2length > 10) {
            bnctargetsize = 17;
        }               
        
        jQuery("#bnc-ticker-8000000000001 #ticker h2").css("font-size", bnctargetsize);
    }    
	
	var a=document.getElementsByTagName("head")[0],
	b=document.createElement("script");
	b.type="text/javascript",
	b.onload=function(){t.startTicker()},
	b.onreadystatechange = function () {
    	if (b.readyState == 'complete' || b.readyState == 'loaded') {t.startTicker();}
    },
    b.src=t.siteURL+"jquery-sparkline.min.js",
	a.appendChild(b);
};
