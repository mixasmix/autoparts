<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @author mixasmix
 * @version 1.0
 */

class Brands extends CI_Controller {
    protected $sql;
    protected $session_user_info;
    private $params=array();//свойство для хранения парметров страицы

    public function __construct(){
        parent::__construct();
        $this->sql=SQL::getInstance();
        $this->load->helper('security');
        $this->uRole();
        $this->params['sess_data']=$this->session_user_info;
        $this->params['scripts']=array('<script type="text/javascript" src="/js/jquery.nicescroll.min.js"></script>');
    }
    /**
     * Метод выводит страницу брендов
     * 
     */
    public function index(){
         //$this->output->cache(5);
        $this->load->model('brandmodel', 'bm');
        $brands=$this->bm->getBrands();
        $brandtable=$this->bm->brandtable($brands);
        $this->params['title']='Бренды';
        $this->params['description']='Бренды';
        $this->params['content']= $this->load->view($this->config->item('default_template').'/brandlist', array('brandtable'=>$brandtable, 'breadcrumbs'=>$breadcrumbs), true);
        $this->load->model('viewloader', 'vl');
        echo $this->vl->getView($this->session_user_info, $this->params);
       
         
    }
    /**
     * Метод выводит страницу бренда
     * @param int $bid
     */
    public function brand($bid){
         //$this->output->cache(5);
        $bid=abs($bid*1);
         $sess_data=$this->session_user_info;
        $result_page[0]['sess_data']=$sess_data;
         $this->load->model('brandmodel', 'bm');
        $brandInfo=$this->bm->getBrandInfo($bid);
        if(empty($brandInfo)){
            show_404();
            exit;
        }
        if(empty($brandInfo['description'])){
            $brandInfo['description']='<h2>'.$brandInfo['name'].'</h2><br>Для этого бренда еще нет описания';
        }
        $this->load->model('breadcrumb', 'bc');
        $brcmb['/brands/']='Бренды';
        $breadcrumbs=$this->bc->breadcrumbs($brcmb);
        $this->params['title']=$brandInfo['name'];
        $this->params['description']=$brandInfo['name'];
        $this->params['content']=  $this->load->view($this->config->item('default_template').'/brandpage', array('brandInfo'=>$brandInfo, 'role'=>$sess_data['id_role'], 'breadcrumbs'=>$breadcrumbs), true);
        $this->load->model('viewloader', 'vl');
        echo $this->vl->getView($this->session_user_info, $this->params);
    }
    
    public function addvote(){
        $post=$this->input->post(NULL, TRUE);
       
        $sess_data=$this->session_user_info;
        if(empty($sess_data)){
            return false;
        }
        if($sess_data['id_role']==1 or $sess_data['id_role']==7){
            return false;
        }
        $id_user=$sess_data['id'];
        $this->load->model('brandmodel', 'bm');
        echo $this->bm->addVote($post['id'], $post['rating'], $post['comment'], $id_user, $post['art'], $post['model']);
    }
}