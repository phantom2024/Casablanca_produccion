<?php
require_once("boot.php");

if($_POST['p']){
    
	?>
	Premium: <input type="text" id="tar_premium" name="tar_premium" />
    <br />
    <br />
    <div>
    	<div style="float:left;">
            <input type="button" id="but_con_puntos" name="but_con_puntos" value="Consultar Puntos" />
        </div>
        <div style="float:left;" id="pun_acumulados"></div>
        <div style="clear:both;"></div>
    </div>
    <?
	
	$sql = "
	SELECT
		
		SUM(sys_comprobante_detalle.importe) AS total,
		sys_comprobante.idcomprobante
		
	FROM tur 
	
	INNER JOIN sys_comprobante ON sys_comprobante.id_turno = tur.id_tur
	INNER JOIN sys_comprobante_detalle ON sys_comprobante_detalle.idcomprobante = sys_comprobante.idcomprobante
	INNER JOIN pro on pro.id_pro = sys_comprobante_detalle.idproducto
	
	WHERE
		
		tur.est_tur = 1 AND
		pro.bar = 0 AND
		tur.nro = '".$_POST['id_hab']."'
	";
	$result = mysql_query($sql, $pconnect);
	while($fila = mysql_fetch_assoc($result)){
		$total = $fila['total'];
		$idcomprobante = $fila['idcomprobante'];
	}
	
	$puntos = ($total*16.33) / 100;
	
	$sql = "SELECT codigo, puntos FROM sys_puntos WHERE idcomprobante = '".$idcomprobante."'";
	$result = mysql_query($sql, $pconnect);
	while($fila = mysql_fetch_assoc($result)){
		$tar_premium = $fila['codigo'];
		$puntos_canjeados = $fila['puntos'];
	}
	$can_res = mysql_num_rows($result);
	if($can_res == 0){
	
	?>
    <br />
    <div id="puntos_cargados">
    	<div style="float:left;">            
            <input type="button" id="but_car_puntos" name="but_car_puntos" value="Cargar Puntos" />
        </div>
        <div style="float:left; margin-top:8px; margin-left:8px;">
        	<b>Total: </b><? echo $total; ?> - <b>Puntos: </b><? echo $puntos; ?>
        </div>
        <div style="clear:both;"></div>
    </div>
    
    <br />
    <div id="canjear_puntos">
    	<div style="float:left;">
            <input type="button" id="but_can_puntos" name="but_can_puntos" value="Canjear Puntos" />
        </div>
        <div style="float:left; margin-top:8px; margin-left:8px;">
        	<b>Puntos a Canjear: </b><? echo $total; ?>
        </div>
        <div style="clear:both;"></div>
    </div>
    
	<?
	}else{
		
		echo "<br><b>Premium: </b>".$tar_premium;
		if($puntos_canjeados < 0){
			echo "<br><b>Puntos Canjeados: </b>".$puntos_canjeados."<br>";
		}else{
			echo "<br><b>Puntos Cargados: </b>".$puntos."<br>";
		}
		
	}
	
	unset($result);
	mysql_free_result($pconnect);

	exit;
	
}

if($_POST['pa']){
	
	$sql = "SELECT codigo FROM sys_tarjetas WHERE activo = 1 AND codigo = '".$_POST['tar']."'";
	$result = mysql_query($sql, $pconnect);	
	$can_res = mysql_num_rows($result);
	if($can_res == 0){
		echo "La Tarjeta no es correcta!!";
		exit;
	}
	
	$sql = "SELECT SUM(puntos) AS total_puntos FROM sys_puntos WHERE codigo = '".$_POST['tar']."'";
	$result = mysql_query($sql, $pconnect);
	while($fila = mysql_fetch_assoc($result)){
		$total_puntos = $fila['total_puntos'];
	}
	
	if(is_null($total_puntos)){
		$total_puntos = 0;
	}
	
	// formateamos a dos decimales
	$total_puntos = round($total_puntos, 2);
	
	echo "<b>Puntos Totales:</b> ".$total_puntos;
	
}

