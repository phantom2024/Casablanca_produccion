<?php
require_once("boot.php");

	$dia_pro = traer_dia_producto();
	$hora_pro = traer_hora_producto();
	
	/*
	
	$sql = "
	SELECT pro.tip, tip_hab.tip, pro.pro, pro.val, pro.tie, pro.hor FROM pro 
	
	INNER JOIN tip_hab ON tip_hab.id_tip = pro.tip
	
	WHERE pro.bar = 0 AND pro.hor <> 3 
	
	";*/
	
	$sql = "SELECT * FROM tip_hab";
	$result = mysql_query($sql, $pconnect);

	header ("Content-Type:text/xml");
	
	echo "<productos>";
	
	echo "<config>";
		echo "<dia_pro>".$dia_pro."</dia_pro>";
		echo "<hora_pro>".$hora_pro."</hora_pro>";
	echo "</config>";
	
	for($d=1;$d<=3;$d++)
	{
		echo "<lista>";
		for($i=1;$i<=2;$i++)
		{
			echo "
				  <pantalla>
					  <id_dia>".$d."</id_dia>
			";
			$sql = "SELECT * FROM tip_hab";
			$result = mysql_query($sql, $pconnect);
			echo "<id_hora>".$i."</id_hora>";
			echo "<lista_habitaciones>";
			while($fila = mysql_fetch_assoc($result)){
				
				$sql2 = "SELECT * FROM pro WHERE tip =".$fila['id_tip']." AND dia=".$d." AND hor=".$i." ORDER BY dia, hor";
				$result2 = mysql_query($sql2);
				$hab="";
				$contador=0;
				echo "
				<habitacion>
							<nombre>".$fila['tip']."</nombre>
				";
				while($fila2 = mysql_fetch_assoc($result2))
				{
					$pro = $fila2['pro'];
					if (strpos($pro,'PROMO'))
					{
						$tiempo = explode(":", $fila2['tie']);
						$fila2['tie'] = $tiempo[0].":".$tiempo[1];
						
						$hab .= "
							<promo>
								<hora>".$fila2['tie']."</hora>
								<precio>".$fila2['val']."</precio>
							</promo>
						
						";
						$contador++;
						
						
						if($contador == 2)
						{
							echo $hab;
							$hab = "";
							$contador=0;
						}
					}
					else if(strpos($pro,'ORIGINAL'))
					{
						$tiempo = explode(":", $fila2['tie']);
						$fila2['tie'] = $tiempo[0].":".$tiempo[1];
						
						$hab .= "
						
								<sinpromo>
									<hora>".$fila2['tie']."</hora>
									<precio>".$fila2['val']."</precio>
								</sinpromo>
						
						";
						$contador++;
				
						if($contador == 2)
						{
							echo $hab;
							$hab = "";
							$contador=0;
						}
					}
				}
				echo "</habitacion>";
				
			}
			echo "</lista_habitaciones>";
			echo "</pantalla>";
		}
		echo "</lista>";
	}

	echo "</productos>";
	

?>