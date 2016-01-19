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
	
	$("#fec").datepicker({ dateFormat: "dd-mm-yy" });
	$("#fec_has").datepicker({ dateFormat: "dd-mm-yy" });
	
	$(".botonExcel").click(function(event) {
		$("#datos_a_enviar").val( $("<div>").append( $("#Exportar_a_Excel").eq(0).clone()).html());
		$("#FormularioExportacion").submit();
	});
	
	$(".ver_tur").click(function(event) {
		
		$(".tab_oc").hide();
		
		var hr = $(this).attr('href');
		hr = hr.replace("#", "");
		console.log(hr);
		
		$("#tab_"+hr).show();
		
	});
	
});
</script>
<br/>
<form action="abm.php" method="get">

<input type="hidden" id="fx" name="fx" value="dat">
<table>
<tr>
<th>Fecha</th>
<th>Fecha hasta</th>
<th>Estado</th>
<th>Producto</th>
</tr>
<tr>

<?
if($_GET['fec']){
	$fecha_input = $_GET['fec'];
	$fecha_hasta = $_GET['fec_has'];
}else{
	$fecha_input = date("d-m-Y");
	$fecha_hasta = date("d-m-Y");
}		
?>

<td><input type="text" id="fec" name="fec" value="<? echo $fecha_input; ?>"></td>
<td><input type="text" id="fec_has" name="fec_has" value="<? echo $fecha_hasta; ?>"></td>

<td>
	<select id ="sel" name="sel">
	  <option value="sel_todos">Todos</option>
	  <option value="sel_cerrado">Cerrado</option>
	  <option value="sel_abierto">Abierto</option>
	</select>
</td>
<td>
<select id ="prod" name="prod">
<option value="prod_todos">TODOS</option>
<?
	$consulta = "SELECT pro FROM pro WHERE bar = 0 AND venta = 1";
	$resultado = mysql_query($consulta);
	echo "<br/>";
	while ($fila = mysql_fetch_array($resultado)) 
	{
		echo "<option value='".$fila['pro']."'>".$fila['pro']."</option>";
	}
?>	  
</select>
</td>
<td>
<input type="submit" id="listar_but" value="Listar">
</td>	
</table>


<?

$estado="";
if($_GET['sel'])
{
	if($_GET['sel']=='sel_todos')
	{
		$estado="";
	}
	else if($_GET['sel']=='sel_cerrado')
	{
		$estado=" AND est_tur = 0";
	}
	else if($_GET['sel']=='sel_abierto')
	{
		$estado= " AND est_tur = 1";
	}
}
$prod="";
if($_GET['prod'])
{
	if($_GET['prod']=='prod_todos')
	{
		$prod="";
	}
	else
	{
		$prod= " AND pro.pro = '".$_GET['prod']."'";
	}
}
	
		$date = new DateTime($fecha_input);
		$fec = $date->format('Y-m-d');
		
		$date2 = new DateTime($fecha_hasta);
		$fec_h = $date2->format('Y-m-d');
		

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
			sys_comprobante.idcomprobante
			
		FROM tur 
		
		INNER JOIN sys_comprobante ON sys_comprobante.id_turno = tur.id_tur 
		INNER JOIN sys_comprobante_detalle ON sys_comprobante_detalle.idcomprobante = sys_comprobante.idcomprobante 
		INNER JOIN pro ON pro.id_pro = sys_comprobante_detalle.idproducto
		INNER JOIN hab ON hab.nro = tur.nro
		INNER JOIN tip_hab ON tip_hab.id_tip = hab.tip
		
		WHERE date(tur.fec) >= '".$fec."' AND
			  date(tur.fec) <= '".$fec_h."'
		
		".$estado.$prod."
		
		GROUP BY id_tur
		";
		//echo "<br />".$g_sql;
		$resultado = $db->query($g_sql);
	
