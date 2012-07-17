<?php
require_once("base.inc.php");

$msg = "";
if(array_key_exists("file",$_GET)) {
	$file = basename($_GET['file']);
	if(!is_file(UPLOAD_DIR.$file)) {
		$msg = "File $file does not exist";
		$file = "_indexx.html";
	}
} else {
	$file = "_indexx.html";
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Edit Page</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<script src="upload/jquery.min.js"></script>
<script type="text/javascript" src="tiny_mce/tiny_mce.js"></script>
<script charset="utf-8" type="text/javascript" src="upload/upload.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	Uploader.init({
	    smart_mode: true
	});
	
	tinyMCE.init({
		// General options
		mode : "textareas",
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
		content_css : "preview/index.css",

		// Drop lists for link/image/media/template dialogs
		template_external_list_url : "lists/template_list.js",
		external_link_list_url : "lists/link_list.js",
		external_image_list_url : "lists/image_list.js",
		media_external_list_url : "lists/media_list.js",

		// Style formats
		style_formats : [
			{title : 'Bold text', inline : 'b'},
			{title : 'Red text', inline : 'span', styles : {color : '#ff0000'}},
			{title : 'Red header', block : 'h1', styles : {color : '#ff0000'}},
			{title : 'Example 1', inline : 'span', classes : 'example1'},
			{title : 'Example 2', inline : 'span', classes : 'example2'},
			{title : 'Table styles'},
			{title : 'Table row 1', selector : 'tr', classes : 'tablerow1'}
		],

		// Replace values for the template plugin
		template_replace_values : {
			username : "Some User",
			staffid : "991234"
		}
	});
	
});
</script>
<link rel="stylesheet" media="screen" type="text/css" href="upload/upload.css" />

</head>
<body role="application" class="starting">
	<p id="msg"> 
		<?php echo $msg; ?>
	</p>
	<aside id="upload_container" style="float:left">
		<div id="filedrop">
		  <div id="filedrop-inner-box">
		    <div id="filedrop-chooser">
		      <svg id="upload-icon" xmlns="http://www.w3.org/2000/svg" version="1.1" width="100" height="70">
		        <linearGradient id="grd" x1="0%" y1="0%" x2="0%" y2="100%">
		          <stop offset="10%" style="stop-color: rgb(27, 132, 224);" />
		          <stop offset="90%" style="stop-color: rgb(27, 62, 94);" />
		        </linearGradient>
		        <path transform="translate(10, 10) scale(1.5, 1.5)" fill="url(#grd)" d="M 0 10 Q 0 0 10 0 L 110 0 Q 120 0 120 10 L 120 60 Q 120 70 110 70 L 85 70 L 85 45 L 110 45 L 60 10 L 10 45 L 35 45 L 35 70 L 10 70 Q 0 70 0 60 L 0 10 Z" />
		      </svg>
		      <br />
		      <button class="huge" id="fileinput-button">Dateien ausw&auml;hlen</button>
		    </div><!-- filedrop-chooser -->
		    <div id="filedrop-hint"></div><!-- filedrop-hint -->
		    <form id="upload-form">
		      <input type="file" id="fileinput" multiple />
		    </form>
		  </div><!-- filedrop-inner-box -->
		</div><!-- filedrop -->
		<h2><a href="#">Uploads ^^</a></h2>
		<div id="filelist-container">
		  <ul id="filelist"></ul>
		  <button id="filelist-clear-button" style="display: none">Liste leeren</button>
		</div><!-- filelist-container -->
		<div style="display: none" id="secret-elements">
		  <svg class="mini-button" title="Abbrechen" id="stop-button" xmlns="http://www.w3.org/2000/svg" version="1.1" width="12" height="12">
		    <rect x="0" y="0" width="12" height="12" fill="#fff" />
		    <rect x="1" y="1" width="10" height="10" fill="#c00" />
		  </svg>
		  <svg class="mini-button" title="Pausieren" id="pause-button" xmlns="http://www.w3.org/2000/svg" version="1.1" width="12" height="12">
		    <rect x="0" y="0" width="12" height="12" fill="#fff" />
		    <rect x="2" y="1" width="3" height="10" fill="#00c" />
		    <rect x="7" y="1" width="3" height="10" fill="#00c" />
		  </svg>
		  <svg class="mini-button" title="Fortsetzen" id="play-button" xmlns="http://www.w3.org/2000/svg" version="1.1" width="12" height="12">
		    <rect x="0" y="0" width="12" height="12" fill="#fff" />
		    <path d="M 1 1 L 11 5.5 L 1 11 Z" fill="#0c0" />
		  </svg>
		</div>
		<div id="iframe-container"></div>
	</aside>
	
	<section id="main_container">
		<p>
		Content of <?php echo $file; ?>
		</p>
		<form method="post" action="save.php">
			<div>	
				<!-- Gets replaced with TinyMCE, remember HTML in a textarea should be encoded -->
				<div>
					<textarea id="elm1" name="elm1" rows="15" cols="80" style="width: 80%">
			                <?php	echo file_get_contents (UPLOAD_DIR.$file); ?>
					</textarea>
				</div>
		
				<!-- Some integration calls -->
				<a href="javascript:;" onclick="tinyMCE.get('elm1').show();return false;">[Show]</a>
				<a href="javascript:;" onclick="tinyMCE.get('elm1').hide();return false;">[Hide]</a>
				<a href="javascript:;" onclick="tinyMCE.get('elm1').execCommand('Bold');return false;">[Bold]</a>
				<a href="javascript:;" onclick="alert(tinyMCE.get('elm1').getContent());return false;">[Get contents]</a>
				<a href="javascript:;" onclick="alert(tinyMCE.get('elm1').selection.getContent());return false;">[Get selected HTML]</a>
				<a href="javascript:;" onclick="alert(tinyMCE.get('elm1').selection.getContent({format : 'text'}));return false;">[Get selected text]</a>
				<a href="javascript:;" onclick="alert(tinyMCE.get('elm1').selection.getNode().nodeName);return false;">[Get selected element]</a>
				<a href="javascript:;" onclick="tinyMCE.execCommand('mceInsertContent',false,'<b>Hello world!!</b>');return false;">[Insert HTML]</a>
				<a href="javascript:;" onclick="tinyMCE.execCommand('mceReplaceContent',false,'<b>{$selection}</b>');return false;">[Replace selection]</a>
		
				<br />
				<input type="hidden" name="file" value="<?php echo $file; ?>" />
				<input type="submit" name="save" value="Submit" />
				<input type="reset" name="reset" value="Reset" />
			</div>
		</form>
	</section>
</body>
</html>
