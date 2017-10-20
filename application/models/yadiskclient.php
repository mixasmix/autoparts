<?php
use Yandex\OAuth\OAuthClient;
use Yandex\Disk\DiskClient;
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class yaDiskClient extends CI_Model {
    private $disk;
    public function __construct(){
		parent::__construct();
		$this->load->helper('getpage');
		$this->sql=SQL::getInstance();
		$this->sql->query("SET NAMES 'utf8';");
                $this->disk=$this->getDiskClient();
    }
    
    private function getDiskClient(){
        require_once 'phar://yandex-php-library_0.4.0.phar/vendor/autoload.php';
        $token="9cacc7bc395f4290b0427ccf262ff0a0";
        $diskClient = new DiskClient($token);
        $diskClient->setServiceScheme(DiskClient::HTTPS_SCHEME);
        return $diskClient;
        /*header('Content-type: application/json');
        $result = $diskClient->diskSpaceInfo();
        $result['login'] = $diskClient->getLogin();
        $files = $diskClient->directoryContents('/autocatalog');
        echo json_encode($files);*/ 
    }
    /**
     * Метод возвращает информацию о каталоге
     */
    public function getCatalogInfo($catalog=''){
        return $this->disk->directoryContents('/'.$catalog);
    }
    /**
     * Метод создает директорию по указанному пути
     * @param string $dirname Имя/путь к дериктории
     * @return bool true в случае успеха
     */
    public function createDir($dirname){
        return $this->disk->createDirectory($dirname);
    }
    /**
     * Метод принимает имя каталога куда грузить
     * @param string $dirname Имя дериктории для загрузки
     * @param array $file массив с именем файла, временной дерикторией и размером файла в байтах
     * @return void
     */
    public function uploadFile($dirname, $file){
         return $this->disk->uploadFile($dirname, $file);
    }
    /**
     * Метод скачивает файл в указанную папку
     * @param string $filepath полный путь к файлу на я.диске, например Новая папка/file.txt
     * @param string $destination папка для загрузки
     * @param string $name имя скачанного файла
     */
    public function downloadFile($filepath, $destination, $name ){
        return $this->disk->downloadFile($filepath, $destination, $name);
    }
    /**
     * Метод копирует файл target в destination
     * @param string $target Полный путь к целефому файлу
     * @param string $destionation полный путь к новой копии файла
     * @return bool
     */
    public function copyFile($target, $destionation){
        return $this->disk->copy($target, $destination);
    }
    /**
     * метод удаляет указанный файл
     * @param string $target полный путь к удаляемому файлу
     * @return bool
     */
    public function delFile($target){
        return $this->disk->delete($target);
    }
    /**
     * Метод возвращает мавссив с превьюшкой картинки указанного размера
     * @param string $target Путь к файлу
     * @param string $size размер превью вида '700x1000'
     * @return array
     */
    public function getPreviewImage($target, $size=null){
         return $this->disk->getImagePreview($target, $size);
    }
    /**
     * Метод возвращает свойства папки или файла
     * @param string $path полный путь к файлу или папке
     * @param String $propName default null Имя возвращаемого свойства
     * @return string
     */
    public function getProperty($path, $propName=null){
         return $this->disk->getProperty($path, $propName);
    }
    /**
     * Метод устанавливает свойство папке или файлу
     * @param String $path полный путь к файлу или папке
     * @param string $propName Имя свойства
     * @param string $propValue Занчение свойства
     * @return bool
     */
    public function setProperty($path, $propName, $propValue){
        return $this->disk->setProperty($path, $propName, $propValue);
    }
}