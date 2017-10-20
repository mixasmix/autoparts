<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * @author Mixa
 * @version 2.0
 */
class Admin_delivery extends CI_Model {

    private $sql; //тут у нас класс базы данных

    public function __construct() {
        parent::__construct();
        $this->sql = SQL::getInstance();
        $this->load->helper('cookie');
        $this->sql->query("SET NAMES 'utf8';");
    }

    /*
     * ћетод удаляет заказ из списка
     */

    public function delDelivery($id) {
        //принимаем id удал¤емой из корзины запчасти
        $sql = "UPDATE delivery_state SET delivery_state.id_status=4 WHERE delivery_state.id_delivery=:id;";
        $stm = $this->sql->prepare($sql);
        $stm->execute(array(':id' => $id));
    }

    /**
     * Метод получает данные по товарам из корзины
     * @param mixed $id Принимает или id заказа или значения all(все заказы), new(новые заказы), in_work(заказы в работе)
     * @version 2.0         
     */
    public function getDelivery($id = "active") {
        if ($id == 'active') {
            $stm = $this->sql->prepare('CALL getAllActiveOrders()');
        } elseif ($id == 'archive') {
            $stm = $this->sql->prepare('CALL getAllArchiveOrders()');
        }
        $stm->execute();
        $result = $stm->fetchAll(PDO::FETCH_ASSOC);
        $stm->closeCursor();
        $stm2 = $this->sql->prepare('CALL getThisOrderList(:order_id)');
        /**
         * группируем полученный массив
         */
        if (!empty($result)) {
            $a = [];
            foreach ($result as $res) {
                $a[$res['id']] = $res;
                $stm2->execute([':order_id' => $res['id']]);
                $a[$res['id']]['order_list'] = $stm2->fetchAll(PDO::FETCH_ASSOC);
                $stm2->closeCursor();
            }
            $result = $a;
        }
        return (!empty($result)) ? $result : false;
    }

    /**
     * метод принимает массив и формирует красивую табличку
     * @deprecated 
     */
    /* public function getTable($array){
      $sql="SELECT id, name, value FROM delivery_status";
      $stm=$this->sql->prepare($sql);
      $stm->execute();
      $arr=$stm->fetchAll(PDO::FETCH_ASSOC);

      if(!empty($array)){
      $table="<a href='/panelcontrol/panelcontrol/zakaz/' class='art-button'>Активные заказы</a>&nbsp;&nbsp;<a href='/panelcontrol/panelcontrol/zakaz/archive' class='art-button'>Архив заказов</a><table border=1>";
      $table.="<tr>
      <td>Номер заказа</td>
      <td>Пользователь</td>
      <td>Заказ</td>
      <td>Телефон</td>
      <td>Адрес</td>
      <td>Время заказа</td>
      <td>Статус</td>
      <td>Удалить</td>
      <td>Печать</td>
      </tr>";
      foreach($array as $a){

      $a2=unserialize($a['delivery']);
      $table.="<tr>";
      $table.="	<td>000".$a['id_delivery']."</td>
      <td><a href='#' class='art-blockcontent'>".$a['login']."</a></td>";
      $optprice=0;
      $roznprice=0;
      if(!empty($a2)){
      $table.="<td><table style='font-size:10px;'>";
      foreach($a2 as $delivery){
      $table.="<tr>";
      $a3=unserialize( $delivery['partdata']);
      //var_dump($a3); exit;
      if(empty($a3))
      continue;
      $table.="<td>art:".$delivery['artikul']."</td>
      <td>".$delivery['quantity']."шт</td>
      <td>Опт:".$a3->returned->rpr*$delivery['quantity']." руб</td>
      <td>С нац:".$delivery['price']*$delivery['quantity']." руб</td>
      <td>".$a3->brandName."</td>
      <td>".$a3->description."</td>
      <td>Срок доставки:".$a3->delivery_period."</td>
      ";
      $table.="</tr>";
      $optprice+=$a3->returned->rpr*$delivery['quantity'];
      $roznprice+=$delivery['price']*$delivery['quantity'];
      }

      $table.='<tr style="background:#C5F4FC"><td colspan="6">опт: '.$optprice.' розн: '.$roznprice.' чист: '.($roznprice-$optprice).' </td></tr>';
      $table.="</table></td>";
      } else {
      $table.="<td></td>";
      }
      $table.="<td>".$a['phone']."</td>
      <td>".$a['adress']."</td>
      <td>".date("d-m-Y H:i:s", $a['timestamp'])."</td>";
      $table.="<td><form action='/panelcontrol/panelcontrol/status/' method='POST'><input type='hidden' name='id' value='".$a['id_delivery']."'><select name='status'>";
      foreach($arr as $b){
      if($a['id_status']==$b['id']){

      $table.="<option value='".$b['id']."' selected>".$b['name']."</option>";
      } else {
      $table.="<option value='".$b['id']."'>".$b['name']."</option>";
      }
      //.iconv('cp1251', 'utf8', $a['name'])."</td>";
      }
      $table.="</select><input type='submit' value='OK'/></form></td>";
      $table.="<td><a href='/panelcontrol/panelcontrol/delete/".$a['id_delivery']."'><span class='glyphicon glyphicon glyphicon-remove-circle' aria-hidden='true'></span></a></td>
      ";$table.="<td><a href='/panelcontrol/panelcontrol/invoice/".$a['id_delivery']."'><span class='glyphicon glyphicon glyphicon glyphicon-print' aria-hidden='true'></span></a></td>
      ";

      $table.="</tr>";
      }
      $table.="</table></form>";
      } else {
      return false;
      }

      return $table;
      } */

