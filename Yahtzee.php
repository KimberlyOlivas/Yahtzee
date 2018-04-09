<!DOCTYPE html>
<html>

<head>
<meta charset="utf-8">

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Finger+Paint" > 
<link rel="stylesheet" href="style.css">
<link rel="icon" type="image/png" href="imagenes/icono.gif" />

<title>Yahtzee | Juego</title>
</head>

<body>

<?php

$con = new mysqli("localhost", "root", "root", "Yahtzee");

if(mysqli_connect_errno()){
	echo "Falló la conexión con MySQL: ". mysqli_connect_error();
	exit();
}

$jugadas = ["1's","2's","3's","4's","5's","6's",
			"Tres de un Tipo","Cuatro de un Tipo","FullHouse",
			"Escalera Corta","Escalera Larga","Chance","Yahtzee"];	
$datos_juego;
$suma1_6 = 0;  //Suma las jugadas 1 hasta 6
$bono = 0;
$suma7_13 = 0; //Suma las jugadas 7 hasta 13
$total = 0;
$jugar;
$id = -1;

nuevo();
leeJuego($datos_juego);
obtenerDados();
obtenerLanzamientos();
obtenerJugadas();
obtenerJugador();
muestraJuego();
crearHistorial();
vaidarJugadas();
fin();

$con->close();

