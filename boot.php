<?php
session_start();

$error = false;
if ($error){
	error_reporting(E_ERROR);
	ini_set("display_errors", -1);
}else{
	error_reporting(0);
	ini_set("display_errors", 0);
}

// CONFIGURACION DE BASE DE DATOS
require_once("config_db.php");

// CONFIGURACION DE LOS PARAMETROS DEL SISTEMA
require_once("parametros.php");

// CONEXION
$pconnect = mysql_connect(VAR_HOST, VAR_USERDB, VAR_PASSDB);
mysql_select_db(VAR_DB, $pconnect);

function printr($arr){
	echo "<pre>";
		print_r($arr);
	echo "</pre>";
}

$DIAS = array("Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sábado");
$MESES = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

// funcion infinita para la busqueda de cada promo o receta agrega
function busca_pro_com($idcomp, $id_pro_ingresado, $cantidad_pro, $iddetalle_chown){
	
	$sql = "SELECT id_pro, promo, elaborado FROM pro WHERE id_pro = ".(int)$id_pro_ingresado."";
	$result1 = mysql_query($sql);
	while($fila_pro = mysql_fetch_assoc($result1)){
		
		if($fila_pro['promo'] == 1 || $fila_pro['elaborado'] == 1){
			
			$sql = "SELECT * FROM pro_combinado WHERE id_pro = '".$fila_pro['id_pro']."'";
			$result2 = mysql_query($sql);			
			while($fila_rec = mysql_fetch_assoc($result2)){
				
				// trae el precio del producto						
				$sql = "SELECT val FROM pro WHERE id_pro = '".$fila_rec['id_proc']."'";
				$result3 = mysql_query($sql);			
				while($fila_pre = mysql_fetch_assoc($result3)){
					$precio = $fila_pre['val'];
				}
				
				$precio_final = $precio * $fila_rec['can'];
				
				$cantidad = (float)$fila_rec['can'] * (int)$cantidad_pro;
				
				$sql = "
				INSERT INTO sys_comprobante_detalle (
					iddetalle_chown,
					idcomprobante,
					idproducto,
					cantidad,
					importe,
					detalle,
					num_serie,
					num_inventario,
					fecha_garantia,
					idestado,
					id_usuario_carga
				) VALUES (
				'".$iddetalle_chown."', ".$idcomp.", '".$fila_rec['id_proc']."', '".$cantidad."', '".$precio_final."', '', '', '', '', '', '".$_SESSION['usuario']['id_usuario']."')";
				mysql_query($sql);
				
				busca_pro_com($idcomp, $fila_rec['id_proc'], $cantidad_pro, $iddetalle_chown);
				
			}
			
		}

	}
	
}
			
function traer_dia_producto($fecha_hora){
	
	if($fecha_hora == false){
		
		// obtenemos la dia de la semana actual
		$dia_act = date("N");
		
	}else{
		
		// calculamos segun la fecha y hora enviada el dia de la semana actual
		$exp_fec_hor = explode(" ", $fecha_hora);
		$exp_fec = explode("-", $exp_fec_hor[0]);
		$exp_hor = explode(":", $exp_fec_hor[1]);
		
		$dia_act = date("N", mktime($exp_hor[0], $exp_hor[1], $exp_hor[2], $exp_fec[1], $exp_fec[2], $exp_fec[0]));
		
	}
	
	switch($dia_act){
		case 1:
		case 2:
		case 3:
		case 4:
			$dia = 1;
		break;
		case 5:
		case 6:
			$dia = 2;
		break;
		case 7:
			$dia = 3;
		break;
	break;
	}
	
	// si es feriado
	$fecha_hoy = date("Y-m-d");
	$sql = "SELECT fec FROM feriados WHERE activo = 1 AND fec = '".$fecha_hoy."'";
	$result = mysql_query($sql, $pconnect);
	if(mysql_affected_rows($pconnect) != 0){
		$dia = 3;
	}
	
	// los dias comienzan a las 8 de la mañana
	if(date("H:i") < "08:00"){
		$dia = $dia - 1;
		// error de dia lunes
		if ($dia == 0){
			if($dia_act == 1){
				$dia = 3;
			}else{
				$dia = 1;
			}
		}
		if($dia_act == 6){
			$dia = $dia + 1;
		}
	}
	
	return $dia;
}

