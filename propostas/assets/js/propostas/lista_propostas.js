$(document).ready(function(){
    
    $("#voltar").click(function(){
        window.location = "/Clientes/propostas/index.php/propostas/propostas/realizar_busca";
    });
    
    $("a").click(function(){
        
        var id_proposta = $(this).attr("id_proposta");
        var tipo_proposta = $(this).attr("tipo_proposta");
                              
        if( $(this).attr("id_proposta") == undefined )
        {            
            return false;
        }    
        else
        {
            window.location = "/Clientes/propostas/index.php/propostas/propostas/consultar/" + id_proposta + "/" + tipo_proposta;            
        }    
        
    });
    
});