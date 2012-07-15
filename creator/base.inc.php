<?php
require_once("./config.inc.php");
require_once("./openid.inc.php");

class auth {
	var $openid = NULL;
	public function __construct() {
    	$this->openid = new LightOpenID(DOMAIN);		
	}
	public function isAuth() {
		return isset($_SESSION['user']) && $_SESSION['user']['active'];
	}
	
	public function auth(&$d,$oid) {		
    	if(!$this->openid->mode) {
    		$this->openid->identity = $oid;
			$this->openid->required = array('namePerson/friendly', 'contact/email','namePerson/first');
			setcookie('oid',$oid);
	        header('Location: ' . $this->openid->authUrl());
		} elseif($this->openid->mode!= 'cancel') {
			if($this->openid->validate()) {
				$oid = $this->openid->identity;
				$user=$d->getUserByOpenID($oid);
				if(!empty($user)) {
					$_SESSION['user']=$user;
				} else {
					$attr = $this->openid->getAttributes();
					$name = empty($attr['namePerson/friendly']) ? $attr['namePerson/first'] : $attr['namePerson/friendly'];
					$d->insertUser($oid,$name,$attr['contact/email']);
					$_SESSION['user']=$d->getUserByOpenID($oid);
				}
				return true;
			}
		}
		setcookie('oid');
		unset($_COOKIE['oid']);
		return false;
	}
	
	public function isAdmin() {
		if(!$this->isAuth())
			return false;
		return $_SESSION['user']['admin'];
	}	
	
	public function setAuthUser($user_id) {
		$_SESSION['user']['user_id']=$user_id;
	}
	
	public function activateUser(&$d) {
		$_SESSION['user']['active'] = 1;
		$d->activateUser($_SESSION['user']['user_id']);	
	}
	
	public function isInactiveUser() {
		if(isset($_SESSION['user'])) {
			return !$_SESSION['user']['active'];
		} else {
			return false;
		}		
	}
	public function getAuthUser() {
		if(!isset($_SESSION['user']) || !isset($_SESSION['user']['user_id']))
			return false;
		return $_SESSION['user']['user_id'];
	}	
	public function getAuthUserName() {
		if(!$this->isAuth())
			return false;
		return $_SESSION['user']['name'];
	}
	public function setUserName($name) {
		if(!$this->isAuth())
			return false;
		$_SESSION['user']['name']=$name;
	}
	public function getAuthCookie() {
		if(isset($_COOKIE['oid'])) {
			return $_COOKIE['oid'];			
		} else {
			return false;			
		}
	}
	public function logout($destroy_cookie=true) {			
		if($destroy_cookie) {
			setcookie('oid');
			unset($_COOKIE['oid']);
		}
		session_destroy();
	}
}

session_start();