<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class News extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->load->helper('getpage');
		$this->sql=SQL::getInstance();
		$this->sql->query("SET NAMES 'utf8';");
	}
        public function getNews(){
            $this->load->model('getpage');
            return $this->getpage->getPageData('newsslider');
        }
}
?>
