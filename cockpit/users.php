<?php
/*
Copyright 2012 Hannes Rauhe

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

if($a->isAdmin()) {
	if(array_key_exists("action", $_POST)) {
		if(!empty($_POST['user_id'])) {
			switch($_POST['action']) {
				case 'Delete':
					$d->deleteUser($_POST['user_id']);
					$msg = 'User with ID '.$_POST['user_id'].' deleted!';
					break;
				case 'Activate':
					$d->activateUser($_POST['user_id']);
					$msg = 'User with ID '.$_POST['user_id'].' activated!';
					break;
			}
		}
	}
} else {
	$msg = "You don't have admin permissions!";
}
?>

<html>
	<head>		
		<link rel="stylesheet" type="text/css" href="style.css" media="all">
	</head>
	<body>
<?php
include_once("nav.inc.phtml");
if(!empty($msg)) {
	echo "<p id=\"msg\">$msg</p>";
}
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
					<form action=\"users.php\" method=\"POST\">
					<input type='hidden' name='user_id' value='".$user['user_id']."' />
					<input type=\"submit\" name='action' value='Delete' />
					<input type=\"submit\" name='action' value='Activate' />
					<input type=\"submit\" name='action' value='Promote' />
					</form>
					</td></tr>";
				}
			} else {
				echo "<tr><td>There are no registered users! Run in maintenance mode!</td></tr>";
			}
			?>
		</table>	
<?php
include_once("footer.inc.phtml");
?>	
	</body>
</html>
