<html>
	<head>
		<title>SkyHog</title>
		<link rel="stylesheet" type="text/css" href="style.css" media="all">
	</head>
	<body>
<?php
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
				<form action="pages.php" method="post">
				    <button>Login with Google</button>
				</form>
			</div>
			<div>
				<form action="pages.php" method="post">
				    OpenID: <input type="text" name="openid_identifier" style="float:left;"/> <button>Submit</button>
				</form>
			</div>	
		</section>
<?php
include_once("footer.inc.phtml");
?>
	</body>
</html>