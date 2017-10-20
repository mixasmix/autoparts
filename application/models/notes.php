<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Notes extends CI_Model {
	private $sql; //тут у нас класс базы данных
	public function __construct(){
		parent::__construct();
		$this->sql=SQL::getInstance();
		$this->load->helper('cookie');
		$this->sql->query("SET NAMES 'utf8';");
	}
        /**
         * 
         * @param int $brand_id ID бренда
         * @param int $artikul_id ID артикула
         * @param int $user_id ID юзера
         * @param string $note Заметка
         */
        public function addNote($brand_id, $artikul_id, $note, $user_id, $sid){
            
            $sql="INSERT INTO notes (note, timestamp) VALUES (:note, :tmst)";
            $stm=$this->sql->prepare($sql);
            $stm->execute(array(':note'=>$note, ':tmst'=>time()));
            //id заметки будет здесь
            $id_note=$this->sql->lastInsertId();
            $sid='';
            //$sess_data=$this->session->userdata('user');
            if(empty($user_id)){
                    $user_id=1;
            }
            $sid=$this->session->userdata['session_id'];
            if(!empty($id_note)){
                 $sql="INSERT INTO note_state (id_user, id_note, sid, id_brand, id_artikul) VALUES (:id_user, :id_note, :sid, :id_brand, :id_artikul)";
                    $stm=$this->sql->prepare($sql);
                    $stm->execute(array(':id_user'=>$user_id, ':id_note'=>$id_note, ':sid'=>$sid, ':id_brand'=>$brand_id, ':id_artikul'=>$artikul_id));
                    if($stm->rowCount()){
                        return true;
                    }else{
                        return false;
                    }
            }else{
                return false;
            }
        }
        /**
         * Возвращает ид артикула и бренда
         * @param string $artikul Артикул
         * @param string $brand Бренд
         * @return array Массив id_brand и id_artikul
         */
        public function getArtAndBrandID($artikul, $brand){
            $sql="SELECT t1.id_brand, t1.id_artikul FROM artikul_state t1 INNER JOIN artikuls t2 ON t2.id=t1.id_artikul INNER JOIN brands t3 ON t3.id=t1.id_brand WHERE t2.artikul=:artikul AND t3.`name`=:brand";
            $stm=$this->sql->prepare($sql);
            $stm->execute(array(':artikul'=>$artikul, ':brand'=>$brand));
            $arr=$stm->fetchAll(PDO::FETCH_ASSOC);
            if(!empty($arr[0])){
                return $arr[0];
            } else{
                return false;
            }
            
        }
        /**
         * Метод подсчитывает количество заметок Юзера
         * @param id $id_user ID юзера
         * @param string $sid Session ID
         * @return int Количество заметок пользователя или False в случае неудачи
         */
        public function countNote($id_user, $sid=''){
            if(!empty($id_user)){
                $sql="SELECT COUNT(id_user) as count_note FROM note_state WHERE id_user=:id_user AND deleted!=1";
                $params=array(':id_user'=>$id_user);
            } else{
                $sql="SELECT COUNT(id_user) as count_note FROM note_state WHERE id_user=1 AND sid=:sid AND deleted!=1";
                $params=array(':sid'=>$sid);
            }
            $stm=$this->sql->prepare($sql);
            $stm->execute($params);
            $arr=$stm->fetchAll(PDO::FETCH_ASSOC);
            if(!empty($arr)){
                return $arr[0]['count_note'];
            }else{
                return false;
            }
        }
        
        public function getUserNote($user_id, $sid=''){
            $sql="SELECT t1.id,t1.note,t3.`name`,t4.artikul, t4.description, t1.timestamp
                    FROM notes t1 
                    INNER JOIN note_state t2 ON t1.id=t2.id_note
                    INNER JOIN brands t3 ON t3.id=t2.id_brand 
                    INNER JOIN artikuls t4 ON t4.id=t2.id_artikul  ";
            if(!empty($user_id)){
                $sql=$sql.'WHERE t2.id_user=:user_id';
            }else{
                $sql=$sql.'WHERE t2.sid=:user_id';
                $user_id=$sid;
            }
            $sql.=" AND t2.deleted!=1 ORDER BY t1.id DESC LIMIT 0, 20";
            $stm=$this->sql->prepare($sql);
            $stm->execute(array(':user_id'=>$user_id));
            $arr=$stm->fetchAll(PDO::FETCH_ASSOC);
            if(!empty($arr)){
                return $arr;
            }else{
                return false;
            }
        }
        
        public function updateNote($note, $id_note, $id_user, $sid=''){
            
            $note=filter_var(strip_tags($note), FILTER_SANITIZE_STRING);
            $sql="UPDATE notes t1 INNER JOIN note_state t2 ON t1.id=t2.id_note SET t1.note=:note WHERE t2.id_note=:id_note";
            if(!empty($id_user)){
                $sql=$sql.' AND t2.id_user=:user_id';
            }else{
                $sql=$sql.' AND t2.sid=:user_id';
                $id_user=$sid;
            }
           // var_dump($sql);exit;
            $stm=$this->sql->prepare($sql);
            $stm->execute(array(':user_id'=>$id_user, ':id_note'=>$id_note, ':note'=>$note));
            if($stm->rowCount()){
                return true;
            }else{
                return false;
            }
        }
        
       public function  deleteNote($id_note, $id_user, $sid){
           $id_note=abs((int)$id_note);
           $sql="UPDATE note_state SET deleted=1 WHERE id_note=:id_note";
           if(!empty($id_user)){
                $sql=$sql.' AND id_user=:user_id';
            }else{
                $sql=$sql.' AND sid=:user_id';
                $id_user=$sid;
            }
           
            
            $stm=$this->sql->prepare($sql);
            $stm->execute(array(':user_id'=>$id_user, ':id_note'=>$id_note));
        
            if($stm->rowCount()){
                return true;
            }else{
                return false;
            }
       }
}