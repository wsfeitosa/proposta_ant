$(document).ready(function(){
	
	/** Caland�rios JQuery **/
	
	$( "#inicio" ).datepicker({ 
		minDate: 0,
		dateFormat: 'dd-mm-yy' , 
		changeYear: true , 
		changeMonth: true, 
		showOn: "button",
		buttonImage: "/Imagens/cal.gif",
		buttonImageOnly: true,
		onClose: function( selectedDate ) {
			 $( "#validade" ).datepicker( "option", "minDate", selectedDate );
		 }
	}).attr("readonly","readonly");

	$( "#validade" ).datepicker({ 												
		dateFormat: 'dd-mm-yy' , 
		changeYear: true , 
		changeMonth: true, 
		showOn: "button",
		buttonImage: "/Imagens/cal.gif",
		buttonImageOnly: true,
		onClose: function( selectedDate ) {
			 $( "#inicio" ).datepicker( "option", "maxDate", selectedDate );
		}
	}).attr("readonly","readonly");
	
	/** Quando mudar a modalidade de IMP para EXP ou vice-versa tem que zerar �s taxas **/
	$("#sentido").change(function(){
		$("#taxas_selecionadas").empty();
	});
	
	/** Faz a busca por um cliente **/
    $("#cliente").blur(function() {

        if ($(this).val() != "")
        {
            document.getElementById("frame").src = "/Clientes/propostas/index.php/clientes/clientes/find/" + $("#cliente").val() + "/find_cliente_taxas.js";

            $("#pop").show("slow");
        }

    });
    
    /** Adiciona uma taxa no acordo **/
    $("#incluir_taxa").click(function(){
    	document.getElementById("frame").src = "/Clientes/propostas/index.php/taxas_locais/taxas_locais/add/acordos_taxas_add.js";

        $("#pop").show("slow");
    });
    

    
    /** Remove um cliente selecionado **/
    $("#excluir_cliente").click(function() {

        $("#clientes_selecionados option:selected").remove();

    });
    
    /** Remove uma taxa selecionada **/
    $("#excluir_taxa").click(function() {

        $("#taxas_selecionadas option:selected").remove();

    });
    
    /** Busca �s taxas locais para preenchimento do acordo de taxas locais **/
    $("#portos_selecionados").change(function(){
    	
    	 var id = $(this).find("option").filter(":selected").val();
         var text = $(this).find("option").filter(":selected").text();
         var index = $(this).attr("selectedIndex");
    	    	    	
    	/** verifica se algum cliente j� foi selecionado **/
    	var qtd_itens = 0;

        $("#clientes_selecionados option").each(function() {
            qtd_itens++;
        });
    	
    	if( qtd_itens < 1 )
    	{
    		alert("Selecione o cliente antes de selecionar os portos!");
    		return false;
    	}
    	
    	/** Verifica se o sentido foi selecionado (IMP ou EXP) **/
    	if( $("#sentido").val() == "0" )
    	{
    		alert("Selecione o sentido!");
    		return false;
    	}	
    	
    	/** junta todos os clientes em uma string para enviar para o controller **/
    	var clientes_selecionados = "";
    	
    	$("#clientes_selecionados option").each(function(){
    		clientes_selecionados += $(this).val() + ":";
    	});
    	
    	/** Limpa o combo de taxas **/
    	$("#taxas_selecionadas").empty();
    	
    	/** Envia uma requisi��o ajax para buscar �s taxas dos portos **/
    	//INICIO
        $.ajax({
            type: "POST",
            url: "/Clientes/propostas/index.php/taxas_locais/taxas_locais/find/" + id + "/" + clientes_selecionados + "/" + $("#sentido").val(),
            beforeSend: function() {
                $("#msg").html("Aguarde Processando...");
            },
            dataType: "xml",
            success: function(xml) {
            	            	            	
                $(xml).each(function() {
                	                	                	
                	if( $(this).find("error").text() ) 
                	{
                		alert($(this).find("error").text());
                		return false;
                	}	
                	
                    $(this).find("taxa_local").each(function(){
                    	
                    	var id_taxa = $(this).find("id_taxa_adicional").text();
                    	var nome_taxa = $(this).find("taxa").text();
                    	var valor = $(this).find("valor").text();
                    	var valor_minimo = $(this).find("valor_minimo").text();
                    	var valor_maximo = $(this).find("valor_maximo").text();
                    	var id_moeda = $(this).find("id_moeda").text();
                    	var moeda = $(this).find("moeda").text();
                    	var id_unidade = $(this).find("id_unidade").text();
                    	var unidade = $(this).find("unidade").text();
                    	
                    	var value = id_taxa + ";" + nome_taxa + ";" + valor + ";" +
                    				valor_minimo + ";" + valor_maximo + ";" +
                    				id_moeda + ";" + moeda + ";" + id_unidade + ";" + unidade;
                    	
                    	var label = nome_taxa + " | " + moeda + " " + valor + " " + unidade + " | MIN " + valor_minimo + " | MAX " + valor_maximo; 
                    	
                    	 $("#taxas_selecionadas").append(new Option(label, value));
                    	
                    });

                	$("#msg").html("");

                });

            }//END SUCCESS

        });
        //FIM    	
    });
    
    $("#salvar").click(function(){
    	
    	var msg = "";
    	var erro = 0;
    	
    	if( $("#sentido").val() == "0" )
    	{
    		msg += "Selecione um sentido (IMP ou EXP)!\n";
    		erro = 1;
    	}	
    	    	
    	if( $("#clientes_selecionados option").length < 1 )
    	{
    		msg += "Selecione pelo menos um cliente antes de salvar!\n";
    		erro = 1;
    	}	
    	
    	if( $("#taxas_selecionadas option").length < 1 )
    	{
    		msg += "Selecione pelo menos uma taxa antes de salvar!\n";
    		erro = 1;
    	}
    	
    	if( erro == 1 )
    	{
    		alert(msg);
    		return false;
    	}	
    	
    	/** Seleciona todos os clientes do combo antes de submeter **/	
    	$("#clientes_selecionados option").each(function(){
    		$(this).attr("selected","selected");
    	});
    	
    	/** Seleciona todas �s taxas do comvo de taxas antes de submenter o formul�rio **/
    	$("#taxas_selecionadas option").each(function(){
    		$(this).attr("selected","selected");
    	});
    	
    	$("#nova").submit();
    	
    });
	
});//END FILE