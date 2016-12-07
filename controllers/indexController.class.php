<?php

class indexController{

	public function __construct(){
		
	}
	
	public function getFacebook(){
		return new Facebook\Facebook([
			'app_id' => '1823296127942799',
			'app_secret' => 'bcd01c4125a8accaf02e4ce675461b33',
			'default_graph_version' => 'v2.8',
		]);
	}
	
	public function redirectHome(){
		header('Location:'. serverUrl);
	}
	
	public function indexAction($request){
		echo "index ";
		
		$fb = $this->getFacebook();
		$helper = $fb->getRedirectLoginHelper();
		$permissions = ['email','user_likes'];
		if(!isset($_SESSION['facebook_access_token'])){
			$logUrl = $helper->getLoginUrl(serverUrl.'/index/login/', $permissions);
		}else{
			$logUrl = serverUrl . '/index/logout/?accessToken=' . $_SESSION['facebook_access_token'];
		}
		$v = new view("homeView");
		$v->assign("logUrl", $logUrl);
		
		
	}
	
	public function loginAction($request){
		$fb = $this->getFacebook();
		$helper = $fb->getRedirectLoginHelper();
		try {
			 $accessToken = $helper->getAccessToken();
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
			// When Graph returns an error
			echo 'Graph returned an error: ' . $e->getMessage();
		    exit;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
		    // When validation fails or other local issues
		    echo 'Facebook SDK returned an error: ' . $e->getMessage();
		    exit;
		}

		if (isset($accessToken)) {
		    // Logged in!
		    $_SESSION['facebook_access_token'] = (string) $accessToken;
			$this->redirectHome();
		} elseif ($helper->getError()) {
		    // The user denied the request
			echo (' not logged in');
		    exit;
		}
	}
	
	public function logoutAction($request){
		session_destroy();
		$this->redirectHome();
	}
}