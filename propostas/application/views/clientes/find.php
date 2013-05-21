<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center" class="tabela_azul">
	<tr>
		<td>                    
			<br>				
			<?php echo form_open("index.php/propostas/propostas/nova_proposta",Array("id" => "nova", "name" => "nova")); ?>			
			<table cellpadding="1" cellspacing="1" width="97%" border="0" align="center" class="tabela_padrao">
				<div id="pop">
					<a href="#" onclick="document.getElementById('pop').style.display='none';">[Fechar]</a>											
					<iframe id="frame" name="frame" frameborder="0" width="100%" height="95%" src="#"></iframe>
				</div>                         
				<tr>
					<td class="titulo_tabela">
						CNPJ:
					</td>
					<td class="titulo_tabela">
						Razão:
					</td>
					<td  class="titulo_tabela">
						Cidade:
					</td>
					<td class="titulo_tabela">
						Selecionar:
						<?php echo form_checkbox(Array('id' => 'selecionar_todos'))?>
					</td>															                                                   
				</tr>
				<?php foreach($clientes as $cliente): ?>				
				<tr>
					<td align="center" class="texto_pb">							
						<?php echo $cliente->getCnpj();?>							
					</td>
					<td align="center" class="texto_pb">							
						<?php echo $cliente->getRazao();?>												
					</td>
					<td align="center" class="texto_pb">							
						<?php echo $cliente->getCidade()->getNome();?>							
					</td>
					<td align="center" class="texto_pb">							
						<?php echo form_checkbox(Array("name" => "selecionado", "id" => $cliente->getId(), "value" => $cliente->getCnpj()."-".$cliente->getRazao(), "checked" => FALSE ));?>												
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
