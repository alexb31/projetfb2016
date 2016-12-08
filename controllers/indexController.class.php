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
	
	public function redirect($location = serverUrl){
		if($location == 'home'){
			$location = serverUrl;
		}else{
			$location = serverUrl . $location;
		}
		
		return header('Location:'. $location);
	}
	
	public function userPermissions($permissions = ['email', 'user_likes']){
		$fb = $this->getFacebook();
		$fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
		$permissionNotGranted = array();
		$userPermissions = array();
		
		$helper = $fb->getRedirectLoginHelper();

		$response = $fb->get("/me/permissions");
		$userNode = $response->getDecodedBody();

		foreach ($userNode['data'] as $permission) {
			$userPermissions[] = $permission['permission'];
			if($permission['status'] == 'declined') $permissionNotGranted[] = $permission['permission'];
		} 

		foreach(array_diff($permissions, $userPermissions) as $diff){
			$permissionNotGranted[] = $diff;
		}

		if(!empty($permissionNotGranted)) {
			$_SESSION['rerequest']++;
			if($_SESSION['rerequest'] > 2){
				$this->redirect('/index/notAccepted/');
			}else{
				$url = $helper->getReRequestUrl(serverUrl . '/index/loginCallback/', $permissionNotGranted);
				header('Location:' . $url);
			}
		}else{
			$this->redirect('home');
			$_SESSION['rerequest'] = 0;
		}
	}
	
	public function logUrl($permissions = ['email','user_likes']){
		$fb = $this->getFacebook();
		$helper = $fb->getRedirectLoginHelper();
		if(!isset($_SESSION['facebook_access_token'])){
			$logUrl = $helper->getLoginUrl(serverUrl . '/index/login/', $permissions);
		}else{
			if($_SESSION['rerequest'] < 2)
				$this->userPermissions($permissions);
			
			$logUrl = serverUrl . '/index/logout/';
		}
		return $logUrl;
	}
	
	public function indexAction($request){
		$logUrl = $this->logUrl();
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
			$this->redirect('home');
		} elseif ($helper->getError()) {
		    // The user denied the request
			echo (' not logged in');
		    exit;
		}
	}
	
	public function logoutAction($request){
		session_destroy();
		$this->redirect('home');
	}
	
	public function notAcceptedAction($request){
		$logUrl = $this->logUrl();
		$v = new view("notAccepted");
		$v->assign("logUrl", $logUrl);
	}
	
	public function loginCallbackAction($request){	
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
			// Now you can redirect to another page and use the
			// access token from $_SESSION['facebook_access_token']
			$this->userPermissions();
		}
	}
}