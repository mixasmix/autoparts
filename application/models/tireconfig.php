<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Tireconfig extends CI_Model {
	private $sql; //тут у нас класс базы данных
	public function __construct(){
		parent::__construct();
		$this->sql=SQL::getInstance();
		$this->load->helper('cookie');
		$this->sql->query("SET NAMES 'utf8';");
	}
        /**
         * Метод возвращает список всех радиусов
         * @return array
         */
        public function getAllRadius(){
            $sql="SELECT t1.id, t1.Radius FROM tire_radius t1";
            $stm=$this->sql->prepare($sql);
            $stm->execute();
             $arr=$stm->fetchAll(PDO::FETCH_ASSOC);
            if(!empty($arr)){
                return $arr;
            } else{
                return false;
            }
        }
        /**
         * Метод возвращает список всех возможных ширин профиля
         * @return array
         */
        public function getAllWidth(){
            $sql="SELECT t1.id, t1.width FROM tire_width t1";
            $stm=$this->sql->prepare($sql);
            $stm->execute();
            $arr=$stm->fetchAll(PDO::FETCH_ASSOC);
            if(!empty($arr)){
                return $arr;
            } else{
                return false;
            }
        }
        /**
         * Метод возвращает все возможные величины высоты профиля
         * @return boolean
         */
        public function getAllHeight(){
            $sql="SELECT t1.id, t1.height FROM tire_height t1";
            $stm=$this->sql->prepare($sql);
            $stm->execute();
            $arr=$stm->fetchAll(PDO::FETCH_ASSOC);
            if(!empty($arr)){
                return $arr;
            } else{
                return false;
            }
        }
        /**
         * Метод возвращает все возможные типы шин
         * @return array
         */
        public function getAllTypes(){
            $sql="SELECT t1.id, t1.type FROM tire_type t1";
            $stm=$this->sql->prepare($sql);
            $stm->execute();
            $arr=$stm->fetchAll(PDO::FETCH_ASSOC);
            if(!empty($arr)){
                return $arr;
            } else{
                return false;
            }
        }
        /**
         * Метод возвращает все бренды
         * @return array
         */
        public function getAllBrands(){
            $sql="SELECT t1.id, t1.brand FROM tire_brand t1";
            $stm=$this->sql->prepare($sql);
            $stm->execute();
            $arr=$stm->fetchAll(PDO::FETCH_ASSOC);
            if(!empty($arr)){
                return $arr;
            } else{
                return false;
            }
        }
        /**
         * Метод возвращает массив всей инвормации для конфигуратора
         * @return array Массив информации для конфигуратора
         */
        public function getAllInfoConfigurator(){
            $arr=array();
            $arr['radius']=$this->getAllRadius();
            $arr['brands']=$this->getAllBrands();
            $arr['widths']=$this->getAllWidth();
            $arr['heights']=$this->getAllHeight();
            $arr['types']=$this->getAllTypes();
            return $arr;
        }
        
        public function getRandomTires(){
            $sql="SELECT t2.artikul1, t2.artikul2, t3.brand, t4.height, t5.model, t6.Radius, t7.type, t8.width, t9.link FROM tire_state t1 
                    INNER JOIN tires t2 ON t2.id=t1.id_tire
                    INNER JOIN tire_brand t3 ON t3.id=t1.id_brand
                    INNER JOIN tire_height t4 ON t4.id=t1.id_height
                    INNER JOIN tire_model t5 ON t5.id=t1.id_model
                    INNER JOIN tire_radius t6 ON t6.id=t1.id_radius
                    INNER JOIN tire_type t7 ON t7.id=t1.id_type
                    INNER JOIN tire_width t8 ON t8.id=t1.id_width
                    INNER JOIN tire_img t9 ON t9.id=t1.id_img
                    WHERE t1.id_img!=0
                    LIMIT 0, 20";
            $stm=$this->sql->prepare($sql);
            $stm->execute();
            $arr=$stm->fetchAll(PDO::FETCH_ASSOC);
            if(!empty($arr)){
                return $arr;
            } else{
                return false;
            }
        }
        /**
         * Метод принимает массив условий и возвращает массив параметров, соответствующих данному условию
         * @param array $arr
         */
        public function getInfoConfigurator($arr){
             $sql='SELECT t2.artikul1, t2.artikul2, t3.brand, t5.model, t6.Radius, t7.type, t8.width, t4.height, t9.link   FROM tire_state t1 
                    INNER JOIN tires t2 ON t2.id=t1.id_tire
                    INNER JOIN tire_brand t3 ON t3.id=t1.id_brand
                    INNER JOIN tire_height t4 ON t4.id=t1.id_height
                    INNER JOIN tire_model t5 ON t5.id=t1.id_model
                    INNER JOIN tire_radius t6 ON t6.id=t1.id_radius
                    INNER JOIN tire_type t7 ON t7.id=t1.id_type
                    INNER JOIN tire_width t8 ON t8.id=t1.id_width
                    INNER JOIN tire_img t9 ON t9.id=t1.id_img';
            $i=0;
            if(!empty($arr)){
                $sql.=' WHERE ';
            }
            if(!empty($arr['radius'])){
                $sql.='t1.id_radius=:id_radius';
                $param[':id_radius']=$arr['radius'];
                $i++;
            }
            if(!empty($arr['brand'])){
                $sql.=!empty($i)?' AND ':'';
                $sql.='t1.id_brand=:id_brand';
                $param[':id_brand']=$arr['brand'];
                $i++;
            }
            if(!empty($arr['height'])){
                $sql.=!empty($i)?' AND ':'';
                $sql.='t1.id_height=:id_height';
                $param[':id_height']=$arr['height'];
                $i++;
            }
            if(!empty($arr['width'])){
                $sql.=!empty($i)?' AND ':'';
                $sql.='t1.id_width=:id_width';
                $param[':id_width']=$arr['width'];
                $i++;
            }
            if(!empty($arr['type'])){
                $sql.=!empty($i)?' AND ':'';
                $sql.='t1.id_type=:id_type';
                $param[':id_type']=$arr['type'];
                $i++;
            }
            $stm=$this->sql->prepare($sql);
            $stm->execute($param);
            $arr=$stm->fetchAll(PDO::FETCH_ASSOC);
            
           $b=array(); 
                
                foreach($arr as $a){
                    if($a['link']!='none'){
                        $link=str_replace(':yadisk:/sts2/images/tires/','/uploads/tires/',$a['link']);
                        /*$file =$this->yd->getPreviewImage($link, '700x1000');*/
                        $a['img']=  $link;
                    }else{
                        $a['img']='/uploads/tires/no_tire.png';
                    }
                    $b[]=$a;
                }
                
                return $b;
        }
        
        
        public function countTire(){
            $sql="SELECT COUNT(t2.id) as count FROM tire_state t1 
                    INNER JOIN tires t2 ON t2.id=t1.id_tire
                    INNER JOIN tire_brand t3 ON t3.id=t1.id_brand
                    INNER JOIN tire_height t4 ON t4.id=t1.id_height
                    INNER JOIN tire_model t5 ON t5.id=t1.id_model
                    INNER JOIN tire_radius t6 ON t6.id=t1.id_radius
                    INNER JOIN tire_type t7 ON t7.id=t1.id_type
                    INNER JOIN tire_width t8 ON t8.id=t1.id_width
                    INNER JOIN tire_img t9 ON t9.id=t1.id_img";
            $stm=$this->sql->prepare($sql);
            $stm->execute();
            $arr=$stm->fetchAll(PDO::FETCH_ASSOC);
            if(!empty($arr)){
                return $arr[0]['count'];
            }else{
                return false;
            }
        }
        /**
         * Метод возвращает по 20 Шин в зависимости от $lim и $page
         * @param int $page номер страницы выдачи
         * @param type $lim Лимит выдачи
         * @return array
         */
        public function getTirePage($page=1, $lim=20){
            $limitStart=($page*$lim-$lim)<0?0:$page*$lim-$lim;
            $linitEnd=$limitStart+$lim;
            $sql='SELECT t2.artikul1, t2.artikul2, t3.brand, t5.model, t6.Radius, t7.type, t8.width, t4.height, t9.link   FROM tire_state t1 
                    INNER JOIN tires t2 ON t2.id=t1.id_tire
                    INNER JOIN tire_brand t3 ON t3.id=t1.id_brand
                    INNER JOIN tire_height t4 ON t4.id=t1.id_height
                    INNER JOIN tire_model t5 ON t5.id=t1.id_model
                    INNER JOIN tire_radius t6 ON t6.id=t1.id_radius
                    INNER JOIN tire_type t7 ON t7.id=t1.id_type
                    INNER JOIN tire_width t8 ON t8.id=t1.id_width
                    INNER JOIN tire_img t9 ON t9.id=t1.id_img LIMIT '.$limitStart.', '.$linitEnd;
            $stm=$this->sql->prepare($sql);
            $stm->execute();
            $arr=$stm->fetchAll(PDO::FETCH_ASSOC);
            if(!empty($arr)){
                $this->load->model('yaDiskClient', 'yd');
                $b=array(); 
                
                foreach($arr as $a){
                    if($a['link']!='none'){
                        $link=str_replace(':yadisk:/sts2/images/tires/','/uploads/tires/',$a['link']);
                        /*$file =$this->yd->getPreviewImage($link, '700x1000');*/
                        $a['img']=  $link;
                    }else{
                        $a['img']='/uploads/tires/no_tire.png';
                    }
                    $b[]=$a;
                }
                
                return $b;
            }else{
                return false;
            }
        }
        
        
        
        public function tableGenerate($arr){
            
                $this->load->library('table');
                $this->table->set_template(array( 'table_open'  => '<table class="tire_table">'));
                $i=0;
                $a=array();
                if(!empty($arr)){
                    foreach ($arr as $tire){
                        //var_dimp($tire); exit;
                        $art=!empty($tire['artikul2'])?$tire['artikul2']:$tire['artikul1'];
                        $html='<div class="tire_cart"  itemscope itemtype="http://schema.org/Product">
                                    <div class="tire_image"><img src="'.$tire['img'].'" alt="'.$tire['brand'].' '.trim(str_replace($tire['brand'], '',$tire['model'])).'" itemprop="image"/></div>
                                    <h4><span   itemprop="name" style="display:none">'.$tire['brand'].'</span><span  itemprop="brand">'.$tire['brand'].'</span> <span  itemprop="model">'.trim(str_replace($tire['brand'], '',$tire['model'])).'</span></h4>
                                    <h5><span  itemprop="description">'.$tire['width'].'/'.$tire['height'].$tire['Radius'].' '.$tire['type'].'</span></h5>

                                    <h6><a href="/#!'.$art.'"  itemprop="url" target="_blank">Найти предложения</a></h6>
                        </div>';
                        $a[]=$html;
                        /*if(count($arr)<4){
                            $this->table->add_row($a);
                        }
                        if($i==4){
                            $this->table->add_row($a);
                            $a=array();
                            $i=0;
                            continue;
                        }
                        $i++;*/
                    }
                
                }else{
                    return false;
                }
                $list=$this->table->make_columns($a, 5);
                //$this->table->set_heading(array('Name', 'Color', 'Size'));

                //$this->table->add_row(array('Fred', 'Blue', 'Small'));
                /*$this->table->add_row(array('Mary', 'Red', 'Large'));
                $this->table->add_row(array('John', 'Green', 'Medium'));*/

                return $this->table->generate($list);
        }
}