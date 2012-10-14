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
    	beforeSerialize: function($form, options) { 
		    tinyMCE.get("elm1").save();              
		},
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
	$('#sh_plain_options').hide();
	
	$('#sh_icons').show();
	$('#sh_new_file_form').hide();
	$('#sh_add_page').click(function() {
		$('#sh_icons').hide();
		$('#sh_new_file_form').show();
		$('#sh_new_file_name').focus();
		return false;
    });
    
	$('.sh_page_settings').click(function() {
		$( "#sh_page_settings_dialog" ).dialog({
			height: 200,
			modal: true
		});
		var obj = jQuery.parseJSON($(this).text());
		$("#sh_page_id_old").attr("value",obj.id);
		$("#sh_page_id").attr("value",obj.id);		
		$("#sh_page_name").attr("value",obj.name);
		$("#sh_page_link").attr("value",obj.link);
		return false;
    });
}); 