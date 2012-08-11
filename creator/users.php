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
		<table>
			<?php
			$users = $d->getUsers();
			if(!empty($users)) {
				echo "<tr>";
				foreach (array_keys($users[0]) as $key) {
					echo "<th>$key</th>";
				}
				echo "<th>Functions</th></tr>";
				foreach ($users as $user) {
					echo "<tr>";
					foreach ($user as $key => $value) {
						echo "<td>$value</td>";
					}
					echo "<td>
					<button name='delete' value='".$user['user_id']."' >Delete</button>
					<button name='activate' value='".$user['user_id']."' >Activate</button>
					<button name='admin' value='".$user['user_id']."' >Promote</button>
					</td></tr>";
				}
			} else {
				echo "<tr><td>There are no registered users! Run in maintenance mode!</td></tr>";
			}
			?>
		</table>		
	</body>
</html>
