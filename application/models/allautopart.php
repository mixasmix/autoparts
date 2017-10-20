<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Allautopart extends CI_Model {
    public $wsdl_uri = 'https://allautoparts.ru/WEBService/SearchService.svc/wsdl?wsdl';   //Ссылка на WSDL-документ сервиса
    private static $_soap_client = false;                                                    //Объект SOAP-клиента
    private static $_inited = false;
    private $login='STS2RU';
    private $pass='F5v89e4D41d';
    private $ssid='2768';
   
	
	public function __construct(){
		parent::__construct();
	}
		
	


   /**  
    * init
    * 
    * Инициализирует класс, создаёт объект SOAP-клиента и открывает соединение
    * 
    * @param &array $errors ссылка на текущий массив ошибок
    * @return true в случае успеха, false при ошибке
    */
    public function init(&$errors)
    {
      if(!self::$_inited)
      {
         try
         {
           if (self::$_soap_client = @new SoapClient($this->wsdl_uri, array('soap_version' => SOAP_1_1)))
               self::$_inited = true;
         }
         catch (Exception $e)
         {
            $errors[] = 'Произошла ошибка связи с сервером Автостэлс. '.$e->getMessage();
            return false;
         }
      }
      return self::$_inited;
    }

    /**  
     * query
     * 
     * Выполняет запрашиваемый метод сервиса
     * 
     * @param string $method имя метода
     * @param string $requestData данные запроса
     * @param &array $errors ссылка на текущий массив ошибок
     * @return объект SimpleXMLElement в случае успеха, false при ошибке
     */
    public function query($method, $requestData, &$errors)
    {
      //Инициализация
      if (!$this->init($errors))
      {
        $errors[] = 'Ошибка соединения с сервером Автостэлс: Не может быть инициализирован класс SoapClient';
        return false;
      }
      //Выполнение запроса
      $result =  self::$_soap_client->$method($requestData);
      $resultKey = $method.'Result';
      
      //Проверка ответа на соответствие формату XML
      try
      {
        $XML = new SimpleXMLElement($result->$resultKey);
        //var_dump($XML); exit;
      }
      catch (Exception $e)
      {
        $errors[] = 'Ошибка сервиса Автоселс: полученные данные не являются корректным XML';
        return false;
      }
      
      //Проверка ответа на ошибки
      if(isset($XML->error)) {
        $errors[] = 'Ошибка сервиса Автоселс: '.(string)$XML->error->message;
        if ((string)$XML->error->stacktrace)
          $errors[] = 'Отладочная информация: '.(string)$XML->error->stacktrace;
        return false;
      }
      
      //Закрытие соединение
      $this->close();
      
      return $XML;
    }
    
    /**  
     * close
     * 
     * Закрывает соединение
     * 
     * @param void
     * @return void
     */
    public function close()
    {
      if( self::$_inited )
      {
        self::$_inited = false;
        self::$_soap_client = false;
      }
    }































	

	/**  
	 * Вспомогательные функции
	 */
	   
	   /**
		* generateRandom
		* 
		* Генерирует случайную строку из чисел заданой длины
		* 
		* @param int $maxlen длина строки
		* @return string
		*/
	  public function generateRandom($maxlen = 32) {
		  $code = '';
		  while (strlen($code) < $maxlen) {
			 $code .= mt_rand(0, 9);
		  }
		  return $code;
	   }

		/**  
		* validateData
		* 
		* Фунцкия производит проверку и подготовку данных для отправки в запрос
		* 
		* @param &array $data ссылка на ассоц. массив с данными
		* @param &array $errors ссылка на массив ошибок
		* @return true в случае, если данные корректны, false при ошибке
		*/
		public function validateData(&$data, &$errors) {
			if (!$data['search_code'])
				$errors[] = 'Необходимо ввести номер для поиска';
			
			if (!$data['session_id'])
				$errors[] = 'Необходимо указать ID входа для работы с сервисом';
			
			if ((!$data['session_login'] || !$data['session_password']) && !$data['session_guid'])
				$errors[] = 'Необходимо ввести логин и пароль'.$data['session_guid'];
				
			$data['instock'] = $data['instock'] ? 1 : 0;
			$data['showcross'] = $data['showcross'] ? 1 : 0;
			$data['periodmin'] = $data['periodmin'] ? (int)$data['periodmin'] : -1;
			$data['periodmax'] = $data['periodmax'] ? (int)$data['periodmax'] : -1;
			
			return count($errors) ? false : true;
		}
		
		/**  
		* createSearchRequestXML
		* 
		* Генерация строки запроса на поиск
		* 
		* @param &array $data ссылка на ассоц. массив с данными
		* @return string возвращает строку с XML
		*/
		public function createSearchRequestXML($data) {
			$session_info = 'UserLogin="'.base64_encode($data['session_login']).'" UserPass="'.base64_encode($data['session_password']).'"';
			
			$xml = '<root>
					  <SessionInfo ParentID="'.$data['session_id'].'" '.$session_info.'/>
					  <search>
						 <skeys>
							<skey>'.$data['search_code'].'</skey>
						 </skeys>
						 <instock>'.$data['instock'].'</instock>
						 <showcross>'.$data['showcross'].'</showcross>
						 <periodmin>'.$data['periodmin'].'</periodmin>
						 <periodmax>'.$data['periodmax'].'</periodmax>
					  </search>
					</root>';
					/*echo $xml;*/
			return $xml;
		}
	   
	   /**  
		* createAddBasketRequestXML
		* 
		* Генерация строки запроса на добавление в корзину
		* 
		* @param &array $data ссылка на ассоц. массив с данными
		* @return string возвращает строку с XML
		*/
	  public function createAddBasketRequestXML($data) {
                $data['session_id']=  $this->ssid;
		$data['session_login']=  $this->login;
		$data['session_password']=  $this->pass;
                
		  $session_info = (!empty($data['session_guid'])) ? 
				'SessionGUID="'.$data['session_guid'].'"' : 
				'UserLogin="'.base64_encode($data['session_login']).'" UserPass="'.base64_encode($data['session_password']).'"';
			$xml = '<root>
					 <SessionInfo ParentID="'.$data['session_id'].'" '.$session_info.' />
					 <rows>
						<row>
							<Reference>'.$data['Reference'].'</Reference>
							<AnalogueCodeAsIs>'.$data['AnalogueCodeAsIs'].'</AnalogueCodeAsIs>
							<AnalogueManufacturerName>'.$data['AnalogueManufacturerName'].'</AnalogueManufacturerName>
							<OfferName>'.$data['OfferName'].'</OfferName>
							<LotBase>'.$data['LotBase'].'</LotBase>
							<LotType>'.$data['LotType'].'</LotType>
							<PriceListDiscountCode>'.$data['PriceListDiscountCode'].'</PriceListDiscountCode>
							<Price>'.$data['Price'].'</Price>
							<Quantity>'.$data['Quantity'].'</Quantity>
							<PeriodMin>'.$data['PeriodMin'].'</PeriodMin>
							<ConstraintPriceUp>-1</ConstraintPriceUp>
							<ConstraintPeriodMinUp>-1</ConstraintPeriodMinUp>
						</row>
					 </rows>	 
					</root>';
			return $xml;
	   }
		
		/**  
		* parseSearchResponseXML
		* 
		* Разбор ответа сервиса поиска.
		* 
		 * Собственно просто преобразует данные из SimpleXMLObject в массив,
		* также добавляет к каждой записи уникальный ReferenceID. В данном примере
		* в этом качестве будет выступать случайным образом сгенерированная строка.
		* В реальном использовании Reference обозначает ID конкретной записи в контексте
		* системы, в которой используются сервисы (например, id из таблицы БД, с которой 
		* сопоставлено предложение)
		 *	
		* @param SimpleXMLObject XML-объект
		* @return array возвращает массив данных
		*/
		public function parseSearchResponseXML($xml) {
			$data = array();
			foreach($xml->rows->row as $row) {
				$_row = array();
				foreach($row as $key => $field) {
					if($key=="statistic"){
						//расчитаем вероятность отгрузки
						
						//всего обращений 
						$_all=$field['success']+$field['deny']+$field['partial'];
						//один процент
						$_one=$_all/100;
						//всего успешных
						$_success=round($_one*$field['success']);
						$_row[(string)$key] =$_success;
					}else {
						$_row[(string)$key] = (string)$field;
					}
				}
			 $_row['Reference'] = $this->generateRandom(9);
				$data[] = $_row;
			}
			return $data;
		}
	   
	   
	   /**
		* parseAddBasketResponseXML
		* 
		* Разбор ответа сервиса добавления в корзину.
		* Ответ содержит набор строк с результатами размещения выбранные позиций
		* В этом примере разбор ответа сводится к простой конвертации результата в массив.
		* Интерпретация и вывод результата происходит в файле /html/result_basket.html
		* 
		* @param SimpleXMLObject $xml XML-объект
		* @return array возвращает массив с результатами
		*/
	   public function parseAddBasketResponseXML($xml) {
		  $data = array();
			foreach($xml->rows->row as $row) {
				$_row = array();
				foreach($row as $key => $field) {
					$_row[(string)$key] = (string)$field;
				}
				$data[] = $_row;
			}
			return $data;
	   }
	public function getParts($partNum, $cross=true){
	/**  
	 * Основное тело скрипта
	 */
		
		//Обработка входных данных:
		//Значения формы по-умолчанию
		$defaults = array(
			'session_id' => '',
			'session_guid' => '',
			'session_login' => '',
			'session_password' => '',
			'search_code' => 'OC47',
			'instock' => 'ON',
			'showcross' => '',
			'periodmin' => 0,
			'periodmax' => 10,
		);
		
		//Получение POST данных
		$data = array(); 
		$data['session_id']= $this->ssid;
		$data['session_login']= $this->login;
		$data['session_password']=$this->pass;
		$data['search_code']=$partNum;
		$data['instock']='1';
                if($cross){
                    
                    $data['showcross']='1';
                }else{
                    $data['showcross']='0';
                }
		$data['periodmin']='-1';
		$data['periodmax']='100';

		$errors = array();
		$parsed_data = $data;	//Данные из формы копируются в другую переменную, чтобы 
										//подготовить их для формирования запроса.
										//Исходные данные будут отображены на форме.
			//Генерация запроса
			$requestXMLstring = $this->createSearchRequestXML($parsed_data);
			
			//Выполнение запроса
			$responceXML = $this->query('SearchOffer3', array('SearchParametersXml' => $requestXMLstring), $errors);
			
			//Получен ответ
			if ($responceXML) {
				//Установка параметра session_guid, полученного из ответа сервиса.
				//Параметр используется, как замена связке session_login + session_password,
				//и при повторном поиске может быть подставлен в запрос вместо неё
				$attr = $responceXML->rows->attributes();
				$data['session_guid'] = (string)$attr['SessionGUID'];
				
				//Разбор данных ответа
				$result = $this->parseSearchResponseXML($responceXML);
				
				return $responceXML;
                               
			}
				
		 
	}
        /**
         * @param int $id Ном ер в заказах
         * @param int $provider_id Внутренний номер поаставщика
         * @param string $comment (optional) Коментарий к заказу
         * @return bool Возвращает true в случае успеха, false в случае ошибки
         */
        public function createMakeOrderXML($id, $provider_id, $comment=''){
            $xml='<root>
                    <SessionInfo ParentID="'.$this->ssid.'" UserLogin="'.base64_encode($this->login).'" UserPass="'.base64_encode($this->pass).'"/>
                    <Order number="STS2-'.$id.'">
                    <Comment>'.$comment.'</Comment>
                    <Details>
                    <RowID>'.$provider_id.'</RowID>
                    </Details>
                    <Constraint>
                    <PeriodMinUp>-1</PeriodMinUp>
                    <PriceUp>-1</PriceUp>
                    <DeliveryChange>0</DeliveryChange>
                    <PutAll>0</PutAll>
                    </Constraint>
                    </Order>
                   </root>';
            return $xml;
        }
        /**
         * Формирует XML удаляющий заказ из корзины
         * 
         * @param string $provider_id
         * @return string Возвращает строку-xml
         */
         public function createDeleteBacketXml($provider_id){
             $xml='<root>
                    <SessionInfo ParentID="'.$this->ssid.'" UserLogin="'.base64_encode($this->login).'" UserPass="'.base64_encode($this->pass).'" />
                    <Details>
                    <Detail>
                    <RowID>'.$provider_id.'</RowID>
                    </Detail>
                    </Details>
                   </root>';
             return $xml;
         }
         /**
         * @param int $id Номер в заказах
         * @param int $provider_id Внутренний номер поаставщика
         * @param string $comment (optional) Коментарий к заказу
         * @return bool Возвращает true в случае успеха, false в случае ошибки
         */
        public function createOrdersInWork($arrid){
            $orderNum=null;
            foreach($arrid as $id){
                $orderNum.='<orderNum>STS2-'.$id.'</orderNum>';
            }
            $xml='<root>
                        <SessionInfo ParentID="'.$this->ssid.'" UserLogin="'.base64_encode($this->login).'" UserPass="'.base64_encode($this->pass).'"/>
                        <parameters>
                        <states>

                        </states>
                        <orderNums>
                              '.$orderNum.'
                               </orderNums>
                        <stocksonly>0</stocksonly>

                        <searchArticles>

                        </searchArticles>
                        <sort>cost ASC</sort>
                        <page>
                        <pageNumber>1</pageNumber>
                        <pageLength>100</pageLength>
                        </page>
                        </parameters>
                       </root>';
            return $xml;
        }
        
}
?>