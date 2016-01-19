<?php

$abm->tabla = "rub";
$abm->registros_por_pagina = 15;
$abm->textoTituloFormularioAgregar = "Agregar Rubro";
$abm->textoTituloFormularioEdicion = "Editar Rubro";
$abm->mostrarBorrar = false;
$abm->campoId = "id_rub";

$abm->campos = array(
	array(
		"campo" => "id_rub", 
		"tipo" => "texto", 
		"titulo" => "#", 
		"customPrintListado" => "%s",
		"noNuevo" => true,
		"noEditar" => true,
		"noMostrarEditar" => true
	),
	array(
		"campo" => "rub", 
		"tipo" => "texto", 
		"titulo" => "Rubro", 
		"maxLen" => 45,
		"customPrintListado" => "%s",
		"requerido" => true,
		"hint" => "Nombre para la Rubro"
	)

);

$abm->generarAbm("", "Administrar Rubro");

?>