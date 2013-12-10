<?php
error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Manila');

include 'lib/Tagbond.php';

$clientId = 'your-client-id';
$clientSecret = 'your-client-secret';
$redirectUri = 'your-redirect-uri';

$tagbond = new Tagbond;
$tagbond->setClient($clientId,$clientSecret);
$tagbond->setRedirect($redirectUri);

$tagbond->setScopes(array('user.settings','user.contacts'));

if(!$tagbond->isLoggedIn()){
	$url = $tagbond->getLoginUrl();
	if($tagbond->getResponseType() == 'code'){
		header("Location: ".$url);
		exit;
	}
	else {
		echo $url;
	}
}
else{
	echo $userDetails = $tagbond->getUser();
	if(!$token){
		exit('error:no_token');
	}
}
?>