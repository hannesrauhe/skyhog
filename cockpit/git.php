<?php
/*
Copyright 2012,2013 Hannes Rauhe

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
require_once('./base.inc.php');
$CMS_update = 1;
if(!$a->isAdmin() || !array_key_exists("CMS_update", $_REQUEST) || !$_REQUEST['CMS_update']) {
	chdir($s->getPreviewDir());	
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
} else if(array_key_exists("add", $_POST)) {
	$cmd = "status";
	$arr = git::add("*");
} else if(array_key_exists("diff", $_POST)) {
	$cmd = "status";
	$arr = git::diff();
} else if(array_key_exists("commit", $_POST)) {
	$cmd = "status";
	$arr = git::commit($a->getAuthUserName()." <".$a->getAuthUserMail().">","Commit from webinterface");
} else if(array_key_exists("remote_add", $_POST)) {
	$cmd = "remote add ".escapeshellarg($_POST['remote_name'])." ".escapeshellarg($_POST['remote_url']);
} else if(array_key_exists("remote_rm", $_POST)) {
	$remote = $_POST['remote_name'];
	$cmd = "remote rm ".escapeshellarg($_POST['remote_name']);
} else {
	$cmd = "status";
}
if(empty($arr))
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
			Attention: The Skyhog Update will just update the system files. Your user files may not be compatible anymore.<br />
			<a href="setup.php">Click here after updating to check, if your installation is fine.</a>
		</p>
<?php endif; ?>
		<p>
			Output of git <?php echo $cmd ?>:<br />
			<textarea rows="10" cols="80"><?php
				foreach($arr as &$a1) {
					echo trim($a1)."\n";
				}
			?></textarea>
			<form action="git.php" method="POST">			
				<input type="hidden" name="CMS_update" value="<?php echo $CMS_update; ?>" />	
				<input type="submit" name="log" value="log"/><input type="submit" name="diff" value="diff"/><br /><br />
				<input type="submit" name="add" value="add all untracked"/>
				<input type="submit" name="commit" value="commit all"/><br /><br />
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
