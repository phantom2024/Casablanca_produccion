<?php
ini_set('max_execution_time',0);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>caja</title>
<link href="../css/cupertino/jquery-ui-1.10.3.custom.css" rel="stylesheet">
<style>
.tab_detalle_tur_hab{
	text-align:center;
	width:100%;
	margin:0;
	padding:2;
}
.tab_detalle_tur_hab th{
	border:#CCC 1px solid;
}
.tab_detalle_tur_hab td{
	border:#CCC 1px solid;
}
.trtit_tot{
	font-weight:bold;
	font-size:16px;
}
</style>
<script src="../js/jquery-1.9.1.js"></script>
<script src="../js/jquery-ui-1.10.3.custom.js"></script>
<script>
$(function() {
	
	$(".ver_tur").click(function(event) {
		
		$(".tab_oc").hide();
		
		var hr = $(this).attr('href');
		hr = hr.replace("#", "");
		console.log(hr);
		
		$("#tab_"+hr).show();
		
	});
	
});
</script>

</head>
<body>



<br/>
<form action="abm.php" method="get">
<input type="hidden" id="fx" name="fx" value="caja">
<table>
<tr>
    <th>Turno</th>
    <th>Empleado</th>
</tr>
<tr>
<td>
	<select id="turno" name="turno">
		<option value="sel_todos">Todos</option>
		<?
            $consulta = "SELECT * FROM sys_turno";
            $resultado = mysql_query($consulta);
            while ($fila = mysql_fetch_array($resultado)){
				if(is_null($fila['fecha_c'])){
					$estado = "Abierto";
				}else{
					$estado = $fila['fecha_c'];
				}
				if($_GET['turno'] == $fila['id_turno']){
					echo "<option value='".$fila['id_turno']."' selected>".$fila['id_turno']." - ".$fila['fecha_a']." - ".$estado."</option>";
				}else{
					echo "<option value='".$fila['id_turno']."'>".$fila['id_turno']." - ".$fila['fecha_a']." - ".$estado."</option>";
				}
            }
        ?>	  
	</select>
</td>
<td>
    <select id="usuario" name="usuario">
    	<option value="sel_todos">TODOS</option>
		<?
            $consulta = "SELECT id_usuario, usuario FROM sys_usuario WHERE id_tipo = 2";
            $resultado = mysql_query($consulta);
            while ($fila = mysql_fetch_array($resultado)){
				if($_GET['usuario']==$fila['id_usuario'])
				{
					echo "<option value='".$fila['id_usuario']."' selected>".$fila['usuario']."</option>";
				}
				else
				{
					echo "<option value='".$fila['id_usuario']."'>".$fila['usuario']."</option>";
				}
            }
        ?>	  
    </select>
</td>
<td>
<input type="submit" id="listar_but" value="Listar" onclick="mostrar_tabla();" />
</td>	
</table>


<?

