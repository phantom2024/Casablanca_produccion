<?php
require_once("boot.php");

if(!$_SESSION['usuario']){
	echo json_encode(false);
	exit;
}

// avisa que todabia esta logeado al sistema
$sql = "UPDATE sys_turno_usuario SET fecha_act = NOW() WHERE idturno_usu = '".$_SESSION['turnos']['id_turno_usuario']."'";
$result = mysql_query($sql, $pconnect);

$sql = "SELECT nro, est FROM hab WHERE act = 1";
$result = mysql_query($sql, $pconnect);
while($fila = mysql_fetch_assoc($result)){
	
	$fila['fec_out_c'] = NULL;
	$fila['fec_in_c'] = NULL;
	$fila['est_tur_c'] = NULL;
	
	$sql2 = "SELECT est_tur, fec_in, fec_out, llamado FROM tur WHERE tur.est_tur = 1 AND tur.nro = '".$fila['nro']."'";
	$result_t = mysql_query($sql2, $pconnect);
	if(mysql_num_rows($result_t) != 0){

		while($fila_t = mysql_fetch_assoc($result_t)){
			
			// si se llamo o no al cliente
			$fila['llamado'] = $fila_t['llamado'];
		
			$fila['fec_in'] = $fila_t['fec_in'];
			$fila['fec_out'] = $fila_t['fec_out'];
			
			if($fila_t['fec_in'] != 0){
				$fec_in_c = date_create($fila_t['fec_in']);
				$fec_in = date_format($fec_in_c, 'd/m/Y H:i');
				$fila['fec_in_c'] = $fec_in;				
			}
			
			if($fila_t['fec_out'] != 0){
				
				$fec_out_c = date_create($fila_t['fec_out']);
				$fec_out = date_format($fec_out_c, 'd/m/Y H:i');		
				$fila['fec_out_c'] = $fec_out;
				
				// colocamos hora
				$fila['hora'] = date_format($fec_out_c, 'H:i');
				
				// calulo para termino de turno alerta()
				$start = strtotime('now'); 
				$fe = date_format($fec_out_c, 'Y-m-d H:i:s');
				$end = strtotime($fe); 
				
				$dif = $end - $start;
				$res = $dif / 60;
				$res  = (int)$res;
				
				$fila['alerta_tt'] = $res;
				
				if($res <= 15){
					$fila['alerta'] = true;
					$fila['alerta_t'] = $res;
				}else{
					$fila['alerta'] = false;
				}

			}

		}
		
		// si tiene turno y el estado es dos
		if($fila['est'] == 2){
			$fila['alerta_sal'] = true;
		}else{
			$fila['alerta_sal'] = false;
		}

	}else{

		$fila['hora'] = "S/T.";
		
	}
	
	// campo de dia
	$tiempo = $DIAS[date("w")].", ".date("d"). " de ".$MESES[date("n") - 1]." del ".date("Y")." ".date("H").":".date("i").":".date("s");
	$datos['fecha_act'] = $tiempo;
	
	// array de datos
	$datos['datos'][] = $fila;
	
}

// fabian vallejo 20-03-14
// obntenemos el turno actual
$sql = "SELECT id_turno FROM sys_turno WHERE estado = 1 LIMIT 1";
$result2 = mysql_query($sql, $pconnect);
while($fila2 = mysql_fetch_assoc($result2)){
	$id_turno_actual = $fila2['id_turno'];
}
		
