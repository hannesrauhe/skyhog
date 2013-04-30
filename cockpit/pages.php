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
$file = "";

if(array_key_exists("file",$_GET)) {
	$file = basename($_GET['file']);
	if(!is_file($s->getPreviewDir().$file)) {
		$msg = "File $file does not exist";
		$file = "_index.html";
	}
} else {
	$file = "_index.html";
}
$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
if(!in_array($ext,array("html","css","js")) || strstr($file,"..")) {
	$file = "__template.html";
}

if(!is_file($s->getPreviewDir().$file)) {
	$msg = "File $file does not exist!";
}		

$file_list = array();
$startpos = strlen(realpath($s->getPreviewDir()))+1;
foreach(bfglob($s->getPreviewDir(),"*.{css,js}", GLOB_BRACE, -1) as $f) {
	$file_list[] = substr($f,$startpos);
}
foreach(bfglob($s->getPreviewDir(),"__*.html", GLOB_BRACE, -1) as $f) {
	$file_list[] = substr($f,$startpos);
}

$ds = new site_db($s->getPreviewDir()."scihog.db");
$ordered_pages = $ds->getNavEntries();
//$ordered_pages = array();

$pages = array();
if ($handle = opendir($s->getPreviewDir())) {
    while (false !== ($f = readdir($handle))) {
    	if(substr($f,0,1)=='_' && substr($f,1,1)!='_') {
    		$f = substr($f,1);
    		foreach($ordered_pages as $o) {
    			if($o['link']==$f) 
					continue(2);
    		}
        	$pages[] = $f;
		}
    }
} else {
	$msg .= "Error: preview-directory cannot be opened! Run the maintenance script for more information";
}
			
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Edit Pages</title>
<link rel="stylesheet" type="text/css" href="css/ui-lightness/jquery-ui-1.8.23.custom.css" media="all">
<link rel="stylesheet" type="text/css" href="style.css" media="all">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta charset="UTF-8" />

<script src="js/jquery-1.8.0.min.js"></script>
<script src="js/jquery-ui-1.8.23.custom.min.js"></script>
<script src="js/jquery.form.js"></script>
<script src="js-beautify/beautify-html.js"></script>
<script src="js/pages.js"></script>
</head>
<body role="application" class="starting">
<?php
include_once("nav.inc.phtml");
?>
	<p id="msg"> 
		<?php echo $msg; ?>
	</p>
	<aside id="skyhog_aside_right">
		<div id="skyhog_pages" class="sh_with_border">
			<h3>Pages</h3>
			<h4>In Nav Menu</h4>
			<ul id="sorted_menu" class="connectedSortable">
				<?php
			    foreach($ordered_pages as $f) {
		        	echo "<li class='ui-state-default'>
			        	<span class='ui-icon ui-icon-arrowthick-2-n-s' style='float:left'></span><a href='".$_SERVER['PHP_SELF']."?file=_".$f['link']."'>".$f['id']."</a>
			        	<span class='ui-icon ui-icon-wrench sh_page_settings' style='float:right'>".json_encode($f)."</span>
			        	</li>";
				}
				?>
					
			</ul>
			<h4>Not in Nav Menu</h4>
			<ul id="unsorted_menu" class="connectedSortable">
				<?php
			    foreach($pages as $f) {
		        	echo "<li class='ui-state-highlight new_menu_entry'>
		        		<span class='ui-icon ui-icon-arrowthick-2-n-s' style='float:left'></span><a href='".$_SERVER['PHP_SELF']."?file=_$f'>$f</a>
		        		</li>";
				}
				?>				
			</ul>	
			<ul id="sh_icons" class="ui-widget ui-helper-clearfix" style="display:none;">			
				<li class="ui-state-default ui-corner-all" title="Add Page" id="sh_add_page">
					<a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?new=html" class="ui-icon ui-icon-plusthick"></a>
				</li>	
			</ul>
			<form id="sh_new_file_form" method="POST" action="new.redirect.php">
				<input id="sh_new_file_name" type="text" name="file" />
				<input type="hidden" name="format" value="html" />
				<input type="submit" value="+" />
			</form>		

		</div>
		
		
		<div class="sh_with_border">
		  <h3>Files</h3>
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
				<a href="<?php echo $s->getPreviewURL() ?>" target="new" >Show Preview</a>
				<a href="<?php echo $s->getPageURL() ?>" target="new" >Show Homepage</a>
			</div>
		</div>
	</aside>
	<section id="main_container" style="padding:10px">
		<h2>
		Content of <?php echo $file; ?>
		</h2>
		<form method="post" action="save.target.php" id="content_form">
			<div>	
				<!-- Gets replaced with TinyMCE, remember HTML in a textarea should be encoded -->
				<div>
					<textarea id="elm1" name="elm1" rows="40" cols="180" style="width: 80%" enctype="multipart/form-data"><?php echo file_get_contents ($s->getPreviewDir().$file); ?></textarea>
				</div>	
				
				<span id="sh_plain_options">
					<a id="sh_show_tiny" href="javascript:;">[Show TinyMCE]</a>
                    <a href="check_html.php?file=<?php echo $file; ?>" target="_blank" >[Check Code]</a>
					<a href="javascript:;" onclick="document.getElementById('elm1').value = style_html(document.getElementById('elm1').value);return false;">[Beautify Code]</a>
				</span>
					
				<br />
				<input type="hidden" name="file" value="<?php echo $file; ?>" />
				<input type="hidden" name="navigation_changed" value="0" />
				<input type="submit" name="save"/>
				<input type="reset" name="reset" value="Reset" />
			</div>
		</form>
	</section>
	<div id="sh_page_settings_dialog" title="Change Page Settings">
		<form method="post" action="page_setting.target.php" id="sh_page_settings_form">
			<input type="hidden" name="sh_page_id_old" id="sh_page_id_old" value="" />
			<table>
				<tr><td>Link: </td>			<td><input type="text" id="sh_page_link" name="sh_page_link" value="" readonly="readonly" /></td></tr>
				<tr><td>ID (HTML): </td>	<td><input type="text" id="sh_page_id" name="sh_page_id" value="" /></td></tr>
				<tr><td>Name/Title: </td>	<td><input type="text" id="sh_page_name" name="sh_page_name" value="" /></td></tr>
			</table>
			<input type="submit" name="save"/>
		</form>
	</div>
	<div  style="clear: both"></div>
<?php
include_once("footer.inc.phtml");
?>
</body>
</html>
