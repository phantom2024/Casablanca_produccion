<?php
require_once("boot.php");

$est = $_REQUEST['est'];

$sql = "UPDATE tur SET mostrar_cliente = '".$_POST['mos']."' WHERE est_tur = '".$est."' AND nro = '".$_POST['id_hab']."'";
if(mysql_query($sql, $pconnect)){
	echo "ok";
}else{
	echo "error";
}

?>