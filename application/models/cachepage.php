<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once('/simpledom/simple_html_dom.php');

class Cachepage  extends CI_Model {
	public $sql;
	public $mark;
	public function __construct(){
		parent::__construct();
		$this->sql=SQL::getInstance();
	}
	public function toCache($hash, $link, $parent){
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
		
		
		
		/*
		//ищем поля
		//var_dump($parent);
		$field=$page->find("fieldset");
		$title=$page->find("title");
		
		//если каталог с полями
		if(count($field)){
			$content=$field[0]->parent->outertext;
			
			$query="INSERT INTO cache_page (uri, hash, pagename, content) VALUES (:uri, :hash, :pagename, :content)";
			$stm=$this->sql->prepare($query);
			$stm->execute(array(":hash"=>$hash, ":uri"=>$link, ":pagename"=>$title[0]->plaintext, ":content"=>$content));
			//var_dump($stm); exit;
			$id=$this->sql->lastInsertId();
			
			$query="INSERT INTO cache_page_state (id_parent, id_page) VALUES (:id_parent, :id_page)";
			$stm=$this->sql->prepare($query);
			$stm->execute(array(':id_parent'=>$parent, ':id_page'=>$id));
			
			return true;
		}
		*/
	}
}