$totales = false;
$totales_tur = false;
switch($_SESSION['usuario']['id_tipo']){
	
	case 2:
		
		$sql = "
		SELECT SUM(sys_comprobante_detalle.cantidad * sys_comprobante_detalle.importe) AS total_des_usuario FROM sys_turno_usuario
					
		INNER JOIN sys_comprobante ON sys_comprobante.id_turno_cierre = sys_turno_usuario.idturno_usu
		INNER JOIN sys_comprobante_detalle ON sys_comprobante_detalle.idcomprobante = sys_comprobante.idcomprobante
		INNER JOIN pro ON pro.id_pro = sys_comprobante_detalle.idproducto
		
		WHERE
			
			pro.bar = 2 AND
			sys_turno_usuario.id_turno = '".$id_turno_actual."' AND
			sys_turno_usuario.id_usuario = '".$_SESSION['usuario']['id_usuario']."'				
			
		";
		$result2 = mysql_query($sql);
		while($fila2 = mysql_fetch_assoc($result2)){
			$total_des_usuario = $fila2['total_des_usuario'];
		}
		if(is_null($total_des_usuario)){
			$total_des_usuario = 0;
		}
		unset($fila2);
		mysql_free_result($result2);
		
		//pasamos a positivo
		$total_des_usuario = $total_des_usuario * -1;
		
		$sql = "
		SELECT SUM(sys_comprobante_detalle.cantidad * sys_comprobante_detalle.importe) AS total_usuario FROM sys_turno_usuario
					
		INNER JOIN sys_comprobante ON sys_comprobante.id_turno_cierre = sys_turno_usuario.idturno_usu
		INNER JOIN sys_comprobante_detalle ON sys_comprobante_detalle.idcomprobante = sys_comprobante.idcomprobante
		INNER JOIN pro ON pro.id_pro = sys_comprobante_detalle.idproducto
		
		WHERE
			
			pro.venta = 1 AND
			(sys_comprobante_detalle.mostrar = 1 OR pro.bar = 0) AND
			sys_turno_usuario.id_turno = '".$id_turno_actual."' AND
			sys_turno_usuario.id_usuario = '".$_SESSION['usuario']['id_usuario']."'				
			
		";
		$result2 = mysql_query($sql);
		while($fila2 = mysql_fetch_assoc($result2)){
			$total_usuario = $fila2['total_usuario'];
		}
		if(is_null($total_usuario)){
			$total_usuario = 0;
		}
		unset($fila2);
		mysql_free_result($result2);

		$sql = "
		SELECT SUM(sys_comprobante_detalle.cantidad * sys_comprobante_detalle.importe) AS total_bar FROM sys_turno_usuario
		
		INNER JOIN sys_comprobante ON sys_comprobante.id_turno_cierre = sys_turno_usuario.idturno_usu
		INNER JOIN sys_comprobante_detalle ON sys_comprobante_detalle.idcomprobante = sys_comprobante.idcomprobante
		INNER JOIN pro ON pro.id_pro = sys_comprobante_detalle.idproducto
		
		WHERE pro.bar = 1 AND mostrar = 1 AND sys_turno_usuario.id_turno = '".$id_turno_actual."' AND sys_turno_usuario.id_usuario = '".$_SESSION['usuario']['id_usuario']."'
		";
		$result2 = mysql_query($sql);
		while($fila2 = mysql_fetch_assoc($result2)){
			$total_bar = $fila2['total_bar'];
		}
		if(is_null($total_bar)){
			$total_bar = 0;
		}
		unset($fila2);
		mysql_free_result($result2);

		$sql = "
		SELECT SUM(sys_puntos.puntos) AS total_puntos_cargados FROM sys_turno_usuario
		
		INNER JOIN sys_comprobante ON sys_comprobante.id_turno_cierre = sys_turno_usuario.idturno_usu
		INNER JOIN sys_puntos ON sys_puntos.idcomprobante = sys_comprobante.idcomprobante
		
		WHERE sys_turno_usuario.id_turno = '".$id_turno_actual."' AND sys_turno_usuario.id_usuario = '".$_SESSION['usuario']['id_usuario']."' AND sys_puntos.puntos > 0;
		";
		$result2 = mysql_query($sql);
		while($fila2 = mysql_fetch_assoc($result2)){
			$total_puntos_cargados = $fila2['total_puntos_cargados'];
		}
		if(is_null($total_puntos_cargados)){
			$total_puntos_cargados = 0;
		}
		unset($fila2);
		mysql_free_result($result2);
		
		$sql = "
		SELECT SUM(sys_puntos.puntos) AS total_puntos_canjeados, COUNT(sys_comprobante.idcomprobante) as total_puntos_hab FROM sys_turno_usuario
		
		INNER JOIN sys_comprobante ON sys_comprobante.id_turno_cierre = sys_turno_usuario.idturno_usu
		INNER JOIN sys_puntos ON sys_puntos.idcomprobante = sys_comprobante.idcomprobante
		
		WHERE sys_turno_usuario.id_turno = '".$id_turno_actual."' AND sys_turno_usuario.id_usuario = '".$_SESSION['usuario']['id_usuario']."' AND sys_puntos.puntos < 0;
		";
		$result2 = mysql_query($sql);
		while($fila2 = mysql_fetch_assoc($result2)){
			$total_puntos_canjeados = $fila2['total_puntos_canjeados'];
			$total_puntos_hab = $fila2['total_puntos_hab'];
		}
		if(is_null($total_puntos_canjeados)){
			$total_puntos_canjeados = 0;
		}
		$total_puntos_canjeados = $total_puntos_canjeados * -1;
		unset($fila2);
		mysql_free_result($result2);
		
		// restamos de la caja los puntos canjeados y los descuentos realizados
		$total_usuario = $total_usuario - $total_puntos_canjeados - $total_des_usuario;
		
		// array de totales
		$totales = array(
			"Total: $ " => $total_usuario,
			"Bar: $ " => $total_bar,
			"Descuento: $ " => $total_des_usuario,
			"P. Cargados: " => $total_puntos_cargados,
			"P. Canjeados: " => $total_puntos_canjeados,
			"Hab. Canjeadas: " => $total_puntos_hab
		);
		
	break;
	
}

