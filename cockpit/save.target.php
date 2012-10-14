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
require_once('./base.inc.php');

if(!array_key_exists("file",$_POST)) {
	echo "ERROR: wrong data submitted (file missing)";
	exit(); 
}

$file = basename($_POST['file']);
if(!is_file(UPLOAD_DIR.$file) && !array_key_exists("new",$_POST)) {
	echo "ERROR: file does not exist";
	exit(); 	
}

if(array_key_exists('elm1',$_POST) && !empty($_POST['elm1'])) {
	file_put_contents (UPLOAD_DIR.$file,$_POST['elm1']);
} else {
	echo "ERROR: no content for $file submitted!";
	exit();
}

chdir(UPLOAD_DIR);
git::add($file);
git::commit($a->getAuthUserName(), "Commit from webinterface");
echo "\n";
echo "SUCCESS";
?>
