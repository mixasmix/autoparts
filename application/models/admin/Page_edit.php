<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Page_edit extends CI_Model {
	private $sql; //тут у нас класс базы данных
	public function __construct(){
		parent::__construct();
		$this->sql=SQL::getInstance();
		$this->load->helper('cookie');
		$this->sql->query("SET NAMES 'utf8';");
	}
	/**
	Получаем все страницы
	*/
	public function getAllPage(){
		$query="SELECT id, pagename, title, content FROM pages WHERE deleted!=1";
		$stm=$this->sql->prepare($query);
		$stm->execute();
		$result=$stm->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}
	/**
	Удаляем страницу с заданным id
	*/
	public function deleted($id){
		$query="UPDATE pages SET deleted=1 WHERE id=:id";
		$stm=$this->sql->prepare($query);
		$stm->execute(array(':id'=>$id));
	}
	/**
	Восстановливаем страницу с заданным id
	*/
	public function restore($id){
		$query="UPDATE pages SET deleted=0 WHERE id=:id";
		$stm=$this->sql->prepare($query);
		$stm->execute(array(':id'=>$id));
	}
	/**
	Получаем все страницы, удаленные ранее
	*/
	public function getAllPageArchive(){
		$query="SELECT id, pagename, title, content FROM pages WHERE deleted=1";
		$stm=$this->sql->prepare($query);
		$stm->execute();
		$result=$stm->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}
	/**
	Получаем данные страницы, если она существует
	*/
	public function getPage($id){
		$query="SELECT id, pagename, title, content, description FROM pages WHERE id=:id";
		$stm=$this->sql->prepare($query);
		$stm->execute(array(':id'=>$id));
		$result=$stm->fetchAll(PDO::FETCH_ASSOC);
		if(!empty($result[0])){
			return $result[0];
		} else{
			return false;
		}
	}
	/**
	Обновляем данные о старнице
	*/
	public function updatePage($id, $data){
		if(!empty($data['arch'])){$deleted=1;} else{$deleted=0;}
		$query="UPDATE pages SET pagename=:pagename, content=:content, title=:title, deleted=:deleted,  description=:description WHERE id=:id";
		$stm=$this->sql->prepare($query);
		$stm->execute(array(':id'=>$id, ':pagename'=>$data['pagename'], ':description'=>$data['description'],':content'=>$data['content'],':title'=>$data['title'],':deleted'=>$deleted));
		return true;
	}
	public function createPage($data){
		if(!empty($data['arch'])){$deleted=1;} else{$deleted=0;}
		$query="INSERT INTO pages (pagename, content, title, deleted, description) VALUES (:pagename, :content, :title, :deleted, :description)";
		$stm=$this->sql->prepare($query);
		$stm->execute(array(':pagename'=>$data['pagename'],':content'=>$data['content'],':title'=>$data['title'],':deleted'=>$deleted, ':description'=>$data['description']));
		
	}
}
?>