<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Backet extends CI_Model {

    private $sql; //тут у нас класс базы данных

    public function __construct() {
        parent::__construct();
        $this->load->helper('cookie');
        $this->load->database();
    }

    /*
     * метод добавлени¤ в корзину
     */

    public function addBacket() {
        $post = $this->input->post();
        if (empty($post)) {
            return false;
        }

        $part_data = $this->cachemodel->load($post['uid']);
        //Добавляем позицию в корзину
        $stm = $this->db->query('CALL addBacketPosition(:artikul, :brand, :quantity, :supplier_price, :price, :description, :delivery, :uid, :time)', [
            ':artikul' => $part_data['artikul'],
            ':brand' => $part_data['brand'],
            ':quantity' => $post['quant'] * 1,
            ':supplier_price' => $part_data['raw_data']['Price'],
            ':price' => $part_data['price'],
            ':description' => $part_data['description'],
            ':delivery' => $part_data['minperiod'] . '-' . $part_data['maxperiod'],
            ':uid' => $part_data['uid'],
            ':time' => time()
        ]);
      
        
        if (empty($stm->row()->insert_id)) {
            return false;
        }
       
        $params = [
            ':id_user' => $this->aauth->get_user_id(),
            ':id_backet' => $result[0]['insert_id'],
            ':id_status' => 1,
            ':sid' => session_id(),
            ':prov_id'=>$part_data['supplierId']
        ];
        $stm2 = $this->db->query('CALL addBacketStatePosition(:id_user, :id_backet, :id_status, :sid, :prov_id)', $params);
        
        header('Location: /page/find/' . $part_data['searchArtikul']);
    }

    /**
      Проверяет состояние пользовательской корзины и возвращает сумму и количество товара в массиве
     */
    public function checkbacket($id = '') {
       
        $stm = $this->db->query('CALL checkBacketUserId ( '.$this->aauth->get_user_id().', "'.session_id().'");');
       
        if (!empty($stm->row())) {
            return $stm->row();
        } else {
            return false;
        }
    }

    /**
     * ћетод удал¤ет товар из корзины
     * @param int $id Идентификатор позиции в корзине
     *
     */
    public function delBacket($id) {
        $stm = $this->sql->prepare('CALL deleteBacketPosition(:uid, :sid, :position_id)');
        $stm->execute([':uid' => $this->aauth->get_user_id(), ':sid' => session_id(), ':position_id' => $id]);
        return true;
    }

    /*     * Метод получает данные по товарам из корзины
     * @param bool $timeoutZakaz Если этот параметр в true то выведет все заказы с просроченным таймаутом, иначе все действительные
     */

    public function getBacket($timeoutZakaz = false) {
        $stm = $this->sql->prepare('CALL getBacketPosition(:uid, :sid)');
        $stm->execute([':uid' => $this->aauth->get_user_id(), ':sid' => session_id()]);
        $arr = $stm->fetchAll(PDO::FETCH_ASSOC);
        $stm->closeCursor();
        return $arr;
    }

    /**
     * Метод очищает удаляет все, что находится в корзине пользователя
     * @return boolean
     */
    public function clear() {
        $stm = $this->sql->prepare('CALL clearBacket(:uid, :sid)');
        $stm->execute([':uid' => $this->aauth->get_user_id(), ':sid' => session_id()]);
        $arr = $stm->fetchAll(PDO::FETCH_ASSOC);
        $stm->closeCursor();
        return true;
    }

    /**
      Метод добавляет пользовательскую корзину в заказы
     */
    public function toOrder() {

        if (empty($this->checkbacket()['positions'])) {
            return false;
        }
        $phone = $this->input->post('phone');
        $comment = $this->input->post('comment');
        $stm = $this->sql->prepare('CALL toOrder(:uid, :sid, :phone, :comment)');
        $stm->execute([':uid' => $this->aauth->get_user_id(), ':sid' => session_id(), ':phone' => $phone, ':comment' => $comment]);
        $arr = $stm->fetchAll(PDO::FETCH_ASSOC);
        $stm->closeCursor();
        //var_dump($arr, $stm->errorInfo()); 
        if (!empty($arr[0]['order_num'])) {
            return $arr[0]['order_num'];
        } else {
            return false;
        }

        /* $this->load->helper('getpage');
          $backet=$this->getBacket();  //получаем данные корзины
          if(empty($backet))
          return false;
          //$arr=array();
          foreach($backet as $b){

          $a2=unserialize($b['partdata']);
          unset($b['partdata']);
          $a2->backedata=$b;

          if($a2->provider==='906044c6cb4224c69ba36dc736606b4d'){
          //$this->toOrderAllAutoParts($a2);
          }elseif($a2->provider==='f781d2dc99fbaa3136da525b2977992e'){
          $this->toOrderSparex($a2);
          }elseif($a2->provider==='3f65b76b236b7ac5d499de34635de831'){
          $this->toOrderEmex($a2);
          }


          }

          $delivery=serialize($backet);
          $phone=$this->input->post('phone', true);
          $adress=$this->input->post('adress', true);
          $query="INSERT INTO deliveries (delivery, timestamp, phone, adress) VALUES (:delivery, :timestamp, :phone, :adress)";
          $stm=$this->sql->prepare($query);
          $stm->execute(array(':delivery'=>$delivery, ':timestamp'=>time(), ':phone'=>$phone, ':adress'=>$adress));
          $id_delivery=$this->sql->lastInsertId();

          $sess_data=$this->session->userdata('user');
          if(!empty($sess_data)){
          $id=$sess_data['id'];
          } else {
          $id=1; //если в сессиях id нет то это гость
          }

          $sql="INSERT INTO delivery_state (id_delivery, id_user, id_status) VALUES (:id_delivery, :id_user, :id_status)";
          $stm=$this->sql->prepare($sql);
          $stm->execute(array(':id_delivery'=>$id_delivery, ':id_user'=>$id, ':id_status'=>1));
          $this->delBacket('all', true); //указываем что корзину постащика очищать не надо! */
        /**
         * Отправка сообщения на почту, о том что сформирован новый заказ
         */
        /* $this->load->library('email');
          $this->email->from('sts2.ru', 'service');
          $this->email->to('info@sts2.ru');
          $this->email->subject('Новый заказ №'.$id_delivery);
          $this->email->message("Пришел новый заказ!\n\r Телефон:".$phone."\n\r Номер заказа:".$id_delivery."\n\r Адрес доставки:".$adress);
          $this->email->send(); */
        /* $msg="Пришел новый заказ!\n\r Телефон:".$phone."\n\r Номер заказа:".$id_delivery."\n\r Адрес доставки:".$adress;
          mail("sitests2ru@yandex.ru", 'Новый заказ №'.$id_delivery, $msg, "From: sts2.ru \n".'Content-type: text/html; charset="utf-8"');
          return $id_delivery; */
    }

    /**
     * Метод переводит заказ из корзины AllAutoParts в работу 
     * @param object $obj объект содержимого корзины, одна запись
     * @return bool Возвращает труе в случае успеха, фальш в случае неудачи
     */
    public function toOrderAllAutoParts($obj) {
        //Если массив пришел не пустой
        if (!empty($obj)) {
            $this->load->model('allautopart'); //загружаем модель автопарт
            //устанавливаем новый wsdl для работы с корзиной
            $this->allautopart->wsdl_uri = 'https://allautoparts.ru/WEBService/BasketService.svc/wsdl?wsdl';

            //получаем xml
            $responseXML = $this->allautopart->createMakeOrderXML($obj->backedata['id'], $obj->backedata['provider_id'], 'Тестовый заказ. В работу не ставьте!');
            $error = array();

            $result = $this->allautopart->query('MakeOrder', array('ParametersXml' => $responseXML), $error);

            return true;
        } else {
            //если массив пришел пустой то возвращаем false
            return false;
        }
    }

    public function toOrderEmex($array) {
        
    }

    public function toOrderSparex($array) {
        
    }

    /**
     *
     * @param object $obj
     * @return array Массив результатов
     */
    public function addBacketAllAutoPart($obj) {
        $this->load->model('allautopart');
        $data['Reference'] = $obj->Reference;
        $data['AnalogueCodeAsIs'] = $obj->AnalogueCodeAsIs;
        $data['AnalogueManufacturerName'] = $obj->AnalogueManufacturerName;
        $data['OfferName'] = $obj->OfferName;
        $data['LotBase'] = $obj->LotBase;
        $data['LotType'] = $obj->LotType;
        $data['PriceListDiscountCode'] = $obj->PriceListDiscountCode;
        $data['Price'] = $obj->rpr;
        $data['Quantity'] = $obj->Quantity;
        $data['PeriodMin'] = $obj->PeriodMin;

        //получаем xml для добавления в корзину
        $requestXMLstring = $this->allautopart->createAddBasketRequestXML($data);
        $errors = array();
        $result = $this->allautopart->query('AddBasket', array('AddBasketXml' => $requestXMLstring), $errors);
        $resultArr = $this->allautopart->parseAddBasketResponseXML($result);

        return $resultArr;
    }

    public function addBacketKat36($array) {
        return false;
        $api_key = "248c7f2c-b0e0-0fa9-3b37-f48d400d23dc";
        $this->load->helper('getpage');
        $postparam = 'oem=' . $array['oem'] . '&make_name=' . $array['make_name'] . '&detail_name=' . $array['detail_name'] . '&qnt=' . $array['qnt'] . '&comment=' . $array['comment'] . '&api_hash=' . $array['api_hash'] . '&api_key=' . $api_key;
        $result = json_decode(getPage('http://kat36.ru/api/v1/baskets', $postparam));
        if (!empty($result->data)) {
            $provider_id = $result->data->id;
            return $provider_id;
        } else {
            return false;
        }
    }

    /**
     * Метод возвращает активные заказы для текущего пользователя
     * @return array
     */
    public function getActiveOrders() {
        $stm = $this->sql->prepare('CALL getActiveOrders(:uid, :sid)');
        $stm->execute([':uid' => $this->aauth->get_user_id(), ':sid' => session_id()]);
        $arr = $stm->fetchAll(PDO::FETCH_ASSOC);
        $stm->closeCursor();
        if (!empty($arr)) {
            return $arr;
        } else {
            return false;
        }
    }

    /**
     * Метод возвращает архивные заказы для текущего пользователя
     * @return array
     */
    public function getArchiveOrders() {
        $stm = $this->sql->prepare('CALL getArchiveOrders(:uid, :sid)');
        $stm->execute([':uid' => $this->aauth->get_user_id(), ':sid' => session_id()]);
        $arr = $stm->fetchAll(PDO::FETCH_ASSOC);
        $stm->closeCursor();
        if (!empty($arr)) {
            return $arr;
        } else {
            return false;
        }
    }

    /**
     * Метод возвращает позиции в составе текущего заказа
     * @param int $idOrder ID order
     * @return array
     */
    public function getOrderPosition($idOrder) {
        $stm = $this->sql->prepare('CALL getThisOrderPositions(:id)');
        $stm->execute([':id' => $idOrder]);
        $arr = $stm->fetchAll(PDO::FETCH_ASSOC);
        $stm->closeCursor();
        if (!empty($arr)) {
            return $arr;
        } else {
            return false;
        }
    }

    /**
     * Метод возвращает массив заказов с входящими в них позициями
     */
    public function getOrders() {
        $orders = $this->getActiveOrders();
        $arr = [];
        if (!empty($orders)) {
            foreach ($orders as $order) {
                $order['positions'] = $this->getOrderPosition($order['order_id']);
                $arr[] = $order;
            }
        }
        return $arr;
    }

    /**
     * Метод возвращает массив заказов с входящими в них позициями
     */
    public function getOrdersArchive() {
        $orders = $this->getArchiveOrders();
        $arr = [];

        if (!empty($orders)) {
            foreach ($orders as $order) {
                $order['positions'] = $this->getOrderPosition($order['order_id']);
                $arr[] = $order;
            }
        }
        return $arr;
    }

}
