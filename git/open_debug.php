<?php 

define('APP_DEBUG',true);
if(APP_DEBUG === true){
        ini_set('display_errors',-1);            //错误信息
        ini_set('display_startup_errors',-11);    //php启动错误信息
        error_reporting(E_ALL & ~E_NOTICE);                   //输出报空以外的所有信息
        ini_set('error_log', dirname(__FILE__) . '/logs/bw_error_log.txt'); //将出错信息输出到一个文本文件
    }else{
    	ini_set("display_errors","Off");
    } 
?>