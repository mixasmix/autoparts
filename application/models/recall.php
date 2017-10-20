<?php

/**
 * @author MDTreloni
 * @email mixasmix@mail.ru 
 * @copyright 2015
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Recall extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->load->helper('getpage');
		$this->sql=SQL::getInstance();
		$this->sql->query("SET NAMES 'utf8';");
	}
	/**
	Добавляет звонок в базу
	*/
	public function addRecall(){
		$phone=filter_var(filter_var(filter_var($this->input->post('phone', true), FILTER_SANITIZE_STRING), FILTER_SANITIZE_FULL_SPECIAL_CHARS), FILTER_SANITIZE_NUMBER_INT);
		$name=filter_var(filter_var($this->input->post('name', true), FILTER_SANITIZE_STRING), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		$comment=filter_var(filter_var($this->input->post('msg', true), FILTER_SANITIZE_STRING), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $data=$this->input->post('data', true);
                if(!empty($data)){
                    $comm='Запрос на деталь motorland ';
                    foreach($data as $k=>$v){
                        $comm.='|'.$v.'|';
                    }
                    $comment=$comm.' === '.$comment;
                }
		$sql="INSERT INTO recalls (phone, comment, named, dt) VALUES (:phone, :comment, :name, :dt)";
		$stm=$this->sql->prepare($sql);
		$stm->execute(array(':phone'=>$phone, ':comment'=>$comment, ':name'=>$name, ':dt'=>time()));
		
		$id_recall=$this->sql->lastInsertId();		//тут у нас номер вставленной позиции
		
		/*делаем таблицу соотношения*/
		$sess_data=$this->session->userdata('user');
		$id_user=(!empty($sess_data['id']))?$sess_data['id']:1;
		//запрос на добавление в таблицу сопоставлений будет такой
		$sql="INSERT INTO recall_state (`id_recall`, `id_status`, `id_user`) VALUES (:id_recall, :id_status, :id_user)";
		$stm=$this->sql->prepare($sql);
		$stm->execute(array(':id_recall'=>$id_recall, ':id_status'=>1, ':id_user'=>$id_user));
		
		$err=$this->sql->errorInfo();
		if($err[0]==='00000'){
			return true;
		} else {
			return false;
		}
	}
	public function getRecalls(){
		$sql="SELECT t1.*, t3.login,
									 t3.email,
									 t3.reg_date,
									 t3.last_active,
									 t3.address,
									 t3.auth,
									 t3.social,
									 t3.social_profile,
									 t3.`name`,
									 t3.family,
									 t3.phone AS user_phone,
									 t3.sity,
									 t3.id AS id_user,
									t4.val FROM recalls t1 INNER JOIN recall_state t2 ON t2.id_recall=t1.id INNER JOIN users t3 ON t3.id=t2.id_user INNER JOIN recall_status t4 ON t2.id_status=t4.id WHERE t2.id_status=1";
	//Вот такой вот здоровый селект
		$stm=$this->sql->prepare($sql);
		$stm->execute();
		$arr=$stm->fetchAll(PDO::FETCH_ASSOC);
		return $arr;
	}
	/*Меняем статус заявки*/
	public function editRecall($id, $status){
		if($status==='done')
			$status=2;
		if($status==='delete')
			$status=3;
		$sql="UPDATE recall_state SET id_status=:status WHERE id_recall=:id";
		$stm=$this->sql->prepare($sql);
		$stm->execute(array(':status'=>$status, ':id'=>$id));
	}
}
