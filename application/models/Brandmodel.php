<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Класс работы с брендами
 */
class Brandmodel extends CI_Model {

    private $sql; //тут у нас класс базы данных

    public function __construct() {
        parent::__construct();
        $this->sql = SQL::getInstance();
        $this->load->helper('cookie');
        $this->sql->query("SET NAMES 'utf8';");
        //setlocale(LC_ALL, 'ru_RU');
    }

    /**
     * Метод выводит массив всех брендов в системе
     */
    public function getBrands() {
        $sql = "SELECT brands.id, brands.`name` FROM brands";
        $stm = $this->sql->prepare($sql);
        $stm->execute();
        $arr = $stm->fetchAll(PDO::FETCH_ASSOC);

        //надо отсортировать массив по первой букве
        $sortarr = array();
        foreach ($arr as $a) {
            if (!empty($a['name'])) {
             
                $key=mb_substr($a['name'], 0, 1); 
                
                $sortarr[$key][] = $a;
            }
        }
        ksort($sortarr);
        return $sortarr;
    }

    /**
     * Метод делает красивую табличку из массива брендов
     * @param array $arr Массив врендов
     * @return string HTML-табличка
     */
    public function brandtable($arr) {
        $html = '';
        //подсчитаем сколько всего элементов в массиве
        $arrlength = count($arr);
        $html.='<table class="table_brand_list">';
        $i = 0;
        foreach ($arr as $k => $v) {
            if ($i == 0) {
                $html.='<tr>';
            }
            $html.='<td><a href="" class="a_table_brand_list">' . $k . '</a>';
                //делаем блок с брендами
                $html.='<div class="div_table_brand_list"><p>';
                    foreach($v as $b){
                        $html.='<a href="/brands/brand/'.$b['id'].'" class="a_div_table_brand_list" >'.$b['name'].'</a><br>';
                    }
                $html.='</p></div>';
            $html.='</td>';
            if ($i == 5) {
                $html.='</tr>';
                $i = 0;
            } else {
                $i++;
            }
        }

        $html.='</table>';
        return $html;
    }
    /**
     * Метод возвращает массив данных модели
     * @param int $id ID модели
     * @return array Массив данных модели
     */
    public function getBrandInfo($id){
        $sql="SELECT id, `name`, description FROM brands  WHERE id=:id";
        $stm = $this->sql->prepare($sql);
        $stm->execute(array(':id'=>$id));
        $arr = $stm->fetchAll(PDO::FETCH_ASSOC);
        //получаем последние 20 комментариев к этому бренду
        $sql="SELECT t2.vote, t1.comment, t1.time, t3.login, t3.name FROM brand_comment t1 INNER JOIN brands_state t2 ON t1.id=t2.id_comment INNER JOIN users t3 ON t3.id=t2.id_user  WHERE t2.id_brand=:id AND t1.moderated=1 ORDER BY t1.time DESC LIMIT 0, 20";
        
        $stm = $this->sql->prepare($sql);
        $stm->execute(array(':id'=>$id));
        $arr2 = $stm->fetchAll(PDO::FETCH_ASSOC);
        if(!empty($arr2)){
            $arr[0]['rating']=$arr2;
        }
        return $arr[0]; 
    }
    /**
     * Метод для редактирования данных бренда
     * @param int $id ID редактируемого бренда
     * @param array $data массив данных для редактирования
     */
    public function editBrandInfo($id, $data){
         $sql="UPDATE brands SET description=:description, `name`=:name WHERE id=:id";
        $stm = $this->sql->prepare($sql);
        $stm->execute(array(':id'=>$id, ':description'=>$data['description'], ':name'=>$data['name']));
        
    }
    
