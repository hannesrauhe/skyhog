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
//if(defined(ENABLE_DEBUG)) {
if (!ini_get('display_errors')) {
    ini_set('display_errors', '1');
}
//}

/* check if base.inc.php has been included after session has been started (e.g. in setup.php)*/
if (!isset($_SESSION)) { session_start(); }
/*PHP 5.4:
if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}
 */

header('Content-type: text/html; charset=utf-8');

require_once("./config.inc.php");
require_once("./openid.inc.php");

class skylog {
    var $logfile = FALSE;
    var $loguser;
    
    public function __construct($file,$username) {
        if(defined("ENABLE_LOG")) {
            $this->logfile = fopen(LOG_DIR."/$file","a");
            if($this->logfile===FALSE) {
                echo "FATAL: unable to open log file '".$file."'\n";
                exit(1);
            }
            $this->loguser = $username;
        }
    }
    public function info($msg) {
        $this->write($msg,"INFO"); 
    }
    public function write($msg,$level="INFO") {
        $level = strtoupper($level);
        if(defined("ENABLE_LOG") && strtoupper(ENABLE_LOG)==$level) {
            if($this->logfile===FALSE) {
                echo "FATAL: unable to open log file '".$this->logfile."'\n";
                exit(1);
            } else
                fwrite($this->logfile,date("Y-m-d H:i:s (").$this->loguser.",$level): $msg\n");            
        }
    }
}

class skyhog_db extends SQLite3 {
	public function __construct() {
		$this->open(DB_NAME);
	}
	
