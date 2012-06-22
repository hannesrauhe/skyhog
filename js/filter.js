$(document).ready(function(){

$('#filter > li').click(function(){
	$(this).toggleClass();
});

$('#o-toggle').click(function(){
	$('img.orange').parent().toggle();
});

$('#g-toggle').click(function(){
	$('img.green').parent().toggle();
});

$('#b-toggle').click(function(){
	$('img.blue').parent().toggle();
});

});