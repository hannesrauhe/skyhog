<?php
require_once("base.inc.php");

$msg = "";
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
	$msg .= "Error: preview-directory cannot be opened!";
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
 
<script> 
    $(document).ready(function() { 
        $('#content_form').ajaxForm({ 
            target: '#msg' 
        }); 
        $('#b_generate_prev').click(function() {
        	$.post('generate.target.php', function(data) {
        		$("#msg").html(data);
        	});
        });
        $('#b_generate').click(function() {
        	$.post('generate.target.php', 
	        	{finalize:"1"},
	        	function(data) {
	        		$("#msg").html(data);
	        	});
        });
        $('#sorted_menu, #unsorted_menu').sortable({
			connectWith: ".connectedSortable",
			change: function(event, ui) { 
				$('input[name="navigation_changed"]').attr('value','1');
			 }
		});
    }); 
</script> 
<meta http-equiv="X-UA-Compatible" content="IE=edge" />

<script type="text/javascript" src="tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">	
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

		<link rel="stylesheet" type="text/css" href="style.css" media="all">
</head>
<body role="application" class="starting">
<?php
include_once("nav.inc.phtml");
?>
	<p id="msg"> 
		<?php echo $msg; ?>
	</p>
	<aside id="skyhog_pages" >
		<h2>Pages</h2>
		<h3>In the Navigation</h3>
		<ul id="sorted_menu" class="connectedSortable">
			<?php
		    foreach($ordered_pages as $f) {
	        	echo "<li class='ui-state-default'>
		        	<span class='ui-icon ui-icon-arrowthick-2-n-s'></span><a href='".$_SERVER['PHP_SELF']."?file=_".$f['link']."'>".$f['id']."</a>
		        	</li>";
			}
			?>
				
		</ul>
		<h3>Not</h3>
		<ul id="unsorted_menu" class="connectedSortable">
			<?php
		    foreach($pages as $f) {
	        	echo "<li class='ui-state-highlight'>
	        		<span class='ui-icon ui-icon-arrowthick-2-n-s'></span><a href='".$_SERVER['PHP_SELF']."?file=_$f'>$f</a>
	        		</li>";
			}
			?>				
		</ul>
		<button id="b_generate_prev">Generate Preview</button>
		<button id="b_generate">Generate!</button><br />
		<a href="<?php echo UPLOAD_PATH ?>" target="new" >Show Preview</a>
		<a href="<?php echo PAGE_PATH ?>" target="new" >Show Homepage</a>
	</aside>
	<section id="main_container" style="padding:10px">
		<h2>
		Content of <?php echo $file; ?>
		</h2>
		<form method="post" action="save.target.php" id="content_form">
			<div>	
				<!-- Gets replaced with TinyMCE, remember HTML in a textarea should be encoded -->
				<div>
					<textarea id="elm1" name="elm1" rows="40" cols="180" style="width: 80%">
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
				<input type="hidden" name="navigation_changed" value="0" />
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