	public function insertUser($openid,$name,$email) {
		if(empty($name)) {
			$name="unknown";
		}
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
	public function getUserByID($id) {
		$user=array();
		$stmt = $this->prepare("SELECT * FROM `users` WHERE `user_id` = :id");
		if($stmt) {
			$stmt->bindValue(':id',$id,SQLITE3_INTEGER);
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
	
	/*sites table*/
	
	
    public function getSiteByID($id) {
        $site=array();
        $stmt = $this->prepare("SELECT * FROM `sites` WHERE `id` = :id");
        if($stmt) {
            $stmt->bindValue(':id',$id,SQLITE3_INTEGER);
            $r = $stmt->execute();
            $site = $r->fetchArray();
            $stmt->close();
        } else {
            throw new Exception("Error Processing Request", 1);         
        }
        return $site;       
    }
    
    public function getSites() {
        $sites=array();
        $stmt = $this->prepare("SELECT * FROM `sites`;");
        if($stmt) {
            $r = $stmt->execute();
            while($res = $r->fetchArray(SQLITE3_ASSOC)) {
                $sites[]=$res;
            }
            $stmt->close();
        } else {
            throw new Exception("Error Processing Request", 1);         
        }
        return $sites;  
    }
    
    public function insertSite($site) {
        $cols = array("name","page_url","page_dir","preview_url","preview_dir","git");
        
        $stmt = $this->prepare("INSERT INTO `sites` (`".join("`,`",$cols)."`) VALUES (:".join(",:",$cols).")");
        if($stmt) {
            foreach($cols as $col) {
                $stmt->bindValue(':'.$col,$site[$col],SQLITE3_TEXT);
            }
            $stmt->execute();
            $stmt->close();
            return $this->lastInsertRowID();
        } else {
            throw new Exception("Error Processing Request", 1);         
        }       
        return 0;
    }    
    
    public function updateSite($site) {    
        $cols = array("name","page_url","page_dir","preview_url","preview_dir","git"); 
        $query_txt = "UPDATE `sites` SET ";
        foreach($cols as $c) {
            $query_txt .= "$c = :$c,";
        }
        $query_txt = substr($query_txt, 0, -1);
        $query_txt .= " WHERE id=:id";
        //echo $query_txt;
        $stmt = $this->prepare($query_txt);
        if($stmt) {
            foreach($cols as $col) {
                $stmt->bindValue(':'.$col,$site[$col],SQLITE3_TEXT);
            }
            $stmt->bindValue(':id',$site["id"],SQLITE3_INTEGER);
            $stmt->execute();
            $stmt->close();
            return True;
        } else {
            throw new Exception("Error Processing Request", 1);         
        }       
        return 0;
    }
    
    public function deleteSite($site_id) {      
        $stmt = $this->prepare("DELETE FROM `sites` WHERE id = :sid");
        if($stmt) {
            $stmt->bindValue(':sid',$site_id,SQLITE3_INTEGER);
            $stmt->execute();
            $stmt->close();
        } else {
            throw new Exception("Error Processing Request", 1); 
        }   
        return 0;
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
		
		//try { //exception handling outside
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
						file_put_contents(LOG_DIR."/last_login_user".$user['user_id'].".log", date("Y-m-d H:i:s"));
					} else {
						$attr = $this->openid->getAttributes();
						$name = empty($attr['namePerson/friendly']) ? $attr['namePerson/first'] : $attr['namePerson/friendly'];
						$d->insertUser($oid,$name,$attr['contact/email']);
						$_SESSION['user']=$d->getUserByOpenID($oid);
					}
					return true;
				}
			}
		//} catch(ErrorException $e) {
    	//	echo 'Caught exception: ',  $e->getMessage(), "\n";			
		//}
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
    public function getAuthUserMail() {
		if(!$this->isAuth())
			return false;
		return $_SESSION['user']['email'];
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
	    if(GIT_CMD=="disable") {
	        return;
	    }
		$arr = array();
		$gitarg1 = escapeshellarg($file);
		$retvar = 0;
		$ret = exec(GIT_CMD." add $gitarg1 2>&1",$arr,$retvar);
		if($ret === FALSE || $retvar!=0) {
		    echo "ERROR: adding $file with git wasn't possible\n";
			echo $ret;
		    exit();
		}
		return $arr;
	}
	
	static public function commit($author,$msg) {	
        if(GIT_CMD=="disable") {
            return;
        }	
		$gitarg1 = escapeshellarg($author);
		$gitarg2 = escapeshellarg($msg.", IP:".$_SERVER["REMOTE_ADDR"]);
		$retvar = 0;
		$ret = exec(GIT_CMD." commit -a --author $gitarg1 -m $gitarg2 2>&1",$arr,$retvar);
		if($ret === FALSE || $retvar!=0) {
		    echo "ERROR: commiting staged files with git wasn't possible, commit-msg was $gitarg2\n";
			echo $ret;
		    exit();
		}
		return $arr;
	}
	
	static public function diff() {		
        if(GIT_CMD=="disable") {
            return;
        }
		$retvar = 0;
		$ret = exec(GIT_CMD." diff 2>&1",$arr,$retvar);
		if($ret === FALSE || $retvar!=0) {
		    echo "ERROR: diff caused an error\n";
			echo $ret;
		    exit();
		}
		return $arr;
	}
}

class site {
    var $site_id = -1;
    public function __construct(&$d,$s_id = -1) {
        $this->site_id=$s_id;
        if(!isset($_SESSION['site']['id'])) {
            if(isset($_COOKIE['site_id']) && $this->site_id==-1) {
                $this->site_id = $_COOKIE['site_id'];
            }
            $this->init($d,$this->site_id);
            setcookie('site_id',$this->site_id);
        } else {        
            if($this->site_id!=-1 && $_SESSION['site']['id']!=$this->site_id) {
                $this->init($d,$this->site_id);
                setcookie('site_id',$this->site_id);
            } else {
                $this->site_id = $_SESSION['site']['id'];
            }
        }
    }
    
