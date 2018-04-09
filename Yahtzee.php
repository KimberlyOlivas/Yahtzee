<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Finger+Paint" > 
<link rel="stylesheet" href="style.css">
<link rel="icon" type="image/png" href="/images/icono.gif" />

<title>Yahtzee | Juego</title>
</head>
<body>

<?php

$jugadas = ["1's","2's","3's","4's","5's","6's",
			"Tres de un Tipo","Cuatro de un Tipo","FullHouse",
			"Escalera Corta","Escalera Larga","Chance","Yahtzee"];	
$datos_juego;
$suma1_6 = 0;  //Suma las jugadas 1 hasta 6
$bono = 0;
$suma7_13 = 0; //Suma las jugadas 7 hasta 13
$jugar = 'enabled';

leeJuego($datos_juego);
obtenerDados();
obtenerLanzamientos();
muestra_juego();
jugar();
obtenerJugadas(); 

function muestra_juego(){ //Muestra la pantalla con el juego actual
	global $dados;
	global $suma1_6;
	global $bono;
	global $suma7_13;
	global $datos_juego;
	$lanzamientos = $datos_juego[0];
	
	echo "<div id='cont1'>";
	echo "<h1> Yahtzee </h1>";
	echo "<div id='cont2'>";
	echo "<table class='tabla'>";	
	echo "<tr>";
	
		echo "<td>";
			echo "<table class='tabjugadas'>";
			echo "<thead>";
				echo "<tr>";
					echo "<th class='th1'> Jugadas </th>";
					echo "<th class='th2'> Puntos </th>";
				echo "</tr>";
			echo "</thead>";
			echo "<tbody>";
				for ($i=0; $i<6; $i++)					
					echo "<tr>".jugadas($i)."</tr>";	
				echo dibujarEtiqueta("Suma: ", $suma1_6);
				echo dibujarEtiqueta("Bono: ", $bono);	
				for ($i=6; $i<13; $i++)					
					echo "<tr>".jugadas($i)."</tr>";			
				echo dibujarEtiqueta("Total: ", $suma7_13);
			echo "<tbody>";
			echo "</table>";	
		echo "</td>";
		
		echo "<td>";
		echo "<table class='tabdados'>";	
			echo "<tbody>";
				echo "<form action='Yahtzee.php' method='post'>";
				for ($i=0; $i<5; $i++) {
					echo "<tr><td>\n";
					echo "<img name=d$i src=images/".$dados[$i].".png>";
					echo "&nbsp&nbsp&nbsp&nbsp&nbsp";
					echo "<input id=dado$i name=$i type='checkbox'".check($i, $lanzamientos).">";
					echo "<label for=dado$i><span></span></label></td></tr>";			
				}	
				
				echo "<tr><td class='img'>";
				for ($i=0; $i<$lanzamientos; $i++)
					echo "<img src=images/estrella.png>";
				echo "</td></tr>";
				echo "<tr><td>";
				echo "<button type=submit class='btnlanzar' name='lanzar'".habilitarLanzar($lanzamientos).">";
				echo "<span>Lanzar</span></button>";
				echo "</td></tr>\n";	
				echo "</tr>";
				echo "</form>\n";				
			echo "</tbody>";
			echo "</table>";
		echo "</td>";
		
	echo "</tr>";	
	echo "</table>";
	echo "</div>";
	echo "</div>";
}

function dibujarEtiqueta($jugada, $puntos){ //Crea los espacios para los acumuladores (suma, bono y total)
	echo "<tr><td class='etiqueta'>$jugada</td>";
	echo "<td class='acumulador'>$puntos</td></tr>";
}

function dibujarBoton($i, $jugada, $puntos, $atr){ //Crea los botones para las 13 jugadas
	echo "<td class='jugada'>";
	$cont= ($puntos==0)? 'f' : 't';
	echo "<a href=Yahtzee.php?j=$i&c=$cont>";
	$id = ($atr == 'disabled')? 'gris' : 'verde';
	echo "<button id=$id class='btnjugar' ".habilitarJugar($atr).">";
	echo "$jugada</button></a></td>";
	echo "<td class='puntaje'>";
	if($atr == 'disabled') echo "<b>";
	echo "$puntos</td>";
}