function traer_hora_producto($fecha_hora){
	
	if($fecha_hora == false){
		
		// obtenemos la hora actual
		$hor_act = date("H:i");

	}else{
		
		// calculamos segun la fecha y hora enviada
		$exp_fec_hor = explode(" ", $fecha_hora);
		$exp_fec = explode("-", $exp_fec_hor[0]);
		$exp_hor = explode(":", $exp_fec_hor[1]);
		
		$hor_act = date("H:i", mktime($exp_hor[0], $exp_hor[1], $exp_hor[2], $exp_fec[1], $exp_fec[2], $exp_fec[0]));
		
	}
	
	/*
	if($hor_act < "14:00"){
		$hora = 1;
	}else{
		$hora = 2;
	}
	*/
		
	// si la hora 00:00 <---> 08:00 de la mañana restamos un dia simpre
	if($hor_act > "08:00" and $hor_act < "15:00"){
		$hora = 1;
	}else{
		$hora = 2;
	}
	
	return $hora;
	
}


function traer_hora($fecha_hora){
	
	if($fecha_hora == false){
		
		// obtenemos la hora actual
		$hor_act = date("H:i");

	}else{
		
		// calculamos segun la fecha y hora enviada
		$exp_fec_hor = explode(" ", $fecha_hora);
		$exp_fec = explode("-", $exp_fec_hor[0]);
		$exp_hor = explode(":", $exp_fec_hor[1]);
		
		$hor_act = date("H:i", mktime($exp_hor[0], $exp_hor[1], $exp_hor[2], $exp_fec[1], $exp_fec[2], $exp_fec[0]));
		
	}
	return $hor_act;
	
}

function conversor_tiempo_sin_segundos($seg_ini) {
	
	$horas = floor($seg_ini/3600);
	$minutos = floor(($seg_ini-($horas*3600))/60);
	$segundos = $seg_ini-($horas*3600)-($minutos*60);
	
	$horas = str_pad($horas, 2, "0", STR_PAD_LEFT);
	$minutos = str_pad($minutos, 2, "0", STR_PAD_LEFT);
	
	return $horas.':'.$minutos;
	
}

function hora_salida($fec_in, $tiempo_para_salida, $tiempo, $es_trasnoche = false){
	
	/*
	echo "<br>";
		echo "<br>fec_in: ".$fec_in;
		echo "<br>salida: ".$tiempo_para_salida;
		echo "<br>tiempo: ".$tiempo;
	echo "<br>";
	*/
	
	$exp_tie_sal = explode(":", $tiempo_para_salida);
	$tiempo_turno = $exp_tie_sal[2] + ($exp_tie_sal[1] * 60) + ($exp_tie_sal[0] * 3600);
	
	$exp_fec = explode(" ", $fec_in);
	$exp_fec_d = explode("-", $exp_fec[0]);
	$exp_fec_h = explode(":", $exp_fec[1]);
	
	//mktime (H,i,s,m,d,Y);
	
	$hora_salida_pro = mktime($exp_fec_h[0],$exp_fec_h[1],$exp_fec_h[2], $exp_fec_d[1],$exp_fec_d[2],$exp_fec_d[0]);
	
	// dia limite para los pasos de dia
	$dia_lim = 0;
	if($tiempo > "00:00:00" and $tiempo < "12:00:00"){
		$dia_lim = 1;
	}
	if($es_trasnoche == true){
		// puede tener error
		$hora_actual = date("H:i");
		if($hora_actual > "00:00" and $hora_actual < "06:00"){
			$dia_lim = 0;
		}
	}
	
	$exp_fec_h = explode(":", $tiempo);
	$hora_tiempo = mktime($exp_fec_h[0],$exp_fec_h[1],$exp_fec_h[2], $exp_fec_d[1], $exp_fec_d[2] + $dia_lim,$exp_fec_d[0]);
	
	// diferencia entre las dos fechas
	$tiempo_diff = $hora_tiempo - $hora_salida_pro;
	
	// si el tiempo entre un turno
	if($tiempo_diff <= $tiempo_turno){
		
		$tiempo_cli = conversor_tiempo_sin_segundos($tiempo_diff);
		
	}else{
		
		$tiempo_cli = $tiempo_para_salida;
		
	}
	
	/*
	echo "<br>";
	echo $hora_salida_pro;
	echo "<br>";
	echo $hora_tiempo;
	echo "<br>";
	echo $tiempo_diff;
	echo "<br>";
	echo $tiempo_turno;
	echo "<br>";
	echo $tiempo_cli;
	*/
	
	return $tiempo_cli;
	
}

?>