<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

function cmp($a, $b) {
    if ($a->price <= $b->price) {
        return 0;
    } else {
        return 1;
    }
}

class Page extends CI_Controller {

    protected $sql;
    protected $session_user_info;
    private $params = array(); //свойство для хранения парметров страицы

    public function __construct() {
        parent::__construct();
        $this->load->helper('security');
        $this->load->model('Backet', 'backet'); //загружаем модель корзины для получения текущих значений корзины пользователя
        $this->load->model('Breadcrumb', 'bc');
        $this->load->model('Newsmodel', 'news'); //подгружаем модель новостей
        $this->params['news'] = $this->news->getLastNews();
        $this->params['scripts'] = array('<script type="text/javascript" src="/js/jquery.nicescroll.min.js"></script>');
        $this->params['backet_status'] = $this->backet->checkbacket();
    }

    public function index() {
        $qs = $_SERVER['QUERY_STRING']; //query_string строка запроса
        //если строка запроса содержит _escaped_fragment_
        if (strpos($qs, '_escaped_fragment_') !== false) {
            $this->getpartpage($qs);
        } else {
            $this->pages('general');
        }
    }

    /**
     * Метод делает запрос к модели и выводит содержание страницы
     */
    public function pages($pagename) {
        $this->load->model('Getpage', 'getpage');
        $pagename = xss_clean($pagename);
        $result_page = $this->getpage->getPageData($pagename);
        if ($result_page == false) {
            show_404();
        }
        if ($pagename != 'general') {
            $this->load->model('breadcrumb', 'bc');
            $breadcrumbs = $this->bc->breadcrumbs();
            $result_page[0]['breadcrumbs'] = $breadcrumbs;
            $this->params['content'] = $result_page[0]['content'];
        } else {
            $this->params['content'] = $this->viewloader->getContent('searchblock', ['partNum' => '']) . str_replace('class="search-block"', 'class="search-block hide"', $result_page[0]['content']);
        }
        $this->params['title'] = $result_page[0]['title'];
        $this->params['description'] = $result_page[0]['description'];
        $this->params['sess_data'] = $this->session_user_info;
        echo $this->viewloader->getView($this->params);
    }

    public function status() {
        $this->load->model('news');
        $zakaz = $this->input->post('zakaznumber', TRUE);
        $result_page[0]['content'] = '<form action="" method="POST"><div class="get_status_block"><input type="text" value="" name="zakaznumber"  placeholder="Введите номер вашего заказа" id="checkzakaz_input"/><input type="submit" value="OK" id="checkzakaz_submit"/></div></form><script type="text/javascript" src="/js/checkzakaz.js"></script>';
        if (!empty($zakaz)) {
            $this->load->model('checkzakaz');
            $zakazInfo = $this->checkzakaz->getZakazInfo($zakaz);
            echo $this->checkzakaz->invoice_html_create($zakazInfo);
            return null;
        }
        $sess_data = $this->session_user_info;
        $result_page[0]['sess_data'] = $sess_data;
        $a['title'] = 'Проверка состояния вашего заказа';
        $a['sess_data'] = $sess_data;

        $this->params['title'] = 'Проверка состояния вашего заказа';
        $this->params['content'] = $result_page[0]['content'];
        $this->load->model('viewloader', 'vl');
        echo $this->vl->getView($this->session_user_info, $this->params);
    }

