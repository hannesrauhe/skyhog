<?php
$arr = array();
exec ( "git log", $arr);

foreach($arr as &$a) {
	echo $a."<br />";
}

?>
