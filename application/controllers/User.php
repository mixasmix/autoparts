<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class User extends CI_Controller {

    protected $sql;
    protected $session_user_info;
    private $params = array(); //свойство для хранения парметров страницы

    public function __construct() {
        parent::__construct();
        $this->sql = SQL::getInstance();
        $this->load->helper('security');
        $this->load->helper('cookie');
        $this->load->model('Backet', 'backet'); //загружаем модель корзины для получения текущих значений корзины пользователя
        $this->params['scripts'] = array('<script type="text/javascript" src="/js/jquery.nicescroll.min.js"></script>');
        $this->params['backet_status'] = $this->backet->checkbacket();
    }

    public function index() {
        $userdata = $this->session_user_info;
        if (!$this->aauth->is_login()) {
            $this->auth();
        } else {
            $this->lk();
        }
    }

    /**
     * Метод авторизации пользователя
     * return void
     */
    public function auth() {
        if ($this->aauth->is_login()) {
            header('Location: ' . 'http://' . $_SERVER['HTTP_HOST']);
            exit;
        } //если пользователь уже авторизован
        $postdata = $this->input->post(NULL, TRUE); //получаем все пост данные
        if (!empty($postdata['login']) or ! empty($postdata['password'])) {
            if (!$this->aauth->login($postdata['login'], $postdata['password'], true)) {
                echo $this->viewloader->getContent('auth', array('error' => $this->aauth->get_errors()));
            } else {
                header('Location: http://' . $_SERVER['HTTP_HOST']);
            }
        } else {
            echo $this->viewloader->getContent('auth');
        }
    }

    /**
     * Метод для регистрации пользователя
     */
    public function reg() {
        if ($this->aauth->is_login()) { //если юзер уже существует то перенаправляем на главную страницу
            header('Location: http://' . $_SERVER['HTTP_HOST']);
            exit;
        }
        $postdata = $this->input->post(NULL, TRUE); //получаем все пост данные
        $sess_data = $this->session_user_info;
        if (!empty(
                        $postdata['login']) or ! empty($postdata['password']) or
                $postdata['formtype'] == 'registration' and
                $postdata['password'] == $postdata['password_confirm']) {
            $this->aauth->logout();
            $user_id = $this->aauth->create_user(//создаем нового пользователя
                    $postdata['email'], $postdata['password'], $postdata['login']);
            //если ид пользовтаеля false
            if (!$user_id) {
                //то загружаем вид с формой регистрации и выводим ошибку
                $this->viewloader->getContent('registration', ['reg_confirm' => false, 'error' => $this->aauth->get_errors()]);
            } else {
                $this->aauth->add_member($user_id, 3); //добавляем юзера в группу Default
                $this->aauth->set_user_var('id_template', 1, $user_id);
                $this->aauth->set_user_var('tmpl_name', $this->config->item('default_template'), $user_id);
                $this->aauth->set_user_var('merge', 1.15, $user_id);
                $this->aauth->login_fast($user_id);
                header('Refresh: 5; URL= http://' . $_SERVER['HTTP_HOST']);
                echo $this->viewloader->getContent('registration', ['reg_confirm' => true]);
            }
        } else {
            echo $this->viewloader->getContent('registration', ['reg_confirm' => false]);
        }
    }

    /**
     * Метод разлогинивает юзера
     * return void
     */
    public function out() {
        $this->session->set_userdata(array('session_id' => null)); //Если не сбросить session id то корзина будет юзеру выводится 
        $this->session->set_userdata(array('user' => null));
        $this->aauth->logout();
        header('Location: http://' . $_SERVER['HTTP_HOST']);
    }

    /**
     * Личный кабинет пользователя
     * return void
     */
    public function lk() {
        $this->load->model('Vindecoder', 'vindecoder');
        $sess_data = $this->session_user_info;
        $this->load->model('Checkzakaz','checkzakaz');
        if (!$this->aauth->is_login()) {
            show_error($this->lang->line('error_unautorized'), 401);
        }
        $this->params['title'] = $this->lang->line('msg_lk_name');
        $this->params['description'] = $this->lang->line('msg_lk_name');
        $this->params['content'] = $this->viewloader->getContent('private');
        echo $this->viewloader->getView($this->params);
    }
/*
    public function editdata() {
        $sess_data = $this->session_user_info;
        if ($this->aauth->is_loggedin()) {
            $this->load->model('edituserinfo');
            $type = $this->input->post('type', TRUE);
            $result = $this->edituserinfo->$type($this->input->post('value', TRUE), $this->aauth->get_user_id());
            if (!empty($result)) {
                if ($result[0] == 'email') {
                    $field = 'email';
                } elseif ($result[0] == 'phone') {
                    $field = 'phone';
                } elseif ($result[0] == 'firstname') {
                    $field = "name";
                } elseif ($result[0] == 'lastname') {
                    $field = "family";
                } elseif ($result[0] == 'city') {
                    $field = "sity";
                } elseif ($result[0] == 'address') {
                    $field = "address";
                }
                $sess_data[$field] = $result[1];
                $this->session->set_userdata('user', $sess_data);
                echo json_encode($result);
            }
        }
    }*/

    /**
     * Функция для перезвона
     */
    public function recall() {
        $this->load->model('recall');
        $res = $this->recall->addRecall();
        if ($res) {
            echo true;
        } else {
            echo false;
        }
    }

    /**
     * @author Mixa
     * @return string 
     */
    public function addvin() {
        $this->load->model('vindecoder');
        $vin = $this->input->post('vin', TRUE);
        $result = $this->vindecoder->addNewVin($vin);
        $sess_data = $this->session_user_info;
        $garage = $this->vindecoder->getVinUser($sess_data['id']);
        echo $this->vindecoder->getTableCar($garage);
    }

    /**
     * Методя для восстановления пароля пользователя
     */
    public function pass_recovery() {
        $email = $this->input->post('email');
        if (!empty($email)) {
            $result = $this->aauth->remind_password($email);
            if ($result) {
                header('Location: http://' . $_SERVER['HTTP_HOST'] . '/page/pages/recovery');
            } else {
                $noError = false;
            }
        } else {
            $result_page = array();
            $result_page[0]['content'] = $this->viewloader->getContent('pass_recovery_form');
            $a['title'] = 'Восстановление пароля';
            $this->params['title'] = 'Восстановление пароля';
            $this->params['description'] = 'восстановление пароля';
            $this->params['sess_data'] = $this->session_user_info;
            $this->params['content'] = $result_page[0]['content'];
            echo $this->viewloader->getView($this->params);
        }
    }

    /**/

    public function deletevin() {
        $vinid = str_replace('#', '', $this->input->post('vinid'));
        $this->load->model('vindecoder');
        $sess_data = $this->session_user_info;
        $this->vindecoder->deleteVin($vinid, $sess_data['id']);
        $garage = $this->vindecoder->getVinUser($sess_data['id']);
        echo $this->vindecoder->getTableCar($garage);
    }

    /**
     * Метод для работы с заметками
     * @param string $action
     * @param int $id_note Идентификатор закладки
     */
    public function notes($action = '', $id_note = '') {
        $this->load->model('notes'); //загружаем модель работы с заметками
        $sess_data = $this->session->userdata('user');
        $sid = $this->session->userdata['session_id'];
        if ($action == 'add') {
            $post = $this->input->post('note', true);
            if (!empty($post)) {
                $post = json_decode(strip_tags($post));
                $artikul = filter_var(filter_var($post->artikul, FILTER_SANITIZE_STRING), FILTER_SANITIZE_FULL_SPECIAL_CHARS); //почистим данныен от всякого говна 
                $brand = filter_var(filter_var($post->brand, FILTER_SANITIZE_STRING), FILTER_SANITIZE_FULL_SPECIAL_CHARS); //почистим данныен от всякого говна 
                /**
                 * если имя бренда или артикул больше 30 или меньше 3 символов - нафиг прибьем скрипт
                 */
                if (strlen($brand) > 30 or strlen($artikul) > 30 or strlen($brand) < 3 or strlen($artikul) < 3) {
                    exit;
                }
                if ($this->notes->countNote($sess_data['id'], $sid) >= 20) {
                    echo false;
                }
                //получаем id артикула и id бренда

                $brandAndArtID = $this->notes->getArtAndBrandID($artikul, $brand);

                var_dump($post);
                exit;
                $result = $this->notes->addNote($brandAndArtID['id_brand'], $brandAndArtID['id_artikul'], '', $sess_data['id'], $sid);
                if (!empty($result)) {
                    echo true;
                    // echo $this->notes->countNote($sess_data['id'], $sid);
                } else {
                    echo false;
                }
            }
        } elseif ($action == 'check') {
            echo $this->notes->countNote($sess_data['id'], $sid);
        } elseif ($action == 'delete') {
            $post = $this->input->post(NULL, TRUE);
            $this->notes->deleteNote($post['id_note'], $sess_data['id'], $sid);
        } elseif ($action == 'addnote') {
            $post = $this->input->post(NULL, TRUE);
            if ($this->notes->updateNote($post['note'], $post['id_note'], $sess_data['id'], $sid)) {
                echo 1;
            } else {
                echo 0;
            }
        }
    }

    /**
     * Метод выводит страницу с заметками
     */
    public function notepage() {
        $sess_data = $this->session_user_info;

        $this->load->model('notes');
        if (empty($sess_data['id'])) {
            $sess_data['id'] = false;
            $sess_data['role'] = false;
            $sess_data['id_role'] = false;
        }
        $user_note = $this->notes->getUserNote($sess_data['id'], $this->session->userdata['session_id']);
        $a['title'] = 'Ваши заметки';
        $a['sess_data'] = $sess_data;
        $this->load->view('head', $a);

        $this->load->view('header', array('auth' => $sess_data['role']));
        $this->load->view('backet');
        $this->load->view('usernotepage', array('sess_data' => $sess_data, 'user_note' => $user_note)); //страница с заметкми юзера
        $this->load->view('footer');
        $this->load->view('bottom', array('id_role' => $sess_data['id_role'], 'hide' => true));
    }

    /**
     * Метод выводит страницу с текущими заказами
     */
    public function orders() {
        if (!$this->aauth->is_login()) {
            show_error($this->lang->line('error_unautorized'), 401);
        }
        $this->params['title'] = $this->lang->line('template_private_page_order_link');
        $this->params['description'] = $this->lang->line('template_private_page_order_description');
        $this->params['content'] = $this->viewloader->getContent('userOrdersList', [
            'activeOrders' => $this->backet->getOrders(),
            'archiveOrders' => $this->backet->getOrdersArchive()
        ]);
        echo $this->viewloader->getView($this->params);
    }

    /**
     * Метод выводит гараж пользователя
     */
    public function garage() {
        if (!$this->aauth->is_login()) {
            show_error($this->lang->line('error_unautorized'), 401);
        }
        $this->load->model('vindecoder');
        $this->params['title'] = $this->lang->line('template_garage_title');
        $this->params['description'] = $this->lang->line('template_garage_description');
        $this->params['content'] = $this->viewloader->getContent('garageList', [
            'garageCars' => $this->vindecoder->getVinUser($this->aauth->get_user_id())
        ]);
        echo $this->viewloader->getView($this->params);
    }

    /**
     * Настройки пользователя
     */
    public function setting() {
        if (!$this->aauth->is_login()) {
            show_error($this->lang->line('error_unautorized'), 401);
        }
        $this->load->model('userModel');
         if($this->input->post('action')=='replase_password'){
             $this->userModel->rePass();exit;
         }
        $this->params['title'] = $this->lang->line('template_user_setting_title');
        $this->params['description'] = $this->lang->line('template_user_setting_description');
        $this->params['content'] = $this->viewloader->getContent('userSetting', []);
        echo $this->viewloader->getView($this->params);
    }

}