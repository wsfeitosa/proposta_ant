$(document).ready(function(){
	
	$("#selecionar_todos").click(function(){
		
		if( $(this).is(":checked") )
		{
			
			$("input:checkbox").each(function(){
				
				$(this).attr("checked","checked");
				
			});			
			
		}
		else		
		{
			
			$("input:checkbox").each(function(){
				
				$(this).attr("checked",false);
				
			});
									
		}	
		
	});
	
	$("#adicionar").click(function(){
		
		var clientes = "";
		
		$("input:checked").each(function(){
			
			if( $(this).attr("id") != "selecionar_todos" )
			{
				$("#contatos_para_selecionados", window.parent.document).append(new Option($(this).attr("value"), $(this).attr("id")));
				clientes +=  $(this).attr("id") + ":"; 
			}
			
		});
		
		$("#cliente").val("");
								
		$("#pop",window.parent.document).hide();
		
	});//END FUNCTION
	
});//END FILE