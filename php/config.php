<?php
error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Manila');

//setting the configurations
$tagbond->setClient('your-client-id','your-client-secret');
$tagbond->setRedirect('your-redirect-uri');
//$tagbond->setScopes(array('scope1','scope2'));
//$tagbond->setProxy('server-ip:port');
//$tagbond->setBaseUrl('url-override');
?>