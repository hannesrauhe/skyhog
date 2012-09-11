<?php
require_once("base.inc.php");

$msg = "";
$file = "__template.html";
if(!is_file(UPLOAD_DIR.$file)) {
	$msg = "File $file does not exist - Run setup script!";
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
    }); 
</script> 
<meta http-equiv="X-UA-Compatible" content="IE=edge" />


<link rel="stylesheet" type="text/css" href="style.css" media="all">
</head>
<body role="application" class="starting">
<?php
include_once("nav.inc.phtml");
?>
	<p id="msg"> 
		<?php echo $msg; ?>
	</p>
	<section id="main_container" style="padding:10px">
		<h2>
		Template
		</h2>
		<form method="post" action="save.target.php" id="content_form">
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