function check($i, $lanzamientos){ //Selecciona los checkboxes de los dados lanzados
	if(isset($_POST[$i]) || $lanzamientos == 0)
		return 'checked';
	else
		return '';
}

function habilitarLanzar($i){ //Habilita o deshabilita el botón de lanzar
	return ($i >= 3)? 'disabled': 'enabled';
}

function habilitarJugar($atr){ //Habilita o deshabilita los botones de jugadas
	global $jugar;
	return ($jugar == 'disabled')? $jugar : $atr;
}

function confirmar($i){	//Muestra el mensaje de confirmación en caso de que la jugada eligida tenga 0 puntos
	echo "<script type='text/javascript'>
			var cont = confirm('Esta jugada tiene 0 puntos. ¿Desea utilizarla de todos modos?');
			if(cont) window.location.href = 'Yahtzee.php?j=$i&c=t';
	</script>";
}

function puntajeFinal($total){ //Muestra el mensaje de alerta de fin de juego y su puntaje
	echo "<script type='text/javascript'>
			alert('¡Fin del Juego! Su puntuación es de: $total puntos');
			window.location.href = 'Yahtzee.php';
	</script>";
}

function jugadas($i){ //Establece las jugadas (verifica si puede usarse o no)
	global $datos_juego;
	global $jugadas;
	
	if ($datos_juego[$i+6] == -1)
		dibujarBoton($i+1, $jugadas[$i], puntaje($i), 'enabled');
	else {
		acumuladores($i);			
		dibujarBoton($i+1, $jugadas[$i], $datos_juego[$i+6], 'disabled');
	}
}

function puntaje($i){ //Asigna el puntaje según la jugada
	global $dados;
	$puntos = 0;
	
	switch ($i) {
		case 0: case 1: case 2: 
		case 3: case 4: case 5: 
			$puntos = iguales($dados, $i+1);
			break;
		case 6:
		    $puntos = x_iguales($dados, 3);
			break;
		case 7:
		    $puntos = x_iguales($dados, 4);
			break;	
		case 8:
		    $puntos = full_House($dados);
			break;
		case 9:
		    $puntos = escalera_corta($dados);
			break;	
		case 10:
		    $puntos = escalera_larga($dados);
			break;
		case 11:
		    $puntos = chance($dados);
			break;				
		case 12:
		    $puntos = yahtzee($dados);
			break;	
		default:
			break;
	}	
	return $puntos;
}

function acumuladores($i){ //Asigna el puntaje de los acumuladores (suma, bono y total)
	global $datos_juego;
	global $suma1_6;
	global $suma7_13;
	global $bono;
	
	if($i < 6)
		$suma1_6 += $datos_juego[$i+6];
	else
		$suma7_13 += $datos_juego[$i+6];
	
	if ($suma1_6 > 62)
		$bono = 30;
	else
		$bono = 0;		
}

function iguales($vector, $n){ //Devuelve la suma de las caras con el mismo número
	$suma = 0;
	for ($i=1;$i<5;$i++)
	     if ($vector[$i] == $n)
		     $suma += $n;		
	return $suma;	
}

function x_iguales($vector, $n){ //Devuelve la suma de 3 o 4 caras con el mismo número
	for ($valor=1; $valor<7; $valor++) {
		$contador = 0;
	    for ($i=0; $i<5; $i++)
            if ($vector[$i] == $valor){
				$contador++;
				if ($contador == $n)
					return array_sum($vector);
			}		 
	}
	return 0;
}

function full_House($vector){ //Devuelve 25 si hay 3 y 2 o 2 y 3 caras con el mismo número
	sort($vector);
    if ($vector[0] == $vector[1] && $vector[1] == $vector[2] && $vector[3] == $vector[4])
        return 25;

    if ($vector[0] == $vector[1] && $vector[2] == $vector[3] && $vector[3] == $vector[4])
        return 25;

    return 0;
}

