<html>
    <head>
        <meta http-equiv='refresh' content='1' />
    </head>
<?php
include_once 'classes.php';
$get = array();
$dado = array();
$GMT = -3; 
$log = '';
foreach ($_REQUEST as $chave => $value) {
	$get[$chave] = htmlentities($value);	
	$log.=$chave.'='.$value.'; '; 			//MONTANDO VALOR PARA GRAVAR NO LOG DO BANCO
}
$data= date('Y-m-d H:i:s', strtotime($GMT. 'hours'));

//--------------------GRAVAR REQUEST NO LOG DO BANCO DE DADOS-------------//
if(!empty($log)){
	$querySql = "INSERT INTO log (`id`, `valor`, `data`) VALUES ('','$log','$data')";
	$query = $mysqli->query($querySql);	
}
//-----------------------------------------------------------------------//

if(isset($get['temperatura'])){
	$querySql = "INSERT INTO temperatura (`data`, `temperatura`, `umidade`) VALUES ('$data','$get[temperatura]','$get[umidade]')";
	$query = $mysqli->query($querySql);
	$querySqlLog = "INSERT INTO log (`id`, `valor`, `data`) VALUES ('','$querySql','$data')";
	$query = $mysqli->query($querySqlLog);	
	}
if(isset($get['alarme'])){
	$querySql = "INSERT INTO alarme (`data`, `status`)  VALUES ('$data','$get[alarme]')";
	$query = $mysqli->query($querySql);
	$querySqlLog = "INSERT INTO log (`id`, `valor`, `data`) VALUES ('','$querySql','$data')";
	$query = $mysqli->query($querySqlLog);	
	}
if(isset($get['porta'])){
	$querySql = "INSERT INTO `portasoutput`(`data`, `numero`,`central`, `acao`) VALUES ('$data','$get[porta]','$get[central]','$get[acao]')";
	$query = $mysqli->query($querySql);
	$querySqlLog = "INSERT INTO log (`id`, `valor`, `data`) VALUES ('','$querySql','$data')";
	$query = $mysqli->query($querySqlLog);	
	}
if(isset($get['id'])){
	if($get['id'] == 'param'){
		echo 'parametros';
	}
}
if(isset($get['sensor'])){
	//EXEMPLO DE REQUEST GET - http://127.0.0.1/arduino/gravar.php?sensor=dht11&valor=dht11;32;89&central=192.168.0.177&porta=10
	$querySql = "INSERT INTO `sensores`(`data`, `valor`, `central`, `porta`,`tipo`) VALUES ('$data','$get[valor]','$get[central]','$get[porta]','$get[sensor]')";
	$query = $mysqli->query($querySql);
	}
$querySql = "SELECT * from log order by data desc LIMIT 100 ";
$query = $mysqli->query($querySql);
$valor ='';
while($linha = mysqli_fetch_array($query))
{
	$valor .= $linha['data'].' - '. $linha['valor'];
	$valor .='<br>';
}
echo '<br><br>';
echo 'LOG O SISTEMA AUTOMACAO';
echo '<br><br>';	
echo 
	"<div style='width:600px; height:250px;border:1px solid;position: relative;margin-left:5px;text-align:justify;overflow:auto;padding:5px 5px 20px 10px;'>
		$valor
	</div>";
	
?>
