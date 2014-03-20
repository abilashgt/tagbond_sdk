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

$currentUrl = Tagbond::currentUrl();
$currentUrl = explode('?', $currentUrl);
$currentUrl = $currentUrl[0];
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
					<?php
					//login
					$tagbond->getClientCommunity();
					?>
					<br />
					<b> Token: </b> <?php echo $tagbond->getAccessToken(); ?> <br>
					<br />
					<b> Community Details: </b>
					<?php
					try{
					$details = $tagbond->getCommunity();
					echo '<pre>';
					print_r($details);
					echo '</pre>';
					}
					catch(Exception $e){
						exit('error fetching community details');
					}
					?>

					<?php
					try{
						$profile = $tagbond->getUser(5);
						echo "Profile Details:<br />";
						echo '<pre>';
						print_r($profile);
						echo '</pre>';
					}
					catch(Exception $e){
						exit('error fetching profile details');
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
