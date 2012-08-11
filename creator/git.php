<?php
require_once('./base.inc.php');
$CMS_update = 1;
if(!$a->isAdmin() || !array_key_exists("CMS_update", $_REQUEST) || !$_REQUEST['CMS_update']) {
	chdir(UPLOAD_DIR);	
	$CMS_update = 0;
}

$cmd = '';
$arr = array();
$remotes = array();
$branches = array();
$remote = '';

if(array_key_exists("push", $_POST)) {
	$cmd = "push ".escapeshellarg($_POST['remote'])." ".escapeshellarg($_POST['branch']);
} else if(array_key_exists("pull", $_POST)) {
	$cmd = "pull ".escapeshellarg($_POST['remote'])." ".escapeshellarg($_POST['branch']);
} else if(array_key_exists("log", $_POST)) {
	$cmd = "log";
} else if(array_key_exists("remote_add", $_POST)) {
	$cmd = "remote add ".escapeshellarg($_POST['remote_name'])." ".escapeshellarg($_POST['remote_url']);
} else if(array_key_exists("remote_rm", $_POST)) {
	$remote = $_POST['remote_name'];
	$cmd = "remote rm ".escapeshellarg($_POST['remote_name']);
} else {
	$cmd = "status";
}
exec ( GIT_CMD." $cmd 2>&1", $arr);
exec ( GIT_CMD." remote", $remotes);
exec ( GIT_CMD." branch", $branches);

?>

<html>
	<head>		
		<link rel="stylesheet" type="text/css" href="style.css" media="all">
	</head>
	<body>
<?php
include_once("nav.inc.phtml");
if($CMS_update):
?>
		<p id="update_warning">
			Attention: The Skyhog Update will just update the system files. Your user files may not be compatible anymore.
		</p>
<?php endif; ?>
		<p>
			Output of git <?php echo $cmd ?>:<br />
			<textarea rows="10" cols="80"><?php
				foreach($arr as &$a) {
					echo trim($a)."\n";
				}
			?></textarea>
			<form action="git.php" method="POST">			
				<input type="hidden" name="CMS_update" value="<?php echo $CMS_update; ?>" />	
				<input type="submit" name="log" value="log"/><br /><br />
				<select name="remote" size="<?php echo count($remotes);?>">
					<?php
						foreach($remotes as &$r) {
							echo "<option>$r</option>\n";
						}
					?>
			 	</select>			 	
				<select name="branch" size="<?php echo count($branches);?>">
					<?php
						foreach($branches as &$b) {
							if(substr($b,0,1)=="*") {
								echo "<option selected=\"selected\">".substr($b,2)."</option>\n";
							}
							echo "<option>$b</option>\n";
						}
					?>
			 	</select><br />
				<input type="submit" name="push" value="push"/>
				<input type="submit" name="pull" value="pull"/>				
				<br /><br />
				<input type="text" name="remote_name" value="<?php echo $remote ?>"/> 
				<input type="text" name="remote_url"/> 
				<input type="submit" name="remote_add" value="Add Remote"/> 
				<input type="submit" name="remote_rm" value="Remove Remote"/>
			</form>
		</p>
		
<?php
include_once("footer.inc.phtml");
?>
	</body>
</html>
