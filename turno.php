<?
require_once("boot.php");

$nro_hab = $_POST['id_hab'];

if($_POST['agregar']){

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
	
	// calculamos el dia y la hora para la fecha de entrada
	$dia_act = traer_dia_producto($fec_in);
	$hor_act = traer_hora_producto($fec_in);

	// hora de entrada
	$fec_ent = traer_hora($fec_in);
	$exp_fec_ent = explode(":",$fec_ent);
	
	// obtenemos el precio promo
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
		
		// limpiamos el tiempo de cada habitacion
		$exp_hor = explode(":", $fila['tie']);
		$fila['tie'] = $exp_hor[0].":".$exp_hor[1];
		
		// limpiamos la salida de cada habitacion
		$exp_hor = explode(":", $fila['salida']);
		$fila['salida'] = $exp_hor[0].":".$exp_hor[1];
		
		$pro_pro = $fila;
	}
	unset($fila);
	
	// calculamos hora de salida
	$exp_hor = explode(":", $pro_pro['tie']);
	$tiempo_min = $exp_hor[1] + ($exp_hor[0] * 60);
	$hora_salida_pro = date("H:i", mktime($exp_fec_ent[0], $exp_fec_ent[1], 0, 0, 0, 0) + ($tiempo_min * 60));
	
	// obtenemos el precio original
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
		
		// limpiamos el tiempo de cada habitacion
		$exp_hor = explode(":", $fila['tie']);
		$fila['tie'] = $exp_hor[0].":".$exp_hor[1];
		
		// limpiamos la salida de cada habitacion
		$exp_hor = explode(":", $fila['salida']);
		$fila['salida'] = $exp_hor[0].":".$exp_hor[1];
		
		$pro_ori = $fila;
		
	}
	unset($fila);
	
	// calculamos hora de salida
	$exp_hor = explode(":", $pro_ori['tie']);
	$tiempo_min = $exp_hor[1] + ($exp_hor[0] * 60);
	$hora_salida_ori = date("H:i", mktime($exp_fec_ent[0], $exp_fec_ent[1], 0, 0, 0, 0) + ($tiempo_min * 60));


	$tiene_trasnoche = false;
	//traemos la hora de la fecha de entrada , si tiene trasnoche y si cumple con el horario mostramos
	if($fec_ent > "00:00" and $fec_ent < "06:00"){
		
		// si tiene trasnoche para ese dia
		$sql = "	
			SELECT pro.* FROM tur
				INNER JOIN hab ON hab.nro = tur.nro
				INNER JOIN pro ON pro.tip = hab.tip
			WHERE
				tur.nro = '".$nro_hab."' AND
				pro.bar = 0 AND
				pro.promo_hab = 0 AND
				pro.dia = '".$dia_act."' AND
				pro.hor = '3'
			LIMIT 1
		";
		$result = mysql_query($sql);
		while($fila = mysql_fetch_assoc($result)){
			
			// limpiamos el tiempo de cada habitacion
			$exp_hor = explode(":", $fila['tie']);
			$fila['tie'] = $exp_hor[0].":".$exp_hor[1];
	
			// limpiamos la salida de cada habitacion
			$exp_hor = explode(":", $fila['salida']);
			$fila['salida'] = $exp_hor[0].":".$exp_hor[1];
			
			$pro_tra = $fila;
			
			$tiene_trasnoche = true;
			
		}
		unset($fila);

		// calculamos hora de salida
		$exp_hor = explode(":", $pro_tra['tie']);
		$tiempo_min = $exp_hor[1] + ($exp_hor[0] * 60);
		$hora_salida_tra = date("H:i", mktime($exp_fec_ent[0], $exp_fec_ent[1], 0, 0, 0, 0) + ($tiempo_min * 60));
		
	}
		
	
	// si el tipo de habitacion tiene la nueva promo
	$tipo_hab_promo = array(5,6,7,8,9,10);
	
	$sql = "SELECT tip FROM hab WHERE nro = '".$nro_hab."'";
	$result = mysql_query($sql);
	while($fila_promo = mysql_fetch_assoc($result)){
		$tipo_hab = $fila_promo['tip'];
	}
	$pro_nueva = false;
	if(in_array($tipo_hab, $tipo_hab_promo)){
		
		$sql = "
		SELECT * FROM pro
		INNER JOIN hab ON hab.nro = '".$nro_hab."'
		WHERE
			pro.id_pro = '".ID_PROMO_NUEVA."'
		";
		$result = mysql_query($sql);
		while($fila = mysql_fetch_assoc($result)){
			
			// limpiamos el tiempo de cada habitacion
			$exp_hor = explode(":", $fila['tie']);
			$fila['tie'] = $exp_hor[0].":".$exp_hor[1];
			
			$pro_nueva = $fila;
			
		}
		unset($fila);
		// calculamos hora de salida
		$exp_hor = explode(":", $pro_nueva['tie']);
		$tiempo_min = $exp_hor[1] + ($exp_hor[0] * 60);
		$hora_salida_pro_nueva = date("H:i", mktime($exp_fec_ent[0], $exp_fec_ent[1], 0, 0, 0, 0) + ($tiempo_min * 60));
		
	}
	
	
	// calculamos todo el tiempo que la habitacion tiene cargado
	$sql = "
	SELECT tiempo FROM sys_comprobante_detalle
	INNER JOIN pro ON pro.id_pro = sys_comprobante_detalle.idproducto
	WHERE
		pro.bar = 0 AND
		idcomprobante = '".$id_com."';	
	";
	$result = mysql_query($sql);
	$tiempo_min_car = 0;
	while($fila = mysql_fetch_assoc($result)){
		
		// limpiamos el tiempo de cada habitacion y lo sumamos		
		$exp_hor = explode(":", $fila['tiempo']);
		$tiempo_min_car = $tiempo_min_car + ($exp_hor[1] + ($exp_hor[0] * 60));
		
	}
	
	
	// calculo para medio turno o turno completo o dos turnos
	
	// calculamos la cantidad de tiempo para mitad
	$exp_hor = explode(":", $pro_ori['tie']);
	$tiempo_min = $exp_hor[1] + ($exp_hor[0] * 60);
	$tie_mitad = date("H:i", mktime($exp_hor[0], $exp_hor[1], $exp_hor[2], 0, 0, 0) - (($tiempo_min / 2) * 60));
	
	// calculamos hora de salida mitad
	$exp_hor = explode(":", $tie_mitad);
	$tiempo_min = $tiempo_min_car + ($exp_hor[1] + ($exp_hor[0] * 60));
	$hora_salida_ori_mitad = date("H:i", mktime($exp_fec_ent[0], $exp_fec_ent[1], 0, 0, 0, 0) + ($tiempo_min * 60));
	
	// calculamos la cantidad de tiempo para doble
	$exp_hor = explode(":", $pro_ori['tie']);
	$tiempo_min = $exp_hor[1] + ($exp_hor[0] * 60);
	$tie_doble = date("H:i", mktime($exp_hor[0], $exp_hor[1], $exp_hor[2], 0, 0, 0) + (($tiempo_min) * 60));
	
	// calculamos hora de salida doble
	$exp_hor = explode(":", $tie_doble);
	$tiempo_min = $tiempo_min_car + ($exp_hor[1] + ($exp_hor[0] * 60));
	$hora_salida_ori_doble = date("H:i", mktime($exp_fec_ent[0], $exp_fec_ent[1], 0, 0, 0, 0) + ($tiempo_min * 60));
	
	// calculamos hora de salida para un turno completo
	$exp_hor = explode(":", $pro_ori['tie']);
	$tiempo_min = $tiempo_min_car + ($exp_hor[1] + ($exp_hor[0] * 60));
	$hora_salida_ori_turno = date("H:i", mktime($exp_fec_ent[0], $exp_fec_ent[1], 0, 0, 0, 0) + ($tiempo_min * 60));

	
	?>
    <div>
        <div>
        	<div class="tur_col1">
            	<input type='radio' class="rad_tur"
                	alt="<? echo $pro_pro['id_pro']."_".$hora_salida_pro."_".$pro_pro['tie']."_".$pro_pro['val']."_".$pro_pro['salida']; ?>"
				name='group' value='1'>Precio Promocion
			</div>
            <div class="cont_tur">
                <div class="tur_col2">$ <? echo $pro_pro['val']; ?></div>
                <div class="tur_col3"><? echo $pro_pro['tie']; ?></div>
                <div class="tur_col3"><small>S: <? echo $pro_pro['salida']; ?></small></div>
                <div style="clear:both"></div>
			</div>
            <div style="clear:both"></div>
        </div>
        <div>
        	<div class="tur_col1">
            	<input type='radio' class="rad_tur"
                	alt="<? echo $pro_ori['id_pro']."_".$hora_salida_ori."_".$pro_ori['tie']."_".$pro_ori['val']."_".$pro_ori['salida']; ?>"
				name='group' value='2'>Precio Original
			</div>
            <div class="cont_tur">
                <div class="tur_col2">$ <? echo $pro_ori['val']; ?></div>
                <div class="tur_col3"><? echo $pro_ori['tie']; ?></div>
                <div class="tur_col3"><small>S: <? echo $pro_ori['salida']; ?></small></div>
                <div style="clear:both"></div>
            </div>
            <div style="clear:both"></div>
        </div>
        <div>
        	<div class="tur_col1">
            	<input type='radio' class="rad_tur"
               		alt="<? echo $pro_ori['id_pro']."_".$hora_salida_ori_mitad."_".$tie_mitad."_".$pro_ori['val']."_".$pro_ori['salida']; ?>"
				name='group' value='3'>1/2 Turno
			</div>
            <div class="cont_tur">
                <div class="tur_col2">$ <? echo $pro_ori['val'] / 2; ?></div>
                <div class="tur_col3"><? echo $tie_mitad; ?></div>
                <div style="clear:both"></div>
            </div>
            <div style="clear:both"></div>
        </div>
        <div>
        	<div class="tur_col1">
            	<input type='radio' class="rad_tur"
               		alt="<? echo $pro_ori['id_pro']."_".$hora_salida_ori_turno."_".$pro_ori['tie']."_".$pro_ori['val']."_".$pro_ori['salida']; ?>"
				name='group' value='4'>1 Turno
			</div>
            <div class="cont_tur">
                <div class="tur_col2">$ <? echo $pro_ori['val']; ?></div>
                <div class="tur_col3"><? echo $pro_ori['tie']; ?></div>
                <div style="clear:both"></div>
            </div>
            <div style="clear:both"></div>
        </div>
        <div>
        	<div class="tur_col1">
            	<input type='radio' class="rad_tur"
                	alt="<? echo $pro_ori['id_pro']."_".$hora_salida_ori_doble."_".$tie_doble."_".$pro_ori['val']."_".$pro_ori['salida']; ?>"
				name='group' value='5'>2 Turno
			</div>
            <div class="cont_tur">
                <div class="tur_col2">$ <? echo $pro_ori['val'] * 2; ?></div>
                <div class="tur_col3"><? echo $tie_doble; ?></div>
                <div style="clear:both"></div>
            </div>
            <div style="clear:both"></div>
        </div>
        <?
		if($tiene_trasnoche == true){
		?>
            <div>
                <div class="tur_col1">
                    <input type='radio' class="rad_tur"
                    	alt="<? echo $pro_tra['id_pro']."_".$hora_salida_tra."_".$pro_tra['tie']."_".$pro_tra['val']."_".$pro_tra['salida']; ?>"
					name='group' value='6'>Trasnoche
                </div>
                <div class="cont_tur">
                    <div class="tur_col2">$ <? echo $pro_tra['val']; ?></div>
                    <div class="tur_col3"><? echo $pro_tra['tie']; ?></div>
                    <div class="tur_col3"><small>S: <? echo $pro_tra['salida']; ?></small></div>
                    <div style="clear:both"></div>
                </div>
                <div style="clear:both"></div>
            </div>
		<?			
		}
		if($pro_nueva != false){
		?>
        	<br />
            <div>
                <div class="tur_col1">
                    <input type='radio' class="rad_tur"
                    	alt="<? echo $pro_nueva['id_pro']."_".$hora_salida_pro_nueva."_".$pro_nueva['tie']."_".$pro_nueva['val']."_".$pro_nueva['salida']; ?>"
					name='group' value='7'>Promo Nueva
                </div>
                <div class="cont_tur">
                    <div class="tur_col2">$ <? echo $pro_nueva['val']; ?></div>
                    <div class="tur_col3"><? echo $pro_nueva['tie']; ?></div>
                    <div style="clear:both"></div>
                </div>
                <div style="clear:both"></div>
            </div>
		<?			
		}
		?>
        
    </div>	
    
    <br />
    
    <div class="cont_tur_fin">
    	<div>
        	<div class="turf_col1">Entrada</div>
            <div class="turf_col2">Salida</div>
            <div class="turf_col3">Tiempo</div>
            <div class="turf_col4">Total</div>
            <div style="clear:both"></div>
        </div>
        <div>
        	<form id="form_tur_det" name="form_tur_det">
                <input type="hidden" id="pro_f" name="pro_f" value="0" />
                <input type="hidden" id="tie_fh" name="tie_fh" value="0" />
                <input type="hidden" id="pre_fh" name="pre_fh" value="0" />
                <input type="hidden" id="tie_fs" name="tie_fs" value="0" />
                <div class="turf_col1"><input type="text" disabled="disabled" class="fec_ent" value="<? echo $fec_ent ?>" /></div>
                <div class="turf_col2"><input type="text" disabled="disabled" id="hor_f" name="hora_f" /></div>
                <div class="turf_col3"><input type="text" readonly="readonly" id="tie_f" name="tie_f" /></div>
                <div class="turf_col4"><input type="text" readonly="readonly" id="pre_f" name="pre_f" /></div>
                <div style="clear:both"></div>
            </form>
        </div>
    </div>
    <?
	
	unset($pro_pro);
	unset($pro_ori);
	
	exit;
	
}

