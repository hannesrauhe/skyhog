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

if($a->isAdmin()) {
	$msg = "Settings have to be changed manually in config.inc.php";
} else {
	$msg = "You don't have admin permissions!";
}
?>

<html>
	<head>		
		<link rel="stylesheet" type="text/css" href="style.css" media="all">
	</head>
	<body>
<?php
include_once("nav.inc.phtml");
if(!empty($msg)) {
	echo "<p id=\"msg\">$msg</p>";
}
	$const_d = get_defined_constants(true);
	$const_d = $const_d['user'];
?>
	<table>
	<?php foreach ($const_d as $key => $value) {
		echo "<tr><td>$key</td><td>$value</td></tr>\n"; 
	}
	?>
	</table>	
<?php
include_once("footer.inc.phtml");
?>	
	</body>
</html>
