<?php

/* 
 * Модель для работы с поставщиками
 */

require_once('simpledom/simple_html_dom.php');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Suppliers extends CI_Model {
	public function __construct(){
		parent::__construct();
        }
        /**
         * Метод возвращает массив с результатами предложений от поставщиков
         * @param string $partnumber Артикул детали
         * @return array Массив с результатми запроса
         */
        public function getparts($partnumber){
            //подключаем модели поставщиков
             $this->load->model('suppliers/allautoparts', 'aap');
             $this->load->helper('stringsanitize');
             //var_dump($this->aap->searchalloffer($partnumber), __LINE__); exit;
             $result_array=[];//инициализируем массив
             $result_array=  array_merge($result_array, $this->aap->searchalloffer($partnumber));
             
             return $result_array;
        }
}