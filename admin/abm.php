<?php
session_start();
error_reporting(0);
if ($_GET['fx']<>'proveedores'){
	if($_SESSION['usuario']['id_tipo'] != 1){
		header("Location: ../index.php");
		exit;
	}
}

$archivo = "abms/".$_GET['fx'].".php";
if(!file_exists($archivo)){
	echo "Error! Llamar ha Digital Creative Tel: (261)425-1631";
	exit;
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=windows-1252">
	<title>Abm's</title>
	
	<!-- Estilos -->
	<link href="css/sitio.css" rel="stylesheet" type="text/css">
	<link href="css/abm.css" rel="stylesheet" type="text/css">

	<!-- MooTools -->
	<script type="text/javascript" src="js/mootools-1.2.3-core.js"></script>
	<script type="text/javascript" src="js/mootools-1.2.3.1-more.js"></script>
	
	<!--FormCheck-->
	<script type="text/javascript" src="js/formcheck/lang/es.js"></script>
	<script type="text/javascript" src="js/formcheck/formcheck.js"></script>
	<link rel="stylesheet" href="js/formcheck/theme/classic/formcheck.css" type="text/css" media="screen"/>

	<!--Datepicker-->
	<link rel="stylesheet" href="js/datepicker/datepicker_vista/datepicker_vista.css" type="text/css" media="screen"/>
	<script type="text/javascript" src="js/datepicker/datepicker.js"></script>
	
	<style>
	
		.alternar:hover{	
			background-color:#B9F8F8;
		}
		#tabla_hab{
			display: table;
			border: 1px #E5E5E5 solid;
			border-collapse: collapse;
			border-spacing: 2px;
			font-family: Verdana,Helvetica;
			font-size: 10px;
		}
		#tabla_hab tbody{
			display: table-row-group;
			vertical-align: middle;
			border-color: inherit;
			background : #FDFDFD;
		}
		#tabla_hab tr{
			display: table-row;
			vertical-align: inherit;
			border-color: inherit;
		}
		#tabla_hab td{
			padding: 5px;
			border-left: 1px solid #E5E5E5;
			border-right: 1px solid #E5E5E5;
		}
	
	</style>

</head>
<body>

<?

require("comun/class_db.php");
require("comun/class_abm.php");
require("comun/class_paginado.php");
require("comun/class_orderby.php");

require_once("../config_db.php");

$db = new class_db(VAR_HOST, VAR_USERDB, VAR_PASSDB, VAR_DB);
$db->mostrarErrores = true;
$db->connect();

$abm = new class_abm();

include($archivo);

?>

</body>
</html>