$datos['totales'] = $totales;








/// totales por turno
$sql = "
SELECT SUM(sys_comprobante_detalle.cantidad * sys_comprobante_detalle.importe) AS total_des_turno FROM sys_turno_usuario

INNER JOIN sys_comprobante ON sys_comprobante.id_turno_cierre = sys_turno_usuario.idturno_usu
INNER JOIN sys_comprobante_detalle ON sys_comprobante_detalle.idcomprobante = sys_comprobante.idcomprobante
INNER JOIN pro ON pro.id_pro = sys_comprobante_detalle.idproducto

WHERE pro.bar = 2 AND sys_turno_usuario.id_turno = '".$id_turno_actual."'
";
$result2 = mysql_query($sql);
while($fila2 = mysql_fetch_assoc($result2)){
	$total_des_turno = $fila2['total_des_turno'];
}
if(is_null($total_des_turno)){
	$total_des_turno = 0;
}
unset($fila2);
mysql_free_result($result2);

// pasamos a positivo
$total_des_turno = $total_des_turno *-1;

$sql = "
SELECT SUM(sys_comprobante_detalle.cantidad * sys_comprobante_detalle.importe) AS total_turno FROM sys_turno_usuario

INNER JOIN sys_comprobante ON sys_comprobante.id_turno_cierre = sys_turno_usuario.idturno_usu
INNER JOIN sys_comprobante_detalle ON sys_comprobante_detalle.idcomprobante = sys_comprobante.idcomprobante
INNER JOIN pro ON pro.id_pro = sys_comprobante_detalle.idproducto

WHERE

	pro.venta = 1 AND
	(sys_comprobante_detalle.mostrar = 1 OR pro.bar = 0) AND 
	sys_turno_usuario.id_turno = '".$id_turno_actual."'
	
";
$result2 = mysql_query($sql);
while($fila2 = mysql_fetch_assoc($result2)){
	$total_turno = $fila2['total_turno'];
}
if(is_null($total_turno)){
	$total_turno = 0;
}
unset($fila2);
mysql_free_result($result2);

$sql = "
SELECT SUM(sys_comprobante_detalle.cantidad * sys_comprobante_detalle.importe) AS total_bar FROM sys_turno_usuario

INNER JOIN sys_comprobante ON sys_comprobante.id_turno_cierre = sys_turno_usuario.idturno_usu
INNER JOIN sys_comprobante_detalle ON sys_comprobante_detalle.idcomprobante = sys_comprobante.idcomprobante
INNER JOIN pro ON pro.id_pro = sys_comprobante_detalle.idproducto

