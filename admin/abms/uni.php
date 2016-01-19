<?php

$abm->tabla = "uni";
$abm->registros_por_pagina = 15;
$abm->textoTituloFormularioAgregar = "Agregar Unidad";
$abm->textoTituloFormularioEdicion = "Editar Unidad";
$abm->mostrarBorrar = false;
$abm->campoId = "id_uni";

$abm->campos = array(
	array(
		"campo" => "id_uni", 
		"tipo" => "texto", 
		"titulo" => "#", 
		"customPrintListado" => "%s",
		"noNuevo" => true,
		"noEditar" => true,
		"noMostrarEditar" => true
	),
	array(
		"campo" => "uni", 
		"tipo" => "texto", 
		"titulo" => "Unidad", 
		"maxLen" => 45,
		"customPrintListado" => "%s",
		"requerido" => true,
		"hint" => "Nombre para la unidad"
	)

);

$abm->generarAbm("", "Administrar Unidad");

?>