var generate_callback=
	function(data) {
    		$("#msg").html(data);
			$('#generate_buttons').css("border",'');
    };   
      
$(document).ready(function() { 
    $('#content_form').ajaxForm({ 
        target: '#msg' 
    }); 
    $('#b_generate_prev').click(function() {
    	$.post('generate.target.php', 
    		{"navigation_changed":"0" }, 
    		generate_callback
    	).error(function() { 		   
    		$("#msg").html("Something went wrong. See, if the maintenance script can give you a hint.");
		});
    });
    $('#b_generate').click(function() {
    	$.post('generate.target.php', 
        	{"finalize":"1",
        		"navigation_changed":"0" },
    		generate_callback
    	).error(function() { 		   
    		$("#msg").html("Something went wrong. See, if the maintenance script can give you a hint.");
		});
    });
});