    /**
      Метод изменяет статус заказа
     */
    public function statusDelivery($id, $sid) {
        $sql = "UPDATE delivery_state SET id_status=:sid WHERE id_delivery=:id";
        $stm = $this->sql->prepare($sql);
        $stm->execute(array(':id' => $id, ':sid' => $sid));
    }

    public function statistic() {
        $sql = "SELECT COUNT(*) FROM deliveries"; //узнаем количество заказов всего
        $stm = $this->sql->prepare($sql);
        $stm->execute();
        $arr = $stm->fetchAll(PDO::FETCH_BOTH);
        $statistic['all_delivery'] = $arr[0][0]; //всего заказов

        $sql = "SELECT COUNT(*) FROM delivery_state WHERE delivery_state.id_status=3"; //узнаем количество выполненых заказов
        $stm = $this->sql->prepare($sql);
        $stm->execute();
        $arr = $stm->fetchAll(PDO::FETCH_BOTH);
        $statistic['confirm_delivery'] = $arr[0][0]; //доставлено заказов

        $sql = "SELECT COUNT(*) FROM delivery_state WHERE delivery_state.id_status=1"; //узнаем количество новых заказов
        $stm = $this->sql->prepare($sql);
        $stm->execute();
        $arr = $stm->fetchAll(PDO::FETCH_BOTH);
        $statistic['new_delivery'] = $arr[0][0]; //новых  заказов

        $sql = "SELECT COUNT(*) FROM delivery_state WHERE delivery_state.id_status=4"; //узнаем отклоненных заказов
        $stm = $this->sql->prepare($sql);
        $stm->execute();
        $arr = $stm->fetchAll(PDO::FETCH_BOTH);
        $statistic['bad_delivery'] = $arr[0][0]; //отклоненных  заказов

        $sql = "SELECT COUNT(*) FROM delivery_state WHERE delivery_state.id_status=2"; //В работе
        $stm = $this->sql->prepare($sql);
        $stm->execute();
        $arr = $stm->fetchAll(PDO::FETCH_BOTH);
        $statistic['job_delivery'] = $arr[0][0]; //в работе

        /* Узнаем по деньгами что у нас */
        $sql = "SELECT deliveries.delivery, delivery_state.id_delivery, delivery_state.id_status FROM delivery_state  INNER JOIN deliveries ON deliveries.id=delivery_state.id_delivery";
        $stm = $this->sql->prepare($sql);
        $stm->execute();
        $arr = $stm->fetchAll(PDO::FETCH_ASSOC);
        $all_summ = 0;
        $job_summ = 0;
        $done_summ = 0;
        $new_summ = 0;
        $break_summ = 0;
        foreach ($arr as $a) {
            $b = unserialize($a['delivery']);
            if (empty($b)) {
                continue;
            }
            foreach ($b as $c) {
                /* $e=unserialize($c['partdata']); */
                if (empty($c))
                    continue;
                $summ = $c['quantity'] * $c['price'];
                $all_summ += $summ;
                if ($a['id_status'] == 2) {
                    $job_summ += $summ;
                } elseif ($a['id_status'] == 3) {
                    $done_summ += $summ;
                } elseif ($a['id_status'] == 1) {
                    $new_summ += $summ;
                } elseif ($a['id_status'] == 4) {
                    $break_summ += $summ;
                }
            }
        }
        /* $statistic['job_delivery']=$arr[0][0]; //в работе */

        $table = "<table>
			<tr>
				<td>Всего заказов:&nbsp;&nbsp;</td>
				<td>" . $statistic['all_delivery'] . "</td>
			</tr>
			<tr>
				<td>Выполненых заказов:&nbsp;&nbsp;</td>
				<td>" . $statistic['confirm_delivery'] . "</td>
			</tr>
			<tr>
				<td>Заказов отправлено поставщику:&nbsp;&nbsp;</td>
				<td>" . $statistic['job_delivery'] . "</td>
			</tr>
			<tr>
				<td>Новых заказов:&nbsp;&nbsp;</td>
				<td>" . $statistic['new_delivery'] . "</td>
			</tr>
			<tr>
				<td>Отклонено заказов:&nbsp;&nbsp;</td>
				<td>" . $statistic['bad_delivery'] . "</td>
			</tr>
			</table>
			<hr>
			<table>
			<tr>
				<td>Всего было заказов на сумму:&nbsp;&nbsp;</td>
				<td>" . $all_summ . " руб.</td>
			</tr>
			<tr>
				<td>Сейчас в работе заказов на сумму:&nbsp;&nbsp;</td>
				<td>" . $job_summ . " руб.</td>
			</tr>
			<tr>
				<td>Выполнено заказов на сумму:&nbsp;&nbsp;</td>
				<td>" . $done_summ . " руб.</td>
			</tr>
			<tr>
				<td>Новых заказов на сумму:&nbsp;&nbsp;</td>
				<td>" . $new_summ . " руб.</td>
			</tr>
			<tr>
				<td>Отклонено заказов на сумму:&nbsp;&nbsp;</td>
				<td>" . $break_summ . " руб.</td>
			</tr>
		</table>";
        return $table;
    }

