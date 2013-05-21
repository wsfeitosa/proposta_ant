<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center" class="tabela_azul">
	<tr>
		<td>                    
			<br>				
			<?php echo form_open("index.php/propostas/propostas/realizar_busca",Array("id" => "listar", "name" => "listar")); ?>			
			<table cellpadding="1" cellspacing="1" width="97%" border="0" align="center" class="tabela_padrao">
				<div id="pop">
					<a href="#" onclick="document.getElementById('pop').style.display='none';">[Fechar]</a>											
					<iframe id="frame" name="frame" frameborder="0" width="100%" height="95%" src="#"></iframe>
				</div>                         
				<tr>
					<td class="titulo_tabela">
						Número:
					</td>
					<td class="titulo_tabela">
						Cliente(s):
					</td>
					<td  class="titulo_tabela">
						Sentido:
					</td>
					<td class="titulo_tabela">
						Tipo De Proposta:
					</td>															                                                   
				</tr>
				<?php foreach($propostas as $proposta): ?>				
				<tr>
					<td align="center" class="texto_pb">
                        <a href="#" id_proposta="<?php echo $proposta->getId();?>" tipo_proposta="<?php echo $proposta->getTipoProposta();?>" >
						<?php echo $proposta->getNumero();?>
                        </a>    
					</td>
					<td align="center" class="texto_pb">							
						<?php                            
                            foreach ($proposta->getClientes() as $cliente ):
                                echo $cliente->getCnpj(). " - " .$cliente->getRazao()."<br />";
                            endforeach;
                        ?>
					</td>
					<td align="center" class="texto_pb">							
						<?php echo $proposta->getSentido();?>							
					</td>
					<td align="center" class="texto_pb">							
						<?php echo ucwords(str_replace("_"," ",$proposta->getTipoProposta())); ?>												
					</td>																					      
				</tr>
				<?php endforeach; ?>					                                                                                     
			</table>                        
		</td>
	</tr>
	<tr>
		<td bgcolor="#FFFFFF">&nbsp;</td>
	</tr>			    
</table>
