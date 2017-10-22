<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Getpage extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->load->database();
                
	}
	public function getPageData($pagename) {
		
		$query="SELECT title, content, description FROM pages WHERE pagename=".$this->db->escape($pagename)." AND deleted!='1'";
		$stm=$this->db->query($query);
		//var_dump($stm->row()); exit;
                if(empty($stm->row())){
			return false;
		}
		
		
		return $stm->result_array();
	}
}

