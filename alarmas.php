<?php
require_once("boot.php");

$sql = "select nro,est from hab";
$result = mysql_query($sql, $pconnect);

while($fila = mysql_fetch_assoc($result)){
	$arrayRetorno[$fila["nro"]] = $fila["est"]; 
}
echo json_encode($arrayRetorno);
?>