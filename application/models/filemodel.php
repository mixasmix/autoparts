<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
	Тут модель будет работать с файлами
*/
class Filemodel  extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->load->helper('file');
	}
	/*метод проверит, существует ли файл*/
	public function getFile($path, $uri='', $type='img'){
		$file_info=$this->infoPath($path);
		$file_info['hash']=md5($path);
		/*var_dump($file_info); exit;*/
		$file_info['server_path']='/images/catalog/';
		
		//если расширение png, gif, jpg, bmp
		if($file_info['extension']=='png' or $file_info['extension']=='gif'  or $file_info['extension']=='jpg' or $file_info['extension']=='bmp'){
			//то путь на сервере равен 
			
			
		}elseif(empty($file_info['extension']) and $type=='img'){
			
			$file_info['hash']=md5($uri);
		} elseif(strlen($file_info['extension'])>3){
			$file_info['extension']=substr($file_info['extension'], 0, 3);
			
		}
		//путь до файла будет
		if($file_info['dirname']=='/images'){
			$filepath='/images/'.$file_info['basename'];
		} else{
			$filepath=$file_info['server_path'].$file_info['hash'].'.'.$file_info['extension'];
		}
		//если файл есть
		$file=get_file_info('./'.$filepath);
		/*if($file['size']==0){
			var_dump($file_info); 
		}*/
		if($file['size']!=0){
			/*var_dump(get_file_info('./'.$filepath0)); exit;*/
			return $filepath;
		} else{
			/*var_dump($filepath); exit;*/
			write_file('./'.$filepath, @file_get_contents($path));
			//file_put_contents($filepath, $path);
			return $filepath;
		}
		
	}
	public function infoPath($path){
		 return pathinfo($path);
	}
} 

?>
