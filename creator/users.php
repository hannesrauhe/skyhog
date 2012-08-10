<?php
require_once('./base.inc.php');

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
?>

<html>
	<head>		
		<link rel="stylesheet" type="text/css" href="style.css" media="all">
	</head>
	<body>
<?php
include_once("nav.inc.php");
?>
		<p>
			var_dump($d->getUsers());
		</p>		
	</body>
</html>
