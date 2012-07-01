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
<form method="post" action="show.php">
        <p>     
                <textarea name="content" cols="50" rows="15">This is some content that will be editable with TinyMCE.</textarea>
                <input type="submit" value="Save" />
        </p>
</form>

</body>
</html>