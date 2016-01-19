<?php
require_once("boot.php");

if($_POST['c']){
	
	$id_hab = $_POST['id_hab'];
	$est = $_POST['est'];
	
	// cerramos el turno de la habitacion
	$sql = "	
	SELECT sys_comprobante.idcomprobante FROM sys_comprobante
	INNER JOIN tur ON tur.id_tur = sys_comprobante.id_turno
	WHERE tur.est_tur = '".$est."' AND tur.nro = '".$id_hab."'
	";
	$result = mysql_query($sql, $pconnect);
	while($fila = mysql_fetch_assoc($result)){
		$idcomprobante = $fila['idcomprobante'];
	}
	$sql = "UPDATE sys_comprobante SET id_turno_cierre = '".$_SESSION['turnos']['id_turno_usuario']."' WHERE idcomprobante = '".$idcomprobante."'";
	mysql_query($sql, $pconnect);
	
	$sql = "UPDATE tur SET est_tur = 0 WHERE est_tur = '".$est."' AND nro = '".$id_hab."'";
	if(mysql_query($sql, $pconnect)){
		echo "ok";
	}else{
		echo "error";
	}
	
	exit;

}
?>