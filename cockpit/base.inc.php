<?php
/*
Copyright 2012 Hannes Rauhe

This file is part of Skyhog.

Skyhog is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.
	
Skyhog is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Skyhog.  If not, see <http://www.gnu.org/licenses/>.
*/
require_once("./config.inc.php");
require_once("./openid.inc.php");

class sqlite_db extends SQLite3 {
	public function __construct() {
		$this->open(UPLOAD_DIR.DB_NAME);
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
	
	public function getUsers() {		
		$users=array();
		$stmt = $this->prepare("SELECT * FROM `users`;");
		if($stmt) {
			$r = $stmt->execute();
			while($res = $r->fetchArray(SQLITE3_ASSOC)) {
				$users[]=$res;
			}
			$stmt->close();
		} else {
			throw new Exception("Error Processing Request", 1);			
		}
		return $users;	
	}

	public function activateUser($user_id) {
		$stmt = $this->prepare("UPDATE `users` SET active=1 WHERE user_id = :uid");
		if($stmt) {
			$stmt->bindValue(':uid',$user_id,SQLITE3_INTEGER);
			$stmt->execute();
			$stmt->close();
		} else {
			throw new Exception("Error Processing Request", 1);	
		}		
		
	}	
	public function deleteUser($user_id) {		
		$stmt = $this->prepare("DELETE FROM `users` WHERE user_id = :uid");
		if($stmt) {
			$stmt->bindValue(':uid',$user_id,SQLITE3_INTEGER);
			$stmt->execute();
			$stmt->close();
		} else {
			throw new Exception("Error Processing Request", 1);	
		}	
		return 0;
	}
	
	/** nav table **/
	
	public function getNavEntries() {		
		$nav=array();
		$stmt = $this->prepare("SELECT * FROM `nav` WHERE menu_order>=0 ORDER BY menu_order;");
		if($stmt) {
			$r = $stmt->execute();
			while($res = $r->fetchArray(SQLITE3_ASSOC)) {
				$nav[]=$res;
			}
			$stmt->close();
		} else {
			throw new Exception("Error Processing Request", 1);			
		}
		return $nav;	
	}
	
	public function insertNavEntry($link,$id,$name,$menu_order) {
		$nav=array();
		$stmt = $this->prepare("INSERT INTO nav (link,id,name,menu_order) VALUES (:link,:id,:name,:menu_order);");
		if($stmt) {
			$stmt->bindValue(':link',$link,SQLITE3_TEXT);
			$stmt->bindValue(':id',$id,SQLITE3_TEXT);
			$stmt->bindValue(':name',$name,SQLITE3_TEXT);
			$stmt->bindValue(':menu_order',$menu_order,SQLITE3_INTEGER);
			$stmt->execute();
			$stmt->close();
		} else {
			throw new Exception("Error while inserting new nav entry", 1);			
		}
		
	}
			
	public function orderNavEntries($nav_order) {	
		$nav=array();
		$stmt = $this->prepare("UPDATE `nav` SET menu_order=-1;");
		if($stmt) {
			$stmt->execute();
			$stmt->close();
		} else {
			throw new Exception("Error while reseting menu_order", 1);			
		}
		
		if(!empty($nav_order)) {
			$i = 0;
			$stmt = $this->prepare("UPDATE `nav` SET menu_order=:i WHERE id=:ent");
			foreach($nav_order as $ent) {				
				if($stmt) {
					$stmt->bindValue(':i',$i,SQLITE3_INTEGER);
					$stmt->bindValue(':ent',trim($ent),SQLITE3_TEXT); //have to trim because of strange spaces after JSON-parsing
					$stmt->execute();
					if($this->changes()==0) {
						$this->insertNavEntry($ent, $ent, $ent, $i);
					}
					$stmt->reset();
				} else {
					throw new Exception("Error while setting menu_order", 1);			
				}
				$i++;
			}
			$stmt->close();
		}
	}
}

class auth {
	var $openid = NULL;
	public function __construct() {
		if(DOMAIN!=="NONE") {
    		$this->openid = new LightOpenID(DOMAIN);	
		} else {
			$_SESSION['user']=array("name"=>"unauthorized", "user_id" => -1, "active" => 1, "admin" => 0);
		}		
	}
	public function isAuth() {
		return isset($_SESSION['user']) && $_SESSION['user']['active'];
	}
	
	public function auth(&$d,$oid) {
		if($this->isAuth()) {
			return true;
		}
		
    	if(!$this->openid->mode) {
    		if(empty($oid)) {
    			return false;
    		}
    		$this->openid->identity = $oid;
			$this->openid->required = array('namePerson/friendly', 'contact/email','namePerson/first');
	        header('Location: ' . $this->openid->authUrl());
			exit(0);
		} elseif($this->openid->mode!= 'cancel') {
			if($this->openid->validate()) {
				$oid = $this->openid->identity;
				setcookie('oid',$oid);
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

class git {
	static public function add($file) {
		$gitarg1 = escapeshellarg($file);
		$retvar = 0;
		$ret = system(GIT_CMD." add $gitarg1",$retvar);
		if($ret === FALSE || $retvar!=0) {
		    echo "ERROR: adding $file with git wasn't possible\n";
			echo $ret;
		    exit();
		}
	}
	
	static public function commit($author,$msg) {		
		$gitarg1 = escapeshellarg($author);
		$gitarg2 = escapeshellarg($msg.", IP:".$_SERVER["REMOTE_ADDR"]);
		$retvar = 0;
		$ret = system(GIT_CMD." commit --author $gitarg1 -m $gitarg2",$retvar);
		if($ret === FALSE || $retvar!=0) {
		    echo "ERROR: commiting staged files with git wasn't possible, commit-msg was $gitarg2\n";
			echo $ret;
		    exit();
		}
	}
}

session_start();

$d = new sqlite_db();
$a = new auth();
$oid = '';

if(array_key_exists("oid", $_COOKIE) && !empty($_COOKIE['oid'])) {
	$oid = $_COOKIE['oid'];
}

if(array_key_exists("openid_identifier", $_POST) && !empty($_POST["openid_identifier"])) {
	$oid = $_POST['openid_identifier'];
}

if(!$a->auth($d,$oid)) {
	$msg = "Please provide a valid OpenID";	
	Header("Location: index.php?msg=".urlencode($msg)."&redirect=".urlencode($_SERVER['SCRIPT_NAME']));
	exit(0);
}

if($a->isInactiveUser()) {
	$msg = "your account needs to be activated by the administrator";
	Header("Location: index.php?msg=".urlencode($msg));
	exit(0);
}

$_SESSION['KCFINDER'] = array();
$_SESSION['KCFINDER']['disabled'] = false;

$msg = '';
