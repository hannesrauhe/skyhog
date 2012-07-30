<?php
require_once("./base.inc.php");

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

