/*
** Функция возвращат объект XMLHttpRequest
*/




function getXmlHttpRequest()
{
	if (window.XMLHttpRequest) 
	{
		try 
		{
			return new XMLHttpRequest();
		} 
		catch (e){}
	} 
	else if (window.ActiveXObject) 
	{
		try 
		{
			return new ActiveXObject('Msxml2.XMLHTTP');
		} catch (e){}
		try 
		{
			return new ActiveXObject('Microsoft.XMLHTTP');
		} 
		catch (e){}
	}
	return null;
}

//Надо назначить слушателя событий для button
window.onload=function(){
	if(document.getElementById('searchPart')){
		//document.getElementById('searchPart').addEventListener('click', getPart);
                $('#searchPart').on('click', getPart);
	}
	$.ajax({url: "/addbacket/check/",  success: checkbacket});
	//получаем хэш
	//и если он есть то запускаем поиск
	
	if(loc){
		getParts(loc);
		//find_form=$('#body-container').html();
	}
	//забиндим клик на отправить в recall
	$('#recall_popup_block input[type=submit]').on('click', sendRecall);
        checkNote();
}
var loc=window.location.hash.replace("#","");
var loc=loc.replace('!', '');
var find_form;
var form_part_num; //здесь будут хранится номер из формы
var providers=12; //Количество поставщиков
var stop_search=false; //если труе то поиск прекращаем
var allparts=[]; //здесь у нас будут все позиции хранится
function addParts(dat, n){
	$.ajax({ type: "POST", url: "/parts/addparts/"+n, data: "parts="+dat,  success: function(){}});
}
function getPart(event, part){
        allparts=[];
	stop_search=false;
	//получаем объект поля ввода номера
        $('div').remove('#information');
        if($('div').is('#bestParts')){
            $('div').remove('#bestParts');
        }
        if(!part){
            var part=document.getElementById('partNumber');
        }
	//если пусто в поле ввода, то не делаем ничего
	if(!part.value)
		return false;
	$('#status').css('display', 'block');
	$('#stop_search').css('display', 'block');
	$('#stop_search').on('click', function(){
		$('#status').css('display', 'none');
		stop_search=true;
		$('#stop_search').css('display', 'none');
	});
	event.preventDefault();
		addpage.count=false;
		addpage.all = false;	
	
        /*if(part.value=='GXE10-0088644'){
             window.location.href = "/page/secretpage/";
             return false;
        }*/
	//если value не равно пустой строке то делаем запрос
	if(part.value){
		find_form=/*$('#body-container').html();*/'<div style="width:100%; position: relative; margin:0 auto;" id="search"> <form style="height:150px;" action="/" method="POST"> <h1>НАЙТИ АВТОЗАПЧАСТИ</h1> <input value="2888" style="margin-left:50px" id="partNumber" name="part_number" placeholder="Введите номер запчасти" type="text"> <h6>Например, GXE10-0088644</h6> <!--<p>или</p><br><input type="text" name ="vin_number" placeholder="Введите VIN-номер вашего автомобиля" id="vinNumber"/><h6>Например, JN1WNYD21U0000001</h6>--><br> <input style="position:absolute; top:50px; left:650px" id="searchPart" value="Найти" type="submit">&nbsp;</form> </div>';
		$('#body-container').html('');
		form_part_num=part.value;
		for(var i=1; i<=providers; i++){
			$.ajax({url: "/parts/getparts/"+part.value+"/"+i+"/",  success: addpage});
			//addpage.count=i;
		
		}	
			
		
	}
		
	//иначе проверем не пусто ли поле vin
	else {
		var vin=document.getElementById('vinNumber');
		//
		//
		// Здесь надо буцдет работать с каталогом Пока оставим, разберемся с parts
		//
		//
		//
		//
		//
		//
		//
		//
		//
		//
		//
		//
	}
  }