if(mysql_affected_rows() > 0)
{
	echo "<br/>";
	
	echo '<table border="1" id="Exportar_a_Excel" width="100%" cellpadding="2" cellspacing="2"><tr>';	
	echo "<th>Id</th>";
	echo "<th>Habitaci&oacute;n</th>";
	echo "<th>Estado</th>";
	echo "<th>Fecha</th>";
	echo "<th>Fecha in</th>";
	echo "<th>Fecha out</th>";
	echo "<th>Fecha salida</th>";
	echo "<th>Valor</th>";
	echo "<th>Ver</th>";
	
	$totalValor;
	$totalTiempo;
	
	while ($fila = mysql_fetch_assoc($resultado)) 
	{
		
		$tiempo_hab = $fila['tiempo_hab'];	
	
		echo "<tr class='alternar'>";
		echo "<td>".$fila['id_tur']."</td>";
		
		// fabian vallejo agrege numero y tipo de habitacion
		echo "<td>$fila[nro] - $fila[tip]</td>";
		
		if($fila[est_tur]=='0')
		{
			echo "<td>Cerrado</td>";
		}
		else
		{
			echo "<td>Abierto</td>";
		}
		
		$date = new DateTime($fila['fec']);
		echo "<td>".$date->format('d-m-Y H:i')."</td>";
		
		$date = new DateTime($fila['fec_in']);
		echo "<td>".$date->format('d-m-Y H:i')."</td>";
		
		$date = new DateTime($fila['fec_out']);
		echo "<td>".$date->format('d-m-Y H:i')."</td>";
		
		
		if($fila['cambio_ama'] == NULL || $fila['cambio_ama'] == '0000-00-00 00:00:00'){
			echo "<td>Sin Salida</td>";
		}else{
			$date = new DateTime($fila['cambio_ama']);
			echo "<td>".$date->format('d-m-Y H:i')."</td>";
		}
		
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
		
			pro.bar <> 2 AND
			sys_comprobante.id_turno = $fila[id_tur]
		
		";
		
		//echo "<br /><br />".$query;
		
		$con = mysql_query($query);
		while($tab = mysql_fetch_array($con)){
			if($tab['mostrar'] == 1 || $tab['bar'] == 0){
				$valor += $tab['importe']*$tab['cantidad']; 	
			}
		}
		
		// calculo descuento
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
			sys_comprobante.id_turno = '".$fila['id_tur']."'
		
		";
		$descuento = 0;
		$result_desc = mysql_query($sql);
		while($fila_desc = mysql_fetch_array($result_desc)){
			$descuento = $fila_desc['importe'];
			$usuario = $fila_desc['usuario'];
		}
		
		//$valor = $valor - ($descuento * -1);
		
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
		echo '<tr><td colspan=9><div class="tab_oc" id="tab_'.$fila['id_tur'].'" style="display:none;">';
		
		
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
			
			if($tab['bar']==0){
				$calc_tie = $tab['tie'] * $tab['cantidad'];
				$hora = explode(".", $calc_tie);
				if($hora[0]<10){
					$horafinal = "0".$hora[0];
				}else{
					$horafinal = $hora[0];
				}
				if($hora[1]==null){
					$horafinal .= ":00";
				}else{
					$min = $calc_tie-$hora[0];//esto es por si es 03:15:00 por ejemplo el turno
					$min_split = explode(".",(60*$min));
					if($min_split[0]<10){
						$horafinal .= ":0".($min_split[0]);
					}else{
						$horafinal .= ":".($min_split[0]);
					}
				}
				echo "<td align='left'>$tab[pro] - ".$horafinal."</td>";				
			}else{
				echo "<td align='left'>$tab[pro]</td>";
			}
						
			echo "</tr>";
		}
		echo "</table>";
		
		
		// tabla descuento
		if($descuento != 0){

			echo "<br />";
			echo '<table class="tab_detalle_tur_hab" width="100%" style="text-align:center;">';
			echo '<tr><td align="left" colspan="3">Detalle del Descuento</td></tr>';
			echo '<td>Importe</td><td align="left">'.$descuento.' - Usuario: '.$usuario.'</td>';
			echo '</table>';
			
		}
		
		// Tabla Medios de pago
		echo "<br />";
		echo '<table class="tab_detalle_tur_hab" width="100%" style="text-align:center;">';
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
		
		
		
		echo "</div></td></tr>";
	}
		

		$totalFinal = $totalHab + $totalBar - $totalDescuento;
		
		echo "<tr class='trtit_tot'>";
			echo "<td colspan='7' style='text-align:right'>Cantidad Habitac&iacute;ones: </td>";
			echo "<td colspan='2'>".$cantidad_hab."</td>";
		echo "</tr>";
		echo "<tr class='trtit_tot'>";
			echo "<td colspan='7' style='text-align:right'>Total Habitac&iacute;ones: </td>";
			echo "<td colspan='2'>$ ".$totalHab."</td>";
		echo "</tr>";
		echo "<tr class='trtit_tot'>";
			echo "<td colspan='7' style='text-align:right'>Total Bar: </td>";
			echo "<td colspan='2'>$ ".$totalBar."</td>";
		echo "</tr>";
		echo "<tr class='trtit_tot'>";
			echo "<td colspan='7' style='text-align:right'>Total Descuento: </td>";
			echo "<td colspan='2'>$ ".$totalDescuento."</td>";
		echo "</tr>";
		echo "<tr class='trtit_tot'>";
			echo "<td colspan='7' style='text-align:right'>Total Final: </td>";
			echo "<td colspan='2'>$ ".$totalFinal."</td>";
		echo "</tr>";
		
		echo "<tr class='trtit_tot'>";
			echo "<td colspan='7' style='text-align:right'>Total Efectivo: </td>";
			echo "<td colspan='2'>$ ".$totalEfectivo."</td>";
		echo "</tr>";
		echo "<tr class='trtit_tot'>";
			echo "<td colspan='7' style='text-align:right'>Total Tarjeta: </td>";
			echo "<td colspan='2'>$ ".$totalTarjeta."</td>";
		echo "</tr>";
		echo "<tr class='trtit_tot'>";
			echo "<td colspan='7' style='text-align:right'>Total Puntos: </td>";
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