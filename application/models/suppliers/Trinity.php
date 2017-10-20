<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
get_instance()->load->iface('supplier'); //подгружаем интерфейс поставщиков /interfaces/supplier.php
/**
* Класс для работы с поставщиком trinity
* 
*/
class Trinity extends CI_Model implements Supplier {
    private static $client;
    private static $_inited = false;
    private $login;
    private $pass;
    private $key;
    public function __construct() {
        parent::__construct();
        $this->config->load('suppliers'); /*конфиг с данными поставщиков*/
        $this->login=$this->config->item('trinity')['login'];
        $this->pass=$this->config->item('trinity')['password'];
        $this->key=$this->config->item('trinity')['key'];
        $this->load->helper('procents');
        $this->load->helper('getpage');
        $url='http://trinity-parts.ru';
        $params='login='.$this->login.'&password='.$this->pass.'&do_member=on&member=forever';
        getPage($url, $params, 'trinity', 1);
        /**
         * Подключаемся к вебсервису
         */
        try{
            self::$client=new SoapClient('http://trinity-parts.ru/ws/onlineservices.1cws?wsdl', array('login' => 'sts2',
                        'password' => 'LrLDZO'));
            self::$_inited = true;
        }  catch (Exception $e){
            self::$_inited = false;
            return false;
        }
        return true;
    }
    /*
    public function step1($partNumber) {
        $url="http://trinity-parts.ru/user/ajax/search-by-codeonbadeo";
        $params='login='.$this->login.'&password='.$this->pass.'&do_member=on&member=forever&code='.$partNumber;
        $html=getPage($url, $params, 'trinity', 1);
        if(!empty(json_decode($html))){
            $object=json_decode($html);
            $parts=$object->adeo->detail;
            if(!empty($parts)){
                $a=[];
                foreach ($parts as $part){
                    $b=[];
                    $b['artikul']=$part->article;
                    $b['brand']=$part->producer;
                    $b['description']=$part->ident;
                    $a[]=$b;
                }
                return $a;
            }else{
                return false;
            }
        }else{
            return false;
        }
        
    }
    public function step2($uid, $partNumber='') {
        $url='http://trinity-parts.ru/user/ajax/search-by-code-brand-adeo';
        $params='login='.$this->login.'&password='.$this->pass.'&do_member=on&member=forever&code='.$partNumber.'&producer='.$uid;
        $html=getPage($url, $params, 'trinity', 1);
        $parts=json_decode($html);
        if(!empty($parts)){
            if(!empty($parts->adeo->Прайс))
                $parts_origin=$parts->adeo->Прайс;
            if(!empty($parts->adeo->Замены))
                $parts_replacement=$parts->adeo->Замены;
            $result=[];
            if(!empty($parts_origin)){
                foreach($parts_origin as $part){
                    $a=[];
                    $a['brand']=$part->producer;
                    $a['name']=$part->caption;
                    $a['searchArtikul']=stringSanitize($part->code);
                    $a['artikul']=stringSanitize($part->code);
                    $a['uid']=  $part->b_id;
                    $a['origin']=1;
                    $a['minparties']=$part->minOrderCount;
                    $a['minperiod']=$this->dataConvert($part->deliveryDisplay)[0];
                    $a['maxperiod']=$this->dataConvert($part->deliveryDisplay)[1];
                    $a['description']=$part->caption;
                    $a['quantity']=$part->rest*1;
                    $a['price']= procents($part->price); //делаем наценку
                    $a['chanceOfDelivery']='н/д';
                    $a['OfferName']=$part->stock;
                    $a['Store']=$part->RegionName;
                    $a['supplierId']=2;
                    $result[]=$a;
                }
            }
            if(!empty($parts_replacement)){
                foreach($parts_replacement as $part){
                    $a=[];
                    $a['brand']=$part->producer;
                    $a['name']=$part->caption;
                    $a['searchArtikul']=stringSanitize($part->code);
                    $a['artikul']=stringSanitize($part->code);
                    $a['uid']=  $part->b_id;
                    $a['origin']=0;
                    $a['minparties']=$part->minOrderCount;
                    $a['minperiod']=$this->dataConvert($part->deliveryDisplay)[0];
                    $a['maxperiod']=$this->dataConvert($part->deliveryDisplay)[1];
                    $a['description']=$part->caption;
                    $a['quantity']=$part->rest*1;
                    $a['price']= procents($part->price); //делаем наценку
                    $a['chanceOfDelivery']='н/д';
                    $a['OfferName']=$part->stock;
                    $a['Store']=$part->RegionName;
                    $a['supplierId']=2;
                    $result[]=$a;
                }
            }
            return $result;
        }else{
            return false;
        }
    }
    public function searchalloffer($partNumber) {
        $parts=$this->step1($partNumber);
        if(!empty($parts)){
            $result=[];
            foreach($parts as $part){
                $data=$this->step2($part['brand'], $part['artikul']); 
                $result[]=$data;
            }
            return $result;
        }else{
            return [];
        }
    }*/
    public function addbacket($uid, $quantity, $price, $comment=''){
        
    }
    public function editbacket($uid, $quantity){}
    public function clearbacket(){}
    public function deletebacket($uid){}
    public function getbacket(){}
    public function makeorder($uid, $quantity, $price, $comment=''){}
    
    
    /**
     * Метод преобразует срок доставки и возвращает в виде индексированного массива
     * @param string $string
     * @return array
     */
    private function dataConvert($string){
        if(strpos($string, '-')){
            $period=explode('-', $string);
        }elseif(strpos($string, '/')){
            $period=explode('/', $string);
        }elseif(strpos($string, '\\')){
            $period=explode('\\', $string);
        }
       return $period; 
    }
    /**
     * возвращает информацию по поставщику
     * @param string $deliveryId
     */
    private function deliveryInfo($deliveryId){
        return self::$client->DeliveryInfo(array(
                        'ClientCode' => $this->key,
                        'DeliveryID' => $deliveryId
                    ));
    }
    /**
     * метод для авторизации в тринити через веб-интерфейс
     */
    public function trinityGetBacket(){
        
        $url='http://trinity-parts.ru/user/order';
        $params='login='.$this->login.'&password='.$this->pass.'&do_member=on&member=forever';
        //getPage($url, $params, 'trinity', 1);
        $html=getPage($url, $params, 'trinity', 1);
        if(!empty($html)){
            $this->load->helper('simplehtmldom');
            $h = str_get_html($html);
            $rows=$h->find('div.manager-items table tr');
            $array=[];
            foreach($rows as $row){
                $tds=$row->find('td');
                $a=[];
                $a['artikul']=trim($tds[1]->plaintext);
                $a['brand']=trim($tds[3]->plaintext);
                $a['quantity']=trim($tds[5]->find('input')[0]->value);
                $a['price']=str_replace(',','.', trim(str_replace(' ',  '', $tds[6]->plaintext)))*1;
                $a['uid']=$row->getAttribute('data-id');
                $array[]=$a;
            }
            return $array;
        }
    }
    