    public function init(&$d,$site_id) {    
        $this->site_id=$site_id;
        if($this->site_id!=-1) {
            $site = $d->getSiteByID($this->site_id);
            if(empty($site)) {
                $this->site_id = -1;
                return;
            }
                
            $_SESSION['site'] = $site;
        /*
            $_SESSION['site'] = array();
            $_SESSION['site']['id'] = $this->site_id;
            $_SESSION['site']['name']="Notapaper";
            $_SESSION['site']['page_url']="http://localhost/notapaper";
            $_SESSION['site']['page_dir']="/var/www/notapaper/";
            $_SESSION['site']['preview_url']="http://localhost/p_notapaper";
            $_SESSION['site']['preview_dir']="/var/www/p_notapaper/";
            $_SESSION['site']['git']="https://github.com/hannesrauhe/notapaper.git";
          */
         }
    }
    
    public function getSiteID() {
        return $this->site_id;
    }
    
    public function getSiteName() {
        if($this->site_id!=-1) {
            return $_SESSION['site']['name'];
        }
        return false;
    }
    
    public function getPreviewDir() {
        if($this->site_id!=-1) {
            return $_SESSION['site']['preview_dir'];
        }
        return false;
    }
    public function getPreviewURL() {
        if($this->site_id!=-1) {
            return $_SESSION['site']['preview_url'];
        }
        return false;
    }
    public function getPageURL() {
        if($this->site_id!=-1) {
            return $_SESSION['site']['page_url'];
        }
        return false;
    }
    public function getPageDir() {
        if($this->site_id!=-1) {
            return $_SESSION['site']['page_dir'];
        }
        return false;
    }
    public function getGitRemote() {
        if($this->site_id!=-1) {
            return $_SESSION['site']['git'];
        }
        return false;
    }
}

class site_db extends SQLite3 {
    /** nav table **/
    
    public function getNavEntries() {       
        $nav=array();
        $stmt = $this->prepare("SELECT * FROM `nav` WHERE menu_order>=0 ORDER BY menu_order;");
        if($stmt) {
            $r = $stmt->execute();
            while($res = $r->fetchArray(SQLITE3_ASSOC)) {
                if(empty($res['file'])) {
                    $res['file'] = $res['link'];
                }
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


$d = new skyhog_db();
$a = new auth();
$oid = '';

if(array_key_exists("oid", $_COOKIE) && !empty($_COOKIE['oid'])) {
	$oid = $_COOKIE['oid'];
}

if(array_key_exists("openid_identifier", $_POST) && !empty($_POST["openid_identifier"])) {
	$oid = $_POST['openid_identifier'];
}

try {
    if(!$a->auth($d,$oid)) {
        $msg = "Please provide a valid OpenID";	
        Header("Location: index.php?msg=".urlencode($msg)."&redirect=".urlencode($_SERVER['SCRIPT_NAME']));
        exit(0);
    }

    if($a->isInactiveUser()) {
        if(isset($_REQUEST['redirect']) && $_REQUEST['redirect']=="/setup.php") {
            Header("Location: setup.php");
            exit(0);
        }            
        $msg = "your account needs to be activated by the administrator";
        Header("Location: index.php?msg=".urlencode($msg)."&redirect=".urlencode($_SERVER['SCRIPT_NAME']));
        exit(0);
    }
} catch (Exception $e) {
    $msg = 'An error occured: '.$e->getMessage();
    Header("Location: index.php?msg=".urlencode($msg));
    exit(0);
}

$msg = '';

$site_id = -1;
if(isset($_REQUEST['site_id'])) {
    $site_id = $_REQUEST['site_id'];
}
$s = new site($d,$site_id);
$l = new skylog("system.log",$a->getAuthUserName());

if($site_id!=-1 && $site_id!=$s->getSiteID()) {
    $msg="The requested site does not exist! Check the maintenance script, if that seems to be wrong.";
}

if($s->getSiteID()==-1 && FALSE===array_search(basename($_SERVER['SCRIPT_NAME']),array("sites.php","setup.php","edit_site.redirect.php"))) {
    if(!isset($msg) || empty($msg)) {
        $msg = "because of an unknown error the site could not be changed";
    }    
    Header("Location: sites.php?msg=".urlencode($msg)."&redirect=".urlencode($_SERVER['SCRIPT_NAME']));
    exit(0);
}
