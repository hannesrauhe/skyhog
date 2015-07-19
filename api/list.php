<?php
/*
 Copyright 2012-2015 Hannes Rauhe

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
 
function run_scihog($script, $args, &$output, &$return_value) {
	$script = SCIHOG_DIR . $script;
	if(!is_file($script)) {
		$output = "$script is not a valid file or wasn't found";
		return "";
	}
	$cmd = escapeshellcmd(PYTHON_CMD) . " ";
	$cmd .= realpath($script);
	//do not return server-paths but only script and parameters
	$ret_cmd .= basename($script);
	foreach ($args as $arg) {
		$cmd .= " ". escapeshellarg($arg);
		$ret_cmd .= " ". escapeshellarg($arg);
	}
	exec($cmd . " 2>&1", $output, $return_value);
	return $ret_cmd;
}

require_once("./base.inc.php");

$json_return = [];

$retval = 0;
$output = "";
$json_return["cmd"] = run_scihog("list.py", [], $output, $retval);

if($retval!=0) {
	$json_return["status"]="error";
	$json_return["error"]=implode("\n", $output);
} else {
	$json_return["status"]="ok";
	$json_return["pages"]=$output;
}

echo json_encode($json_return);

