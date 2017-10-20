<?php

/**
 * @author MDTreloni
 * @email mixasmix@mail.ru 
 * @copyright 2015
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Changelog extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->load->helper('getpage');
		$this->sql=SQL::getInstance();
		$this->sql->query("SET NAMES 'utf8';");
	}
	public function addLog($message){
		$sql="INSERT INTO changelog (message, dt) VALUES (:message, :dt)";
		$stm=$this->sql->prepare($sql);
		$stm->execute(array(':message'=>$message, ':dt'=>time()));
		$id_changelog=$this->sql->lastInsertId();		//тут у нас номер вставленной позиции
		$id_admin=$this->session->userdata('identifier');
		
		$sql="INSERT INTO change_state (id_changelog, id_admin) VALUES (:id_changelog, :id_admin)";
		$stm=$this->sql->prepare($sql);
		
		$stm->execute(array(':id_changelog'=>$id_changelog, ':id_admin'=>$id_admin));
		
		$sql="SELECT t1.*, t3.first_name FROM changelog t1 INNER JOIN change_state t2 ON t2.id_changelog=t1.id INNER JOIN admin_users t3 ON t2.id_admin=t3.id WHERE t1.id=:id_changelog ORDER BY t1.dt DESC";
		$stm=$this->sql->prepare($sql);
		$stm->execute(array(':id_changelog'=>$id_changelog));
		$arr=$stm->fetchAll(PDO::FETCH_ASSOC);
		
		return $arr[0];
	}
	public function getLog(){
		$sql="SELECT t1.*, t3.first_name FROM changelog t1 INNER JOIN change_state t2 ON t2.id_changelog=t1.id INNER JOIN admin_users t3 ON t2.id_admin=t3.id WHERE t2.deleted!='1' ORDER BY t1.dt DESC LIMIT 0,20 ";
		$stm=$this->sql->prepare($sql);
		$stm->execute();
		$arr=$stm->fetchAll(PDO::FETCH_ASSOC);
		return $arr;
	}
}