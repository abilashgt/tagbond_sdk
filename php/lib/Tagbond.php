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

	public function getLoginUrl(){
		$url = $this->base_url.'/oauth?';
		$url.= 'client_id='.$this->client_id;
		$url.= '&redirect_uri='.$this->redirect_uri;
		$url.= '&response_type='.$this->response_type;
		$url.= '&scope='.$this->scopes;

		return $url;
	}

	public function setCookie(){
		if($_GET['code']){
			//open connection
			$session = curl_init();
			if($this->proxy){
				curl_setopt($session, CURLOPT_PROXY, $this->proxy);
			}
			curl_setopt($session, CURLOPT_URL, $this->base_url.'/oauth/accesstoken');
			//curl_setopt($session,CURLOPT_HTTPHEADER,array(
			//	'Authorization'=>'Bearer '.''
			//	));
			$post = array(
				'client_id'=>$this->client_id,
				'client_secret'=>$this->client_secret,
				'redirect_uri'=>$this->redirect_uri,
				'grant_type'=>'authorization_code',
				'code'=>$_GET['code']
				);

			//post
			$postString = '';
			foreach($post as $key=>$value) { $postString .= $key.'='.$value.'&'; }
			rtrim($postString, '&');

			curl_setopt($session, CURLOPT_POST, count($post));
			curl_setopt($session, CURLOPT_POSTFIELDS, $postString);
			curl_setopt($session, CURLOPT_RETURNTRANSFER, True);

			//execute post
			$content = curl_exec($session);

			// Check if any error occured
			$response = curl_getinfo($session);
			if($response['http_code'] != 200) {
				echo "Got negative response from server, http code: ".
				$response['http_code'] . "\n";
				exit;
			}

			//close connection
			curl_close($session);

			//output
			$result = json_decode($content, true);
			//print_r($result); exit;
			if(is_array($result)){
				$token = $result['result']['access_token'];
				if($token){
					$this->access_token = $token;
					setcookie("tagbond_access_token", $token);
					return true;
				}
			}
		}

		return false;
	}

	public function getCookie(){
		$token = $_COOKIE['tagbond_access_token'];
		if($token){
			return $token;
		}

		return false;
	}

	public function getAccessToken(){
		if($this->access_token){
			$token = $this->access_token;
		}
		else{
			$token = $this->getCookie();
			$this->access_token = $token;
		}

		if($token){
			return $token;
		}

		return false;
	}

	public function isLoggedIn(){
		if($token = $this->getAccessToken()){
			return true;
		}
		else if($_GET['code']){
			if($this->setCookie()){
				return true;
			}
			else{
				//throw exception
				exit("error:unable_to_set_cookie");
			}
		}

		return false;
	}

	public function getData($url, $post = array()){
		if($token = $this->getAccessToken()){
			$post['access_token'] = $token;

			//post
			$postString = '';
			foreach($post as $key=>$value) { $postString .= $key.'='.$value.'&'; }
			rtrim($postString, '&');
			//echo $postString; exit;

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

			//output
			return $result = json_decode($content, true);
		}

		return false;
	}

	public function getUser(){
		return $this->getData('user/profile');
	}
}
?>