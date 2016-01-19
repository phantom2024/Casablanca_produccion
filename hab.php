<?php
require_once("boot.php");

$hab = $_REQUEST['hab'];
$est = $_REQUEST['est'];

// obtenemos tipo y estado de la habitacion
$sql = "

SELECT

	tip_hab.tip,
	hab.est,
	hab.utl,
	TIMESTAMPDIFF(MINUTE, hab.utl, NOW()) AS tiempo,
	TIMESTAMPDIFF(MINUTE, hab.tur, NOW()) AS tiempo_espera

FROM hab

INNER JOIN tip_hab ON tip_hab.id_tip = hab.tip

WHERE

	hab.id_hab = '".$hab."'

";
$result = mysql_query($sql, $pconnect);
while($fila = mysql_fetch_assoc($result)){
	
	$fec_utl_c = date_create($fila['utl']);
	$fec_utl = date_format($fec_utl_c, 'H:i:s');
	
	$hab_tie = $fila['tiempo'];
	$hab_est = $fila['est'];
	$hab_tip = $fila['tip'];
	
	$tiempo_espera = $fila['tiempo_espera'];
	
}
unset($result);
mysql_free_result($pconnect);

if($est == 2){
	$hab_est = 2;
}

if($hab_tie >= 10){
	$alerta_tiempo = 'color:#F00;';
}else{
	$alerta_tiempo = 'color:#093;';
}

?>

<div>
	<div style="float:left;"><? echo $hab." - ".$hab_tip; ?></div>
    <div style="float:right;"><code style="font-size:16px; <? echo $alerta_tiempo; ?>"><? echo $fec_utl; ?></code></div>
    <div style="clear:both;"></div>
</div>

<?php
// error de comunic
if($hab_est == 4){
	?>
	<br />
	<div>Error! Llamar ha Digital Creative Tel: (261)428-8566</div>
	<?php
}


$sql = "
SELECT
	
	est_tur,
	fec_in,
	fec_out,
	TIMEDIFF(NOW(), fec_in) AS tiempo_tot,
	TIMEDIFF(fec_out, NOW()) AS tiempo_hab,
	TIMEDIFF(cambio_ama, fec_in) AS tiempo_dif,
	chequeo,
	llamado,
	mostrar_cliente
	
FROM tur 

WHERE

	est_tur = '".$est."' AND
	nro = '".$hab."'
	
";
$result = mysql_query($sql, $pconnect);

// si no tiene turno
if(mysql_affected_rows($pconnect) == 0){

	// verde
	if($hab_est == 1){
		?>
        <br />
		<div>Lista</div>
		<?php
	}
		
	// amarillo
	if($hab_est == 2){
		?>
        <br />
		<div>Limpieza</div>
		<?php
	}
	
	// roja sin turno
	if($hab_est == 3){
		?>
        <br />
		<div>Sin turno</div>
        <div><b>Espera:</b> <? echo $tiempo_espera; ?> min.</div>
		<?php
	}
	
}

