<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
get_instance()->load->iface('supplier'); //подгружаем интерфейс поставщиков /interfaces/supplier.php
/**
* Класс для работы с поставщиком автопитер
* 
*/
class Autopiter extends CI_Model implements Supplier {
    private $client;
    public function __construct() {
        parent::__construct();
        $this->config->load('suppliers'); /*конфиг с данными поставщиков*/
        /**
         * Подключаемся к вебсервису
         */
        $this->client=new SoapClient('http://service.autopiter.ru/price.asmx?WSDL', ['soap_version' => SOAP_1_2]);
        if (!($this->client->IsAuthorization()->IsAuthorizationResult)) {
            //http://service.autopiter.ru/price.asmx?op=Authorization
            //UserID - ваш клиентский id, Password - ваш пароль
            $this->client->Authorization(array("UserID"=>$this->config->item('autopiter')['login'], "Password"=>$this->config->item('autopiter')['password'], "Save"=> "true"));
        }

    }
    /**
     * Метод первого шага для двухшагового поиска
     * @param string $partNumber Искомый артикул
     * @return array
     */   
    public function step1($partNumber){
         $result=(array)$this->client->FindCatalog (array("ShortNumberDetail"=>$partNumber))->FindCatalogResult->SearchedTheCatalog;
        if(!empty($result)){
            $items=[];
            foreach($result as $r){
                $a=[];
                $a['brand']=$r->Name;
                $a['name']=$r->NameDetail;
                $a['searchArtikul']=$r->ShortNumber;
                $a['uid']=$r->id;
                $a['supplierId']=1;
                $items[]=$a;
            }
            return $items;
        }else{
            return false;
        }
    }
    /**
     * Метод второго шага для двухшагового поиска
     * @param string $uid Уникальный идентификатор детали
     * @param string $partNumber Искомый номер
     * @return array
     */
    public function step2($uid, $partNumber=''){
        $result= (array)$this->client->GetPriceId(array("ID"=>$uid, "FormatCurrency" => 'РУБ', "SearchCross"=>1,"IdArticleDetail"=>null))->GetPriceIdResult->BasePriceForClient;
        if(!empty($result)){
            $items=[];
            foreach($result as $r){
                $a=['brand'>$r->NameOfCatalog,'name'=>$r->NameRus,'searchArtikul'=>(!empty($partNumber))?$partNumber:'','analogCode'=>$r->Number,'uid'=>$r->IdDetail,'origin'=>$r->SearchNum,'minparties'=>$r->MinNumberOfSales,'minperiod'=>$r->NumberOfDaysSupply,'maxperiod'=>$r->NumberOfDaysSupply,'description'=>$r->NameRus,'quantity'=>$r->NumberOfAvailable,'price'=>$r->SalePrice,'chanceOfDelivery'=>$r->RealTimeInProc,'supplierId'=>1];
                $items[]=$a;
            }
            return $items;
        }else{
            return false;
        }
    }
    
