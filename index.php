<?php
session_start();

if($_SESSION['usuario']){
	header("Location: home.php");
	exit;
}

$error = "";
if($_POST){
	
	require_once("boot.php");
																																  //q no sea proovedor
	$sql = "SELECT * FROM sys_usuario WHERE activo = 1 AND usuario = '".$_POST['usuario']."' AND clave = '".$_POST['password']."' AND id_tipo <> 9 LIMIT 1";
	$result = mysql_query($sql, $pconnect);
	$can_res = mysql_num_rows($result);
	if($can_res == 1){
		
		while($fila = mysql_fetch_assoc($result)){
			$_SESSION['usuario'] = $fila;
		}
		
		//////////////////////////////////////////////
		// los turnos solo los abren los porteros?? //
		//////////////////////////////////////////////
		
		// si ya ahi un tuno abierto se utiliza ese de lo contrario se crea uno nuevo
		$sql = "SELECT id_turno FROM sys_turno WHERE estado = 1 LIMIT 1";
		$result = mysql_query($sql, $pconnect);
		$can_res = mysql_num_rows($result);
		if($can_res == 0){
			$sql = "INSERT INTO sys_turno (id_usu_a, fecha_a, estado) VALUES ('".$_SESSION['usuario']['id_usuario']."', NOW(), 1)";
			$result = mysql_query($sql, $pconnect);
			$id_turno = mysql_insert_id($pconnect);
		}else{
			while($fila = mysql_fetch_assoc($result)){
				$id_turno = $fila['id_turno'];
			}
		}
		
		/*
		/////////////////////////////////////////////////////////////
		// falla cuando tenemos el mismo usuario en la conserjeria //
		/////////////////////////////////////////////////////////////
		// cierre forzado el usuario cierrar el navegador o apaga la maquina se coloca id 2 para saber cuando susede esto
		$sql = "SELECT id_turno FROM sys_turno_usuario WHERE estado = 1 AND id_usuario = '".$_SESSION['usuario']['id_usuario']."'";
		$result = mysql_query($sql, $pconnect);
		$can_res = mysql_num_rows($result);
		if($can_res != 0){
			$sql = "UPDATE sys_turno_usuario SET estado = 2 WHERE estado = 1 AND id_usuario = '".$_SESSION['usuario']['id_usuario']."'";
			$result = mysql_query($sql, $pconnect);
		}
		*/
		
		// se crea nuevo ingreso en el sistema
		$sql = "INSERT INTO sys_turno_usuario (id_usuario, id_turno, ip, fecha_in, estado) VALUES ('".$_SESSION['usuario']['id_usuario']."', '".$id_turno."', '".$_SERVER['REMOTE_ADDR']."', NOW(), 1)";
		$result = mysql_query($sql, $pconnect);
		$id_turno_usuario = mysql_insert_id($pconnect);
		
		$datos = array(
			"id_turno" => $id_turno,
			"id_turno_usuario" => $id_turno_usuario
			);
		$_SESSION['turnos'] = $datos;
		
		header("Location: home.php");
		
		mysql_close($pconnect);
		
		exit;

	}else{
		$error = "El usuario o la clave son incorrectos!!";
	}

}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Casablanca</title>
	
	<link href="css/cupertino/jquery-ui-1.10.3.custom.css" rel="stylesheet">
	<link href="css/style.css" rel="stylesheet">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">

	<style>
	*{
		margin:0;
		padding:0;
	}
	body{
		/* Este codigo es ára realizar el gradiente de la página */
		background: #9e211f; /* Old browsers */
		background: -moz-linear-gradient(left,  #9e211f 0%, #f7a427 47%, #f7a427 48%, #9e211f 100%); /* FF3.6+ */
		background: -webkit-gradient(linear, left top, right top, color-stop(0%,#9e211f), color-stop(47%,#f7a427), color-stop(48%,#f7a427), color-stop(100%,#9e211f)); /* Chrome,Safari4+ */
		background: -webkit-linear-gradient(left,  #9e211f 0%,#f7a427 47%,#f7a427 48%,#9e211f 100%); /* Chrome10+,Safari5.1+ */
		background: -o-linear-gradient(left,  #9e211f 0%,#f7a427 47%,#f7a427 48%,#9e211f 100%); /* Opera 11.10+ */
		background: -ms-linear-gradient(left,  #9e211f 0%,#f7a427 47%,#f7a427 48%,#9e211f 100%); /* IE10+ */
		background: linear-gradient(to right,  #9e211f 0%,#f7a427 47%,#f7a427 48%,#9e211f 100%); /* W3C */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#9e211f', endColorstr='#9e211f',GradientType=1 ); /* IE6-9 */

	}
	#contenedor{
		width:300px;
		margin:0 auto;
	}
	</style>

	<script src="js/jquery-1.9.1.js"></script>
	<script src="js/jquery-ui-1.10.3.custom.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

</head>
<body>

	<div id="contenedor">

		&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<img src="img/marca.png" />
		<br />
		<br />
		<br />
		<form id="login" name="login" action="index.php" method="post">


			<div>
				<input type="text" class="form-control input-lg" placeholder="Usuario" aria-describedby="sizing-addon1" id="usuario" name="usuario" >
				<br>
				<input type="password" class="form-control input-lg" placeholder="Clave" aria-describedby="sizing-addon1" id="password" name="password">
				<br>
				<input type="submit" class="btn btn-warning btn-lg btn-block"  aria-describedby="sizing-addon1" id="send" name="send" value="Ingresar">
			</div>
			
			<br />
			<div>
				<? if($error != ""){ ?>
				<div style="background-color:#FF0000; margin:2px; padding:2px; color: white;"><? echo $error; ?></div>
				<? } ?>
			</div>

		</form>
	</div>

</body>
</html>