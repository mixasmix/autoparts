<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Класс работы с брендами
 */
class Breadcrumb extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->helper('cookie');
        setlocale(LC_ALL, 'ru_RU');
    }
    /**
     * Метод вызвращает сгенегированные хлебные крошки
     * @param array $array Массив параметров
     * @return string
     */
    public function breadcrumbs($array=false){
       $this->load->helper('url');
       $this->config->load('breadcrumb');
       $html="<div class='breadcrumbs'>".anchor('', $this->config->item ('breadcrumbs_index'));
            if(!empty($array)){
                foreach($array as $k=>$v){
                    $html.=$this->config->item ('breadcrumbs_delimeter');
                    $html.=anchor($k, $v);
                }
            }
       $html.="</div>";
        
       return $html;;
        
    }
}