WHERE pro.bar = 1 AND mostrar = 1 AND sys_turno_usuario.id_turno = '".$id_turno_actual."'
";
$result2 = mysql_query($sql);
while($fila2 = mysql_fetch_assoc($result2)){
	$total_bar = $fila2['total_bar'];
}
if(is_null($total_bar)){
	$total_bar = 0;
}
unset($fila2);
mysql_free_result($result2);

$sql = "
SELECT SUM(sys_puntos.puntos) AS total_puntos_cargados FROM sys_turno_usuario

INNER JOIN sys_comprobante ON sys_comprobante.id_turno_cierre = sys_turno_usuario.idturno_usu
INNER JOIN sys_puntos ON sys_puntos.idcomprobante = sys_comprobante.idcomprobante

WHERE sys_turno_usuario.id_turno = '".$id_turno_actual."' AND sys_puntos.puntos > 0;
";
$result2 = mysql_query($sql);
while($fila2 = mysql_fetch_assoc($result2)){
	$total_puntos_cargados = $fila2['total_puntos_cargados'];
}
if(is_null($total_puntos_cargados)){
	$total_puntos_cargados = 0;
}
unset($fila2);
mysql_free_result($result2);

$sql = "
SELECT SUM(sys_puntos.puntos) AS total_puntos_canjeados, COUNT(sys_comprobante.idcomprobante) as total_puntos_hab FROM sys_turno_usuario

INNER JOIN sys_comprobante ON sys_comprobante.id_turno_cierre = sys_turno_usuario.idturno_usu
INNER JOIN sys_puntos ON sys_puntos.idcomprobante = sys_comprobante.idcomprobante

WHERE sys_turno_usuario.id_turno = '".$id_turno_actual."' AND sys_puntos.puntos < 0;
";
$result2 = mysql_query($sql);
while($fila2 = mysql_fetch_assoc($result2)){
	$total_puntos_canjeados = $fila2['total_puntos_canjeados'];
	$total_puntos_hab = $fila2['total_puntos_hab'];
}
if(is_null($total_puntos_canjeados)){
	$total_puntos_canjeados = 0;
}
$total_puntos_canjeados = $total_puntos_canjeados * -1;
unset($fila2);
mysql_free_result($result2);


// restamos de la caja los puntos canjeados y los descuentos realizados
$total_turno = $total_turno - $total_puntos_cargados - $total_des_turno;

// array de totales 
$totales_tur = array(
	"Total Turno: $ " => $total_turno,
	"Total Bar: $ " => $total_bar,
	"Total Descuento: $ " => $total_des_turno,
	"Total P. Cargados: " => $total_puntos_cargados,
	"Total P. Canjeados: " => $total_puntos_canjeados,
	"Total Hab. Canjeadas: " => $total_puntos_hab
);
		
$datos['totales_tur'] = $totales_tur;



















//cargamos los turnos pendientes
$sql = "

SELECT
	
	tur.id_tur,
	tur.nro,
	tur.cambio_ama,
	SUM(sys_comprobante_detalle.cantidad * sys_comprobante_detalle.importe) as total

FROM tur

INNER JOIN sys_comprobante ON sys_comprobante.id_turno = tur.id_tur
INNER JOIN sys_comprobante_detalle ON sys_comprobante_detalle.idcomprobante = sys_comprobante.idcomprobante
INNER JOIN pro ON pro.id_pro = sys_comprobante_detalle.idproducto

WHERE

	tur.est_tur = 2 AND
	pro.bar IN (0,1)
	
GROUP BY  tur.id_tur;

";
$result3 = mysql_query($sql);
while($fila3 = mysql_fetch_assoc($result3)){
	
	$fecha = date_create($fila3['cambio_ama']);
	$fec_salida = date_format($fecha, 'd-m H:i');
	$fila3['fec_salida'] = $fec_salida;
	
	$datos['pendientes'][] = $fila3;
}

unset($result);
unset($result_t);
mysql_free_result($pconnect);

echo json_encode($datos);
//printr($datos);

mysql_close($pconnect);

?>