if($_POST['guardar']){

	// id de comprobante del turno de la habitacion
	$sql = "
	SELECT tur.fec_in, tur.id_tur, sys_comprobante.idcomprobante FROM tur
	INNER JOIN sys_comprobante ON sys_comprobante.id_turno = tur.id_tur									
	WHERE tur.est_tur = 1 AND tur.nro = '".$nro_hab."';
	";
	$result = mysql_query($sql);
	while($fila_comp = mysql_fetch_assoc($result)){
		$fec_in = $fila_comp['fec_in'];
		$id_tur = $fila_comp['id_tur'];
		$id_com = $fila_comp['idcomprobante'];
	}
	
	$opc = (int)$_POST['val_radio'];
	if($opc == 1 || $opc == 2 || $opc == 6 || $opc == 7){
		
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

	}
	// colocamos la cantidad
	$cantidad = 1;
	if($opc == 3){
		$cantidad = 0.5;
	}
	if($opc == 5){
		$cantidad = 2;
	}
	
	// obtenemos el porteto logiado
	$id_usu_turno = $_SESSION['usuario']['id_usuario'];
	
	$correjido = 2;
	if($_POST['pre_f'] != $_POST['pre_fh']){
		$correjido = 3;
	}
	if($_POST['tie_f'] != $_POST['tie_fh']){
		$correjido = 3;
	}
	
	// tiempo que le corresponde al cliente segun corte
	if($opc == 6){
		$tiempo_para_salida = hora_salida($fec_in, $_POST['tie_f'], $_POST['tie_fs'], true);
	}else{
		$tiempo_para_salida = hora_salida($fec_in, $_POST['tie_f'], $_POST['tie_fs']);
	}
	
	// agregamos la nueva promo
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
	'".$_POST['pro_f']."',
	'".$cantidad."',
	'".$_POST['pre_f']."',
	'',
	'',
	'',
	'',
	'',
	'1',
	'".$id_usu_turno."',
	'".$tiempo_para_salida."',
	'".$correjido."'
	
	)";
	mysql_query($sql, $pconnect);
	
	if($opc == 1 || $opc == 2 || $opc == 6 || $opc == 7){
		// modificamos la hora de salida desde el principio
		$sql = "UPDATE tur SET fec_out = ADDTIME(fec_in, '".$tiempo_para_salida."') WHERE id_tur = '".$id_tur."'";
		mysql_query($sql, $pconnect);
	}else{
		// modificamos la hora de salida desde lo que tenia
		$sql = "UPDATE tur SET fec_out = ADDTIME(fec_out, '".$tiempo_para_salida."') WHERE id_tur = '".$id_tur."'";
		mysql_query($sql, $pconnect);
	}
	
	exit;	
}

?>