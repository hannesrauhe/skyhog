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
session_start();
if(isset($_SESSION['user']) && $_SESSION['user']['active']) {
	header("Location: pages.php");	
}

$redirect = "pages.php";
if(isset($_REQUEST['redirect'])) {
	$redirect = $_REQUEST['redirect'];
}

?>
<html>
	<head>
		<title>SkyHog Cockpit</title>
		<link rel="stylesheet" type="text/css" href="style.css" media="all">
	</head>
	<body>
<?php
if(is_file("config.inc.php")):
	include_once("nav.inc.phtml");
?>	
		<section>
			<h1>SkyHog</h1>
			<?php if(array_key_exists("msg", $_REQUEST)): ?>
			<p id="msg">
				<?php echo $_REQUEST['msg'] ?>
			</p>
			<?php endif; ?>
			<div>
				<form action="<?php echo $redirect; ?>" method="post">
				    OpenID: <input type="text" name="openid_identifier" value="<?php if(isset($_COOKIE['oid'])) echo $_COOKIE['oid']; ?>" /> 
				    <input type="submit" value"Login" />
				</form>
				<p>or:</p>
			</div>								
			<div>
				<form action="<?php echo $redirect; ?>" method="post">
					<input type="hidden" name="openid_identifier" value="https://www.google.com/accounts/o8/id"/>
				    <input type="submit" value="Login with Google" />
				</form>
			</div>
		</section>
<?php
else:	
?>
		<section>
			SkyHog has not been set up yet. Please visit <a href="setup.php">this page</a> to install it.
		</section>
<?php
endif;
	
include_once("footer.inc.phtml");
?>
	</body>
</html>