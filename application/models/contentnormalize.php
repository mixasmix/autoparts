<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once('simpledom/simple_html_dom.php');
/*
Тут будем приводить контент к стандартному виду
*/
class Contentnormalize  extends CI_Model {
	public function __construct(){
		parent::__construct();
	}
	
	/*Метод получает массив с контентом, приводит его к нормальному виду и возвращает назад*/
	public function normalizeContent($content){
		
		$html = str_get_html($content['content']);
		/*Ищем все элементы со стилями и удаляем стили у них*/
		$style=$html->find('*[style]');
		
		foreach($style as $st){
			$st->style=null;
		}
		/*ищем все элементы с id, событиями и удаляем их*/
		$elem=$html->find('*[id]');
		foreach($elem as $el){
			$el->id=null;
		}
		$elem=$html->find('*[onmouseout]');
		foreach($elem as $el){
			$el->onmouseout=null;
		}
		$elem=$html->find('*[onmouseover]');
		foreach($elem as $el){
			$el->onmouseover=null;
		}
		$elem=$html->find('*[onclick]');
		foreach($elem as $el){
			$el->onclick=null;
		}
		$elem=$html->find('*[name]');
		foreach($elem as $el){
			$el->name=null;
		}
		
		$elem=$html->find('*[class]');
		foreach($elem as $el){
			$el->class=null;
		}
		$this->load->model('filemodel', 'fm');
		/*Ищем все ссылки на странице*/
		$a=$html->find('a');
		foreach($a as $link){
			/*$check=strrpos($link->href, 'http://www.neoriginal.ru/spares');
			var_dump($check); 
			var_dump($link->href); 
			exit;
			if($check!=false){
				$link->href='/parts/getparts/'.$link->plaintext;
			}*/
		}
		/*Ищем все картинки на странице*/
		$img=$html->find('img');
		if(count($img)>0){
			//если существуют IMG
			foreach($img as $i){
				$filepath=$this->fm->getFile($i->src, $content['uri']);
				$i->src=$filepath;
				//////
				/***
				Сейчас надо написать модель для чека и сохранения файла
				**
				*/
				/////////
			}
		}
		
		$content['content']=$html->save();
		return $content;
	}
	
}