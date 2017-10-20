<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Getuserinfo extends CI_Model {
	private $sql;
	private $garage;
	private $backet;
	private $delivery;
	public function __construct(){
		parent::__construct();
		$this->sql=SQL::getInstance();
		$this->load->helper('cookie');
	}
	public function getInfo($id){
		$this->getGarage($id);
		$this->getDelivery($id);
		$this->getBacket($id);
		$arr['garage']=$this->garage;
		$arr['delivery']=$this->delivery;
		$arr['backet']=$this->backet;
		return $arr;
	}
	private function getGarage($id){
		
			//погнали
			//Нам надо запросить у базы данные о машинах пользователя
			//пишем запрос
			$query="SELECT t1.* FROM vins t1 INNER JOIN vin_state AS t2 ON t2.id_vin=t1.id WHERE t2.id_user=:id";
			//Дальше все как обычно подготавливаем запрос, обрабатываем, сохраняем в $result
			$stm=$this->sql->prepare($query);
			$stm->execute(array(':id'=>$id));
			$result=$stm->fetchAll(PDO::FETCH_ASSOC);
			//Запиываем результат в переменную $garage и веренм труе
			$this->garage=$result;
	}
	/**
	*	Метод запрашивает данные о товарах в корзине пользователя и записывает их в переменную $backet
	*/
	private function getBacket($id){
			//погнали
			//Нам надо запросить у базы данные о корзине пользователя
			//пишем запрос
			$query="SELECT t1.* FROM backet t1 INNER JOIN backet_state AS t2 ON t2.id_backet=t1.id WHERE t2.id_user=:id";
			//Дальше все как обычно подготавливаем запрос, обрабатываем, сохраняем в $result
			$stm=$this->sql->prepare($query);
			$stm->execute(array(':id'=>$id));
			$result=$stm->fetchAll(PDO::FETCH_ASSOC);
			//Запиываем результат в переменную $garage и веренм труе
			$this->backet=$result;
	}
	/**
	*	Метод запрашивает данные о заказах пользователя и записывает их в переменную $delivery
	*/
	private function getDelivery($id){
			//погнали
			//Нам надо запросить у базы данные о корзине пользователя
			//пишем запрос
			$query="SELECT t1.*, t3.name, t3.value FROM deliveries t1 INNER JOIN delivery_state AS t2 ON t2.id_delivery=t1.id INNER JOIN delivery_status AS t3 ON t3.id=t2.id_status WHERE t2.id_user=:id";
			//Дальше все как обычно подготавливаем запрос, обрабатываем, сохраняем в $result
			$stm=$this->sql->prepare($query);
			$stm->execute(array(':id'=>$id));
			$result=$stm->fetchAll(PDO::FETCH_ASSOC);
			//Запиываем результат в переменную $garage и веренм труе
			$this->delivery=$result;
	}
}
	
?>
