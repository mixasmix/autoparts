<?php
class Cachemodel extends CI_Model{
    private $_cacheFolder;
    private $memcached;
    public function __construct(){
        $this->_cacheFolder = $_SERVER['DOCUMENT_ROOT'].'/application/cache/';
        $this->memcached = new Memcache();
        //Соединяемся с нашим сервером
        //if(!$this->memcached->connect('188.120.232.181', 11211)){
        if(!$this->memcached->connect('localhost', 11211)){
            return false;
        }
    }

    /**
    * чтение
    * 
    * @param mixed $key
    */
    public function load($key){
        $var= $this->memcached->get(md5($key));
        if(!empty($var)){
            return unserialize($var);
        }else{
            return false;
        }
    }

    /**
    * добавление
    * 
    * @param mixed $key имя файла
    * @param mixed $data
    * @param mixed $time
    */
    public function save($key, $data, $time){
        if($this->memcached->set(md5($key), serialize($data), MEMCACHE_COMPRESSED, $time)){
            return true;
        }else{
            return false;
        }
    }

    /**
    * удаление 
    * 
    * @param mixed $key
    */
    public function remove($key){
        $this->memcached->delete(md5($key));
        /*$file = $this->_cacheFolder . md5($key);
        if(file_exists($file)){
            unlink($file);
        }*/
    }
}