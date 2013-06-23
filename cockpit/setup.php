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
if (!ini_get('display_errors')) {
    ini_set('display_errors', '1');
}

function check_dir($path,$descr,$warning='') {
    if(substr($path,-1)!="/") {
        echo $path." has no directory separator in the end! Please add one in the config file!";
        exit(1);
    }
    if(is_dir($path)) {
        echo $descr." exists ";
        if(@touch($path."/testtouch") && @unlink($path."/testtouch")) {
            echo "and is writable\n";
        } else {
            echo "but is not writable: ".$path."!\n";
            if(empty($warning))
                exit(1);
            else "WARNING: Scyhog does not necessarily need acccess rights to this directory, but the following will not work: ".$warning."\n";
        }
    } else {
        if(mkdir($path)) {
            echo $descr." created\n";
        } else {
            echo "couldn't create ".$descr." at ".$path."!\n";
            exit(1);
        }
    }
}

//reinit Session
session_start();
$_SESSION = array();
session_destroy();

ob_start();
?>
<a href="setup.php">Reload</a>
<br />
<textarea readonly="readonly" rows="20" cols="80">
<?php
require_once("setup/00_check_config.inc.php");


//create/check directories
check_dir("./","skyhog directory","update via webinterface");
check_dir(LOG_DIR, "log directory");
check_dir(BAK_DIR, "backup directory");
check_dir(WRK_DIR, "working directory");
check_dir(DEFAULT_LIVE_DIR, "live pages directory","creating directories for new pages automatically");
check_dir(DEFAULT_PREVIEW_DIR, "preview pages directory","creating directories for new pages automatically");

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

ob_end_flush();

//check the tables now
require_once("./setup/01_check_tables.inc.php");

echo "The Skyhog installation looks good\n";
if($s->getSiteID()==-1) {
    echo "... but there is no site installed/choosen. Click \"start\".\n";
} else {
    echo "I'm checking the site ".$s->getSiteName()." now.\n";
    require_once("./setup/03_check_site.inc.php");
}
echo "\nSUCCESS";
?>
</textarea>
<br />
Your installation seems to be ok (you should see the SUCCESS message above): <a href="index.php">Start</a>
<br /><br /><br /><br />
config.inc.php looks like this:<br />
<textarea readonly="readonly" rows="20" cols="80">
<?php
    echo file_get_contents(dirname(__FILE__)."/config.inc.php");
?>    
</textarea>
