<?php
require_once("base.inc.php");

$msg = "";

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Edit Page</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<script src="upload/jquery.min.js"></script>
<script charset="utf-8" type="text/javascript" src="upload/upload.js"></script>
<link rel="stylesheet" media="screen" type="text/css" href="upload/upload.css" />
<script type="text/javascript">
$(document).ready(function() {
	Uploader.init({
	    smart_mode: true
	});
</script>

</head>
<body role="application" class="starting">
	<p id="msg"> 
		<?php echo $msg; ?>
	</p>
	<section id="upload_container" style="float:left">
		<div id="filedrop">
		  <div id="filedrop-inner-box">
		    <div id="filedrop-chooser">
		      <svg id="upload-icon" xmlns="http://www.w3.org/2000/svg" version="1.1" width="100" height="70">
		        <linearGradient id="grd" x1="0%" y1="0%" x2="0%" y2="100%">
		          <stop offset="10%" style="stop-color: rgb(27, 132, 224);" />
		          <stop offset="90%" style="stop-color: rgb(27, 62, 94);" />
		        </linearGradient>
		        <path transform="translate(10, 10) scale(1.5, 1.5)" fill="url(#grd)" d="M 0 10 Q 0 0 10 0 L 110 0 Q 120 0 120 10 L 120 60 Q 120 70 110 70 L 85 70 L 85 45 L 110 45 L 60 10 L 10 45 L 35 45 L 35 70 L 10 70 Q 0 70 0 60 L 0 10 Z" />
		      </svg>
		      <br />
		      <button class="huge" id="fileinput-button">Dateien ausw&auml;hlen</button>
		    </div>
		    <div id="filedrop-hint"></div>
		    <form id="upload-form">
		      <input type="file" id="fileinput" multiple />
		    </form>
		  </div>
		</div>
		<h2><a href="#">Uploads ^^</a></h2>
		<div id="filelist-container">
		  <ul id="filelist"></ul>
		  <button id="filelist-clear-button" style="display: none">Liste leeren</button>
		</div>
		<div style="display: none" id="secret-elements">
		  <svg class="mini-button" title="Abbrechen" id="stop-button" xmlns="http://www.w3.org/2000/svg" version="1.1" width="12" height="12">
		    <rect x="0" y="0" width="12" height="12" fill="#fff" />
		    <rect x="1" y="1" width="10" height="10" fill="#c00" />
		  </svg>
		  <svg class="mini-button" title="Pausieren" id="pause-button" xmlns="http://www.w3.org/2000/svg" version="1.1" width="12" height="12">
		    <rect x="0" y="0" width="12" height="12" fill="#fff" />
		    <rect x="2" y="1" width="3" height="10" fill="#00c" />
		    <rect x="7" y="1" width="3" height="10" fill="#00c" />
		  </svg>
		  <svg class="mini-button" title="Fortsetzen" id="play-button" xmlns="http://www.w3.org/2000/svg" version="1.1" width="12" height="12">
		    <rect x="0" y="0" width="12" height="12" fill="#fff" />
		    <path d="M 1 1 L 11 5.5 L 1 11 Z" fill="#0c0" />
		  </svg>
		</div>
		<div id="iframe-container"></div>
	</section>	
</body>
</html>