    public function addVote($id, $rating, $comment, $id_user, $artikul='', $model=''){
        $id=(int)abs($id*1);
        $comment=  strip_tags($comment);
        $artikul=  strip_tags($artikul);
        $model=  strip_tags($model);
        if(!empty($artikul)){
            //нам надо выбрать артикул данного бренда если он есть
            $sql="SELECT t1.id FROM artikuls t1 INNER JOIN artikul_state t2 ON t1.id=t2.id_artikul WHERE t1.artikul=:art AND t2.id_brand=:brand";
             $stm = $this->sql->prepare($sql);
            $stm->execute(array(':art'=>$artikul, ':brand'=>$id));
            $arr = $stm->fetchAll(PDO::FETCH_ASSOC);
           if(!empty($arr)){
            $art_id=$arr[0]['id'];
           }else{
               $art_id=129;
           }
        }else{
            $art_id=129;
        }
        if(!empty($comment)){
            $sql="INSERT INTO brand_comment(comment, time) VALUES (:comment, :time)";
            $stm = $this->sql->prepare($sql);
            $stm->execute(array(':comment'=>$comment, ':time'=>time()));
            $com_id=$this->sql->lastInsertId();
            
            $sql="INSERT INTO brands_state(id_brand, id_user, id_comment, vote, id_artikul) VALUES (:id_brand, :id_user, :id_comment, :vote, :id_artikul)";
            $stm = $this->sql->prepare($sql);
            $stm->execute(array(':id_brand'=>$id, ':id_user'=>$id_user, ':id_comment'=>$com_id, ':vote'=>$rating, ':id_artikul'=>$art_id));
           
                return true;
            
        }else{
            return false;
        }
    }
    /**
     * Метод возвращает новые комментарии к бренду
     */
    public function getNewVote(){
        $sql="SELECT t1.vote,t5.id as id_comment, t5.`comment`,t4.artikul, t4.id as id_artikul, t3.login, t3.`name`, t2.`name` as brand_name, t2.id as id_brand 
                FROM brands_state t1 
                INNER JOIN brands t2 ON t1.id_brand=t2.id 
                INNER JOIN users t3 ON t3.id=t1.id_user 
                INNER JOIN artikuls t4 ON t4.id=t1.id_artikul 
                INNER JOIN brand_comment t5 ON t1.id_comment=t5.id
                WHERE t5.moderated=0 AND t1.deleted=0";
                $stm = $this->sql->prepare($sql);
                $stm->execute();
                $arr = $stm->fetchAll(PDO::FETCH_ASSOC);
                if(!empty($arr)){
                    return $arr;
                }else{
                    return false;
                }
    }
    public function deleteVote($id_commnet){
        $sql="UPDATE brands_state SET deleted=1 WHERE id_comment=:id_comment";
        $stm = $this->sql->prepare($sql);
        $stm->execute(array(':id_comment'=>$id_commnet));
        return true;
    }
    public function moderatedVote($id_commnet){
        $sql="UPDATE brand_comment SET moderated=1 WHERE id=:id_comment";
        $stm = $this->sql->prepare($sql);
        $stm->execute(array(':id_comment'=>$id_commnet));
        return true;
    }
    
    /**
     * Возвдращает массив брендов без описания
     * @return array Массив брендов
     * @return false Возвращает false В случае неудачи
     */
    public function getBrandsNoDescription(){
        $sql="SELECT id, `name` FROM brands WHERE description IS NULL ";
        $stm = $this->sql->prepare($sql);
        $stm->execute();
        $arr = $stm->fetchAll(PDO::FETCH_ASSOC);
        if(!empty($arr)){
            return $arr;
        }else{
            return false;
        }
    }
    
    /**
     * Метод возвращает рейтинг бренда
     * @param int $id ID бренда
     */
    
    public function getRating($id){
        $sql="SELECT   AVG(t2.vote) AS rating FROM brands t1 INNER JOIN brands_state t2 ON t1.id=t2.id_brand WHERE t2.deleted!=1 AND t1.`id`=:bn";
        $stm = $this->sql->prepare($sql);
        $stm->execute(array(':bn'=>$id));
        $arr = $stm->fetchAll(PDO::FETCH_ASSOC);
        if(!empty($arr[0]['rating'])){
            return $arr[0]['rating'];
        }else{
            return false;
        }
    }
    /**
     * Метод возвращает ID бренда по его имени
     * @param string $brandname Имя бренда
     * @return int ID бренда или false в случае неудачи
     */
    public function getBrandId($brandname){
        if(strpos($brandname, '/')){
            $brandname= explode('/', $brandname)[0];
            
        }
        
        $brandname= stringSanitize($brandname, 'а');
        
        
        $cahce=$this->cachemodel->load($brandname);
        if(!empty($cache)){
            return $cache;
        }
        if(strlen($brandname)<=3){//если имя бренда три символа или меньше
            $sql="SELECT id FROM brands WHERE `namenormalize`=:bn";
            $prop=[':bn'=>$brandname];
        }else{
            $sql="SELECT id FROM brands WHERE `namenormalize` LIKE :bn";
            $prop=[':bn'=>'%'.$brandname.'%'];
        }
        $stm = $this->sql->prepare($sql);
         $stm->execute($prop);
        $arr = $stm->fetchAll(PDO::FETCH_ASSOC); 
        if(!empty($arr[0]['id'])){
            $this->cachemodel->save($brandname, $arr[0]['id'], 60*60*24);
            return $arr[0]['id'];
        }
        return false;
    }
    
    public function getRatingCount($brandname){
        $sql="SELECT   COUNT(t2.vote)  AS count FROM brands t1 INNER JOIN brands_state t2 ON t1.id=t2.id_brand WHERE t2.deleted!=1 AND t1.`name`=:bn";
        $stm = $this->sql->prepare($sql);
        $stm->execute(array(':bn'=>$brandname));
        $arr = $stm->fetchAll(PDO::FETCH_ASSOC);
        if(!empty($arr[0]['count'])){
            return $arr[0]['count'];
        }else{
            return 0;
        }
    }
}
