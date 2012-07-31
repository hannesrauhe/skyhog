<?php
require_once("./config.inc.php");
require_once("./openid.inc.php");

class sqlite_db extends SQLite3 {
	public function __construct() {
		$this->open(UPLOAD_DIR."/scihog.db");
	}
	
	public function insertUser($openid,$name,$email) {
		$stmt = $this->prepare("INSERT INTO `users` (`name`,`openid`,`email`) VALUES (:name,:id,:mail)");
		if($stmt) {
			$stmt->bindValue(':name',$name,SQLITE3_TEXT);
			$stmt->bindValue(':id',$openid,SQLITE3_TEXT);
			$stmt->bindValue(':mail',$email,SQLITE3_TEXT);
			$stmt->execute();
			$stmt->close();
			return $this->lastInsertRowID();
		} else {
			throw new Exception("Error Processing Request", 1);			
		}		
		return 0;
	}
	public function getUserByOpenID($openid) {
		$user=array();
		$stmt = $this->prepare("SELECT * FROM `users` WHERE `openid` = :id");
		if($stmt) {
			$stmt->bindValue(':id',$openid,SQLITE3_TEXT);
			$r = $stmt->execute();
			$user = $r->fetchArray();
			$stmt->close();
		} else {
			throw new Exception("Error Processing Request", 1);			
		}
		return $user;		
	}
	public function getUserByID($user_id) {
		$user=array();
		/*$stmt = $this->conn->stmt_init();
		if($stmt->prepare("SELECT * FROM `users` WHERE `user_id` = ?")) {
			$stmt->bind_param('i',$user_id);
			$stmt->execute();
			$this->bind_array($stmt, $user);
			if(!$stmt->fetch()) {
				$stmt->close();
				return 0;
			}
			$stmt->close();
		} else {
			throw new Exception("Error Processing Request", 1);			
		}*/
		return $user;		
	}
	
	public function getUsers() {
		$elements=array();
		/*$stmt = $this->conn->stmt_init();
		if($stmt->prepare("SELECT * FROM `users`")) {
			//$stmt->bind_param('s',$openid);
			$stmt->execute();
			$elements = $this->fetch_all($stmt);//row->fetch_all(MYSQLI_ASSOC);
			$stmt->close();
		} else {
			throw new Exception("Error Processing Request", 1);			
		}*/
		return $elements;		
	}
	
	public function changeUserName($user_id,$name) {
		/*$elements=array();
		$stmt = $this->conn->stmt_init();
		if($stmt->prepare("UPDATE `users` SET name=? WHERE user_id = ?")) {
			$stmt->bind_param('si',$name,$user_id);
			$stmt->execute();
			$stmt->close();
		} else {
			throw new Exception("Error Processing Request", 1);			
		}	*/
	}
	public function activateUser($user_id) {
		$stmt = $this->prepare("UPDATE `users` SET active=1 WHERE user_id = :uid");
		if($stmt) {
			$stmt->bind_param(':uid',$user_id,SQLITE3_INTEGER);
			$stmt->execute();
			$stmt->close();
		} else {
			throw new Exception("Error Processing Request", 1);	
		}		
		
	}	
	public function deleteUser($id) {
		/*$stmt = $this->conn->stmt_init();
		if($stmt->prepare("DELETE FROM `users` WHERE `user_id` = ?")) {
			$stmt->bind_param('i',$id);
			$stmt->execute();
			$stmt->close();
		} else {
			throw new Exception("Error Processing Request", 1);			
		}*/
		return 0;
	}
}

class auth {
	var $openid = NULL;
	public function __construct() {
    	$this->openid = new LightOpenID(DOMAIN);		
	}
	public function isAuth() {
		return isset($_SESSION['user']) && $_SESSION['user']['active'];
	}
	
	public function auth(&$d,$oid) {
		if($this->isAuth()) {
			return true;
		}
		
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

$d = new sqlite_db();
$a = new auth();

if(!$a->auth($d,'https://www.google.com/accounts/o8/id')) {
	echo "you have to register with google";
	exit(0);
}

if($a->isInactiveUser()) {
	echo "your account needs to be activated by the administrator";
	exit(0);
}
