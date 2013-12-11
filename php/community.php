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

$currentUrl = Tagbond::currentUrl();
$currentUrl = explode('?', $currentUrl)[0];
?>

<html>
<head><title>Tagbond SDK</title></head>
<body>
	<div>
		<div>
			<a href="<?php echo $currentUrl ?>/../demo.php">Back</a><br />
			<b>Community Login</b>: To login as your community itself. (client credentials)<br />
			<?php
			if(!$_GET['client']){
				?>
				<input type="submit" value="Community Login" onClick="parent.location='<?php echo $currentUrl ?>?client=true'">
				<?php
			}
			else{ ?>
				<div style="border-top:1px solid;">
					<b>Result Array:</b>
					<?php
					$tagbond->getClientCommunity();
					$details = $tagbond->getCommunity();

					if($details){
						echo '<pre>';
						print_r($details);
						echo '</pre>';
					}
					else{
						exit('error:no_token');
					}
					?>
				</div>
			<?php
			}
			?>
		</div>
	</div>
</body>
</html>