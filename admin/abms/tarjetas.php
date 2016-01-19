<?php

$abm->tabla = "sys_tarjetas";
$abm->registros_por_pagina = 15;
$abm->textoTituloFormularioAgregar = "Agregar Tarjeta";
$abm->textoTituloFormularioEdicion = "Editar Tarjeta";
$abm->mostrarBorrar = false;
$abm->campoId = "id_tarjeta";

$g_sql = ""; 

$abm->campos = array(

	array(
		"campo" => "codigo", 
		"tipo" => "texto", 
		"titulo" => "Codigo de Barra", 
		"customPrintListado" => "%s"
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

$abm->generarAbm($g_sql, "Administrar Tarjetas");

?>