<?php
require_once("boot.php");
$archivo = fopen("datos.txt", "a+");
	$cadena = date("d-m-Y H:i:s")."; nro: (".$_REQUEST['n'].") hab: (".$_REQUEST['hab'].") err: (".$_REQUEST['error'].") \r\n";
	fputs($archivo, $cadena);
	fclose($archivo);
/*
/////////////////////////////////////////////////////////////////////////////////////////////////////////////
// guarda log de todo lo que llega //////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////

$file = fopen("config.txt", "r");
while(!feof($file)){
	if(fgets($file) == 1){
		$guarda_log = true;
	}else{
		$guarda_log = false;
	}
}
fclose($file);

if($guarda_log == true){

	$archivo = fopen("datos.txt", "a+");
	$cadena = date("d-m-Y H:i:s")."; nro: (".$_REQUEST['n'].") hab: (".$_REQUEST['hab'].") err: (".$_REQUEST['error'].") \r\n";
	fputs($archivo, $cadena);
	fclose($archivo);
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////
*/
/*
// guardamos en la base todos los errores que llegen
if($_REQUEST['error']){
		
	$exp_err = explode("-", $_REQUEST['error']);
	
	foreach($exp_err as $key_err => $value_err){
		
		switch($key_err){
			case 0:
				$placa = "31-32";
			break;
			case 1:
				$placa = "33-34";
			break;
			case 2:
				$placa = "35-36";
			break;
		}
		
		if($value_err != 0){
			$sql = "INSERT INTO log_err (fec, placa, error) VALUES (NOW(), '".$placa."', '".$value_err."')";
			mysql_query($sql, $pconnect);
		}
		
	}
	
}
*/

