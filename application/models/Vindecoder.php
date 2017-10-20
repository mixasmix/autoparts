<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once('simpledom/simple_html_dom.php');

class Vindecoder extends CI_Model {
	private $sql; //тут у нас класс базы данных
	public function __construct(){
		parent::__construct();
		$this->sql=SQL::getInstance();
		$this->load->helper('cookie');
		$this->sql->query("SET NAMES 'utf8';");
	}
	
	public function addNewVin($vin){
		//проверяем что в вине нет лишних символов и он равен 17 символам
		$vin= preg_replace('%[^A-Za-zА-Яа-я0-9]%', '', $vin); 
		if(strlen($vin)!=17){
			return false;
		}
		$sess_data=$this->session->userdata('user');
		$vin=strtoupper($vin);
		//проверяем, есть ли вин в базе
		$findVin=$this->findVinBase($vin); 
		//если массив - вин есть, если false - нет
		if(!empty($findVin)){
			/*если вин есть
			нам надо проверить, какой пользователь его добавляет
			Если тот же, кто добавил его в базу в первый раз, то вернуть данные из базы
			*/
			$a=false;
			foreach($findVin as $vin){
				if($vin['user_id']==$sess_data['id']){
					$a=true;
					break;
				}
			}
			//если значение юзер ид найдено, и a стало true
			if($a){
                                $sql="UPDATE vin_state SET deleted=0 WHERE id_vin=:id_vin and id_user=:id_user";
                                $stm=$this->sql->prepare($sql);
                                $stm->bindParam(':id_vin', $findVin[0]['vin_id'], PDO::PARAM_INT);
                                $stm->bindParam(':id_user', $sess_data['id'], PDO::PARAM_INT);
                                $stm->execute();
				return false; //возвращаем false так как наша миссия на этом исполнена, вин в базу не добавлен, там он уже есть
			}else{
				//если вин в базе есть, а юзера нет, то следует соотнести юзера с базой
				$this->addVinState($sess_data['id'], $findVin[0]['vin_id'], $findVin[0]['inf_id']);
				return true; //возвращаем true так как юзера мы все же добавили
			}
		}else{
			//Если у нас в базе ничего нет, не вина ни информации
			$id_vin=$this->addVinBase($vin); //сначала добавляем вин в таблицу винов
			$inf=$this->getVinInfo($vin); //получаем информацию о вине
			$id_inf=$this->addVinInf($inf);//добавляем информацию об этом вине в базу
			$id_user=(!empty($sess_data['id']))?$sess_data['id']:1; //устанавливаем, кто из юзверей вызвал метод
			
			//сводим всю информацию в таблицу соотношения
			$this->addVinState($id_user, $id_vin, $id_inf);
			return true;
		}
	}
		/*public function getVinInfo($vin){
		$vin= preg_replace('%[^A-Za-zА-Яа-я0-9]%', '', $vin); 
		if(strlen($vin)!=17){
			return false;
		}
		$this->load->helper('getpage');
		
		$sess_data=$this->session->userdata('user');
		$vin=strtoupper($vin);
		
		$sql="SELECT t1.vin, t5.id as user_id, t3.inf FROM vins t1 INNER JOIN vin_state t4 ON t4.id_vin=t1.id INNER JOIN vin_info t3 ON t4.id_vininfo=t3.id INNER JOIN users t5 ON t4.id_user=t5.id WHERE t1.vin=:vin";
		
		$stm=$this->sql->prepare($sql);
		$stm->execute(array(':vin'=>$vin));
		$arr=$stm->fetchAll(PDO::FETCH_ASSOC);
		if(!empty($arr[0])){
			return $arr[0];
		} else {
			//сначала добавляем новый вин в базу
			$sql="INSERT INTO vins (vin, dt) VALUES (:vin, :dt)";
			$stm=$this->sql->prepare($sql);
			$stm->execute(array(':vin'=>$vin, ':dt'=>time()));
			$id_vin=$this->sql->lastInsertId();		//тут у нас номер вставленной позиции
			
			//запрашиваем данные по вину
			
			$a=getPage('http://pogazam.ru/vin/?vin='.$vin);
			$html=str_get_html($a);
			$div=$html->find('#table_result_search');
			$table= str_get_html('<table>'.$div[0]->innertext.'</table>');
			unset($a, $div, $html);
			$tr=$table->find('tr');
			if(count($tr>1)){
				$table=array();
					foreach($tr as $t){
						$td=$t->find('td');
						$td0=$td[0]->find('b');
						@$k=$td0[0]->innertext;
						@$v=$td[1]->innertext;
						if(!empty($k) and !empty($v)){							
							$table[$k]=$v;
						}
					}
			} else {
				$table=false;
			}
			//Если таблица не пустая добавляем ее в базу
			//var_dump($table); exit;
			if($table){
				$sql="INSERT INTO vin_info (inf) VALUES (:inf)";
				$stm=$this->sql->prepare($sql);
				
				$stm->execute(array(':inf'=>json_encode($table)));
				
				$id_info=$this->sql->lastInsertId();		//тут у нас номер вставленной позиции
			}
			
			$id_user=(!empty($sess_data['id']))?$sess_data['id']:1;
			$id_vininfo=(!empty($id_info))?$id_info:0;
			
			$sql="INSERT INTO vin_state (id_vin, id_vininfo, id_user) VALUES (:id_vin, :id_vininfo, :id_user)";
			$stm=$this->sql->prepare($sql);
			$stm->execute(array(':id_vin'=>$id_vin, ':id_vininfo'=>$id_vininfo, ':id_user'=>$id_user));
			
			
			$sql="SELECT t1.vin, t5.*, t3.inf FROM vins t1 INNER JOIN vin_state t4 ON t4.id_vin=t1.id INNER JOIN vin_info t3 ON t4.id_vininfo=t3.id INNER JOIN users t5 ON t4.id_user=t5.id WHERE t1.id=:id";
		
			$stm=$this->sql->prepare($sql);
			$stm->execute(array(':id'=>$id_vin));
			$arr=$stm->fetchAll(PDO::FETCH_ASSOC);
			
			return $arr[0];
				
		}
	}*/
	/**
         * Возвращает информацию по данному вину
         * @param string $vin Вин номер автомобиля
         * @return array
         */
	public function getVinInfo($vin){
            //проверим длинну vin номера
            $vin=trim($vin);
            if(strlen($vin)!=17)
                return false;
            /*Проверим есть ли информация для данного вин номера в базе и если есть вернем ее*/
            $stm1=$this->sql->prepare('CALL getVinInfo(:id_vin)'); //получаем информацию по вину
            $stm2=$this->sql->prepare('CALL addVin(:vin)'); //получаем ид вина
            $stm2->execute([':vin'=>$vin]);
            $result=$stm2->fetchAll(PDO::FETCH_ASSOC);
            if(empty($result[0]))
                return false;
            $id_vin=$result[0]['id'];
            $stm2->closeCursor();
            $stm1->execute([':id_vin'=>$id_vin]);
            $vin_info=$stm1->fetchAll(PDO::FETCH_ASSOC);
            $stm1->closeCursor();
            if(!empty($vin_info)){
                $result=[];
                foreach($vin_info as $vi){
                    $result[$vi['param']]=$vi['value'];
                }
                return $result;
            }else{
                /*
                 * если информации по вину нет то вернем ее со стороннего сервиса
                 */
                $this->load->helper('getPage');
                $sess_id=json_decode(getPage('https://avtobot.net/main2/process', 'vin='.$vin))->session;
                $parameters=json_decode(getPage('https://avtobot.net/blocks/vindecode', 'urlhash='.$sess_id));
                if(!empty($parameters)){
                    $this->addInfoVinToBase((array)$parameters, $id_vin);
                    return (array)$parameters;
                }else{
                    return false;
                }
            }
            /*
		$this->load->helper('getpage');
		$vin=strtoupper($vin);
		//запрашиваем данные по вину
			
			$a=getPage('http://pogazam.ru/vin/?vin='.$vin.'&makenum=1');
			$html=str_get_html($a);
			$div=$html->find('#table_result_search');
			$table= str_get_html('<table>'.$div[0]->innertext.'</table>');
			unset($a, $div, $html);
			$tr=$table->find('tr');
			if(count($tr>1)){
				$table=array();
					foreach($tr as $t){
						$td=$t->find('td');
						$td0=$td[0]->find('b');
						@$k=$td0[0]->innertext;
						@$v=$td[1]->innertext;
						if(!empty($k) and !empty($v)){							
							$table[$k]=$v;
						}
					}
			} else {
				$table=false;
			}
		return $table;*/
	}
	/**
	Ищем вин в базе данных
	*/
	public function findVinBase($vin){
		$vin=strtoupper($vin);
		$sql="SELECT t1.vin, t1.id AS vin_id, t5.id as user_id, t3.inf, t3.id as inf_id FROM vins t1 INNER JOIN vin_state t4 ON t4.id_vin=t1.id INNER JOIN vin_info t3 ON t4.id_vininfo=t3.id INNER JOIN users t5 ON t4.id_user=t5.id WHERE t1.vin=:vin";
		
		$stm=$this->sql->prepare($sql);
		$stm->execute(array(':vin'=>$vin));
		$arr=$stm->fetchAll(PDO::FETCH_ASSOC);
		if(!empty($arr)){
			return $arr;
		} else{
			return false;
		}
	}
	/**
            Добавляет вин в базу
         * @param string $vin Вин номер автомобиля
         * @return int ID vin номер
	*/
	public function addVinBase($vin){
            $sql="CALL addVin (:vin)";
            $stm=$this->sql->prepare($sql);
            $stm->execute(array(':vin'=>$vin));
            $id_vin=$stm->fetchAll(PDO::FETCH_ASSOC)[0]['id'];
            $stm->closeCursor();
            if(!empty($id_vin)){
                return $id_vin;
            } else{
                return $false;
            }
	}
        public function addInfoVinToBase($parameters, $id_vin){
            $stm1=$this->sql->prepare('CALL addValueVin(:val)');
            $stm2=$this->sql->prepare('CALL addParameterVin(:val)');
            $stm3=$this->sql->prepare('CALL addStateVinParams(:id_vin, :id_param, :id_value)');
            foreach($parameters as $k=>$v){
                if(empty($v) or empty($k)){
                    continue;
                }
                $stm1->execute([':val'=>trim($v)]);
                $id_val=$stm1->fetchAll(PDO::FETCH_ASSOC)[0]['id'];
                $stm1->closeCursor();
                $stm2->execute([':val'=>trim($k)]);
                $id_param=$stm2->fetchAll(PDO::FETCH_ASSOC)[0]['id'];
                $stm2->closeCursor();
                if(!empty($id_val) and !empty($id_param)){
                    $stm3->execute([':id_vin'=>$id_vin, ':id_param'=>$id_param, ':id_value'=>$id_val]);
                    $stm3->closeCursor();
                }
            }
        }
	/**
	Список всех винов в базе
	*/
	public function getAllVinBase($lim_min=0, $lim_max=20){
		$sql="SELECT t1.vin, t1.dt, t5.*, t3.inf FROM vins t1 INNER JOIN vin_state t4 ON t4.id_vin=t1.id INNER JOIN vin_info t3 ON t4.id_vininfo=t3.id INNER JOIN users t5 ON t4.id_user=t5.id ORDER BY t1.dt DESC LIMIT ?, ?"; 
		$stm=$this->sql->prepare($sql);
		$stm->bindParam(1, $lim_min, PDO::PARAM_INT);
		$stm->bindParam(2, $lim_max, PDO::PARAM_INT);
		$stm->execute();
		$arr=$stm->fetchAll(PDO::FETCH_ASSOC);
		return $arr;
	}
	/**
	Выбрать машины только этого пользователя
         * @return array VIN номера для декущего пользователя
	*/
	public function getVinUser($user_id){
            /*$parameters=$this->getVinInfo('JMZGH12F781114236');
            $id_vin=$this->addVinBase('JMZGH12F781114236');
            $this->addInfoVinToBase($parameters, $id_vin);
            exit;*/
            $sql="CALL getVinsThisUser(:user_id)"; 
            $stm=$this->sql->prepare($sql);
            $stm->execute(array(':user_id'=>$this->aauth->get_user_id()));
            $arr=$stm->fetchAll(PDO::FETCH_ASSOC);
            $stm->closeCursor();
            
            
            if(!empty($arr)){
               $c=[];
               foreach($arr as $a){
                   $a['info']=$this->getVinInfo($a['vin']);
                   $c[]=$a;
               }
            }
            return $c;
	}
        
