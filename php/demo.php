<?php
error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Manila');

session_start();

include 'lib/Tagbond.php';

$clientId = 'your-client-id';
$clientSecret = 'your-client-secret';
$redirectUri = 'your-redirect-uri';

$tagbond = new Tagbond;
$tagbond->setClient($clientId,$clientSecret);
$tagbond->setRedirect($redirectUri);
//$tagbond->setScopes(array('scope1','scope2'));

//checking redirect uri
if(Tagbond::currentUrl() != $redirectUri){
	echo "Incorrect redirect uri. Please put this file on the right path where your redirect uri points";
	exit;
}

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
	$userDetails = $tagbond->getData('user/profile');
	//$userDetails = $tagbond->getUser();

	if($userDetails){
		echo '<pre>';
		print_r($userDetails);
		echo '</pre>';
		exit;
	}
	else{
		exit('error:no_token');
	}
}
?>

<html>
<head><title>Tagbond SDK</title></head>
<body>
	<div>
		<input type="submit" value="Login with Tagbond" onClick="parent.location='<?php echo $url ?>'">
	</div>
</body>
</html>