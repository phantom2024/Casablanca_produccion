<?php
require_once("boot.php");

$est = $_REQUEST['est'];

if($_POST['r']){
    
	$sql = "
	
	SELECT SUM(sys_comprobante_detalle.cantidad * sys_comprobante_detalle.importe) AS total FROM tur 
	
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
    <div style="text-align:center;">
    <input type="hidden" id="est_tur_des" name="est_tur_des" value="<?php echo $est; ?>" />
    <input type="hidden" id="imp_total_hab_aum" name="imp_total_hab_aum" value="<?php echo $imp_total_hab; ?>" />
    </div>
    <br />
    <div>Total: $ <?php echo $imp_total_hab; ?></div>
    <br />
    <div>	
        <div style="float:left; width:100px;">Importe:</div>
    	<div style="float:left;"><input type="text" id="imp_aumento" name="imp_aumento" value="" /></div>
        <div style="clear:both;"></div>
    </div>
	<?

	exit;

}

if($_POST['gr']){
	
	$sql = "
	SELECT sys_comprobante.idcomprobante FROM tur
	INNER JOIN sys_comprobante ON sys_comprobante.id_turno = tur.id_tur
	WHERE tur.est_tur = '".$est."' AND tur.nro = '".$_REQUEST['id_hab']."';
	";
	$result = mysql_query($sql);
	while($fila = mysql_fetch_assoc($result)){
		$idcomprobante = $fila['idcomprobante'];
	}
	
	$imp_aum = $_REQUEST['imp_aum'];

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
	".$idcomprobante.", '".ID_RECARGO."', '1', '".$imp_aum."', '', '', '', '', '', '1', '".$_SESSION['usuario']['id_usuario']."')";
	if(mysql_query($sql, $pconnect)){
		echo "ok";
	}else{
		echo "error";
	}
	
	exit;
	
}
