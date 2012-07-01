<?php
$directory="../";
$msg = "";
if(array_key_exists("file",$_GET)) {
	$file = basename($_GET['file']);
	if(!is_file($directory.$file)) {
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
<!-- TinyMCE -->
<script type="text/javascript" src="tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
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
		content_css : "../index.css",

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
</script>
<!-- /TinyMCE -->

</head>
<body role="application">
	<p id="msg"> 
		<?php echo $msg; ?>
	</p>
	<p>
	Content of <?php echo $file; ?>
	</p>
	<form method="post" action="save.php">
		<div>	
			<!-- Gets replaced with TinyMCE, remember HTML in a textarea should be encoded -->
			<div>
				<textarea id="elm1" name="elm1" rows="15" cols="80" style="width: 80%">
		                <?php	echo file_get_contents ($directory.$file); ?>
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

</body>
</html>
