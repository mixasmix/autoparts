<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Getimage extends CI_Controller {
    public function __construct(){
		parent::__construct();
                $this->load->helper('getpage');
		
	}
    public function get($brand, $part){
        $this->load->model('getartikulinfo','gai');
        /**
         * @var array $imgs Массив со ссылками на изображение
         */
        $brand=  str_replace('_', '/', $brand);
        $brand=  str_replace('(', '', $brand);
        $brand=  str_replace(')', '', $brand);
        
        $imgs=$this->gai->getImage($brand, $part);
        
        if(!empty($imgs)){
            $getArray=array();
            
            foreach($imgs[0] as $img){
                $img=(string)$img;
                $imageSize=getimagesize($img);
                
                $imageData = base64_encode(getPage($img));
                $getArray[]=array('mime'=>$imageSize['mime'], 'base_64'=>$imageData, 'width'=>$imageSize[0], 'height'=>$imageSize[1]);
                
            }
            echo json_encode($getArray);
        } else{
            echo 'false';
        }
        
    }
    public function getinform($part, $brand='') {
        $this->load->model('getartikulinfo','gai');
        $this->gai->getPartCard($brand, $part);
    }
    
    
    
    public function imgacessory($imgid, $widht, $height){
        $this->load->model('getartikulinfo','gai');
        header('COntent-type:image/jpeg');
         $this->gai->getAcessoryImage($imgid, $widht, $height);
    }
}