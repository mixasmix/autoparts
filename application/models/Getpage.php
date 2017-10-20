<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Getpage extends CI_Model {
	protected $sql;
	public function __construct(){
		parent::__construct();
		$this->sql=SQL::getInstance();
                $this->sql->query("SET NAMES 'utf8';");
                
	}
	public function getPageData($pagename) {
		$sql=$this->sql;
		$query="SELECT title, content, description FROM pages WHERE pagename=:pagename AND deleted!='1'";
		$stm=$sql->prepare($query);
		$stm->execute(array(':pagename'=>$pagename));
		$arr=$stm->fetchAll(PDO::FETCH_ASSOC);
		if(empty($arr)){
			return false;
		}
		$newarr=array();
		///
		foreach($arr[0] as $k=>$v){
			$newarr[0][$k]=$v;
		}
		
		
		return $newarr;
	}
}