function addpage(d){
        
	//если поиск прекращен
	if(stop_search){
		return false;
	}
	addpage.count++;
	//так, пришли данные. 
	// проверяем существование блока partlist и таблицы
	// если таблицы нет, то создаем ее
	if(!$('div').is('#partlist')){
		//добавляем элемент
		
		$('.main-content').html($('#search').html()+'<div id="partlist"></div>');
		$('#partlist').html('<div id="legend" class="legend">	\n\
                                        <p>Таким цветом \n\
                                        <span class="colored_green"></span> \n\
                                        выделены оригинальные детали по запрошенному номеру. <br>		\n\
                                        Минимальный заказ по умолчанию - 1 шт. Если в поле "количесто" указано значение, \n\
                                        отличное от 1, например 10 - то минимальный заказ позиции не может быть меньше 10 шт и \n\
                                        должен быть кратным 10 - 10, 20, 30 и тд. Цена указана за одну единицу.	\n\
                                        </p>\n\
                                    </div>\n\
                                    <div id="partlisttable" class="partlisttable">\n\
                                        <div class="div_header"> 	\n\
                                        <div class="div_h col1">Артикул</div>\n\
                                        <div class="div_h col2">Производитель</div> 	\n\
                                        <div class="div_h col3">Описание</div> 	\n\
                                        <div class="div_h col4">Информ.</div> \n\
                                        <div class="div_h col10">Кол-во на складе</div>	\n\
                                        <div class="div_h col5">Срок доставки</div> 	\n\
                                        <div class="div_h col6">Вероятность отгрузки</div> \n\
                                        <div class="div_h col7">Кол-во</div>	\n\
                                        <div class="div_h col8">Цена</div>  \n\
                                        <!--<div class="div_h col11"></div>-->	\n\
                                        <div class="div_h col9">В корзину</div> \n\
                                    </div>\n\
                                    </div> ');
		
		
		//получаем содержимое #body-container и переносим 
		/*$('#search').attr('style', 'width:100%; position: relative; margin:0 auto;');
		$('#search').find('form').first().attr('style', 'height:150px;');
		$('#searchPart').attr('style', 'position:absolute; top:50px; left:650px');
		$('#partNumber').attr('style', 'margin-left:50px');*/
                $('#search').addClass('search-horizontal');
                $('.search__input-area').addClass('search__input-area_horizontal');
                $('.search__input-area_example').remove();
                
		$('h2.partlist_count').remove();
		$('#partNumber').attr('value', form_part_num);
                form_part_num.replace(' ', '');
		//для добавления номера в форму поиска
		if(form_part_num){
			window.location.hash="#!"+form_part_num;
		}
		
		
		document.getElementById('searchPart').addEventListener('click', getPart);
		
	} 
	// если есть, то пишем в нее
	if(d){
           
		/*Распарсиваем JSON*/
		
		var data=JSON.parse(d); 
                if(!$.isEmptyObject(data)){
                    allparts=$.merge(allparts,data);
                }
		addParts(d, addpage.count);
	} else {
		return false;
	}
	
	if(data.length){
		addpage.all+=data.length;
	}
	
	for(var i=0; i<data.length; i++){
			/**Расчет указателя */
			if(data[i].chance_of_shipment=="н/д"){
				var pointer=-5;
			} else {
				var pointer=data[i].chance_of_shipment;
			}
				var shipment_pointer="style='margin-left:"+pointer+"px'";
			/**Расчет указателя */
			/**/
				var origin_point;
				if(data[i].origin==1)
					origin_point="style='background-color:#F0FFF0'";
				else 
					origin_point='';
			/**/
			insert_string='<div class="div_tr" id="'+data[i].crc+'" '+origin_point+'> 		<div class="div_td col1">'+data[i].artikul+'</div> 		<div class="div_td col2"><p><a href="/brands/brand/'+data[i].bid+'" class="a_brandInfo">'+data[i].brandName+'</a></p><div class="parts_rating rating'+data[i].rating+'"></div></div> 		<div class="div_td col3">'+data[i].description+'</div> 		';
			
			if(data[i].image){
                            insert_string+='<div class="div_td col4"><a class="img_parts" href="#"></a></div> ';
                        }else{
                            insert_string+='<div class="div_td col4"><p class="img_parts_no" ></p></div> ';
                        }
                        
			
			if(data[i].Quantity){
				insert_string+='<div class="div_td col10">'+data[i].Quantity+'</a></div> ';
			} else {
				insert_string+='<div class="div_td col10">н/д</div> ';
			}
                        
                        /*
                         * Кнопка "в блокнот" col11
                         * 
                         */
			insert_string+='<div class="div_td col5">'+data[i].delivery_period+'</div> 		<div class="div_td col6"><div class="shipment"><p>'+data[i].chance_of_shipment+'</p><div class="shipment_progress"></div><div class="shipment_pointer"><div class="pointer" '+shipment_pointer+'></div></div></div></div> 		<div class="div_td col7"><input type="" value="'+data[i].minoffer+'"/ class="inp_count"></div> <div class="div_td col8 pcost">'+data[i].price+'</div> 	<!--<div class="div_td col11"><a href="#" class="a_notebook_add" title="Добавить в закладки"></a></div>-->	<div class="div_td col9"><a href="/addbacket/add/'+data[i].uniqueid+"_"+data[i].provider+'"  class="linkaddbacket">В корзину<span style="display:none">'+data[i].crc+'</span><span style="display:none">'+data[i].provider+'</span><span style="display:none">'+data[i].uniqueid+'</span><span style="display:none">			{"Reference":"'+data[i].Reference+'", "AnalogueCodeAsIs":"'+data[i].AnalogueCodeAsIs+'", "AnalogueManufacturerName":"'+data[i].AnalogueManufacturerName+'", "OfferName":"'+data[i].OfferName+'", "LotBase":"'+data[i].LotBase+'", "LotType":"'+data[i].LotType+'", "PriceListDiscountCode":"'+data[i].PriceListDiscountCode+'", "rpr":"'+data[i].rpr+'", "Quantity":"'+data[i].Quantity+'", "PeriodMin":"'+data[i].PeriodMin+'"}</span></a></div> </div>';
				
			/*Находим последний элемент набора*/	
			var last_elem=$('#partlisttable').children('div').last();
			insertDiv(last_elem, insert_string, data[i].price);
	}
	$(".img_parts").click(function(event){
		event.preventDefault();	
	});
	
        
	$(".a_notebook_add").click(addNewNote);
        
	if(addpage.count==providers){
				
				var str="";
				if(addpage.all){
					str='<h2 class="partlist_count" style="margin:10px 0 0 10px">По вашему запросу найдено '+addpage.all+' позиций</h2>'+getBestParts(allparts);
                                    
                                }
				else
					str='<h2 class="partlist_count" style="margin:10px 0 0 10px">По вашему запросу ничего не найдено</h2>';
		$('#partlist').before(str);
		$('#status').css('display', 'none');
		$('#stop_search').css('display', 'none');
		$(".img_parts").click(function(event){
					image_slider(event);
				});
	}
        $(".linkaddbacket").off('click');
        $(".linkaddbacket").click(function(event){
            event.preventDefault();
            
	});
        $(".linkaddbacket").click(function(event){
            addBacket(event);
	});
        $(".besppartsscroltoposition").off('click');
          
        $(".besppartsscroltoposition").click(function(event){
           var crc=$(this).attr('data-scrollposition');
           $('#'+crc).css('background', '#BEFEBE');
           setTimeout(function(){
                window.location.hash="#!"+loc;
                
            }, 100); 
	});
}
/**
 * при клике по кнопке перейти надо записать содержимое loc в адресную строку
 * */
