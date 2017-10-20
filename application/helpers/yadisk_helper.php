<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class YandexDisk
{
  private $appId;
  private $appSecret;
  private $login;
  private $password;
  private $token;
  private $tokenCreateTime;
  private $ttl;
  
  public $error = 'No error';
  
  public function __construct($conf)
  {
    $this->appId = $conf['app_id'];
    $this->appSecret = $conf['app_secret'];
    $this->login = $conf['login'];
    $this->password = $conf['password'];
    
    $this->getToken();
  }
  
  public function ls($remotePath, $onlyType = FALSE)
  {
    $ch = $this->getCurl($remotePath);
    
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PROPFIND');
    
    $header = array(
        'Accept: */*',
        "Authorization: OAuth {$this->token}",
        'Depth: 1',
    );
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    
    $result = curl_exec($ch);
    
    if($this->getResult($ch, array(207))) {
      $xml = simplexml_load_string($result, 'SimpleXMLElement', 0, 'd', true);
      $list = array();
      
      if ($onlyType === 'd') { // Only dirs
        foreach ($xml as $item) {
          if (isset($item->propstat->prop->resourcetype->collection)) {
            $list[] = strval($item->href);
          }
        }
      } else if ($onlyType === 'f') { // Only files
        foreach ($xml as $item) {
          if (!isset($item->propstat->prop->resourcetype->collection)) {
            $list[] = strval($item->href);
          }
        }
      } else {
        foreach ($xml as $item) { // All items with type
          if (isset($item->propstat->prop->resourcetype->collection)) {
            $type = 'd';
          } else {
            $type = 'f';
          }
          
          $list[] = array(
            'href' => strval($item->href),
            'type' => $type
          );
        }
      }
      
      return $list;
    }
    
    return false;
  }
 
  public function rm($remotePath)
  {		
    $ch = $this->getCurl($remotePath);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_exec($ch);
    
    return $this->getResult($ch, array(200));
  }
  
  public function mkdir($remotePath)
  {
    $ch = $this->getCurl($remotePath);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "MKCOL");
    curl_exec($ch);
    return $this->getResult($ch, array(201, 405));
  }
  
  public function download($remotePath, $localPath)
  {
    $file = fopen($localPath, 'w');
    
    $ch = $this->getCurl($remotePath);
    curl_setopt($ch, CURLOPT_HTTPGET, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FILE, $file);
    curl_exec($ch);
    
    fclose($file);
    
    return $this->getResult($ch, array(200));
  }
  
  public function upload($remotePath, $localPath)
  {
    $file = fopen($localPath, 'r');
    
    $ch = $this->getCurl($remotePath);
    curl_setopt($ch, CURLOPT_PUT, 1);
    curl_setopt($ch, CURLOPT_INFILE, $file);
    curl_setopt($ch, CURLOPT_INFILESIZE, filesize($file));
    
    $header =array(
      'Accept: */*',
      "Authorization: OAuth {$this->token}",
      'Expect: ',
    );
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_exec($ch);
    
    fclose($file);
    return $this->getResult($ch, array(200,201,204));
  }
  private function getCurl($remotePath)
  {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_PORT, '443');
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_URL, 'https://webdav.yandex.ru' . $remotePath); # TODO: особое внимание при отладке                                                                              
    $header = array(
      'Accept: */*',
      "Authorization: OAuth {$this->token}",
    );
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    return $ch;
  }
  private function getResult($ch, $codes)
  {
    if (curl_errno($ch)) {
      $this->error = curl_error($ch);
      return false;
    } else  {
      $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      if (!in_array($httpCode, $codes)) {
        $this->error = 'Response http error:' . $httpCode;
        return false;
      } else {
        return true;
      }
    }
  }
  private function getToken()
  {
    $tokenIsOutdated = (time() > ($this->ttl + $this->tokenCreateTime));
    
    if ($this->ttl > 0 && !$tokenIsOutdated)
      return $this->token;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_URL, 'https://oauth.yandex.ru/token');
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 4);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $post = 'grant_type=password&client_id=' . $this->appId
      . '&client_secret=' . $this->appSecret
      . '&username=' . $this->login
      . '&password=' . $this->password;
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_POST, 1);
    $header = array(
      'Content-type: application/x-www-form-urlencoded',
      'Content-Length: ' . strlen($post),
    );
    curl_setopt($ch, CURLOPT_HEADER, $header);
    $curlResponse = curl_exec($ch);
    if (!$curlResponse || curl_errno($ch)) {
      $this->error = curl_error($ch);
      curl_close($ch);
      return false;
    }
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if (($httpCode !== 200) && ($httpCode !== 400)) {
      $this->error = "Request Status is " . $httpCode;
      curl_close($ch);
      return false;
    }
    $curlResponseBody = $this->getResponseBody($ch, $curlResponse);
    $result = json_decode($curlResponseBody, true);
    curl_close($ch);
    if (isset($result['error']) && ($result['error'] != '')) {
      $this->error = $result['error'];
      return false;
    }
    $this->token = $result['access_token'];
    $this->ttl = intval($result['expires_in']);
    $this->tokenCreateTime = intval(time());
    return $this->token;
  }
  private function getResponseBody($ch, $curlResponse)
  {
    return substr($curlResponse, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
  }
}