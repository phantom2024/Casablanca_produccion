<?php
require_once("boot.php");

if($_REQUEST['hab']){

		$sql = "
		SELECT
		
			id_tur,
			nro,
			est_tur,
			fec_in,
			fec_out,
			TIMEDIFF(NOW(), fec_in) AS tiempo_tot,
			TIMEDIFF(fec_out, NOW()) AS tiempo_hab,
			chequeo,
			llamado,
			mostrar_cliente
			
		FROM tur 
		
		WHERE
		
			est_tur IN (1,2) AND mostrar_cliente = 1 LIMIT 1
			
		";
		$result = mysql_query($sql, $pconnect);
		$total = 0;
		while($fila = mysql_fetch_assoc($result)){
			
			$id_tur = $fila['id_tur'];
			$hab = $fila['nro'];
			
			// armamos fechas
			$fec_in_c = date_create($fila['fec_in']);
			$fec_in = date_format($fec_in_c, 'd/m/Y H:i');
		
			$fec_out_c = date_create($fila['fec_out']);
			$fec_out = date_format($fec_out_c, 'd/m/Y H:i');
			
			/*
			?>
			<br />
			<div><b>Fecha Entrada:</b> <?php echo $fec_in; ?></div>
			<div><b>Fecha Salida:</b> <?php echo $fec_out; ?></div>
			<div>
				<div style="float:left;">
					<b>Tiempo: </b><?php echo $fila['tiempo_tot']; ?>
				</div>
				<div style="float:left;"><b>&nbsp;-&nbsp;</b></div>
				<div style="float:left; <?php echo $alerta_tiempo_atras; ?>">
					<?php echo $fila['tiempo_hab']; ?>
				</div>
				<div style="clear:both;"></div>
			</div>
            <?php
            */
			
			?>
            <div class="fittext1">
            	<b>Habitac&iacute;on N&uacute;mero <?php echo $hab; ?></b>
            </div>
			<br />
			<table class="fittext2" cellpadding="1" cellspacing="1" border="0" width="100%">    
				<tr>
					<td><b>Turno</b></td>
					<td><b>Valor</b></td>
					<td><b>Tiempo</b></td>
				</tr>
			<?php
			
			$sql = "
			SELECT
				
				tur.*,
				sys_comprobante_detalle.*,
				pro.*
				
			FROM tur 
			
			INNER JOIN sys_comprobante ON sys_comprobante.id_turno = tur.id_tur
			INNER JOIN sys_comprobante_detalle ON sys_comprobante_detalle.idcomprobante = sys_comprobante.idcomprobante
			INNER JOIN pro ON pro.id_pro = sys_comprobante_detalle.idproducto
			
			WHERE
				
				tur.est_tur IN (1,2) AND
				pro.bar = 0 AND
				tur.id_tur = '".$id_tur."'
				
			";
			$result_t = mysql_query($sql, $pconnect);
			
			$total_turno = 0;
			$trasnoche=0;
			while($fila_t = mysql_fetch_assoc($result_t)){
				
				if($fila_t['hor'] == 3){
					$trasnoche = 1;
				}
				$tie_exp = explode(":", $fila_t['tie']);
				$id_tur = $fila_t['id_tur'];
				$fec_in_tur = $fila_t['fec_in'];
				
				
				if($fila_t['cantidad']==1){
					$turno = "(1)";
				}else if($fila_t['cantidad']==0.5){
					$turno="(1/2)";
				}else if($fila_t['cantidad']==2){
					$turno="(2)";
				}
				
				$horas = $tie_exp[0];
				$minutos = $tie_exp[1];
				$total_minutos = ($horas * 60)+$minutos; 
				$total_minutos *= $fila_t['cantidad'];;
				$total_horas = $total_minutos / 60;
				
				$hora = explode(".", $total_horas);
				if($hora[0]<10){
					$horafinal = "0".$hora[0];
				}else{
					$horafinal = $hora[0];
				}
				if($hora[1]==null){
					$horafinal .= ":00";
				}else{
					$min = $total_horas-$hora[0];//esto es por si es 03:15:00 por ejemplo el turno
					$min_split = explode(".",(60*$min));
					if($min_split[0]<10){
						$horafinal .= ":0".($min_split[0]);
					}else{
						$horafinal .= ":".($min_split[0]);
					}
				}

				echo "<tr>";
					echo "<td>".$fila_t['pro']." ".$turno."</td>";
					echo "<td>".$fila_t['importe']*$fila_t['cantidad']."</td>";
					echo "<td>".$horafinal."</td>";
				echo "</tr>";
				
				$total_turno = $total_turno + $fila_t['importe']*$fila_t['cantidad'];
				
			}
			
			?>
				<tr>
					<td colspan="3"><b>Total Turno: <? echo $total_turno; ?> $</b></td>
				</tr>
			</table>
			<?php
			$sql = "
			
			SELECT sys_comprobante_detalle.*, pro.* FROM tur 
			
			INNER JOIN sys_comprobante ON sys_comprobante.id_turno = tur.id_tur
			INNER JOIN sys_comprobante_detalle ON sys_comprobante_detalle.idcomprobante = sys_comprobante.idcomprobante
			
			INNER JOIN pro ON pro.id_pro = sys_comprobante_detalle.idproducto
			
			WHERE
			
			pro.bar = 1 AND
			est_tur IN (1,2) AND
			
			sys_comprobante_detalle.mostrar = 1 AND
			
			tur.id_tur = '".$id_tur."'";
			
			//echo $sql;
			$result_t2 = mysql_query($sql, $pconnect);
			if(mysql_affected_rows($pconnect) > 0){
				
			?>
			<br />
			<table class="fittext2" cellpadding="1" cellspacing="1" border="0" width="100%">    
				<tr>
					<td><b>Producto</b></td>
					<td><b>Valor</b></td>
					<td><b>Cantidad</b></td>
					<td><b>Sub Total</b></td>
				</tr>
			<?php
			
			$total_bar = 0;
			while($fila_t2 = mysql_fetch_assoc($result_t2)){
				
				$id_tur = $fila_t2['id_tur'];
				
				echo "<tr>";
					echo "<td>".$fila_t2['pro']."</td>";
					echo "<td>".$fila_t2['importe']."</td>";
					echo "<td>".$fila_t2['cantidad']."</td>";
					echo "<td>".$fila_t2['importe']*$fila_t2['cantidad']."</td>";
				echo "</tr>";
				
				$total_bar = $total_bar + $fila_t2['importe'] * $fila_t2['cantidad'];		
			}
			
			?>
				<tr>
					<td colspan="4"><b>Total Bar: <? echo $total_bar; ?> $</b></td>
				</tr>
			</table>
			<div class="fittext2">
			<?php
			}else{
				echo "<br /><b style='color:#F00'>Sin Bar</b>";
			}
			
			
			$sql = "
			SELECT
				
				sys_comprobante_detalle.importe
				
			FROM tur 
			
			INNER JOIN sys_comprobante ON sys_comprobante.id_turno = tur.id_tur
			INNER JOIN sys_comprobante_detalle ON sys_comprobante_detalle.idcomprobante = sys_comprobante.idcomprobante
			INNER JOIN pro ON pro.id_pro = sys_comprobante_detalle.idproducto
			
			WHERE
		
				tur.est_tur IN (1,2) AND
				pro.bar = 2 AND
				tur.id_tur = '".$id_tur."'
				
			";
			$result_des = mysql_query($sql);
			$total_descuento = 0;
			while($fila_des = mysql_fetch_assoc($result_des)){
				$total_descuento = $fila_des['importe'];
			}
			
			if($total_descuento != 0){
			?>
				<br />
				<b>Total Descuento: </b><?php echo $total_descuento; ?>
			<?php
			}
			
			// armamos total	
			$total = $total_turno + $total_bar + $total_descuento;
			
			?>
			<br />
			<br />
			<div><b>Total:</b> <? echo $total; ?> $</div>
            </div>
			<?php
		
		
	}
	
	exit;
	
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Casablanca</title>
	
<link href="css/cupertino/jquery-ui-1.10.3.custom.css" rel="stylesheet">
<link href="css/style.css" rel="stylesheet">

<style>
	body{
		padding:0;
		margin:0;
		/*
		background-color:#523230;
		font-size: 100%;
		*/
		background-color:#523230;
		color: #fff;
		font: 16px/1.8 sans-serif;
	}
	#contenedor{
		/*
		background-color:#523230;
		position:relative;
		*/
	}
	.letra{
		float:left;
		padding:5px;
		margin:5px;
		text-transform: capitalize;
		font-family:"Times New Roman";
		font-size:100%;
		font-weight:bold;
		border-bottom:#000 2px solid;
	}
	.titulo{
		width:40%;
		color:#FFF;
	}
	.sinpromo{
		width:25%;
		color:#FFCC01;
	}
	.promo{
		width:25%;
		color:#FF9901;
	}
	.habitacion{
		color:#FFF;
	}
</style>

<script src="js/jquery-1.9.1.js"></script>
<script src="js/jquery-ui-1.10.3.custom.js"></script>
<script src="js/jquery.fittext.js"></script>
<script>

function actualiza_pantalla(){

	$.ajax({
		type: 'post',
		url: 'pantalla_cliente.php',
		data: 'hab=on',
		success: function(data){
			$("#contenedor").html(data);
			setTimeout("actualiza_pantalla()", 15000);
		}
	});

}

$(function() {
	
	var wi_height = $(window).height();
	if(wi_height <= 600){
		$("#contenedor").fitText(0, { minFontSize: '12px', maxFontSize: '21px' });
	}
	if(wi_height > 600){
		$("#contenedor").fitText(0, { minFontSize: '12px', maxFontSize: '25px' });
	}
	
	actualiza_pantalla();	

});

</script>

</head>
<body>

    <div id="contenedor" class="habitacion"></div>
    
</body>
</html>