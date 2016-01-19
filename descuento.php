<?php
require_once("boot.php");

$est = $_REQUEST['est'];

if($_POST['d']){
    
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
	$total_bar = 0;
	while($fila = mysql_fetch_assoc($result)){
		$imp_total_hab = $fila['total'];
	}
	
	?>
	<br />
    <div style="text-align:center;">
    <input type="hidden" id="est_tur_des" name="est_tur_des" value="<?php echo $est; ?>" />
    <input type="hidden" id="imp_total_hab" name="imp_total_hab" value="<?php echo $imp_total_hab; ?>" />
    <input type="button" id="des_p_imp" name="des_p_imp" value="Descuento Por Importe" />
    <input type="button" id="des_p_por" name="des_p_por" value="Descuento En Porcentaje" />
    </div>
    <br />
    <div>Total: $ <?php echo $imp_total_hab; ?></div>
    <br />
    <div>	
        <div style="float:left; width:100px;">Importe:</div>
    	<div style="float:left;"><input type="text" id="imp_descuento" name="imp_descuento" /></div>
        <div style="clear:both;"></div>
    </div>
    <div>	
        <div style="float:left; width:100px;">Porcentaje: </div>
    	<div style="float:left;"><input type="text" id="imp_porcentaje" name="imp_porcentaje" /></div>
        <div style="clear:both;"></div>
    </div>
    <div>	
        <div style="float:left; width:100px;">Total: </div>
    	<div style="float:left;"><input type="text" id="imp_total_des" name="imp_total_des" disabled="disabled" /></div>
        <div style="clear:both;"></div>
    </div>
    
    <script>
		$("#des_p_imp").button();
		$("#des_p_por").button();
	</script>
	<?

	exit;

}

if($_POST['gd']){
	
	$sql = "
	
	SELECT
		
		sys_comprobante.idcomprobante
		
	FROM tur
	
	INNER JOIN sys_comprobante ON sys_comprobante.id_turno = tur.id_tur
	
	WHERE
	
		tur.est_tur = '".$est."' AND
		tur.nro = '".$_REQUEST['id_hab']."';

	";
	
	$result = mysql_query($sql);
	while($fila = mysql_fetch_assoc($result)){
		$idcomprobante = $fila['idcomprobante'];
	}
	
	$descuento = $_REQUEST['imp_des'] * -1;

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
	".$idcomprobante.", '".ID_DESCUENTO."', '1', '".$descuento."', '', '', '', '', '', '1', '".$_SESSION['usuario']['id_usuario']."')";
	if(mysql_query($sql, $pconnect)){
		echo "ok";
	}else{
		echo "error";
	}
	
	exit;
	
}
