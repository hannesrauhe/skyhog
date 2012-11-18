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

function bfglob($path, $pattern = '*', $flags = 0, $depth = 0) {
    $matches = array();
    $folders = array(rtrim($path, DIRECTORY_SEPARATOR));
    
    while($folder = array_shift($folders)) {
        $matches = array_merge($matches, glob($folder.DIRECTORY_SEPARATOR.$pattern, $flags));
        if($depth != 0) {
            $moreFolders = glob($folder.DIRECTORY_SEPARATOR.'*', GLOB_ONLYDIR);
            $depth   = ($depth < -1) ? -1: $depth + count($moreFolders) - 2;
            $folders = array_merge($folders, $moreFolders);
        }
    }
    return $matches;
}

$msg = "";
$file = isset($_REQUEST['file']) ? $_REQUEST['file'] : "__template.html";
$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
if(!in_array($ext,array("html","css","js")) || strstr($file,"..")) {
	$file = "__template.html";
}

if(!is_file(UPLOAD_DIR.$file)) {
	$msg = "File $file does not exist!";
}		

$file_list = array();
$startpos = strlen(realpath(UPLOAD_DIR))+1;
foreach(bfglob(UPLOAD_DIR,"*.{css,js}", GLOB_BRACE, -1) as $f) {
	$file_list[] = substr($f,$startpos);
}
foreach(bfglob(UPLOAD_DIR,"__*.html", GLOB_BRACE, -1) as $f) {
	$file_list[] = substr($f,$startpos);
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Edit Page</title>
<script src="js/jquery-1.8.0.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/ui-lightness/jquery-ui-1.8.23.custom.css" media="all">
<script src="js/jquery-ui-1.8.23.custom.min.js"></script>
<!--<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/jquery-ui.min.js"></script>-->
<script src="http://malsup.github.com/jquery.form.js"></script> 
<script src="js/template.js"></script>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta charset="UTF-8" />


<link rel="stylesheet" type="text/css" href="style.css" media="all">
</head>
<body role="application" class="starting">
<?php
include_once("nav.inc.phtml");
?>
	<p id="msg"> 
		<?php echo $msg; ?>
	</p>
	<aside id="skyhog_aside_right">
		<h2>Files</h2>
		<div id="skyhog_files" class="sh_with_border">
			<ul>
				<?php
			    foreach($file_list as $f) {
		        	echo "<li>
			        	<a href='".$_SERVER['PHP_SELF']."?file=$f'>$f</a>
			        	</li>";
				}
				?>
					
			</ul>
		</div>
		<div class="sh_with_border">
			<div id="generate_buttons">
				<input type="hidden" name="navigation_changed" value="0" />
				<button id="b_generate_prev">Generate Preview</button>
				<button id="b_generate">Generate!</button><br />
			</div>
			<div id="preview_links">
				<a href="<?php echo UPLOAD_PATH ?>" target="new" >Show Preview</a>
				<a href="<?php echo PAGE_PATH ?>" target="new" >Show Homepage</a>
			</div>
		</div>
	</aside>
	<section id="main_container" style="padding:10px">
		<h2>
		Template <?php echo $file; ?>
		</h2>
		<form method="post" action="save.target.php" id="content_form" enctype="multipart/form-data">
			<div>	
				<!-- Gets replaced with TinyMCE, remember HTML in a textarea should be encoded -->
				<div>
					<textarea id="elm1" name="elm1" rows="40" cols="180" style="width: 80%"><?php	echo file_get_contents (UPLOAD_DIR.$file); ?></textarea>
				</div>
				<input type="hidden" name="file" value="<?php echo $file; ?>" />
				<input type="submit" name="save" value="Submit" />
				<input type="reset" name="reset" value="Reset" />
			</div>
		</form>
	</section>
	<div  style="clear: both"></div>
<?php
include_once("footer.inc.phtml");
?>
</body>
</html>
