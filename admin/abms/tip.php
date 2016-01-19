<?php

$abm->tabla = "tip_hab";
$abm->registros_por_pagina = 15;
$abm->textoTituloFormularioAgregar = "Agregar Tipo de Habitacion";
$abm->textoTituloFormularioEdicion = "Editar Tipo de Habitacion";
$abm->mostrarBorrar = false;
$abm->campoId = "id_tip";

$g_sql = ""; 

$abm->campos = array(
	array(
		"campo" => "tip", 
		"tipo" => "texto", 
		"titulo" => "Nombre ", 
		"maxLen" => 30,
		"customPrintListado" => "%s",
		"requerido" => true,
		"hint" => "Nombre para el tipo de  habitacion"
	),
	array(
		"campo" => "act", 
		"tipo" => "bit", 
		"titulo" => "Activa", 
		"datos" => array("1"=>"SI", "0"=>"NO"),
		"valorPredefinido" => "1",
		"centrarColumna" => true,
		"hint" => "Indica si el Tipo estará activo"
	)
);

$abm->generarAbm($g_sql, "Administrar Tipo de Habitacion");

?>