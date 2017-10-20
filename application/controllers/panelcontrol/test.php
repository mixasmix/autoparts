<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
error_reporting(E_ALL);

class test extends CI_Controller {
        private $params=array(); //массив параметров для отображения страницы
        protected $sql;
        
	public function __construct(){
            parent::__construct();
            
            $this->load->model('viewloader', 'vl'); //подгружаем модель вывода отображения
            $this->sql=SQL::getInstance();
        }
        
        public function index(){
           /*$this->load->model('suppliers/trinity', 'trinity');
           echo '<pre>';
                   var_dump($this->trinity->searchalloffer('oc257'));
                  // $this->trinity->trinityGetBacket();
                   //$this->trinity->sendInVork(74468963);*/
           }
           
}
?>