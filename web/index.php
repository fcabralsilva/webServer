<?php
	$id 	= addslashes(isset($_GET['id']) ? $_GET['id'] : 'menu');
	$acao 	= addslashes(isset($_GET['acao']) ? $_GET['acao'] : '');
	$setor	= addslashes(isset($_GET['setor']) ? $_GET['setor'] : '');
	$com	= addslashes(isset($_GET['com']) ? $_GET['com'] : '');
	$exec	= addslashes(isset($_GET['exec']) ? $_GET['exec'] : '');
	$GMT = -3;
	$data= date('Y-m-d H:i:s', strtotime($GMT. 'hours'));
	include 'classes.php';
	include 'funcoes.php';
	echo '
	<!DOCTYPE html>
	<html lang="pt-br">
	<head>
		<meta charset="utf-8"/>
		<meta content="width=device-width, initial-scale=1, maximum-scale=1" name="viewport">
		<meta charset="utf-8">
		<link rel="stylesheet" href="css/bootstrap.min.css">
		<link rel="stylesheet" href="css/estilo.css">
		<script language="JavaScript" src="script.js"></script>
		<script language="JavaScript" src="jquery.min.js"></script>
		<title></title>
	</head>
	<body>
		<div class="container">
			<div class="row">
				<div class="col-sm-4" style="width:100px;float:left ">
					<a href="?id=menu">
						<img src="ico/casa.png" style="margin:10px 0 0 0;"/>
						<!--<span class="	fa fa-home" style="font-size:48px;color:whrite;margin:5px 0 0 0 ;"></span>-->
					</a>
				</div>
				<div class="col-sm-4" style="text-align:right;float:right">
					<a href="?id=configurar">
						<img src="ico/config.png" style="width:30px; margin:20px 0 0 0;"/>
						<!--<span class="glyphicon glyphicon-cog" style="font-size:24px;color:whrite; margin:15px 0 0 0 ;"></span>-->
					</a>
				</div>
			</div>';
	//*******************************************************************************************************
	//				TEMPERATURA E UMIDADE DA TELA INICIAL
	//*******************************************************************************************************
	$querySql = "select valor from parametro where parametro = 'termometroPrincipal'";
	$centralTermometro = mysqli_fetch_assoc($mysqli->query($querySql));
	$querySql ="SELECT DATE_FORMAT (s.data,'%d/%m %H:%i') AS data, s.valor, s.central, c.nome, s.porta, s.tipo FROM sensores s, central c WHERE s.central ='$centralTermometro[valor]' AND s.tipo = 'dht11' AND s.central = c.ip ORDER BY s.data DESC LIMIT 1";
	if(!$query = $mysqli->query($querySql)){
		echo "Errorcode: ".$mysqli->errno;
		echo "<br>Errormessage: ".$mysqli->error;
		$log_registro = "Errorcode: ".$mysqli->errno." Errormessage: ".$mysqli->error;
		Log_error($log_registro,$data);
		exit();
	};
	while ($r = mysqli_fetch_assoc($query)) {
		$dadoT[0] = $r['data'];
		$dadoT[1] = explode(";",$r['valor']);
		$dadoT[2] = $r['nome'];
	}
	if(!isset($dadoT)){
		$dadoT = 0;
	}
	$tempPrincipal = $dadoT[1][1];
	$umidadePrincipal = $dadoT[1][2];
	//*******************************************************************************************************
	//				VERIFICA SE O ALARME ESTA ATIVO NO BANCO E CRIA VARIAVEL CHECKED
	//*******************************************************************************************************

	$querySql ='SELECT max(data)data, status FROM alarme group by data, status  ORDER BY data DESC LIMIT 1';
	if(!$query = $mysqli->query($querySql)){
		echo "Errorcode: ".$mysqli->errno;
		echo "<br>Errormessage: ".$mysqli->error;
		$log_registro = "Errorcode: ".$mysqli->errno." Errormessage: ".$mysqli->error;
		Log_error($log_registro,$data);
		exit();
	};
	$campoChave = array('data', 'status');
	while ($r = mysqli_fetch_assoc($query)) {
		for ($n = 0; $n <= count($campoChave) - 1; $n++) {
		$alarme[] = $r[$campoChave[$n]];
		}
	}
	if($alarme[1] == "ativo"){			//
		$checkeAlarme = 'checked';
	}else{
		$checkeAlarme = '';
	//---------------------------------------------------------------------------------------------------------
	}
	if ($id == 'menu') {
		if($exec == 'reltemp' ){
			echo"
			<div class='row'>
				<div id='itemDivTitulo' >
					<div >
						<p style='margin:10px 0 0 0; font-size:18px;'>
								Histórico
						</p>
					</div>
				</div>

			</div>
			</br>";
			$querySql ="SELECT date_format(data,'%d/%m %h:%i') as data, valor FROM sensores where central = '$centralTermometro[valor]' and tipo='dht11' order by data desc limit 25";
			if(!$query = $mysqli->query($querySql)){
				echo "Errorcode: ".$mysqli->errno;
				echo "<br>Errormessage: ".$mysqli->error;
				$log_registro = "Errorcode: ".$mysqli->errno." Errormessage: ".$mysqli->error;
				Log_error($log_registro,$data);
				exit();
			};
			echo"<table class='' style='width:100%;'>
					<tr style='background-color:rgba(0, 255, 188, 0.09)'>
						<td><strong>Data</strong></td><td><strong>Temp Cº</strong></td><td><strong>Umid %</strong></td>
					</tr>";
			while ($r = mysqli_fetch_array($query)) {
				$data=$r['data'];
				$valor= explode(";",$r['valor']);
				echo"<tr id='trnone'><td><strong>";
				echo $data;
				echo"</strong></td><td><strong>";
				echo $valor[1];
				echo"</strong></td><td><strong>";
				echo $valor[2];
				echo"</strong></td></td>";
			}
			echo"</table>";

			voltar('?id=menu');
			exit;
		}
		echo "
		<script>
			setInterval('window.location.reload()', 5000);
		</script>
		<div class='row'>
			<a href='?id=menu&exec=reltemp' >
				<div id='itemDiv'>
					<div style='line-height:0;'>
						<p>
							<img src='ico/termometro.png' style='width:12px;'/> $tempPrincipal ºC
							<img src='ico/nuvem.png' style='width:20px;'/> $umidadePrincipal %
						</p>
						<p>
							<span style='font-size:10px;margin:0 0 0 10px'>$dadoT[0] - $dadoT[2] </span>
						</p>
					</div>
				</div>
			</a>
		</div> ";
		$querySql = "select valor from parametro where parametro = 'alarme'";
		if(!$query = $mysqli->query($querySql)){
			echo "Errorcode: ".$mysqli->errno;
			echo "<br>Errormessage: ".$mysqli->error;
			$log_registro = "Errorcode: ".$mysqli->errno." Errormessage: ".$mysqli->error;
			Log_error($log_registro,$data);
			exit();
		};
		$statusAlarme = mysqli_fetch_assoc($query);
		if ($statusAlarme['valor'] == 1){
			echo"
			<div class='row'>
				<div id='itemDiv'>
				<div style='width:130px;margin:0 auto'>
					<div style='width:60px;float:left;padding:10px;'>
							 Alarme
					</div>
					<div style='float:right;padding:5px;' >
							 <label class='switch'>
								<input type='checkbox' name ='alarme' onclick=\"verificarCheckBox('alarme')\" $checkeAlarme>
								<span class='slider round '></span>
							</label>
					</div>
				</div>
				</div>
			</div>";
		}
		echo"<div class='row'>
			<a href='?id=comodos'>
				<div id='itemDiv' >
					<div >
						<p style='margin:10px 0 0 0; font-size:18px;'>
						<img src='ico/comodo.png' style='width:25px;'/>
						<!--<i class='fa fa-hotel' aria-hidden='true' style='font-size:22px;color:whrite;margin:0px 0 0 0 ;'></i>-->
							Cômodos
						</p>
					</div>
				</div>
			</a>
		</div>
		<div class='row'>
		<a href='?id=lampadas'>
			<div id='itemDiv' >
				<div >
					<p style='margin:10px 0 0 0; font-size:18px;'>
					<img src='ico/lampada.png' style='width:25px;'/>
					<!--<i class='fa fa-lightbulb-o' aria-hidden='true' style='font-size:22px;color:whrite;margin:0px 0 0 0 ;'></i>-->
						Lâmpadas
					</p>
				</div>
			</div>
		</a>
		</div>
		<div class='row'>
		<a href='?id=tomadas'>
			<div id='itemDiv' >
				<div >
					<p style='margin:10px 0 0 0; font-size:18px;'>
					<img src='ico/tomada.png' style='width:25px;'/>
					<!--<i class='fa fa-plug' aria-hidden='true' style='font-size:22px;color:whrite;margin:0px 0 0 0 ;'></i>-->
						Tomadas
					</p>
				</div>
			</div>
		</div>";
	}
	if ($id == "configurar"){
		echo"
		<script>
			setInterval('window.location.reload(false)';);
		</script>";
		if($setor == 'novaCentral'){

			if($exec == 'consultar'){
				echo"
			<div class='row'>
				<div id='itemDivTitulo' >
					<div >
						<p style='margin:10px 0 0 0; font-size:18px;'>
							<i class='fa fa-' aria-hidden='true' style='font-size:22px;color:whrite;margin:0px 0 0 0 ;'></i>
								Centrais Cadastradas
						</p>
					</div>
				</div>
			</div>
				</br>";
				$querySql ='SELECT * FROM central';
				if(!$query = $mysqli->query($querySql)){
					echo "Errorcode: ".$mysqli->errno;
					echo "<br>Errormessage: ".$mysqli->error;
					$log_registro = "Errorcode: ".$mysqli->errno." Errormessage: ".$mysqli->error;
					Log_error($log_registro,$data);
					exit();
				};
				$campoChave = array('nome', 'ip', 'porta');
				echo"<table id='tabela1' class='ta ble'>
						<tr style='background-color:rgba(0, 255, 188, 0.09)'>
							<td><strong>Nome</strong></td><td><strong>IP</strong></td><td><strong>Porta</strong></td><td></td>
						</tr>";
				while ($r = mysqli_fetch_array($query)) {
					echo"<tr><td><strong>";
					echo $r['nome'];
					echo"</strong></td><td><strong>";
					echo $r['ip'];
					echo"</strong></td><td><strong>";
					echo $r['porta'];
					echo"</strong></td><td><a href='?id=configurar&setor=novaCentral&exec=excluir&ip=$r[ip]'><img src='ico/apagar.png' style='width:25px;'/></a></td></tr>";
				}
				echo"</table>";
 				voltar('?id=configurar&setor=novaCentral');
				exit;
			}
			if($exec == 'inserir'){
				foreach($_POST as $key=>$value){
					if($value == ''){
						echo '
						<br>
						<div class="alert alert-danger" role="alert">
							<strong>Atenção:</strong> 
							Você tentou gravar dados em branco, retorne e preencha todos os campos!"
						</div>';
						voltar('?id=configurar&setor=novaCentral');
						exit;
					}else{
						$postDados[$key] = $value;
						$querySql = "select $key from central where $key = '$value'";
						//CONDIÇÃO QUE PERMITE REPETIÇÃO NO INSERT
						$campoPermitido = "porta";
						$campoNpermitido = array('nome'=>['Nome da Central'],'ip'=>["Endereço IP"]);
						$query = $mysqli->query($querySql);
						$r = mysqli_fetch_assoc($query);
						//VERIFICA SE O DADO JA EXISTE NO BANCO DE DADOS
						if(strtolower($value) == strtolower($r[$key])){ 
							//VERIFICA SE O DADO E DIFERENTE DO CAMPO PERMITIDO, SE FOR MOSTA MENSAGEM DE ERRO DUPLICAÇÃO
							if($key <> $campoPermitido){
								$campo = implode("",$campoNpermitido[$key]);
								echo "
								<br>
								<div class='alert alert-danger'>
									<strong>Atenção:</strong> <br>
									ERRO 01: Existe um ou mais campos duplicado.<br>
									Campo duplicado: <strong>$campo</strong>
								</div>";
								voltar('?id=configurar&setor=novaCentral');
								exit;
							}
						}
					}
				}
				$querySql ="INSERT INTO `central`(`data`, `nome`, `ip`, `porta`) VALUES ('$data','$postDados[nome]','$postDados[ip]','$postDados[porta]')";
				if(!$query = $mysqli->query($querySql)){
					echo "Errorcode: ".$mysqli->errno;
					echo "<br>Errormessage: ".$mysqli->error;
					$log_registro = "Errorcode: ".$mysqli->errno." Errormessage: ".$mysqli->error;
					Log_error($log_registro,$data);
					exit();
				};
				LogBanco($querySql,$data);

			}
			if($exec == 'excluir'){
				
				foreach($_REQUEST as $key=>$value){
					$excluirDados[$key] = $value;
				}
				$querySql ="delete from central where ip = '$excluirDados[ip]'";
				if(!$query = $mysqli->query($querySql)){
					echo "Errorcode: ".$mysqli->errno;
					echo "<br>Errormessage: ".$mysqli->error;
					$log_registro = "Errorcode: ".$mysqli->errno." Errormessage: ".$mysqli->error;
					Log_error($log_registro,$data);
				};
				LogBanco($querySql,$data);
				$querySql ="delete from itens where central = '$excluirDados[ip]'";
				if(!$query = $mysqli->query($querySql)){
					echo "Errorcode: ".$mysqli->errno;
					echo "<br>Errormessage: ".$mysqli->error;
					$log_registro = "Errorcode: ".$mysqli->errno." Errormessage: ".$mysqli->error;
					Log_error($log_registro,$data);
				}; 
				LogBanco($querySql,$data);
				$querySql ="UPDATE `parametro` SET `valor` = '0' WHERE `parametro` = 'termometroPrincipal'and valor ='$excluirDados[ip]'";
				if(!$query = $mysqli->query($querySql)){
					echo "Errorcode: ".$mysqli->errno;
					echo "<br>Errormessage: ".$mysqli->error;
					$log_registro = "Errorcode: ".$mysqli->errno." Errormessage: ".$mysqli->error;
					Log_error($log_registro,$data);
				};
				LogBanco($querySql,$data);
				$querySql ="delete from sensores where central = '$excluirDados[ip]'";
				if(!$query = $mysqli->query($querySql)){
					echo "Errorcode: ".$mysqli->errno;
					echo "<br>Errormessage: ".$mysqli->error;
					$log_registro = "Errorcode: ".$mysqli->errno." Errormessage: ".$mysqli->error;
					Log_error($log_registro,$data);
				};
				LogBanco($querySql,$data);
				$querySql ="delete from portasoutput where central = '$excluirDados[ip]'";
				if(!$query = $mysqli->query($querySql)){
					echo "Errorcode: ".$mysqli->errno;
					echo "<br>Errormessage: ".$mysqli->error;
					$log_registro = "Errorcode: ".$mysqli->errno." Errormessage: ".$mysqli->error;
					Log_error($log_registro,$data);
					exit();
				};
				LogBanco($querySql,$data);
				header("location:?id=configurar&setor=novaCentral&exec=consultar");
			}
			echo"
			<div class='row'>
				<div id='itemDivTitulo' >
					<div >
						<p style='margin:10px 0 0 0; font-size:18px;'>
							<i class='fa fa-' aria-hidden='true' style='font-size:22px;color:whrite;margin:0px 0 0 0 ;'></i>
								Nova Central
						</p>
					</div>
				</div>
			</div>
			<form class='form-horizontal' method='post' action='index.php?id=configurar&setor=novaCentral&exec=inserir'>
				<fieldset>
					<div class='row'>
						<div id='itemDivPainel' >
						<div class='form-group'>
							</br>
							<label class='col-md-5 control-label' for='Nome da Cental'>Nome Cental</label>
							<div class='col-md-5'>
								<input id='Nome da Cental' name='nome' type='text' placeholder='Ex: Cental_1' class='form-control input-md' required=''>
							</div>
						</div>
					<div class='form-group'>
						<label class='col-md-5 control-label' for='ip'>Endereço IP</label>
						<div class='col-md-7'>
							<input id='ip' name='ip' type='text' placeholder='Ex: 192.168.1.10' class='form-control input-md' pattern=\"[0-9.]{1,3}\.[0-9.]{1,3}\.[0-9.]{1,3}\.[0-9.]{1,3}\" required='' maxlength='15'>
						</div>
					</div>
					<div class='form-group'>
					  <label class='col-md-5 control-label' for='porta'>Porta</label>
					  <div class='col-md-5'>
						<input id='porta' name='porta' type='text' placeholder='Ex: 10'  pattern=\"[0-9]{1,2}\" class='form-control input-md' required=''>
					  </div>
					</div>
					<div class='form-group'>
					  <label class='col-md-4 control-label' for='Inserir'></label>
					  <div class='col-md-8'>
						<button id='Inserir' name='' class='btn btn-success'>Inserir</button>
						<button id='consultar' onClick='javascript:location.href=\"?id=configurar&setor=novaCentral&exec=consultar\"''name='consultar' class='btn btn-info'>Consultar</button>
					  </div>
					</div>
				</fieldset>
			</form>";
			voltar('?id=configurar');
			exit;
		}
		if($setor == 'novoComodo'){
			if($exec == 'consultar'){
				echo"
			<div class='row'>
				<div id='itemDivTitulo' >
					<div >
						<p style='margin:10px 0 0 0; font-size:18px;'>
							<i class='fa fa-' aria-hidden='true' style='font-size:22px;color:whrite;margin:0px 0 0 0 ;'></i>
							Comôdos Cadastrados
						</p>
					</div>
				</div>
			</div>
				</br>";
				$querySql ='SELECT * FROM comodos';
				if(!$query = $mysqli->query($querySql)){
					echo "Errorcode: ".$mysqli->errno;
					echo "<br>Errormessage: ".$mysqli->error;
					$log_registro = "Errorcode: ".$mysqli->errno." Errormessage: ".$mysqli->error;
					Log_error($log_registro,$data);
					exit();
				};
				$campoChave = array('id_comodo', 'nome', 'data');
				echo"<table class=''>
						<tr style='background-color:rgba(0, 255, 188, 0.09)'>
							<td><strong>ID</strong></td><td><strong>Comôdo</strong></td><td></td>
						</tr>";
				while ($r = mysqli_fetch_array($query)) {
					echo"<tr><td><strong>";
					echo $r['id_comodo'];
					echo"</strong></td><td><strong>";
					echo $r['nome'];
/* 					echo"</strong></td><td><strong>";
					echo $r['data']; */
					echo"</strong></td><td><a href='?id=configurar&setor=novoComodo&exec=excluir&id_comodo=$r[id_comodo]'><img src='ico/apagar.png' style='width:25px;'/></a></td></tr>";
				}
				echo"</table>";
 				voltar('?id=configurar&setor=novoComodo');
				exit;
			}
			if($exec == 'inserir'){
				foreach($_POST as $key=>$value){
					if($value == ''){
						echo "</br>Atenção: Você tentou gravar dados em branco, retorne e preencha todos os campos!";
						voltar("?id=configurar&setor=novoComodo");
						exit;
					}else{
						$postDados[$key] = $value;
						$querySql = "select $key from comodos where $key = '$value'";
						//CONDIÇÃO QUE PERMITE REPETIÇÃO NO INSERT
						$campoPermitido = "";
						$campoNpermitido = array('nome'=>['Nome do Comôdo']);
						$query = $mysqli->query($querySql);
						$r = mysqli_fetch_assoc($query);
						//print_r($r[$key] . $value);
						//VERIFICA SE O DADO JA EXISTE NO BANCO DE DADOS
						if(strtolower($value) == strtolower($r[$key])){ 
							//VERIFICA SE O DADO E DIFERENTE DO CAMPO PERMITIDO, SE FOR MOSTA MENSAGEM DE ERRO DUPLICAÇÃO
								$campo = implode("",$campoNpermitido[$key]);
								echo "
								<br>
								<div class='alert alert-danger'>
									<strong>Atenção:</strong> <br>
									ERRO 01: Existe um ou mais campos duplicado.<br>
									Campo duplicado: <strong>$campo</strong>
								</div>";
								voltar('?id=configurar&setor=novoComodo');
								exit;
						}
					}
				}
				$querySql ="INSERT INTO comodos VALUES ('','$postDados[nome]','$data')";
				if(!$query = $mysqli->query($querySql)){
					echo "Errorcode: ".$mysqli->errno;
					echo "<br>Errormessage: ".$mysqli->error;
					$log_registro = "Errorcode: ".$mysqli->errno." Errormessage: ".$mysqli->error;
					Log_error($log_registro,$data);
					exit();
				};
				LogBanco($querySql,$data);

			}
			if($exec == 'excluir'){
				foreach($_REQUEST as $key=>$value){
					$excluirDados[$key] = $value;
				}
				$querySql ="delete from comodos where id_comodo = '$excluirDados[id_comodo]'";
				if(!$query = $mysqli->query($querySql)){
					echo "Errorcode: ".$mysqli->errno;
					echo "<br>Errormessage: ".$mysqli->error;
					$log_registro = "Errorcode: ".$mysqli->errno." Errormessage: ".$mysqli->error;
					Log_error($log_registro,$data);
					exit();
				};
				header("location:?id=configurar&setor=novoComodo&exec=consultar");
			}

			echo"
			<div class='row'>
				<div id='itemDivTitulo' >
					<div >
						<p style='margin:10px 0 0 0; font-size:18px;'>
							<i class='fa fa-' aria-hidden='true' style='font-size:22px;color:whrite;margin:0px 0 0 0 ;'></i>
								Novo Comôdo
						</p>
					</div>
				</div>
			</div>
			<div class='row'>
				<div id='itemDivPainel' >
					<form class='form-horizontal' method='post' action='index.php?id=configurar&setor=novoComodo&exec=inserir'>
						<fieldset>
						<div class='form-group'>
						  <label class='col-md-5 control-label' for='Nome da Cental'>Nome Comôdo</label>
						  <div class='col-md-5'>
						  <input id='' name='nome' type='text' placeholder='' maxlength='20' class='form-control input-md' required=''>
						</div>
				</div>
				<div class='form-group'>
				  <label class='col-md-4 control-label' for='Inserir'></label>
				  <div class='col-md-8'>
					<button id='Inserir' name='' class='btn btn-success'>Inserir</button>
					<button id='consultar' onClick='javascript:location.href=\"?id=configurar&setor=novoComodo&exec=consultar\"''name='consultar' class='btn btn-info'>Consultar</button>
				  </div>
				</div>
				</div>

				</fieldset>
			</form>";
			voltar('?id=configurar');
			exit;
		}
		if($setor == 'novoComando'){

			if($exec == 'consultar'){
				echo"
			<div class='row'>
				<div id='itemDivTitulo' >
					<div >
						<p style='margin:10px 0 0 0; font-size:18px;'>
							<i class='fa fa-' aria-hidden='true' style='font-size:22px;color:whrite;margin:0px 0 0 0 ;'></i>
								Comandos Cadastrado
						</p>
					</div>
				</div>
			</div>

				</br>";
				$querySql ='SELECT * FROM itens';
				if(!$query = $mysqli->query($querySql)){
					echo "Errorcode: ".$mysqli->errno;
					echo "<br>Errormessage: ".$mysqli->error;
					$log_registro = "Errorcode: ".$mysqli->errno." Errormessage: ".$mysqli->error;
					Log_error($log_registro,$data);
					exit();
				};
				$campoChave = array('nome', 'ip', 'porta');
				echo"<table>
						<tr style='background-color:rgba(0, 255, 188, 0.09);font-size:9px'>
							<td><strong>item</strong></td><td><strong>Tipo</strong></td><td><strong>IP Central</strong></td><td><strong>Porta</strong></td><td></td>
						</tr>";
				while ($r = mysqli_fetch_array($query)) {
					echo"<tr  style='font-size:9px'><td><strong>";
					echo $r['nome'].': '.$r['comodo'];
					echo"</strong></td><td><strong>";
					if($r['type'] == 'checkbox'){
						$tipo = 'INTER.';
					}else if($r['type'] == 'range'){
						$tipo = 'DIMMER';
					}else if($r['type'] == 'dht11'){
						$tipo = 'DHT11';
					}else if($r['type'] == 'mq-2'){
						$tipo = 'MQ-2';
					}else{
						$tipo = 'NãoCad';
					}
					echo $tipo;
					echo"</strong></td><td><strong>";
					echo $r['central'];
					echo"</strong></td><td><strong>";
					echo $r['porta'];
					echo"</strong></td><td><a href='?id=configurar&setor=novoComando&exec=excluir&data=$r[data]'><img src='ico/apagar.png' style='width:25px;'/></a></td></tr>";
				}
				echo"</table>";
 				voltar('?id=configurar&setor=novoComando');
				exit;
			}
			if($exec == 'inserir'){
				foreach($_POST as $key=>$value){
					$postDados[$key] = $value;
				}
				$querySqlPorta = "select * from itens where porta ='$postDados[porta]' and central = '$postDados[central]'";
				if(!$query = $mysqli->query($querySqlPorta)){
					echo "Errorcode: ".$mysqli->errno;
					echo "<br>Errormessage: ".$mysqli->error;
					$log_registro = "Errorcode: ".$mysqli->errno." Errormessage: ".$mysqli->error;
					Log_error($log_registro,$data);
					exit();
				};
				$r = mysqli_fetch_row($query);
				if($r >= 1){
					echo '
					<br>
					<div class="alert alert-danger" role="alert">
						<strong>Atenção:</strong> 
						A porta '.$postDados['porta'].' que esta tentando utilizar já esta ocupada, utilize outra porta da central!!!"
					</div>';
					voltar('?id=configurar&setor=novoComando');
					exit;
				}

				$querySql ="INSERT INTO `itens`(`data`, `comodo`, `central`, `porta`, `nome`, `type`, `acao`)
				VALUES ('$data','$postDados[comodo]','$postDados[central]','$postDados[porta]','$postDados[nome]','$postDados[tipo]','$postDados[acao]')";
				if(!$query = $mysqli->query($querySql)){
					echo "Errorcode: ".$mysqli->errno;
					echo "<br>Errormessage: ".$mysqli->error;
					$log_registro = "Errorcode: ".$mysqli->errno." Errormessage: ".$mysqli->error;
					Log_error($log_registro,$data);
					exit();
				};
				LogBanco($querySql,$data);

			}
			if($exec == 'excluir'){

				foreach($_REQUEST as $key=>$value){
					$excluirDados[$key] = $value;
				}
				$querySql ="delete from itens where data = '$excluirDados[data]'";
				if(!$query = $mysqli->query($querySql)){
					echo "Errorcode: ".$mysqli->errno;
					echo "<br>Errormessage: ".$mysqli->error;
					$log_registro = "Errorcode: ".$mysqli->errno." Errormessage: ".$mysqli->error;
					Log_error($log_registro,$data);
					exit();
				};
				header("location:?id=configurar&setor=novoComando&exec=consultar");
			}

			// Criar lista do SELECT de comodos
			$querySql ='SELECT * FROM comodos ';
			if(!$query = $mysqli->query($querySql)){
				echo "Errorcode: ".$mysqli->errno;
				echo "<br>Errormessage: ".$mysqli->error;
				$log_registro = "Errorcode: ".$mysqli->errno." Errormessage: ".$mysqli->error;
				Log_error($log_registro,$data);
				exit();
			};
			$option = 0;
			while($resultado= mysqli_fetch_array($query)){
				$option.="<option value='$resultado[nome]'>".$resultado['nome']."</option>";
			}
			// Criar lista do SELECT de centrais
			$querySql2 ='SELECT * FROM central';
			if(!$query2 = $mysqli->query($querySql2)){
				echo "Errorcode: ".$mysqli->errno;
				echo "<br>Errormessage: ".$mysqli->error;
				$log_registro = "Errorcode: ".$mysqli->errno." Errormessage: ".$mysqli->error;
				Log_error($log_registro,$data);
				exit();
			};
			$option2 = 0;
			while($resultado2= mysqli_fetch_array($query2)){
				$option2.="<option value='$resultado2[ip]'>".$resultado2['nome']."</option>";
			}
			echo"
			<div class='row'>
				<div id='itemDivTitulo'>
					<div >
						<p style='margin:10px 0 0 0; font-size:18px;'>
							<i class='fa fa-' aria-hidden='true' style='font-size:22px;color:whrite;margin:0px 0 0 0 ;'></i>
								Configurar Ação
						</p>
					</div>
				</div>
			</div>

			<div class='row'>
				<div id='itemDivPainel' >
				</br>
				<form class='form-horizontal' method='post' action='index.php?id=configurar&setor=novoComando&exec=inserir'>
					<fieldset>
					<div class='form-group'>
					  <label class='col-md-5 control-label' for='Nome da Cental'>Nome do Cômodo</label>
					  <div class='col-md-6'>
					  <select class='form-control' name='comodo' required>
						<option value=''>----</option>
						$option
					  </select>
					  </div>
					</div>
					<div class='form-group'>
					  <label class='col-md-5 control-label' for='ip'>Central</label>
					  <div class='col-md-6'>
					  <select class='form-control' name='central' required>
					  <option value=''>----</option>
						$option2
					  </select>
					  </div>
					</div>
					<div class='form-group'>
					  <label class='col-md-5 control-label' for='porta'>Porta na central usada?</label>
					  <div class='col-md-4'>
						<input id='porta' name='porta' type='text' placeholder='Ex: 10' pattern='[0-9]{1,2}' class='form-control input-md' required=''>
					  </div>
					</div>
					<div class='form-group'>
					  <label class='col-md-5 control-label' for='porta' >O que quer controlar?</label>
					  <div class='col-md-6'>
						<input id='porta' name='nome' type='text' placeholder='' maxlength='20' class='form-control input-md' required=''>
					  </div>
					</div>
					<div class='form-group'>
					  <label class='col-md-5 control-label' for='porta'>Tipo</label>
					  <div class='col-md-6'>
						<select class='form-control' name='tipo' required>
							<option value=''>----</option>
							<option value='checkbox'>Interruptor</option>
							<option value='pulso'>Pulso</option>
							<option value='dht11'>DHT11</option>
							<option value='mq-2'>MQ-2</option>
						</select>
						<br>
						<select class='form-control' name='acao' required>
							<option value=''>----</option>
							<option value='lampada'>Lampada</option>
							<option value='tomada'>Tomada</option>
							<option value='sensor'>Sensor</option>
							<option value='mq-2'>MQ-2</option>
						</select>
					  </div>
					</div>
					<div class='form-group'>
					  <label class='col-md-4 control-label' for='Inserir'></label>
					  <div class='col-md-8'>
						<button id='Inserir' name='Inserir' class='btn btn-success'>Inserir</button>
						<button id='consultar' onClick='javascript:location.href=\"?id=configurar&setor=novoComando&exec=consultar\"''name='consultar' class='btn btn-info'>Consultar</button>
					  </div>
					</div>
					</div>
					</div>
					</fieldset>
				</form>";
			voltar('?id=configurar');
			exit;
		}
		if($setor == 'parametros'){
			if($exec == 'ativar'){
				$querySql = "update parametro set valor=1 where parametro = 'alarme'";
				if(!$query = $mysqli->query($querySql)){
					echo "Errorcode: ".$mysqli->errno;
					echo "<br>Errormessage: ".$mysqli->error;
					$log_registro = "Errorcode: ".$mysqli->errno." Errormessage: ".$mysqli->error;
					Log_error($log_registro,$data);
					exit();
				};
				LogBanco($querySql,$data);
				header("Location:index.php?id=configurar&setor=parametros");
			}
			if($exec == 'updateTermometro'){
				$central= addslashes(isset($_POST['central']) ? $_POST['central'] : '');
				$querySql = "update parametro set valor='$central' where parametro = 'termometroPrincipal'";
				if(!$query = $mysqli->query($querySql)){
					echo "Errorcode: ".$mysqli->errno;
					echo "<br>Errormessage: ".$mysqli->error;
					$log_registro = "Errorcode: ".$mysqli->errno." Errormessage: ".$mysqli->error;
					Log_error($log_registro,$data);
					exit();
				};
				LogBanco($querySql,$data);
				header("Location:index.php?id=configurar&setor=parametros");			
			}
			if($exec == 'desativar'){
				$querySql = "update parametro set valor=0 where parametro = 'alarme'";
				if(!$query = $mysqli->query($querySql)){
					echo "Errorcode: ".$mysqli->errno;
					echo "<br>Errormessage: ".$mysqli->error;
					$log_registro = "Errorcode: ".$mysqli->errno." Errormessage: ".$mysqli->error;
					Log_error($log_registro,$data);
					exit();
				};
				LogBanco($querySql,$data);
				header("Location:index.php?id=configurar&setor=parametros");
				}
			if($exec == 'deletar'){
				$querySql = "TRUNCATE table `temperatura`";
				$query = $mysqli->query($querySql);
				$querySql = "TRUNCATE table `sensores`";
				$query = $mysqli->query($querySql);
				$querySql = "TRUNCATE table `portasoutput`";
				$query = $mysqli->query($querySql);
				$querySql = "TRUNCATE table `log`";
				$query = $mysqli->query($querySql);
				header("Location:index.php?id=configurar&setor=parametros");
			}
			$querySql = "select valor from parametro where parametro='alarme'";
			if(!$query = $mysqli->query($querySql)){
				echo "Errorcode: ".$mysqli->errno;
				echo "<br>Errormessage: ".$mysqli->error;
				$log_registro = "Errorcode: ".$mysqli->errno." Errormessage: ".$mysqli->error;
				Log_error($log_registro,$data);
				exit();
			};
			$statusAlarme = mysqli_fetch_assoc($query);
			echo"
			<div id='itemDivTitulo'>
				<div >
					<p style='margin:10px 0 0 0; font-size:18px;'>
						<i class='fa fa-' aria-hidden='true' style='font-size:22px;color:whrite;margin:0px 0 0 0 ;'></i>
							Parametros Gerais
					</p>
				</div>
			</div>
			<div class='row'>
				<div id='itemDivPainel' >
					<label class='col-md-9 control-label' for='porta'>
						Deletar Histórico:
					</label>
					<button onClick='javascript:location.href=\"?id=configurar&setor=parametros&exec=deletar\"''name='ativar' class='btn btn-danger'>Apagar</button>
				</div>
			</div>";
			echo"
			<div class='row'>
				<div id='itemDivPainel' >
					<label class='col-md-8 control-label' for='porta'>
						Módulo de Alarme :
					</label>";
					if($statusAlarme['valor'] == '0'){
						echo"	<button id=''
								onClick='javascript:location.href=\"?id=configurar&setor=parametros&exec=ativar\"''
								name='ativar' class='btn btn-success'>Ativado</button>";
					}else{
						echo"	<button id=''
								onClick='javascript:location.href=\"?id=configurar&setor=parametros&exec=desativar\"''
								name='desativar' class='btn btn-danger'>Desativar</button>";
					}
				echo"
				</div>
			</div>";
			$querySql2 ='SELECT * FROM central';
			if(!$query2 = $mysqli->query($querySql2)){
				echo "Errorcode: ".$mysqli->errno;
				echo "<br>Errormessage: ".$mysqli->error;
				$log_registro = "Errorcode: ".$mysqli->errno." Errormessage: ".$mysqli->error;
				Log_error($log_registro,$data);
				//exit();
			};
			$option2 = 0;
			while($resultado2= mysqli_fetch_array($query2)){
				$option2.="<option value='$resultado2[ip]'>".$resultado2['nome']."</option>";
			}
			echo"
			<div class='row'>
				<div id='itemDivPainel' >
					<label class='col-md-4 control-label' for='porta'>
						Termômetro Principal
					</label>
					<form class='form-horizontal' method='post' action='?id=configurar&setor=parametros&exec=updateTermometro'>
					<div class='col-xs-5'>
						<select class='form-control' name='central' required >
							<option value=''>----</option>
							$option2
						</select>
					</div>
					<div>
						<input type='submit' name='desativar' class='btn btn-success' value='Alterar' />
					</div>
				</div>
			</div>";
			voltar('?id=configurar');
			exit;
		}
		echo"
			<div class='row'>
			<div id='itemDiv' >
				<a href='?id=configurar&setor=novaCentral'>
				<div >
					<p style='margin:10px 0 0 0; font-size:18px;'>
					<i class='fa fa-' aria-hidden='true' style='font-size:22px;color:whrite;margin:0px 0 0 0 ;'></i>
					Nova Central
					</p>
				</div>
				</a>
			</div>
		</div>
		<div class='row'>
			<a href='?id=configurar&setor=novoComodo'>
			<div id='itemDiv' >
				<div >
					<p style='margin:10px 0 0 0; font-size:18px;'>
					<i class='fa fa-' aria-hidden='true' style='font-size:22px;color:whrite;margin:0px 0 0 0 ;'></i>
					Inserir Cômodo
					</p>
				</div>
			</div>
			</a>
		</div>
		<div class='row'>
		<a href='?id=configurar&setor=novoComando'>
			<div id='itemDiv' >
				<div >
					<p style='margin:10px 0 0 0; font-size:18px;'>
					<i class='fa fa' aria-hidden='true' style='font-size:22px;color:whrite;margin:0px 0 0 0 ;'></i>
						Configurar Ação
					</p>
				</div>
			</div>
		</div>

		<div class='row'>
		<a href='?id=configurar&setor=parametros'>
			<div id='itemDiv' >
				<div >
					<p style='margin:10px 0 0 0; font-size:18px;'>
					<i class='fa fa' aria-hidden='true' style='font-size:22px;color:whrite;margin:0px 0 0 0 ;'></i>
					Parametros Gerais
					</p>
				</div>
			</div>
		</div>";
		voltar('?id=menu');
		exit;

	}
	if ($id == 'comodos') {
		echo"
		<script>
			setInterval('window.location.reload()', 5000);
		</script>";
		if($setor==2){
			echo"
			<div class='row'>
				<div id='itemDivDetalhe' >
					<div >
						<p style='margin:10px 0 0 0; font-size:18px;'>";
							echo $com;
						echo"		
						</p>
					</div>
				</div>
			</div>";
			$itensBanco = 'select * from itens where comodo ="'.$com.'" order by nome';
			if(!$query = $mysqli->query($itensBanco)){
				echo "Errorcode: ".$mysqli->errno;
				echo "<br>Errormessage: ".$mysqli->error;
				$log_registro = "Errorcode: ".$mysqli->errno." Errormessage: ".$mysqli->error;
				Log_error($log_registro,$data);
				exit();
			};
			//VERIFICA SE TEM RESULTADOS PARA A CONSULTA DE ITENS DO COMODO
			if(mysqli_num_rows($query)<=0){
				echo '
				<br>
				<div class="alert alert-danger" role="alert">
					<strong>Atenção:</strong> 
					ERRO: Não há comandos configurados para este comôdo!
				</div>';
				voltar('?id=comodos');
				exit;
			}
			//QUEBRANDO O RESULTADO EM UM ARRAY
			$n=0;
			while ($r = mysqli_fetch_assoc($query)) {
				//print_r($r);
				if($r['acao'] == 'sensor'){
					$sensorCad [$r['type']] =array($r['comodo'],$r['nome'],$r['type'],$r['central'],$r['porta']);
				}
				if($r['acao'] == 'lampada'){
					$lampadaCad [] =array($r['type'],$r['comodo'],$r['nome'],$r['type'],$r['central'],$r['porta']);
				}
				if($r['acao'] == 'tomada'){
					$tomadaCad [] =array($r['type'],$r['comodo'],$r['nome'],$r['type'],$r['central'],$r['porta']);
				}
				$n++;
			}
			
 			//print_r($sensorCad);
			//echo "<br>";			
			//print_r(count($lampadaCad));
			//echo "<br>";			
			//print_r($tomadaCad);
			echo "<br>";
 			if(isset($sensorCad)){
				if($sensorCad['dht11'][2] == 'dht11'){
					$central = $sensorCad['dht11'][3];
					$porta = $sensorCad['dht11'][4];
					$querySql = "select * from sensores where central='$central' and porta = '$porta' order by data desc limit 1";
					$query = $mysqli->query($querySql);
					if(mysqli_num_rows($query)> 0){
						while($resultado = mysqli_fetch_assoc($query)){
							$a =$resultado['valor']; 
						}
						$valor = explode(";",$a);
						$identificacao2 ="<img src='ico/termometro.png' style='width:20px;'/> $valor[1] ºC"; 
						$v = "<img src='ico/nuvem.png' style='width:35px;'/> $valor[2] %";
						echo"
							<table>
								<tr style='height:50px'>
									<td>
										$identificacao2
									</td>				
									<td>
										$v
									</td>
								</tr>
							</table>";
					}
				}
					if($sensorCad['mq-2'][2] == 'mq-2'){
					$central = $sensorCad['mq-2'][3];
					$porta = $sensorCad['mq-2'][4];
					$querySql = "select * from sensores where central='$central' and porta = '$porta' order by data desc limit 1";
					$query = $mysqli->query($querySql);
						if(mysqli_num_rows($query) > 0){
							while($resultado = mysqli_fetch_assoc($query)){
								$a =$resultado['valor']; 
							}
							$valor = explode(";",$a);
							if($valor[1] < 249){
								$identificacao3 ="Gás-Fumaça"; 
								$v2= "Ausente:($valor[1])";
							}else{
								$identificacao3 ="<div style='background:red'>Gás-Fumaça"; 
								$v2="<div style='background:red'>Detectado: ($valor[1])</div>";
							}
							echo"
								<table>
									<tr style='height:50px'>
										<td>
											$identificacao3
										</td>				
										<td>
											$v2
										</td>
									</tr>
								</table>";
						}
					}
				}
			
			
			if(isset($lampadaCad)){
				echo "<table>";
				for($i=0;$i<=count($lampadaCad)-1;$i++){
					$comodo = $lampadaCad[$i][1];
					$nome = $lampadaCad[$i][2];
					$tipo = $lampadaCad[$i][3];
					$central= $lampadaCad[$i][4];
					$porta = $lampadaCad[$i][5];
					$queryStatusP = "SELECT acao FROM `portasoutput` WHERE numero = '$porta' and central = '$central' order by data desc limit 1";
					if(!$resultStatusPorta = $mysqli->query($queryStatusP)){
						echo "Errorcode: ".$mysqli->errno;
						echo "<br>Errormessage: ".$mysqli->error;
						$log_registro = "Errorcode: ".$mysqli->errno." Errormessage: ".$mysqli->error;
						Log_error($log_registro,$data);
						exit();
					};
					$statusPorta = mysqli_fetch_row($resultStatusPorta);
					if($statusPorta[0] == "liga"){
						$check = 'checked';
						}else{
							$check = '';
							}
					if($tipo == 'range'){
						$classe ="";
						$atributos = "onclick=\"myFunction()\" id='' name='$nome'";
						$input = "<input type='$tipo' $atributos/>";
						$span='';
					}
					if($tipo == 'checkbox'){
						$atributos = "onclick=\"verificarCheckBox('$nome','$central',$porta)\" name='$nome' $check";
						$classe ="<label class='switch'>";
						$input = "<input type='$tipo' $atributos/>";
						$span = "<span class='slider round'></span></label>";
					}
					echo"
						<tr>
							<td>
								$nome
							</td>
							<td >
								$classe
								$input
								$span
							</td>
						</tr>";
				}
				echo "</table>";
			}
			if(isset($tomadaCad)){
				echo "<table>";
				for($i=0;$i<=count($tomadaCad)-1;$i++){
					$comodo = $tomadaCad[$i][1];
					$nome = $tomadaCad[$i][2];
					$tipo = $tomadaCad[$i][3];
					$central= $tomadaCad[$i][4];
					$porta = $tomadaCad[$i][5];
					$queryStatusP = "SELECT acao FROM `portasoutput` WHERE numero = '$porta' and central = '$central' order by data desc limit 1";
					if(!$resultStatusPorta = $mysqli->query($queryStatusP)){
						echo "Errorcode: ".$mysqli->errno;
						echo "<br>Errormessage: ".$mysqli->error;
						$log_registro = "Errorcode: ".$mysqli->errno." Errormessage: ".$mysqli->error;
						Log_error($log_registro,$data);
						exit();
					};
					$statusPorta = mysqli_fetch_row($resultStatusPorta);
					if($statusPorta[0] == "liga"){
						$check = 'checked';
						}else{
							$check = '';
							}
					if($tipo == 'range'){
						$classe ="";
						$atributos = "onclick=\"myFunction()\" id='' name='$nome'";
						$input = "<input type='$tipo' $atributos/>";
						$span='';
					}
					if($tipo == 'checkbox'){
						$atributos = "onclick=\"verificarCheckBox('$nome','$central',$porta)\" name='$nome' $check";
						$classe ="<label class='switch'>";
						$input = "<input type='$tipo' $atributos/>";
						$span = "<span class='slider round'></span></label>";
					}
					echo"
						<tr>
							<td>
								$nome
							</td>
							<td >
								$classe
								$input
								$span
							</td>
						</tr>";
				}
				echo "</table>";
			}
			
			
			voltar('?id=comodos');
			exit;
		}
		echo"
		<div class='row'>
			<div id='itemDivTitulo' >
				<div >
					<p style='margin:10px 0 0 0; font-size:22px;'>
					<img src='ico/comodo.png' style='width:30px;'/>
					<!--<i class='fa fa-hotel' aria-hidden='true' style='font-size:22px;color:whrite;margin:0px 0 0 0 ;'></i>-->
						Cômodos
					</p>
				</div>
			</div>
		</div>";
		//$comodos= array('Cozinha','Sala','Quarto','Quarto');
		$querySql ='SELECT * FROM comodos';
		if(!$query = $mysqli->query($querySql)){
			echo "Errorcode: ".$mysqli->errno;
			echo "<br>Errormessage: ".$mysqli->error;
			$log_registro = "Errorcode: ".$mysqli->errno." Errormessage: ".$mysqli->error;
			Log_error($log_registro,$data);
			exit();
		};
		if(mysqli_num_rows($query)<=0){
				echo "</br>Não há comodos configurado no banco de dados!";
				voltar('?id=menu');
				exit;
			}
		$c = 0;
		while ($r = mysqli_fetch_array($query)) {
			$comodos[$c]= $r['nome'];
			$c++;
		}
		for($i=0; $i<=count($comodos)-1;$i++){
			echo "
			<div class='row'>
				<div id='itemDiv' style='margin:10px 0 0 0;padding:20px;' >
					<div id='tda'>
						<a href='?id=comodos&setor=2&com=$comodos[$i]' id=''><p>$comodos[$i]</p></a>
					</div>
				</div>
			</div>";
		}
		voltar('?id=menu');
	}
	if ($id == 'lampadas'){
		echo"
		<script>
			setInterval('window.location.reload()', 5000);
		</script>";

		$querySql = "select * from itens where acao = 'lampada' ";
		if(!$query = $mysqli->query($querySql)){
			echo "Errorcode: ".$mysqli->errno;
			echo "<br>Errormessage: ".$mysqli->error;
			$log_registro = "Errorcode: ".$mysqli->errno." Errormessage: ".$mysqli->error;
			Log_error($log_registro,$data);
			exit();
		};
		if(mysqli_num_rows($query)<=0){
			echo "</br>Não há comandos configurados para este cômodo!";
			voltar('?id=menu');
			exit;
		}
		while ($r = mysqli_fetch_assoc($query)) {
			$itenComodo[]= $r;
		}
		$itensType = array('text','range','checkbox','submit');
		echo"
			<div id='itemDivDetalhe' >
				<p style='margin:0px 0 0 0; font-size:22px;'>
					<img src='ico/lampada.png' style='width:25px;'/>
					<!--<i class='fa fa-hotel' aria-hidden='true' style='font-size:22px;color:whrite;margin:0px 0 0 0 ;'></i>-->
					Lâmpadas
				</p>
			</div>";
		echo "<div id='itemDivDetalhe2' >
				<div >
					<table>";
		$n=0;
		$portasInterruptor = 0;
		for ($row = 0; $row <= count($itenComodo)-1; $row++) {
			$type = $itenComodo[$row]['type'];
			$chamadaGet = $itenComodo[$row]['porta'];
			$identificacao = $itenComodo[$row]['nome'].' - '.$itenComodo[$row]['comodo'];
			$central = $itenComodo[$row]['central'];
			$queryStatusP = "SELECT acao FROM `portasoutput` WHERE numero = '$chamadaGet' and central = '$central' order by data desc limit 1";
			if(!$resultStatusPorta = $mysqli->query($queryStatusP)){
				echo "Errorcode: ".$mysqli->errno;
				echo "<br>Errormessage: ".$mysqli->error;
				$log_registro = "Errorcode: ".$mysqli->errno." Errormessage: ".$mysqli->error;
				Log_error($log_registro,$data);
				exit();
			};
			$statusPorta = mysqli_fetch_row($resultStatusPorta);
			//print_r($statusPorta[0]);

			if($statusPorta[0] == "liga"){
				$check = 'checked';
				}else{
					$check = '';
					}
			if($type == 'range'){
				$classe ="";
				$atributos = "onclick=\"myFunction()\" id='' name='$chamadaGet'";
				$input = "<input type='$type' $atributos/>";
				$span='';
			}
			$nPorta = 0;
			if($type == 'checkbox'){
				$portasInterruptor.=';'.$chamadaGet;
				$atributos = "onclick=\"verificarCheckBox('$chamadaGet','$central')\" name='$chamadaGet' $check";
				$classe ="<label class='switch'>";
				$input = "<input type='$type' $atributos/>";
				$span = "<span class='slider round'></span></label>";
			}
			if ($row < count($itenComodo)-1){

			}else{
			echo"
				<div>
					<button id=''
					onClick='javascript:location.href=\"?id=lampadas&setor=todas&exec=ativar&portas=$portasInterruptor&central=$central\"''
					name='ativar' class='btn btn-success'>Ligar Tudo</button>
					<button id=''
					onClick='javascript:location.href=\"?id=lampadas&setor=todas&exec=desativar&portas=$portasInterruptor\"''
					name='desativar' class='btn btn-danger'>Desligar Tudo</button>
				</div></br>";
			}
			echo"
				<tr>
					<td>
						$identificacao
					</td>
					<td style='text-aligin:right'>
						$classe
						$input
						$span
					</td>
				</tr>
			";
		}
		echo"</table>
			</div>
		</div>";
		if ($exec == 'ativar'){
			foreach($_GET as $key=>$value){
				$getDados[$key] = $value;
			}
			$portasAtivas = explode(';',$getDados['portas']);
			for ($i=1; $i<=count($portasAtivas)-1;$i++){
				
				$query = "INSERT INTO `portasoutput`(`data`, `numero`, `central`, `acao`) VALUES ('$data','$portasAtivas[$i]','$central','liga')";
				$resultStatusPorta = $mysqli->query($query);
				$url = "http://".$central."/?porta=".$portasAtivas[$i]."&acao=liga&central=".$central;
				//$url2 = "gravar.php?porta=".$portasAtivas[$i]."&acao=liga&central=".$central;
				echo '
					<script language="javascript" type="text/javascript">
						ligarTodos("'.$url.'");
					</script>';
				};
			echo"
			<script>location.href=('?id=lampadas');</script>";
			LogBanco($querySql,$data);
		}
		if ($exec == 'desativar'){
			foreach($_GET as $key=>$value){
				$getDados[$key] = $value;
			}
			$portasAtivas = explode(';',$getDados['portas']);
			for ($i=1; $i<=count($portasAtivas)-1;$i++){
				$query = "INSERT INTO `portasoutput`(`data`, `numero`, `central`, `acao`) VALUES ('$data','$portasAtivas[$i]','$central','desligar')";
				$resultStatusPorta = $mysqli->query($query);
				$url = "http://".$central."/?porta=".$portasAtivas[$i]."&acao=desligar&central=".$central;
				//$url2 = "gravar.php?porta=".$portasAtivas[$i]."&acao=desligar&central=".$central;
				echo '
					<script language="javascript" type="text/javascript">
						ligarTodos("'.$url.'");
					</script>';
				};
			echo"
			<script>location.href=('?id=lampadas');</script>";
			LogBanco($querySql,$data);

		}
		voltar('?id=menu');
		exit;
	}
	if ($id == 'tomadas'){
		echo"
		<script>
			setInterval('window.location.reload()', 5000);
		</script>";

		$querySql = "select * from itens where acao = 'tomada'";
		if(!$query = $mysqli->query($querySql)){
			echo "Errorcode: ".$mysqli->errno;
			echo "<br>Errormessage: ".$mysqli->error;
			$log_registro = "Errorcode: ".$mysqli->errno." Errormessage: ".$mysqli->error;
			Log_error($log_registro,$data);
			exit();
		};
		if(mysqli_num_rows($query)<=0){
			echo "</br>Não há comandos configurado com esta descrição!";
			voltar('?id=menu');
			exit;
		}
		while ($r = mysqli_fetch_assoc($query)) {
			$itenComodo[]= $r;
		}
		$itensType = array('text','range','checkbox','submit');
		echo"
			<div id='itemDivTitulo' >
				<p style='margin:10px 0 0 0; font-size:22px;'>
					<img src='ico/tomada.png' style='width:30px;'/>
					<!--<i class='fa fa-hotel' aria-hidden='true' style='font-size:22px;color:whrite;margin:0px 0 0 0 ;'></i>-->
						Tomadas
				</p>
			</div>";
		echo "<div id='itemDivDetalhe2' >
				<div >
					<table>";
		$n=0;
		$portasInterruptor = 0;
		for ($row = 0; $row <= count($itenComodo)-1; $row++) {
			$type = $itenComodo[$row]['type'];
			$chamadaGet = $itenComodo[$row]['porta'];
			$identificacao = $itenComodo[$row]['nome'].' - '.$itenComodo[$row]['comodo'];
			$central = $itenComodo[$row]['central'];
			$queryStatusP = "SELECT acao FROM `portasoutput` WHERE numero = '$chamadaGet' and central = '$central' order by data desc limit 1";
			if(!$resultStatusPorta = $mysqli->query($queryStatusP)){
				echo "Errorcode: ".$mysqli->errno;
				echo "<br>Errormessage: ".$mysqli->error;
				$log_registro = "Errorcode: ".$mysqli->errno." Errormessage: ".$mysqli->error;
				Log_error($log_registro,$data);
				exit();
			};

			$statusPorta = mysqli_fetch_row($resultStatusPorta);
			//print_r($statusPorta[0]);

			if($statusPorta[0] == "liga"){
				$check = 'checked';
				}else{
					$check = '';
					}
			if($type == 'range'){
				$classe ="";
				$atributos = "onclick=\"myFunction()\" id='' name='$chamadaGet'";
				$input = "<input type='$type' $atributos/>";
				$span='';
			}
			$nPorta = 0;
			if($type == 'checkbox'){
				$portasInterruptor.=';'.$chamadaGet;
				$atributos = "onclick=\"verificarCheckBox('$chamadaGet','$central')\" name='$chamadaGet' $check";
				$classe ="<label class='switch'>";
				$input = "<input type='$type' $atributos/>";
				$span = "<span class='slider round'></span></label>";
			}
			if ($row < count($itenComodo)-1){

			}else{

				echo"
				<div>
					<button id=''
					onClick='javascript:location.href=\"?id=tomadas&setor=todas&exec=ativar&portas=$portasInterruptor&central=$central\"''
					name='ativar' class='btn btn-success'>Ligar Tudo</button>
					<button id=''
					onClick='javascript:location.href=\"?id=tomadas&setor=todas&exec=desativar&portas=$portasInterruptor\"''
					name='desativar' class='btn btn-danger'>Desligar Tudo</button>
				</div></br>";
			}
			echo"
				<tr>
					<td>
						$identificacao
					</td>
					<td style='text-aligin:right'>
						$classe
						$input
						$span
					</td>
				</tr>
			";
		}
		echo"</table>
			</div>
		</div>";
		if ($exec == 'ativar'){
			foreach($_GET as $key=>$value){
				$getDados[$key] = $value;
			}
			$portasAtivas = explode(';',$getDados['portas']);
			for ($i=1; $i<=count($portasAtivas)-1;$i++){
				$url = "http://".$central."/?porta=".$portasAtivas[$i]."&acao=liga&central=".$central;
				$url2 = "gravar.php?porta=".$portasAtivas[$i]."&acao=liga&central=".$central;
				echo '
					<script language="javascript" type="text/javascript">
						ligarTodos("'.$url2.'");
						ligarTodos("'.$url.'");
					</script>';
				};
			echo"
			<script>location.href=('?id=tomadas');</script>";
		}
		if ($exec == 'desativar'){
			foreach($_GET as $key=>$value){
				$getDados[$key] = $value;
			}
			$portasAtivas = explode(';',$getDados['portas']);
			for ($i=1; $i<=count($portasAtivas)-1;$i++){
				$url = "http://".$central."/?porta=".$portasAtivas[$i]."&acao=desligar&central=".$central;
				$url2 = "gravar.php?porta=".$portasAtivas[$i]."&acao=desligar&central=".$central;
				echo '
					<script language="javascript" type="text/javascript">
						ligarTodos("'.$url2.'");
						ligarTodos("'.$url.'");
					</script>';
				};
			echo"
			<script>location.href=('?id=tomadas');</script>";

		}
		voltar('?id=menu');
		exit;
	}
	?>
</div>
</body>
</html>