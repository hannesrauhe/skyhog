<html>
<head>
<!-- Load Queue widget CSS and jQuery -->
<style type="text/css">@import url(plupload/js/jquery.ui.plupload/css/jquery.ui.plupload.css);</style>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/jquery-ui.min.js"></script>
<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/themes/base/jquery-ui.css" type="text/css">

<!-- Load plupload and all it's runtimes and finally the jQuery UI queue widget -->
<script type="text/javascript" src="plupload/js/plupload.js"></script>
<script type="text/javascript" src="plupload/js/plupload.html5.js"></script>
<script type="text/javascript" src="plupload/js/plupload.gears.js"></script>
<script type="text/javascript" src="plupload/js/plupload.flash.js"></script>
<script type="text/javascript" src="plupload/js/jquery.ui.plupload/jquery.ui.plupload.js"></script>

<script type="text/javascript">
// Convert divs to queue widgets when the DOM is ready
$(document).ready(function() {
	$("#uploader").plupload({
		// General settings
		runtimes : 'gears,html5,flash',
		url : 'upload.target.php',
		max_file_size : '10mb',
		//chunk_size : '1mb',
		//unique_names : true,

		// Specify what files to browse for
		filters : [
			{title : "Image files", extensions : "jpg,gif,png"},
			{title : "Zip files", extensions : "zip"}
		],

		// Flash settings
		flash_swf_url : 'plupload/js/plupload.flash.swf',
	});

	// Client side form validation
	$('form').submit(function(e) {
        var uploader = $('#uploader').plupload('getUploader');

        // Files in queue upload them first
        if (uploader.files.length > 0) {
            // When all files are uploaded submit form
            uploader.bind('StateChanged', function() {
                if (uploader.files.length === (uploader.total.uploaded + uploader.total.failed)) {
                    $('form')[0].submit();
                }
            });
                
            uploader.start();
        } else
            alert('You must at least upload one file.');

        return false;
    });
});
</script>
</head>
<body>
<form>
	<div id="uploader">
		<p>You browser doesn't have HTML5, Gears or Flash support.</p>
	</div>
</form>
</body>
</html>