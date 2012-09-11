//author Hannes Rauhe


$(document).ready(function() { 
    $('#content_form').ajaxForm({ 
        target: '#msg' 
    }); 
    $('#b_generate_prev').click(function() {
    	$.post('generate.target.php', function(data) {
    		$("#msg").html(data);
    	});
    });
    $('#b_generate').click(function() {
    	$.post('generate.target.php', 
        	{finalize:"1"},
        	function(data) {
        		$("#msg").html(data);
        	});
    });
    $('#sorted_menu, #unsorted_menu').sortable({
		connectWith: ".connectedSortable",
		change: function(event, ui) { 
			$('#nav_order').html('');
			nav_entry=$('#sorted_menu').first();
			input_el = $('#nav_order').append(
				$('<input type="text" name="order" value="" />').attr(
					"value",
					nav_entry.html())
				);
			$('input[name="navigation_changed"]').attr('value','1');
		 }
	});
}); 