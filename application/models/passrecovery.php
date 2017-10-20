<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Passrecovery extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->sql=SQL::getInstance();
		$this->load->helper('cookie');
        $this->sql->query("SET NAMES 'utf8';");
	}
	
	/**
	* Метод проверяет есть ли пользователь с таким email  в базе данных
	* @param string $email
	* @return bool
	*/
	public function checkEmailUserBase($email){
		$email=filter_var($email, FILTER_SANITIZE_EMAIL);
		$sql="SELECT email FROM users WHERE email=:email";
		$stm=$this->sql->prepare($sql);
		$stm->execute(array(':email'=>$email));
		$arr=$stm->fetchAll(PDO::FETCH_ASSOC);
		if(!empty($arr)){return true;}else{return false;}
	}
	/**
	* Метод проверяет есть ли пользователь с таким email  в базе данных
	* @param string $hash
	* @return bool
	*/
	public function checkHashUserBase($hash){
		
		$sql="SELECT email FROM users WHERE pass_recovery_hash=:hash";
		$stm=$this->sql->prepare($sql);
		$stm->execute(array(':hash'=>$hash));
		$arr=$stm->fetchAll(PDO::FETCH_ASSOC);
		if(!empty($arr)){return true;}else{return false;}
	}
	/**
	* Метод формирует hash строку для восстановления пароля
	* @param string $email
	* @return array Возвращает массив со сгенерированным хэшем и датой генерации
	*/
	public function getHash($email){
		$time=time();
		$hash=md5('srwhdnjsdegruj'.$email).md5('ewvavaqegfwhgew'.$email.$time);
		return array($time, $hash);
	}
	/**
	*  Метод добавляет в базу хэш для восстановления пароляи время его генерации
	* @param string $email
	* @param int $time
	* @param string $hash
	* @return bool
	*/
	public function addBaseHashRecovery($email, $time, $hash){
		try{
			$sql="UPDATE users SET prh_timestamp=:time, pass_recovery_hash=:hash WHERE email=:email";
			$stm=$this->sql->prepare($sql);
			$stm->execute(array(':email'=>$email, ':time'=>$time,':hash'=>$hash));
		} catch (PDOException $e){
			return false;
		}
		return true;
	}
	/**
	* Метод возвращает emai, hash и timestamp
	*
	* @param string $hash хэш для восстановления пароля
	* @return array Возвращает массив со значениями id, email, timestamp, hash, sailt
	*/
	public function getTimeAndHash($hash){
		$sql="SELECT id, email, prh_timestamp, pass_recovery_hash, sailt FROM users WHERE pass_recovery_hash=:prh";
		$stm=$this->sql->prepare($sql);
		$stm->execute(array(':prh'=>$hash));
		$arr=$stm->fetchAll(PDO::FETCH_ASSOC);
                if( $arr){
                    return $arr[0];
                }else{
                    return false;
                }
	}
	/**
	* Метод обнуляет тиместамп и хэш
	* @param int $id
	* @return bool 
	*/
	public function setRecoveryInfoNull($id){
		try{
			$sql="UPDATE users SET prh_timestamp=null, pass_recovery_hash=null WHERE id=:id";
			$stm=$this->sql->prepare($sql);
			$stm->execute(array(':id'=>$id));
		} catch (PDOException $e){
			return false;
		}
		return true;
	}
	private function passSave($pass, $sailt){
		$pass=sha1(md5($pass).md5($sailt));
		return $pass;
	}
	
	public function setNewPass($new_pass, $id, $sailt){
		$pass=$this->passSave($new_pass, $sailt);
		try{
			$sql="UPDATE users SET password=:pass WHERE id=:id";
			$stm=$this->sql->prepare($sql);
			$stm->execute(array(':pass'=>$pass, ':id'=>$id));
		} catch (PDOException $e){
			return false;
		}
		return true;
	}
}
?>