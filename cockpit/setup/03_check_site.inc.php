<?php
/*
Copyright 2012, 2013 Hannes Rauhe

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

//preview/upload directory
$git_rem = $s->getGitRemote();

$git_disabled = GIT_CMD=="DISABLE" || $git_rem=="DISABLE";
if(!$git_disabled && !is_dir($s->getPreviewDir()) && !empty($git_rem)) {
    echo "Trying to clone ".$s->getGitRemote()." to directory ".$s->getPreviewDir()."\n";
    system( GIT_CMD." clone ".escapeshellarg($s->getGitRemote())." ".escapeshellarg($s->getPreviewDir())." 2>&1", $ret);    
    if($ret!==0) {
        echo "git clone failed somehow!";// I'm going to initialize a standard page. Delete it and reinitialize again, if you want to retry.\n";
        exit(1);
    }
}
check_dir($s->getPreviewDir(), "the sites preview directory");
check_dir($s->getPageDir(), "the sites page/live directory");

//git
if(!$git_disabled) {
    chdir($s->getPreviewDir());
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
        system( GIT_CMD. ' config --global user.name "SkyHog CMS"');
        system( GIT_CMD. ' config --global user.email info@scitvity.net');
        system( GIT_CMD. ' config user.name "SkyHog CMS"');
        system( GIT_CMD. ' config user.email '+$a->getAuthUserMail());
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
}


chdir(dirname(__FILE__));
if(!is_file($s->getPreviewDir()."__template.html")) {
    if(!copy("__template.html", $s->getPreviewDir()."__template.html")) {
        echo "__template.html could not be created\n";
        exit(1);
    }
    echo "__template.html created\n";
} else {
    echo "__template.html is there\n";  
}


if(!is_file($s->getPreviewDir()."_index.html")) {
    if(!copy("_index.html", $s->getPreviewDir()."_index.html")) {
        echo "_index.html could not be created\n";
        exit(1);
    }
    echo "_index.html created\n";
} else {
    echo "_index.html is there\n";  
}

$ds = new site_db($s->getPreviewDir()."scihog.db");
/* nav table*/
$nav_schema = "CREATE TABLE nav (link TEXT, id TEXT PRIMARY KEY, name TEXT, menu_order INTEGER, file TEXT)";

$query_result = $ds->querySingle('SELECT * FROM nav', true);
if($query_result===FALSE) {
    $r = $ds->exec($nav_schema.";");
    if(!$r) {
        echo "Nav table cannot be created, because: \n";
        echo $ds->lastErrorMsg();
        exit(1);
    }
    echo "Created nav table.\n";
} else {
    echo "Nav table exists... ";
    $r = $ds->query("select sql from sqlite_master where type = 'table' and name = 'nav';");
    $res = $r->fetchArray(SQLITE3_ASSOC);
    if($nav_schema!=$res['sql']) {
        echo "but its schema is\n".$res['sql']." and should be\n".$nav_schema."\n";
        $query_result = FALSE;
        if($res['sql'] == "CREATE TABLE nav (link TEXT, id TEXT PRIMARY KEY, name TEXT, menu_order INTEGER)") {
            $query_result = $ds->exec('ALTER TABLE nav ADD COLUMN file TEXT');
            echo "I'm trying to add the missing column: ".$ds->lastErrorMsg();
            echo "\n";
        }
        if($query_result===FALSE) {
            echo "Please use PHPsqlite to export the table, delete it afterwards and rerun the maintenance script! Try to import the data to get it back!\n";
            echo "This script continues now, because this error might not be critical! \n\n";
        } else {
            echo "I added the missing column\n";
        }
    } else {
        echo "and looks fine\n";
    }
}

