<?php
if((include_once("./config.inc.php")) === FALSE) {
//config does not exist yet - use template
	if(!array_key_exists('DOMAIN', $_POST)) {
		echo "TODO: Print form!\n"; //TODO
		exit(1);
	} else {
		//relevant data has been POSTED - replace in config-template
		$file_c = file_get_contents("./config.inc.php.template");
		if($file_c) {
		    $file = str_replace(array_keys($_POST), array_values($_POST), $file);
		    if(file_put_contents("./config.inc.php", $file_c) === FALSE) {
		    	echo "The config file hasn't been written, make the scyhog directory writable for the webserver and reload or copy this content to config.inc.php:\n";
				echo $file;
				exit(1);
		    }
		}
	}
}

//reinit Session
session_start();
$_SESSION = array();
session_destroy();

//preview/upload directory
if(is_dir(UPLOAD_DIR)) {
	echo "Preview dir exists ";
	if(touch(UPLOAD_DIR."/testtouch") && unlink(UPLOAD_DIR."/testtouch")) {
		echo "and is writable\n";
	} else {
		echo "but is not writable!\n";
		exit(1);
	}
} else {
	if(mkdir(UPLOAD_DIR)) {
		echo "Preview dir created\n";
	}
}

//db
$db = new SQLite3(UPLOAD_DIR."scihog.db");

/* Users table */
$query_result = $db->querySingle('SELECT * FROM users', true);
if($query_result===FALSE) {
	$r = $db->exec("CREATE TABLE users(user_id INTEGER PRIMARY KEY, name VARCHAR(100), openid VARCHAR(255) UNIQUE, email VARCHAR(100), active BOOL, admin BOOL);");
	if(!$r) {
		echo "User table cannot be created, because: \n";
		echo $db->lastErrorMsg();
		exit(1);
	}
	echo "Created user table. Please reload!\n";
	exit(0);
} else if(!empty($query_result)) {
	$query_result = $db->querySingle('SELECT * FROM users WHERE user_id=1', true);
	if(empty($query_result)) {
		echo "User table exists and has entries, but User 1 isn't there... repair manually!\n";
		exit(1);
	}
	if($query_result['active']!=1) {
		if(!$db->exec("UPDATE users SET active=1,admin=1 WHERE user_id=1")) {
			echo "User 1 seems to be inactive and could not be activated, because: \n";
			echo $db->lastErrorMsg();
			exit(1);
		}
		echo "Activated User 1. Please reload!\n";
		exit(0);
	}
}

/* nav table */
$query_result = $db->querySingle('SELECT * FROM nav', true);
if($query_result===FALSE) {
	$r = $db->exec("CREATE TABLE nav (link TEXT, id TEXT PRIMARY KEY, name TEXT, menu_order INTEGER);");
	if(!$r) {
		echo "Nav table cannot be created, because: \n";
		echo $db->lastErrorMsg();
		exit(1);
	}
} 

$db->close();


// basic tests are done - now do the authentication
require_once("./base.inc.php");


//live directory
if(is_dir(PAGE_DIR)) {
	echo "Page dir exists ";
	if(touch(PAGE_DIR."/testtouch") && unlink(PAGE_DIR."/testtouch")) {
		echo "and is writable\n";
	} else {
		echo "but is not writable!\n";
		exit(1);
	}	
} else {
	echo "Page dir does not exist!\n";
	exit(1);
}

//git
chdir(UPLOAD_DIR);
$arr = array();
$ret=0;

echo "The output of git init:\n";
system( GIT_CMD." init 2>&1",$ret); 
if($ret!==0) {
	echo "git init failed somehow!\n";
	exit(1);
}

if(!is_file(".gitignore")) {
	if(FALSE===file_put_contents(".gitignore", "*.html\n!_*.html")) {
		echo ".gitignore could not be created\n";
	}
	echo ".gitignore created\n";
}


