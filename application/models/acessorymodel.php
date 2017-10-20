<?php
/**
 * Модель для работы с аксессуарами
 */

class Acessorymodel extends CI_Model {
    protected $sql;
    public function __construct(){
            parent::__construct();
            $this->sql=SQL::getInstance();
            $this->sql->query("SET NAMES 'utf8';");
    }
    
    /**
     * Метод получает все категории аксессуаров и возвращает массив
     * @return array Массив категорий
     */
    public function getAllCategories($show=0){
        $query="SELECT id, category, pseudonim FROM categories";
        $stm=$this->sql->prepare($query);
        $stm->execute();
        return $stm->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
     * Метод выводит содержимое выбранной категории
     * 
     * @param string $pseudonim Псевдоним страницы
     * @param int $page Номер страницы Default:1
     * @param array $param Массив параметров Default:0
     * @return array Массив с брендами, параметрами и товарами в данной категории
     * 
     */
    public function getCategory($pseudonim, $page=1, $param=0, $lim=50){
        
        //var_dump($pseudonim); exit;
        $limitStart=($page*$lim-$lim)<0?0:$page*$lim-$lim;
        $linitEnd=$limitStart+$lim;
        //$limit=50;
        //Узнаем id категории
        $query="SELECT id, category FROM categories WHERE pseudonim=:pseudo";
        $stm=$this->sql->prepare($query);
        $stm->execute(array(':pseudo'=>$pseudonim));
        $result=$stm->fetchAll(PDO::FETCH_ASSOC);
        $catId=$result[0]['id'];
        $catName=$result[0]['category'];
        //var_dump($catId); exit;
        //Получаем все параметры категории
        /**
         * Хранимая процедура getParamsThisCat принимает один параметр catId - идентификатор категории
         * Возвращает все возможные параметры для данной категории
         */
        $stm=$this->sql->prepare('CALL getParamsThisCat(:catId)');
        $stm->execute(array(':catId'=>$catId));
       // var_dump($stm->errorInfo()); exit;
        $params=$stm->fetchAll(PDO::FETCH_ASSOC);
        $stm->closeCursor();
        
        //обходим массив параметров и  
        //получаем возможные значения параметров
        $stm=$this->sql->prepare('CALL getValuesToCatAndParams(:catId, :pId);');
        
        $newparams=array();
        foreach($params as $p){
            $stm->execute(array(':catId'=>$catId, ':pId'=>$p['id']));
            
            $values=$stm->fetchAll(PDO::FETCH_ASSOC);
            $stm->closeCursor();
            $p['values']=$values;
            $newparams[]=$p;
        }
        unset ($params);
        if(!empty($lim)){
            $lim=' LIMIT '.$limitStart.', '.$lim;;
        }
        
        //запрос будет такой 
        $query="SELECT DISTINCT a.id, a.`artikul`, a.`price`, a.`name`, a.`description`, a.`fullname`, b.`id` AS brandId, b.`name` AS brandName FROM acessories AS a 
                INNER JOIN acessory_state AS acs ON acs.`id_acessory`=a.`id`
                    INNER JOIN brands AS b ON b.`id`=acs.`id_brand`
                    INNER JOIN categories AS c ON c.`id`=acs.`id_category`
                    INNER JOIN param_state AS ps ON ps.`id_category`=c.`id` AND ps.`id_acessory`=a.`id`
                    INNER JOIN parameters AS p ON p.`id`=ps.`id_param_name`
                    INNER JOIN param_values AS pv ON pv.`id`=ps.`id_param_value`
                    WHERE ps.`id_category`=".$catId;
        if(!empty($param)){
            //подсчитаем количество параметров
            $cp=count($param);
           //var_dump($cp); exit;
            if($cp>=1){

                $pv=' AND ps.`id_param_value` IN (';
                $pn=' AND ps.`id_param_name` IN (';
                foreach($param as $k=>$v){
                    $k=abs($k*1); //делаем int из параметров
                    $v=abs($v*1);
                    if($k==40){
                        if($cp==1){
                          $pv='';
                          $pn='';  
                        }elseif($cp>1)
                            $cp-=1; //если параметров больше одного и один из них 40- бренд, то единицу отнимаем
                        continue;
                    }
                    
                    $pv.=$v.',';
                    $pn.=$k.',';
                }
                    if($k==40){
                        if($cp==1){
                          $pv='';
                          $pn='';  
                        }
                        
                    }else{
                        $pv.=')';
                        $pn.=') GROUP BY 1 HAVING COUNT(*)='.$cp;
                    }
                if(!empty($param[40])){
                    $pv.='  AND b.id='.$param[40];
                }
            }elseif(!empty($param[40]) AND $cp==1){
               $pv='  AND b.id='.$param[40];
               $pn='';
            }
            $q1=$query." ".$pv." ".$pn.$lim;
            $q1=str_replace(',)',')', $q1);
        }else{
            
            $q1=$query." ORDER BY a.id ASC".$lim;//теперь надо посчитать сколько всего записей в выборке
            $q=str_replace('DISTINCT a.id, a.`artikul`, a.`price`, a.`name`, a.`description`, a.`fullname`, b.`id` AS brandId, b.`name` AS brandName ',' COUNT(DISTINCT a.id) AS counted ', $q1);
            $q=str_replace($lim, '', $q);
            $stmc=$this->sql->query($q);
            $count=$stmc->fetchAll(PDO::FETCH_ASSOC);

            $count=$count[0]['counted'];
        }
        //var_dump($q1); exit;
        $stm=$this->sql->query($q1);
        $res=$stm->fetchAll(PDO::FETCH_ASSOC);
        if(empty($count)){
            $count=count($res);
        }
        
        //Запросим изображения для данного артикула
        $q="CALL getAcessoryImages(:aid);"; //передаем в сохраненную процедуру id аксессуара
        $stm=$this->sql->prepare($q);
        $result=array();
        foreach ($res as $r){
            
            $stm->execute(array(':aid'=>$r['id']));
            $imgs=$stm->fetchAll(PDO::FETCH_ASSOC);
            $stm->closeCursor();
            if(!empty($imgs)){
                $r['images']=$imgs;
            }else{
                $r['images']=false;
            }
            $result[]=$r;
        }
        unset($res);
        
        //Запросим параметры для данного артикула
        $q="CALL getParamsThisArticle(:aid);"; //передаем в сохраненную процедуру id аксессуара
        $stm=$this->sql->prepare($q);
        $res=array();
        foreach ($result as $r){
            
            $stm->execute(array(':aid'=>$r['id']));
            $params=$stm->fetchAll(PDO::FETCH_ASSOC);
            $stm->closeCursor();
            if(!empty($params)){
                $r['params']=$params;
            }else{
                $r['params']=false;
            }
            $res[]=$r;
        }
        unset($result);
        
        $brands=$this->getBrandsThisCategory($catId);
        
        $arr=array();
        $arr['brands']=$brands;
        $arr['params']=$newparams;
        $arr['details']=$res;
        $arr['counted']=$count;
        $arr['title']=$catName; //имя категории
        return $arr;
       
    }
    
    /**
     * Метод создает табличку с категориями аксессуаров
     * 
     * @param array $array Массив категорий аксессуаров
     */
    public function getTablesAllCategories($array){
        
        /*$this->load->library('table');
        
        echo $this->table->generate($array); exit;*/
        $html='<div id="acessories_list"><table><tbody>';
            $i=0;
            foreach($array as $a){
                //var_dump($a);exit;
                if($i==0){
                   $html.='<tr>'; 
                }
                    $html.='<td><div class="asessories_list_icon_div "><a href="/page/acessories/'.$a['pseudonim'].'/1"><div  style="background: url(/images/'.$a['pseudonim'].'.png);"></div><span>'.$a['category'].'</span></a></div></td>';
                if($i==4){
                   $html.='</tr>'; 
                   $i=0;
                   continue;
                }
                $i++;
            }                
        $html.='</tbody></table></div>';
        
        return $html;
        
    }
    /**
     * Метод создает табличку flex с категориями аксессуаров
     * 
     * @param array $array Массив категорий аксессуаров
     */
    public function getTablesAllCategoriesFlex($array){
        
        /*$this->load->library('table');
        
        echo $this->table->generate($array); exit;*/
        $c=count($array);
        $html='<div id="acessories_list">';
            $i=0;
            foreach($array as $a){
                
                    $html.='<div class="asessories_list_icon_div "><a href="/page/acessories/'.$a['pseudonim'].'/1"><div  style="background: url(/images/'.$a['pseudonim'].'.png);"></div><span>'.$a['category'].'</span></a></div>';
                if($i==4){
                   
                }
                $i++;
            }
            $o=$c%4;/**остаток от деления*/
            for($i=0; $i==$o; $i++){
                $html.='<div class="asessories_list_icon_div "></div>';
            }
        $html.='</div>';
        
        return $html;
        
    }
    
    
    /**
     * Метод возвражает массив брендов в данной категории
     * 
     * @param int $catId ID категории
     * @return array
     */
    public function getBrandsThisCategory($catId){
        $stm=$this->sql->prepare('CALL getBrandsThisCategory(:catId)');
        $stm->execute(array(':catId'=>$catId));
        return $stm->fetchAll(PDO::FETCH_ASSOC);
        $stm->closeCursor();
    }
    
    /**
     * Метод генерирует HTML с товарами
     * 
     * @param array $arr массив с данными о товарах
     * @return string Сгенерированная HTML строка
     */
    public function acessoryHtmlGenerator($arr){
        $html=$this->load->view('acessorylist', $arr, true);
        
        return $html;
    }
    
    public function priceChecker(){
        $time=time(); //текущее время будет
        $lastupd=$time-1728000; //время обновления старше 20 дней
        $this->load->helper('getpage');
        $stm=$this->sql->prepare('CALL get100RandomAcessoriesEmptyPrice(:lastupd)');//получаем сто товаров без цен или с просроченными ценами
        $stm->execute(array(':lastupd'=>$lastupd));
        $res=$stm->fetchAll(PDO::FETCH_ASSOC);
        $stm->closeCursor();
        
        $updquery="UPDATE acessories SET price=:price, lastupd=:lastupd WHERE id=:id";
        $stm=$this->sql->prepare($updquery);
        
        //если такого артикула нет в выборке, то установим price в null
        $updquery2="UPDATE acessories SET price=(NULL), lastupd=:lastupd WHERE id=:id";
        $stm2=$this->sql->prepare($updquery2);
       
        foreach($res as $r){ 
            $data=getPage('http://'.$_SERVER['HTTP_HOST'].'/parts/getallparts/'.$r['artikul'], '', '', 0, 10);
            $price=0;
            $data=json_decode($data);
            if(!empty($data)){
                foreach($data as $d){
                    if($price==0){
                       $price=$d->price;
                    }
                    $art=$d->artikul;
                    $bname=$d->brandName;
                    if($art===$r['artikul'] AND $bname===$r['brand']){
                        if($d->price<$price){
                            $price=$d->price;
                        }
                    }else{
                        $price=0;
                        $stm2->execute(array(':lastupd'=>$time, ':id'=>$r['id']));
                        continue;
                    }
                }

                if($price!=0){
                    $stm->execute(array(':price'=>$price, ':lastupd'=>$time, ':id'=>$r['id']));
                }
            }
        }
    }
    
    /***Административная часть***/
    
    /**
     * Метод возвращает все категории аксессуаров
     * @return array
     */
    public function getAdmAllCategories(){
        return $this->getAllCategories(1);
    }
    /**
     * Метод добавляет новую категорию 
     */
    public function addNewCategory(){
        
    }
}