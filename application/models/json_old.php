<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Json extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->load->helper('getpage');
	}
	public function allautoparts($arr_sparex){
		/*echo getPage('http://allautoparts.ru/asp-x/Search/Search.ashx?ajax=search1&producerid=-1&mode=0&cross=1&directionid=-1&periodmin=-1&periodmax=-1&clientid=500&currency=rub&key=312330&_=1420120601465');*/
		/*$xmlstring=getPage('http://allautoparts.ru/asp-x/Search/Search.ashx?ajax=search2&producerid=-1&mode=0&cross=1&directionid=-1&periodmin=-1&periodmax=-1&clientid=500&currency=rub&productid=52989463&stocksonly=0&sort=cost_asc&_=1420120601469');
		$xmlstring=str_replace('<?xml version="1.0" encoding="utf-16"?>','',$xmlstring);
		echo $xmlstring;*/
		################################################################################
		//Вот у нас массив sparex. Перебираем в цикле его
		// Создаем массив куда будем писать с новыми ключами
		$arr_sparex_unique=array();
		foreach($arr_sparex as $a){
			//записываем в массив 
			if(!empty($a->dn)){
				$arr_sparex_unique[$a->dn.'_'.$a->mn]=$a;
			}
		}
		//Теперь крутим массив с уникальными номерами, делаем запрос на allautoparts
		//делаем массив со всеми найденными productID
		$productId_allautoparts=array();
		foreach($arr_sparex_unique as $a){
			//первый запрос на получение деталей по номерам
			//Проверим $a->dn на пустоту
			if(!empty($a->dn)){
				$xmlstring=getPage('http://allautoparts.ru/asp-x/Search/Search.ashx?ajax=search1&producerid=-1&mode=0&cross=1&directionid=-1&periodmin=-1&periodmax=-1&clientid=500&currency=rub&key='.$a->dn.'&_=1420120601465', '', 'allautoparts');
				$xmlstring=str_replace('<?xml version="1.0" encoding="utf-16"?>','',$xmlstring);
				$result=json_decode($xmlstring); //массив результатов
				//крутим массив, проверяем есть ли совпадения по имени производителя
				if(!empty($result)){
					foreach($result as $r){
						$productId_allautoparts[]=$r->productId;
					}
				}
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
		echo count($arr_result);echo '<hr>';
		var_dump($unique_arr); echo '<hr>';
		
	}
	public function htmlToArr($xmlstring){
	
		//создаем дом
		//file_put_contents('temp', $xmlstring);
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
				$a['origin']=2;
				$a['provider']='906044c6cb4224c69ba36dc736606b4d';//здесь строка-идентификатор поставщика
				$result_array[]=$a;
			}
		}
		}
		

	
		return $result_array;
	}
	public function getParts($oN){
	
		/*$xmlstring=getPage('http://allautoparts.ru/asp-x/Search/Search.ashx?ajax=search2&producerid=-1&mode=0&cross=1&directionid=-1&periodmin=-1&periodmax=-1&clientid=500&currency=rub&productid=52989463&stocksonly=0&sort=cost_asc&_=1420120601469', '', 'allautoparts');
		var_dump($this->htmlToArr($xmlstring)); exit;*/
		//$url='http://allautoparts.ru/closed/auth/actions.asp'; getPage($url, 'action=auth&back=%2Fclosed%2Fdefault.asp&fp_id=1&flogin=guest&fpass=guest', 'allautoparts', 1); //Авторизация на allautoparts
		/*$xmlstring=getPage('http://allautoparts.ru/asp-x/Search/Search.ashx?ajax=search2&producerid=-1&mode=0&cross=1&directionid=-1&periodmin=-1&periodmax=-1&clientid=500&currency=rub&productid=52989463&stocksonly=0&sort=cost_asc&_=1420120601469', '', 'allautoparts');
		$xmlstring=str_replace('<?xml version="1.0" encoding="utf-16"?>','',$xmlstring);
		file_put_contents('test.html', $xmlstring);
		echo mb_detect_encoding ($xmlstring);*/
		/*$dom= new DOMDocument('1.0', 'utf-8');
		$dom->loadHTML($xmlstring);
		$dom->formatOutput=true;
		$dom->saveHTMLfile('test.html');*/
		//echo $xmlstring;
		
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
		return $arr_sparex;
		//$this->allautoparts($arr_sparex);
	}
}
?>