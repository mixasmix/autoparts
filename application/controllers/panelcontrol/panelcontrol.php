<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
error_reporting(E_ALL);

class Panelcontrol extends CI_Controller {

    private $params = array(); //массив параметров для отображения страницы

    public function __construct() {
        parent::__construct();
        if (!$this->input->post('login', true)) {
            if (!$this->aauth->is_admin()) {
                $this->auth();
            }
        }


        $this->load->model('viewloader', 'vl'); //подгружаем модель вывода отображения
    }

    public function index() {
        if ($this->aauth->is_admin()) {
            $this->load->model('admin/admin_delivery');
            $this->load->model('recall'); //модель 'перезвонить'
            $this->load->model('admin/changelog'); //модель чейнджлог
            $this->load->model('vindecoder'); //модель виндекодер
            $this->params['data'] = $this->admin_delivery->statistic(); //статистика
            $this->params['recalls'] = $this->recall->getRecalls(); // кто просит перезвонить
            $this->params['log'] = $this->changelog->getLog(); // лог 
            $this->params['vins'] = $this->vindecoder->getAllVinBase(); // винномера 
            $this->params['content'] = $this->load->view('adminpanel/adminview/panelcontrol_gen', $this->params, true);
            $this->vl->adminGetView($this->params);
            //$this->viewLoader(array('pagedata'=>$this->load->view('adminpanel/panelcontrol_info',  array('data'=>$this->admin_delivery->statistic(), 'recalls'=>$recalls, 'log'=>$log, 'vins'=>$vins),  true)));
        }

        /* if(empty($this->input->post('login'))) */
        /* $this->join(); */


        //$this->load->library('authentication');
        /* $this->authentication->logout(); */
        //если админ залогинен
        //var_dump($this->authentication->is_loggedin());
        // Read the username
        /* $username = '';

          // Read the password
          $password = '';

          // Create the new user
          $user_id = $this->authentication->create_user($username, $password); */
    }

    public function out() {
        $this->aauth->logout();
        header('Location: /panelcontrol/');
    }

    public function auth() {
        $this->load->view('adminpanel/adminview/admin_auth');
    }

    /* метод выводит все активные заказы */

    public function zakaz($type = "active") {
        if ($this->aauth->is_admin()) {
            $this->load->model('admin/admin_delivery');
            if ($type == "active") {
                $array = $this->admin_delivery->getDelivery("active");
            } elseif ($type == "archive") {
                $array = $this->admin_delivery->getDelivery('archive');
            }
            $this->params['content'] = $this->load->view('adminpanel/adminview/order_table', [
                'orders' => $array,
                'statuses' => $this->admin_delivery->getAllStatus(),
                'backets' => $this->admin_delivery->getBacketActivePosition()
                    ], true);
            $this->vl->adminGetView($this->params);
        }
    }

    /* метод создает инвойс в формате pdf */

    public function invoice($id) {
        if ($this->authentication->is_loggedin()) {
            $this->load->model('admin/admin_delivery');
            $array = $this->admin_delivery->getDelivery($id);
            $html = $this->admin_delivery->invoice_html_create($array[0]);
            echo $html;
        }
    }

    /* метод удаляет выбранный заказ */

    public function delete($id) {
        if ($this->authentication->is_loggedin()) {
            $this->load->model('admin/admin_delivery');
            $this->admin_delivery->delDelivery($id);
            header('Location: /panelcontrol/panelcontrol/zakaz');
        }
    }

    /* метод изменяет текущий тстаус заказа */

    public function status() {
        if ($this->authentication->is_loggedin()) {
            $id = $this->input->post('id', true);
            $sid = $this->input->post('status', true);
            $this->load->model('admin/admin_delivery');
            $this->admin_delivery->statusDelivery($id, $sid);
            header('Location: /panelcontrol/panelcontrol/zakaz');
        }
    }

