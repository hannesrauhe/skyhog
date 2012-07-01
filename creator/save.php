<?php
$directory="../";
if(!array_key_exists("file",$_POST)) {
	header ("Location: edit.php"); 
	exit(); 
}
$file = basename($_POST['file']);
if(!is_file($directory.$file)) {
	header ("Location: edit.php?file=$file"); 
	exit(); 	
}

if(array_key_exists("content",$_POST) && !empty($_POST['content'])) {
	file_put_contents ($directory.$file,$_POST['file']);
} else {
	echo "ERROR: no content for $file submitted!";
	exit();
}

$gitarg = escapeshellarg($directory.$file);
if(!system("git add $gitarg")) {
	echo "ERROR: adding $file with git wasn't possible";
	exit();
}
$gitarg = escapeshellarg("Commit from webinterface, IP:".$_SERVER["REMOTE_ADDR"]);
if(!system("git commit -m $gitarg")) {
	echo "ERROR: commiting staged $file with git wasn't possible, commit-msg was ".$gitarg;
	exit();
}

header ("Location: edit.php?file=$file"); 
?>