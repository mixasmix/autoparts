<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reg extends CI_Model {
	public $sql;
	public $errorMessage;
	public function __construct(){
		parent::__construct();
		$this->sql=SQL::getInstance();
		$this->load->helper('cookie');
                $this->sql->query("SET NAMES 'utf8';");
                $this->sql->query("SET FOREIGN_KEY_CHECKS=0;");
	}
	public function registration($login, $pass, $email) {
            
                        $login=  strip_tags($login);
                        $email=  strip_tags($email);
			$sailt=md5($login);	//���� ��5 �� ������
			$passSailt=$this->passSave($pass, $sailt);		//������� ������
			//���� ��������� � ���� �� ����� ��� ��� � ����
			$query='SELECT * FROM users WHERE login=:login or email=:email';
			$stm=$this->sql->prepare($query);
			$stm->execute(array(':login'=>$login, ':email'=>$email));
			$result=$stm->fetchAll(PDO::FETCH_ASSOC);
			if(!empty($result)){
				$this->errorMessage='Такой пользователь уже зарегистрирован';
				return false;
			} else{
				//���� ������������ ��� �� ���������� �����������
				$query='INSERT INTO users (login, password, email, sailt, reg_date, user_ip) VALUES (:login, :pass, :email, :sailt, :reg_date, :user_ip)';
				
				$stm=$this->sql->prepare($query);
				$result=$stm->execute(array(':login'=>$login, ':email'=>$email, ':pass'=>$passSailt, ':sailt'=>$sailt, ':reg_date'=>time(), ':user_ip'=>$_SERVER['REMOTE_ADDR']));
				if($result==false){ $this->errorMessage='������ ����������� ������������'; return false;}
				else {
                                        
					$id_user=$this->sql->lastInsertId();
					$query2='INSERT INTO user_state (id_user, id_role) VALUES (:id_user, :id_role)';
					$stm2=$this->sql->prepare($query2);
					$stm2->execute(array(':id_user'=>$id_user, ':id_role'=>2));
                                        //$this->sql->query("SET FOREIGN_KEY_CHECKS=1;");
                                       // var_dump($stm2->errorInfo());
				}
			}
			return true;
		
	}
	private function passSave($pass, $sailt){
		$pass=sha1(md5($pass).md5($sailt));
		return $pass;
	}
}