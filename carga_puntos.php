<?php
require_once("boot.php");

$tarjetas = array(
"104987",
"104502",
"104503",
"104990",
"104989",
"104988",
"104966",
"104965",
"104967",
"104968"
);

foreach($tarjetas as $key => $value){
	
	$sql = "INSERT INTO sys_tarjetas (codigo, activo) VALUES ('".$value."', 1)";
	echo "<br>".$sql;
	//$result = mysql_query($sql);
	
	$sql = "INSERT INTO sys_puntos (codigo, puntos, idcomprobante) VALUES ('".$value."', 333, 0)";
	echo "<br>".$sql;
	//$result = mysql_query($sql);
	
	echo "<br>";
		
}

?>