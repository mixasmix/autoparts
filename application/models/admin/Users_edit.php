<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users_edit extends CI_Model {
	private $sql; //тут у нас класс базы данных
	public function __construct(){
		parent::__construct();
		$this->sql=SQL::getInstance();
		$this->load->helper('cookie');
		$this->sql->query("SET NAMES 'utf8';");
	}
	/**
	Получаем всех юзеров
	*/
	public function getAllUsers($lim_min=0, $lim_max=100){
		$query="SELECT * FROM `users` WHERE deleted!=1 LIMIT ?, ?";
		$stm=$this->sql->prepare($query);
		$stm->bindParam(1, $lim_min, PDO::PARAM_INT);
		$stm->bindParam(2, $lim_max, PDO::PARAM_INT);
		$stm->execute();
		$result=$stm->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}
	/**
	Удаляем юзера с заданным id
	*/
	public function deleted($id){
		$query="UPDATE users SET deleted=1 WHERE id=:id";
		$stm=$this->sql->prepare($query);
		$stm->execute(array(':id'=>$id));
		
	}
	/**
	Восстановливаем страницу с заданным id
	*/
	public function restore($id){
		$query="UPDATE pages SET deleted=0 WHERE id=:id";
		$stm=$this->sql->prepare($query);
		$stm->execute(array(':id'=>$id));
	}
	/**
	Получаем все страницы, удаленные ранее
	*/
	public function getAllPageArchive(){
		$query="SELECT id, pagename, title, content FROM pages WHERE deleted=1";
		$stm=$this->sql->prepare($query);
		$stm->execute();
		$result=$stm->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}
	/**
	Получаем данные страницы, если она существует
	*/
	public function getUser($id){
		$query="SELECT * FROM users WHERE id=:id";
		$stm=$this->sql->prepare($query);
		$stm->execute(array(':id'=>$id));
		$result=$stm->fetchAll(PDO::FETCH_ASSOC);
		if(!empty($result[0])){
			return $result[0];
		} else{
			return false;
		}
	}
	/**
	Обновляем данные о старнице
	*/
	public function updateUser($id, $data){
              
		$data['login']=(!empty($data['login']))?$data['login']:'';
		$data['phone']=(!empty($data['phone']))?$data['phone']:'';
		$data['last_active']=(!empty($data['last_active']))?$data['last_active']:0;
		$data['reg_date']=(!empty($data['reg_date']))?$data['reg_date']:0;
                 
		$data['email']=(!empty($data['email']))?$data['email']:'';
		//$data['reg_date']=(!empty($data['reg_date']))?$data['reg_date']:'';
                
               // var_dump($data['reg_date']) ; exit;
		$data['address']=(!empty($data['address']))?$data['address']:'';
		$data['auth']=(!empty($data['auth']))?$data['auth']:'';
		$data['social']=(!empty($data['social']))?$data['social']:'';
		$data['name']=(!empty($data['name']))?$data['name']:'';
		$data['social_profile']=(!empty($data['social_profile']))?$data['social_profile']:'';
		$data['uid']=(!empty($data['uid']))?$data['uid']:'';
		$data['family']=(!empty($data['family']))?$data['family']:'';
		$data['sity']=(!empty($data['sity']))?$data['sity']:'';
		$data['merge']=(!empty($data['merge']))?$data['merge']:'';
		
              /*  $query="UPDATE users SET login='{$data['login']}',
								email='{$data['email']}',
								reg_date={$data['reg_date']},
								last_active={$data['last_active']},
								address='{$data['address']}',
								auth='{$data['auth']}',
                                                                social='{$data['social']}',
								`name`='{$data['name']}',
								social_profile='{$data['social_profile']}',
								uid='{$data['uid']}',
								phone='{$data['phone']}',
								family='{$data['family']}',
								sity='{$data['sity']}',
								`merge`={$data['merge']}
				WHERE id={$id}";
                var_dump($query) ; exit;*/
		$query="UPDATE users SET login=:login,
								email=:email,
								reg_date=:reg_date,
								last_active=:last_active,
								address=:address,
								auth=:auth,
								social=:social,
								`name`=:name,
								social_profile=:social_profile,
								uid=:uid,
								phone=:phone,
								family=:family,
								sity=:sity,
								`merge`=:merge
				WHERE id=:id";
		$stm=$this->sql->prepare($query);
               
		$stm->execute(array(
							':login'=>$data['login'],
							':email'=>$data['email'],
							':reg_date'=>$data['reg_date'],
							':last_active'=>$data['last_active'],
							':address'=>$data['address'],
							':auth'=>$data['auth'],
							':social'=>$data['social'],
							':name'=>$data['name'],
							':social_profile'=>$data['social_profile'],
							':uid'=>$data['uid'],
							':phone'=>$data['phone'],
							':family'=>$data['family'],
							':sity'=>$data['sity'],
							':merge'=>$data['merge'],
							':id'=>$id
		));
		
		return true;
	}
	public function createPage($data){
		if(!empty($data['arch'])){$deleted=1;} else{$deleted=0;}
		$query="INSERT INTO pages (pagename, content, title, deleted) VALUES (:pagename, :content, :title, :deleted)";
		$stm=$this->sql->prepare($query);
		$stm->execute(array(':pagename'=>$data['pagename'],':content'=>$data['content'],':title'=>$data['title'],':deleted'=>$deleted));
		
	}
}
?>