<?php
//pr($proposta);
?>
<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center" class="tabela_azul">
    <tr>
        <td>                    
            <br>				
            <?php echo form_open("", Array("id" => "form_consulta", "name" => "form_consulta")); ?>
            <input type="hidden" name="sentido" id="sentido" value="<?php echo $sentido; ?>" />	
            <input type="hidden" name="id_proposta" id="id_proposta" value="<?php echo $proposta->getId(); ?>" />
            <input type="hidden" name="tipo_proposta" id="tipo_proposta" value="<?php echo get_class($proposta); ?>" />		
            <table cellpadding="1" cellspacing="1" width="97%" border="0" align="center" class="tabela_padrao">
                <div id="pop">
                    <a href="#" onclick="document.getElementById('pop').style.display = 'none';">[Fechar]</a>											
                    <iframe id="frame" name="frame" frameborder="0" width="100%" height="95%" src="#"></iframe>
                </div>       
                <div id="msg"></div>               
                <tr>
                    <td colspan="4" id="label_tipop">
                        Proposta Número:
                    </td>                    															                                                   
                </tr>
                <tr>
                    <td colspan="4" class="texto_pb">
                        <?php echo $proposta->getNumero(); ?>
                    </td>                    															                                                   
                </tr>
                <tr>
                    <td colspan="4" id="label_tipop">
                        Clientes:
                    </td>                    															                                                   
                </tr>					
                <tr>
                    <td class="texto_pb" colspan="4">							
                        <?php foreach ($proposta->getClientes() as $cliente): ?>
                        <?php   echo $cliente->getCnpj() . " - ". $cliente->getRazao() . " -> " . $cliente->getCidade()->getNome() . "-" . $cliente->getEstado() . "<br />"; ?>
                        <?php endforeach; ?>
                    </td>                    																					      
                </tr>
                
                <tr>
                    <td colspan="2">
                        Contatos Emails Para:
                    </td>
                    <td colspan="2">
                        Contatos Emails CC:
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="texto_pb">
                        <?php
                        foreach ($proposta->obterEmails() as $email) 
                        {
                            if( $email->getTipo() == "P" )
                            {    
                                echo $email->getEmail()."<br />";                      
                            }
                        }
                        ?>
                    </td>
                    <td colspan="2" class="texto_pb">
                        <?php
                        foreach ($proposta->obterEmails() as $email) 
                        {
                            if( $email->getTipo() == "C" )
                            {    
                                echo $email->getEmail()."<br />";                      
                            }
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" class="titulo_tabela">
                        Itens Da Proposta                        
                    </td>
                </tr>	
                </table>
                <div id="accordion" style="padding:10px; width:99%;">
                    <?php foreach($proposta->getItens() as $item): ?>                                   
                    <h2><a href="#"><?php echo $item->getNumero(); ?></a></h2>
                    <div>
                        <p>
                        <table cellpadding="1" cellspacing="1" width="97%" border="0" align="center" class="tabela_padrao">
                            <tr>
                                <td width="25%">Mercadoria</td>
                                <td width="25%">PP</td>
                                <td width="25%">CC</td>
                                <td width="25%">Validade</td>                                
                            </tr>
                            <tr>
                                <td class="texto_pb"><?php echo utf8_decode($item->getMercadoria()); ?></td>
                                <td class="texto_pb"><?php echo $item->getPp() == TRUE ? "SIM" : "NÃO"; ?></td>
                                <td class="texto_pb"><?php echo $item->getCc() == TRUE ? "SIM" : "NÃO"; ?></td>
                                <td class="texto_pb"><?php echo $item->getValidade()->format("d/m/Y"); ?></td>                                
                            </tr>
                            <tr>
                                <td>Origem:</td>
                                <td>Embarque:</td>
                                <td>Desembarque:</td>
                                <td>Destino:</td>
                            </tr>
                            <tr>
                                <td class="texto_pb"><?php echo $item->getTarifario()->getRota()->getPortoOrigem()->getNome(); ?></td>
                                <td class="texto_pb"><?php echo $item->getTarifario()->getRota()->getPortoEmbarque()->getNome(); ?></td>
                                <td class="texto_pb"><?php echo $item->getTarifario()->getRota()->getPortoDesembarque()->getNome(); ?></td>
                                <td class="texto_pb"><?php echo $item->getTarifario()->getRota()->getPortoFinal()->getNome(); ?></td>
                            </tr>
                            <tr>
                                <td width="25%">Peso</td>
                                <td width="25%">Cubagem</td>
                                <td width="25%">Volumes</td>
                                <td width="25%">&nbsp;</td>                                
                            </tr>
                            <tr>
                                <td class="texto_pb"><?php echo $item->getPeso(); ?></td>
                                <td class="texto_pb"><?php echo $item->getCubagem(); ?></td>
                                <td class="texto_pb"><?php echo $item->getVolumes(); ?></td>
                                <td class="texto_pb">&nbsp;</td>                                
                            </tr>
                            <tr>
                                <td colspan="2" width="50%">Frete e Adicionais</td>
                                <td colspan="2" width="50%">Taxas Locais</td>
                            </tr>                            
                            <tr>                            	
                                <td class="texto_pb" colspan="2">
                                <?php
                                	foreach($item->getTarifario()->getTaxa() as $taxa)
                                	{
                                		if( $taxa instanceof Taxa_Adicional )
                                		{                                		
                                			echo utf8_decode($taxa->getNome())." | " . $taxa->getMoeda()->getSigla() . " ". number_format($taxa->getValor(),2) . " " .
                                				 $taxa->getUnidade()->getUnidade() . " | " . number_format($taxa->getValorMinimo(),2) . " | " . 
                                				 number_format($taxa->getValorMaximo(),2) . "<br />";
                                		}	
                                	}	
                                ?>
                                </td>
                                <td class="texto_pb" colspan="2">
                                <?php
                                	foreach($item->getTarifario()->getTaxa() as $taxa)
                                	{
                                		if( $taxa instanceof Taxa_Local )
                                		{                                		
                                			echo utf8_decode($taxa->getNome())." | " . $taxa->getMoeda()->getSigla() . " ". number_format($taxa->getValor(),2) . " " .
                                				 $taxa->getUnidade()->getUnidade() . " | " . number_format($taxa->getValorMinimo(),2) . " | " . 
                                				 number_format($taxa->getValorMaximo(),2) . "<br />";
                                		}	
                                	}	
                                ?>
                                </td>                                
                            </tr>
                            <tr>
                                <td colspan="2" width="50%">Observações Internas</td>
                                <td colspan="2" width="50%">Observações Cliente</td>
                            </tr>
                            <tr>
                                <td class="texto_pb" colspan="2"><?php echo nl2br(utf8_decode(urldecode($item->getObservacaoInterna()))); ?></td>
                                <td class="texto_pb" colspan="2"><?php echo nl2br(utf8_decode(urldecode($item->getObservacaoCliente()))); ?></td>
                            </tr>
                        </table>
                        </p>
                    </div>
                    <?php endforeach; ?>                    
                </div>                                       
            </td>
        </tr>
        <tr>
            <td bgcolor="#FFFFFF">&nbsp;</td>
        </tr>			    
    </table>

