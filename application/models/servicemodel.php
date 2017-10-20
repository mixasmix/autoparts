<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Servicemodel extends CI_Model {
    public function __construct(){
		parent::__construct();
		$this->load->helper('getpage');
		$this->sql=SQL::getInstance();
                $this->sql->query("SET NAMES 'utf8';");
    }     
}