/*$(document).scroll(function(){
    if(loc){
     window.location.hash="#!"+loc;
 }
})*/

function insertDiv(elem, insert_string, dprice){
	
		/*Узнаем значение его цены*/
		var elem_price=elem.find('.pcost');
			/*сравниваем пришедшую цену с последней ценой в наборе*/
			if(dprice>elem_price.text()){
				
				elem.after(insert_string);
			}else{
				
				if(elem.prev()){
					insertDiv(elem.prev(), insert_string, dprice);
				} else{
					elem.before(insert_string);
				}
				
			}
	
}
/*
Функция доваления товара в корзину. Надо вывести диалоговое окно с выбором количества товара
**/
function addBacket(event){
	addBacket.count++;
	//может быть имеет смысл сделать хранение корзины в куках
	var div=$(event.target).parent().parent();
        
	addBacketButtonAnimation(event.target);
        
        if($(div).attr('id')=='bestParts'){
            var obj=JSON.parse($(div).find('span').eq(0).text());
            var a=JSON.parse($(div).find('span').eq(1).text());
            obj.count=1;
         /*console.log(obj);
         return;*/
         
         //var a=JSON.parse(div.find('div.col9').find('span').eq(3).text());
        }else{
         var obj={'artikul':div.find('div.col1').text(), 'brandName':div.find('div.col2').text(), 'description':div.find('div.col3').text(), 'delivery_period':div.find('div.col5').text(), 'chance_of_shipment':div.find('div.col6').text(), 'price':div.find('div.col8').text(), 'crc':div.find('div.col9').find('span').eq(0).text(), 'provider':div.find('div.col9').find('span').eq(1).text(), 'uniqueid':div.find('div.col9').find('span').eq(2).text(), 'count':div.find('input.inp_count').val()};
            
            var a=JSON.parse(div.find('div.col9').find('span').eq(3).text());
         }
         
        obj.returned=a;
        console.log(obj);
	$.ajax({ type: "POST", url: "/addbacket/add/", data: "parts="+JSON.stringify(obj),  success: checkbacket});
}
/*Обновляет корзину*/
function checkbacket(d){
	
	var data = JSON.parse(d);
	$('#quantity').find('span').text(data[0]);
	$('#summ').text(data[1]);
	if($("div").is("#user-cabinet-backet")){
		$('.user-cabinet-backet_table_1 .user-cabinet-backet_td_2').text(data[0]+' товаров');
		$('.user-cabinet-backet_table_2 .user-cabinet-backet_td_2').text(data[1]+' рублей');
	}
}
/***/
/*если сервер вызвал*/
function getParts(parts){
    
					addpage.count=false;
					addpage.all = false;
                                        var part={value:parts};
                                        var e={};
                                        e.preventDefault=function(){};
                                        getPart(e, part);
    //получаем объект поля ввода номера
   
		
  }
 /**
	Функция будет делать слайдшоу
 */
 function image_slider(event){
                $("html,body").css("overflow","hidden");
		var div=$(event.target).parent().parent();
                var brand=div.children('div.col2').text();
                var part=div.children('div.col1').text();
                $('#modal_window').css('display','block');
		$('#modal_window_container').css('display','block');
		$('#modal_window').click(function(){
				$('#modal_window').css('display','none');
				$('#modal_window_container').css('display','none');
                                $("html,body").css("overflow","auto");
                                return;
		});
		/*var width_modal=$('#modal_window').width();
		var height_modal=$('#modal_window').height();*/
		$('#modal_window_container').click(function(e){
			e.stopPropagation();
		});
		/*var left=width_modal/2-350+'px';
		var top=height_modal/2-250+'px';*/
		$('#modal_window_container').css('left', '0');
		$('#modal_window_container').css('top',  '0');
		$('#modal_window_container').css('right',  '0');
		$('#modal_window_container').css('bottom',  '0');
		$('#modal_window_container').css('margin',  'auto');
		$('#modal_window_container').css('width',  'auto');
		$('#modal_window_container').css('height',  'auto');
                $('#modal_window_container_information').html('<p></p>');
                $('#modal_window_container_next_button').css('display','none');
		$('#modal_window_container_back_button').css('display','none');
                $('#modal_window_container_content').width(false);
                $('#modal_window_container_content').height(false);
                $('#modal_window_container_content').html('');
                $('#modal_window_container_content').css('background', '#fff url(/images/processing.gif) center center no-repeat');
                var brand=brand.replace('/', ' ');
                var brand=brand.replace('(', '');
                var brand=brand.replace(')', '');
                //$.get('/getimage/get/'+brand+'/'+part, function(data){
                $.get('/getimage/getinform/'+part+'/'+brand, function(data){
                     
                    //var img=JSON.parse(data);
                    var img=data;
                    if(img==false){
                        var info={};
                        info.content='<h2 style="text-align: center; line-height: 500px">Нет изображения</h2>';
                        info.header='';
                        /*info.width=img[0].width;
                        info.height=img[0].height;*/
                        info.nav=0;
                        informationPopUp(info);
                        
                       /* $('#modal_window_container_content').css('background', '');
                        $('#modal_window_container_next_button').css('display','none');
                        $('#modal_window_container_back_button').css('display','none');
                        $('#modal_window_container_content').html('<h2 style="text-align: center; line-height: 500px">Нет изображения</h2>');
                         */
                        return;
                    }
                    //if(img.length==1){
                        var info={};
                        //info.content='<img src="data:'+img[0].mime+';base64,'+img[0].base_64+'"  style="vertical-align: middle;"/>';
                        info.content=img;
                        info.header='';
                        info.width=img[0].width;
                        info.height=img[0].height;
                        info.nav=0;
                        informationPopUp(info);

                    /*} else {
                            var count=0;
                            var this_img=count+1;
                           /**
                            * Вызываем функцию вывода информации
                            * *
                            var info={};
                            info.content='<img src="data:'+img[count].mime+';base64,'+img[count].base_64+'"  style="vertical-align: middle;"/>';
                            info.header=this_img+'/'+img.length;
                            info.width=img[count].width;
                            info.height=img[count].height;
                            info.nav=1;
                            informationPopUp(info);
                            $('#modal_window_container_next_button').click(function(){
                                    count++;
                                    if(count==img.length){
                                            count=0;
                                    }
                                    var this_img=count+1;
                                    var info={};
                                    info.content='<img src="data:'+img[count].mime+';base64,'+img[count].base_64+'"  style="vertical-align: middle;"/>';
                                    info.header=this_img+'/'+img.length;
                                    info.width=img[count].width;
                                    info.height=img[count].height;
                                    info.nav=1;
                                    informationPopUp(info);                            });
                            $('#modal_window_container_back_button').click(function(){
                                    if(count==0){
                                            count=img.length;
                                    }
                                    count--;
                                    var this_img=count+1;
                                    var info={};
                                    info.content='<img src="data:'+img[count].mime+';base64,'+img[count].base_64+'"  style="vertical-align: middle;"/>';
                                    info.header=this_img+'/'+img.length;
                                    info.width=img[count].width;
                                    info.height=img[count].height;
                                    info.nav=1;
                                    informationPopUp(info);                              });
                    }*/
                });
		
		
		
		
 }
 /**
  * Функция для работы со всплывающим окном
  *  
  */
 $("*").scroll(function(){
      $('div.part_character_div_table_container').getNiceScroll().resize();
     $('div.part_info_applicability').getNiceScroll().resize();
 });
        
 function informationPopUp(info){
    $('#modal_window_container_content').css('background', ''); //убираем анимацию загрузки
    $('#modal_window_container_content').html('<div id="modal_window_container_information"><h2>'+info.header+'</h2></div><div class="modal_window_container_information_content">'+info.content+'</div><div class="close_popup_modal_window"><a href="#"></a></div>'); //здесь иноформация
    var contentwidth=$('div.part_info').width();
    var contentheight=$('div.part_info').height();
    //$('div.part_character_div_table_container').niceScroll({cursorwidth:'10px', zindex :100});
    //$('div.part_info_applicability').niceScroll({cursorwidth:'10px', zindex :100});
    $('#modal_window_container_content').width(contentwidth+50); //ширину контента
    $('#modal_window_container_content').height(contentheight+50);//высоту контента
    
    $('a.part_character_image_zoom').click(imageZoom);
    $('a.other_images').click(function(event){
        event.preventDefault();
        $('div.part_character_image_div_div img').attr('src', $(this).find('img').attr('src'));
    });
    $('div.close_popup_modal_window a').click(function(event){
                    event.preventDefault();
                   $('#modal_window').css('display','none');
                     //$('#modal_window').fadeOut()
                    //$('#modal_window_container').fadeOut()
                    $('#modal_window_container').css('display','none');
                    $("html,body").css("overflow","auto");
    });
    if(info.nav==1){
        //если нужна навигация
        $('#modal_window_container_next_button').css('display','block');
        $('#modal_window_container_back_button').css('display','block');
    }
 }
 $('div.imagezoom img').ready(function(){
    var contentwidth=$('div.part_info').width();
    var contentheight=$('div.part_info').height();
    //$('div.part_character_div_table_container').niceScroll({cursorwidth:'10px', zindex :100});
    //$('div.part_info_applicability').niceScroll({cursorwidth:'10px', zindex :100});
    $('#modal_window_container_content').width(contentwidth+50); //ширину контента
    $('#modal_window_container_content').height(contentheight+50);//высоту контента
     
 })
 function imageZoom(event){
        event.preventDefault();
        //текущая картинка у нас будет
        $('#modal_window_container_content').append('<div class="imagezoom"><img src="'+$('div.part_character_image_div_div img').attr('src')+'"/><a class="part_character_image_zoom_out" href="#"></a></div>');
        var width=$('div.imagezoom img').width();
        var height=$('div.imagezoom img').height();
        $('div.imagezoom').css('width',width+'px');
        $('div.imagezoom').css('height',height+'px');
        $('div.imagezoom').css('display','none');
        $('div.imagezoom').fadeIn();
        $('a.part_character_image_zoom_out').click(function(event){
            event.preventDefault();
            $('div.imagezoom').fadeOut(function(){
                $('div.imagezoom').remove();
            }); 
            
        });
    }
 /**Анимация кнопки добавления в корзину**/
 function addBacketButtonAnimation(elem){
	
	 $(elem).animate({'margin-top': '10px'}, 200);
	$(elem).animate({'margin-top': '0'}, 200);
	
 }
 
 //Функция выводит\скрывает форму ввода логина
 function login_form_display(event){
	$('#login_popup').toggle(200);
	event.preventDefault();
 }
 
 /**
  * Скрываем контейнер с рисунком
  * 
  * 
  * */
 
 /*$(document).mouseup(function (e) {
    var container = $("div.imagezoom"); //тут селектор
    if (container.has(e.target).length === 0){
        $('div.imagezoom').fadeOut(function(){  // а тут действие
                $('div.imagezoom').remove();    //
        });                                     //
    }
});//Для чего я всю эту херню делал а? Никто не знает?
 $(document).mouseup(function (e) {
    var container = $("#modal_window_container"); //тут селектор
    if (container.has(e.target).length === 0){
        $('#modal_window').css('display','none');
        $('#modal_window_container').css('display','none');
        $("html,body").css("overflow","auto");                                   //
    }
});*/





 $(document).click(function(event){
      if( $(event.target).closest("#login_popup").length ) 
        return;
	if( $(event.target).closest("a[onclick=login_form_display(event)]").length ) 
        return;
      $("#login_popup").hide(200);
      event.stopPropagation();
	  
      
          
        if( $(event.target).closest("#recall_popup").length ) 
        return;
	if( $(event.target).closest("a[onclick=recall_form_display(event)]").length ) 
        return;
      $("#recall_popup").hide(200);
      event.stopPropagation();
    });

