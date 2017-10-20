
function correct_iw(element, hw){
	
	
	if (!$chk(element.getParent())) return false;
	
	var w = element.getParent().getProperty('width');
	
	w = w.toInt() / 100;
	
	
	var dw = screen.availWidth;
	
	if (!$chk(element.getSize())) return false;
	
	var iw = element.getSize().x;
	
	m  = ((dw*w)/iw);
    
	if($chk(hw)){
        correct_xy_hw(element, m, hw);
    }
    else{
        correct_xy_hw(element, m);
    }
	
	element.width = Math.ceil(dw*w);
}

function correct_xy(img_obj, m){
			
	m = Math.round(m*1000)/1000;
	
	var parent_id = img_obj.getParent();
	
	var div_arr = parent_id.getElements('.coord');

	if (div_arr.length > 0){
		
		div_arr.each(function(element) {
			
			var div_y = element.getStyle('margin-top').toInt();
			var div_x = element.getStyle('margin-left').toInt();
			
			element.setStyles({
				'margin-top': Math.round(div_y * m) + "px",
				'margin-left': Math.round(div_x * m) + "px"
			});
			
			
		});
	}
}

function correct_xy_hw(img_obj, m, hw){
			
	m = Math.round(m*1000)/1000;
	
	var parent_id = img_obj.getParent();
	
	var div_arr = parent_id.getElements('.coord');

	if (div_arr.length > 0){
		
		div_arr.each(function(element) {
			
			var div_y = element.getStyle('margin-top').toInt();
			var div_x = element.getStyle('margin-left').toInt();            
            var div_h = element.getStyle('height').toInt();
            var div_w = element.getStyle('width').toInt();
            
            //РЈСЃС‚Р°РЅРѕРІРєР° РєРѕРѕСЂРґРёРЅР°С‚
			if($chk(hw)){
                var new_h = Math.round(div_h * m);
                var new_w = Math.round(div_w * m);
                
                element.getFirst().setStyles({
                    'height': new_h + "px",
                    'width': new_w + "px"
                });
                
                element.setStyles({
                    'margin-top': Math.round(div_y * m) + "px",
                    'margin-left': Math.round(div_x * m) + "px",
                    'height': new_h + "px",
                    'width': new_w + "px"
                });
            }
            else{
                element.setStyles({
                    'margin-top': Math.round(div_y * m) + "px",
                    'margin-left': Math.round(div_x * m) + "px"                   
                });
            }
			
			
		});
	}
}

/*************************************************************************/
/*
/*************************************************************************/
function actionLink(el, fire){
	
	var ename = el.getProperty('name');
	
	if (ename == null){
		el.fireEvent(fire);
		return;
	}

	loadpage.getElements('[name='+ename+']').each(function(el2){
		el2.fireEvent(fire);
	});
	
}
/*************************************************************************/

function lp(u, st, way){
	
	if($chk(st))u+= "&st="+st;

	if($chk(way)){

		wayObject.set(st,{
			"st"  : st,
			"text": way,
			"url" : u
		});
	}
	
	currentUrl = u;
	currentSt  = st;
	
    sendRequest(currentUrl);
	
}

function lpl(l){
    loadpage.load(l);
}

function sendRequest2(url){
    onRequest();
    loader.setStyle('display', '');
        
    new Request({
        method: 'get', // GET Р·Р°РїСЂРѕСЃ
        url: url,
        onSuccess: function(html) { // СЌС‚Рѕ РјС‹ РґРµР»Р°РµРј, РєРѕРіРґР° РІСЃРµ РѕРє                                
                new Request({
                    method: 'get', // GET Р·Р°РїСЂРѕСЃ
                    url: url + eval(html),
                    onSuccess: function(html2) { // СЌС‚Рѕ РјС‹ РґРµР»Р°РµРј, РєРѕРіРґР° РІСЃРµ РѕРє
                
                        loadpage.set('html', html2);
                        onComplete();
                        loader.setStyle('display', 'none');
                    },
                    onFailure: function() { // РµСЃР»Рё РІСЃРµ РїР»РѕС…Рѕ
                        //loadpage.set('html', 'The request failed.');
                        loader.setStyle('display', 'none');
                    }
                }).send();
                /*loadpage.set('html', html);
                onComplete();
                loader.setStyle('display', 'none');*/
            },
            onFailure: function() { // РµСЃР»Рё РІСЃРµ РїР»РѕС…Рѕ
                //loadpage.set('html', 'The request failed.');
                loader.setStyle('display', 'none');
            }
    }).send();
}

function HadleStaticPRequest(){    
    onComplete();
}

function HadleStaticPRequest2(){    
    loader = $('loader');	
    onRequest();
    loader.setStyle('display', '');    
    
    var url = window.location.href;
    
    if(url.indexOf('?') == -1)
    {
        url += '?';
    }
    
    url += '&fromchanged=true';
        
    new Request({
        method: 'get', // GET Р·Р°РїСЂРѕСЃ
        url: url,
        onSuccess: function(html) { // СЌС‚Рѕ РјС‹ РґРµР»Р°РµРј, РєРѕРіРґР° РІСЃРµ РѕРє                                
                new Request({
                    method: 'get', // GET Р·Р°РїСЂРѕСЃ
                    url: url + eval(html),
                    onSuccess: function(html2) { // СЌС‚Рѕ РјС‹ РґРµР»Р°РµРј, РєРѕРіРґР° РІСЃРµ РѕРє                
                        loadpage.set('html', html2);
                        onComplete();
                        loader.setStyle('display', 'none');
                    },
                    onFailure: function() { // РµСЃР»Рё РІСЃРµ РїР»РѕС…Рѕ                        
                        loader.setStyle('display', 'none');
                    }
                }).send();                
            },
            onFailure: function() { // РµСЃР»Рё РІСЃРµ РїР»РѕС…Рѕ                
                loader.setStyle('display', 'none');
            }
    }).send();
}

function stop(e){
    if(e.stopPropagation) e.stopPropagation();
    else e.cancelBubble = true;
    if(e.preventDefault) e.preventDefault();
    else e.returnValue = false;
}