    public function print_invoice($id, $hash) {
        $checksum = md5('uywevgweeewefe' . $id);
        if ($checksum === $hash) {
            $this->load->model('admin/admin_delivery');
            $array = $this->admin_delivery->getDelivery($id);
            $array[0]['phone'] = substr($array[0]['phone'], 0, 3) . '****' . substr($array[0]['phone'], 7, $array[0]['phone']);
            $html = $this->admin_delivery->invoice_html_create($array[0]);
            echo $html . '<script>window.onload = function () {
									window.print();
							}</script>';
        }
    }

    
    private function getpartpage($qs) {
        //echo phpinfo(); exit;
        $this->load->model('viewloader', 'vl');
        $arr = explode('=', $qs);
        $partNum = $arr[1];
        $key = $qs;
        if (empty($partNum)) {
            $this->pages('general');
            return;
        }
        $this->load->model('Cachemodel', 'cache');
        $cache = $this->cache->load(md5($key));
        if (empty($cache)) {
            $data = file_get_contents('http://' . $_SERVER['HTTP_HOST'] . '/parts/getallparts/' . $partNum);
            $this->cache->save(md5($key), $data, 60 * 60 * 24 * 30);
        } else {
            $data = $cache;
        }
        $data = json_decode($data);
        $this->load->model('news');
        $result_page = array();
        uasort($data, 'cmp');
        $sess_data = $this->session_user_info;
        $result_page[0]['partdata'] = $data;
        $this->params['title'] = $data[0]->brandName . ' ' . $data[0]->artikul . ' ' . $data[0]->description;
        $this->params['description'] = $data[0]->description;
        $this->params['content'] = $this->load->view('partlist', $result_page[0], true);
        echo $this->vl->getView($this->session_user_info, $this->params);
    }

    

    /**
     * Метод аксессуаров
     * 
     * @param string $pagename Имя страницы default:void
     */
    public function acessories($pagename = '') {
        $this->load->model('acessorymodel', 'as');
        $this->load->model('cachemodel', 'cache');
        $key = md5('ejhdsrpokjohnkweytih' . $pagename);
        $lim = 50;
        $this->load->library('pagination');
        $config['base_url'] = '/page/acessories/' . $pagename;

        $config['per_page'] = $lim;
        $config['use_page_numbers'] = TRUE;
        $config['first_link'] = 'Первая';
        $config['last_link'] = 'Последняя';

        //$config['use_page_numbers'] = TRUE;
        //Подгружаем модель для работы с аксессуарами
        if (empty($pagename)) {
            $this->load->model('viewloader', 'vl');
            $cache = $this->cache->load($key);
            $this->params['title'] = 'Аксессуары';
            if (empty($cache)) {
                /* $this->params['content']=$this->as->getTablesAllCategories($this->as->getAllCategories()); */
                $this->params['content'] = $this->as->getTablesAllCategoriesFlex($this->as->getAllCategories());
                $this->cache->save($key, $this->params['content'], 60 * 60);
            } else {
                $this->params['content'] = $cache;
            }
            echo $this->vl->getView($this->session_user_info, $this->params);
        } elseif ($pagename == 'ajax') {
            $pgname = $this->input->post('locate');
            $pgname = str_replace('http://sts2.ru/page/acessories/', '', $pgname);
            $a = explode('/', $pgname);
            $pagename = $a[0];
            $pagenum = (!empty($a[1])) ? $a[1] : 1;
            $key = md5($key . $pagenum . implode($this->uri->uri_to_assoc(4)));
            $cache = $this->cache->load($key);
            if (empty($cache)) {
                $res = $this->as->getCategory($pagename, $pagenum, $this->uri->uri_to_assoc(4), false);
                $this->cache->save($key, $res, 60 * 60);
            } else {
                $res = $cache;
            }
            $pagination = '<div class="pagination">Найдено ' . $res['counted'] . '</div>';
            $res['pagination'] = $pagination;


            $res['tableOnly'] = true;
            $html = $this->load->view('acessorylist', $res, true);
            echo $html;
            exit;
        } else {

            $key = md5($key . $pagenum . implode($this->uri->uri_to_assoc(4)) . $this->uri->segment(4));
            $cache = $this->cache->load($key);
            if (empty($cache)) {
                $res = $this->as->getCategory($pagename, $this->uri->segment(4), $this->uri->uri_to_assoc(5));
                $this->cache->save($key, $res, 60 * 60);
            } else {
                $this->cache->remove($key);
                $res = $cache;
            }
            $this->load->model('breadcrumb', 'bc');
            $this->params['breadcrumbs'] = $this->bc->breadcrumbs(array('/page/acessories' => 'Аксессуары'));
            $config['total_rows'] = $res['counted'];
            $config['uri_segment'] = 4;
            $this->pagination->initialize($config);
            $pagination = $this->pagination->create_links();
            $params = '';
            foreach ($this->uri->uri_to_assoc(5) as $k => $v) {
                $params .= $k . '/' . $v . '/';
            }
            $params = str_replace('//', '/', $params);
            $pagination = str_replace('">', '/' . $params . '">', $pagination);
            $pagination = '<div class="pagination">' . $pagination . '</div>';
            $res['pagination'] = $pagination;
            $this->load->model('viewloader', 'vl');

            $this->params['title'] = $res['title'];
            $this->params['content'] = $this->as->acessoryHtmlGenerator($res);

            echo $this->vl->getView($this->session_user_info, $this->params);
        }


        //Если pagename пустой, то выводим все категории аксессуаров
        //Если не пустой, то надо получить ID категории и вывести возможные параметры и таблицу товаров
    }

    /**
     * Метод выводит страницу с результатами поиска по артикулу
     * @param string $partnumber
     * @return void 
     */
    public function find($partnumber = '') {
        if (!empty($this->input->post('part_number'))) {
            $part_number = stringSanitize($this->input->post('part_number'));
        } elseif (!empty($partnumber)) {
            $part_number = stringSanitize($partnumber);
        } else {
            $this->params['content'] = $this->viewloader->getContent('searchblock', ['partNum' => '']);
        }

        if (!empty($part_number)) {
            $this->load->model('json');
            $this->params['content'] = $this->viewloader->getContent('searchblock', ['partNum' => $part_number]) . $this->viewloader->getContent('partlist', ['partdata' => $this->json->getPartsAllSuppliers($part_number)], true);
        }
        $this->params['title'] = $this->lang->line('template_tablesearch_title');
        $this->params['description'] = $this->lang->line('template_tablesearch_description');
        echo $this->viewloader->getView($this->params);
    }

}
