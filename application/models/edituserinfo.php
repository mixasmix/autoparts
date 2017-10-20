<?php

/**
 * @author MDTreloni
 * @email mixasmix@mail.ru 
 * @copyright 2015
 * @deprecated
 */
require_once('simpledom/simple_html_dom.php');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class EditUserInfo extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->load->helper('getpage');
		$this->sql=SQL::getInstance();
		$this->sql->query("SET NAMES 'utf8';");
	}
	/**
	����� ��� �������������� ������ �����
	*/
	public function __call($name, $arg){
	
		/*echo $name; //��� ��� ������
		$arg[0]; //��� ��������
		$arg[1]; //��� id �����*/
		/*$arg[0]=filter_var(filter_var($arg[0], FILTER_SANITIZE_STRING), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		if($name=='email'){
			$arg[0]=filter_var($arg[0], FILTER_SANITIZE_EMAIL);
			$sql="UPDATE users SET email=:value WHERE id=:id";
		}elseif($name=='phone'){
			$arg[0]=filter_var($arg[0], FILTER_SANITIZE_NUMBER_INT);
			$sql="UPDATE users SET phone=:value WHERE id=:id";
		}elseif($name=='firstname'){
			$sql="UPDATE users SET name=:value WHERE id=:id";
		}elseif($name=='lastname'){
			$sql="UPDATE users SET family=:value WHERE id=:id";
		}elseif($name=='city'){
			$sql="UPDATE users SET sity=:value WHERE id=:id";
		}elseif($name=='address'){
			$sql="UPDATE users SET address=:value WHERE id=:id";
		}else{
			return false;
		}
		$stm=$this->sql->prepare($sql);
		$stm->execute(array(':id'=>$arg[1], ':value'=>$arg[0]));
		$err=$this->sql->errorInfo();
		if($err[0]==='00000'){
			return array($name, $arg[0]);
		} else {
			return 'error';
		}*/
                $error=false;
		$arg[0]=filter_var(filter_var($arg[0], FILTER_SANITIZE_STRING), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		if($name=='email'){
			$arg[0]=filter_var($arg[0], FILTER_SANITIZE_EMAIL);
			 $error=$this->aauth->set_user_var('email', $arg[0], $arg[1]);
		}elseif($name=='phone'){
			$arg[0]=filter_var($arg[0], FILTER_SANITIZE_NUMBER_INT);
			 $error=$this->aauth->set_user_var('phone', $arg[0], $arg[1]);
		}elseif($name=='firstname'){
			 $error=$this->aauth->set_user_var('firstname', $arg[0], $arg[1]);
		}elseif($name=='lastname'){
			 $error=$this->aauth->set_user_var('lastname', $arg[0], $arg[1]);
		}elseif($name=='city'){
			 $error=$this->aauth->set_user_var('city', $arg[0], $arg[1]);
		}elseif($name=='address'){
			 $error=$this->aauth->set_user_var('address', $arg[0], $arg[1]);
		}else{
			return false;
		}
		
		if($error){
			return array($name, $arg[0]);
		} else {
			return 'error';
		}
		
	}
}
?>