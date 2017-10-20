<?php

/**
* Функция удаляет все нежелательные символы в строке и приводит строку к нормальному виду
* @param string $str Входная строка
* @param string $flag Флаг, указывающий, каким образом очищать строку. По умолчанию a - для артикулов
* @return str Возвращает очищенную строку
*/
function stringSanitize($str, $flag='a'){
   $str=trim($str);
   $str= urldecode($str);
   if($flag=='a'){
       $str = preg_replace ("/[^a-zA-ZА-Яа-я0-9\s]/","",$str);
       $str=  str_replace(' ', '', $str);
       $str=mb_strtoupper($str);
   }elseif($flag=='b'){
      if(strpos($str, '/')){
          $str=substr($str, 0, strpos($str, '/'));
          $str = preg_replace ("/[^a-zA-ZА-Яа-я0-9\s]/","",$str);
          $str=  str_replace(' ', '', $str);
          $str=mb_strtoupper($str);
      }
      if(strpos($str, ' ')){
           $str=substr($str, 0, strpos($str, ' '));
      }
   }
   return $str;
}