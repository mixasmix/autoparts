<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Addbacket extends CI_Controller {
        protected $sql;
        protected $session_user_info;
	public function __construct(){
		parent::__construct();
		$this->sql=SQL::getInstance();
		$this->load->helper('security');
	}
	
        
}
?>