    public function join() {
        $username = $this->input->post('login', true);
        $password = $this->input->post('password', true);
        if ($this->aauth->login($username, $password)) {


            if ($this->aauth->is_admin()) {
                header('Refresh: 5; url=/panelcontrol/');
                echo "Вы успешно вошли в систему. Сейчас вы будуте перенаправлены";
            } else {
                show_error('Access denied', 401);
            }
        } else {
            echo "Вы неавторизованы";
        }
    }

    public function pagedit($action = '', $id = '') {
        if ($this->authentication->is_loggedin()) {
            $this->load->model('admin/page_edit');
            if (empty($action)) {
                $this->params['action'] = 'pagelist';
                $this->params['arc'] = 0;
                $this->params['pages'] = $this->page_edit->getAllPage();
                $this->params['content'] = $this->load->view('adminpanel/adminview/panelcontrol_gen', $this->params, true);
                $this->vl->adminGetView($this->params);
            }
            if ($action == 'edit') {
                //если пост пустой
                $postdata = $this->input->post(NULL, false);
                if (empty($postdata)) {
                    $this->params['page'] = $this->page_edit->getPage($id);
                    $this->params['action'] = 'pagedit';
                    $this->params['content'] = $this->load->view('adminpanel/adminview/panelcontrol_gen', $this->params, true);
                    $this->vl->adminGetView($this->params);
                } else {
                    //если пришли данные
                    $this->page_edit->updatePage($id, $postdata);
                    header('Location: /panelcontrol/panelcontrol/pagedit');
                }
            }
            if ($action == 'delete') {
                if (!empty($id)) {
                    $this->page_edit->deleted($id);
                    header('Location: /panelcontrol/panelcontrol/pagedit');
                }
            }
            if ($action == 'create') {
                //если пост пустой
                $postdata = $this->input->post();
                if (empty($postdata)) {
                    $this->params['action'] = 'pagedit';
                    $this->params['content'] = $this->load->view('adminpanel/adminview/panelcontrol_gen', $this->params, true);
                    $this->vl->adminGetView($this->params);
                    //$this->viewLoader(array('pagedata'=>$this->load->view('adminpanel/create_edit_page','',  true)));
                } else {
                    //если пришли данные
                    $this->page_edit->createPage($postdata);
                    header('Location: /panelcontrol/panelcontrol/pagedit');
                }
            }
            if ($action == 'archive') {
                $this->params['action'] = 'pagelist';
                $this->params['arc'] = 1;
                $this->params['pages'] = $this->page_edit->getAllPageArchive();
                $this->params['content'] = $this->load->view('adminpanel/adminview/panelcontrol_gen', $this->params, true);
                $this->vl->adminGetView($this->params);
            }
            if ($action == 'restore') {
                $this->page_edit->restore($id);
                header('Location: /panelcontrol/panelcontrol/pagedit');
            }
        }
    }

    /**
      metod для рабыт с юзерами
      Выводит список всех зарегистрированных в системе юзеров
     */
    public function users($action = '', $id = '') {
        if ($this->authentication->is_loggedin()) {
            $this->load->model('admin/users_edit');
            if (empty($action)) {
                $this->params['action'] = 'userlist';
                $this->params['users'] = $this->users_edit->getAllUsers(0, 1000);
                $this->params['content'] = $this->load->view('adminpanel/adminview/panelcontrol_gen', $this->params, true);
                $this->vl->adminGetView($this->params);

                //$this->viewLoader(array('pagedata'=>$this->load->view('adminpanel/users_edit_pages', array('users'=>$users),  true)));
            }
            if ($action == 'edit') {
                //если пост пустой
                $postdata = $this->input->post(NULL, TRUE);
                if (empty($postdata)) {
                    $this->params['action'] = 'useredit';
                    $this->params['user'] = $this->users_edit->getUser($id);
                    $this->params['content'] = $this->load->view('adminpanel/adminview/panelcontrol_gen', $this->params, true);
                    $this->vl->adminGetView($this->params);
                    //$this->viewLoader(array('pagedata'=>$this->load->view('adminpanel/edit_user', array('user'=>$user, 'action'=>'edit'),  true)));
                } else {
                    //если пришли данные
                    $this->users_edit->updateUser($id, $postdata);
                    header('Location: /panelcontrol/panelcontrol/users');
                }
            }
            if ($action == 'delete') {
                $this->users_edit->deleted($id);
                header('Location: /panelcontrol/panelcontrol/users');
            }
        }
    }