function muestraJuego(){ //Muestra la pantalla con el juego actual
	global $dados;
	global $suma1_6;
	global $bono;
	global $suma7_13;
	global $total;
	global $datos_juego;
	global $jugar;
	$lanzamientos = $datos_juego[2];
	$jugar = ($datos_juego[2] == 0)? 'disabled' :'enabled';
	
	echo "<div id='cont1'>";
	echo "<span id='span1'><h3> Yahtzee </h3></span>";
	echo "<div id='cont2'>";
	echo "<span id='span2'>";
	echo "<span class='input-group'>";
	echo "<form action='Yahtzee.php?id=$datos_juego[0]' method='post'>";
	echo "Jugador:&nbsp&nbsp<input type='text' class='jugador' name='jugador' value=$datos_juego[1] size=9>";
	echo "<button class='btnguardar type='submit' name='guardar'><img src=imagenes/guardar.png></button>";
	echo "</form>&nbsp&nbsp";
	echo "<form action='Yahtzee.php' method='post'>";
	echo "<button class='btnnuevo' name='nuevo' type='submit'>Nuevo</button></form>";
	echo "<button class='btnrecords' type='button' data-toggle='modal' data-target='#puntajes'>Records</button>";	
	echo "</span></span></div>";
	echo "<div id='cont3'>";
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
				$total = $suma1_6 + $bono + $suma7_13;
				echo dibujarEtiqueta("Total: ", $total);
				guardaTotal();
			echo "<tbody>";
			echo "</table>";	
		echo "</td>";
		
		echo "<td>";
		echo "<table class='tabdados'>";	
			echo "<tbody>";
				echo "<form action='Yahtzee.php?id=$datos_juego[0]' method='post'>";
				for ($i=0; $i<5; $i++) {
					echo "<tr><td>\n";
					echo "<img name=d$i src=imagenes/".$dados[$i].".png>";
					echo "&nbsp&nbsp&nbsp&nbsp&nbsp";
					if($lanzamientos != 0) mostrarChecks($i,$lanzamientos);		
				}	
				
				echo "<tr><td class='img'>";
				for ($i=0; $i<$lanzamientos; $i++)
					echo "<img src=imagenes/estrella.png>";
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

function crearHistorial(){	//Muestra el modal de records (mejores puntajes)
	$puntajes = mejoresPuntajes();
	echo"<div class='modal fade' id='puntajes' role='dialog'>
    <div class='modal-dialog'>
      <div class='modal-content'>
        <div class='modal-header'>       
          <h4 class='modal-title'>Mejores Puntajes</h4>
		  <button type='button' class='close' data-dismiss='modal'>&times;</button>
        </div>
        <div class='modal-body'>
		<table class='table table-sm table-striped'>\n
			<thead><tr><th>Posición</th><th>Jugador</th><th>Puntaje</th></tr></thead>
			<tbody>";
		foreach($puntajes as $column => $value){
			if($column > 2) $column = 3;
			echo "<tr><td width='20%'><img src=imagenes/m".$column.".png></td><td>$value[jugador]</td><td>$value[total]</td></tr>";
		}		
		"	</tbody>
		</table>
        </div>
        <div class='modal-footer'>
          <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
        </div>
      </div>
      
    </div>
  </div>";
}

function mejoresPuntajes(){ //Obtiene los 10 puntajes más altos y sus jugadores respectivos
	global $con;
	$listaPuntajes = array();
	$sql = "SELECT total, jugador FROM juego ORDER BY total DESC LIMIT 10";
	$query = $con -> query($sql); 
	while($result = mysqli_fetch_assoc($query)){
		$puntaje = array('jugador' => $result['jugador'],'total' => $result['total']);
		array_push($listaPuntajes, $puntaje);
	}
	return $listaPuntajes;
}

function dibujarEtiqueta($jugada, $puntos){ //Crea los espacios para los acumuladores (suma, bono y total)
	echo "<tr><td class='etiqueta'>$jugada</td>";
	echo "<td class='acumulador'>$puntos</td></tr>";
}

function dibujarBoton($i, $jugada, $puntos, $atr){ //Crea los botones para las 13 jugadas
	global $datos_juego;
	echo "<td class='jugada'>";
	echo "<a name=a href=Yahtzee.php?id=$datos_juego[0]&j=$i&c=".permitirJugada($puntos).">";
	$id = ($atr == 'disabled')? 'gris' : 'verde';
	echo "<button id=$id class='btnjugar' ".habilitarJugar($atr).">";
	echo "$jugada</button></a></td>";
	echo "<td class='puntaje'>";
	if($atr == 'disabled') echo "<b>";
	echo "$puntos</td>";
}

function mostrarChecks($i, $n){ //Muestra los checkboxes
	echo "<input id=dado$i name=$i type='checkbox' ".seleccionarCheck($i)." ".deshabilitarCheck($n).">";
	echo "<label for=dado$i><span></span></label></td></tr>";	
}

function seleccionarCheck($i){ //Selecciona los checkboxes de los dados lanzados
	return (isset($_POST[$i]))? 'checked' : '';
}

function deshabilitarCheck($n){ //Deshabilitar los checkboxes
	return ($n == 3)? 'disabled' : '';
}

function permitirJugada($puntos){ //Permite realizar la jugada si el puntaje es diferente de 0
	return ($puntos == 0)? 'f' : 't';
}

function habilitarLanzar($i){ //Habilita o deshabilita el botón de lanzar
	return ($i > 2)? 'disabled': 'enabled';
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
	
	if ($datos_juego[$i+8] == -1)
		dibujarBoton($i+1, $jugadas[$i], puntaje($i), 'enabled');
	else {
		acumuladores($i);			
		dibujarBoton($i+1, $jugadas[$i], $datos_juego[$i+8], 'disabled');
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
		    $puntos = xIguales($dados, 3);
			break;
		case 7:
		    $puntos = xIguales($dados, 4);
			break;	
		case 8:
		    $puntos = fullHouse($dados);
			break;
		case 9:
		    $puntos = escaleraCorta($dados);
			break;	
		case 10:
		    $puntos = escaleraLarga($dados);
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
		$suma1_6 += $datos_juego[$i+8];
	else
		$suma7_13 += $datos_juego[$i+8];
	
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

function xIguales($vector, $n){ //Devuelve la suma de 3 o 4 caras con el mismo número
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

function fullHouse($vector){ //Devuelve 25 si hay 3 y 2 o 2 y 3 caras con el mismo número
	sort($vector);
    if ($vector[0] == $vector[1] && $vector[1] == $vector[2] && $vector[3] == $vector[4])
        return 25;

    if ($vector[0] == $vector[1] && $vector[2] == $vector[3] && $vector[3] == $vector[4])
        return 25;

    return 0;
}

function escaleraCorta($vector){ //Devuelve 30 si hay 4 dados con números en secuencia (1234X o 2345X o 3456X)
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

function escaleraLarga($vector){ //Devuelve 40 si hay 5 dados con números en secuencia (12345 o 23456)
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

function nuevo(){ //Verifica si debe crear un nuevo juego
	global $id;
	if(isset($_GET['id']))
		$id = (int)$_GET['id'];
	if(isset($_POST['id']))
		$id = (int)$_POST['id'];
	if($id == -1 || isset($_POST['nuevo'])){
		nuevoJuego();
		obtenerId();
	}
}

function obtenerDados(){ //Obtiene las caras de los dados (si fue seleccionado lo lanza de nuevo, sino lo conserva)
	global $datos_juego;
	global $dados;
	for($i=0; $i<5; $i++) {
		if(isset($_POST['lanzar']) && !isset($_POST[$i]))
			$datos_juego[$i+3] = rand(1,6);
		$dados[$i] = $datos_juego[$i+3];		
	}
}

function obtenerLanzamientos(){ //Obtiene la cantidad de lanzamientos y habilita/deshabilita las jugadas según dicha cantidad
	global $datos_juego;
	global $jugar;
	if(isset($_POST['lanzar']) && $datos_juego[2] < 3) {
		$jugar = 'enabled';
		$datos_juego[2] = $datos_juego[2]+1;
		escribeJuego($datos_juego, 0);
	}

}

function obtenerJugadas(){ //Obtiene la jugada utilizada para guardarla
	global $datos_juego;
	if(isset($_GET['j'])) {
		$i = (int)$_GET['j'];
		if($_GET['c'] == 't'){ //+0 puntos o confirmación
			guardarJugada($i);
		}
	}
}

function obtenerJugador(){ //Obtiene el nombre del jugador para guardarlo
	global $datos_juego;
	if(isset($_POST['guardar'])) {
		$datos_juego[1] = $_POST['jugador'];
		guardaJugador($datos_juego);
	}
}

function vaidarJugadas(){ //Vaida la jugada utilizada (si continuar con 0 puntos o no)
	global $datos_juego;
	if(isset($_GET['j'])) {
		$i = (int)$_GET['j'];
		if($_GET['c'] == 'f'){ //>0 puntos o confirmación
			escribeJuego($datos_juego, $i);
			confirmar($i);	
		}
	}
}

function guardarJugada($i){ //Guarda el puntaje de la jugada si ha sido seleccionada por primera vez
	global $datos_juego;
	if($datos_juego[$i+7] == -1){
		$datos_juego[2] = 0;
		$datos_juego[$i+7] = puntaje($i-1);
		escribeJuego($datos_juego, $i);
	}
}

function fin(){ //Verifica si es el final del juego para reiniciar los datos y mostrar el puntaje total
	global $suma1_6;
	global $suma7_13;
	global $bono;
	if(finJuego()){
		nuevojuego();
		puntajeFinal($suma1_6+$bono+$suma7_13);
	}
}

function finJuego(){ //Verifica que todas las jugadas tengan puntaje (es decir que todas se hayan usado)
	global $datos_juego;
	for($i=8; $i< 21; $i++){
		if($datos_juego[$i] == -1)
			return false;
	}
	return true;
}

function nuevoJuego() { //Crea un nuevo juego
	global $con;
	$sql = "INSERT INTO juego 
			(id,jugador,lanzamientos,dado1,dado2,dado3,dado4,dado5,jugada1,jugada2,jugada3,jugada4,jugada5,jugada6,jugada7,jugada8,jugada9,jugada10,jugada11,jugada12,jugada13,total)
			VALUES(0,'Jugador',0,5,4,3,2,1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,0)";
	$con->query($sql);
}

function obtenerId() { //Obtiene el id del juego
	global $con;
	global $id;
	$sql = "SELECT * FROM juego ORDER BY id DESC LIMIT 1";
	$query = $con->query($sql);
	$result = mysqli_fetch_array($query);
	$id = $result[0];
}

function guardaJugador($datos_juego) { //Guarda el jugador actual del juego
	global $con;
	$sql = "UPDATE juego SET jugador = '$datos_juego[1]' WHERE id = $datos_juego[0]";
	$con->query($sql);
}

function guardaTotal() { //Guarda el total actual del juego
	global $con;
	global $total;
	global $datos_juego;
	$sql = "UPDATE juego SET total = $total WHERE id = $datos_juego[0]";
	$con->query($sql);
}

function escribeJuego($datos_juego, $i) { //Guarda el estado actual del juego
	global $con;
	$sql = "UPDATE juego SET lanzamientos = $datos_juego[2], dado1 = $datos_juego[3], dado2 = $datos_juego[4],
			dado3 = $datos_juego[5], dado4 = $datos_juego[6], dado5 = $datos_juego[7] WHERE id = $datos_juego[0]";
	$con->query($sql);
	if($i != 0){
		$jugada = "jugada" . $i;
		$nj = $i+7;
		$sql = "UPDATE juego SET $jugada = $datos_juego[$nj] WHERE id = $datos_juego[0]";
		$con->query($sql);		
	}
}

function leeJuego(&$datos_juego) { //Levanta el estado del juego
	global $con;
	global $id;
	$sql = "SELECT * FROM juego WHERE id = $id";
	$query = $con->query($sql);
	$result = mysqli_fetch_array($query);
	for($i=0; $i<22; $i++)
		$datos_juego[$i] = $result[$i];
}

/*
function escribeJuego($datos_juego) { //Guarda el estado actual del juego en un archivo de texto
	$ArchivoYahtzee = "datos/MiYahtzee.txt";
    $Handle = fopen($ArchivoYahtzee, 'w');
    $Data = implode(";", $datos_juego);
    fwrite($Handle, $Data);
    fclose($Handle);
}

function leeJuego(&$datos_juego) { //Levanta el estado del juego desde un archivo de texto
    $ArchivoYahtzee = "datos/MiYahtzee.txt";
    if (file_exists($ArchivoYahtzee)) {
        $Handle = fopen($ArchivoYahtzee, 'r');    
        $Data = fread($Handle, filesize($ArchivoYahtzee));
        fclose($Handle);
    }
    else
        $Data = "1;Kim;0;1;2;3;4;5;-1;-1;-1;-1;-1;-1;-1;-1;-1;-1;-1;-1;-1";
    $datos_juego = explode(";", $Data);
}
*/
?>
</body>
</html>