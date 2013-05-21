<?php
class Test_Acordos_Taxas_Facade extends CI_TestCase{
	
	public function setUp()
	{
		parent::setUp();
		$this->CI->load->model("Taxas_Locais_Acordadas/acordos_taxas_facade");		
		$this->CI->load->model("Taxas_Locais_Acordadas/acordo_taxas_locais_model");
		$this->CI->load->model("Taxas_Locais_Acordadas/acordo_taxas_entity");
		$_SESSION['matriz'][1] = "SP";
	}
	
	public function tearDown()
	{
		parent::tearDown();
		unset($_SESSION['matriz']);
		//$this->CI->db->delete("acordos_taxas_locais_globais","id > 0");//FIXME Descomentar aki quando o teste estiver pronto
	}
	
	public function testAssertPreConditionsShouldPass()
	{
		$this->assertTrue(class_exists("Acordos_Taxas_Facade"));
		$this->assertTrue(class_exists("Acordo_Taxas_Locais_Model"));
		$this->assertTrue(class_exists("Acordo_Taxas_Entity"));
	}
	
	/**
	 * @depends testAssertPreConditionsShouldPass
	 */
	public function testSalvarAcordoTaxasLocais()
	{

		$_POST['sentido'] = "IMP";
		$_POST['clientes_selecionados'] = Array(12,14,15,16);
		$_POST['portos_selecionados'] = Array(1,2,3,4);
		$_POST['taxas_selecionadas'] = Array('8;CAPATAZIAS;49.50;160.00;0.00;88;R$;3;WM',
											  '21;DESCONSOLIDAÇÃO;80.00;80.00;80.00;42;USD;4;BL',
											  '22;DESOVA;40.00;185.00;0.00;88;R$;3;WM',
											  '25;LIBERAÇÃO;60.00;60.00;60.00;42;USD;4;BL');
		$_POST['inicio'] = "18-05-2013";
		$_POST['validade'] = "30-05-2013";
		$_POST['observacao_interna'] = "TESTE OBS INTERNA";
		
		$facade = new Acordos_Taxas_Facade();
		$id_acordo = $facade->salvarAcordoTaxasLocais($this->CI->input->post());
		
		$this->assertNotNull($id_acordo);
		$this->assertTrue(is_integer($id_acordo));
		
		/** Verifica se o registro existe de fato no banco de dados **/
		$rs = $this->CI->db->get_where("CLIENTES.acordos_taxas_locais_globais","id = ".$id_acordo);
			
		$this->assertEquals(1, $rs->num_rows());
		
	}
	
}//END CLASS