if($_GET['turno']){

	$turno = "";
	if($_GET['turno']){
		
		if($_GET['turno']=='sel_todos'){
			$turno = "";
		}else{
			$turno = " AND cierre_turno.id_turno = '".$_GET['turno']."'";
		}
		
	}
	
	$usuario = "";
	if($_GET['usuario']){
		
		if($_GET['usuario']=='sel_todos'){
			$usuario ="";
		}else{
			$usuario = " AND usuario_cierre.id_usuario = '".$_GET['usuario']."'";
		}
	}
	
		/*
		$date = new DateTime($fecha_input);
		$fec = $date->format('Y-m-d');
		
		$date2 = new DateTime($fecha_hasta);
		$fec_h = $date2->format('Y-m-d');
		*/
		
		$g_sql = "
		SELECT
			
			tur.id_tur,
			est_tur,
			fec,
			fec_in,
			fec_out,
			cambio_ama,
			sys_comprobante_detalle.importe,
			pro.pro,
			sys_comprobante_detalle.cantidad,
			hab.nro,
			tip_hab.tip,
			TIMEDIFF(cambio_ama, fec_in) AS tiempo_hab,
			
			cierre_turno.id_turno,
			usuario_apertura.usuario AS usu_ape,
			usuario_cierre.usuario AS usu_cie,
			
			sys_comprobante.idcomprobante,
			sys_comprobante_detalle.correjido
			
		FROM tur 
		
		INNER JOIN sys_comprobante ON sys_comprobante.id_turno = tur.id_tur 
		INNER JOIN sys_comprobante_detalle ON sys_comprobante_detalle.idcomprobante = sys_comprobante.idcomprobante 
		
		INNER JOIN sys_turno_usuario AS apertura ON apertura.idturno_usu = sys_comprobante.id_turno_apertura
		INNER JOIN sys_turno AS apertura_turno ON apertura_turno.id_turno = apertura.id_turno
		INNER JOIN sys_usuario AS usuario_apertura ON usuario_apertura.id_usuario = apertura.id_usuario
		
		INNER JOIN sys_turno_usuario AS cierre ON cierre.idturno_usu = sys_comprobante.id_turno_cierre
		INNER JOIN sys_turno AS cierre_turno ON cierre_turno.id_turno = cierre.id_turno
		INNER JOIN sys_usuario AS usuario_cierre ON usuario_cierre.id_usuario = cierre.id_usuario
		
		INNER JOIN pro ON pro.id_pro = sys_comprobante_detalle.idproducto
		INNER JOIN hab ON hab.nro = tur.nro
		INNER JOIN tip_hab ON tip_hab.id_tip = hab.tip
		
		WHERE
		
		1 = 1
		
		".$turno."
		
		".$usuario."
		
		GROUP BY id_tur
		
		";
		
		echo $sql;
		
		//este 1=1 es para que no de sintaxis para hacerla mas facil al usar turno y usuario
		
		$resultado = $db->query($g_sql);
	
if(mysql_affected_rows() > 0){
	
	echo "<br/>";
	echo '<table border="1" id="Exportar_a_Excel" width="100%" cellpadding="2" cellspacing="2"><tr>';
	echo "<th>Id Turno</th>";
	echo "<th>Apertura - Cierre</th>";
	echo "<th>Habitaci&oacute;n</th>";
	echo "<th>Estado</th>";
	echo "<th>Fecha in</th>";
	echo "<th>Fecha salida</th>";
	echo "<th>Valor</th>";
	echo "<th>Ver</th>";
	
	$totalValor;
	$totalTiempo;
	
	while ($fila = mysql_fetch_assoc($resultado)){
		
		$tiempo_hab = $fila['tiempo_hab'];	
	
		if($fila['correjido'] == 3){
			$style = "background-color:#FFD5BF;";
		}else{
			$style = "";
		}

		echo "<tr class='alternar' style='".$style."'>";
		echo "<td>".$fila['id_tur']." - ".$fila['id_turno']."</td>";
		echo "<td>".$fila['usu_ape']." - ".$fila['usu_cie']."</td>";
		
		echo "<td>$fila[nro] - $fila[tip]</td>";
		
		if($fila['est_tur']=='0'){
			echo "<td>Cerrado</td>";
		}else{
			echo "<td>Abierto</td>";
		}
		
		//$date = new DateTime($fila['fec']);
		//echo "<td>".$date->format('d-m-Y H:i')."</td>";
		
		$date = new DateTime($fila['fec_in']);
		echo "<td>".$date->format('d-m-Y H:i')."</td>";
		
		//$date = new DateTime($fila['fec_out']);
		//echo "<td>".$date->format('d-m-Y H:i')."</td>";
		
		if($fila['cambio_ama'] == NULL || $fila['cambio_ama'] == '0000-00-00 00:00:00'){
			echo "<td>Sin Salida</td>";
		}else{
			$date = new DateTime($fila['cambio_ama']);
			echo "<td>".$date->format('d-m-Y H:i')."</td>";
		}
		
		//echo "<td>".$fila['cambio_ama']."</td>";
		
		$valor = 0;
		$query = "
		
		SELECT
			
			sys_comprobante_detalle.importe,
			sys_comprobante_detalle.cantidad,
			pro.pro,
			pro.bar,
			pro.tie,
			sys_comprobante_detalle.mostrar
			
		FROM sys_comprobante_detalle
		
		INNER JOIN sys_comprobante ON sys_comprobante.idcomprobante = sys_comprobante_detalle.idcomprobante 
		INNER JOIN pro ON pro.id_pro = sys_comprobante_detalle.idproducto
		
		WHERE
			
			pro.bar IN (0, 1) AND
			sys_comprobante.id_turno = $fila[id_tur]
		
		";		
		$con = mysql_query($query);
		while($tab = mysql_fetch_array($con)){
			if($tab['mostrar'] == 1 || $tab['bar'] == 0){
				$valor += $tab['importe']*$tab['cantidad']; 
			}
		}
		
		//calculo aumento
		$sql = "
		
		SELECT
			
			sys_comprobante_detalle.importe,
			sys_usuario.usuario
			
		FROM sys_comprobante_detalle
		
		INNER JOIN sys_comprobante ON sys_comprobante.idcomprobante = sys_comprobante_detalle.idcomprobante 
		INNER JOIN pro ON pro.id_pro = sys_comprobante_detalle.idproducto
		INNER JOIN sys_usuario ON sys_usuario.id_usuario = sys_comprobante_detalle.id_usuario_carga
		
		WHERE
		
			pro.bar = 3 AND
			sys_comprobante.id_turno = $fila[id_tur]
		
		";
		
		$aumento = 0;
		$result_desc = mysql_query($sql);
		while($fila_aument = mysql_fetch_array($result_desc)){
			$aumento = $fila_aument['importe'];
			$usuario_aumento = $fila_aument['usuario'];
		}
		
		//calculo descuento
		$sql = "
		
		SELECT
			
			sys_comprobante_detalle.importe,
			sys_usuario.usuario
			
		FROM sys_comprobante_detalle
		
		INNER JOIN sys_comprobante ON sys_comprobante.idcomprobante = sys_comprobante_detalle.idcomprobante 
		INNER JOIN pro ON pro.id_pro = sys_comprobante_detalle.idproducto
		INNER JOIN sys_usuario ON sys_usuario.id_usuario = sys_comprobante_detalle.id_usuario_carga
		
		WHERE
		
			pro.bar = 2 AND
			sys_comprobante.id_turno = $fila[id_tur]
		
		";
		
		$descuento = 0;
		$result_desc = mysql_query($sql);
		while($fila_desc = mysql_fetch_array($result_desc)){
			$descuento = $fila_desc['importe'];
			$usuario = $fila_desc['usuario'];
		}
		
		// el total de habitaciones es un count
		$cantidad_hab = $cantidad_hab + 1;
		
		$totalDescuento += ($descuento * -1);
		
		// coloque el importe en rojo si tiene descuento
		if(($descuento * -1) > 0){
			echo "<td style='color:#F00;'>$ "."$valor</td>";
		}else{
			echo "<td>$ "."$valor</td>";
		}
		echo "<td><center><a class='ver_tur' href='#".$fila['id_tur']."'>Ver</a></center></td>";
		echo "</tr>";
		echo '<tr><td colspan=8><div class="tab_oc" id="tab_'.$fila['id_tur'].'" style="display:none;">';
		
		// tabla de detalle 
		echo '<table class="tab_detalle_tur_hab">';
		echo '<tr><td align="left" colspan="5">Detalle del Comprobante</td></tr>';
		echo "<tr><th>Cantidad</th><th>Valor</th><th>Subtotal</th><th align='left'>Tipo</th><th align='left'>Producto</th></tr>";
		$con = mysql_query($query);
		while($tab = mysql_fetch_array($con))
		{
			echo "<tr>";
			echo "<td>$tab[cantidad]</td>";
			
			echo "<td>$tab[importe]</td>";
			$subtotal = $tab['importe'] * $tab['cantidad'];
			echo "<td>".$subtotal."</td>";
			
			if($tab['mostrar'] == 1 || $tab['bar'] == 0){
				echo "<td align='left'>Venta</td>";
			}else{
				echo "<td align='left'>Materia Prima</td>";
			}
			
			// sumamos solo hab
			if($tab['bar'] == 0){
				$totalHab += $subtotal;
			}
			
			// sumamos solo bar
			if($tab['bar'] == 1 && $tab['mostrar'] == 1){
				$totalBar += $subtotal;
			}
			
			if($tab['bar']==0)
			{
				$calc_tie=$tab[tie] * $tab['cantidad'];
				$hora = explode(".", $calc_tie);
				if($hora[0]<10)
				{
					$horafinal = "0".$hora[0];
				}
				else
				{
					$horafinal = $hora[0];
				}
				if($hora[1]==null)
				{
					$horafinal .= ":00";
				}
				else
				{
					$min = $calc_tie-$hora[0];//esto es por si es 03:15:00 por ejemplo el turno
					$min_split = explode(".",(60*$min));
					if($min_split[0]<10){
						$horafinal .= ":0".($min_split[0]);
					}
					else{
						$horafinal .= ":".($min_split[0]);
					}
				}
				echo "<td align='left'>$tab[pro] - ".$horafinal."</td>";
			}
			else
			{
				echo "<td align='left'>$tab[pro]</td>";
			}
			
			echo "</tr>";
		}
		echo "</table>";		
		
		// Tabla Medios de pago
		echo "<br />";
		echo '<table class="tab_detalle_tur_hab">';
		echo '<tr><td align="left" colspan="3">Detalle del Pago</td></tr>';
		echo '<th>Medio</th><th>Importe</th><th align="left">Detalle</th>';
		
		$sql = "
		
		SELECT sys_comprobante_documento.*, sys_medio.medio, sys_puntos.codigo FROM sys_comprobante_documento
		
		INNER JOIN sys_medio ON sys_medio.id_medio = sys_comprobante_documento.id_medio
		LEFT JOIN sys_puntos ON sys_puntos.idcomprobante = sys_comprobante_documento.idcomprobante
		
		WHERE
		
			sys_comprobante_documento.idcomprobante = '".$fila['idcomprobante']."';
				
		";
		$result_docum = mysql_query($sql);
		while($fila_docum = mysql_fetch_array($result_docum)){
			
			$detalle = "";
			switch($fila_docum['id_medio']){
				case 1:
					$totalEfectivo += $fila_docum['importe'];
				break;
				case 2:
					$detalle = "Codigo Pago: ".$fila_docum['codigo_tarjeta'];
					$totalTarjeta += $fila_docum['importe'];
				break;
				case 3:
					$detalle = "Codigo Pago: ".$fila_docum['codigo_tarjeta'];
					$totalTarjeta_cre += $fila_docum['importe'];
				break;
				case 4:
					$detalle = "Premium: ".$fila_docum['codigo'];
					$totalPuntos += $fila_docum['importe'];
				break;
			}
			
			echo "
			<tr>
				<td>".$fila_docum['medio']."</td>
				<td>".$fila_docum['importe']."</td>
				<td align='left'>".$detalle."</td>
			</tr>
			";

		}	
		echo '</table>';
		
		
		// Tabla puntos cargados
		
		$sql = "
		
		SELECT sys_puntos.* FROM sys_comprobante
		
		INNER JOIN sys_puntos ON sys_puntos.idcomprobante = sys_comprobante.idcomprobante
		
		WHERE
		
			sys_comprobante.idcomprobante = '".$fila['idcomprobante']."'
				
		";
		$resultpuntoscar = mysql_query($sql);
		$puntos_cargados = 0;
		while($fila_puntoscar = mysql_fetch_array($resultpuntoscar)){
			
			$puntos_cargados = $fila_puntoscar['puntos'];
			$tarjeta_pre = $fila_puntoscar['codigo'];
			
		}
		if($puntos_cargados > 0){
			
			echo "<br />";
			echo '<table class="tab_detalle_tur_hab" width="100%" style="text-align:center;">';
			echo '<tr><td align="left" colspan="3">Detalle Puntos Cargados</td></tr>';
			echo '<tr>';
				echo '<th>Puntos</th><th align="left">Detalle</th>';
			echo '</tr>';
			echo '<tr>';	
				echo '<td>'.$puntos_cargados.'</td><td align="left">Premium: '.$tarjeta_pre.'</td>';
			echo '</tr>';
			echo '</table>';
			
			$totalPuntosCargados += $puntos_cargados;
		}

		// tabla aumento
		if($aumento != 0){

			echo "<br />";
			echo '<table class="tab_detalle_tur_hab" width="100%" style="text-align:center;">';
				echo '<tr><td align="left" colspan="3">Detalle del Aumento</td></tr>';
				echo '<tr><td>Importe</td><td align="left">'.$aumento.' / Usuario: '.$usuario_aumento.'</td></tr>';
			echo '</table>';
			
		}
		
		// tabla descuento
		if($descuento != 0){

			echo "<br />";
			echo '<table class="tab_detalle_tur_hab" width="100%" style="text-align:center;">';
				echo '<tr><td align="left" colspan="3">Detalle del Descuento</td></tr>';
				echo '<tr><td>Importe</td><td align="left">'.$descuento.' / Usuario: '.$usuario.'</td></tr>';
			echo '</table>';
			
		}
		
		
		echo "</div></td></tr>";
	}
		
		$totalFinal = $totalHab + $totalBar - $totalDescuento;
		
		echo "<tr class='trtit_tot'>";
			echo "<td colspan='6' style='text-align:right'>Cantidad Habitac&iacute;ones: </td>";
			echo "<td colspan='2'>".$cantidad_hab."</td>";
		echo "</tr>";
		echo "<tr class='trtit_tot'>";
			echo "<td colspan='6' style='text-align:right'>Total Habitac&iacute;ones: </td>";
			echo "<td colspan='2'>$ ".$totalHab."</td>";
		echo "</tr>";
		echo "<tr class='trtit_tot'>";
			echo "<td colspan='6' style='text-align:right'>Total Bar: </td>";
			echo "<td colspan='2'>$ ".$totalBar."</td>";
		echo "</tr>";
		echo "<tr class='trtit_tot'>";
			echo "<td colspan='6' style='text-align:right'>Total Descuento: </td>";
			echo "<td colspan='2'>$ ".$totalDescuento."</td>";
		echo "</tr>";
		echo "<tr class='trtit_tot'>";
			echo "<td colspan='6' style='text-align:right'>Total Final: </td>";
			echo "<td colspan='2'>$ ".$totalFinal."</td>";
		echo "</tr>";
		
		echo "<tr class='trtit_tot'>";
			echo "<td colspan='6' style='text-align:right'>Total Efectivo: </td>";
			echo "<td colspan='2'>$ ".$totalEfectivo."</td>";
		echo "</tr>";
		echo "<tr class='trtit_tot'>";
			echo "<td colspan='6' style='text-align:right'>Total Tarjeta Debito: </td>";
			echo "<td colspan='2'>$ ".$totalTarjeta."</td>";
		echo "</tr>";
		echo "<tr class='trtit_tot'>";
			echo "<td colspan='6' style='text-align:right'>Total Tarjeta Credito: </td>";
			echo "<td colspan='2'>$ ".$totalTarjeta_cre."</td>";
		echo "</tr>";


		echo "<tr class='trtit_tot'>";
			echo "<td colspan='6' style='text-align:right'>Total Puntos Cargados: </td>";
			echo "<td colspan='2'>$ ".$totalPuntosCargados."</td>";
		echo "</tr>";
		echo "<tr class='trtit_tot'>";
			echo "<td colspan='6' style='text-align:right'>Total Puntos Canjeados: </td>";
			echo "<td colspan='2'>$ ".$totalPuntos."</td>";
		echo "</tr>";
		
	echo "</table>";
	?>
	</form>
	<br>
	<form action="ficheroExcel.php" method="post" target="_blank" id="FormularioExportacion">
	<input value="Exportar Excel" type="submit" class="botonExcel" />
	<input type="hidden" id="datos_a_enviar" name="datos_a_enviar" />
	</form>
	<?
	
}else{
	
	?>
	<br>
	<div style="color:#F00;">No hay datos para los campos solicitados.</div>
	<?
	
}
}
?>
</body>
</html>