//Функция выводит\скрывает форму ввода телефона для обратной связи
 function recall_form_display(event){
	$('#recall_popup').toggle(200);
	event.preventDefault();
}

/**
функция отправки recall сообщения на сервер
*/
function sendRecall(event){
	event.preventDefault();
	//получаем данные из полей ввода
	var name=$('#recall_popup_block input[type=text]').val();
	var phone=$('#recall_popup_block input[type=tel]').val();
	var msg=$('#recall_popup_block textarea').val();
        
	if(!phone){
		$('#recall_popup_block input[type=tel]').css('border', '1px solid red');
		$('#recall_popup_block input[type=tel]').attr('placeholder', 'Вы не ввели номер телефона!');
		return false;
	}else{
            
		$.post('/user/recall/', {'name':name, 'phone':phone, 'msg':msg}, function(d){
			if(d==1){
				$('#recall_popup_block').html('<p style="margin-top:20px; color:white; text-align:center">Ваша заявка принята. Оператор свяжется с вами в ближайшее время</p><br><input type="submit" value="Закрыть" onclick="recall_hide()"/>');
			}else{
				$('#recall_popup_block').html('<p>Ой, что то пошло не так :( Приносим свои изменения</p>');
			}
		});
	}
}
function recall_hide(){
	$("#recall_popup").hide(200);
}
/**
 * Фукция добавляем новую заметку
 * @returns void
 */
