<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class MyBacket extends CI_Controller {

    protected $sql;
    protected $session_user_info;
    private $params = array(); //свойство для хранения парметров страицы

    public function __construct() {
        parent::__construct();
        $this->sql = SQL::getInstance();
        $this->load->helper('security');
        $this->params['scripts'] = array('<script type="text/javascript" src="/js/jquery.nicescroll.min.js"></script>');
        $this->load->model('Backet', 'backet');
        $this->params['backet_status'] = $this->backet->checkbacket();
    }

    public function index() {
        $this->load->model('getpage');
        $result_page = $this->getpage->getPageData('parts');
        $backet_position = $this->backet;
        $this->params['content'] = $this->viewloader->getContent('backetlist', ['backetPositions' => $this->backet->getBacket()]);
        echo $this->viewloader->getView($this->params);
    }

    public function delete() {
        $this->load->model('backet');
        $this->backet->delBacket($this->input->post('position_id'));
        header('Location: /mybacket/');
    }

    public function confirm() {
        $this->params['title'] = $this->lang->line('template_confirm_order_title');
        $this->params['description'] = $this->lang->line('template_confirm_order_description');
        $this->params['content'] = /* $this->viewloader->getContent('confirm_backetlist', ['backetPositions'=> $this->backet->getBacket()]). */$this->viewloader->getContent('confirmOrder');
        $this->load->model('viewloader', 'vl');
        echo $this->vl->getView($this->params);
    }

    public function done() {
        $this->load->model('backet');
        $order_num = $this->backet->toOrder();
        if ($order_num) {
            $this->params['title'] = $this->lang->line('template_order_done_title');
            $this->params['description'] = $this->lang->line('template_order_done_description');
            $this->params['content'] = $this->lang->line('template_order_done_content_number') . $order_num . $this->lang->line('template_order_done_content_text');
            echo $this->viewloader->getView($this->params);
        } else
            header('Location: /mybacket/');
    }

    public function clear() {
        if (!empty($this->input->post('clear'))) {
            $this->backet->clear();
        }
        header('Location: /mybacket/');
    }

    public function add() {
        $this->load->model('Backet', 'backet');
        $this->backet->addBacket();
    }

    public function check() {
        $this->load->model('Backet', 'backet');
        $this->backet->checkbacket();
    }

}

?>