    /**
      метод создает HTML invoice
     */
    public function invoice_html_create($arr) {
        $html = $this->load->view('invoice', ['arr' => $arr], true);
        return $html;
    }

    /**
      метод создает pdf из Html
     */
    public function pdf_create($html) {
        /* require_once($_SERVER['DOCUMENT_ROOT'].'/application/models/dompdf/dompdf_config.inc.php');
          $dompdf = new DOMPDF();
          $dompdf->load_html($html);
          $dompdf->render();
          /*$dompdf->stream('aaa'.".pdf"); */
        /* return $dompdf->output(); */
        // $this->load->helper(array('dompdf', 'file'));
        pdf_create($html, 'filename');
    }

    /**
     * Метод отправляет поставщику все заказы
     * @param array $arr
     */
    public function workDelivery($arr) {

        $status = 0;
        foreach ($arr as $a) {
            //var_dump(unserialize($a['delivery'])); exit;
            $delivery = unserialize($a['delivery']);
            $array_delivery = array();
            foreach ($delivery as $d) {
                $d['partdata'] = unserialize($d['partdata']);
                //если поставщик аллавтопартс
                if ($d['partdata']->provider === '906044c6cb4224c69ba36dc736606b4d') {
                    $this->toOrderAllAutoParts($d);
                    //var_dump($d); exit;
                } elseif ($d['partdata']->provider === 'b1e590c4cf8b0a5814241aa63205c767') {
                    //если поставщик китавтотранс
                    //  var_dump($d); exit;
                    $provider_id = $this->toOrderKit36($d);

                    $d['provider_id'] = $provider_id; // $provider_id;
                }
                $d['partdata'] = serialize($d['partdata']);
                //тут дальше пошло для других поставшикой
                $array_delivery[] = $d; //это у нас будет обновленный массив с данными по заказам
            }
            /* var_dump($array_delivery); */
            //var_dump(unserialize($a['delivery']));exit;
            $sql = "UPDATE deliveries SET delivery=:pd WHERE id=:id";
            $stm = $this->sql->prepare($sql);
            $param = array(':id' => $a['id_delivery'], ':pd' => serialize($array_delivery));
            $stm->execute($param);
            //var_dump($param); exit;
            if ($stm->rowCount()) {
                $sql2 = "UPDATE delivery_state SET id_status=2 WHERE id_delivery=:id";
                $stm2 = $this->sql->prepare($sql2);
                $stm2->execute(array(':id' => $a['id_delivery']));
                if ($stm2->rowCount()) {
                    $status = 1;
                }
            }
            //var_dump($stm->rowCount());
        }
        return $status;
    }