function addNewNote(event){
   event.preventDefault();
   addBacketButtonAnimation(this);
   var obj=new Object();
   obj.artikul=$(this).parent().parent().find('.col1').text();
   obj.brand=$(this).parent().parent().find('.col2').text();
   $.ajax({ type: "POST", url: "/user/notes/add", data: "note="+JSON.stringify(obj),  success: checkNote});
}
function checkNote(){
   /*$.ajax({ type: "POST", url: "/user/notes/check",  success: function(data){
           if(data>=1){
                if(!$('div').is('#notebook')){
                    $('body').append('<div id="notebook"><a href="/user/notepage"><p></p></a></div>');
                }
                $('#notebook p').text(data);
           }
   }});*/
}

function sortList(partNumb, sort_val){
    var rows=$.makeArray($('div.div_tr')); //тут у нас будут все поля таблицы
    if(!sort_val){
      var e= rows.sort(function(a, b){
           if(partNumb==$(a).find('div.col1').text()){
               return 1000; 
               //console.log($(a).find('div.col1').text());
           }else{
               return -1;
           }
       });
    }
    if(e==rows){
        return false;
    }
    return e;
}
/**
 * фукция выводит окно с кратким описанием метода
 * @param {int} id_brand
 * @returns {void}


function getBrandInformation(id_brand){
    $.get('/brands/brand/'+id_brand+'/getinfo', function(){
        
    })
} */



