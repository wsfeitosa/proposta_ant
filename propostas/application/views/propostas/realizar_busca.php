<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center" class="tabela_azul">
	<tr>
		<td>                    
			<br>				
			<?php echo form_open("index.php/propostas/propostas/listar_resultados_busca",Array("id" => "realizar_busca", "name" => "realizar_busca")); ?>
			<table cellpadding="1" cellspacing="1" width="97%" border="0" align="center" class="tabela_padrao">
				<div id="pop">
					<a href="#" onclick="document.getElementById('pop').style.display='none';">[Fechar]</a>											
					<iframe id="frame" name="frame" frameborder="0" width="100%" height="95%" src="#"></iframe>
				</div>                         
				<tr>
					<td colspan="1" id="label_tipop" width="33%">
						Selecione o Tipo de Proposta:
					</td>
					<td colspan="1" id="label_sentido" width="33%">
						Sentido:
					</td>
					<td colspan="1" id="label_dado_busca" width="33%">
						
					</td>										                                                   
				</tr>					
				<tr>
					<td class="texto_pb" colspan="1">							
						<?php echo form_dropdown("tipo_consulta",$tipos_consultas,"","id='tipo_consulta'");?>							
					</td>
					<td class="texto_pb" colspan="1">							
						<?php echo form_dropdown("sentido",$sentidos,"","id='sentido'");?>							
					</td>
					<td class="texto_pb" colspan="1">							
						<?php echo form_input(Array("name" => "dado_para_busca", "id" => "dado_para_busca")); ?>				
					</td>																      
				</tr>									                                                                                     
			</table>                        
		</td>
	</tr>
	<tr>
		<td bgcolor="#FFFFFF">&nbsp;</td>
	</tr>			    
</table>
