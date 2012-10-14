<?php
require_once("base.inc.php");

$msg = "";
$file = "";

if(array_key_exists("file",$_GET)) {
	$file = basename($_GET['file']);
	if(!is_file(UPLOAD_DIR.$file)) {
		$msg = "File $file does not exist";
		$file = "_index.html";
	}
} else {
	$file = "_index.html";
}

$ordered_pages = $d->getNavEntries();

$pages = array();
if ($handle = opendir(UPLOAD_DIR)) {
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
<script src="tiny_mce/tiny_mce.js"></script>
<script type="text/javascript" >	
	function openKCFinder(field_name, url, type, win) {
	    tinyMCE.activeEditor.windowManager.open({
	        file: 'kcfinder-2.51/browse.php?opener=tinymce&type=' + type,
	        title: 'KCFinder',
	        width: 700,
	        height: 500,
	        resizable: "yes",
	        inline: true,
	        close_previous: "no",
	        popup_css: false
	    }, {
	        window: win,
	        input: field_name
	    });
	    return false;
	}
	
	tinyMCE.init({
		// General options
		mode : "textareas",
		document_base_url : "<?php echo UPLOAD_PATH; ?>",
		theme : "advanced",
		plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave,visualblocks",

		// Theme options
		theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
		theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft,visualblocks",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,

		// Example content CSS (should be your site CSS)
		content_css : "<?php echo UPLOAD_PATH ?>index.css",
		file_browser_callback: 'openKCFinder',

		// Style formats
		style_formats : [
			{title : 'Bold text', inline : 'b'},
			{title : 'Red text', inline : 'span', styles : {color : '#ff0000'}},
			{title : 'Red header', block : 'h1', styles : {color : '#ff0000'}},
			{title : 'Example 1', inline : 'span', classes : 'example1'},
			{title : 'Example 2', inline : 'span', classes : 'example2'},
			{title : 'Table styles'},
			{title : 'Table row 1', selector : 'tr', classes : 'tablerow1'}
		]
	});
</script>
<script src="js/pages.js"></script>
</head>
<body role="application" class="starting">
<?php
include_once("nav.inc.phtml");
?>
	<p id="sh_msg"> 
		<?php echo $msg; ?>
	</p>
	<aside id="skyhog_aside_right">
		<h2>Pages</h2>
		<div id="skyhog_pages" class="sh_with_border">
			<h3>In Nav Menu</h3>
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
			<h3>Not in Nav Menu</h3>
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
		Content of <?php echo $file; ?>
		</h2>
		<form method="post" action="save.target.php" id="content_form">
			<div>	
				<!-- Gets replaced with TinyMCE, remember HTML in a textarea should be encoded -->
				<div>
					<textarea id="elm1" name="elm1" rows="40" cols="180" style="width: 80%" enctype="multipart/form-data">
			                <?php echo file_get_contents (UPLOAD_DIR.$file); ?>
					</textarea>
				</div>	
				
				<span id="sh_plain_options">
					<a id="sh_show_tiny" href="javascript:;">[Show TinyMCE]</a>
					<a href="javascript:;" onclick="document.getElementById('elm1').value = style_html(tinyMCE.get('elm1').getContent());return false;">[Beautify Code]</a>
				</span>
				
				<span id="sh_tinymce_options">
					<a id="sh_hide_tiny" href="javascript:;">[Show Code]</a>	
				</span>	
				
				<script type="text/javascript">
					$('#sh_show_tiny').click(function() {
						$('#sh_plain_options').hide();
						$('#sh_tinymce_options').show();
						tinyMCE.get('elm1').show();
						return false;
					});
					$('#sh_hide_tiny').click(function() {
						$('#sh_tinymce_options').hide();
						$('#sh_plain_options').show();
						tinyMCE.get('elm1').hide();
						return false;
					});
				</script>		
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