    /**
     * Метод переводит заказ из корзины AllAutoParts в работу 
     * @param массив $obj массив содержимого корзины, одна запись
     * @return bool Возвращает труе в случае успеха, фальш в случае неудачи
     */
    public function toOrderAllAutoParts($obj) {
        //Если массив пришел не пустой
        if (!empty($obj)) {
            $this->load->model('allautopart'); //загружаем модель автопарт
            // var_dump($obj);exit;
            //устанавливаем новый wsdl для работы с корзиной
            $this->allautopart->wsdl_uri = 'https://allautoparts.ru/WEBService/BasketService.svc/wsdl?wsdl';

            //получаем xml
            $responseXML = $this->allautopart->createMakeOrderXML($obj['id'], $obj['provider_id'], '');
            $error = array();

            $result = $this->allautopart->query('MakeOrder', array('ParametersXml' => $responseXML), $error);

            return true;
        } else {
            //если массив пришел пустой то возвращаем false
            return false;
        }
    }

    public function toOrderKit36($d) {
        // return 2775;
        $api_key = "248c7f2c-b0e0-0fa9-3b37-f48d400d23dc";
        $this->load->helper('getpage');
        $pd = $d['partdata'];
        $postparam = 'oem=' . $pd->artikul . '&make_name=' . $pd->brandName . '&detail_name=' . $pd->description . '&qnt=' . $pd->count . '&comment=test&api_hash=' . $pd->uniqueid . '&api_key=' . $api_key;
        $result = json_decode(getPage('http://kat36.ru/api/v1/baskets', $postparam));
        if (!empty($result->data)) {
            $provider_id = $result->data->id;
            getPage('http://kat36.ru/api/v1/baskets/order', 'api_key=' . $api_key);
            return $provider_id;
        } else {
            return false;
        }
    }

