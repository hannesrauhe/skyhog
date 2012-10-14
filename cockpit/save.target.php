<?php
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
