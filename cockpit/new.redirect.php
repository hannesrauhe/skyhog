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
	