    /**
     * Метод выводит позиции в наличии у нас на складе
     */
    public function getSkladArtikuls($page) {
        $limitStart = ($page * 20 - 20) < 0 ? 0 : $page * 20 - 20;
        $linitEnd = $limitStart + 20;
        $sql = 'SELECT t1.id,  t1.artikul, t4.price, t4.count, t1.description, t3.`name` FROM artikuls t1
                INNER JOIN artikul_state t2 ON t1.id=t2.id_artikul
                INNER JOIN brands t3 ON t2.id_brand=t3.id 
                INNER JOIN artikul_availability t4 ON t4.id_artikul=t2.id_artikul  WHERE t4.count!=0 ORDER BY t1.id ASC  LIMIT ' . $limitStart . ', ' . $linitEnd . '';
        $stm = $this->sql->prepare($sql);
        $stm->execute();
        $arr = $stm->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($arr)) {
            return $arr;
        } else {
            return false;
        }
    }

    public function countScladArtikuls() {
        $sql = 'SELECT COUNT(t1.id) as kol FROM artikuls t1
                INNER JOIN artikul_state t2 ON t1.id=t2.id_artikul
                INNER JOIN brands t3 ON t2.id_brand=t3.id 
                INNER JOIN artikul_availability t4 ON t4.id_artikul=t2.id_artikul WHERE t4.count!=0';
        $stm = $this->sql->prepare($sql);
        $stm->execute();
        $arr = $stm->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($arr)) {
            return $arr[0]['kol'];
        } else {
            return 0;
        }
    }

    /**
     * Метод возвращает список артикулов, подходящих под заданный
     * @param string $art Артикул
     */
    public function getSearchedArtikuls($art) {
        $stm = $this->sql->prepare('CALL getFindedArtikuls(:art);');
        $stm->execute([':art' => $art]);
        $arr = $stm->fetchAll(PDO::FETCH_ASSOC);
        $stm->closeCursor();
        if (!empty($arr)) {
            return $arr;
        } else {
            return false;
        }
    }

    /**
     * Метод выводит информацию по артикулу по его id
     * @param type $id
     */
    public function getArtikulIdInfo($id) {
        $sql = 'SELECT t1.id, t3.`name`, t1.artikul, t1.description FROM artikuls t1 INNER JOIN artikul_state t2 ON t1.id=t2.id_artikul INNER JOIN brands t3 ON t3.id=t2.id_brand WHERE t1.id=:id';
        $stm = $this->sql->prepare($sql);
        $stm->execute(array(':id' => $id));
        $arr = $stm->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($arr)) {
            $sql2 = 'SELECT t4.count, t4.price FROM artikuls t1
                INNER JOIN artikul_state t2 ON t1.id=t2.id_artikul
                INNER JOIN brands t3 ON t2.id_brand=t3.id 
                INNER JOIN artikul_availability t4 ON t4.id_artikul=t2.id_artikul WHERE t1.id=:id';
            $stm = $this->sql->prepare($sql2);
            $stm->execute(array(':id' => $arr[0]['id']));
            $arr2 = $stm->fetchAll(PDO::FETCH_ASSOC);
            $arr[0]['count'] = !empty($arr2[0]['count']) ? $arr2[0]['count'] : 0;
            $arr[0]['price'] = !empty($arr2[0]['price']) ? $arr2[0]['price'] : 0;
            return $arr[0];
        } else {
            return false;
        }
    }

    public function addSaveSkladPosition($id, $price, $count) {
        $sql = 'SELECT t4.count, t4.price FROM artikuls t1
                INNER JOIN artikul_state t2 ON t1.id=t2.id_artikul
                INNER JOIN brands t3 ON t2.id_brand=t3.id 
                INNER JOIN artikul_availability t4 ON t4.id_artikul=t2.id_artikul WHERE t1.id=:id';
        $stm = $this->sql->prepare($sql);
        $stm->execute(array(':id' => $id));
        $arr = $stm->fetchAll(PDO::FETCH_ASSOC);
        if (empty($arr)) {
            $sql = 'INSERT INTO artikul_availability (id_artikul, count, price) VALUES(:id_artikul, :count, :price)';
        } else {
            $sql = 'UPDATE artikul_availability SET price=:price, count=:count WHERE id_artikul=:id_artikul';
        }
        $stm = $this->sql->prepare($sql);
        $stm->execute(array(':id_artikul' => $id, ':count' => $count, ':price' => $price));
        if ($stm->rowCount()) {
            return true;
        } else {
            return false;
        }
    }

    public function createSitemap($filename, $linkarr, $prefix = '') {
        if (!empty($linkarr)) {
            $xml = new DOMDocument('1.0', 'utf-8');
            $xml->formatOutput = true;
            $urlset = $xml->createElement('urlset');
            $urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
            foreach ($linkarr as $k => $v) {
                $url = $xml->createElement('url');
                $loc = $xml->createElement('loc');
                $loc->appendChild($xml->createTextNode($prefix . $v));
                $url->appendChild($loc);
                $urlset->appendChild($url);
            }
            $xml->appendChild($urlset);
            $xml->save($_SERVER['DOCUMENT_ROOT'] . $filename);
        }
    }

    public function getAllArtukuls() {
        $sql = 'SELECT DISTINCT(artikul) FROM artikuls WHERE artikul!="" LIMIT 50000 OFFSET 100000';
        $stm = $this->sql->prepare($sql);
        $stm->execute();
        $arr = $stm->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($arr)) {
            return $arr;
        }
    }

    /**
     * Метод выбирает все позиции в корзине с истекшим таймаутом
     * и устанавливает timeout в единицу, очищает корзины поставщиков
     */
    public function setBacketTimeout() {

        $time = time() - 86400; // просроченные заказы - заказы, время добавления в корзину больше суток от настоящего момента

        $sql = "SELECT * FROM backet WHERE datetime<:time AND deleted=0 AND timeout!=1";
        $sql2 = "UPDATE backet SET timeout=1 WHERE datetime<:time AND deleted=0 AND timeout!=1";
        $stm = $this->sql->prepare($sql);
        $stm2 = $this->sql->prepare($sql2);
        $stm->execute(array(':time' => $time));
        $arr = $stm->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($arr)) {
            $stm2->execute(array(':time' => $time));
            foreach ($arr as $a) {
                $partdata = unserialize($a['partdata']);
                if ($partdata->provider === '906044c6cb4224c69ba36dc736606b4d') {
                    //если данная позиция - аллавтопарт
                    $this->load->model('allautopart');
                    $responseXML = $this->allautopart->createDeleteBacketXml($a['provider_id']);
                    $this->allautopart->wsdl_uri = 'https://allautoparts.ru/WEBService/BasketService.svc/wsdl?wsdl'; //Устанавливаем новый wsdl
                    $error = array();
                    $result = $this->allautopart->query('DeleteBasketDetails', array('ParametersXml' => $responseXML), $error);
                }
            }
        } else {
            return false;
        }
    }

    /*     * метод принимает массив и формирует красивую табличку */

    public function fin($array) {
        if (!empty($array)) {
            $tabl = "<table border=1>";
            $table = "<tr>
                                            <td>Номер заказа</td>
                                            <td>Заказ</td>
                                            <td>Время заказа</td>
                                            <td>оптовая стоимость</td>
                                            <td>Розничная стоимость</td>
                                            <td>Доход</td>
                                    </tr>";
            $alloptsumm = 0; //общая оптовая сумма
            $allroznsumm = 0; //общая розничная сумма
            $counter = 0; //счетчик количества заказов
            $allmerge = 0; //общий доход
            foreach ($array as $a) {

                $a2 = unserialize($a['delivery']);
                if (empty($a2)) {
                    continue;
                }
                $counter++;
                $table .= "<tr>";
                $table .= "	<td>000" . $a['id_delivery'] . "</td>";
                if (!empty($a2)) {
                    $table .= "<td><table style='font-size:10px;'>";
                    $optsumm = 0; //итоговая оптовая сумма
                    $roznsumm = 0; //итоговая розничная сумма
                    foreach ($a2 as $delivery) {
                        $table .= "<tr>";
                        $a3 = unserialize($delivery['partdata']);
                        if (empty($a3))
                            continue;
                        $optsumm += $a3->returned->rpr * $delivery['quantity'];
                        $roznsumm += $delivery['price'] * $delivery['quantity'];
                        $table .= "<td>art:" . $delivery['artikul'] . "</td>
                                                            <td>" . $delivery['quantity'] . "шт</td>
                                                            <td>Опт:" . $a3->returned->rpr * $delivery['quantity'] . " руб</td>
                                                            <td>С нац:" . $delivery['price'] * $delivery['quantity'] . " руб</td>
                                                            <td>" . $a3->brandName . "</td>
                                                            <td>" . $a3->description . "</td>
                                            ";
                        $table .= "</tr>";
                    }
                    $table .= "</table></td>";
                } else {
                    $table .= "<td></td>";
                }
                $table .= "<td>" . date("d-m-Y H:i:s", $a['timestamp']) . "</td>";
                $table .= "<td>" . $optsumm . "</td>";
                $table .= "<td>" . $roznsumm . "</td>";

                $alloptsumm += $optsumm;
                if ($a['id_status'] != 10) {
                    $allroznsumm += $roznsumm;
                    $allmerge += ($roznsumm - $optsumm);
                    $table .= "<td>" . $roznsumm . "</td>";
                    $table .= "<td>" . ($roznsumm - $optsumm) . "</td>";
                } else {
                    $allroznsumm -= $roznsumm;
                    $allmerge -= ($roznsumm - $optsumm);
                    $table .= "<td>-" . $roznsumm . "</td>";
                    $table .= "<td>" . ($optsumm - $roznsumm) . "</td>";
                }


                $roznsumm = $optsumm = 0;
                $table .= "</tr>";
            }
            $tablh = "<tr>
                                            <td style='font-weight:bold'>Всего заказов: " . $counter . "</td>
                                            <td  style='font-weight:bold'>Средний чек: " . round($allroznsumm / $counter, 1) . "</td>
                                            <td style='font-weight:bold'>Средний доход с заказа:" . round($allmerge / $counter, 1) . "</td>
                                            <td style='font-weight:bold'>Общая оптовая стоимость: " . $alloptsumm . "</td>
                                            <td style='font-weight:bold'>Общая розничная стоимость: " . $allroznsumm . "</td>
                                            <td style='font-weight:bold'>Общий Доход: " . $allmerge . "</td>
                                    </tr>";
            $table .= "</table></form>";

            $tabl = $tabl . $tablh . $table;
        } else {
            return false;
        }

        return $tabl;
    }

    /**
     * Метод выозвращает все статусы из базы данных
     * @return array Массив данных из БД - статусы заказов и корзины
     * @version 1.0
     */
    public function getAllStatus() {
        $stm = $this->sql->prepare('CALL getAllStatus()');
        $stm->execute();
        $result = $stm->fetchAll(PDO::FETCH_ASSOC);
        $stm->closeCursor();
        return $result;
    }

    /**
     * Метод изменяет статус для данного заказа
     * @param int $status_id ID статуса заказа
     * @param int $order_id ID заказа
     * @return bool возвращает 1 в случае успеха
     */
    public function setOrderStatus($status_id, $order_id) {
        $stm = $this->sql->prepare('CALL setOrderStatus(:status_id, :order_id)');
        $result = $stm->execute([
            ':status_id' => $status_id,
            ':order_id' => $order_id
        ]);
        $stm->closeCursor();
        return (int) $result;
    }

    /**
     * Метод изменяет статус для данной позиции
     * @param int $status_id ID статуса заказа
     * @param int $position_id ID заказа
     * @return bool возвращает 1 в случае успеха
     */
    public function setPositionStatus($status_id, $order_id) {
        $stm = $this->sql->prepare('CALL setPositionStatus(:status_id, :order_id)');
        $result = $stm->execute([
            ':status_id' => $status_id,
            ':order_id' => $order_id
        ]);
        $stm->closeCursor();
        return (int) $result;
    }

    /**
     * Метод возвращает все позиции в корзине у которых статус "В корзине"
     * @return array
     */
    public function getBacketActivePosition() {
        $stm = $this->sql->prepare('CALL getActiveBacketPosition()');
        $stm->execute();
        $result = $stm->fetchAll(PDO::FETCH_ASSOC);
        $stm->closeCursor();
        return $result;
    }

}
