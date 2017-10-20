<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
echo '<pre>';
class Getcatalogvin  extends CI_Model {
	private $carInfo=array();
	private $file_dir;
	public $addUrl;
	private $sql;
	public $vinDecodePage;
	public $pageId;
	public function __construct(/*car_info*/){
		$this->sql=SQL::getInstance();
		//$this->file_dir=DOC_ROOT.'/temp/';
		//$this->carInfo['vin']=$car_info['1'];
		//$this->carInfo['cat']=$car_info['0'];
		//$item=$this->getPageVinDecode();
		//$this->vinDecodePage=$item[0];
		//$this->pageId=$item[1];
	}
	/*Метод возвращает ид модели*/
	private function getModel(){
	
	}
	/*Метод проверяет доступность сайта*/
	private function checkUrl($mark='kia'){
		$check=get_headers('http://www.neoriginal.ru/cat/'.$mark);
		if($check[0]!="HTTP/1.1 200 OK")
			return false;
		else 
			return true;
	}
	/**
	* метод возвращает страницу с каталога
	* @return array
	*/
	private function getPageVin(){
			$content=$this->getPage('http://www.neoriginal.ru/cat/'.$this->carInfo['cat'], 'vin='.$this->carInfo['vin']);
			return (array('content'=>$content, 'url'=>'http://www.neoriginal.ru/cat/'.$this->carInfo['cat']));
	}
	public function getPage($url, $post=''){
		$cookie='cookie.txt';
		$ya = curl_init($url);
		curl_setopt($ya, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ya, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ya, CURLOPT_COOKIE, $cookie);
		curl_setopt($ya, CURLOPT_COOKIESESSION, true);
		curl_setopt($ya, CURLOPT_RETURNTRANSFER, 1);
		/*curl_setopt($ya, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
		curl_setopt($ya, CURLOPT_PROXY, 'localhost:9150'); */
		curl_setopt($ya, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.0.1) Gecko/2008070208'); 
		curl_setopt($ya, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ya, CURLOPT_VERBOSE, 0);
		//curl_setopt($ya, CURLOPT_HEADER, 1);
		curl_setopt($ya, CURLOPT_POST, true);
		curl_setopt($ya, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ya, CURLOPT_COOKIEFILE, $cookie);
		curl_setopt($ya, CURLOPT_COOKIEJAR, $cookie);
		return curl_exec($ya);
	}
	/**
	* Метод возвращает страницу с элементами
	* @return string
	*
	*/
	private function getPageVinDecode(){
		$file_name=$this->carInfo['cat'].$this->carInfo['vin'];
		//проверяем существование файла
		$page=$this->addPageCacheAndBase($file_name, '', 1, $this->getPageVin());
		//создаем дом-документ
		$dom=new DOMDocument();
		@$dom->loadHTML($page['content']);
		//Если нет поля для ввода вина
		$vin_input=$dom->getElementById('vin');
		$content=$dom->getElementsByTagName('button');
		$url='http://www.neoriginal.ru'.str_replace('"','', str_replace('document.location="','',$content->item(0)->getAttribute('onclick')));
		$file_name=md5($url);
		$get_page=$this->addPageCacheAndBase($file_name, $url, $page['id']);
		//создаем новый дом и отсекаем все до группы з/ч
		$dom=new DOMDocument();
		@$dom->loadHTML($get_page['content']);
		//ищем поле с группами запчастей
		$fields_part_group=$dom->getElementsByTagName('legend');
		foreach($fields_part_group as $field_part_group){
			if($field_part_group->textContent=='Выбор группы з/ч' or $field_part_group->textContent=='Регион пр-ва' or $field_part_group->textContent=='Выбор модели'){
				$field=$field_part_group->parentNode;}
			else {
				$field=false;
			}
		}
		$result_page=$this->hrefReplace($dom->saveHTML($field));
		$this->addUrl=$result_page['url'];
		return array($result_page['content'], $get_page['id']);
		
	}
	/**
	* Метод добавляет страницу в кэш, в базу и добавляет связанность страниц в базе
	*
	*
	*/
	private function addPageCacheAndBase($filename='',  $url='', $parent_id=1, $content=''){
		$sql=$this->sql;
		//узнаем есть ли запись в базе
		//делаем запрос в базу
			$query="SELECT cache_page.* FROM cache_page WHERE filename=:filename";
			$stm=$sql->prepare($query);
			$stm->execute(array(':filename'=>$filename));
			$arr=$stm->fetchAll(PDO::FETCH_ASSOC);
			//если запрос не пустой
			if(!empty($arr)){
				$filename=$arr[0]['filename'];
				$id_page=$arr[0]['id'];
				$content=$arr[0]['content'];
			}
			//А если пустой, то надо добавить 
			else{
				//проверяем не пустой ли контент
				if(!empty($content)){
					$content=$content['content'];	
					$url=$content['url'];
				} else {
					$content=$this->getPage($url);
				}
				//Готовим запрос
				$query="INSERT INTO cache_page (filename, uri, hash, content) VALUES (:filename, :uri, :hash, :content)";
				$stm=$sql->prepare($query);
				$stm->execute(array(':filename'=>$filename, 'uri'=>$url, ':hash'=>md5($url), ':content'=>$content));
				
				$id_page=$sql->lastInsertId();
				//добавляем соотношение страниц
				$query="INSERT INTO cache_page_state (id_parent, id_page) VALUES (:id_parent, :id_page)";
				$stm=$sql->prepare($query);
				$stm->execute(array(':id_parent'=>$parent_id, ':id_page'=>$id_page));
			}
		/*
		if(true){
			if(!is_file($this->file_dir.$filename)){
				// если нет то записываем фыйл в папку
				if(!empty($content)){
					$content=$content['content'];	
					$url=$content['url'];
				} else {
					$content=$this->getPage($url);
				}
				//$content=str_replace('charset=windows-1251', 'charset=utf-8', $content);
				file_put_contents($this->file_dir.$filename, $content);
				//и доавляем в базу
				$query="INSERT INTO cache_page (filename, uri, hash) VALUES (:filename, :uri, :hash)";
				$stm=$sql->prepare($query);
				$stm->execute(array(':filename'=>$filename, 'uri'=>$url,':hash'=>md5($url)));
				$id_page=$sql->lastInsertId();
				//добавляем соотношение страниц
				$query="INSERT INTO cache_page_state (id_parent, id_page) VALUES (:id_parent, :id_page)";
				$stm=$sql->prepare($query);
				$stm->execute(array(':id_parent'=>$parent_id, ':id_page'=>$id_page));
		/*
		*
		* Меняем класс дла записи в базу а не в файлы
		*
		//проверяем есть ли файл
		if(!is_file($this->file_dir.$filename)){
			// если нет то записываем фыйл в папку
			if(!empty($content)){
				$content=$content['content'];	
				$url=$content['url'];
			} else {
				$content=$this->getPage($url);
			}
			//$content=str_replace('charset=windows-1251', 'charset=utf-8', $content);
			file_put_contents($this->file_dir.$filename, $content);
			//и доавляем в базу
			$query="INSERT INTO cache_page (filename, uri, hash) VALUES (:filename, :uri, :hash)";
			$stm=$sql->prepare($query);
			$stm->execute(array(':filename'=>$filename, 'uri'=>$url,':hash'=>md5($url)));
			$id_page=$sql->lastInsertId();
			//добавляем соотношение страниц
			$query="INSERT INTO cache_page_state (id_parent, id_page) VALUES (:id_parent, :id_page)";
			$stm=$sql->prepare($query);
			$stm->execute(array(':id_parent'=>$parent_id, ':id_page'=>$id_page));*/
		/*} else {
			
		}*/
		return array('filename'=>$filename, 'content'=>$content, 'id'=>$id_page);
	}
	public function hrefReplace($content){
		$dom=new DOMDocument();
		@$dom->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . $content);
		$ahref=$dom->getElementsByTagName('a');
		$legend=$dom->getElementsByTagName('legend');
		$arr_temp=array();
		$i=0;
		if(!$legend->textField=='Выбор запчасти'){
			foreach ($ahref as $a){
				$value_href=$a->getAttribute ('href');
				if(!stristr($value_href, 'http://www.neoriginal.ru/spares/')){
					$a->removeAttribute('href');
					$value_replace='http://www.neoriginal.ru'.$value_href;
					$a->setAttribute ('href', '/?pid='.md5($value_replace));
					$arr_temp[$i]['name']=$a->textContent;
					$arr_temp[$i]['hash']=md5($value_replace);
					$arr_temp[$i]['uri']=$value_replace;
				} /*elseif(!stristr($value_href, '/cat/'.$this->carInfo['cat'])){
				Вот тут херня какая то, надо переделать
				echo $value_href; echo '/cat/'.$this->carInfo['cat']; exit;
					$a->removeAttribute('href');
					$value_replace='http://www.neoriginal.ru'.$value_href;
					$a->setAttribute ('href', '/?pid='.md5($value_replace));
					$arr_temp[$i]['name']=$a->textContent;
					$arr_temp[$i]['hash']=md5($value_replace);
					$arr_temp[$i]['uri']=$value_replace;
				
				} */else {
					$a->removeAttribute('href');
					$value_replace=$a->textContent;
					/*if(empty($value_replace))
						continue*/
					$a->setAttribute ('href', '/?partid='.$value_replace);
					$arr_temp[$i]['name']=$a->textContent;
					$arr_temp[$i]['hash']=md5($value_replace);
					$arr_temp[$i]['uri']=$value_replace;
				}
				$i++;
			}
		}
		$arr['content']=$dom->saveHTML();
		$arr['url']=$arr_temp;
		return $arr;
	}
}
?>