<?php

/**
 * @author MDTreloni
 * @email mixasmix@mail.ru 
 * @copyright 2015
 */
require_once('simpledom/simple_html_dom.php');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Json extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->load->helper('getpage');
		$this->sql=SQL::getInstance();
                $this->sql->query("SET NAMES 'utf8';");
	}
        
        /**
         * Метод возвращает результат поиска от всех поставщиков
         * @param string $partNumber запрашиваемый артикул
         * @return array Массив с результатами поиска
         */
        public function getPartsAllSuppliers($partNumber){
            $this->load->model('brandmodel');
            $array=[];
            $this->load->model('suppliers');
            $array= $this->suppliers->getparts($partNumber);
            
            
            /**
             * Обходим массив и получаем ID бренда и 
             */
            $newarray=[];
            foreach($array as $a){
                $a['bid']=$this->brandmodel->getBrandId($a['brand']);
                $a['rating']=$this->brandmodel->getRating($a['bid']);
                $newarray[]=$a;
            }
            return $newarray;
        }
        
	public function getParts($oN, $s=false, $brand=''){
	//первый парам это номер детали 
	//втрой парам это номер склада
	// с 1-го по 10-спарекс 11-allautoparts
        if(empty($s)){
            $s=$this->uri->segment(4);
        }
	if($s>=1 and $s<8){
            return false;
	} elseif($s==11){
            return false;
        } elseif($s==9){
            return false;
        } elseif($s==10){
            return $this->createSkladPosition($oN);
	} elseif($s==12){
            if(!empty($brand))
                return $this->allautoparts($oN, true);
            else 
                return $this->allautoparts($oN);
	}else{
            return false;
	}
	return false;
	
	}
	/**
	функция для получения картинки с емекса
	Принимаем номер детали и производителя
	Возвращаем адрес ссылки на картинку
	*/
	public function getImage($number, $bayer){
            /**
             * Почекаем наличие картинок для данного артикула
             */
            $url="http://vag-part.ru/webservices/?flag=check&artikul=".$number.'&brand='.$bayer.'&key=59ae799555021052738682fd7f4ac9a2';
            if(getPage($url)!=1)
                return false;
            else 
                return true;
	} 
	/**
	* функция для добавления деталей в базу
	* Вызывается ajax-ом, возможно надо переделать на сокеты, а ajax-ом дергать только рисунки 
	*
	*
	*/
	public function addArtikulBase(){
            $parts=json_decode($this->input->post('parts', true)); //тут массив Parts
            if(empty($parts)) return false;
            //обходим массив
            foreach ($parts as $part){
                $q1="SELECT brands.id FROM brands WHERE brands.name=:brandname"; //Запрос на получение бренда
                $stm=$this->sql->prepare($q1);
                $stm->execute(array(':brandname'=>$part->brandName));	
                $arr=$stm->fetchAll(PDO::FETCH_ASSOC);
                if(empty($arr)){
                        //если массив пустой то добавляем бренд в базу
                        $i1="INSERT INTO brands (name) VALUES (:brandname)";
                        $stm=$this->sql->prepare($i1);
                        $stm->execute(array(':brandname'=>$part->brandName));
                        //и получаем номер последнего вставленного ID
                        $id_brand=$this->sql->lastInsertId();
                } else {
                        $id_brand=$arr[0]['id'];
                }


                $q2="SELECT id FROM artikuls WHERE artikul=:a"; //запрос на получение артикула
                $stm=$this->sql->prepare($q2);
                $stm->execute(array(':a'=>$part->artikul));
                $arr=$stm->fetchAll(PDO::FETCH_ASSOC);
                if(!empty($arr)){
                        continue;//если артикул уже в базе то перепрыгиваем дальше
                } else{
                        $i2="INSERT INTO artikuls (artikul, description) VALUES (:artikul, :description)";
                        $stm=$this->sql->prepare($i2);
                        $stm->execute(array(':artikul'=>$part->artikul, ':description'=>$part->description));
                        var_dump($stm->errorInfo());
                        //и получаем номер последнего вставленного ID
                        $id_artikul=$this->sql->lastInsertId();
                        //и добавляем в таблицу соответствия
                        $i3="INSERT INTO artikul_state (id_brand, id_artikul) VALUES (:id_brand, :id_artikul)";
                        $stm=$this->sql->prepare($i3);
                        $stm->execute(array(':id_brand'=>$id_brand, ':id_artikul'=>$id_artikul));
                }
            } 
	}
    /**
     * Метод работы с kat36
     * @param string $oN //номер детали
     * @return array Список предложений
     */
    public function kat36Service($oN, $brand=''){
        $api_key='248c7f2c-b0e0-0fa9-3b37-f48d400d23dc';
        $get='http://kat36.ru/api/v1/search/get_offers_by_oem_and_make_name?api_key='.$api_key.'&oem='.$oN.'&make_name='.$brand;
       //var_dump(getPage($get, '', '', 0, 10)); exit;
        $result=json_decode(getPage($get, '', '', 0, 10));
        $array_result=array();
        
        
         
        if(!empty($result->data)){ 
            
            foreach($result->data as $pd){
                //грязный хак. У китайцев кто то их поставщиков отдает единицу вместо нормальной цены.
                //Чтобы не выводить такие предложения сделаем этот хак
                if($pd->cost=='1'){
                    continue;
                }
                //конец хака
                $a=array();
                $a['uniqueid']=$pd->system_hash;
                $a['brandName']=$pd->make_name;
                $a['artikul']=$pd->oem;
                //$a['description']=$pd->detail_name;
                $a['description']=''.$pd->detail_name.'';
                $perMin=$pd->min_delivery_day+1; $perMax=$pd->max_delivery_day+2;
                $a['delivery_period']= $perMin.'-'.$perMax;
                $a['PeriodMin']=$pd->min_delivery_day+1;
                $a['PeriodMax']=$pd->max_delivery_day+2;
                $a['Quantity']=$pd->qnt;
                $a['chance_of_shipment']='н/д';
                $a['rpr']=$pd->cost;
                $a['price']=$pd->cost;
                $a['last_upd']=time();
                $a['origin']= 0;
                $a['minoffer']=($pd->min_qnt)?$pd->min_qnt:'1' ;
                $a['provider']='b1e590c4cf8b0a5814241aa63205c767';//здесь строка-идентификатор поставщика
                //Для аллавтопарта надо добавить следующее
                $array_result[]=$a;
                
                
                
            }
            /*  foreach($result->data as $data){
               $pr=json_decode(getPage('http://kat36.ru/api/v1/search/get_offers_by_oem_and_make_name?api_key='.$api_key.'&oem='.$data->number.'&make_name='.$data->brand));
               
               if(!empty($pr->data)){
                    foreach($pr->data as $partdata){
                        $array_result[$partdata->oem.$partdata->make_name.$partdata->cost]=$partdata;
                        
                    }
                
                    
               }
               
           }
            var_dump($array_result); exit; 
            
            */
          
            
            
            
            
            
            
            
            
        }else{
            return false;
        }
       // var_dump($array_result);exit;
        return $array_result;
    }
    
    public function getSkladArtikul($artikul){
        $sql='SELECT t1.id, t1.artikul, t4.price, t4.count, t1.description, t3.`name` FROM artikuls t1
                INNER JOIN artikul_state t2 ON t1.id=t2.id_artikul
                INNER JOIN brands t3 ON t2.id_brand=t3.id 
                INNER JOIN artikul_availability t4 ON t4.id_artikul=t2.id_artikul WHERE t1.artikul=:artikul AND t4.count!=0';
        $stm=$this->sql->prepare($sql);
        $stm->execute(array(':artikul'=>$artikul));
        $arr=$stm->fetchAll(PDO::FETCH_ASSOC );
        if(!empty($arr)){
            return $arr[0];
        }else{
            return false;
}
    }
    public function createSkladPosition($artikul){
        $getSkladArtikul=$this->getSkladArtikul($artikul);
       
        if(!empty($getSkladArtikul)){
            $a=array();
            $a['uniqueid']=$getSkladArtikul['id'];
            $a['brandName']=$getSkladArtikul['name'];
            $a['artikul']=$getSkladArtikul['artikul'];
            $a['description']=$getSkladArtikul['description'];
            $a['delivery_period']= 'В наличии';
            $a['PeriodMin']=0;
            $a['PeriodMax']=0;
            $a['Quantity']=$getSkladArtikul['count'];
            $a['chance_of_shipment']='100';
            $a['rpr']=$getSkladArtikul['price'];
            $a['price']=$getSkladArtikul['price'];
            $a['last_upd']=time();
            $a['origin']= 0;
            $a['minoffer']= 1;
            return array($a);
        }else{
            return false;
        }
    }
    
    public function autopiter($partNumber){
        $client=new SoapClient('http://service.autopiter.ru/price.asmx?WSDL', ['soap_version' => SOAP_1_2]);
        $client->Authorization(array("UserID"=>'281594', "Password"=>'q15wer88', "Save"=> "true"));
         try{
                        $result=$client->FindCatalog (array("ShortNumberDetail"=>$partNumber))->FindCatalogResult->SearchedTheCatalog;

        }catch(Exception $e){
            return false;
        }
        $a=[];
         if(count($result)>1){
            foreach($result as $res){
                $b=$client->GetPriceId(array("ID"=>$res->id, "FormatCurrency" => 'РУБ', "SearchCross"=>1,"IdArticleDetail"=>null))->GetPriceIdResult->BasePriceForClient;
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
             $b=(array)$client->GetPriceId(array("ID"=>$result->id, "FormatCurrency" => 'РУБ', "SearchCross"=>1,"IdArticleDetail"=>null))->GetPriceIdResult->BasePriceForClient;
             foreach($b as $v){
                 $c[]=(array)$v;
             }
             $a= $c;
         }
         if(!empty($a)){
             $result_array=[];
             foreach ($a as $c){
                $item=array();
                $item['uniqueid']=$c['IdDetail'];
                $item['brandName']=$c['NameOfCatalog'];
                $item['artikul']=$c['Number'];
                $item['description']=$c['NameRus'];
                $perMin=$c['NumberOfDaysSupply ']+1; $perMax=$c['NumberOfDaysSupply ']+2;
                $item['delivery_period']= $perMin.'-'.$perMax.'.';
                $item['PeriodMin']=$perMin;
                $item['PeriodMax']=$perMax;
                $item['nal']=$c['NumberOfAvailable'];
                $item['chance_of_shipment']=$c['RealTimeInProc'];
                $price=$c['SalePrice'];
                $item['rpr']=$c['SalePrice'];
                $item['price']=$price;
                $item['last_upd']=time();
                $item['origin']= $c['SearchNum'];
                $item['minoffer']= $c['MinNumberOfSales'];
                $item['provider']='eharewawrehjwaerjhiahiohio';//здесь строка-идентификатор поставщика
                //Для аллавтопарта надо добавить следующее
                $result_array[]=$item;
             }
         }else{
             return false;
         }
        return $result_array;
    }
}
?>