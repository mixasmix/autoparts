
var loadpage = {};
var loader = {};

window.addEvent('domready', function(){

	loadpage = $('loadpage');
	loader = $('loader');


    var split = window.location.href.split('?');
    var href = '';

    if(split[1]){
        if(split[1].indexOf('#') != -1){
            href += split[1].substr(0, split[1].indexOf('#'));
        }
        else{
            href += split[1];
        }
        href = '&' + href;
    }

    HM = new HistoryManager();

    HM.addEvent('changed',function(value){
        var url = window.location.href+'fromchanged/true/l/' +value;
        if(value == '' || value == '#'){
            url = window.location.href+'fromchanged/true' + href;
        }
        sendRequest(url);
    });

    var ajxrf = true;
    HM.initAjax(ajxrf);

    if(ajxrf){
        HM.start();
    }
    else{
        HM.set('');
    }

});

function onRequest(){

	if (Browser.Engine.trident4)loadpage.empty();

	var size = loadpage.getParent().getSize();

	loader.setStyles({
		'width': size.x,
		'height': size.y,
		'display': ''
	});


}


function onComplete(){

	loadpage.getElements('.over').each(function(el){


		el.addEvents({
			'mouseenter': function(){
				actionLink(el, 'over');
			},
			'mouseleave': function(){
				actionLink(el, 'out');
			},
			'over':function(){
				el.setStyles({"background-color":"#006FA4", "color":"#FFFFFF"});

			},
			'out':function(){
				el.setStyles({"background-color":"", "color":""});
			}
		});
	});
    loadpage.getElements('.over_td').each(function(el){


        el.addEvents({
            'mouseenter': function(){
                actionLink(el, 'over');
            },
            'mouseleave': function(){
                actionLink(el, 'out');
            },
            'over':function(){
                el.setStyle("border-color", "#FF0000");
            },
            'out':function(){
                el.setStyle("border-color", '');
            }
        });
    });
	loadpage.getElements('.coord').each(function(el){


		el.addEvents({
			'mouseenter': function(){
				actionLink(el, 'over');
			},
			'mouseleave': function(){
				actionLink(el, 'out');
			},
			'over':function(){
				el.setStyle("border", "2px solid #FF0000");
			},
			'out':function(){
				el.setStyle("border", "2px solid #006FA4");

			}
		});
	});

	var image_load = [];
	loadpage.getElements('img.resize').each(function(el, i){

		el.addEvent("load", function(){
			if (image_load[i] != 1){
				correct_iw(el, true);
				image_load[i] = 1;
                window.addEvent('resize', function(){
                    correct_iw(el, true);
                });
			}
		});

	});

	loadpage.getElements('.article').each(function(el){
		el.addEvents({
			'mouseenter': function(){
				el.setStyles({"background-color":"#6DADCB"});
			},
			'mouseleave': function(){
				el.setStyles({"background-color":""});
			}
		});
	});

	loadpage.getElements('form').each(function(el){

		el.addEvent('submit', function(ev){
			ev.stop();
			HM.fireEvent('changed', el.getProperty('action') + '&' + el.toQueryString());

		});
	});


	var fs = $('search_text');


	if ($chk(fs)){

		fs.addEvent('keyup', function(ev){

			var s = $('search_text').getProperty('value');

			loadpage.getElements('li').each(function(el){

                if($chk(el.getElement('div[name=title]'))){
                    if (el.getElement('div[name=title]').get('html').test(s, "i")){
                        el.setStyle('display', '');
                    }else{
                        el.setStyle('display', 'none');
                    }
                }

			});

		});

	}

	//loader.setStyle('display','none');

}

//Для обработки изображений - переход на строку
function goToCalloutRow(name)
{
    //снятие подсветок позиций
    var lights = loadpage.getElements('.over').each(function(el){
        if(el.hasClass('highlight')){
            el.removeClass('highlight');
        }
    });

    //переход к позиции
    var divs = loadpage.getElements('[name='+name+']');
    var ch = false;
    divs.each(function(el){
        if(el.nodeName.toLowerCase() == 'tr' && ch == false){
            var myFx = new Fx.Scroll(window).toElement(el);
            ch=true;
        }
    });

    if(!ch){
        alert('Необслуживаемая деталь!');
        return;
    }


    //подсветка позиции
    divs.each(function(el){
        el.addClass('highlight');
    });

}

var sw;
function articleRow(article){
    if(article == ''){
        alert('Необслуживаемая деталь!');
        return false;
    }
	if(sw) sw.close();
	sw = window.open('http://www.autodoc.ru/Web/price/art/'+article+'?analog=on&access=1', 'site');
	sw.focus();
	return false;
}