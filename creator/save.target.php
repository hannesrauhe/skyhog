<?php
require_once('./base.inc.php');

if(!array_key_exists("file",$_POST)) {
	echo "ERROR: wrong data submitted (file missing)";
	exit(); 
}

$file = basename($_POST['file']);
if(!is_file(UPLOAD_DIR.$file)) {
	echo "ERROR: file does not exist";
	exit(); 	
}

if(array_key_exists('elm1',$_POST) && !empty($_POST['elm1'])) {
	file_put_contents (UPLOAD_DIR.$file,$_POST['elm1']);
} else {
	echo "ERROR: no content for $file submitted!";
	exit();
}

$gitarg1 = escapeshellarg(UPLOAD_DIR);
$gitarg2 = escapeshellarg(UPLOAD_DIR.$file);
$retvar = 0;
$ret = system(GIT_CMD." --git-dir=$gitarg1 add $gitarg2",$retvar);
echo GIT_CMD." --git-dir=$gitarg1 add $gitarg2";
if($ret === FALSE || $retvar!=0) {
    echo "ERROR: adding $file with git wasn't possible\n";
	echo $ret;
    exit();
}
$gitarg2 = escapeshellarg("Commit from webinterface, IP:".$_SERVER["REMOTE_ADDR"]);
$retvar = 0;
$ret = system(GIT_CMD." --git-dir=$gitarg1 commit -m $gitarg2",$retvar);
if($ret === FALSE || $retvar!=0) {
    echo "ERROR: commiting staged $file with git wasn't possible, commit-msg was $gitarg\n";
	echo $ret;
    exit();
}
echo "\n";
echo "SUCCESS";
?>