    public function searchalloffer($partNumber){
        try{
                        $result=$this->client->FindCatalog (array("ShortNumberDetail"=>$partNumber))->FindCatalogResult->SearchedTheCatalog;

        }catch(Exception $e){
            return [];
        }
        $a=[];
         if(count($result)>1){
            foreach($result as $res){
                $b=$this->client->GetPriceId(array("ID"=>$res->id, "FormatCurrency" => 'РУБ', "SearchCross"=>1,"IdArticleDetail"=>null))->GetPriceIdResult->BasePriceForClient;
                $c=[];
                
                $b=(array)$b;
                //var_dump($b); exit;
                foreach($b as $v){
                    
                    $c[]=(array)$v;
                }
                $a=array_merge($a, $c);
               // var_dump($a, __LINE__); exit;
            }
         }else{
             $c=[];
             $b=(array)$this->client->GetPriceId(array("ID"=>$result->id, "FormatCurrency" => 'РУБ', "SearchCross"=>1,"IdArticleDetail"=>null))->GetPriceIdResult->BasePriceForClient;
             foreach($b as $v){
                 $c[]=(array)$v;
             }
             $a= $c;
             //var_dump($a, __LINE__); exit;
         }
         if(!empty($a)){
            $items=[];
            foreach($a as $r){
                $a=[];
                $a['brand']=$r['NameOfCatalog'];
                $a['name']=$r['NameRus'];
                $a['searchArtikul']=$partNumber;
                $a['analogCode']=$r['Number'];
                $a['uid']=$r['IdDetail'];
                $a['origin']=$r['SearchNum'];
                $a['minparties']=$r['MinNumberOfSales'];
                $a['minperiod']=$r['NumberOfDaysSupply'];
                $a['maxperiod']=$r['NumberOfDaysSupply'];
                $a['description']=$r['NameRus'];
                $a['quantity']=$r['NumberOfAvailable'];
                $a['price']=$r['SalePrice'];
                $a['chanceOfDelivery']=$r['RealTimeInProc'];
                $a['supplierId']=2;
                $items[]=$a;
            }
            return $items;
         }else{
             return [];
         }
    }
    /**
     * Метод добавляет деталь в корзину
     * @param string $uid Уникальный идентификатор детали
     * @param int $quantity Количество
     * @param float $price Цена детали
     * @param string $comment Комментарий 
     * @return void
     */
    public function addbacket($uid, $quantity, $price, $comment=''){
        $this->client->InsertToBasket(["items"=> 
                        [["Catalog"=>'',
                            "Comment"=>$comment,
                            "Cost"=>$price, 
                            "Id"=>null, 
                            "Name"=>'', 
                            "Number"=>'', 
                            "IdArticleDetail"=>$uid, 
                            "Quantity"=>$quantity]]]);
    }
    /**
     * Метод изменяет деталь в корзине
     * @param string $uid Уникальный идентификатор детали
     * @param int $quantity Количество
     * @return void
     */
    public function editbacket($uid, $quantity){
        $result=$this->client->UpdateQtyItemCart(array("id"=>$uid, "qty"=>$quantity))->UpdateQtyItemCartResult;
        $this->client->UpdateBasket();
        return $result;
    }
    /**
     * Метод очищает корзину
     * @return bool
     */
    public function clearbacket(){
        return $this->client->ClearBasket();
    }
    /**
     * Метод удаляет деталь из корзины
     * @param string $uid Уникальный идентификатор детали
     * @return void
     */
    public function deletebacket($uid){
        return $this->client->DeleteItemCart(array("id"=>$idDetail));
    }
    
    /**
     * Метод возвращает позиции в корзине
     * @return array
     */
    public function getbacket(){
        $result= (array)$this->client->GetBasket()->GetBasketResult;
        if(!empty($result)){
          $items=[];
          foreach($result as $r){
              $a=[];
              $a['brand']=trim($r->Catalog);
              $a['name']=trim($r->Name);
              $a['searchArtikul']='';
              $a['uid']=$r->IdArticleDetail;
              $a['supplierId']=1;
              $a['comment']=$r->Comment;
              $a['quantity']=$r->Quantity;
              $a['price']=$r->Cost;
              $a['artikul']=trim($r->Number);
              $items[]=$a;
          }
          return $items;
        }
    }
    /**
     * Метод отправляет заказ в корзине в работу
     * @param string $uid Уникальный идентификатор
     * @param int $quantity
     * @param float $price
     * @param string $comment
     * @return void
     */
    public function makeorder($uid, $quantity, $price, $comment=''){
         $this->client->MakeOrderByItems(["items"=> 
                            [["Catalog"=>'',
                            "Comment"=>$comment,
                            "Cost"=>$price, 
                            "Id"=>null, 
                            "Name"=>'', 
                            "Number"=>'', 
                            "IdArticleDetail"=>$uid, 
                            "Quantity"=>$quantity]]]);
         return;
    } 
}