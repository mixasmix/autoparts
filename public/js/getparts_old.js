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
	document.getElementById('searchPart').addEventListener('click', getPart);
	

}
function addParts(dat, n){
	$.ajax({ type: "POST", url: "/parts/addparts/"+n, data: "parts="+dat,  success: function(){}});
}
function getPart(event){
	event.preventDefault();
		addpage.count=false;
		addpage.all = false;
	//получаем объект поля ввода номера
	var part=document.getElementById('partNumber');
	//если value не равно пустой строке то делаем запрос
	if(part.value){
		for(var i=1; i<=11; i++){
			$.ajax({url: "/parts/getparts/"+part.value+"/"+i+"/",  success: addpage});
		}	
			//$.get('/parts/getparts/2888',  '', addpage(data));
		document.getElementById('search').innerHTML="Идет поиск...";
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
	if(!addpage.count){
		addpage.count = 0;
		document.getElementById('body-container').innerHTML='';
		var content_text=document.getElementById('body-container');
		var table=document.createElement('table');
		var style=document.createAttribute('style');
		style.value="border: 4px double #333;  border-collapse: separate; width: 100%;  border-spacing: 7px 11px;";
		table.setAttributeNode(style);
		content_text.appendChild(table);
		addpage.table=table;
		addpage.content_text=content_text;
	
	}
	if(!addpage.all){
		addpage.all = 0;
	}
	addpage.count++;
	
	/*alert(data);*/
	//получаем div body-container
	var content_text=document.getElementById('body-container');
	var table=document.createElement('table');
	var style=document.createAttribute('style');
	style.value="border: 4px double #333;  border-collapse: separate; width: 100%;  border-spacing: 7px 11px;";
	table.setAttributeNode(style);
	content_text.appendChild(table);
	if(d){
		/*alert(d);*/
		var data=JSON.parse(d);
		addParts(d, addpage.count);
	} else {
		return false;
	}
	
	if(data.length){
		addpage.all+=data.length;
	}
	//alert(data.length);
	for(var i=0; i<data.length; i++){
		var tr=document.createElement('tr');
		var td1=document.createElement('td');
		var td2=document.createElement('td');
		var td3=document.createElement('td');
		var td4=document.createElement('td');
		var td5=document.createElement('td');
		var td6=document.createElement('td');
		var td7=document.createElement('td');
		var td8=document.createElement('td');
		var a=document.createElement('a');
		var text1=document.createTextNode(data[i].brandName);
		var text2=document.createTextNode(data[i].artikul);
		var text3=document.createTextNode(data[i].description);
		var text4=document.createTextNode(data[i].delivery_period);
		if(data[i].images){
			/*alert(data[i].images);*/
			var text5=document.createTextNode(data[i].images);
		} else {
			var text5=document.createTextNode(data[i].chance_of_shipment);
		}
		var text6=document.createTextNode(data[i].chance_of_shipment);
		var text7=document.createTextNode(data[i].price);
		var text8=document.createTextNode('В корзину');
		
		a.appendChild(text8);
		var link=document.createAttribute('href');
		link.value='#';
		var onclick=document.createAttribute('onclick');
		onclick.value='addBacket("'+data[i].uniqueid+"_"+data[i].provider+'")';
		a.setAttributeNode(link);
		a.setAttributeNode(onclick);
		
		td1.appendChild(text1);
		td2.appendChild(text2);
		td3.appendChild(text3);
		td4.appendChild(text4);
		td5.appendChild(text5);
		td6.appendChild(text6);
		td7.appendChild(text7);
		td8.appendChild(a);
		
		tr.appendChild(td1);
		tr.appendChild(td2);
		tr.appendChild(td3);
		tr.appendChild(td4);
		tr.appendChild(td5);
		tr.appendChild(td6);
		tr.appendChild(td7);
		tr.appendChild(td8);
		/*
		tr.appendChild(document.createElement('td').appendChild(document.createTextNode(data[i].sern)));
		tr.appendChild(document.createElement('td').appendChild(document.createTextNode(data[i].mn)));
		tr.appendChild(document.createElement('td').appendChild(document.createTextNode(data[i].dnr)));
		tr.appendChild(document.createElement('td').appendChild(document.createTextNode(data[i].rpr)));*/
		addpage.table.appendChild(tr);
		//break;
		
	}
	if(addpage.count==11){
		var cont=document.getElementById('content_text').innerHTML;
		document.getElementById('content_text').innerHTML='<h3>Найдено позиций: '+addpage.all+'</h3><br>'+cont;
	
	}
	//.innerHTML="<pre>"+data+"</pre>";
}
function addBacket(arg){
	alert(arg);
}
/*если сервер вызвал*/
function getParts(parts){
					addpage.count=false;
					addpage.all = false;
				//получаем объект поля ввода номера
					for(var i=1; i<=11; i++){
						$.ajax({url: "/parts/getparts/"+parts+"/"+i+"/",  success: addpage});
					}	
						//$.get("/parts/getparts/2888",  "", addpage(data));
		document.getElementById("content_text").innerHTML="Идет поиск...";
  }