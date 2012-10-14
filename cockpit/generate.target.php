<?php
require_once('./base.inc.php');

chdir(UPLOAD_DIR);	

if(array_key_exists("navigation_changed", $_POST) && !empty($_POST["navigation_changed"])) {
	$nav_order = $_POST["navigation_changed"];
	//var_dump($nav_order);
	if(is_array($nav_order)) {
		$d->orderNavEntries($nav_order);
		git::add(DB_NAME);
		git::commit($a->getAuthUserName(), "Navigation changes");
	} else {
		echo "Internal Error: new navigation submitted is not an array";
	}
}

$arg1 = escapeshellarg(dirname(__FILE__)."/generate.py");
$arg2 = "";
if(array_key_exists("finalize", $_POST)) {
	$arg2 = "--final True";
}
$arg3 = "--template ".escapeshellarg(UPLOAD_DIR."__template.html");
$retvar = 0;
$ret = system(PYTHON_CMD." $arg1 $arg2 $arg3",$retvar);
//echo PYTHON_CMD." $arg1 $arg2 $arg3";
if($ret === FALSE || $retvar!=0) {
    echo "ERROR executing python\n";
	echo $ret;
    exit();
}
echo "\n";
echo "SUCCESS";

?>
