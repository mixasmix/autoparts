<?php

//error_reporting(E_ALL);
/**
 * @author MDTreloni
 * @email mixasmix@mail.ru 
 * @copyright 2015
 */
require_once('simpledom/simple_html_dom.php');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Getartikulinfo extends CI_Model {
    //Блядь пошли все в жопу, будем парсить через гугл
	private $url_info="http://vag-part.ru/webservices/?key=59ae799555021052738682fd7f4ac9a2&";
	/*private $automag=array(
            'www.autodoc.ru',
            'www.port3.ru',
            'www.autopiter.ru',
            'amzp.ru',
            'avto.pro',
            '4mycar.ru',
            //'parts.wse.by',
            'zap-moskva.ru',
            'limru.ru',
            'plentycar.ru',
            'euroauto.ru',
            'www.ukrshops.com.ua',
            'www.vianor-tyres.ru',
            'e96.ru',
            'www.e-katalog.ru',
            'www.yokohama-online.com',
            'tyres.dn.ua'
        );*/
        public function __construct(){
		parent::__construct();
		$this->load->helper('getpage');
		$this->sql=SQL::getInstance();
                 $this->sql->query("SET NAMES 'utf8';");
	}
	
	public function getImage($brand, $part){
		$url=$this->url_info.'flag=io&artikul='.$part;
                if(!empty($brand)){
                    $url.='&brand='.$brand;
                }
		$html=getPage($url);
                $sm=new SimpleXMLElement($html);
                if(!empty($sm->artikul->images)){
                    //$i=0;
                    foreach($sm->artikul->images as $img){
                        $img_urls[]=$img->image;
                        
                    }
                }
                
                if(!empty($img_urls)){
                    return $img_urls;
                } else{
                    return false;
                }
	}
        public function getPartCard($brand, $part){
           
            $url=$this->url_info.'flag=icoab&artikul='.$part;
                if(!empty($brand)){
                    $url.='&brand='.$brand;
                }
               
            $html=getPage($url, '', '', 0, 10);
           
            $sm=new SimpleXMLElement($html);
            $information=array();
            $information['brand']=(string)$sm->artikul->BRAND;
            $information['artikul']=(string)$sm->artikul->ARTICLE_NR;
            $information['description']=(string)$sm->artikul->DESC;
            
            if(count($sm->artikul->characteristic->char)){  
                foreach($sm->artikul->characteristic->char as $char){
                    $tmp=array();
                    $tmp['name']=(string)$char->NAME;
                    $tmp['value']=(string)$char->VALUE;
                    $information['char'][]=$tmp;
                }
            }
            
            if(count($sm->artikul->advcharacteristic->advchar)){  
                foreach($sm->artikul->advcharacteristic->advchar as $char){
                    if(!empty((string)$char->NAME) && !empty((string)$char->VALUE)){
                        $tmp=array();
                        $tmp['name']=(string)$char->NAME;
                        $tmp['value']=(string)$char->VALUE;
                        $information['advchar'][]=$tmp;
                    }
                }
            }
            
            if(count($sm->artikul->applicability->work)){
                 foreach($sm->artikul->applicability->work as $w){
                    $tmp=array();
                    $tmp['mark']=(string)$w->MARK;
                    $tmp['model']=(string)$w->MODEL;
                    $tmp['type']=(string)$w->TYPE;
                    $tmp['fromyear']=(string)$w->FROMYEAR;
                    if(strlen((string)$w->BEFOREYEAR)>4)
                        $tmp['beforeyear']=(string)$w->BEFOREYEAR;
                    else 
                        $tmp['beforeyear']='по н.в.';
                    
                    $tmp['powhp']=(string)$w->POWHP;
                    $tmp['cyl']=(string)$w->CYL;
                    $tmp['eng']=(string)$w->ENG;
                    $tmp['typeng']=(string)$w->TYPENG;
                    $tmp['typfuel']=(string)$w->TYPFUEL;
                    $tmp['typbody']=(string)$w->TYPBODY;
                    $information['apply'][]=$tmp;
                }
                
            }
            if(count($sm->artikul->images->image)){
                 foreach($sm->artikul->images->image as $i){
                     $information['images'][]=(string)$i;
                 }
            }
            
            //var_dump($html);
            $this->load->view('partcard', $information);
        }
        
    public function getAcessoryImage($idImg, $w=0, $h=0){
        $imgurl='https://vag-part.ru/tdimg/acessories_img/'.$idImg.'.jpg';
        $this->load->model('cachemodel', 'cache');
        $key=(md5($imgurl.$w.$h));
        $cache=$this->cache->load($key);
        if($w!=0 AND $h!=0){
            if(!empty($cache)){
                return $cache;
            }else{
                $thumb=$this->create_thumbnail($imgurl, $w, $h);
                $this->cache->save($key, $thumb, 60*60*60*24*30);
                return $thumb;
            }
            
        }else{
            return file_get_contents($imgurl);
        }
    }
    
    private function create_thumbnail($path, $width, $height) {
        error_reporting(E_ALL); 
	$info = getimagesize($path); //получаем размеры картинки и ее тип
        //var_dump($info); exit;
	$size = array($info[0], $info[1]); //закидываем размеры в массив
        //ищем соотношение сторон
        $ratio_orig =$info[0]/$info[1];

        if ($width/$height > $ratio_orig) {
           $width = $height*$ratio_orig;
        } else {
           $height = $width/$ratio_orig;
        }

        // ресэмплирование
       /* $image_p = imagecreatetruecolor($width, $height);
        $image = imagecreatefromjpeg($filename);
        imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
        */
        //В зависимости от расширения картинки вызываем соответствующую функцию
	if ($info['mime'] == 'image/png') {
		$src = imagecreatefrompng($path); //создаём новое изображение из файла
	} else if ($info['mime'] == 'image/jpeg') {
		$src = imagecreatefromjpeg($path);
	} else if ($info['mime'] == 'image/gif') {
 		$src = imagecreatefromgif($path);
	} else if ($info['mime'] == 'image/x-ms-bmp') {
 		$src = $this->imagecreatefrombmp ($path);
	} else {
		return false;
	}
        
       
        $imgs=imagecreatetruecolor($width, $height);
        imagefill ($imgs, 0, 0, imagecolorallocate ($imgs, 255,255,255));
        imagecopyresampled($imgs, $src, 0, 0, 0, 0, $width,$height,$info[0],$info[1]);
         return imagejpeg($imgs);;
    }
    private function imagecreatefrombmp($p_sFile){
        $file    =    fopen($p_sFile,"rb");
        $read    =    fread($file,10);
        while(!feof($file)&&($read<>""))
            $read    .=    fread($file,1024);
        $temp    =    unpack("H*",$read);
        $hex    =    $temp[1];
        $header    =    substr($hex,0,108);
        if (substr($header,0,4)=="424d")
        {
            $header_parts    =    str_split($header,2);
            $width            =    hexdec($header_parts[19].$header_parts[18]);
            $height            =    hexdec($header_parts[23].$header_parts[22]);
            unset($header_parts);
        }
        $x                =    0;
        $y                =    1;
        $image            =    imagecreatetruecolor($width,$height);
        $body            =    substr($hex,108);
        $body_size        =    (strlen($body)/2);
        $header_size    =    ($width*$height);
        $usePadding        =    ($body_size>($header_size*3)+4);
        for ($i=0;$i<$body_size;$i+=3)
        {
            if ($x>=$width)
            {
                if ($usePadding)
                    $i    +=    $width%4;
                $x    =    0;
                $y++;
                if ($y>$height)
                    break;
            }
            $i_pos    =    $i*2;
            $r        =    hexdec($body[$i_pos+4].$body[$i_pos+5]);
            $g        =    hexdec($body[$i_pos+2].$body[$i_pos+3]);
            $b        =    hexdec($body[$i_pos].$body[$i_pos+1]);
            $color    =    imagecolorallocate($image,$r,$g,$b);
            imagesetpixel($image,$x,$height-$y,$color);
            $x++;
        }
        unset($body);
        return $image;
    }
}