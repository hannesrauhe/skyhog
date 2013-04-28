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
		Available Sites
		</h2>
		
        <table>
            <?php
            $active_site = array();
            $users = $d->getSites();
            if(!empty($users)) {
                echo "<tr>";
                foreach (array_keys($users[0]) as $key) {
                    echo "<th>$key</th>";
                }
                echo "<th>Functions</th></tr>";
                foreach ($users as $user) {
                    if($user['id']==$s->getSiteID()) {
                        $active_site = $user;
                    }
                    echo "<tr>";
                    foreach ($user as $key => $value) {
                        echo "<td>$value</td>";
                    }
                    echo "<td>
                    <a href=\"edit_site.redirect.php?action=Change&site_id=".$user['id']."\">Change</a>
                    <a href=\"edit_site.redirect.php?action=Delete&site_id=".$user['id']."\">Delete</a>
                    <a href=\"setup.php?site_id=".$user['id']."\">Initialize</a>
                    </td></tr>";
                }
            } else {
                echo "<tr><td>There are no registered sites!</td></tr>";
            }
            ?>
        </table>
        <h2>Create new site</h2>
        <form method="POST" action="edit_site.redirect.php">
            Name: <input type="text" name="name"></input>
            <input type="submit"  name='action' value='Create' />
        </form>
        <?php if($s->getSiteID()!=-1): ?>
        <h2>Change Site Properties:</h2>
        <form action="edit_site.redirect.php" method=\"POST\">    
            <table>
                <?php
                foreach ($active_site as $key=>$value) {
                        echo "<tr><td>$key</td>
                        <td><input type=\"text\" name=\"$key\" value=\"$value\" /></td></tr>";
                    }
                ?>
            </table>
        <input type="submit" name='action' value='Update' />
        </form>
        <?php endif; ?>
	</section>
	<div  style="clear: both"></div>
<?php
include_once("footer.inc.phtml");
?>
</body>
</html>
