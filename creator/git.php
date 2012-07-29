<?php
require_once('./base.inc.php');

$arr = array();
exec ( "git --git-dir=".UPLOAD_DIR." log", $arr);

foreach($arr as &$a) {
	echo $a."<br />";
}

?>
