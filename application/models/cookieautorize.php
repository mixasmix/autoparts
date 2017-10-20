<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Cookieautorize extends CI_Model {
	public function __construct(){
		$this->confirm();
	} 
	private function confirm(){
	$remember=$this->input->cookie('remember_me');
	$session=$this->input->cookie('IpQoaRuFG7_session');
		if(!empty($remember) and empty($session)){
			$cookie=explode('_', $remember);
			$id=$cookie[0]*1;
			//$uniqueString=$cookie[1];
			$sql=SQL::getInstance();
			$query="SELECT t1.*, t3.role, t2.id_role FROM users t1 INNER JOIN user_state AS t2 ON t2.id_user=t1.id INNER JOIN roles AS t3 ON t2.id_role=t3.id WHERE t1.id=:id";
			$stm=$sql->prepare($query);
			$stm->execute(array(':id'=>$id));
			$result=$stm->fetchAll(PDO::FETCH_ASSOC);
			$cookieString=$result[0]['id'].'_'.md5($result[0]['sailt'].$_SERVER['REMOTE_ADDR']);
			if($cookieString===$this->input->cookie('remember_me', true)){
				$result[0]['password']='';
				$this->session->set_userdata(array('user'=>$result[0]));
			} else {
				delete_cookie('remember_me');
			}
		}
	}
}
?>