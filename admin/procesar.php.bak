<?php

session_start();

if(($_SESSION['usuario']['id_tipo'] != 1)&&($_SESSION['usuario']['id_tipo'] != 2)){
	header("Location: ../index.php");
	exit;
}
require("comun/class_db.php");
require("comun/class_abm.php");
require("comun/class_paginado.php");
require("comun/class_orderby.php");

require_once("../config_db.php");

$db = new class_db(VAR_HOST, VAR_USERDB, VAR_PASSDB, VAR_DB);
$db->mostrarErrores = true;
$db->connect();

function verificarMercaderias($mercaderias){
//verifica que las mercaderias no tengan la cantidad 0 o nula
	// print_r($mercaderias);
	if(empty($mercaderias)){
		return false;
	}
	
	$correcto = true;
	foreach ($mercaderias as $valor){
		if(($valor["cantidad"] == "") || ($valor["cantidad"] == 0) || ($valor["cantidad"] == null)){
			$correcto = false;
			break;
		}
		// if(($valor["precio"] == "") || ($valor["precio"] == null)){
		// 	$correcto = false;
		// 	break;
		// }
	}
	// return false;
	return $correcto;
}

if($_GET["merc"] == "ok"){
	$mercaderias = $_POST["mercaderias"];
	$fecha_orig = $_POST["fecha"];
	$numero = $_POST["nro_factura"];
	$idcomp_tipo = $_POST["tipo_compra"];
	$proovedor = $_POST["proovedor"];
	
	$fecha_orig = explode("/",$fecha_orig);
	$fecha = $fecha_orig[2]."-".$fecha_orig[1]."-".$fecha_orig[0];
	
	$sql = "
		INSERT INTO sys_comprobante
		(
		fecha,
		numero,
		idcomp_tipo,
		identidad,
		fecha_alta,
		idempresa,
		idusuario_carga
		)
		VALUES
		(
		'".$fecha."',
		'".$numero."',
		'".$idcomp_tipo."',
		'".$proovedor."',
		NOW(),
		1,
		'".$_SESSION["usuario"]["id_usuario"]."'
		)
	";
	
	if(verificarMercaderias($mercaderias)){
		if($db->query($sql)){
			$idcomprobante = mysql_insert_id();
			
			for($i=0; $i< count($mercaderias); $i++){
				$sql = "
				INSERT INTO sys_comprobante_detalle
				(
				idcomprobante,
				idproducto,
				cantidad
				/*importe*/
				)
				VALUES
				(
				'".$idcomprobante."',
				'".$mercaderias[$i]["id_producto"]."',
				'".$mercaderias[$i]["cantidad"]."'
				/*'".$mercaderias[$i]["precio"]."'*/
				)
				";
				$db->query($sql);
			}
			echo json_encode(array(
				"estado"=>"ok"
			));
		}else{
			echo json_encode(array(
				"estado"=>"error",
				"msg"=>"Error al insertar el comprobante"
			));
		}
	}else{
		echo json_encode(array(
			"estado"=>"error",
			"msg"=>"Error en los datos de mercaderia"
		));
	}
	
	
	
	
	// echo json_encode($mercaderias);
	// echo json_encode($mercaderias);
}

?>