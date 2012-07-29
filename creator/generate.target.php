<?php
require_once('./base.inc.php');

$arg1 = escapeshellarg(dirname(__FILE__)."/generate.py");
$retvar = 0;
$ret = system(PYTHON_CMD." $arg1",$retvar);
echo $ret;
if($ret === FALSE || $retvar!=0) {
    echo "ERROR executing python\n";
    exit();
}
echo "\n";
echo "SUCCESS";
?>
