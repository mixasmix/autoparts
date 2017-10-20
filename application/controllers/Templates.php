<?php
class Templates extends CI_Controller {
	 public function __construct(){
		parent::__construct();
                $this->load->model('viewloader', 'vl');
	}
        public function tmpl($dirname, $filename){
            echo $this->vl->getTemplFile($dirname, $filename);
        }
}
?>