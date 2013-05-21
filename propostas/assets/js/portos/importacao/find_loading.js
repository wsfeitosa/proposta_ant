$(document).ready(function(){
	
	$("input:checkbox").click(function(){
		
		$("#un_embarque", window.parent.document).val($(this).attr("id"));
		$("#embarque", window.parent.document).val($(this).val());
		
		$("#pop",window.parent.document).hide("slow");
		
	});
	
	
	
});//END FILE