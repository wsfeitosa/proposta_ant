<?php
$this->load->helper(array("html","url","form"));
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
   <head>
        <META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
        <META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
        <META HTTP-EQUIV="EXPIRES" CONTENT="0">     
        <meta http-equiv="Content-Type" content="text/html;charset=iso-8859-1" >
        <title><?php echo $form_title;?></title>
        <link rel="stylesheet" href="http://<?php echo $_SERVER["HTTP_HOST"];?>/Estilos/geral.css" type="text/css" />
        <link rel="stylesheet" href="http://<?php echo $_SERVER["HTTP_HOST"];?>/Libs/jquery-ui/css/redmond/jquery-ui-1.8.17.custom.css" type="text/css" />
        <script language="javascript" src="http://<?php echo $_SERVER["HTTP_HOST"];?>/Libs/jquery-ui/js/jquery-1.7.1.min.js"></script>
        <script language="javascript" src="http://<?php echo $_SERVER["HTTP_HOST"];?>/Libs/jquery-ui/js/jquery-ui-1.8.17.custom.min.js"></script>
        <style>			
			#pop{display:none;position:absolute;top:40%;left:38%;margin-left:-150px;margin-top:-150px;padding:10px;width:650px;height:400px;border:1px solid #d0d0d0; background-color:white;}
		</style>
        <?php echo $css; ?>        
        <?php echo $js; ?>                         
    </head>
<body>
<table cellpadding="0" cellspacing="0" border="0" width="100%" align="left">
    <tr>        
        <td class="titulo_tabela" width="10">
            <img alt="img1" src="http://<?php echo $_SERVER["HTTP_HOST"];?>/Imagens/pixel.gif" width="10" height="1" border="0">
        </td>
        <td class="header1" nowrap>
            <?php echo $form_name;?>
            <img alt="img2" src="http://<?php echo $_SERVER["HTTP_HOST"];?>/Imagens/pixel.gif" width="10" height="1" border="0">
        </td>
        <td>
            <img alt="img3" src="http://<?php echo $_SERVER["HTTP_HOST"];?>/Imagens/formtab_r.gif" width="10" height="21" border="0">
        </td>
        <td class="linha_t" width="100%">&nbsp;</td>
        <td class="linha_t">
            <img alt="img4" src="http://<?php echo $_SERVER["HTTP_HOST"];?>/Imagens/pixel.gif" width="10" height="8" border="0">
        </td>                   
    </tr>   
    <tr>
        <td colspan="5">

