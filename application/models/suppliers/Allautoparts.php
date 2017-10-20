<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
get_instance()->load->iface('supplier'); //подгружаем интерфейс поставщиков /interfaces/supplier.php
/**
 * Класс для работы с поставщиком allautoparts
 * 
 */

class Allautoparts extends CI_Model implements Supplier {

    private static $client;
    private static $_inited = false;
    private $login;
    private $pass;
    private $ssid;

    public function __construct() {
        parent::__construct();
        $this->config->load('suppliers'); /* конфиг с данными поставщиков */
        $this->login = $this->config->item('allautoparts')['login'];
        $this->pass = $this->config->item('allautoparts')['password'];
        $this->ssid = $this->config->item('allautoparts')['ssid'];
        $this->load->helper('procents');
        /**
         * Подключаемся к вебсервису
         */
        try {
            self::$client = new SoapClient('https://allautoparts.ru/WEBService/SearchService.svc/wsdl?wsdl', ['soap_version' => SOAP_1_1, 'stream_context' => stream_context_create([
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    ],
                ])
            ]);
            self::$_inited = true;
        } catch (Exception $e) {
            self::$_inited = false;
            return false;
        }
        return true;
    }

    /**
     * Метод первого шага для двухшагового поиска
     * @param string $partNumber Искомый артикул
     * @return array
     */
    public function step1($partNumber) {
        
    }

    /**
     * Метод второго шага для двухшагового поиска
     * @param string $uid Уникальный идентификатор детали
     * @param string $partNumber Искомый номер
     * @return array
     */
    public function step2($uid, $partNumber = '') {
        
    }

    public function searchalloffer($partNumber) {
        $result = $this->getParts($partNumber); //полученный результат запроса
        $result = $this->parseSearchResponseXML($result); //SimpleXML to Array
        if (!empty($result)) {
            $items = [];
            foreach ($result as $r) {
                $r = (array) $r;
                $a = [];
                $a['raw_data'] = $r;
                $a['brand'] = $r['AnalogueManufacturerName'];
                $a['name'] = $r['ProductName'];
                $a['searchArtikul'] = stringSanitize($partNumber);
                $a['artikul'] = stringSanitize($r['AnalogueCodeAsIs']);
                $a['uid'] = md5(implode($r));
                $a['origin'] = (int) !$r['IsCross'];
                $a['minparties'] = $r['LotBase'];
                $a['minperiod'] = $r['PeriodMin'];
                $a['maxperiod'] = $r['PeriodMax'];
                $a['description'] = $r['ProductName'];
                $a['quantity'] = $r['Quantity'];
                $a['price'] = procents($r['Price']); //делаем наценку
                $a['chanceOfDelivery'] = (int) $r['statistic'];
                $a['OfferName'] = $r['OfferName'];
                $a['PriceListDiscountCode'] = $r['PriceListDiscountCode'];
                $a['LotType'] = $r['LotType'];
                $a['LotBase'] = $r['LotBase'];
                $a['supplierId'] = 2;
                $items[] = $a;
                $this->cachemodel->save($a['uid'], $a, 86400); //сохраняем пришедшее от поставщика в кэше для добавления в корзину
            }
            return $items;
        } else {
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
    public function addbacket($uid, $quantity, $price, $comment = '') {
        
    }

    /**
     * Метод изменяет деталь в корзине
     * @param string $uid Уникальный идентификатор детали
     * @param int $quantity Количество
     * @return void
     */
    public function editbacket($uid, $quantity) {
        
    }

    /**
     * Метод очищает корзину
     * @return bool
     */
    public function clearbacket() {
        
    }

    /**
     * Метод удаляет деталь из корзины
     * @param string $uid Уникальный идентификатор детали
     * @return void
     */
    public function deletebacket($uid) {
        
    }

    /**
     * Метод возвращает позиции в корзине
     * @return array
     */
    public function getbacket() {
        
    }

    /**
     * Метод отправляет заказ в корзине в работу
     * @param string $uid Уникальный идентификатор
     * @param int $quantity
     * @param float $price
     * @param string $comment
     * @return void
     */
    public function makeorder($uid, $quantity, $price, $comment = '') {
        
    }

    ###############################################################################  

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
    public function query($method, $requestData, &$errors) {
        //Инициализация
        if (!self::$_inited) {
            $errors[] = 'Ошибка соединения с сервером Автостэлс: Не может быть инициализирован класс SoapClient';
            return false;
        }
        
        //Выполнение запроса
        try {


            $result = self::$client->$method($requestData);
        } catch (Exception $e) {
            var_dump($e);
            exit;
        }
        $resultKey = $method . 'Result';

        //Проверка ответа на соответствие формату XML
        try {
            $XML = new SimpleXMLElement($result->$resultKey);
        } catch (Exception $e) {
            $errors[] = 'Ошибка сервиса Автоселс: полученные данные не являются корректным XML';
            return false;
        }

        //Проверка ответа на ошибки
        if (isset($XML->error)) {
            $errors[] = 'Ошибка сервиса Автоселс: ' . (string) $XML->error->message;
            if ((string) $XML->error->stacktrace)
                $errors[] = 'Отладочная информация: ' . (string) $XML->error->stacktrace;
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
    public function close() {
        if (self::$_inited) {
            self::$_inited = false;
            self::$client = false;
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
            $errors[] = 'Необходимо ввести логин и пароль' . $data['session_guid'];

        $data['instock'] = $data['instock'] ? 1 : 0;
        $data['showcross'] = $data['showcross'] ? 1 : 0;
        $data['periodmin'] = $data['periodmin'] ? (int) $data['periodmin'] : -1;
        $data['periodmax'] = $data['periodmax'] ? (int) $data['periodmax'] : -1;

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
        $session_info = 'UserLogin="' . base64_encode($data['session_login']) . '" UserPass="' . base64_encode($data['session_password']) . '"';

        $xml = '<root>
					  <SessionInfo ParentID="' . $data['session_id'] . '" ' . $session_info . '/>
					  <search>
						 <skeys>
							<skey>' . $data['search_code'] . '</skey>
						 </skeys>
						 <instock>' . $data['instock'] . '</instock>
						 <showcross>' . $data['showcross'] . '</showcross>
						 <periodmin>' . $data['periodmin'] . '</periodmin>
						 <periodmax>' . $data['periodmax'] . '</periodmax>
					  </search>
					</root>';
        /* echo $xml; */
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
        $data['session_id'] = $this->ssid;
        $data['session_login'] = $this->login;
        $data['session_password'] = $this->pass;

        $session_info = (!empty($data['session_guid'])) ?
                'SessionGUID="' . $data['session_guid'] . '"' :
                'UserLogin="' . base64_encode($data['session_login']) . '" UserPass="' . base64_encode($data['session_password']) . '"';
        $xml = '<root>
					 <SessionInfo ParentID="' . $data['session_id'] . '" ' . $session_info . ' />
					 <rows>
						<row>
							<Reference>' . $data['Reference'] . '</Reference>
							<AnalogueCodeAsIs>' . $data['AnalogueCodeAsIs'] . '</AnalogueCodeAsIs>
							<AnalogueManufacturerName>' . $data['AnalogueManufacturerName'] . '</AnalogueManufacturerName>
							<OfferName>' . $data['OfferName'] . '</OfferName>
							<LotBase>' . $data['LotBase'] . '</LotBase>
							<LotType>' . $data['LotType'] . '</LotType>
							<PriceListDiscountCode>' . $data['PriceListDiscountCode'] . '</PriceListDiscountCode>
							<Price>' . $data['Price'] . '</Price>
							<Quantity>' . $data['Quantity'] . '</Quantity>
							<PeriodMin>' . $data['PeriodMin'] . '</PeriodMin>
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
        foreach ($xml->rows->row as $row) {
            $_row = array();
            foreach ($row as $key => $field) {
                if ($key == "statistic") {
                    //расчитаем вероятность отгрузки
                    //всего обращений 
                    $_all = $field['success'] + $field['deny'] + $field['partial'];
                    //один процент
                    $_one = $_all / 100;
                    //всего успешных
                    $_success = round($_one * $field['success']);
                    $_row[(string) $key] = $_success;
                } else {
                    $_row[(string) $key] = (string) $field;
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
        foreach ($xml->rows->row as $row) {
            $_row = array();
            foreach ($row as $key => $field) {
                $_row[(string) $key] = (string) $field;
            }
            $data[] = $_row;
        }
        return $data;
    }

    public function getParts($partNum, $cross = true) {
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
        $data['session_id'] = $this->ssid;
        $data['session_login'] = $this->login;
        $data['session_password'] = $this->pass;
        $data['search_code'] = $partNum;
        $data['instock'] = '1';
        if ($cross) {

            $data['showcross'] = '1';
        } else {
            $data['showcross'] = '0';
        }
        $data['periodmin'] = '-1';
        $data['periodmax'] = '100';

        $errors = array();
        $parsed_data = $data; //Данные из формы копируются в другую переменную, чтобы 
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
            $data['session_guid'] = (string) $attr['SessionGUID'];

            //Разбор данных ответа
            $result = $this->parseSearchResponseXML($responceXML);
            //var_dump($result); exit;
            return $responceXML;
        }
    }

    /**
     * @param int $id Ном ер в заказах
     * @param int $provider_id Внутренний номер поаставщика
     * @param string $comment (optional) Коментарий к заказу
     * @return bool Возвращает true в случае успеха, false в случае ошибки
     */
    public function createMakeOrderXML($id, $provider_id, $comment = '') {
        $xml = '<root>
                    <SessionInfo ParentID="' . $this->ssid . '" UserLogin="' . base64_encode($this->login) . '" UserPass="' . base64_encode($this->pass) . '"/>
                    <Order number="STS2-' . $id . '">
                    <Comment>' . $comment . '</Comment>
                    <Details>
                    <RowID>' . $provider_id . '</RowID>
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
    public function createDeleteBacketXml($provider_id) {
        $xml = '<root>
                    <SessionInfo ParentID="' . $this->ssid . '" UserLogin="' . base64_encode($this->login) . '" UserPass="' . base64_encode($this->pass) . '" />
                    <Details>
                    <Detail>
                    <RowID>' . $provider_id . '</RowID>
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
    public function createOrdersInWork($arrid) {
        $orderNum = null;
        foreach ($arrid as $id) {
            $orderNum .= '<orderNum>STS2-' . $id . '</orderNum>';
        }
        $xml = '<root>
                        <SessionInfo ParentID="' . $this->ssid . '" UserLogin="' . base64_encode($this->login) . '" UserPass="' . base64_encode($this->pass) . '"/>
                        <parameters>
                        <states>

                        </states>
                        <orderNums>
                              ' . $orderNum . '
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
