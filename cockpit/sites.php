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
require_once("base.inc.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Change Site</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta charset="UTF-8" />


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
		Change Site
		</h2>
		
        <table>
            <?php
            $users = $d->getSites();
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
                    <form action=\"sites.php\" method=\"POST\">
                    <input type='hidden' name='site_id' value='".$user['id']."' />
                    <input type=\"submit\" name='action' value='Change' />
                    <input type=\"submit\" name='action' value='Delete' />
                    </form>
                    </td></tr>";
                }
            } else {
                echo "<tr><td>There are no registered users! Run in maintenance mode!</td></tr>";
            }
            ?>
        </table>    
	</section>
	<div  style="clear: both"></div>
<?php
include_once("footer.inc.phtml");
?>
</body>
</html>