if($_POST['gp']){
	
	$est = $_POST['est'];
	
	$sql = "SELECT codigo FROM sys_tarjetas WHERE activo = 1 AND codigo = '".$_POST['tar']."'";
	$result = mysql_query($sql, $pconnect);	
	$can_res = mysql_num_rows($result);
	if($can_res == 0){
		echo "La Tarjeta no es correcta!!";
		exit;
	}
	
	// graba puntos
	$sql = "
	select SUM(sys_comprobante_detalle.importe) AS total, sys_comprobante.idcomprobante from tur 
	
	INNER JOIN sys_comprobante ON sys_comprobante.id_turno = tur.id_tur
	INNER JOIN sys_comprobante_detalle ON sys_comprobante_detalle.idcomprobante = sys_comprobante.idcomprobante
	INNER JOIN pro on pro.id_pro = sys_comprobante_detalle.idproducto
	
	WHERE tur.est_tur = '".$est."' AND pro.bar = 0 AND tur.nro = '".$_POST['id_hab']."';
	";
	$result = mysql_query($sql, $pconnect);
	while($fila = mysql_fetch_assoc($result)){
		$total = $fila['total'];
		$idcomprobante = $fila['idcomprobante'];
	}
	
	$puntos = ($total*16.33) / 100;
	
	// redondeamos a dos decimales los puntos
	$puntos = round($puntos, 2);
	
	$sql = "INSERT INTO sys_puntos (fecha, codigo, puntos, idcomprobante)
	VALUES (NOW(), '".$_POST['tar']."', '".$puntos."', '".$idcomprobante."')";
	if(mysql_query($sql, $pconnect)){
		
		echo "<br><b>Premium: </b>".$_POST['tar'];
		echo "<br><b>Puntos Cargados: </b>".$puntos;
		?>
        <script>
			$("#canjear_puntos").hide();
		</script>
        <?
		
	}
	
	exit;
}

if($_POST['cp']){
	
	$est = $_POST['est'];
	
	$sql = "SELECT codigo FROM sys_tarjetas WHERE activo = 1 AND codigo = '".$_POST['tar']."'";
	$result = mysql_query($sql, $pconnect);	
	$can_res = mysql_num_rows($result);
	if($can_res == 0){
		echo "La Tarjeta no es correcta!!";
		exit;
	}
	
	// canjea puntos

	// cuantos puntos tengo
	$sql = "SELECT SUM(puntos) AS total_puntos FROM sys_puntos WHERE codigo = '".$_POST['tar']."'";
	$result = mysql_query($sql, $pconnect);
	while($fila = mysql_fetch_assoc($result)){
		$total_puntos = $fila['total_puntos'];
	}
	if(is_null($total_puntos)){
		$total_puntos = 0;
	}
	
	// cuantos necesito
	$sql = "
	select SUM(sys_comprobante_detalle.importe) AS total, sys_comprobante.idcomprobante from tur 
	INNER JOIN sys_comprobante ON sys_comprobante.id_turno = tur.id_tur
	INNER JOIN sys_comprobante_detalle ON sys_comprobante_detalle.idcomprobante = sys_comprobante.idcomprobante
	INNER JOIN pro on pro.id_pro = sys_comprobante_detalle.idproducto
	WHERE tur.est_tur = '".$est."' AND pro.bar = 0 AND tur.nro = '".$_POST['id_hab']."';
	";
	$result = mysql_query($sql, $pconnect);
	while($fila = mysql_fetch_assoc($result)){
		//$total = $fila['total'];
		$idcomprobante = $fila['idcomprobante'];
	}
	
	// cuantos puntos necesito para pagar el precio original
	$total = $_REQUEST['imp_med_pun'];
	
	if($total <= $total_puntos){
		
		$total = $total * -1;
		
		$sql = "INSERT INTO sys_puntos (fecha, codigo, puntos, idcomprobante) 
		VALUES (NOW(), '".$_POST['tar']."', '".$total."', '".$idcomprobante."')";
		if(mysql_query($sql, $pconnect)){
			echo "<br><b>Premium: </b>".$_POST['tar'];
			echo "<br><b>Puntos Canjeados: </b>".$total;
			?>
			<script>
				$("#puntos_cargados").hide();
			</script>
			<?
		}
		
	}else{
		echo "No tiene la cantidad de puntos necesarios!!";
	}
	
	exit;
}
