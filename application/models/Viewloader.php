<?php

/* 
 * Класс для вывода внешнего вида сайта
 * and open the template in the editor.
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Viewloader extends CI_Model {
        private $html='';
        private $tmplname;
        private $sql;
	public function __construct(){
		parent::__construct();
		$this->load->database();
                $this->tmplname=($this->aauth->get_user_var('tmpl_name'))?$this->aauth->get_user_var('tmpl_name'):$this->config->item('default_template');
                
        }
        /**
         * Метод выводит внешний вид сайта
         * @param string $tmplname Имя шаблона
         * @param array $params Массив параметров
         */
        public function getView($params){
            $params['directory']=$this->tmplname;
            $this->html=$this->load->view( $this->tmplname.'/head', $params, true); //загружаем шапку сайта
            $this->html.=$this->load->view($this->tmplname.'/content', $params, true); //загружаем контент сайта
            $this->html.=$this->load->view($this->tmplname.'/footer', $params, true); //загружаем подвал сайта
            return $this->html;
        }
        /**
         * Метод для вывода отображения админпанели
         */
        public function adminGetView($params=array('content'=>'Какой то контент')){
           return $this->load->view('adminpanel/adminview/html', $params);
        }
        /**
         * Метод возвращает содержимое view файла
         * @param string $filename Имя файл
         * @param array $params Массив параметров
         * @param bool $returned Выводить или возвращать результат. По умолчанию - true
         * @return type
         */
        public function getContent($filename, $params=[], $returned=true){
            return $this->load->view($this->tmplname.'/'.$filename, $params, $returned);
        }
}
?>
