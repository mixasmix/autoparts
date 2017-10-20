<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
function procents($price, $procent=''){
    $std_procent=1.15;
    $price=(float)$price;
    $procent=(int)$procent;

    if($price>5000){
        $std_procent=1.13;
        if(!empty($procent) AND $procent>13){
            $procent=1.13;
        }
    }
    $procent=(empty($procent))?$std_procent:$procent;
    return $price*$procent;
}
?>