    public function recall($active, $id, $status) {
        if ($this->authentication->is_loggedin()) {
            if ($active === 'edit') {
                $this->load->model('recall'); //модель 'перезвонить'
                $this->recall->editRecall($id, $status);
                header('Location: /panelcontrol/');
            }
        }
    }

    public function changelog() {
        if ($this->authentication->is_loggedin()) {
            $this->load->model('admin/changelog');
            if ($this->input->post('value')) {
                $result = $this->changelog->addLog($this->input->post('value'));
                $result['dt'] = date('d.m.y H:i:s', $result['dt']);
                echo json_encode($result);
            }
        }
    }

    public function vindecoder() {
        $this->load->model('vindecoder'); //модель виндекодер
    }

    public function addVin() {
        $this->load->model('vindecoder');
        $this->input->post('vin');
        echo $this->vindecoder->addNewVin($vin);
    }

    /**
     * Метод ставит в работу новые заказы
     * @author Dark_Dante
     */
    public function inWorkDelivery() {
        if ($this->authentication->is_loggedin()) {
            $this->load->model('admin/admin_delivery');
            $array = $this->admin_delivery->getDelivery('new'); //все заказы
            $result = $this->admin_delivery->workDelivery($array);
            if ($result) {
                echo 'Заказы успешно отправлены поставщику<br> <a href="/panelcontrol/">Вернуться на главную</a>';
            } else {
                echo 'Что то пошло не так<br> <a href="/panelcontrol/">Вернуться на главную</a>';
            }
        }
    }

    public function brands($a = '', $id = '') {
        if ($this->authentication->is_loggedin()) {
            $this->load->model('brandmodel', 'bm');
            if ($a == 'brand') {

                $this->params['edit'] = true; //редактирование бренда
                $this->params['brand'] = $this->bm->getBrandInfo($id); //получаем данные по бренду
                $this->params['action'] = 'brandedit'; //экшн
                $this->params['content'] = $this->load->view('adminpanel/adminview/panelcontrol_gen', $this->params, true);
                $this->vl->adminGetView($this->params);
            } elseif ($a == 'edit') {
                if ($this->input->post()) {
                    $data = $this->input->post();
                    $this->bm->editBrandInfo($id, $data);
                    header('Location: /panelcontrol/panelcontrol/brands');
                }
            } else {

                $this->params['nodesc'] = $this->bm->getBrandsNoDescription(); //бренды без описания
                $this->params['brandtable'] = str_replace('/brands/', '/panelcontrol/panelcontrol/brands/', $this->bm->brandtable($this->bm->getBrands()));
                $this->params['action'] = 'brandlist'; //экшн
                $this->params['content'] = $this->load->view('adminpanel/adminview/panelcontrol_gen', $this->params, true);

                $this->vl->adminGetView($this->params);
            }
        }
    }

