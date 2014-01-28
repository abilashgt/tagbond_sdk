<?php
class Tagbond
{
	private $base_url;
	private $client_id;
	private $client_secret;
	private $redirect_uri;
	private $scopes;
	private $response_type;

	private $access_token;

	private $proxy;
	private $sessionName = 'tagbond_access_token';

	function __construct() {
		$this->base_url = 'https://api.tagbond.com';
		$this->response_type = 'code';
	}

	function __destruct() {
		//destructor
	}

	public function setProxy($proxy){
		$this->proxy = $proxy;
	}

	public function setBaseUrl($url){
		$this->base_url = $url;
	}

	public function setClient($id, $secret){
		$this->client_id = $id;
		$this->client_secret = $secret;
		return true;
	}

	public function setRedirect($uri){
		$this->redirect_uri = $uri;
		return true;
	}

	public function checkRedirect(){
		$currentUrl = self::currentUrl();
		if(strpos($currentUrl, $this->redirect_uri) === false){
			return false;
		}

		return true;
	}

	public function setResponseType($type){
		$this->response_type = $type;
		return true;
	}

	public function getResponseType($type){
		return $this->response_type;
	}

	public function setScopes($scopes){
		if(is_array($scopes)){
			$this->scopes = implode(' ', $scopes);
			return true;
		}
		return false;
	}

	public function setSession($token){
		if($token){
			$this->access_token = $token;
			$_SESSION[$this->sessionName] = $token;
			return true;
		}

		return false;
	}

	public function getSession(){
		$token = $_SESSION[$this->sessionName];
		if($token){
			$this->access_token = $token;
			return $token;
		}

		return false;
	}

	public function getAccessToken(){
		if($token = $this->access_token){
			return $token;
		}
		else if($token = $this->getSession()){
			return $token;
		}
		else if($_GET['code']){
			$post = array(
				'client_id'=>$this->client_id,
				'client_secret'=>$this->client_secret,
				'redirect_uri'=>$this->redirect_uri,
				'grant_type'=>'authorization_code',
				'code'=>$_GET['code']
				);

			$result = $this->postCurl('/oauth/accesstoken', $post);

			if(is_array($result)){
				$token = $result['result']['access_token'];

				if($this->setSession($token)){
					return true;
				}
				else{
					//throw exception
					exit("error:unable_to_set_cookie");
				}
			}
		}

		if($token){
			return $token;
		}

		return false;
	}

	public function postCurl($url, $post = array()){
		//post
		$postString = '';
		foreach($post as $key=>$value) { $postString .= $key.'='.$value.'&'; }
		rtrim($postString, '&');

		//curl open connection
		$session = curl_init();
		//curl proxy
		if($this->proxy){
			curl_setopt($session, CURLOPT_PROXY, $this->proxy);
		}
		//curl url
		curl_setopt($session, CURLOPT_URL, $this->base_url.'/'.$url);
		//curl post
		curl_setopt($session, CURLOPT_POST, count($post));
		curl_setopt($session, CURLOPT_POSTFIELDS, $postString);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, True);

		//curl_setopt($session,CURLOPT_HTTPHEADER,array(
		//	'Authorization'=>'Bearer '.''
		//	));

		//curl execute
		$content = curl_exec($session);

		// Check if any error occured
		$response = curl_getinfo($session);
		if($response['http_code'] != 200) {
			//throw exeption
			echo "Got negative response from server, http code: ".
			$response['http_code'] . "\n";
			exit;
		}

		//curl close connection
		curl_close($session);

		echo $result;
		//output
		return $result = json_decode($content, true);
	}


	public function isLoggedIn(){
		if($token = $this->getAccessToken()){
			return true;
		}

		return false;
	}

	public function getLoginUrl(){
		$url = $this->base_url.'/oauth?';
		$url.= 'client_id='.$this->client_id;
		$url.= '&redirect_uri='.$this->redirect_uri;
		$url.= '&response_type='.$this->response_type;
		$url.= '&scope='.$this->scopes;

		return $url;
	}

	public function getImplicitLoginUrl(){
		$url = $this->base_url.'/oauth?';
		$url.= 'client_id='.$this->client_id;
		$url.= '&redirect_uri='.$this->redirect_uri;
		$url.= '&response_type=token';
		$url.= '&scope='.$this->scopes;

		return $url;
	}

	public function getClientCommunity(){
		$post = array(
			'client_id'=>$this->client_id,
			'client_secret'=>$this->client_secret,
			'grant_type'=>'client_credentials',
			'redirect_uri'=>$this->redirect_uri,
			);
		$result = $this->postCurl('oauth/accesstoken', $post);
		$this->setSession($result['result']['access_token']);
		return $result;
	}

	public function getData($url, $post = array()){
		if($token = $this->getAccessToken()){
			$post['access_token'] = $token;
			return $this->postCurl($url, $post);
		}
	}

	public function getUser(){
		return $this->getData('user/profile');
	}

	public function getPerspective(){
		return $this->getData('perspective');
	}

	public function getCommunity(){
		return $this->getData('community/details');
	}

	public static function currentUrl() {
		$pageURL = 'http';
		if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}

		$pageURL .= "://";

		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} 
		else {
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}

		return $pageURL;
	}

}
?>