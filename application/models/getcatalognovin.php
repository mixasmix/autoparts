<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once('simpledom/simple_html_dom.php');

class Getcatalognovin  extends CI_Model {
	public $sql;
	public $mark;
	public function __construct(){
		parent::__construct();
		$this->sql=SQL::getInstance();
	}
	/**
	* Основная функция для работы с каталогом
	*/
	public function getPage($hash, $id){
		//тут надо бы hash обработать
		$query="SELECT id, uri, filename, content FROM cache_page WHERE hash=:hash";
		$stm=$this->sql->prepare($query);
		$stm->execute(array(":hash"=>$hash));
		$arr=$stm->fetchAll(PDO::FETCH_ASSOC);
		$content=$arr[0]; //Тут найденая страница
		/*теперь нам надо запросить все <a> на странице, выбрать их href
		добавить к href домен
		перевести их в md5
		запросить страницу по ее href
		Отправить ее на функцию проверки существования в базе
		Если true то все нормальтно
		Если фолс то вызываем функцию добавления в cache page
		Заменяем все ссылки в content["content"] на /catalog/getpage/hash/id
		Возвращаем $content
		*/
		
		//делаем simple dom
		$html=str_get_html($content["content"]);
		//Вибираем все ссылки
		$a=$html->find("a");
		//проходим все ссылки, добавляем к ним домен, делаем md5, складываем в массив
		$i=0;
		$links=array();//пустой массив под ссылки
		/*
		Это все дело надо обернуть в if
		Вдруг в каталоге есть выбор региона производства 
		И еще ниссан, мерседес и еще что то там не забываем
		*/
		//если существует select на странице то это регион производства
		
		$sel=$html->find("input[type=radio]");
		
		$this->load->helper('url');
		$addcache_url=site_url("addcachepage/addcache");
		$this->load->helper('getpage');
		if(!count($sel)){
			/*var_dump($content);*/
			foreach($a as $link){
				$href=$link->href;
				if($link->href=="#") continue;
				if($link->class=="accordion-toggle collapsed") continue;
				$check=stristr($link->href, 'http://www.neoriginal.ru/spares/');
				/*strpos($link->href, 'http://www.neoriginal.ru/spares');
				var_dump($check);
				var_dump($link->href);
				/*exit;*/
				
				//полное имя будет 
				$href="http://www.neoriginal.ru".$href;
				$hash=md5($href);
				if($check){
					//var_dump($check); exit;
					$link->href='/parts/getparts/'.end(explode('/', $check));
				} else {
					$link->href="/catalog/getcatalog/".$hash."/".$content["id"];
				}
				$links[$i]["l"]=$href;
				$links[$i]["h"]=$hash;
				$i++;
				
				//теперь нам надо чекнуть ссылку на наличие в базе
				$b=$this->checkBase($hash);
				//если в базе такой страницы не существует
				if($b==false){
					$this->exec_script($addcache_url, array('hash'=>$hash, 'href'=>$href, 'parent'=>$content["id"]));
						/*getPage($addcache_url, "hash=".$hash."&href=".$href."&parent=".$content["id"], '',0, 0); */
						//$this->addCachePage($hash, $href, $content["id"]);
					
				}
			}
		}else{
			
			
		}
		$content["content"]=$html->save();
		
		return str_replace('display:none;','', $content);
		/*var_dump($links);*/
	}
	public function exec_script($url, $params = array()){
		$parts = parse_url($url);
	 
		if (!$fp = fsockopen($parts['host'], isset($parts['port']) ? $parts['port'] : 80))
		{
			return false;
		}
	 
		$data = http_build_query($params, '', '&');
	 
		fwrite($fp, "POST " . (!empty($parts['path']) ? $parts['path'] : '/') . " HTTP/1.1\r\n");
		fwrite($fp, "Host: " . $parts['host'] . "\r\n");
		fwrite($fp, "Content-Type: application/x-www-form-urlencoded\r\n");
		fwrite($fp, "Content-Length: " . strlen($data) . "\r\n");
		fwrite($fp, "Connection: Close\r\n\r\n");
		fwrite($fp, $data);
		fclose($fp);
	 
		return true;
	}
	/**
	* Функция будет искать страницу в кэше
	*
	* она примет хэш от урла оригинальной страницы каталога
	* Если страница в кэше есть то она вернет true
	* Если страницы нет, то получаем false
	*/
	private function checkBase($hash){
		$query="SELECT id FROM cache_page WHERE hash=:hash";
		$stm=$this->sql->prepare($query);
		$stm->execute(array(":hash"=>$hash));
		$arr=$stm->fetchAll(PDO::FETCH_ASSOC);
		if(!empty($arr))
			return true;
		else
			return false;
	}
	public function addCachePage($hash, $link, $parent){
		$this->load->helper('getpage');
		$page=str_get_html(getPage($link));
		//ищем поля
		if(!empty($page)){
			$field=$page->find("fieldset");
			$title=$page->find("title");
		} else {
			$field=false;
		}
		
		//если каталог с полями
		if(count($field)){
			$content=$field[0]->parent->outertext;
			
		} else{
			$div=$page->find(".panel-body");
			//var_dump($div[0]->parent->outertext);exit;
			if(!empty($div)){
				$content=$div[0]->parent->outertext;
			} else {
				return false;
			}
		}
		//var_dump($content); exit;
		$query="INSERT INTO cache_page (uri, hash, pagename, content) VALUES (:uri, :hash, :pagename, :content)";
			$stm=$this->sql->prepare($query);
			$stm->execute(array(":hash"=>$hash, ":uri"=>$link, ":pagename"=>$title[0]->plaintext, ":content"=>$content));
			
			$id=$this->sql->lastInsertId();
			$query="INSERT INTO cache_page_state (id_parent, id_page) VALUES (:id_parent, :id_page)";
			$stm=$this->sql->prepare($query);
			$stm->execute(array(':id_parent'=>$parent, ':id_page'=>$id));
			
			return true;
		
	}
	
}
?>