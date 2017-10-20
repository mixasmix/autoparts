<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Функция-обертка для работы с cURL
 * @param string $url Урл-адрес по которому идет обращение
 * @param string $post Если обращаемся постом то бьем сюда пост-данные
 * @param string $hostname Имя хоста С таким именем будет создан файл для хранения сессионных данных
 * @param int $autorize Если 1 то отправляем ранее сохраненные куки
 * @param int $timeout Таймаут в секундах
 * @param string $modyfy Для отправки запросов типа DELETE или PUT - значения соответствующие
 * @return string
 */
function getPage($url, $post='', $hostname='', $autorize=0, $timeout=5, $modify=''){
	
		$cookie='cookie.txt';
		$ya = curl_init($url);
		curl_setopt($ya, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ya, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ya, CURLOPT_COOKIE, $cookie);
		curl_setopt($ya, CURLOPT_COOKIESESSION, false);
		curl_setopt($ya, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ya, CURLOPT_TIMEOUT, $timeout); 
		/*curl_setopt($ya, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
                curl_setopt($ya, CURLOPT_PROXY, 'localhost:9150'); */
		curl_setopt($ya, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.0.1) Gecko/2008070208'); 
		curl_setopt($ya, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ya, CURLOPT_VERBOSE, 0);
		//curl_setopt($ya, CURLOPT_HEADER, 1);
		//���� post �� ������
		if(!empty($post)){
			curl_setopt($ya, CURLOPT_POST, true);
			curl_setopt($ya, CURLOPT_POSTFIELDS, $post);
		}
		if(!empty($hostname)){
                        
			curl_setopt($ya, CURLOPT_COOKIEFILE, $_SERVER['DOCUMENT_ROOT'].'/cookie/'.$hostname.'_cookie.txt');
		}
               
                if(!empty($modify)){
                    
                    curl_setopt($ya, CURLOPT_CUSTOMREQUEST, $modify);
                }
		/*curl_setopt($ya, CURLOPT_COOKIE, '__utma=54593257.1580367279.1415991136.1420483114.1421165997.6; __utmz=54593257.1415991136.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); sparex_login=mixasmix; __utmb=54593257.17.10.1421165997; sparex_sid=7b1n3rhtapgpujnbi2vqidh407; jv_enter_ts_124104=1421165997798; jv_gui_state_124104=WIDGET; PHPSESSID=7b1n3rhtapgpujnbi2vqidh407; dispaly_mode=0; __utmc=54593257; __utmt=1');
		*/
		//��������� ���������� COOKIE � ����
		if($autorize=1){
			curl_setopt($ya, CURLOPT_COOKIEJAR, $_SERVER['DOCUMENT_ROOT'].'/cookie/'.$hostname.'_cookie.txt');
		}
		return curl_exec($ya);
}
function ping($host,$port=80,$timeout=6)
{       
        
        $fsock = fsockopen($host, $port, $errno, $errstr, $timeout);
        if ( ! $fsock )
        {
                return FALSE;
        }
        else
        {
                return TRUE;
        }
}
?>