$total = 0;
while($fila = mysql_fetch_assoc($result)){
	
	$chequeo = "";
	switch($fila['chequeo']){
		case 0:
			$chequeo = " - Revisando Habitacion";
		break;
		case 1:
			$chequeo = " - <b style='color:#0F0;'>Todo bien</b>";
		break;
		case 2:
			$chequeo = " - <b style='color:#F00;'>Todo Mal</b>";
		break;
		default:
			$chequeo = "";
		break;
	}
	
	// si es 2 y todabia tiene turno esta saliendo el veiculo
	if($est == 2){
		?>
		<br />
		<div style="color:#F00;">¡¡Hab. Pendiente!!</div>
		<?php
	}else{
		if($hab_est == 2){
			?>
			<br />
			<div>Saliendo<?php echo $chequeo; ?></div>
			<?php
		}
	}
	
	// si es 3 y ocupada	
	if($hab_est == 3){
		?>
		<br />
		<div>Ocupada</div>
		<?php
	}
	
	// armamos fechas
	$fec_in_c = date_create($fila['fec_in']);
	$fec_in = date_format($fec_in_c, 'd/m/Y H:i');

	$fec_out_c = date_create($fila['fec_out']);
	$fec_out = date_format($fec_out_c, 'd/m/Y H:i');
	
	if($fila['tiempo_hab'] < '00:00:00'){
		$alerta_tiempo_atras = 'color:#F00;';
	}else{
		$alerta_tiempo_atras = '';
	}
	
	// llamado al cliente faltando 15 para salir
	$llamar_cliente = "";
	$se_llamo = "";
	$exp_hora = explode(":",$fila['tiempo_hab']);
	
	if($exp_hora[0] == 0 && $exp_hora[1] < 15 || ($fila['tiempo_hab'] < '00:00:00')){
		
		$llamar_cliente = '<a href="#" class="open_llamar" id="llamado_'.$hab.'"><img src="img/phone.png" /></a>';		
		switch($fila['llamado']){
			case 1:
				$se_llamo = "Se llamo y atendio";
			break;
			case 2:
				$se_llamo = "<b style='color:#F00;'>Se llamo y No atendio</b>";
			break;
		}
		
	}
	
	?>
	<br />
	<div><b>Fecha Entrada:</b> <?php echo $fec_in; ?></div>
    <div><b>Fecha Salida:</b> <?php echo $fec_out; ?></div>
    <?
    if($est == 2){
		?>
    	<div><b>Tiempo: </b><?php echo $fila['tiempo_dif']; ?></div>
    	<?
    }else{
		?>
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
    	<?
    }
	if($est == 1){
		?>
        <div>
            <?
            if($_SESSION['usuario']['id_tipo'] == 2){
				?>
				<div style="float:left; margin-left: 5px; margin-top: 5px;">
					<?php echo $llamar_cliente; ?>
				</div>
				<?
            }
            ?>
            <div style="float:left; margin-left:10px;">
                <?php echo $se_llamo; ?>
            </div>
            <div style="clear:both;"></div>
        </div>
    	<?
	}
	if($_SESSION['usuario']['id_tipo'] == 2){
	?>
    <br />
    <table cellpadding="1" cellspacing="1" border="0" width="430">    
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
		
		tur.est_tur = '".$est."' AND
		pro.bar = 0 AND
		tur.nro = '".$hab."'
		
	";
	$result_t = mysql_query($sql, $pconnect);
	
	$total_turno = 0;
	$trasnoche = 0;
	while($fila_t = mysql_fetch_assoc($result_t)){

		$exp_tie = explode(":",$fila_t['tiempo']);
		$fila_t['tiempo'] = $exp_tie[0].":".$exp_tie[1];
		
		echo "<tr>";
			
			// solo desarrollo fabian vallejo
			//echo "<td>[".$fila_t['iddetalle']."] ".$fila_t['pro']." <small>(".$fila_t['cantidad'].")</small></td>";
			
			echo "<td>".$fila_t['pro']." <small>(".$fila_t['cantidad'].")</small></td>";
			echo "<td>".$fila_t['importe']*$fila_t['cantidad']."</td>";
			echo "<td>".$fila_t['tiempo']."</td>";
		echo "</tr>";
		
		$total_turno = $total_turno + $fila_t['importe']*$fila_t['cantidad'];
		
	}
	
	?>
    	<tr>
        	<td colspan="3"><b>Total Turno: <? echo $total_turno; ?> $</b></td>
		</tr>
	</table>
	<?php
	}

	$sql = "
	
	SELECT
		
		sys_comprobante_detalle.*,
		pro.*
	
	FROM tur 
	
	INNER JOIN sys_comprobante ON sys_comprobante.id_turno = tur.id_tur
	INNER JOIN sys_comprobante_detalle ON sys_comprobante_detalle.idcomprobante = sys_comprobante.idcomprobante
	
	INNER JOIN pro ON pro.id_pro = sys_comprobante_detalle.idproducto
	
	WHERE
	
		pro.bar = 1 AND
		est_tur = '".$est."' AND	
		sys_comprobante_detalle.mostrar = 1 AND
		nro = '".$hab."'
		
	";
	
	//echo $sql;
	$result_t2 = mysql_query($sql, $pconnect);
	if(mysql_affected_rows($pconnect) > 0){
		
	?>
    <br />
    <table cellpadding="1" cellspacing="1" border="0" width="430">    
        <tr>
    		<td><b>Producto</b></td>
            <td><b>Valor</b></td>
            <td><b>Cantidad</b></td>
            <td><b>Sub Total</b></td>
            <td>&nbsp;</td>
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
			echo '<td><a href="#'.$fila_t2['iddetalle'].'" class="eli_itm_bar_det">X</a></td>';
		echo "</tr>";
		
		$total_bar = $total_bar + $fila_t2['importe'] * $fila_t2['cantidad'];		
	}
	
	?>
    	<tr>
        	<td colspan="4"><b>Total Bar: <? echo $total_bar; ?> $</b></td>
		</tr>
    </table>

    <?php
	}else{
		echo "<br /><b style='color:#F00'>Sin Bar</b><br />";
	}
	

	if($_SESSION['usuario']['id_tipo'] == 2){

	$sql = "
	SELECT
		
		sys_comprobante_detalle.importe
		
	FROM tur 
	
	INNER JOIN sys_comprobante ON sys_comprobante.id_turno = tur.id_tur
	INNER JOIN sys_comprobante_detalle ON sys_comprobante_detalle.idcomprobante = sys_comprobante.idcomprobante
	INNER JOIN pro ON pro.id_pro = sys_comprobante_detalle.idproducto
	
	WHERE

		tur.est_tur = '".$est."' AND
		pro.bar = 2 AND
		tur.nro = '".$hab."'
		
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
	
	
	// armamos recargo
	$sql = "
	SELECT
		
		sys_comprobante_detalle.importe
		
	FROM tur 
	
	INNER JOIN sys_comprobante ON sys_comprobante.id_turno = tur.id_tur
	INNER JOIN sys_comprobante_detalle ON sys_comprobante_detalle.idcomprobante = sys_comprobante.idcomprobante
	INNER JOIN pro ON pro.id_pro = sys_comprobante_detalle.idproducto
	
	WHERE

		tur.est_tur = '".$est."' AND
		pro.bar = 4 AND
		tur.nro = '".$hab."'
		
	";
	$result_rec = mysql_query($sql);
	$total_recargo = 0;
	while($fila_des = mysql_fetch_assoc($result_rec)){
		$total_recargo = $fila_des['importe'];
	}
	
	if($total_recargo != 0){
	?>
    	<br />
    	<b>Total Recargo: </b><?php echo $total_recargo; ?>
    <?php
	}
	
	
	// armamos total	
	$total = $total_turno + $total_bar + $total_descuento + $total_recargo;
	
	?>
	<br />
    <br />
	<div style="font-size:24px;"><b>Total:</b> <? echo $total; ?> $</div>
    <br />
    <?php
	}
	/*
	
	$sql = "
	SELECT puntos FROM sys_puntos
	
	INNER JOIN sys_comprobante ON sys_comprobante.idcomprobante = sys_puntos.idcomprobante
	INNER JOIN tur ON tur.id_tur = sys_comprobante.id_turno
	
	WHERE tur.est_tur = 1 AND tur.nro = '".$hab."'
	";
	$result_puntos = mysql_query($sql);
	while($fila_puntos = mysql_fetch_assoc($result_puntos)){
		$puntos = $fila_puntos['puntos'];
	}
	if(!is_null($puntos)){
		if($puntos > 0){
			echo "<b>Puntos Cargados: </b>".$puntos;
		}else{
			$puntos = $puntos * -1;
			echo "<b>Puntos Canjeados: </b>".$puntos;
		}
	}
	*/
	?>
    <br />
    <div>

        <?php
		if($hab_est == 3){

            if($_SESSION['usuario']['id_tipo'] == 3){
			?>            
            <div style="float:left;">
                <button class="buttonb" id="buttonb_<?php echo $hab; ?>">Bar</button>
            </div>
			<?php
			}
            if($_SESSION['usuario']['id_tipo'] == 2){
			?>
            <div style="float:left;">
                <button class="button_nue_tur" id="buttont_<?php echo $hab; ?>">Extender Turno</button>
            </div>
			<!--
            <div style="float:left;">
                <button class="buttonp" id="buttonp_<?php //echo $hab; ?>">Puntos</button>
            </div>
            -->
        	<?php
			}

		}
		
		if($hab_est == 1 || $hab_est == 2){
			if($_SESSION['usuario']['id_tipo'] == 2){
				
				/*
				?>
				<div style="float:left;">
					<button class="buttonc" id="buttonc_<?php echo $hab; ?>">Cerrar</button>
                </div>
				<?
				*/
				if($fila['mostrar_cliente'] == 0){
				?>
                <div style="float:left;">
                    <button class="buttonmos" id="buttonmos_<?php echo $hab; ?>">Mostrar</button>
                </div>
				<?php
				}else{
				?>
                <div style="float:left;">
                    <button class="buttonnomos" id="buttonnomos_<?php echo $hab; ?>">No Mostrar</button>
                </div>
				<?php
				}
				?>
                <div style="float:left;">
                    <button class="buttond" id="buttond_<?php echo $hab; ?>">Descuento</button>
                </div>
                <div style="float:left;">
                    <button class="buttonr" id="buttonr_<?php echo $hab; ?>">Recargo</button>
                </div>
				<div style="float:left;">
					<button class="buttonm" id="buttonm_<?php echo $hab; ?>">Cerrar</button>
				</div>
				<?php
				
			}
		}
		?>
        
        <div style="clear:both;"></div>
        
    </div>
	
    <script>
		$(".buttonb").button();
		$(".button_nue_tur").button();
		$(".buttonc").button();
		$(".buttonp").button();
		$(".buttond").button();
		$(".buttonm").button();
		$(".buttonmos").button();
		$(".buttonnomos").button();
		$(".buttonr").button();
	</script>
    
    <?php
	
}

unset($result);
unset($result_t);
unset($result_t2);
mysql_free_result($pconnect);

mysql_close($pconnect);

?>