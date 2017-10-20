<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth extends CI_Model {
	public $sql;
	public $errorMessage;
	public function __construct(){
		parent::__construct();
		$this->sql=SQL::getInstance();
		$this->load->helper('cookie');
		$this->sql->query("SET NAMES 'utf8';");
	}
	public function autorization($login, $pass, $remember='') {
                $login=  strip_tags($login);
                $pass=  strip_tags($pass);
		//$sql=$this->sql;
		$query="SELECT t1.*, t3.role, t2.id_role, t2.id_template FROM users t1 INNER JOIN user_state AS t2 ON t2.id_user=t1.id INNER JOIN roles AS t3 ON t2.id_role=t3.id WHERE t1.login=:login or t1.email=:email";
		$stm=$this->sql->prepare($query);
		$stm->execute(array(':login'=>$login, ':email'=>$login));
		$result=$stm->fetchAll(PDO::FETCH_ASSOC);
		//var_dump($result); exit;
		if(empty($result)){
			$this->errorMessage='Такой пользователь не зарегистрирован в системе';
			return false;
                }elseif($result[0]['deleted']==1){
                        $this->errorMessage='Пользователь удален из системы';
			return false;
                } else{
                    //var_dump($result[0]['password'], $passSave); exit;
			$passSave=$this->passSave($pass, $result[0]['sailt']);
                       
			if($result[0]['password']===$passSave){
                               
				$result[0]['password']='';
				$this->session->set_userdata(array('user'=>$result[0]));
				
				$cookie_expired=60*60*24*90;
					$cookie = array(
						'name'   => 'remember_me',
						'value'  => $result[0]['id'].'_'.md5($result[0]['sailt'].$_SERVER['REMOTE_ADDR']),
						'expire' => $cookie_expired
					);
					$this->input->set_cookie($cookie);
					
                                        $query="UPDATE users SET last_active=:la, user_ip=:uip WHERE id=:id";
                                        $stm=$this->sql->prepare($query);
                                        $stm->execute(array(':la'=>time(), ':id'=>$result[0]['id'], ':uip'=>$_SERVER['REMOTE_ADDR']));
                                       header('Location: '.'http://'.$_SERVER['HTTP_HOST']);
				
				
				
			}else {
				$this->errorMessage='Неправильный логин или пароль';
			}
		}
		
	}
	private function passSave($pass, $sailt){
		$pass=sha1(md5($pass).md5($sailt));
		return $pass;
	}
	public function authSocial($token){
                
                    
                
		//если авторизация была через соц сеть
		
		###################################################
		
		//Надо будет это куда то перенести
		//Типо если токен постом есть, то запрашиваем эту фигню, а если нет, то запрашиваем данные из базы и все как обычно
			$s = file_get_contents('http://ulogin.ru/token.php?token=' . $token . '&host=' . $_SERVER['HTTP_HOST']);
			$user = json_decode($s, true);
			//$user['network'] - соц. сеть, через которую авторизовался пользователь
			//$user['identity'] - уникальная строка определяющая конкретного пользователя соц. сети
			//$user['first_name'] - имя пользователя
			//$user['last_name'] - фамилия пользователя

		 
		   if(!empty($user) and empty($user['error'])){
				
				//надо проверить есть ли этот юзер в базе, если нет, добавить
				$query="SELECT t1.*, t3.role, t2.id_role FROM users t1 INNER JOIN user_state AS t2 ON t2.id_user=t1.id INNER JOIN roles AS t3 ON t2.id_role=t3.id WHERE t1.uid=:uid";
				$stm=$this->sql->prepare($query);
				$stm->execute(array(':uid'=>$user['uid']));
				$result=$stm->fetchAll(PDO::FETCH_ASSOC);
				//если результат не пустой, выводим данные юзера из базы
				
				if(!empty($result[0])){ 
					
					$this->session->set_userdata(array('user'=>$result[0]));
					$query2="UPDATE users SET last_active=:la, user_ip=:uip WHERE uid=:uid";
                                        $params=array(':la'=>time(), ':uid'=>$user['uid'], ':uip'=>$_SERVER['REMOTE_ADDR']);
				} 
                               
				//если пустой то надо добавить юзера в базу
				else {
					$id_user=$this->regSocial($user);
					$query="SELECT t1.*, t3.role, t2.id_role FROM users t1 INNER JOIN user_state AS t2 ON t2.id_user=t1.id INNER JOIN roles AS t3 ON t2.id_role=t3.id WHERE t1.id=:id";
					$stm=$this->sql->prepare($query);
					$stm->execute(array(':id'=>$id_user));
					$result=$stm->fetchAll(PDO::FETCH_ASSOC);
					$this->session->set_userdata(array('user'=>$result[0]));
                                        $query2="UPDATE users SET last_active=:la, user_ip=:uip WHERE id=:id";
                                        $params=array(':la'=>time(), ':id'=>$id_user, ':uip'=>$_SERVER['REMOTE_ADDR']);
				}
                               
                                
                                
                                $stm=$this->sql->prepare($query2);
                                $stm->execute($params);
		   }
                  
		 ################################################  
	   //Перенаправляем пользователя на гравную страницу сайта
	   header('Location: '.'http://'.$_SERVER['HTTP_HOST']);
	}
	private function regSocial($userdata) {
				//если пользователя нет то продолжаем регистрацию
				
				$query='INSERT INTO users (name, family, social_profile, auth, uid, reg_date) VALUES (:name, :family, :social_profile, :auth, :uid, :reg_date)';
				$stm=$this->sql->prepare($query);
				$result=$stm->execute(array(':name'=>$userdata['first_name'], ':family'=>$userdata['last_name'], ':social_profile'=>$userdata['profile'], ':auth'=>$userdata['network'], ':uid'=>$userdata['uid'], ':reg_date'=>time()));
				//var_dump($this->sql->errorInfo()); exit;
				if($result==false){ $this->errorMessage='Ошибка регистрации пользователя'; return false;}
				else {
					$id_user=$this->sql->lastInsertId();
					$query='INSERT INTO user_state (id_user, id_role) VALUES (:id_user, :id_role)';
					$stm=$this->sql->prepare($query);
					$stm->execute(array(':id_user'=>$id_user, ':id_role'=>2));
				}
			return $id_user;
	}
}