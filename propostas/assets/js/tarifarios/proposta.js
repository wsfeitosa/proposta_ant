$(document).ready(function(){
	
	/** Envia a requisição a pagina para carrega o tariário **/	
	$("input:checkbox").click(function(){
		
		/** obtem e formata os ids dos clientes **/
		var clientes = "";
		
		$("#clientes_selecionados option", window.parent.document).each(function(){
			
			clientes += $(this).val() + ":";
			
		});
		
		$.ajax({
            type: "POST",            
            url:  "/Clientes/propostas/index.php/tarifarios/tarifarios/fill/" + $(this).attr('id') + "/" + $(this).val() + "/" + clientes + "/" + $(this).attr('id_item'),            
            beforeSend: function(){                     
                $("#msg").html("Aguarde Processando...");
            },
            dataType: "xml",
            success: function(xml){        
                
                $(xml).find("tarifario").each(function(){
                	
                	var id_tarifario = $(this).find("id_tarifario").text();
                	var origem = $(this).find("data_inicio").text();
                	var validade = $(this).find("validade").text();
                	var sentido = $(this).find("sentido").text();
                	var observacao_tarifario = $(this).find("observacao").text();
                	var rotas = $(this).find("rota");
                	var taxas_adicionais = $(this).find("taxas_adicionais");
                	var taxas_locais = $(this).find("taxas_locais");
                	                	                	                	
                	/** Carrega os portos do tarifario **/
                	$("#origem", window.parent.document).val($(rotas).find("origem").find("nome").text());
                	$("#un_origem", window.parent.document).val($(rotas).find("origem").find("uncode").text());
                	
                	$("#embarque", window.parent.document).val($(rotas).find("embarque").find("nome").text());
                	$("#un_embarque", window.parent.document).val($(rotas).find("embarque").find("uncode").text());
                	
                	$("#desembarque", window.parent.document).val($(rotas).find("desembarque").find("nome").text());
                	$("#un_desembarque", window.parent.document).val($(rotas).find("desembarque").find("uncode").text());
                	
                	$("#destino", window.parent.document).val($(rotas).find("destino").find("nome").text());
                	$("#un_destino", window.parent.document).val($(rotas).find("destino").find("uncode").text());
                	
                	/** Carrega às observações do tariário **/
                	//$("#observacao_cliente", window.parent.document).val(observacao_tarifario);
                	
                	/** Zera o frete e os adicionais **/
                	$("#frete_adicionais", window.parent.document).empty();
                	
                	/** carrega o frete e taxas adicionais **/
                	$(taxas_adicionais).find("taxa").each(function(){
                		
                		label =  $(this).find("nome").text() + " | " + 
                				 $(this).find("moeda").text() + " " +
                				 $(this).find("valor").text() + " " +
                				 $(this).find("unidade").text() + " | " +
                				 $(this).find("valor_minimo").text() + " | " + 
                				 $(this).find("valor_maximo").text();
                		
                		value =  $(this).find("id_taxa").text() + ";" +
                				 $(this).find("id_moeda").text() + ";" +
                				 $(this).find("id_unidade").text() + ";" +
                				 $(this).find("valor").text() + ";" +
                				 $(this).find("valor_minimo").text() + ";" +
                				 $(this).find("valor_maximo").text();
                		
                		$("#frete_adicionais", window.parent.document).append(new Option(label, value));
                		
                	});
                	
                	/** Zera as taxas locais **/
                	$("#taxas_locais", window.parent.document).empty();
                	
                	/** carrega o frete e taxas locais **/
                	$(taxas_locais).find("taxa").each(function(){
                		
                		label =  $(this).find("nome").text() + " | " + 
                				 $(this).find("moeda").text() + " " +
                				 $(this).find("valor").text() + " " +
                				 $(this).find("unidade").text() + " | " +
                				 $(this).find("valor_minimo").text() + " | " + 
                				 $(this).find("valor_maximo").text();
                		
                		value =  $(this).find("id_taxa").text() + ";" +
                				 $(this).find("id_moeda").text() + ";" +
                				 $(this).find("id_unidade").text() + ";" +
                				 $(this).find("valor").text() + ";" +
                				 $(this).find("valor_minimo").text() + ";" +
                				 $(this).find("valor_maximo").text();
                		
                		$("#taxas_locais", window.parent.document).append(new Option(label, value));
                		
                	});
                	                	        	
                	$("#id_tarifario", window.parent.document).val(id_tarifario);
                	        	
                });
                
                $("#pop",window.parent.document).hide("slow");
                
            }//END SUCCESS
            
		});
		
	});//END 
	
});