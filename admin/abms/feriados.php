<link href="../css/cupertino/jquery-ui-1.10.3.custom.css" rel="stylesheet">
<script src="../js/jquery-1.9.1.js"></script>
<script src="../js/jquery-ui-1.10.3.custom.js"></script>

<?

$abm->tabla = "feriados";
$abm->registros_por_pagina = 15;
$abm->textoTituloFormularioAgregar = "Agregar Feriado";
$abm->textoTituloFormularioEdicion = "Editar Feriado";
$abm->mostrarBorrar = true;
$abm->campoId = "id_fer";

$g_sql = "SELECT *, DATE_FORMAT(fec, '%d-%m-%Y') AS fecha FROM feriados "; 

$abm->campos = array(
	array(
		"campo" => "fecha", 
		"tipo" => "texto", 
		"titulo" => "Feriado", 
		"customPrintListado" => "%s",
		"noNuevo" => true,
		"noEditar" => true,
		"noMostrarEditar" => true
	),
	array(
		"campo" => "fec", 
		"tipo" => "texto", 
		"titulo" => "Feriado",
		"customPrintListado" => "%s",
		"noListar" => true
	),
	array(
		"campo" => "activo", 
		"tipo" => "bit", 
		"titulo" => "Activo",
		"datos" => array("1"=>"SI", "0"=>"NO"),
		"valorPredefinido" => "1",
		"centrarColumna" => true
	)
);

$abm->generarAbm($g_sql, "Administrar Feriados");

?>

<script>
$(function() {
	
	$("#fec").datepicker({ dateFormat: "yy-mm-dd" });
	
});
</script>
