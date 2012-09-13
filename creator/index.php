<?php
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
	$a = 0;
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
				    OpenID: <input type="text" name="openid_identifier" value="<?php echo $_COOKIE['oid']; ?>" /> 
				    <input type="submit" value"Login" />
				</form>
				<p>or:</p>
			</div>								
			<div>
				<form action="<?php echo $redirect; ?>" method="post">
					<input type="hidden" name="openid_identifier" value="'https://www.google.com/accounts/o8/id'"/>
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