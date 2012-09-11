//author Hannes Rauhe

var navigation_order = new Array();
var navigation_changed = 0;

var generate_callback=
	function(data) {
    		$("#msg").html(data);
    		$('#sorted_menu li').removeClass("ui-state-highlight");
    		$('#sorted_menu li').addClass("ui-state-default");
    		$('#unsorted_menu li').addClass("ui-state-highlight");
    		$('#unsorted_menu li').removeClass("ui-state-default");
			$('#generate_buttons').css("border",'');
    };
	        
$(document).ready(function() { 
    $('#content_form').ajaxForm({ 
        target: '#msg' 
    }); 
    $('#b_generate_prev').click(function() {
    	$.post('generate.target.php', 
    		{"navigation_changed": navigation_changed ? navigation_order : navigation_changed } ,//$('input[name="navigation_changed"]').attr('value')} , 
    		generate_callback
    	).error(function() { 		   
    		$("#msg").html("Something went wrong. See, if the maintenance script can give you a hint.");
		});
    });
    $('#b_generate').click(function() {
    	$.post('generate.target.php', 
        	{"finalize":"1",
        		"navigation_changed": navigation_changed ? navigation_order : navigation_changed } ,//$('input[name="navigation_changed"]').attr('value')} ,
    		generate_callback
    	).error(function() { 		   
    		$("#msg").html("Something went wrong. See, if the maintenance script can give you a hint.");
		});
    });
    $('#sorted_menu, #unsorted_menu').sortable({
		connectWith: ".connectedSortable",
		placeholder: "ui-state-highlight",
		dropOnEmpty: true,
		stop: function(event, ui) { 
			navigation_changed=1;
			$('#sorted_menu li').each(function(index) {
				navigation_order[index] = $.trim($(this).text());
			});
			$('#generate_buttons').css("border","2px solid red");
			//just debugging - and alternative later...
			$('input[name="navigation_changed"]').attr('value',JSON.stringify(navigation_order));
		 }
	});
	$('#sorted_menu, #unsorted_menu').disableSelection();
}); 