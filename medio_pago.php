<?php
require_once("boot.php");

$est = $_REQUEST['est'];

if($_POST['m']){
    
	$sql = "
	
	SELECT
		
		SUM(sys_comprobante_detalle.cantidad * sys_comprobante_detalle.importe) AS total
		
	FROM tur 
	
	INNER JOIN sys_comprobante ON sys_comprobante.id_turno = tur.id_tur
	INNER JOIN sys_comprobante_detalle ON sys_comprobante_detalle.idcomprobante = sys_comprobante.idcomprobante	
	INNER JOIN pro ON pro.id_pro = sys_comprobante_detalle.idproducto
	
	WHERE
	
		est_tur = '".$est."' AND
		(sys_comprobante_detalle.mostrar = 1 or pro.bar = 0) and
		nro = '".$_REQUEST['id_hab']."';

	";
	
	$result = mysql_query($sql);
	while($fila = mysql_fetch_assoc($result)){
		$imp_total_hab = $fila['total'];
	}
	
	?>
	<br />
    <input type="hidden" id="imp_total_hab_medio_pago" name="imp_total_hab_medio_pago" value="<?php echo $imp_total_hab; ?>" />
	<div class="imp_total_hab_medio_pago">Total: $ <span><?php echo $imp_total_hab; ?></span></div>
    <br />
	<div>
    	Medio de pago:
        <?php
		echo '<select id="sel_medio_pago" name="sel_medio_pago">';
		echo '<option value="0">Seleccione un medio de pago</option>';
		$sql = "SELECT * FROM sys_medio";
		$result = mysql_query($sql);
		while($fila = mysql_fetch_assoc($result)){
			echo '<option value="'.$fila['id_medio'].'">'.$fila['medio'].'</option>';
		}
		echo '</select>';		
		?>
    </div>
	<div id="id_med_sel">
    	<div id="cont_med_efe">
        	<br />
        	Importe:<input type="text" id="imp_med_efe" name="imp_med_efe" value="<?php echo $imp_total_hab; ?>" />
            <br />
            <input type="button" id="but_imp_med_efe" name="but_imp_med_efe" value="Cargar" />
        </div>
        <div id="cont_med_tar">
        	<br />
        	Importe: <input type="text" id="imp_med_tar" name="imp_med_tar" value="<?php echo $imp_total_hab; ?>" />
            <br />
            Codigo: <input type="text" id="imp_med_tar_cod" name="imp_med_tar_cod" />
            <br />
            <input type="button" id="but_imp_med_tar" name="but_imp_med_tar" value="Cargar" />
        </div>
        <div id="cont_med_tar_cre">
        	<br />
        	Importe: <input type="text" id="imp_med_tar_cre" name="imp_med_tar_cre" value="<?php echo $imp_total_hab; ?>" />
            <br />
            Codigo: <input type="text" id="imp_med_tar_cod_cre" name="imp_med_tar_cod_cre" />
            <br />
            <input type="button" id="but_imp_med_tar_cre" name="but_imp_med_tar_cre" value="Cargar" />
        </div>
        <div id="cont_med_pun">
        	
            <br />
			Premium: <input type="password" id="tar_premium" name="tar_premium" />
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
				sys_comprobante.idcomprobante,
				tur.fec_in
				
			FROM tur 
            
            INNER JOIN sys_comprobante ON sys_comprobante.id_turno = tur.id_tur
            INNER JOIN sys_comprobante_detalle ON sys_comprobante_detalle.idcomprobante = sys_comprobante.idcomprobante
            INNER JOIN pro on pro.id_pro = sys_comprobante_detalle.idproducto
            
            WHERE tur.est_tur = '".$est."' AND (sys_comprobante_detalle.mostrar = 1 or pro.bar = 0) AND tur.nro = '".$_POST['id_hab']."';
            ";
            $result = mysql_query($sql, $pconnect);
            while($fila = mysql_fetch_assoc($result)){
                $total_turno = $fila['total'];
                $idcomprobante = $fila['idcomprobante'];
				$fec_in = $fila_comp['fec_in'];
            }
            
			// calculamos el dia y la hora para la fecha de entrada
			$dia_act = traer_dia_producto($fec_in);
			$hor_act = traer_hora_producto($fec_in);
			
			// obtenemos el precio original
			$sql = "	
				SELECT pro.val FROM tur
					INNER JOIN hab ON hab.nro = tur.nro
					INNER JOIN pro ON pro.tip = hab.tip
				WHERE
					tur.est_tur = 1 AND
					tur.nro = '".$_POST['id_hab']."' AND
					pro.bar = 0 AND
					pro.promo_hab = 0 AND
					pro.dia = '".$dia_act."' AND
					pro.hor = '".$hor_act."'
				LIMIT 1
			";
			$result = mysql_query($sql);
			while($fila = mysql_fetch_assoc($result)){
				$total = $fila['val'];
			}
			
            $puntos = ($total_turno*16.33) / 100;
            
            $sql = "SELECT codigo, puntos FROM sys_puntos WHERE idcomprobante = '".$idcomprobante."'";
            $result = mysql_query($sql, $pconnect);
            while($fila = mysql_fetch_assoc($result)){
                $tar_premium = $fila['codigo'];
                $puntos_canjeados = $fila['puntos'];
            }
            $can_res = mysql_num_rows($result);
            if($can_res == 0){
            
			$puntos = round($puntos, 2);
			$total = round($total, 2);
			
            ?>
            <br />
            <div id="puntos_cargados">
                <div style="float:left;">            
                    <input type="button" id="but_car_puntos" name="but_car_puntos" value="Cargar Puntos" />
                </div>
                <div style="float:left; margin-top:8px; margin-left:8px;">
                    <b>Total: </b><? echo $total_turno; ?> - <b>Puntos: </b><? echo $puntos; ?>
                </div>
                <div style="clear:both;"></div>
            </div>
            <br />          
            <input type="hidden" id="imp_med_pun" name="imp_med_pun" value="<?php echo $total; ?>" />
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
            ?>
            
        </div>
    </div>
    <br />
	
    <form id="form_list_pago" name="form_list_pago" action="medio_pago.php" method="form_list_pago">
        <input type="hidden" id="medio_pago_est" name="medio_pago_est" value="<?php echo $est; ?>" />
        <input type="hidden" id="imp_total_aumento" name="imp_total_aumento" value="0" />	    
    	<div id="medio_a_grabar"></div>
        <br />
        <div>
            <div style="float:left; width:70px; margin-left:100px;">Saldo</div>
            <div style="float:left;"> $</div>
            <div style="float:left; width:100px;" id="totalpagado">0</div>
            <div style="float:left; width:200px;" id="cont_totalvuelto">
                <div style="float:left; width:70px;">Vuelto</div>
                <div style="float:left;"> $</div>
                <div style="float:left; width:100px;" id="totalvuelto">0</div>
            	<div style="clear:both;"></div>
            </div>
            <div style="clear:both;"></div>
        </div>
        <input type="hidden" id="imp_saldo" name="imp_saldo" value="0" />
        <input type="hidden" id="imp_vuelto" name="imp_vuelto" value="0" />
    </form>
    
	<?

	$sql = "
	SELECT sys_puntos.codigo, sys_puntos.puntos FROM tur 
	INNER JOIN sys_comprobante ON sys_comprobante.id_turno = tur.id_tur
	INNER JOIN sys_puntos ON sys_puntos.idcomprobante = sys_comprobante.idcomprobante
	WHERE tur.est_tur = '".$est."' AND tur.nro = '".$_POST['id_hab']."';
	";
	$result = mysql_query($sql, $pconnect);
	$puntos = 0;
	while($fila = mysql_fetch_assoc($result)){
		$puntos = $fila['puntos'];
		$codigo = $fila['codigo'];
	}
	if($puntos < 0){
		$puntos = $puntos * -1;
		?>
        <script>
        	ya_canjeo(<? echo $puntos; ?>, <? echo $codigo; ?>);
        </script>
        <?
	}

	exit;

}

if($_POST['cm']){

	$id_medio = $_POST['id_medio'];
	$medio = $_POST['medio'];
	$imp_medio = $_POST['imp_medio'];
	$cod_tar = $_POST['cod_tar'];
	$cod_pre = $_POST['cod_pre'];
	
	?>
    <input type="hidden" class="id_mediob" id="id_medio[]" name="id_medio[]" value="<?php echo $id_medio; ?>" />
    <input type="hidden" class="imp_medio" id="imp_medio[]" name="imp_medio[]" value="<?php echo $imp_medio; ?>" />
    <input type="hidden" id="cod_tar[]" name="cod_tar[]" value="<?php echo $cod_tar; ?>" />
	<div>
    	<div style="float:left; width:170px;"><?php echo $medio; ?></div>
        <div style="float:left;"> $</div>
        <div style="float:left; width:100px;"><?php echo $imp_medio; ?></div>
        <?
        if($cod_tar){
			?>
            <div style="float:left; width:250px;">Codigo: <?php echo $cod_tar; ?></div>
            <?
       	}
        if($cod_pre){
			?>
            <div style="float:left; width:250px;">Premium: <?php echo $cod_pre; ?></div>
            <?
       	}
        ?>
        <div style="clear:both;"></div>
    </div>
	<?

	exit;
}

if($_POST['gp']){
	
	$est = $_REQUEST['medio_pago_est'];
	
	$sql = "
	SELECT sys_comprobante.idcomprobante FROM tur 
	INNER JOIN sys_comprobante ON sys_comprobante.id_turno = tur.id_tur
	WHERE tur.est_tur = '".$est."' AND tur.nro = '".$_POST['id_hab']."';
	";
	$result = mysql_query($sql);
	while($fila = mysql_fetch_assoc($result)){
		$idcomprobante = $fila['idcomprobante'];
	}
	
	foreach($_POST['id_medio'] as $key => $value){
		
		$sql = "
		INSERT INTO sys_comprobante_documento (fecha, idcomprobante, id_medio, importe, codigo_tarjeta) VALUES
		(NOW(), '".$idcomprobante."', '".$_POST['id_medio'][$key]."', '".$_POST['imp_medio'][$key]."', '".$_POST['cod_tar'][$key]."')";
		mysql_query($sql);

	}
	if($_POST['imp_total_aumento'] != 0){

		$aumento_por_tar_cre = $_POST['imp_total_aumento'];
		
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
			id_usuario_carga
			
		) VALUES (
		".$idcomprobante.", '".ID_AUMENTO_TARJETA_CRE."', '1', '".$aumento_por_tar_cre."', '', '', '', '', '', '1', '".$_SESSION['usuario']['id_usuario']."')";
		mysql_query($sql);
		
	}
	
	echo "ok";
	
	exit;
	
}
