<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Parts extends CI_Controller {
        protected $sql;
        protected $session_user_info;
	public function __construct(){
		parent::__construct();
		$this->load->model('json');
                $this->sql=SQL::getInstance();
		$this->load->helper('security');
                $this->uRole();
	}
	
	public function index(){
		$this->load->model('getpage');
		$result_page=$this->getpage->getPageData('parts');
		$sess_data=$this->session_user_info;
		$result_page[0]['sess_data']=$sess_data;
		$result_page[0]['content']='Контент';
		$this->load->view('head', $result_page[0]);
		$this->load->view('header');
		$this->load->view('backet',  $result_page[0]);
		$this->load->view('content', $result_page[0]);
		$this->load->view('footer');
		$this->load->view('bottom');
	}
	public function addparts(){
		$this->json->addArtikulBase();
	}
	public function getparts($parts, $s='', $returned=false, $brand=''){
		$userdata=$this->session->userdata('user');
		if(empty($s)){
                    $this->load->model('getpage');
                    $result_page=$this->getpage->getPageData('parts');
                    $sess_data=$this->session_user_info;
                    $result_page[0]['sess_data']=$sess_data;
                    $result_page[0]['content']='<script type="text/javascript">getParts("'.$parts.'");</script>Контент';
                    $this->load->view('head', $result_page[0]);
                    $this->load->view('header');
                    $this->load->view('backet',  $result_page[0]);
                    $this->load->view('content', $result_page[0]);
                    $this->load->view('footer');
                    $this->load->view('bottom');
			
		} else{
                   
                    $res=(array)$this->json->getParts($parts, $s, $brand=true);
                    
		}
                if($returned==1){
                     return $res;
                }else{
                    echo json_encode($res);
		
		}
	
        }
        public function getallparts($parts){
            $i=15;
            $arr=array();
            for($j=8; $j<=$i; $j++){
                $res=$this->getparts($parts, $j, 1);
               $arr+=$res; 
            }
           echo json_encode($arr);
        }
}
?>