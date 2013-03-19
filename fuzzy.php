<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">
<html>
   <head>
		<meta name="description" content="Free Web tutorials">
		<meta name="keywords" content="HTML,CSS,XML,JavaScript">
		<meta name="author" content="Ståle Refsnes">
		<meta charset="UTF-8">
	</head>
	<body>
<?php
include ('Utils.php');
try{
	$entradas = array(
	/*2004*/-27.75,1.47,4.29,6.51,-4.22,-2.79,7.76,-1.24,-1.45,7.99,-3.24,36.31,
	/*2005*/-25.97,-4.22,16.09,-8.23,-0.35,-3.85,6.63,-3.3,-2.1,7.13,-3.01,35.6,
	/*2006*/-25.38,-3.99,6.14,5.23,-8.54,-2.07,4.78,-1.12,1.12,2.32,1.18,31.79,
	/*2007*/-23.29,-2.41,10.79,-0.77,-4.51,-0.9,1.56,2.37,0.47,1.63,1.88,33.56,
	/*2008*/-22.84,-2.35,16.76,-12.13,8.66,-5.65,4.4,3.57,-5.45,7.22,1.19,27.86,
	/*2009*/-22.59,-4.12,6.68,7.2,-3.67,-5.25,5.92,0.95,-3.77,8.33,-2.24,31.68,
	/*2010*/-20.96,-5.54,10.74,-3.28,0.46,-4.59,4.21,-1.37,-0.11,7.82,-4.43,34.71
	);
	$saidasEsperadas = array(
	/*2011*/-20.49,-6.35,10.14,8,-10.92,-2.49,6.41,-1.84,-0.22,5.16,-1.84,30.66,
	/*2012*/-18.91,0.27,7.54,-1.37,-2.37,-5.39,-0.08,2.12,0.79,2.80,2.13,29.73
	);

	$nEntradas = count($entradas);
	$janela = 13;


	$resultadoPasso3 = array();

	$todasRegras = array();



	//definindo limites
	list($limInferior,$limSuperior) = array(-34.16,42.72);
	$labelRotulos = array('mb','b','m','a','ma');
	$numRotulos = count($labelRotulos);

	for ($recorte=0; ($recorte + $janela) <= $nEntradas ; $recorte++) { 

		$arr_Janela = array_slice($entradas, $recorte ,$janela);

		$final = array();
		//PASSO 1 - DIVIDIR OS ESPAÇOS DE ENTRADA E SAÍDA EM CONJUNTOS FUZZY
		//Define os intervalos de cada rótulo
		$intervaloRotulos = defineIntervalosTRI($limInferior,$limSuperior,$numRotulos);
		$retorno = array();	

		//PASSO 2 - GERAR REGRAS
		for ($i=$recorte; $i < ( ($recorte + $janela)) ; $i++) {
			$x = $arr_Janela[$i];
			$temp = array();
			for ($j=0; $j < $numRotulos; $j++) {
				$f = $intervaloRotulos[$j];
				$e = ($j - 1) < 0 ? $limInferior-1: $intervaloRotulos[$j - 1];
				$g = ($j + 1) > $numRotulos ? $limSuperior+1: $intervaloRotulos[$j + 1];
				$temp[$labelRotulos[$j]] = TRI($x,$e,$f,$g);
			}
			$nI = getIndexMes($i,$janela);
			$retorno[getMesExtenso($nI).$nI] = $temp;
		}

		//PASSO 3 - ATRIBUIR GRAU A CADA REGRA
		$grauRegra = 1.0;//Número neutro para multiplicação
		foreach ($retorno as $mesExtenso => $mes) {
			$maiorGrauPertinenciaValor = 0.0;
			$maiorGrauPertinenciaRotulo = '';
			foreach ($mes as $rotulo => $valorRotulo) {
				if($valorRotulo > $maiorGrauPertinenciaValor) {
					$maiorGrauPertinenciaValor = $valorRotulo;
					$maiorGrauPertinenciaRotulo = $rotulo;
				}
			}
			$grauRegra = $grauRegra * $maiorGrauPertinenciaValor;
		}
		$retorno['grau']= $grauRegra;
		$resultadoPasso3[$recorte] = $retorno;
		//Escolhe o maior valor de cada mes
		$maiorValorMes = array();
		foreach($retorno as $key => $mes){
			if($key != 'grau'){
				$maiorValorMes[] = max($mes);//retorna a primeira posicao
			}
		}
		$todasRegras[] = $maiorValorMes;
		if($recorte == 2){
			break;
		}
	}
	var_dump($resultadoPasso3[1]);
}catch(Exception $e){
	var_dump($e->getMessage());
}
?>
   </body>
</html>