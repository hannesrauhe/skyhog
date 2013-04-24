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

chdir($s->getPreviewDir());	

if(array_key_exists("navigation_changed", $_POST) && !empty($_POST["navigation_changed"])) {
	$nav_order = $_POST["navigation_changed"];
	//var_dump($nav_order);
	if(is_array($nav_order)) {
		$d->orderNavEntries($nav_order);
		git::add(DB_NAME);
		git::commit($a->getAuthUserName()." <".$a->getAuthUserMail().">", "Navigation changes");
	} else {
		echo "Internal Error: new navigation submitted is not an array";
	}
}

$arg1 = escapeshellarg(dirname(__FILE__)."/../generate.py");
$arg2 = "";
$arg4 = "";
$arg5 = "";
if(array_key_exists("finalize", $_POST)) {
	$arg2 = "--final True";
	$arg4 = "--page_dir ".escapeshellarg($s->getPageDir());
	$arg5 = "--backup_dir ".escapeshellarg(BAK_DIR);
}
$arg3 = "--template ".escapeshellarg($s->getPreviewDir()."__template.html");
$retvar = 0;
$ret = system(PYTHON_CMD." $arg1 $arg2 $arg3 $arg4 $arg5",$retvar);
//echo PYTHON_CMD." $arg1 $arg2 $arg3 $arg4 $arg5";
if($ret === FALSE || $retvar!=0) {
    echo "ERROR executing python\n";
	echo $ret;
    exit();
}
echo "\n";
echo "SUCCESS";

?>
