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

chdir(UPLOAD_DIR);	
$filename = "__template.html";

$arg1 = escapeshellarg(dirname(__FILE__)."/../check.py");
$arg2 = escapeshellarg(UPLOAD_DIR.$filename);
$retvar = 0;
$ret = system(PYTHON_CMD." $arg1 $arg2 2>&1",$retvar);
if($ret === FALSE || $retvar!=0) {
    echo "ERROR executing python\n";
	echo $ret;
    exit();
}