        public function getTableCar($garage){
            $html='';
        
            if(!empty($garage)){
                    $count=1;
                    foreach($garage as $car){
                            if($count==1){
                                    $html.='<div class="div_user_garage_row">';
                            }
                            /***********************************/
                           $html.='
                            <div class="div_user_garage_car">
                            <table>';
                                   
                                            $car_info=json_decode($car['inf']);
                                            foreach($car_info as $k=>$v){
                                                  $html.='
                                                    <tr>
                                                            <td>'.$k.'</td>
                                                            <td>'.$v.'</td>
                                                    </tr>';
                                            }
                                   $html.='</table>
                                <div class="div_user_garage_car_image_parent"><a href="#'.$car['vin_id'].'" class="a_deleted_garage" title="Удалить из гаража"></a><!--<div class="div_user_garage_car_image"></div>--></div>
                            </div>';
                            /***********************************/

                            if($count==3){
                                    $html.='</div>';
                                    $count=1;
                            }else{
                                $count++;
                            }
                    }
            }
            return $html;
        }
        
        public function deleteVin($id_vin, $id_user){
            $sql="UPDATE vin_state SET deleted=1 WHERE id_vin=:id_vin and id_user=:id_user";
            $stm=$this->sql->prepare($sql);
		$stm->bindParam(':id_vin', $id_vin, PDO::PARAM_INT);
		$stm->bindParam(':id_user', $id_user, PDO::PARAM_INT);
		$stm->execute();
        }
}