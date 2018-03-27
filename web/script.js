	function mascara(t, mask){
		 var i = t.value.length;
		 var saida = mask.substring(1,0);
		 var texto = mask.substring(i)
		 if (texto.substring(0,1) != saida){
			t.value += texto.substring(0,1);
		 }
	 }
	function verificarCheckBox(nome,central,porta) {
		var check = document.getElementsByName(nome); 
		for (var i=0;i<check.length;i++){ 
			if(nome == 'alarme'){
				if (check[i].checked == true){ 
					var url = "http://192.168.0.177/?alarme=1";
					comandoCentral(url);
				}  else {
					var url = "http://192.168.0.177/?alarme=0";
					comandoCentral(url);
				}
			}else{
				if (check[i].checked == true){ 
					var url = "http://"+central+"/?porta="+porta+"&acao=liga&central="+central;
					var url2 = "http://127.0.0.1/web/gravar.php?porta="+porta+"&acao=liga&central="+central;
					gravaBanco(url2);
					comandoCentral(url);
				}  else {
					var url = "http://"+central+"/?porta="+porta+"&acao=desligar&central="+central;
					var url2 = "http://127.0.0.1/web/gravar/gravar.php?porta="+porta+"&acao=desligar&central="+central;
					gravaBanco(url2);
					comandoCentral(url);
				}
			}
		}
		 var gravarBD;
		function gravaBanco(gravarBD) {
		 var xhttp = new XMLHttpRequest();
		  xhttp.open("GET",gravarBD, true);
		  xhttp.send();
		  gravarBD = 0;
		}
		var acao
		function comandoCentral(acao) {
		 var xhttp = new XMLHttpRequest();
		  xhttp.open("GET",acao, true);
		  xhttp.send();
		  acao = 0;
		}
	}
	var ligar
	function ligarTodos(ligar) {
	 var xhttp = new XMLHttpRequest();
	  xhttp.open("GET",ligar, true);
	  xhttp.send();
	  ligar = 0;
	}	
	function myFunction() {
		var x = document.getElementById("myRange").value;
		document.getElementById("demo").innerHTML = x;
	}
	function myFunction() {
    alert("Hello\nHow are you?");
}
