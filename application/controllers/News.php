<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class News extends CI_Controller {
        protected $sql;
        protected $session_user_info;
        private $params=array();//свойство для хранения парметров страицы
	public function __construct(){
		parent::__construct();
		$this->load->model('json');
                $this->sql=SQL::getInstance();
		$this->load->helper('security');
                $this->uRole();
                $this->params['sess_data']=$this->session_user_info;
                $this->params['scripts']=array('<script type="text/javascript" src="/js/jquery.nicescroll.min.js"></script>');
	}
        /**
         * То что будет при заходе в новости
         */
        public function index(){
            $this->article();
        }
        /**
         * Выводит двадцать последних новостей
         */
        public function article($id='', $page=''){
             
            $this->load->model('newsmodel', 'nm');
            $this->load->model('breadcrumb', 'bc');
            
           
            if(empty($id)){
                $brcmb['/news/']='Новости';
                $breadcrumbs=$this->bc->breadcrumbs($brcmb);
                $result_page[0]['breadcrumbs']= $breadcrumbs;
                /**
                 * Подгружаем пагинацию
                 */
                $lim=20;
                $this->load->library('pagination');
                $config['base_url'] = '/news/page/';
                $config['total_rows'] =$this->nm->getCount();
                $config['per_page'] = $lim; 
                $config['use_page_numbers'] = TRUE;
                $config['first_link'] = 'Первая';
                $config['last_link'] = 'Последняя';
                $config['uri_segment'] = 3;
                $this->pagination->initialize($config);
                $pagination=$this->pagination->create_links();
                
                $sess_data=$this->session_user_info;
		$result_page[0]['sess_data']=$sess_data;
                if(empty($page)){
                    $result_page[0]['news']=$this->nm->getLastNews(1, $lim);
                }else {
                    $result_page[0]['news']=$this->nm->getLastNews($page, $lim);
                }
                $result_page[0]['pagination']=$pagination;
                $this->params['news']=$result_page[0]['news'];
                $this->params['title']='Новости';
                $this->params['description']='Новое на сайте';
                $this->params['content']=$this->load->view('news', $result_page[0], true);
                $this->load->model('viewloader', 'vl');
                echo $this->vl->getView($this->session_user_info, $this->params);
                /*
                $result_page[0]['pagination']=$pagination;
                $a['title']='Новости';
                $a['description']='У нас появились новые  автозапчасти для иномарок';
                $a['sess_data']=$sess_data;
                $a['scripts']=array('<script type="text/javascript" src="/js/jquery.nicescroll.min.js"></script>');
		$this->load->view('head', $a);
		$this->load->view('header', array('auth'=>$sess_data['role'], 'sessdata'=>$sess_data));
		$this->load->view('backet',  $result_page[0]);
		$this->load->view('news', $result_page[0]);
		$this->load->view('footer');
		$this->load->view('bottom', array('sess_data'=>$sess_data));*/
            }else{
                $brcmb['/news/']='Новости';
                $breadcrumbs=$this->bc->breadcrumbs($brcmb);
                $result_page[0]['breadcrumbs']= $breadcrumbs;
                $sess_data=$this->session_user_info;
		$result_page[0]['sess_data']=$sess_data;
                $news=$this->nm->getArticle($id);
                if(empty($news)){
                    show_404();
                }
                $result_page[0]['news']=$news;
                $this->params['title']=$news['title'];
                $this->params['description']=$news['description'];
                $this->params['content']=$this->load->view('newspage', $result_page[0], true);
                $this->load->model('viewloader', 'vl');
                echo $this->vl->getView($this->session_user_info, $this->params);
                /*
                $result_page[0]['news']=$news;
                $a['title']=$news['title'];
                $a['description']=$news['description'];
                $a['sess_data']=$sess_data;
                $a['scripts']=array('<script type="text/javascript" src="/js/jquery.nicescroll.min.js"></script>');
		$this->load->view('head', $a);
		$this->load->view('header', array('auth'=>$sess_data['role'], 'sessdata'=>$sess_data));
		$this->load->view('backet',  $result_page[0]);
		$this->load->view('newspage', $result_page[0]);
		$this->load->view('footer');
		$this->load->view('bottom', array('sess_data'=>$sess_data));
                 */
              
            }
            
            
            
                
        }
        public function page($pagenum=1){
                $this->article(false, $pagenum);
        }
        
}