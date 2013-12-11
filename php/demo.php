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
	//testing the enronment
	$currentUrl = Tagbond::currentUrl();
	if(strpos($currentUrl, $redirectUri) === false){
		echo "Error: Incorrect redirect uri. Please put the files on the right location where your <b>Redirect Uri</b> is set.";
		echo "<br /><br />";
		echo "Hint: Your server URL is <b>'$currentUrl'</b>. The 'Redirect Uri' set in the configuration is <b>'$redirectUri'</b>.";
		exit;
	}
	?>
	<div>
		<div>
			For normal user's to login securely for your community<br />
			<?php 
			if(!$tagbond->isLoggedIn()){ ?>
				<input type="submit" value="Login with Tagbond" onClick="parent.location='<?php echo $tagbond->getLoginUrl() ?>'">
			<?php
			} 
			else { ?>
				<div>
					<?php
					$userDetails = $tagbond->getData('user/profile');
					//$userDetails = $tagbond->getUser();

					if($userDetails){
						echo '<pre>';
						print_r($userDetails);
						echo '</pre>';
					}
					else{
						exit('error:no_token');
					}
					?>
				</div>
			<?php 
			} ?> 
		</div>
		<div>
			<br />
			<br />
		</div>
		<div>
			To login as the your community itself to manage the community<br />
			<?php
			if(!$_GET['client']){
				?>
				<input type="submit" value="Community Login" onClick="parent.location='<?php echo $url ?>'">
				<?php
			}
			else{
				
				?>
				<div>
					<?php
					$clientDetails = $tagbond->getClient();

					if($clientDetails){
						echo '<pre>';
						print_r($clientDetails);
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
		<div>
			<br />
			<br />
		</div>
		<div id="implicit">
			User Login Url for Javascript based applications (insecure login) <br />
			<a href="<?php echo $tagbond->getImplicitLoginUrl() ?>"><b>Login URL</b></a>
		</div>
	</div>

	<script>
	var url = document.URL;
	var hash = url.substring(url.indexOf("#")+1);
	if(url.indexOf("#")!=-1){
		var implicit = document.getElementById('implicit');
		implicit.innerHTML = "<b>Access Token</b> recieved from <b>Implicit</b> login is <b>"+ hash +"</b>";
		alert("Access Token recieved");
	}
	</script>
</body>
</html>