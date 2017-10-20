<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Newsmodel extends CI_Model {
	public $sql;
	public $errorMessage;
	public function __construct(){
		parent::__construct();
		$this->sql=SQL::getInstance();
		$this->load->helper('cookie');
		$this->sql->query("SET NAMES 'utf8';");
	}
        /**
         * Метод возвращает 20 последних новостей
         * @return array Массив с новостями
         */
        public function getLastNews($page=1, $lim=20){
            $limitStart=($page*$lim-$lim)<0?0:$page*$lim-$lim;
            $linitEnd=$limitStart+$lim;
            $sql="SELECT id, title, content, description, datetime FROM news WHERE deleted!=1 ORDER BY datetime DESC LIMIT ".$limitStart.", ".$linitEnd;
            $stm=$this->sql->prepare($sql);
            $stm->execute();
            $result=$stm->fetchAll(PDO::FETCH_ASSOC);
            if(!empty($result)){
                return $result;
            }else{
                return false;
            }
        }
        
        public function getAllNews(){
            $sql="SELECT id, title, content, description, datetime FROM news WHERE deleted!=1";
            $stm=$this->sql->prepare($sql);
            $stm->execute();
            $result=$stm->fetchAll(PDO::FETCH_ASSOC);
            if(!empty($result)){
                return $result;
            }else{
                return false;
            }
        }
        /**
         * Метод добавляет новость
         * @param array $data Массив данных
         * @return bool Возвращает true в случае успеха
         */
        public function addNews($data){
            $sql="INSERT INTO news (title, content, description, datetime) VALUES (:title, :content, :description, :datetime)";
            $stm=$this->sql->prepare($sql);
            $stm->execute(array(':title'=>$data['title'], ':content'=>$data['content'], ':description'=>$data['description'], ':datetime'=>time()));
            if($stm->rowCount()){
                return true;
            }else{
                return false;
            }
        }
        /**
         * Метод возвращает данные новостной записи, если она не удалена
         * @param int $id id новости
         * @return boolean
         */
        public function getArticle($id){
            $id=abs($id*1);
            
            $sql="SELECT id, title, content, description, datetime FROM news WHERE id=:id AND deleted!=1";
            $stm=$this->sql->prepare($sql);
            $stm->execute(array(':id'=>$id));
            $result=$stm->fetchAll(PDO::FETCH_ASSOC);
            
            if(!empty($result)){
                return $result[0];
            }else{
                return false;
            }
        }
        /**
         * Метод ля удаления новостной записи
         * @param int $id ID записи
         * @return boolean Возвращает true в случае успеха, false в случае ошибки
         */
        public function deleteArticle($id){
            $id=abs($id*1);
            $sql="UPDATE news SET deleted=1 WHERE id=:id";
            $stm=$this->sql->prepare($sql);
            $stm->execute(array(':id'=>$id));
           $stm->fetchAll(PDO::FETCH_ASSOC);
            if($stm->rowCount()){
                return true;
            }else{
                return false;
            }
        }
        /**
         * Метод для редактирования новостной записи
         * @param array $data Массив для 
         * @param type $id
         * @return boolean
         */
        public function editArticle($data, $id){
            $id=abs($id*1);
            $sql="UPDATE news SET title=:title, content=:content, description=:description, datetime=:dt WHERE id=:id";
            $stm=$this->sql->prepare($sql);
            $stm->execute(array(':id'=>$id, ':title'=>$data['title'], ':content'=>$data['content'], ':description'=>$data['description'], ':dt'=>time()));
            $stm->fetchAll(PDO::FETCH_ASSOC);
            if($stm->rowCount()){
                return true;
            }else{
                return false;
            }
        }
        /**
         * Метод подсчитывает количество новостей
         * @return int Количество новостных записей
         */
        public function getCount(){
            $sql="SELECT COUNT(id) as count FROM news WHERE deleted!=1";
            $stm=$this->sql->prepare($sql);
            $stm->execute();
            $result=$stm->fetchAll(PDO::FETCH_ASSOC);
            if(!empty($result)){
                return $result[0]['count'];
            }else{
                return false;
            }
        }
        
}