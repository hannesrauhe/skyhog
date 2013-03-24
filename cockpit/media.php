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
require_once("base.inc.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Media Manager</title>
<link rel="stylesheet" type="text/css" href="css/ui-lightness/jquery-ui-1.8.23.custom.css" media="all">
<link rel="stylesheet" type="text/css" href="style.css" media="all">
<link rel="stylesheet" type="text/css" href="css/media.css" media="all">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta charset="UTF-8" />

<script src="js/jquery-1.8.0.min.js"></script>
<script src="js/media.js"></script>
</head>
<body role="application" class="starting">
<?php
include_once("nav.inc.phtml");
?>
	<p id="sh_msg"> 
		<?php echo $msg; ?>
	</p>
	<div id="kcfinder_div">
		<iframe name="kcfinder_iframe" src="kcfinder-2.51/browse.php?type=files"frameborder="0" width="100%" height="100%" marginwidth="0" marginheight="0" scrolling="no" />
    </div>
<?php
include_once("footer.inc.phtml");
?>
</body>
</html>