    public function sendInVork($uid){
        $url='http://trinity-parts.ru/user/order/send-new/';
        $url1='http://trinity-parts.ru/user/order/check-change-basket-set/';
        $url2='http://trinity-parts.ru/user/order/check-placememt-availability-by-balance-sum/';
        $url3='http://trinity-parts.ru/user/order/check-goods-actuality/';
        $params='login='.$this->login.'&password='.$this->pass.'&do_member=on&member=forever&actualise=1&after_before=before&selected_items[]='.$uid;
        getPage($url1, $params, 'trinity', 1);
        getPage($url2, $params, 'trinity', 1);
        getPage($url3, $params, 'trinity', 1);
        $html=getPage($url, $params, 'trinity', 1);
        $json=json_decode($html);
        if(!empty($json->OrderNumber)){
            return $json->OrderNumber*1;
        }
    }
    
    
    public function step1($partNumber){
        try{
            $result=self::$client->SearchByTranscription([
                'ClientCode' => $this->key,
                'Transcription' => $partNumber,
                'ShowOff' => 1000,
                'ShowNumber' => 10000
            ]);
        } catch (SoapFault $e){
            var_dump($e); //вот тут надо повесить логгирование
        }
        var_dump($result);exit;
        if(!empty($result->return->ElSearchResults))
            return $result->return->ElSearchResults;
        else {
            return false;
        }
    }
    public function step2($uid, $partNumber=''){
        $result=self::$client->SearchByBrands([
            'ClientCode' => $this->key,
            'TranscriptionBrand' => "" .$partNumber. ":" . $uid . ";", //Вместо бденда во втром парамере будеn UID
            'ShowOff' => 1,
            'ShowNumber' => 10000
        ]);
        $return=[];
        if(!empty($result->return->ElSearchResults)){
            $item=$result->return->ElSearchResults;
        }
        if(!empty($item)){
            if (gettype($item) == 'array') {
                foreach ($item as $b) {
                    $b=(array)$b;
                    $a=[];
                    $a['brand']=$b['Brand'];
                    $a['name']=$b['Name'];
                    $a['searchArtikul']=stringSanitize($partNumber);
                    $a['artikul']=stringSanitize($b['Transcription']);
                    $a['uid']=  $b['Price_ID'];
                    $a['origin']=($a['searchArtikul']==$a['artikul'])?1:0;
                    $a['minparties']=$b['Multiplicity'];
                    $a['minperiod']=$this->dataConvert($b['DeliveryDate'])[0];
                    $a['maxperiod']=$this->dataConvert($b['DeliveryDate'])[1];
                    $a['description']=$b['Name'];
                    $a['quantity']=$b['Balance'];
                    $a['price']= procents($b['Price']); //делаем наценку
                    $a['chanceOfDelivery']=$this->deliveryInfo($b['Delivery']);
                    $a['OfferName']=$b['Delivery'];
                    $a['Store']=$b['Store'];
                    $a['supplierId']=2;
                    $return[]=$a;
                } 
            }else{
                $b=(array)$item;
                $a=[];
                $a['brand']=$b['Brand'];
                $a['name']=$b['Name'];
                $a['searchArtikul']=stringSanitize($partNumber);
                $a['artikul']=stringSanitize($b['Transcription']);
                $a['uid']=  $b['Price_ID'];
                $a['origin']=($a['searchArtikul']==$a['artikul'])?1:0;
                $a['minparties']=$b['Multiplicity'];
                $a['minperiod']=$this->dataConvert($b['DeliveryDate'])[0];
                $a['maxperiod']=$this->dataConvert($b['DeliveryDate'])[1];
                $a['description']=$b['Name'];
                $a['quantity']=$b['Balance'];
                $a['price']= procents($b['Price']); //делаем наценку
                $a['chanceOfDelivery']=$this->deliveryInfo($b['Delivery']);
                $a['OfferName']=$b['Delivery'];
                $a['Store']=$b['Store'];
                $a['supplierId']=2;
                $return[]=$a;
            }
            
        }
        return $return;
    }
    public function searchalloffer($partNumber){
        $searchResult=$this->step1($partNumber);
        
        if(empty($searchResult))
            return false;
        $result=[];
        /**
         * Потом надо добавить эту хуйню на бету
         */
        if(!empty($searchResult->Brand)){
            $result=$this->step2($searchResult->Brand, $searchResult->Transcription);
        }else{
            foreach ($searchResult as $sr){
               $a=$this->step2($sr->Brand, $sr->Transcription);
               if(!empty($a)){
                   $result=array_merge($result, $a);
               }else{
                   continue;
               }
            }
        
        }
        return $result;
    }
}
?>
