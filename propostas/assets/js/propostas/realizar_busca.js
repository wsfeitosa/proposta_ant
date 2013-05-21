$(document).ready(function(){
	
	$("#tipo_consulta").change(function(){
        
        switch($(this).val())
        {
            case "numero":
                $("#label_dado_busca").html("Número:");
            break;
        
            case "cliente":
                $("#label_dado_busca").html("Cliente:");
            break;
            
            case "origem":
                $("#label_dado_busca").html("Origem:");
            break;
            
            case "destino":
                $("#label_dado_busca").html("Destino:");
            break;
            
            case "periodo":
                $("#label_dado_busca").html("Período:");
            break;
            
            default:
                alert($(this).val());
            
        }
        
    });
    
    $("#novo").click(function(){
        window.location = "/Clientes/propostas/index.php";
    });
    
    $("#localizar").click(function(){
        
        var erro = 0;
        var msg = "";
        
        if( $("#tipo_consulta").val() == "0" )
        {
            erro = 1;
            msg += "Selecione um tipo de busca!\n";
        }    
        
        if( $("#sentido").val() == "0" )
        {
            erro = 1;
            msg += "Selecione o sentido da busca\n";
        }    
        
        if( $("#dado_para_busca").val() == "" )
        {
            erro = 1;
            msg += "Informe o " + $("#tipo_consulta option:selected").text() + " para realizar a busca!\n";
        }    
        
        if( erro == 1 )
        {
            alert(msg);
            return false;
        }
        else
        {
            $("form").submit();
        }
        
    });    
	
});//END FILE