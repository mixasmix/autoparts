<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Createtable  extends CI_Model {
	public function __construct(){
		parent::__construct();
		require_once('simpledom/simple_html_dom.php');
	}
	/**
	* Нам надо запросить страницу. Выбрать из нее родительский элемент - это должен быть tr
	*
	*
	*/
	public function gettable2(){
		$array=unserialize(file_get_contents('test3'));
		$this->load->helper('getpage');
		$foo=array();
		foreach($array as $key=>$a){
			$query="SELECT id FROM cache_page WHERE hash=:hash";
			$sql=SQL::getInstance();
			$stm=$sql->prepare($query);
			
			$stm->execute(array(':hash'=>md5($a['link'])));
			
			$arr=$stm->fetchAll(PDO::FETCH_ASSOC);
			$img=array_pop(explode("/", $a['img']));
		//	echo "/images/logo/".$img; exit;
			$id_page=$arr[0]["id"];
			/*var_dump($id_page); exit;*/
				//добавляем соотношение страниц
			$query="UPDATE cache_page SET filename=:filename WHERE id=:id";
			$stm=$sql->prepare($query);
			$stm->execute(array(':filename'=>$img, ':id'=>$id_page));
			
			file_put_contents($img, file_get_contents('http://www.neoriginal.ru'.$a['img']));
		}
		//file_put_contents("test3", serialize($array));
		/*$html=file_get_html("temp");
		echo "<pre>";
		$glob_arr=array();
		foreach ($html->find("a") as $a){
			$arr=array();
			$arr['link']='http://www.neoriginal.ru'.$a->href;
			//echo $a->href.'<br>';
			$img=$a->find("img");
			$strong=$a->find("strong");
			foreach($strong as $str){
				$arr["name"]=str_replace("Каталог запчастей ","", $str->plaintext);
			}
			foreach($img as $i){
				$arr['img']=$i->src;
			}
			$glob_arr[]=$arr;
		}
		
		file_put_contents("test2", serialize($glob_arr));*/
	}
	
		
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function gettable(){
		$sql=SQL::getInstance();
		$query="SELECT id, uri, filename, pagename FROM cache_page";
		$stm=$sql->prepare($query);
		//var_dump($sql);
		$stm->execute();
		$arr=$stm->fetchAll(PDO::FETCH_ASSOC);
		$i=1;
		$table="<table>";
		foreach($arr as $a){
			if($a['id']==1){
				continue;
			}
			if($i==1){
				$table.='<tr>';
			}
				$table.="<td><a href='/catalog/getcatalog/".md5($a["uri"])."/".$a['id']."'><img src='/images/logo/".$a['filename']."'/>".$a['pagename']."</a></td>";		
				
			if($i==5){
				$table.='</tr>';
			}
			$i++;
			if($i==6)
				$i=1;
		}
		
		$table.='</tr></table>';
		return $table;
	}	
}

?>