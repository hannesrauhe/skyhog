<?php
require_once 'base.inc.php';

$msg="";
$format = "";

if(array_key_exists("file",$_REQUEST)) {
	$file = basename($_REQUEST['file']);
} else {
	exit(1);
}

if(array_key_exists("format",$_REQUEST)) {
	$format = $_REQUEST['format'];
}

if($format=="html") {
	if(substr($file,0,1)!='_') {
		$file="_".$file;
	} 
	
	if(substr($file,1,1)=='_') {
		$file="_a".substr($file,2);
	}	
	
	if(substr($file,-5)!='.html') {
		$file=$file.".html";
	}
	
	if(is_file(UPLOAD_DIR.$file)) {
		$msg = "File $file already exists";
		exit(1);
	}
	fclose(fopen(UPLOAD_DIR.$file, 'a'));
	Header("Location:pages.php?file=".urlencode($file));
	exit(0);
}
	

