<?php
$tiposSensores = array('dht11','dht22'); // LISTA DE SENSORES QUE FUNCIONAM
if(!$query = $mysqli->query($querySqlSensor)){	// 	RETORNA ERRO CASO OCORRA ERRO NA CONSULTA
	echo "Errorcode: ".$mysqli->errno;
	echo "<br>Errormessage: ".$mysqli->error;
	$log_registro = "Errorcode: ".$mysqli->errno." Errormessage: ".$mysqli->error;
	Log_error($log_registro,$data);
	exit();
}
$campoChave = array('tipo', 'valor');
while ($r = mysqli_fetch_assoc($query)) {
	$dadoSensor[$r['tipo']] = $r['valor'];
	}
if(!isset($dadoSensor)){
	exit;
}
$listaSensores= array('dht11','mq-2','.');
$i=0;
while($i<=count($dadoSensor)){
	if(array_key_exists($listaSensores[$i],$dadoSensor)){
		$tipo = explode(';',$dadoSensor[$listaSensores[$i]]);
		if($tipo[0] == "dht11"){
			$t=$tipo[1];
			$u=$tipo[2];
			$identificacao2 ="<img src='ico/termometro.png' style='width:20px;'/> $t ºC"; 
			$valor = "<img src='ico/nuvem.png' style='width:35px;'/> $u%";
		}
		if($tipo[0] == "mq-2"){
			$f = $tipo[1];
			if($f <= 254){
				$identificacao2 ="Gás-Fumaça"; 
				$valor= "Ausente:($f)";
			}else{
				$identificacao2 ="<div id='' style='background:red'>Gás-Fumaça"; 
				$valor="Detectado: ($f)</div>";
			}
		}
		echo"
		<table>
			<tr style='height:50px'>
				<td>
					$identificacao2
				</td>				
				<td>
					$valor
				</td>
			</tr>
		</table>";
	}
$i++;
}
?>