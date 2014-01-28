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
?>

<html>
<head><title>Tagbond SDK</title></head>
<body>
	<?php
	//testing the enronment
	$currentUrl = Tagbond::currentUrl();
	if(!$tagbond->checkRedirect()){
		echo "Error: Incorrect redirect uri. Please put the files on the right location where your <b>Redirect Uri</b> is set.";
		echo "<br /><br />";
		echo "Hint: Your server URL is <b>'$currentUrl'</b>. The 'Redirect Uri' set in the configuration is <b>'$redirectUri'</b>.";
		exit;
	}
	?>
	<div>
		<div style="border:1px solid;">
			<b>User Login (secure) </b>: User Login (authorization code)<br />
			<?php 
			if(!$tagbond->isLoggedIn()){ ?>
				<input type="submit" value="Login with Tagbond" onClick="parent.location='<?php echo $tagbond->getLoginUrl() ?>'">
			<?php
			} 
			else { ?>
				<div style="border-top:1px solid;">
					<b>Result Array:</b>
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
		<div style="border:1px solid;">
			<b>User Login (Insecure)</b>: User Login for Javascript based applications (implicit)
			<div id="implicit" style="border-top:1px solid;">
				 <br />
				<a href="<?php echo $tagbond->getImplicitLoginUrl() ?>"><b>Login URL</b></a>
			</div>
		</div>
		<div>
			<br />
			<br />
		</div>
		<div style="border:1px solid;">
			To login as the your community itself to manage the community<br />
			<input type="submit" value="Community Login" onClick="parent.location='<?php echo $currentUrl ?>/../community.php?client=true'">
		</div>
	</div>

	<script>
	var url = document.URL;
	var hash = url.substring(url.indexOf("#")+1);
	if(url.indexOf("#")!=-1){
		var implicit = document.getElementById('implicit');
		implicit.innerHTML = "<b>Access Token</b>: "+ hash +"</b>";
		alert("Access Token recieved");
	}
	</script>
</body>
</html>