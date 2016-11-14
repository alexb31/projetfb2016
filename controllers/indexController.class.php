<?php

class indexController{

	public function __construct(){
	}

	public function indexAction($request){
		echo "index";
		$v = new view("homeView");
		$v->assign("pseudo", "yves");

	}

	public function homeAction($request){
		echo "home";
	}

}