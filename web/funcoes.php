<?php

function voltar($link){
	echo"
		<div class='row'>
			<div id='' >
				<div style='text-align:right'>
					<p style='margin:10px 0 0 0; font-size:18px;'>
					<a href='$link'>
					<img src='ico/esquerda.png' style='width:25px;'/>
					<!--<i class='fa fa-arrow-circle-left' aria-hidden='true' style='font-size:22px;color:whrite;margin:0px 0 0 0 ;'> </i>-->  
							Voltar
						</p>
					</a>
				</div>
			</div>
		</div>	
		";
	
}
function Log_error($log_registro,$today)
	{
		$name = 'logError.txt';
		$text = $today.' '.$log_registro.PHP_EOL;
		$file = fopen($name,"a",0);
		fwrite($file, $text);
		fclose($file);
	}
function LogBanco($valor,$data){
		include 'classes.php';
		$dados = mysql_real_escape_string($valor);
		$querySqlLog = "INSERT INTO log (`id`, `valor`, `data`) VALUES ('','$dados','$data')";
		$mysqli->query($querySqlLog);	
	}
?>