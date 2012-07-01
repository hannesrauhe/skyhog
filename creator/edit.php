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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
<head>
<title>TinyMCE Test</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8"/>

<script type="text/javascript" src="tinymce/jscripts/tiny_mce/tiny_mce.js"></script>

<script type="text/javascript">
tinyMCE.init({
        mode : "textareas"
});
</script>
</head>
<body>
	<p id="msg"> 
		<?php echo $msg; ?>
	</p>
	<p>
	Content of <?php echo $file; ?>
	</p>
	<form method="post" action="save.php">
	        <p>     
	                <textarea name="content" cols="50" rows="15">
	                <?php	echo file_get_contents ($directory.$file); ?>
					</textarea>
					<input type="hidden" name="file" value="<?php echo $file; ?>" />
	                <input type="submit" value="Save" />
	        </p>
	</form>

</body>
</html>