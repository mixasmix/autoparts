<?php

/**
 * @author MDTreloni
 * @email mixasmix@mail.ru 
 * @copyright 2015
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Json extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->load->helper('getpage');
	}
	public function allautoparts($partNumber){
		//примем сюда номер запчасти а не массив спарекса
				$xmlstring=getPage('http://allautoparts.ru/asp-x/Search/Search.ashx?ajax=search1&producerid=-1&mode=0&cross=1&directionid=-1&periodmin=-1&periodmax=-1&clientid=500&currency=rub&key='.$partNumber.'&_=1420120601465', '', 'allautoparts');
				$xmlstring=str_replace('<?xml version="1.0" encoding="utf-16"?>','',$xmlstring);
				$result=json_decode($xmlstring); //массив результатов
				//крутим массив, проверяем есть ли совпадения по имени производителя
				if(!empty($result)){
					foreach($result as $r){
						$productId_allautoparts[]=$r->productId;
					}
				}
		//теперь массив с номерами надо скормить allautoparts на search2
		$arr_result=array();
		foreach($productId_allautoparts as $pid){
			$xmlstring=getPage('http://allautoparts.ru/asp-x/Search/Search.ashx?ajax=search2&producerid=-1&mode=0&cross=1&directionid=-1&periodmin=-1&periodmax=-1&clientid=500&currency=rub&productid='.$pid.'&stocksonly=0&sort=cost_asc&_=1420120601469', '', 'allautoparts');
			$xmlstring=str_replace('<?xml version="1.0" encoding="utf-16"?>','',$xmlstring);
			//если товар не найден возвращается строка
			if(substr_count($xmlstring, 'По вашему запросу предложения не найдены.')){continue;}
			//тут у нас должна быть какая то функция для приведения html кода к массиву
			//echo $xmlstring.'<hr><hr><hr><hr>';
			$arr=$this->htmlToArr($xmlstring);
			
			//var_dump($arr);
			$arr_result=array_merge($arr_result, $arr);		
		}
		
		$unique_arr=array();
		foreach($arr_result as $arr){
			$unique_arr[$arr['brandName'].'_'.$arr['artikul'].'_'.$arr['delivery_period'].'_'.$arr['price']]=$arr;
		}
		return $unique_arr;
		
	}
	public function htmlToArr($xmlstring){
	
		//создаем дом
		$dom= new DOMDocument('1.0' ,'utf-8');
		$dom->loadXML('<?xml version="1.0" encoding="utf-8"?><RootElement>'.$xmlstring.'</RootElement>');
		//получаем все элементы tr
		$tbody=$dom->getElementsByTagName('tbody');
		$result_array=array();
		if($tbody->length==4){
			if($tbody->item(1)){
				$orig_tr=$tbody->item(1)->getElementsByTagName('tr'); //тут оригинальные номера
				foreach($orig_tr as $item){
					$a=array();
					//начинаем лазить по дереву и собирать информацию
					$a['uniqueid']=$item->getAttribute('ref');
					$brandName_td=$item->firstChild;
					$a['brandName']=$brandName_td->nodeValue;
					$artikul_td=$brandName_td->nextSibling;
					$a['artikul']=$artikul_td->firstChild->nodeValue;
					$desc_td=$artikul_td->nextSibling;
					$a['description']=$desc_td->firstChild->nodeValue;
					$deliver_per=$desc_td->nextSibling;
					$a['delivery_period']=$deliver_per->nodeValue;
					$price_td=$deliver_per->nextSibling;
					$a['price']=str_replace('%C2%A','', urlencode($price_td->firstChild->nodeValue))*1;
					$a['last_upd']=$price_td->getAttribute('upd');
					$a['origin']=1;
					$a['provider']='906044c6cb4224c69ba36dc736606b4d';//здесь строка-идентификатор поставщика
					$result_array[]=$a;
					//echo '<hr>'.mb_detect_encoding($a['description']).'<hr>';
				}
			}
			if($tbody->item(3)){
				$repl_tr=$tbody->item(3)->getElementsByTagName('tr'); //тут неоригинальные номера
				foreach($repl_tr as $item){
					$a=array();
					//начинаем лазить по дереву и собирать информацию
					$a['uniqueid']=$item->getAttribute('ref');
					$brandName_td=$item->firstChild;
					$a['brandName']=$brandName_td->nodeValue;
					$artikul_td=$brandName_td->nextSibling;
					$a['artikul']=$artikul_td->firstChild->nodeValue;
					$desc_td=$artikul_td->nextSibling;
					$a['description']=$desc_td->firstChild->nodeValue;
					$deliver_per=$desc_td->nextSibling;
					$a['delivery_period']=$deliver_per->nodeValue;
					$price_td=$deliver_per->nextSibling;
					$a['price']=str_replace('%C2%A','', urlencode($price_td->firstChild->nodeValue))*1;
					$a['last_upd']=$price_td->getAttribute('upd');
					$a['origin']=0;
					$a['provider']='906044c6cb4224c69ba36dc736606b4d';//здесь строка-идентификатор поставщика
					$result_array[]=$a;
				}
			}
		}	
		return $result_array;
	}
	public function getParts($oN){
		$arr_sparex=array();
		//getPage('http://sparex.ru/user/enter/', 'login=mixasmix&password=3444067');
		################################################################################
		/**
		* Что нам надо сделать:
		* Запросить номера у спарекса
		* Сделать второй массив спарекса с ключами в формате номердетали_производитель
		* Оставить только уникальные ключи
		* В цикле перебрать номера
		* По каждому номеру насдо сделать запрос к allautiparts
		* Сохранить значения allautoparts в массив
		*
		*
		*/
		################################################################################
		for($i=0; $i<=5; $i++){
			$url="http://sparex.ru/add/index.php?guid=&article=".$oN."&scladname=sklad".$i."&replace=1";
			$content=getPage($url, '', 'sparex');
			//$arr[0]= getPage('http://sparex.ru/search/inbasketnew/', 'gloalid=44F3F4CC1D216712E92A50519DDF20EF&count=1');
			if($content!='[]'){
				$arr_sparex=array_merge($arr_sparex, json_decode($content));
			}
		}
		//надо привести массив спарекса к общему виду 
		$result_array=array();
		foreach ($arr_sparex as $item){
			//создаем пустой массив куда будем все складывать
			$a=array();
			//Переносим свойства объекта в данные массива
			$a['uniqueid']=$item->globalid;
			$a['brandName']=$item->mn;
			$a['artikul']=$item->dn;
			$a['description']=$item->dnr;
			$a['delivery_period']=$item->add.'/'.$item->dig;
			$a['chance_of_shipment']=$item->ddp;
			/**
			*	ddp - вероятность отгрузки, в процентах или н/д
			*	rp - розничная цена я так понимаю
			* 	lp - неизвестный коэффициент
			*	add - ожидаемый срок доставки
			*	dig - гарантируемый срок отгрузки
			*
			*/
			$a['price']=(float)$item->rp*$item->lq;
			$a['last_upd']=date('Y-m-d H:i',time());
			if($item->pg=='Original')
				$a['origin']=1; //если оригинал
			elseif($item->pg=='ReplacementNonOriginal')
				$a['origin']=0;	//если неоригинал
			elseif($item->pg=='ReplacementOriginal')
				$a['origin']=2; //если аналог
			else
				$a['origin']=false; //если неопределено
			$a['provider']='f781d2dc99fbaa3136da525b2977992e';//здесь строка-идентификатор поставщика
			$result_array[]=$a;
				
		}
		var_dump( $result_array);
	}
}
?>