/**
 * Функция выбирает наилучшее предложение по цене, по оригинальности, по срокам доставки, самое лучшее предложение
 * @param {array} allparts Массив всех найденных позиций
 */

function getBestParts(allparts){
    var bestOrigin={};
    var bestPrice={};
    var bestShipment={};
    var bestTheBest={};
    for(i=0; i<allparts.length; i++){
       var d=allparts[i]; //сократим
       
        //если d содержит признак оригинала и цена меньше 
        /*##############Ищем оригнал с наименьшей ценой#############*/
        if(d.origin==1){
            if(('price' in bestOrigin)){
                if(bestOrigin.price>d.price){
                    bestOrigin=d;
                }
            }else{
                bestOrigin=d;
            }
        }
        /*----------------------------------------------------------*/
        /*#############Ищем наилучшее предложение по цене###############*/
        
        if('price' in bestPrice){
             if(bestPrice.price>d.price){
                    bestPrice=d;
            }
        }else{
            bestPrice=d;
        }
        /*----------------------------------------------------------*/
        /*#############Ищем наилучшее предложение по доставке###############*/
        if('PeriodMin' in bestShipment){
            var a=bestShipment.PeriodMin;
            var b=d.PeriodMin;
            if(a>b){
                bestShipment=d;
            }else if(a==b && bestShipment.chance_of_shipment<d.chance_of_shipment){
                bestShipment=d;
            }else if(a==b && bestShipment.chance_of_shipment>=d.chance_of_shipment && bestShipment.price>d.price){
                 bestShipment=d;
            }
        }else{
            bestShipment=d;
        }
        /*----------------------------------------------------------*/
        /*#############Ищем самое наилучшее предложение###############*/
        if('delivery_period' in bestTheBest){
            var a=bestShipment.PeriodMin;
            var b=d.PeriodMin;
            if(bestTheBest.origin==1 && bestTheBest.price>d.price && bestTheBest.chance_of_shipment>d.chance_of_shipment && a>b && bestTheBest.rating<=d.rating){
                bestTheBest=d;
            }else if(bestTheBest.price>d.price && bestTheBest.chance_of_shipment<=d.chance_of_shipment && a>=b  && bestTheBest.rating<=d.rating){
                bestTheBest=d;
            }else if(bestTheBest.price>d.price && bestTheBest.chance_of_shipment<=d.chance_of_shipment  && bestTheBest.rating<=d.rating){
                bestTheBest=d;
            }else if(bestTheBest.price>d.price  && bestTheBest.rating<=d.rating){
                bestTheBest=d;
            }
        }else{
            bestTheBest=d;
        }
    }
    var html='<div id="bestParts">';
    //старая система вывода
    /*
    if(!$.isEmptyObject(bestOrigin)){
		var data=JSON.stringify(bestOrigin);
        html+='<div class="bestParts bestOrigin"><h3>Оригинал</h3> <div class="bestParts-brand-partNum">'+bestOrigin.brandName+': '+bestOrigin.artikul+'</div> <div class="parts_rating rating'+bestOrigin.rating+'"></div> <div class="bestParts-description"><p>'+bestOrigin.description+'</p> </div> <div class="bestParts-delivery-period"><p> Срок доставки: '+bestOrigin.delivery_period+' дней</p></div> <div class="bestParts-chance-of-shipment"><p> Вероятность отгрузки: '+bestOrigin.chance_of_shipment+'%</p></div> <div class="bestParts-price"><h4>'+bestOrigin.price+' руб</h4> </div> <a href="/addbacket/add/'+bestOrigin.uniqueid+"_"+bestOrigin.provider+'"  class="linkaddbacket">В корзину<span style="display:none">'+data+'</span><span style="display:none">			{"Reference":"'+bestOrigin.Reference+'", "AnalogueCodeAsIs":"'+bestOrigin.AnalogueCodeAsIs+'", "AnalogueManufacturerName":"'+bestOrigin.AnalogueManufacturerName+'", "OfferName":"'+bestOrigin.OfferName+'", "LotBase":"'+bestOrigin.LotBase+'", "LotType":"'+bestOrigin.LotType+'", "PriceListDiscountCode":"'+bestOrigin.PriceListDiscountCode+'", "rpr":"'+bestOrigin.rpr+'", "Quantity":"'+bestOrigin.Quantity+'", "PeriodMin":"'+bestOrigin.PeriodMin+'"}</span></a></div>';
    }
    if(!$.isEmptyObject(bestPrice)){
		var data=JSON.stringify(bestPrice);
        html+= '<div class="bestParts bestPrice"><h3>Самая низкая цена</h3><div class="bestParts-brand-partNum">'+bestPrice.brandName+': '+bestPrice.artikul+'</div> <div class="parts_rating rating'+bestPrice.rating+'"></div> <div class="bestParts-description"><p>'+bestPrice.description+'</p> </div> <div class="bestParts-delivery-period"><p> Срок доставки: '+bestPrice.delivery_period+' дней</p></div> <div class="bestParts-chance-of-shipment"><p> Вероятность отгрузки: '+bestPrice.chance_of_shipment+'%</p></div> <div class="bestParts-price"><h4>'+bestPrice.price+' руб</h4> </div> <a href="/addbacket/add/'+bestPrice.uniqueid+"_"+bestPrice.provider+'"  class="linkaddbacket">В корзину<span style="display:none">'+data+'</span><span style="display:none">			{"Reference":"'+bestPrice.Reference+'", "AnalogueCodeAsIs":"'+bestPrice.AnalogueCodeAsIs+'", "AnalogueManufacturerName":"'+bestPrice.AnalogueManufacturerName+'", "OfferName":"'+bestPrice.OfferName+'", "LotBase":"'+bestPrice.LotBase+'", "LotType":"'+bestPrice.LotType+'", "PriceListDiscountCode":"'+bestPrice.PriceListDiscountCode+'", "rpr":"'+bestPrice.rpr+'", "Quantity":"'+bestPrice.Quantity+'", "PeriodMin":"'+bestPrice.PeriodMin+'"}</span></a></div>';
    }
    if(!$.isEmptyObject(bestShipment)){
		var data=JSON.stringify(bestShipment);
        html+= '<div class="bestParts bestShipment"><h3>Самая быстрая доставка</h3><div class="bestParts-brand-partNum">'+bestShipment.brandName+': '+bestShipment.artikul+'</div> <div class="parts_rating rating'+bestShipment.rating+'"></div> <div class="bestParts-description"><p>'+bestShipment.description+'</p> </div> <div class="bestParts-delivery-period"><p> Срок доставки: '+bestShipment.delivery_period+' дней</p></div> <div class="bestParts-chance-of-shipment"><p> Вероятность отгрузки: '+bestShipment.chance_of_shipment+'%</p></div> <div class="bestParts-price"><h4>'+bestShipment.price+' руб</h4> </div><a href="/addbacket/add/'+bestShipment.uniqueid+"_"+bestShipment.provider+'"  class="linkaddbacket">В корзину<span style="display:none">'+data+'</span><span style="display:none">			{"Reference":"'+bestShipment.Reference+'", "AnalogueCodeAsIs":"'+bestShipment.AnalogueCodeAsIs+'", "AnalogueManufacturerName":"'+bestShipment.AnalogueManufacturerName+'", "OfferName":"'+bestShipment.OfferName+'", "LotBase":"'+bestShipment.LotBase+'", "LotType":"'+bestShipment.LotType+'", "PriceListDiscountCode":"'+bestShipment.PriceListDiscountCode+'", "rpr":"'+bestShipment.rpr+'", "Quantity":"'+bestShipment.Quantity+'", "PeriodMin":"'+bestShipment.PeriodMin+'"}</span></a></div>';
    }
    if(!$.isEmptyObject(bestTheBest)){
		var data=JSON.stringify(bestTheBest);
        html+='<div class="bestParts bestTheBest"><h3>Самое лучшее предложение</h3><div class="bestParts-brand-partNum">'+bestTheBest.brandName+': '+bestTheBest.artikul+'</div> <div class="parts_rating rating'+bestTheBest.rating+'"></div> <div class="bestParts-description"><p>'+bestTheBest.description+'</p> </div> <div class="bestParts-delivery-period"><p> Срок доставки: '+bestTheBest.delivery_period+' дней</p></div> <div class="bestParts-chance-of-shipment"><p> Вероятность отгрузки: '+bestTheBest.chance_of_shipment+'%</p></div> <div class="bestParts-price"><h4>'+bestTheBest.price+' руб</h4> </div><a href="/addbacket/add/'+bestTheBest.uniqueid+"_"+bestTheBest.provider+'"  class="linkaddbacket">В корзину<span style="display:none">'+data+'</span><span style="display:none">			{"Reference":"'+bestTheBest.Reference+'", "AnalogueCodeAsIs":"'+bestTheBest.AnalogueCodeAsIs+'", "AnalogueManufacturerName":"'+bestTheBest.AnalogueManufacturerName+'", "OfferName":"'+bestTheBest.OfferName+'", "LotBase":"'+bestTheBest.LotBase+'", "LotType":"'+bestTheBest.LotType+'", "PriceListDiscountCode":"'+bestTheBest.PriceListDiscountCode+'", "rpr":"'+bestTheBest.rpr+'", "Quantity":"'+bestTheBest.Quantity+'", "PeriodMin":"'+bestTheBest.PeriodMin+'"}</span></a></div>';
    }*/
    
    if(!$.isEmptyObject(bestOrigin)){
		var data=JSON.stringify(bestOrigin);
        html+='<div class="bestParts bestOrigin"><h3>Оригинал</h3> <div class="bestParts-brand-partNum">'+bestOrigin.brandName+': '+bestOrigin.artikul+'</div> <div class="parts_rating rating'+bestOrigin.rating+'"></div> <div class="bestParts-description"><p>'+bestOrigin.description+'</p> </div> <div class="bestParts-delivery-period"><p> Срок доставки: '+bestOrigin.delivery_period+' дней</p></div> <div class="bestParts-chance-of-shipment"><p> Вероятность отгрузки: '+bestOrigin.chance_of_shipment+'%</p></div> <div class="bestParts-price"><h4>'+bestOrigin.price+' руб</h4> </div> <a href="#'+bestOrigin.crc+'"  class="besppartsscroltoposition linkaddbacket" data-scrollposition="'+bestOrigin.crc+'">Перейти</a></div>';
    }
    if(!$.isEmptyObject(bestPrice)){
		var data=JSON.stringify(bestPrice);
        html+= '<div class="bestParts bestPrice"><h3>Самая низкая цена</h3><div class="bestParts-brand-partNum">'+bestPrice.brandName+': '+bestPrice.artikul+'</div> <div class="parts_rating rating'+bestPrice.rating+'"></div> <div class="bestParts-description"><p>'+bestPrice.description+'</p> </div> <div class="bestParts-delivery-period"><p> Срок доставки: '+bestPrice.delivery_period+' дней</p></div> <div class="bestParts-chance-of-shipment"><p> Вероятность отгрузки: '+bestPrice.chance_of_shipment+'%</p></div> <div class="bestParts-price"><h4>'+bestPrice.price+' руб</h4> </div> <a href="#'+bestPrice.crc+'"  class="besppartsscroltoposition linkaddbacket" data-scrollposition="'+bestPrice.crc+'">Перейти</a></div>';
    }
    if(!$.isEmptyObject(bestShipment)){
		var data=JSON.stringify(bestShipment);
        html+= '<div class="bestParts bestShipment"><h3>Самая быстрая доставка</h3><div class="bestParts-brand-partNum">'+bestShipment.brandName+': '+bestShipment.artikul+'</div> <div class="parts_rating rating'+bestShipment.rating+'"></div> <div class="bestParts-description"><p>'+bestShipment.description+'</p> </div> <div class="bestParts-delivery-period"><p> Срок доставки: '+bestShipment.delivery_period+' дней</p></div> <div class="bestParts-chance-of-shipment"><p> Вероятность отгрузки: '+bestShipment.chance_of_shipment+'%</p></div> <div class="bestParts-price"><h4>'+bestShipment.price+' руб</h4> </div><a href="#'+bestShipment.crc+'"  class="besppartsscroltoposition linkaddbacket" data-scrollposition="'+bestShipment.crc+'">Перейти</a></div>';
    }
    if(!$.isEmptyObject(bestTheBest)){
		var data=JSON.stringify(bestTheBest);
        html+='<div class="bestParts bestTheBest"><h3>Самое лучшее предложение</h3><div class="bestParts-brand-partNum">'+bestTheBest.brandName+': '+bestTheBest.artikul+'</div> <div class="parts_rating rating'+bestTheBest.rating+'"></div> <div class="bestParts-description"><p>'+bestTheBest.description+'</p> </div> <div class="bestParts-delivery-period"><p> Срок доставки: '+bestTheBest.delivery_period+' дней</p></div> <div class="bestParts-chance-of-shipment"><p> Вероятность отгрузки: '+bestTheBest.chance_of_shipment+'%</p></div> <div class="bestParts-price"><h4>'+bestTheBest.price+' руб</h4> </div><a href="#'+bestTheBest.crc+'"  class="besppartsscroltoposition linkaddbacket" data-scrollposition="'+bestTheBest.crc+'">Перейти</a></div>';
    }
    html+='</div>';
    
    return html;
    //return {bestOrigin:bestOrigin, bestPrice:bestPrice, bestShipment:bestShipment, bestTheBest:bestTheBest};
}