    /**
     * Метод для загрузки изображений на сервер
     */
    public function fileupload($type = 'image') {
        if ($this->authentication->is_loggedin()) {
            $arr = array();
            $query_string = $_SERVER['QUERY_STRING'];
            parse_str($query_string, $arr);
            if ($type == 'image') {
                
            }
            $config['upload_path'] = $_SERVER['DOCUMENT_ROOT'] . '/uploads/image/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['max_size'] = '100';
            $config['max_width'] = '1024';
            $config['max_height'] = '768';
            $config['encrypt_name'] = true;

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('upload')) {
                echo json_encode(array('Ошибка' => $this->upload->display_errors()));
            } else {
                $data = $this->upload->data();
                echo json_encode($data);
            }
        }
    }

    /**
     * Метод проверяет состояние заказа. Будет дергаться кроном через getPage()
     */
    public function croncheck() {
        $this->load->model('checkzakaz', 'cz');
        $this->load->model('admin/admin_delivery', 'ad');
        $arr = $this->ad->getDelivery('in_work'); //получаем все заказы в работе
        $this->ad->setBacketTimeout();

        // Создание карты артикулов      

        $artukuls = $this->ad->getAllArtukuls();
        $art = array();
        foreach ($artukuls as $a) {
            $art[] = $a['artikul'];
        }
        $this->ad->createSitemap('/sitemapartikul3.xml', $art, 'http://' . $_SERVER['HTTP_HOST'] . '/#!');

        // Обходим массив заказов если он не пустой

        if (!empty($arr)) {

            foreach ($arr as $a) {
                $b = unserialize($a['delivery']);
                $positionId = array();
                foreach ($b as $c) {
                    $d = unserialize($c['partdata']);
                    //var_dump($c); exit;
                    if ($d->provider == '906044c6cb4224c69ba36dc736606b4d') {
                        $positionId[$d->provider][] = $c['id'];
                    } elseif ($d->provider == 'b1e590c4cf8b0a5814241aa63205c767') {
                        $positionId[$d->provider][] = array($c['id'], $c['provider_id']);
                    }
                }

                //обойдем массив id циклом
                $resultSatusParts = array();
                foreach ($positionId as $key => $value) {
                    $resultSatusParts = $resultSatusParts + $this->cz->getProviderStatus($value, $key);
                    //var_dump($resultSatusParts);
                }

                //переменная-признак готовности заказа
                $done = 1;
                foreach ($resultSatusParts as $r) {

                    if ($r['state_id'] == 80) {
                        $done = $done * 1;
                    } else {
                        $done = $done * 0;
                    }
                }

                if ($done) {
                    $msg = "Заказ #" . $a['id'] . " Готов к выдаче Телефон:" . $a['phone'] . "\n\r Номер заказа: 000" . $a['id'] . "\n\r Адрес доставки:" . $a['adress'];
                    mail("sitests2ru@yandex.ru", 'Заказ готов к выдаче', $msg, "From: sts2.ru \n" . 'Content-type: text/html; charset="utf-8"');
                    /* $this->load->library('email');
                      $this->email->from('sts2.ru', 'service');
                      $this->email->to('info@sts2.ru');
                      $this->email->subject('Заказ №'.$a['id'].' Готов к выдаче');
                      $msg="Заказ #".$a['id']." Готов к выдаче Телефон:".$a['phone']."\n\r Номер заказа: 000".$a['id']."\n\r Адрес доставки:".$a['adress'];
                      $this->email->message($msg);
                      $this->email->send(); */
                }
            }
        }


        /**
         * чекер цен на аксессуары
         */
        $this->load->model('acessorymodel', 'as');
        $this->as->priceChecker();
    }

    /**
     * Метод выводит неодобренные отзывы по фирмам
     */
    public function brandvote($action = '', $id_comment = '') {
        if ($this->authentication->is_loggedin()) {
            $this->load->model('brandmodel', 'bm');
            if (empty($action)) {
                $this->params['newVote'] = $this->bm->getNewVote();
                $this->params['action'] = 'brandvotelist';
                $this->params['content'] = $this->load->view('adminpanel/adminview/panelcontrol_gen', $this->params, true);
                $this->vl->adminGetView($this->params);

                //$this->viewLoader(array('pagedata'=>$this->load->view('adminpanel/brandvotelist', array('newVote'=>$newVote), true)));
            } elseif ($action == 'edit') {
                $post = $this->input->post();
                if (!empty($post['moderated'])) {
                    $this->bm->moderatedVote($post['id_comment']);
                    header('Location: /panelcontrol/panelcontrol/brandvote');
                }
            } elseif ($action == 'delete') {
                if (!empty($id_comment)) {
                    $this->bm->deleteVote($id_comment);
                    header('Location: /panelcontrol/panelcontrol/brandvote');
                }
            }
        }
    }

    /**
     * Слуебный метод для всякой фигни
     */
    public function mymetod() {
        exit;
        //require_once($_SERVER['DOCUMENT_ROOT'].'/application/models/simpledom/simple_html_dom.php');
        //echo file_get_contents($_SERVER['DOCUMENT_ROOT'].'/uploads/tires.txt');
        $xml = new SimpleXMLElement(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/uploads/tires.xml'));
        $html = "<!doctype html>
                    <html>
                            <head>
                                    <title></title>
                            </head>
                            <body><table>";
        foreach ($xml->tbody->tr as $tr) {
            $html .= '<tr>';
            $html .= '<td>' . $tr->td[0] . '</td>';
            $html .= '<td><a href="http://sts2.ru#' . $tr->td[1] . '">' . $tr->td[1] . '</a></td>';
            $html .= '<td><a href="http://sts2.ru#' . $tr->td[2] . '">' . $tr->td[2] . '</a></td>';
            $html .= '<td>' . $tr->td[3] . '</td>';
            $html .= '<td>' . $tr->td[4] . '</td>';
            $html .= '<td>' . $tr->td[5] . '</td>';
            $html .= '<td>' . $tr->td[6] . '</td>';
            $html .= '</tr>';
        }
        $html .= '</table></body>
                            </html>';
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/uploads/tires.html', $html);
        echo 'done';
    }

    private function viewLoader($array) {
        $this->load->view('adminpanel/adminpanel_head');
        $this->load->view('adminpanel/adminpanel_body', array('pagedata' => $array['pagedata']));
    }

    public function availability($page = 0) {
        if ($this->authentication->is_loggedin()) {
            $this->load->model('admin/admin_delivery', 'ad');
            $post = $this->input->post(null, true);
            if ($page === 'getpart') {
                //вот тут получим список похожих артикулов

                echo json_encode($this->ad->getSearchedArtikuls($post['part']));
            } elseif ($page === 'getpartid') {

                echo json_encode($this->ad->getArtikulIdInfo($post['id']));
            } elseif ($page === 'addsklad') {

                echo $this->ad->addSaveSkladPosition($post['id'], $post['price'], $post['count']);
            } elseif ($page === 'delsklad') {
                echo $this->ad->addSaveSkladPosition($post['id'], 0, 0);
            } else {

                $skladArtikuls = $this->ad->getSkladArtikuls($page);
                //var_dump($skladArtikuls); exit;
                $this->load->library('pagination');
                $config['base_url'] = '/panelcontrol/panelcontrol/availability/';
                $config['total_rows'] = $this->ad->countScladArtikuls();
                $config['per_page'] = 20;
                $config['use_page_numbers'] = TRUE;
                $this->pagination->initialize($config);
                $pagination = $this->pagination->create_links();
                $this->params['skladArtikuls'] = $skladArtikuls;
                $this->params['action'] = 'skladlist'; //экшн
                $this->params['content'] = $this->load->view('adminpanel/adminview/panelcontrol_gen', $this->params, true);

                $this->vl->adminGetView($this->params);
            }
        }
    }

    public function news($action = '', $id = '') {
        if ($this->authentication->is_loggedin()) {
            $this->load->model('newsmodel', 'nm');

            if ($action == 'create') {
                //если пост пустой
                $postdata = $this->input->post();
                if (empty($postdata)) {

                    $this->params['action'] = 'createnews';
                    $this->params['content'] = $this->load->view('adminpanel/adminview/panelcontrol_gen', $this->params, true);
                    $this->vl->adminGetView($this->params);
                } else {
                    //если пришли данные
                    if ($this->nm->addNews($postdata)) {
                        header('Location: /panelcontrol/panelcontrol/news');
                    } else {
                        echo 'Что то пошло не так';
                    }
                }
            } elseif ($action == 'edit') {

                $postdata = $this->input->post(NULL, false);
                if (empty($postdata)) {
                    $this->params['news'] = $this->nm->getArticle($id);
                    $this->params['action'] = 'createnews';
                    $this->params['content'] = $this->load->view('adminpanel/adminview/panelcontrol_gen', $this->params, true);
                    $this->vl->adminGetView($this->params);
                    //$this->viewLoader(array('pagedata'=>$this->load->view('adminpanel/create_edit_news', array('news'=>$news),  true)));
                } else {
                    //если пришли данные
                    if ($this->nm->editArticle($postdata, $id)) {
                        header('Location: /panelcontrol/panelcontrol/news');
                    } else {
                        echo 'Что то пошло не так';
                    }
                }
            } elseif ($action == 'delete') {
                if ($this->nm->deleteArticle($id)) {
                    header('Location: /panelcontrol/panelcontrol/news');
                } else {
                    echo 'Что то пошло не так';
                }
            } else {

                $this->params['news'] = $this->nm->getAllNews();
                $this->params['action'] = 'newslist';
                $this->params['content'] = $this->load->view('adminpanel/adminview/panelcontrol_gen', $this->params, true);
                $this->vl->adminGetView($this->params);
                //$this->viewLoader(array('pagedata'=>$this->load->view('adminpanel/newslist', array('news'=>$news),  true)));
            }
        }
    }

    /**
     * Метод для работы с финансами
     */
    public function finance($action = "") {
        if ($this->authentication->is_loggedin()) {
            /**
             * Нам надо получить все заказы со статусом отличным от "удален"
             */
            $this->load->model('admin/admin_delivery');
            $a_active = $this->admin_delivery->getDelivery(); //активные заказы
            $a_archive = $this->admin_delivery->getDelivery('archive'); //архивные заказы
            echo $this->admin_delivery->fin($a_archive);
            //var_dump($a_archive);
        }
    }

    /**
     * Метод для работы с аксессуарами
     * 
     */
    public function acessory($action = false) {
        if ($this->authentication->is_loggedin()) {
            $this->load->model('viewloader', 'vl');
            //если action categories
            if ($action == 'categrories') {
                echo $this->vl->adminGetView();
            } elseif ($action == 'acessories') {
                //если action acessories
            }
        }
    }

    /**
     * Метод устанавливает/изменяет указанный параметр
     * @param string $action Каккой парамерт изменить
     */
    public function setParam($action = '') {
        if ($this->aauth->is_admin()) {
            $this->load->model('admin/admin_delivery');
            if ($action == 'setOrderStatus') {
                $status_id = (int) $this->input->post('id_status');
                $order_id = (int) $this->input->post('id_order');
                echo $this->admin_delivery->setOrderStatus($status_id, $order_id);
            }
            if ($action == 'deleteThisPosition') {
                $status_id = 5; //ставим статус 5 что соотвествует значению Удален
                $positions_id = (array) $this->input->post('id_positions');
                $result = 1;
                if (!empty($positions_id)) {
                    foreach ($positions_id as $pos_id) {
                        $r = $this->admin_delivery->setPositionStatus($status_id, $pos_id);
                        if ($result)
                            $result = $r;
                    }
                }else {
                    $result = 0;
                }
                echo $result;
            }
            if ($action == 'setPositionStatus') {
                $status_id = (int) $this->input->post('id_status');
                $pos_id = (int) $this->input->post('id_position');
                echo $this->admin_delivery->setPositionStatus($status_id, $pos_id);
            } else {
                echo 0;
            }
        }
    }

    /**
     * метод который принимает/возвращает информацию по Ajax
     * @param string $action 
     */
    public function ajaxActions($action = '') {
        if ($this->aauth->is_admin()) {
            $this->load->model('admin/admin_delivery');
            if($action=='getFindedArtikuls'){
                echo json_encode($this->admin_delivery->getSearchedArtikuls($this->input->post('art')));
            }
        }
    }

}
