<?php
if (!ini_get('display_errors')) {
    ini_set('display_errors', '1');
}

function check_dir($path,$descr) {
if(is_dir($path)) {
    echo $descr." exists ";
    if(@touch($path."/testtouch") && @unlink($path."/testtouch")) {
        echo "and is writable\n";
    } else {
        echo "but is not writable!\n";
        exit(1);
    }
} else {
    if(mkdir($path)) {
        echo $descr." created\n";
    } else {
        echo "couldn't create ".$descr."!\n";
        exit(1);
    }
}
}
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
?>
<a href="index.php">Start (wait for SUCCESS message)</a>
<br />
<a href="setup.php">Reload</a>
<br />
<textarea readonly="readonly" rows="20" cols="80">
<?php
require_once("setup/00_check_config.inc.php");

//reinit Session
session_start();
$_SESSION = array();
session_destroy();

//preview/upload directory
/*
if(is_dir(UPLOAD_DIR)) {
	echo "Preview dir exists ";
	if(touch(UPLOAD_DIR."/testtouch") && unlink(UPLOAD_DIR."/testtouch")) {
		echo "and is writable\n";
	} else {
		echo "but is not writable!\n";
		exit(1);
	}
} else {
	if(defined("USE_GIT_REPO")) {
		$ret=1;
		echo "The output of git clone:\n";
		system( GIT_CMD." clone ".escapeshellarg(USE_GIT_REPO)." ".UPLOAD_DIR." 2>&1", $ret); 		
		if($ret!==0) {
			echo "git clone failed somehow!\n";
			exit(1);
		}
	} else {
		if(mkdir(UPLOAD_DIR)) {
			echo "Preview dir created\n";
		} else {
			echo "couldn't create the preview/upload dir!\n";
			exit(1);
		}
	}
}
*/

//create/check directories
check_dir(LOG_DIR, "log directory");
check_dir(BAK_DIR, "backup directory");
check_dir(WRK_DIR, "working directory");

//db
$db = new SQLite3(DB_NAME);

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

$db->close();

// basic tests are done - now do the authentication
require_once("./base.inc.php");

//check the tables now
require_once("./setup/01_check_tables.inc.php");

/*
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

echo "Checking local git config:";
system( GIT_CMD. ' config --get user.name',$ret);
echo $ret."\n";
if($ret!=0) {
	echo "No local user is set, setting the defaults for you\n";
	system( GIT_CMD. ' config user.name "SkyHog CMS"');
	system( GIT_CMD. ' config user.email info@scitivity.net');
}

if(!is_file(".gitignore")) {
	if(FALSE===file_put_contents(".gitignore", "*.html\n!_*.html")) {
		echo ".gitignore could not be created\n";
		exit(1);
	}
	echo ".gitignore created\n";
} else {
	echo ".gitignore is there\n";	
}


chdir(dirname(__FILE__));
if(!is_file(UPLOAD_DIR."__template.html")) {
	if(!copy("setup/__template.html", UPLOAD_DIR."__template.html")) {
		echo "__template.html could not be created\n";
		exit(1);
	}
	echo "__template.html created\n";
} else {
	echo "__template.html is there\n";	
}


if(!is_file(UPLOAD_DIR."_index.html")) {
	if(!copy("setup/_index.html", UPLOAD_DIR."_index.html")) {
		echo "_index.html could not be created\n";
		exit(1);
	}
	echo "_index.html created\n";
} else {
	echo "_index.html is there\n";	
}
*/
echo "SUCCESS";
?>
</textarea>

