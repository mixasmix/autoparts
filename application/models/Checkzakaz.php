<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once('simpledom/simple_html_dom.php');
/**
 * Класс проверки состояния заказа
 */
class Checkzakaz extends CI_Model {
	private $sql; //тут у нас класс базы данных
        private $status=array(  '-10' => "Отказ",
                                '-20' => "Отказ: Свами свяжется менеджер",
                                '0' => "Корзина",
                                '10' => "Отправлено на обработку",
                                '20' => "Принято обработчиком",
                                '30' => "В работе",
                                '40' => "В работе у поставщика",
                                '50' => "Закуплено поставщиком",
                                '60' => "Отгружено поставщиком",
                                '70' => "Пришло на центральный склад",
                                '75' => "В пути",
                                '78' => "Склад Воронеж",
                                '80' => "готов к выдаче склад Воронеж",
                                '99' => "Исключительная ситуация",
                                '90' => "Передан курьеру. Готов к доставке",
            
                                'processing' => "Отправлено на обработку",
                                'commit'     => 'Принято обработчиком', 
                                'v-zakaze'   => 'В работе у поставщика', 
                                'supplier-commit' => 'Закуплено поставщиком', 
                                'transit'         => 'В пути', 
                                'supplier-accept' => 'В пути', 
                                'prishlo'         => 'Склад Воронеж', 
                                'vydano'          => 'готов к выдаче склад Воронеж',
                                'otkaz'           => 'Отказ',
                                'snyat'           => 'Отказ: Свами свяжется менеджер'
            
                                );
	public function __construct(){
		parent::__construct();
		$this->sql=SQL::getInstance();
		$this->load->helper('cookie');
                $this->sql->query("SET NAMES 'utf8';");
               
	}
	/**
	* Метод выводит сведения о заказе
	* @param int $zakaznum Номер заказа в системе
	*/
	public function getZakazInfo($zakaznum){
		//Нам надо отрезать 000
		$zakaznum=intval(str_replace('000','', $zakaznum));
                if(empty($zakaznum)){
                    return false;
                }
		//делаем запрос к базе данных
		$sql="SELECT deliveries.phone, deliveries.delivery, users.login, deliveries.`timestamp`, deliveries.adress, delivery_state.id_delivery, delivery_state.id_status, delivery_status.`name` as status_msg FROM deliveries INNER JOIN delivery_state ON deliveries.id=delivery_state.id_delivery INNER JOIN delivery_status ON delivery_state.id_status=delivery_status.id INNER JOIN users ON delivery_state.id_user=users.id WHERE delivery_state.id_delivery=:id";
		$stm=$this->sql->prepare($sql);
		$stm->execute(array(':id'=>$zakaznum));
		$arr=$stm->fetchAll(PDO::FETCH_ASSOC);
                if(!empty($arr)){
                    return $arr[0];
                }else{return false;}
		
	}
	public function invoice_html_create($arr){
		$html='<style>#contain{margin: 0 auto;}#body{margin-bottom: 30px;}#body table{width:100%;border:1px solid black;border-collapse:collapse;}#head{margin-bottom: 20px;}#head_right{ }#head_right p{text-align:center;margin:0;}#footer{} </style>  <div id="contain"><div id="head"><div id="head_right"><p  style="font-family: Arial,Impact, sans-serif; font-size:20px; color:black; margin:0; text-align:center">Заказ №000'.$arr['id_delivery'].'</p>';
		$html.='<p>'.date('d-m-Y h:i:s', $arr['timestamp']).'</p>
					<p>Покупатель: '.$arr['login'].'</p>
					<p>Телефон: '.substr($arr['phone'], 0, 3).'****'.substr($arr['phone'], 7, strlen($arr['phone'])).'</p>
					<p style="font-size:20px; text-align:left;">Статус заказа: '.$arr['status_msg'].'</p>
				</div>
			</div>
			<hr>
			<div id="body">
				<h3>Заказ №000'.$arr['id_delivery'].' от '.date('d-m-Y h:i:s', $arr['timestamp']).'</h3>';
		$a=unserialize($arr['delivery']);
		$html.='<table border=1>
					<tr>
						<th>№</th>
						<th>Товар</th>
						<th>№ каталог</th>
						<th>Кол-во</th>
						<th>Ед.</th>
						<th>Цена</th>
						<th>Всего</th>
						<th>Срок доставки</th>
						<th>Статус</th>
					</tr>';
		$i=0;
		$summ=0;
                
                //соберем массив id позиций
                $positionId=array();
                if(empty($a)){
                    return false;
                }
                foreach($a as $b){
			 $c=unserialize($b['partdata']);
                            //$positionId[$c->provider][]=$b['id']; 
                            if($c->provider=='906044c6cb4224c69ba36dc736606b4d'){
                                $positionId[$c->provider][]=$b['id']; 
                            }elseif($c->provider=='b1e590c4cf8b0a5814241aa63205c767'){
                                $positionId[$c->provider][]=array($b['id'], $b['provider_id']); 
                            }
                            //var_dump($b); exit;
                }
                //обойдем массив id циклом
                $resultSatusParts=array();
                
                foreach($positionId as $key=>$value){
                    $resultSatusParts=$this->getProviderStatus($value,$key)+ $resultSatusParts;
                    
                }
                
		foreach($a as $b){
			$pid=$b['provider_id']; //внутренний номер провайдера
			$c=unserialize($b['partdata']);
                        $status='';
                        
                       
                        if(@$resultSatusParts[$b['id']]['state_msg']){
                                $status=$resultSatusParts[$b['id']]['state_msg'];
                            }else{
                                $status='Принято в работу';
                        }
			$i++;
			$html.='<tr>
						<td class="td_border td_align_center">'.$i.'</td>
						<td class="td_border td_align_center">'.$c->description.' '.$c->brandName.'</td>
						<td class="td_border td_align_center"><a href="/#!'.$c->artikul.'">'.$c->artikul.'</a></td>
						<td class="td_border td_align_center">'.$b['quantity'].'</td>
						<td class="td_border td_align_center">шт.</td>
						<td class="td_border td_align_center">'.$b['price'].'</td>
						<td class="td_border td_align_center">'.$b['price']*$b['quantity'].'</td>
						<td class="td_border td_align_center">'.$c->delivery_period.'</td>
						<td class="td_border td_align_center">'.$status.'</td>
					</tr>';
					$s=$b['price']*$b['quantity'];
					$summ+=$s;
					
		}
		$html.='</table>
				<br>
				<hr>
				<p>Всего '.$i.' наименований на сумму '.$summ.' рублей</p>
			</div>
			<div id="fooret"></div>
		</div>
	'.'<div id="print_invoice_div"><a href="/page/print_invoice/'.$arr['id_delivery'].'/'.md5('uywevgweeewefe'.$arr['id_delivery']).'" target="_blank">Распечатать инвойс</a></div>';
		return $html;
	}
        public function getProviderStatus($arr, $provider){
          //var_dump($arr); exit;
            //если провайдер аллавтопартс
            if($provider=='906044c6cb4224c69ba36dc736606b4d'){
                $this->load->model('allautopart'); //загружаем модель автопарт
                 //устанавливаем новый wsdl для работы с корзиной
                 $this->allautopart->wsdl_uri='https://allautoparts.ru/WEBService/OrderService.svc/wsdl?wsdl';
                 //получаем xml
                 $responseXML=$this->allautopart->createOrdersInWork($arr);
                 $error=array();
               
                 $result=$this->allautopart->query('OrdersInWork3', array('ParametersXml'=>$responseXML), $error);
                 $responseArray=array();
                 foreach($result->order as $o){
                     if(empty($o["orderNum"])){
                            $responseArray[$id]['state_id']=false;
                            $responseArray[$id]['state_msg']=false;
                            continue;
                     }
                    // print_r($o->orderIn->details->detail->states->state['stateId']); exit;
                     $id=  str_replace('STS2-', '', $o["orderNum"].'');
                     $responseArray[$id]['state_id']=$o->orderIn->details->detail->states->state['stateId']*1;
                     $responseArray[$id]['state_msg']=$this->status[$o->orderIn->details->detail->states->state['stateId']*1];//(string)$o->orderIn->details->detail->states->state['hint'];
                 }
                
            }elseif($provider=='b1e590c4cf8b0a5814241aa63205c767'){
               
                $this->load->helper('getpage');
                $api_key='248c7f2c-b0e0-0fa9-3b37-f48d400d23dc';
                $zakaz_id=$arr[0][1];
                $id=$arr[0][0];
                $query='http://kat36.ru/api/v1/order_items/?search[id_eq]='.$zakaz_id.'&api_key='.$api_key;
                $result=json_decode(getPage($query));
                if(!empty($result)){
                    //var_dump($result->data[0]->status_code); exit;
                    if(!empty($result->data[0])){
                        $st_code=$result->data[0]->status_code;
                    }else{
                        $st_code=10;
                    }
                        $responseArray[$id]['state_id']=($st_code=='prishlo')?80:0;
                        $responseArray[$id]['state_msg']=$this->status[$st_code];
                    
                }
            }
           
            return $responseArray;
           
        }
        /**
	* Метод выводит сведения о заказе
	* @param int $uid ID пользователя
	*/
	public function getZakazUser($id){
		//делаем запрос к базе данных
		$sql="SELECT deliveries.phone, deliveries.delivery, users.login, deliveries.`timestamp`, deliveries.adress, delivery_state.id_delivery, delivery_state.id_status, delivery_status.`name` as status_msg FROM deliveries INNER JOIN delivery_state ON deliveries.id=delivery_state.id_delivery INNER JOIN delivery_status ON delivery_state.id_status=delivery_status.id INNER JOIN users ON delivery_state.id_user=users.id WHERE delivery_state.id_user=:id AND delivery_state.id_status NOT  IN(4,3,6,7)";
		$stm=$this->sql->prepare($sql);
		$stm->execute(array(':id'=>$id));
		$arr=$stm->fetchAll(PDO::FETCH_ASSOC);
		return $arr;
	}
        /**
	* Метод выводит сведения о архивных заказах
	* @param int $uid ID пользователя
	*/
	public function getArchiveZakazUser($id){
		//делаем запрос к базе данных
		$sql="SELECT deliveries.phone, deliveries.delivery, users.login, deliveries.`timestamp`, deliveries.adress, delivery_state.id_delivery, delivery_state.id_status, delivery_status.`name` as status_msg FROM deliveries INNER JOIN delivery_state ON deliveries.id=delivery_state.id_delivery INNER JOIN delivery_status ON delivery_state.id_status=delivery_status.id INNER JOIN users ON delivery_state.id_user=users.id WHERE delivery_state.id_user=:id AND delivery_state.id_status IN(4,3,6,7) ORDER BY deliveries.`timestamp` DESC";
		$stm=$this->sql->prepare($sql);
		$stm->execute(array(':id'=>$id));
		$arr=$stm->fetchAll(PDO::FETCH_ASSOC);
		return $arr;
	}
        /**
        * Метод формирует таблицу с активными заказами пользователя
         * @param array $arr Массив выборки из базы данных
         * @param bool $archive булево в архиве заказ или нет
         * @return string Строка с HTML разметкой
        */
        public function getTableZakazUser($array, $archive=false){
            $html='';
            if(!empty($array)){
                foreach($array as $arr){
                    $html.='<h3>Заказ №000'.$arr['id_delivery'].' от '.date('d-m-Y h:i:s', $arr['timestamp']).' Статус заказа: '.$arr['status_msg'].'</h3>';
                    $a=unserialize($arr['delivery']);
                    $html.='<table border=1>
					<tr>
						<th>№</th>
						<th>Товар</th>
						<th>№ каталог</th>
						<th>Кол-во</th>
						<th>Ед.</th>
						<th>Цена</th>
						<th>Всего</th>
						<th>Срок доставки</th>
						<th>Статус</th>
					</tr>';
                    $i=0;
                    $summ=0;
                    //соберем массив id позиций
                    $positionId=array();
                    foreach($a as $b){
                            $c=unserialize($b['partdata']);
                            //$positionId[$c->provider][]=$b['id']; 
                            if($d->provider=='906044c6cb4224c69ba36dc736606b4d'){
                                $positionId[$c->provider][]=$b['id']; 
                            }elseif($d->provider=='b1e590c4cf8b0a5814241aa63205c767'){
                                $positionId[$c->provider][]=array($b['id'], $b['provider_id']); 
                            }
                    }
                    //обойдем массив id циклом
                    $resultSatusParts=array();
                    
                    foreach($positionId as $key=>$value){
                        $resultSatusParts=$this->getProviderStatus($value,$key)+ $resultSatusParts;
                    }

                    foreach($a as $b){
                            $pid=$b['provider_id']; //внутренний номер провайдера
                            $c=unserialize($b['partdata']);
                           
                            
                            $status='';
                            
                            if(@$resultSatusParts[$b['id']]['state_msg']){
                                $status=$resultSatusParts[$b['id']]['state_msg'];
                            }else{
                                if(!$archive){
                                    $status='Принято в работу';
                                }else{
                                    $status='Архив';
                                }
                            }
                            $i++;
                            $html.='<tr>
                                                    <td>'.$i.'</td>
                                                    <td>'.$c->description.' '.$c->brandName.'</td>
                                                    <td><a href="/#!'.$c->artikul.'">'.$c->artikul.'</a></td>
                                                    <td>'.$b['quantity'].'</td>
                                                    <td>шт.</td>
                                                    <td>'.$b['price'].'</td>
                                                    <td>'.$b['price']*$b['quantity'].'</td>
                                                    <td>'.$c->delivery_period.'</td>
                                                    <td>'.$status.'</td>
                                            </tr>';
                                            $s=$b['price']*$b['quantity'];
                                            $summ+=$s;

                    }
                    $html.='</table>
                                    <br>
                                    <hr>
                                    <p>Всего '.$i.' наименований на сумму '.$summ.' рублей</p><br><br><br><br>';

                }
            }
            return $html;
        }
		
}