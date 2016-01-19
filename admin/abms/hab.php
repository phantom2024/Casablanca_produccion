<?php

$abm->tabla = "hab";
$abm->registros_por_pagina = 15;
$abm->textoTituloFormularioAgregar = "Agregar Habitacion";
$abm->textoTituloFormularioEdicion = "Editar Habitacion";
$abm->mostrarBorrar = false;
$abm->campoId = "id_hab";

$g_sql = "
SELECT * FROM hab
INNER JOIN tip_hab ON tip_hab.id_tip = hab.tip
"; 

$abm->adicionalesInsert = ", est=0, act=1";

$abm->campos = array(
	array(
		"campo" => "nro", 
		"tipo" => "texto", 
		"titulo" => "Numero", 
		"maxLen" => 30,
		"customPrintListado" => "%s",
		"requerido" => true,
		"hint" => "Numero para la habitacion"
	),
	array(
		"campo" => "tip", 
		"tipo" => "dbCombo", 
		"sqlQuery" => "SELECT id_tip, tip FROM tip_hab ORDER BY id_tip", 
		"campoValor" => "id_tip", 
		"campoTexto" => "tip", 
		"titulo" => "Tipo",
		"incluirOpcionVacia" => false,
		"noListar" => false,
		"requerido" => true
	),
	array(
		"campo" => "utl", 
		"tipo" => "texto", 
		"titulo" => "Actualizacion", 
		"maxLen" => 30,
		"customPrintListado" => "%s",
		"noNuevo" => true,
		"noEditar" => true,
		"noMostrarEditar" => true
	),
	array(
		"campo" => "act",
		"tipo" => "bit", 
		"titulo" => "Activa", 
		"datos" => array("1"=>"SI", "0"=>"NO"),
		"valorPredefinido" => "1",
		"centrarColumna" => true,
		"hint" => "Indica si la habitacion estará activa"
	)
);

$abm->generarAbm($g_sql, "Administrar Habitacion");


?>