function escalera_corta($vector){ //Devuelve 30 si hay 4 dados con números en secuencia (1234X o 2345X o 3456X)
	$v = array_unique ($vector);
    $v1 = array_values($v);
	$n = count($v1);	
	
	if ($n < 4)
		return (0);	
	sort ($v1);
    $cont = 0;
    for ($i=0; $i < $n-1; $i++) {
        if ($v1[$i] == ($v1[$i+1]-1)){
            if(++$cont==3)
                return 30;
        }
        else 
            $cont=0;
    }
    return  0;
}

function escalera_larga($vector){ //Devuelve 40 si hay 5 dados con números en secuencia (12345 o 23456)
	sort ($vector);
    for ($i=0; $i<4; $i++)
         if ($vector[$i] != $vector[$i+1]-1)
			 return 0;	 
	return 40;
}

function chance($vector){ //Devuelve la suma de los números de las 5 caras
	return array_sum($vector);
}

function yahtzee($vector){ //Devuelve 50 si todos los números de las caras son iguales
	for ($i=1;$i<5;$i++)
	     if ($vector[0] != $vector[$i])
		     return 0;	
	return 50;
}

function obtenerDados(){ //Obtiene las caras de los dados (si fue seleccionado lo lanza de nuevo, sino lo conserva)
	global $datos_juego;
	global $dados;
			
	for($i=0; $i<5; $i++) {
		if(isset($_POST[$i]))
			$datos_juego[$i+1] = rand(1,6);
		$dados[$i] = $datos_juego[$i+1];		
	}
}

function obtenerLanzamientos(){ //Obtiene la cantidad de lanzamientos y habilita/deshabilita las jugadas según dicha cantidad
	global $datos_juego;
	global $jugar;
	if(isset($_POST['lanzar'])) {
		$jugar = 'enabled';
		$datos_juego[0] = $datos_juego[0]+1;
		escribeJuego($datos_juego);
	}
	if($datos_juego[0] == 0)
		$jugar = 'disabled';
}

function obtenerJugadas(){ //Obtiene la jugada utilizada para validarla (si continuar con 0 puntos o no)
	global $datos_juego;
	if(isset($_GET['j'])) {
		$i = (int)$_GET['j'];
		if($_GET['c'] == 't'){ //+0 puntos o confirmación
			guardarJugada($i);
		}else{ //'f' 0 puntos
			escribeJuego($datos_juego);
			confirmar($i);	
		}
	}
}

function guardarJugada($i){ //Guarda el puntaje de la jugada si ha sido seleccionada por primera vez
	global $datos_juego;
	if($datos_juego[$i+5] == -1){
		$datos_juego[0] = 0;
		$datos_juego[$i+5] = puntaje($i-1);
		escribeJuego($datos_juego);
		header("Refresh:0");
	}
}

function jugar(){ //Verifica si es el final del juego para reiniciar los datos y mostrar el puntaje total
	global $suma1_6;
	global $suma7_13;
	global $bono;
	if(finJuego()){
		escribeJuego([0,1,2,3,4,5,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1]);
		puntajeFinal($suma1_6+$bono+$suma7_13);
	}	
}

function finJuego(){ //Verifica que todas las jugadas tengan puntaje (es decir que todas se hayan usado)
	global $datos_juego;
	for($i=6; $i< 18; $i++){
		if($datos_juego[$i] == -1)
			return false;
	}
	return true;
}

function escribeJuego($datos_juego) { //Guarda el estado actual del juego
	$ArchivoYahtzee = "MiYahtzee.txt";
    $Handle = fopen($ArchivoYahtzee, 'w');
    $Data = implode(";", $datos_juego);
    fwrite($Handle, $Data);
    fclose($Handle);
}

function leeJuego(&$datos_juego) { //Levanta el estado del juego
    $ArchivoYahtzee = "MiYahtzee.txt";
    if (file_exists($ArchivoYahtzee)) {
        $Handle = fopen($ArchivoYahtzee, 'r');    
        $Data = fread($Handle, filesize($ArchivoYahtzee));
        fclose($Handle);
    }
    else
        $Data = "0;1;2;3;4;5;-1;-1;-1;-1;-1;-1;-1;-1;-1;-1;-1;-1;-1;";
    $datos_juego = explode(";", $Data);
}

?>
</body>
</html>