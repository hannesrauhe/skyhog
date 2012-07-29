<?php
require_once('./base.inc.php');

$arg1 = escapeshellarg(dirname(__FILE__)."/generate.py");
$arg2 = "";
if(array_key_exists("finalize", $_POST)) {
	$arg2 = "--final";
}
$retvar = 0;
$ret = system(PYTHON_CMD." $arg1 $arg2",$retvar);
if($ret === FALSE || $retvar!=0) {
    echo "ERROR executing python\n";
    exit();
}
echo "\n";
echo "SUCCESS";
?>
