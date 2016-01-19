<?php
set_time_limit(0);

// base de datos
$pconnect = mysql_pconnect("localhost", "casa_blanca", "Lf43Zf9YcsMtR2nM");
mysql_select_db("casa_blanca", $pconnect);

`mode com4: BAUD=19200 PARITY=N data=8 stop=1 xon=off`;
$fp = fopen ("COM4:", "rb");
if(!$fp){
	echo "Uh-oh. Port not opened.";
}else{

	$cadena = "";
	$can_paq = 0;
	$c = 0;
	$llena = true;
	while(($char = fread($fp, 1)) != "nunca"){

		if($char == chr(13)){
			$llena = false;
		}		
		if($llena == true){
			$cadena .= $char;
		}
		if($llena == false){
		
			echo $cadena."\n";
			$paq = explode(";",$cadena);

			$sql = "UPDATE hab SET est = '".trim($paq[1])."' WHERE nro = '".trim($paq[0])."'";
			if(mysql_query($sql, $pconnect)){
				
				// colocamos justo despues del update para no perder el valor
				$glog = mysql_affected_rows($pconnect);

				if(trim($paq[1]) == 3)
				{			
					$sql = "SELECT id_tur, fec_in FROM tur WHERE est_tur = 1 AND id_hab = '".trim($paq[0])."'";
					$result = mysql_query($sql, $pconnect);
					if(mysql_affected_rows($pconnect) > 0){
						
						while($fila = mysql_fetch_assoc($result)){
							$id_tur = $fila['id_tur'];
							$fec_in = $fila['fec_in'];
						}
						
						if($fec_in == 0){
							
							$sql = "
							SELECT SEC_TO_TIME( SUM( TIME_TO_SEC( pro.tie ) ) ) AS tiempo FROM tur_det
							INNER JOIN pro ON pro.id_pro = tur_det.id_pro
							WHERE id_tur = '".$id_tur."'
							";
							$result = mysql_query($sql, $pconnect);
							while($fila = mysql_fetch_assoc($result)){
								$tiempo = $fila['tiempo'];
							}
							
							$sql = "UPDATE tur SET fec_in = NOW(), fec_out = ADDTIME(NOW(), '".$tiempo."') WHERE id_tur = '".$id_tur."'";
							mysql_query($sql, $pconnect);
							
						}
						
						$est_3_st = 0;
						
					}else{
						// ver esto
						echo "Sin turno\n";
						$est_3_st = 1;
					}
					
				}
				
				if ($glog > 0){
					$sql = "INSERT INTO log_hab (id_hab, est, sin) VALUES ('".trim($paq[0])."', '".trim($paq[1])."', '".$est_3_st."')";
					mysql_query($sql, $pconnect);
				}
			}
			$cadena = "";			
			$llena = true;
		}

	}
	
}

fclose ($fp);

?>