$contar = 0;
if($_REQUEST['hab']){

	$exp_pla = explode("~", $_REQUEST['hab']);
	
	foreach($exp_pla as $key => $value){
		
		// obtenemos el estado y la habitacion
		$paq = explode("-", $value);

		$nro_hab = (int)trim($paq[0]);
		$est_hab = (int)trim($paq[1]);
		
		if($nro_hab != 0){
			
			// llego un valor para la habitacion 0,1,2,3,4
			// realizamos dos updates por que si no siempre guarda log al afectar simpre una linea
			$sql = "UPDATE hab SET utl = NOW() WHERE nro = '".$nro_hab."'";
			mysql_query($sql, $pconnect);
			
			$actualizamos = false;
			if($est_hab == 4){

				$sql = "UPDATE hab SET con_azul = con_azul + 1 WHERE nro = '".$nro_hab."'";
				mysql_query($sql, $pconnect);
				
				$sql = "SELECT con_azul FROM hab WHERE nro = '".$nro_hab."'";
				$result = mysql_query($sql);
				while($fila_con_a = mysql_fetch_assoc($result)){
					$con_azul = $fila_con_a['con_azul'];
				}
				if($con_azul > 4){
					$actualizamos = true;
				}
				
			}else{
				$actualizamos = true;
			}
			if($actualizamos == true){
				
				echo "ok:".$nro_hab."-".$est_hab;
				
				// colocamos el contado a 0
				$sql = "UPDATE hab SET con_azul = 0 WHERE nro = '".$nro_hab."'";
				mysql_query($sql, $pconnect);
				
				// actualizamos el estado de la habitacion
				$sql = "UPDATE hab SET est = '".$est_hab."' WHERE nro = '".$nro_hab."'";
				mysql_query($sql, $pconnect);
				// colcamos justo despues del update para no perder el valor
				$glog = mysql_affected_rows($pconnect);
				
				// paso a estado rojo
				if($est_hab == 3){
					
					if($glog > 0){
						
						// si es la primera ves que llega rojo no 
						// hacemos nada espera a la segunda ves
						
					}else{
						
						// despues de que grabamos el estado en la base de datos y a la hora que se coloco la tarjeta
						// obtenemos hora y esperamos hasta que se cumpla los 10 min
						
						$sql = "SELECT TIMESTAMPDIFF(MINUTE, tur, NOW()) AS tiempo FROM hab WHERE nro = '".$nro_hab."'";
						$result = mysql_query($sql, $pconnect);
						while($fila = mysql_fetch_assoc($result)){
							$tiempo = $fila['tiempo'];
						}
												
						if((int)$tiempo >= 9){
							
							$sql = "SELECT TIMESTAMPDIFF(MINUTE, fec_out, NOW()) AS tiempo_limite FROM tur WHERE est_tur = 1 AND nro = '".$nro_hab."'";
							$result = mysql_query($sql, $pconnect);
							while($fila = mysql_fetch_assoc($result)){
								$tiempo_limite = $fila['tiempo_limite'];
							}
							$count_precio = mysql_affected_rows($pconnect);
							
							$dia = traer_dia_producto();
							$hora = traer_hora_producto();
							
							//echo "<br>dia: ".$dia;
							//echo "<br>hora: ".$hora;
							
							// si la habitacion ya tiene turno cargado
							if($count_precio != 0){
								
								$tiempo_de_espera = 35;
								// si pasaron mas de 35 min sin salir
								if($tiempo_limite >= $tiempo_de_espera){
									
									// id de comprobante del turno de la habitacion
									$sql = "
									SELECT tur.id_tur, tur.fec_in, sys_comprobante.idcomprobante FROM tur
									INNER JOIN sys_comprobante ON sys_comprobante.id_turno = tur.id_tur									
									WHERE tur.est_tur = 1 AND tur.nro = '".$nro_hab."'
									";
									$result = mysql_query($sql);
									while($fila_comp = mysql_fetch_assoc($result)){
										$id_tur = $fila_comp['id_tur'];
										$fec_in = $fila_comp['fec_in'];
										$id_com = $fila_comp['idcomprobante'];
									}
									mysql_free_result($result);
									unset($fila_comp);
										
									// calculamos el dia y la hora para la fecha de entrada
									$dia_act = traer_dia_producto($fec_in);
									$hor_act = traer_hora_producto($fec_in);
									
									// obtenemos el ultimo conserje logeado
									$sql = "
									SELECT sys_turno_usuario.id_usuario FROM sys_turno_usuario
									INNER JOIN sys_usuario ON sys_usuario.id_usuario = sys_turno_usuario.id_usuario
									WHERE id_tipo = 2
									ORDER BY fecha_in DESC LIMIT 1;
									";
									$result = mysql_query($sql);
									while($fila = mysql_fetch_assoc($result)){
										$id_usu_turno = $fila['id_usuario'];
									}
									mysql_free_result($result);
									unset($fila);
									
									////////////////////////////////////////////////////
									// reglas //////////////////////////////////////////
									////////////////////////////////////////////////////
									
									$regla1 = false;
									// 1) la habitacion tiene la promo nueva?
									$sql = "
									SELECT sys_comprobante_detalle.* FROM tur
									INNER JOIN sys_comprobante ON sys_comprobante.id_turno = tur.id_tur
									INNER JOIN sys_comprobante_detalle ON sys_comprobante_detalle.idcomprobante = sys_comprobante.idcomprobante
									WHERE
										tur.est_tur = 1 AND
										tur.nro = '".$nro_hab."' AND
										sys_comprobante_detalle.idproducto = '".ID_PROMO_NUEVA."'
									";
									$result = mysql_query($sql, $pconnect);
									if(mysql_affected_rows($pconnect) >= 1){
										
										// si tiene la promo nueva tenemos que colocarle la promo original
										$regla1 = true;
										
										// eliminamos todo los turnos del comprobante
										$sql = "
										SELECT iddetalle FROM sys_comprobante_detalle
										INNER JOIN pro ON pro.id_pro = sys_comprobante_detalle.idproducto
										WHERE sys_comprobante_detalle.idcomprobante = '".$id_com."' AND pro.bar = 0;
										";
										$result = mysql_query($sql);
										while($fila_del_det = mysql_fetch_assoc($result)){
											$sql = "DELETE FROM sys_comprobante_detalle WHERE iddetalle = '".$fila_del_det['iddetalle']."'";
											mysql_query($sql);
										}
										mysql_free_result($result);
										unset($fila_del_det);
								
										// obtenemos el precio promo y lo cargamos
										$sql = "	
											SELECT pro.* FROM tur
												INNER JOIN hab ON hab.nro = tur.nro
												INNER JOIN pro ON pro.tip = hab.tip
											WHERE
												tur.est_tur = 1 AND
												tur.nro = '".$nro_hab."' AND
												pro.bar = 0 AND
												pro.promo_hab = 1 AND
												pro.dia = '".$dia_act."' AND
												pro.hor = '".$hor_act."'
											LIMIT 1
										";
										$result = mysql_query($sql);
										while($fila = mysql_fetch_assoc($result)){
											$pro_pro = $fila;
										}
										mysql_free_result($result);
										unset($fila);
										
										// tiempo que le corresponde al cliente segun corte								
										$tiempo_para_salida = hora_salida($fec_in, $pro_pro['tie'], $pro_pro['salida']);
										
										// agregamos el nuevo producto
										$sql = "
										INSERT INTO sys_comprobante_detalle (
										
										idcomprobante,
										idproducto,
										cantidad,
										importe,
										detalle,
										num_serie,
										num_inventario,
										fecha_garantia,
										idestado,
										mostrar,
										id_usuario_carga,
										tiempo,
										correjido
										
										) VALUES (
										
										".$id_com.",
										'".$pro_pro['id_pro']."',
										'1',
										'".$pro_pro['val']."',
										'',
										'',
										'',
										'',
										'',
										'1',
										'".$id_usu_turno."',
										'".$tiempo_para_salida."',
										'4'
										
										)";
										mysql_query($sql, $pconnect);
										
										// modificamos la hora de salida desde el principio
										$sql = "UPDATE tur SET fec_out = ADDTIME(fec_in, '".$tiempo_para_salida."'), llamado = 0 WHERE id_tur = '".$id_tur."'";
										mysql_query($sql, $pconnect);
										
									}
									
									// 2) la habitacion tiene promo original?
									$regla2 = false;
									if($regla1 == false){
										
										// obtenemos el precio promo y nos fijamos si esta cargado en la hab
										$sql = "	
											SELECT pro.id_pro FROM tur
												INNER JOIN hab ON hab.nro = tur.nro
												INNER JOIN pro ON pro.tip = hab.tip
											WHERE
												tur.est_tur = 1 AND
												tur.nro = '".$nro_hab."' AND
												pro.bar = 0 AND
												pro.promo_hab = 1 AND
												pro.dia = '".$dia_act."' AND
												pro.hor = '".$hor_act."'
											LIMIT 1
										";
										$result = mysql_query($sql);
										while($fila = mysql_fetch_assoc($result)){
											$id_pro_original = $fila['id_pro'];
										}
										mysql_free_result($result);
										unset($fila);
										
										$sql = "
										SELECT sys_comprobante_detalle.* FROM tur
										INNER JOIN sys_comprobante ON sys_comprobante.id_turno = tur.id_tur
										INNER JOIN sys_comprobante_detalle ON sys_comprobante_detalle.idcomprobante = sys_comprobante.idcomprobante
										WHERE
											tur.est_tur = 1 AND
											tur.nro = '".$nro_hab."' AND
											sys_comprobante_detalle.idproducto = '".$id_pro_original."'
										";
										$result = mysql_query($sql, $pconnect);
										while($fila = mysql_fetch_assoc($result)){
											$lo_que_tiene = $fila;
										}										
										if(mysql_affected_rows($pconnect) >= 1){
											
											// si tiene la promo original enotonces tenemos que colocarle 1 turno original
											$regla2 = true;
											
											// eliminamos todo los turnos del comprobante
											
											$sql = "
											SELECT iddetalle FROM sys_comprobante_detalle
											INNER JOIN pro ON pro.id_pro = sys_comprobante_detalle.idproducto
											WHERE sys_comprobante_detalle.idcomprobante = '".$id_com."' AND pro.bar = 0;
											";
											$result = mysql_query($sql);
											while($fila_del_det = mysql_fetch_assoc($result)){
												$sql = "DELETE FROM sys_comprobante_detalle WHERE iddetalle = '".$fila_del_det['iddetalle']."'";
												mysql_query($sql);
											}
											mysql_free_result($result);
											unset($fila_del_det);
											
											// obtenemos el precio original y le cargamos 1 turno original
											$sql = "	
												SELECT pro.* FROM tur
													INNER JOIN hab ON hab.nro = tur.nro
													INNER JOIN pro ON pro.tip = hab.tip
												WHERE
													tur.est_tur = 1 AND
													tur.nro = '".$nro_hab."' AND
													pro.bar = 0 AND
													pro.promo_hab = 0 AND
													pro.dia = '".$dia_act."' AND
													pro.hor = '".$hor_act."'
												LIMIT 1
											";
											$result = mysql_query($sql);
											while($fila = mysql_fetch_assoc($result)){
												$pro_ori = $fila;
											}
											mysql_free_result($result);
											unset($fila);
											
											// tiempo que le corresponde al cliente segun corte
											$tiempo_para_salida = hora_salida($fec_in, $pro_ori['tie'], $pro_ori['salida']);
											
											// agregamos el nuevo producto
											$sql = "
											INSERT INTO sys_comprobante_detalle (
											
											idcomprobante,
											idproducto,
											cantidad,
											importe,
											detalle,
											num_serie,
											num_inventario,
											fecha_garantia,
											idestado,
											mostrar,
											id_usuario_carga,
											tiempo,
											correjido
											
											) VALUES (
											
											".$id_com.",
											'".$pro_ori['id_pro']."',
											'1',
											'".$pro_ori['val']."',
											'',
											'',
											'',
											'',
											'',
											'1',
											'".$id_usu_turno."',
											'".$tiempo_para_salida."',
											'4'
											
											)";
											mysql_query($sql, $pconnect);
											
											
											// modificamos la hora de salida desde el principio
											$sql = "UPDATE tur SET fec_out = ADDTIME(fec_in, '".$tiempo_para_salida."'), llamado = 0 WHERE id_tur = '".$id_tur."'";
											mysql_query($sql, $pconnect);
											
											
											
											
											// pedido ponce
											// los concerjes me dicen que esta mal
											/*
											$sql = "UPDATE sys_comprobante_detalle SET idproducto = '".$pro_ori['id_pro']."', importe = '".$pro_ori['val']."', correjido = 4  WHERE iddetalle = '".$lo_que_tiene['iddetalle']."'";
											mysql_query($sql, $pconnect);
											
											$sql = "UPDATE tur SET llamado = 0 WHERE id_tur = '".$id_tur."'";
											mysql_query($sql, $pconnect);
											*/
											
										}
									}
									
									// 3) de ahora en mas debemos calcular cuanto lleva y que es lo que le corresponde 
									//    entonces cada 15 le agregamos medio periodo, y asi continuamente
									if($regla1 == false && $regla2 == false){
										// podemos realizar la regla tres
										
										// obtenemos el precio original y nos fijamos si esta cargado en la hab
										$sql = "
											SELECT pro.* FROM tur
												INNER JOIN hab ON hab.nro = tur.nro
												INNER JOIN pro ON pro.tip = hab.tip
											WHERE
												tur.est_tur = 1 AND
												tur.nro = '".$nro_hab."' AND
												pro.bar = 0 AND
												pro.promo_hab = 0 AND
												pro.dia = '".$dia_act."' AND
												pro.hor = '".$hor_act."'
											LIMIT 1
										";
										$result = mysql_query($sql, $pconnect);
										while($fila = mysql_fetch_assoc($result)){
											$id_original = $fila['id_pro'];
											$pro_ori = $fila;
										}
										mysql_free_result($result);
										unset($fila);
										
										$sql = "
										SELECT sys_comprobante_detalle.* FROM tur
										INNER JOIN sys_comprobante ON sys_comprobante.id_turno = tur.id_tur
										INNER JOIN sys_comprobante_detalle ON sys_comprobante_detalle.idcomprobante = sys_comprobante.idcomprobante
										WHERE
											tur.est_tur = 1 AND
											tur.nro = '".$nro_hab."' AND
											sys_comprobante_detalle.idproducto = '".$id_original."'
										";
										$result = mysql_query($sql, $pconnect);
										if(mysql_affected_rows($pconnect) >= 1){
																						
											//calculamos la mitad de la cantidad
											$cantidad = 0.5;
											// no hace falta calcular el precio lo realiza automaticamente al colocar 0.5
											
											// calculamos la mitad del tiempo
											$exp_hor = explode(":", $pro_ori['tie']);
											$tiempo_min = $exp_hor[1] + ($exp_hor[0] * 60);
											$tie_mitad = date("H:i", mktime($exp_hor[0], $exp_hor[1], $exp_hor[2], 0, 0, 0) - (($tiempo_min / 2) * 60));
											
											// tambien debemos completar lo que le falta a la habitacion 
											// si es que el conserje lo modifica manualmente
											
											$can_medios_turnos = ($tiempo_limite - $tiempo_de_espera) / ($tiempo_min / 2);
											$vueltas = (int)$can_medios_turnos;
											
											for($i = 0; $i <= $vueltas; ++$i){
											
												// agregamos el nuevo medio periodo
												$sql = "
												INSERT INTO sys_comprobante_detalle (
													
													idcomprobante,
													idproducto,
													cantidad,
													importe,
													detalle,
													num_serie,
													num_inventario,
													fecha_garantia,
													idestado,
													mostrar,
													id_usuario_carga,
													tiempo,
													correjido
													
												) VALUES (
												
													".$id_com.",
													'".$pro_ori['id_pro']."',
													'".$cantidad."',
													'".$pro_ori['val']."',
													'',
													'',
													'',
													'',
													'',
													'1',
													'".$id_usu_turno."',
													'".$tie_mitad."',
													'4'
													
												)";
												mysql_query($sql, $pconnect);
												
												// modificamos la hora de salida desde que entro tiene original
												$sql = "UPDATE tur SET fec_out = ADDTIME(fec_out, '".$tie_mitad."'), llamado = 0 WHERE id_tur = '".$id_tur."'";
												mysql_query($sql, $pconnect);
											
											}
											
											
											
										}
										
										
										
									}
									
								}
								
							}
							
							if($count_precio == 0){
								
								$sql = "
								SELECT * FROM pro 
								
								INNER JOIN hab ON hab.nro = '".$nro_hab."'
								
								WHERE
									
									pro.tip = hab.tip AND
									pro.promo_hab = 1 AND
									pro.dia = '".$dia."' AND
									pro.hor = '".$hora."'
			
								GROUP BY pro.dia
								ORDER BY pro.val DESC
								
								LIMIT 1
								";
								$result = mysql_query($sql, $pconnect);
								while($fila = mysql_fetch_assoc($result)){
									$pro[] = $fila;
								}
								mysql_free_result($result);
								unset($fila);
								
								//echo $sql."<br />";
								//printr($pro);
								
								////////////////////////////////////////////////
								// si no ahi datos en $pro[] tenemos un error //
								////////////////////////////////////////////////
								
								// si paso el tiempo de espera obtenemos la fecha del turno
								$sql = "SELECT tur FROM hab WHERE nro = '".$nro_hab."'";
								$result = mysql_query($sql, $pconnect);
								while($fila = mysql_fetch_assoc($result)){
									$fec_in = $fila['tur'];
								}
								mysql_free_result($result);
								unset($fila);
								
								// tiempo que le corresponde al cliente segun corte								
								$tiempo_para_salida = hora_salida($fec_in, $pro[0]['tie'], $pro[0]['salida']);
								
								// cargamos el turno
								$sql = "INSERT INTO tur (nro, est_tur, fec, fec_in, fec_out) 
								VALUES ('".$nro_hab."', 1, NOW(), '".$fec_in."', ADDTIME('".$fec_in."', '".$tiempo_para_salida."'))";
								mysql_query($sql, $pconnect);
								
								// obtenemos el id de turno
								$id_tur = mysql_insert_id($pconnect);							
								
								// obtenemos el porteto ultimo logeado
								$sql = "
								SELECT sys_turno_usuario.idturno_usu, sys_turno_usuario.id_usuario FROM sys_turno_usuario
								INNER JOIN sys_usuario ON sys_usuario.id_usuario =  sys_turno_usuario.id_usuario
								WHERE sys_usuario.id_tipo = 2
								ORDER BY fecha_in DESC LIMIT 1;
								";
								$result = mysql_query($sql, $pconnect);
								while($fila = mysql_fetch_assoc($result)){
									$idturno_usu = $fila['idturno_usu'];
									$id_usuario = $fila['id_usuario'];
								}
								mysql_free_result($result);
								unset($fila);
								
								//// comp_tipo 1 es venta
								$sql = "
								
								INSERT INTO sys_comprobante (
								
								fecha,
								numero,
								numero_facturero,
								idcomp_tipo,
								identidad,
								idoperacion,
								idmoneda,
								cotizacion,
								detalle,
								idusuario_carga,
								idtipomov,
								idempresa,
								mensual,
								renueva_contrato,
								idfacturero,
								iddeposito,
								id_turno,
								id_turno_apertura
								
								) VALUES ( NOW(), '', '', '1', '1', '', '', '', '', '', '', '1', '', '', '', '', '".$id_tur."', '".$idturno_usu."')";
								//echo $sql."<br>";
								
								mysql_query($sql, $pconnect);
								$id_com = mysql_insert_id($pconnect);
								
								$sql = "
								INSERT INTO sys_comprobante_detalle (
								
								idcomprobante,
								idproducto,
								cantidad,
								importe,
								detalle,
								num_serie,
								num_inventario,
								fecha_garantia,
								idestado,
								mostrar,
								id_usuario_carga,
								tiempo,
								correjido
								
								) VALUES (
								
								".$id_com.",
								'".$pro[0]['id_pro']."',
								'1',
								'".$pro[0]['val']."',
								'',
								'',
								'',
								'',
								'',
								'1',
								'".$id_usuario."',
								'".$tiempo_para_salida."',
								'1'
								
								)";
								//echo $sql."<br>";
								
								mysql_query($sql, $pconnect);

							}
							
							unset($pro);
							
						}
					}				
				}
				
				// paso a estado amarillo
				if($est_hab == 2){
					
					if($glog == 1){
						
						//echo "<br/>UPDATE amarillo a ".$nro_hab; 
						$sql = "UPDATE tur SET cambio_ama = NOW() WHERE est_tur = 1 AND nro = '".$nro_hab."'";
						mysql_query($sql, $pconnect);
						
					}else if ($glog == 0){
						
						$sql = "SELECT TIMESTAMPDIFF(MINUTE, cambio_ama, NOW()) AS tiempo FROM tur WHERE est_tur=1 AND nro = '".$nro_hab."'";
						$result = mysql_query($sql, $pconnect);
						if(mysql_affected_rows()>0){
							
							while($fila = mysql_fetch_assoc($result)){
								$tiempoamarillo = $fila['tiempo'];
							}
							//echo "</br>tiempo_a: ".$tiempoamarillo;
							if((int)$tiempoamarillo >= 1){
								
								$sql = "UPDATE hab SET tur = NOW() WHERE nro = '".$nro_hab."'";
								mysql_query($sql, $pconnect);
								
								
								// revisamos si se le pude colocar un producto mejor segun el tiempo
								

								// id de comprobante del turno de la habitacion
								$sql = "
								SELECT tur.id_tur, tur.fec_in, sys_comprobante.idcomprobante FROM tur
								INNER JOIN sys_comprobante ON sys_comprobante.id_turno = tur.id_tur									
								WHERE tur.est_tur = 1 AND tur.nro = '".$nro_hab."'
								";
								$result = mysql_query($sql);
								while($fila_comp = mysql_fetch_assoc($result)){
									$id_tur = $fila_comp['id_tur'];
									$fec_in = $fila_comp['fec_in'];
									$id_com = $fila_comp['idcomprobante'];
								}
								mysql_free_result($result);
								unset($fila_comp);
									
								// calculamos el dia y la hora para la fecha de entrada
								$dia_act = traer_dia_producto($fec_in);
								$hor_act = traer_hora_producto($fec_in);
								
								// obtenemos el ultimo conserje logeado
								$sql = "
								SELECT sys_turno_usuario.id_usuario FROM sys_turno_usuario
								INNER JOIN sys_usuario ON sys_usuario.id_usuario = sys_turno_usuario.id_usuario
								WHERE id_tipo = 2
								ORDER BY fecha_in DESC LIMIT 1;
								";
								$result = mysql_query($sql);
								while($fila = mysql_fetch_assoc($result)){
									$id_usu_turno = $fila['id_usuario'];
								}
								mysql_free_result($result);
								unset($fila);
									
								// obtenemos el precio original y nos fijamos si esta cargado en la hab
								$sql = "
									SELECT pro.* FROM tur
										INNER JOIN hab ON hab.nro = tur.nro
										INNER JOIN pro ON pro.tip = hab.tip
									WHERE
										tur.est_tur = 1 AND
										tur.nro = '".$nro_hab."' AND
										pro.bar = 0 AND
										pro.promo_hab = 0 AND
										pro.dia = '".$dia_act."' AND
										pro.hor = '".$hor_act."'
									LIMIT 1
								";
								$result = mysql_query($sql, $pconnect);
								while($fila = mysql_fetch_assoc($result)){
									$id_original = $fila['id_pro'];
								}
								mysql_free_result($result);
								unset($fila);
								
								$sql = "
								SELECT sys_comprobante_detalle.* FROM tur
								INNER JOIN sys_comprobante ON sys_comprobante.id_turno = tur.id_tur
								INNER JOIN sys_comprobante_detalle ON sys_comprobante_detalle.idcomprobante = sys_comprobante.idcomprobante
								WHERE tur.est_tur = 1 AND tur.nro = '".$nro_hab."' AND sys_comprobante_detalle.idproducto = '".$id_original."'
								";
								$result = mysql_query($sql, $pconnect);
								if(mysql_affected_rows($pconnect) >= 1){
									// tiene producto original
									
									// tiempo total con original
									$sql = "
									SELECT TIMESTAMPDIFF(MINUTE, tur.fec_in, tur.fec_out) AS tiempo_tot FROM tur
									INNER JOIN sys_comprobante ON sys_comprobante.id_turno = tur.id_tur									
									WHERE tur.est_tur = 1 AND tur.nro = '".$nro_hab."'
									";
									$result = mysql_query($sql, $pconnect);
									while($fila = mysql_fetch_assoc($result)){
										$tiempo_tot = $fila['tiempo_tot'];
									}
									mysql_free_result($result);
									unset($fila);
									
									// tiempo que lleva en la hab
									$sql = "
									SELECT TIMESTAMPDIFF(MINUTE, tur.fec_in, NOW()) AS tiempo_act FROM tur
									INNER JOIN sys_comprobante ON sys_comprobante.id_turno = tur.id_tur									
									WHERE tur.est_tur = 1 AND tur.nro = '".$nro_hab."'
									";
									$result = mysql_query($sql, $pconnect);
									while($fila = mysql_fetch_assoc($result)){
										$tiempo_act = $fila['tiempo_act'];
									}
									mysql_free_result($result);
									unset($fila);
									
									// si el tiempo que lleva es menor al que tiene
									// buscamos la promo y comprobamos si llega
									if($tiempo_act < $tiempo_tot){
										
										// obtenemos el precio promo y comprobamos si llega
										$sql = "	
											SELECT pro.* FROM tur
												INNER JOIN hab ON hab.nro = tur.nro
												INNER JOIN pro ON pro.tip = hab.tip
											WHERE
												tur.est_tur = 1 AND
												tur.nro = '".$nro_hab."' AND
												pro.bar = 0 AND
												pro.promo_hab = 1 AND
												pro.dia = '".$dia_act."' AND
												pro.hor = '".$hor_act."'
											LIMIT 1
										";
										$result = mysql_query($sql);
										while($fila = mysql_fetch_assoc($result)){
											$pro_pro = $fila;
										}
										mysql_free_result($result);
										unset($fila);
										
										// tiempo que le corresponde al cliente segun corte								
										$tiempo_para_salida = hora_salida($fec_in, $pro_pro['tie'], $pro_pro['salida']);
										
										$exp_tiempo_pro = explode(":", $tiempo_para_salida);
										$tiempo_pro = $exp_tiempo_pro[1] + ($exp_tiempo_pro[0] * 60);
										
										// si el tiempo es menor a la promocion le colocamos la promocion
										if($tiempo_act < $tiempo_pro){

											// eliminamos todo los turnos del comprobante
											$sql = "
											SELECT iddetalle FROM sys_comprobante_detalle
											INNER JOIN pro ON pro.id_pro = sys_comprobante_detalle.idproducto
											WHERE sys_comprobante_detalle.idcomprobante = '".$id_com."' AND pro.bar = 0;
											";
											$result = mysql_query($sql);
											while($fila_del_det = mysql_fetch_assoc($result)){
												$sql = "DELETE FROM sys_comprobante_detalle WHERE iddetalle = '".$fila_del_det['iddetalle']."'";
												mysql_query($sql);
											}
											mysql_free_result($result);
											unset($fila_del_det);
											
											// agregamos el nuevo producto
											$sql = "
											INSERT INTO sys_comprobante_detalle (
											
											idcomprobante,
											idproducto,
											cantidad,
											importe,
											detalle,
											num_serie,
											num_inventario,
											fecha_garantia,
											idestado,
											mostrar,
											id_usuario_carga,
											tiempo,
											correjido
											
											) VALUES (
											
											".$id_com.",
											'".$pro_pro['id_pro']."',
											'1',
											'".$pro_pro['val']."',
											'',
											'',
											'',
											'',
											'',
											'1',
											'".$id_usu_turno."',
											'".$tiempo_para_salida."',
											'4'
											
											)";
											mysql_query($sql, $pconnect);
											
											// modificamos la hora de salida desde el principio
											$sql = "UPDATE tur SET fec_out = ADDTIME(fec_in, '".$tiempo_para_salida."'), llamado = 0 WHERE id_tur = '".$id_tur."'";
											mysql_query($sql, $pconnect);
																					
										}
										
									}
									
									
								}
								
								
							}
							
						}else if(mysql_affected_rows()==0){
							
							$sql = "UPDATE hab SET tur = NOW() WHERE nro = '".$nro_hab."'";
							mysql_query($sql, $pconnect);
							
						}

					}
				}
				
				// paso a estado verde
				if($est_hab == 1){
				
					if($glog == 1){
						
						//echo "<br/>UPDATE verde a ".$nro_hab; 
						$sql = "UPDATE tur SET cambio_ver = NOW() WHERE est_tur = 1 AND nro = '".$nro_hab."'";
						mysql_query($sql, $pconnect);
						
					}
					else if ($glog == 0){
						
						$sql = "SELECT TIMESTAMPDIFF(MINUTE, cambio_ver, NOW()) AS tiempo FROM tur WHERE est_tur=1 AND nro = '".$nro_hab."'";
						$result = mysql_query($sql, $pconnect);
						if(mysql_affected_rows()>0){
							
							while($fila = mysql_fetch_assoc($result)){
								$tiempoverde = $fila['tiempo'];
							}
							
							//echo "</br>tiempo_a: ".$tiempoverde;
							if((int)$tiempoverde >= 1){
								
								$sql = "UPDATE hab SET tur = NOW() WHERE nro = '".$nro_hab."'";
								mysql_query($sql, $pconnect);
								
								// si paso a verde y ahi un turno cargado
								// colocamos el turno como pendiente
								// y dejamos la habitacion lista para usar
								// luego de un minuto
								$sql = "SELECT tur.id_tur FROM tur WHERE est_tur = 1 AND nro = '".$nro_hab."'";
								$result = mysql_query($sql, $pconnect);
								while($fila = mysql_fetch_assoc($result)){
									$id_tur_pen = $fila['id_tur'];
								}
								mysql_free_result($result);
								unset($fila);
								$tiene_turno = mysql_affected_rows($pconnect);

								$sql = "SELECT tur.id_tur FROM tur WHERE est_tur = 2 AND nro = '".$nro_hab."'";
								$result = mysql_query($sql, $pconnect);
								$tiene_turno_pend = mysql_affected_rows($pconnect);
								mysql_free_result($result);
								
								//echo "<br>tiene_turno: ".$tiene_turno;
								//echo "<br>tiene_turno_pend: ".$tiene_turno_pend;
								
								// si tenemos que pasar un turno y no alla ningun turno ya pendiente
								if($tiene_turno != 0 && $tiene_turno_pend == 0){
									
									$sql = "UPDATE tur SET est_tur = 2 WHERE id_tur = '".$id_tur_pen."'";
									mysql_query($sql, $pconnect);
									
								}
								
								
							}
							
						}else if(mysql_affected_rows()==0){

							$sql = "UPDATE hab SET tur = NOW() WHERE nro = '".$nro_hab."'";
							mysql_query($sql, $pconnect);
							
						}
					}
					
				}
				
				if ($glog > 0){
					
					// solo cuando la habitacion pasa de un estado a otro mientras
					// tanto no graba log ya que es el mismo estado
					/*
					$sql = "INSERT INTO log_hab (nro, est) VALUES ('".$nro_hab."', '".$est_hab."')";
					mysql_query($sql, $pconnect);
					*/
				}
				
			}else{
				
				echo "er:".$nro_hab."-".$est_hab." - ".$con_azul;
				
			}
			
			echo "<br />";
			echo chr(10);
			echo chr(13);
			
		}
	}
	// caracter final es muy importante ya que se utiliza en 
	// la arduino ethernet para que comienze un nuevo contador
	echo "#";
	echo $_REQUEST['n'];
	echo " - ";
	echo $_REQUEST['c'];

}

// control de usuarios logeados y deslogeados
$sql = "SELECT idturno_usu, fecha_act, TIMESTAMPDIFF(MINUTE, fecha_act, NOW()) AS tiempo_log FROM sys_turno_usuario WHERE estado = 1";
$result_log = mysql_query($sql, $pconnect);
while($fila_log = mysql_fetch_assoc($result_log)){
	if($fila_log['tiempo_log'] >= 1){
		$sql = "UPDATE sys_turno_usuario SET estado = 2 WHERE idturno_usu = '".$fila_log['idturno_usu']."'";
		mysql_query($sql, $pconnect);
	}else{
		if($fila_log['fecha_act'] != '0000-00-00 00:00:00'){
			$sql = "UPDATE sys_turno_usuario SET estado = 1 WHERE idturno_usu = '".$fila_log['idturno_usu']."'";
			mysql_query($sql, $pconnect);
		}
	}
}
mysql_free_result($result_log);
unset($result_log);
unset($fila_log);

mysql_close($pconnect);

?>