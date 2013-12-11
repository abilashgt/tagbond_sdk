<?php
session_start();

//creating tagbond sdk object
include 'lib/Tagbond.php';
$tagbond = new Tagbond;

//fetching the configurations
if(file_exists(dirname(__FILE__).'/config-testing.php')){
	$config=dirname(__FILE__).'/config-testing.php';
}
else{
	$config=dirname(__FILE__).'/config.php';
}
require $config;

//setting the configurations
$tagbond->setClient($clientId,$clientSecret);
$tagbond->setRedirect($redirectUri);
$tagbond->setScopes($scopes);
?>

<html>
<head><title>Tagbond SDK</title></head>
<body>
	<?php
	//checking redirect uri
	$currentUrl = Tagbond::currentUrl();
	if(strpos($currentUrl, $redirectUri) === false){
		echo "Incorrect redirect uri. Please put this file on the right path where your redirect uri points";
		exit;
	}

	if(!$tagbond->isLoggedIn()){
		$url = $tagbond->getLoginUrl();
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
	<div>
		For reqular user's to login and use Tagbond<br />
		<input type="submit" value="Login with Tagbond" onClick="parent.location='<?php echo $url ?>'">
	</div>
</body>
</html>