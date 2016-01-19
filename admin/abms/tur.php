<?php

$abm->tabla = "pro";
$abm->registros_por_pagina = 15;
$abm->textoTituloFormularioAgregar = "Agregar Turnos";
$abm->textoTituloFormularioEdicion = "Editar Turnos";
$abm->mostrarBorrar = false;
$abm->campoId = "id_pro";

$g_sql = "
SELECT
	
	pro.*,
	tip_hab.tip AS tip_m,

	CASE dia
		WHEN 1 THEN 'LUN A JUEV'
		WHEN 2 THEN 'VIE Y SAB'
		WHEN 3 THEN 'DOMINGO'
	END AS dia_lis,

	CASE hor
		WHEN 1 THEN '8 A 14 HS'
		WHEN 2 THEN '14 A 8 HS'
		WHEN 3 THEN '00 A 8 HS'
	END AS hor_lis

FROM pro

INNER JOIN tip_hab ON tip_hab.id_tip = pro.tip

WHERE pro.bar = 0
"; 

$abm->adicionalesInsert = ", bar=0, sto=0";

$abm->campos = array(
	array(
		"campo" => "pro", 
		"tipo" => "texto", 
		"titulo" => "Nombre", 
		"maxLen" => 30,
		"customPrintListado" => "%s",
		"requerido" => true,
		"hint" => "Nombre para el turno"
	),
	array(
		"campo" => "tip_m", 
		"tipo" => "texto", 
		"titulo" => "Tipo",
		"noNuevo" => true,
		"noEditar" => true,
		"noMostrarEditar" => true
	),
	array(
		"campo" => "tip", 
		"tipo" => "dbCombo", 
		"sqlQuery" => "SELECT id_tip, tip FROM tip_hab ORDER BY id_tip", 
		"campoValor" => "id_tip", 
		"campoTexto" => "tip", 
		"titulo" => "Tipo",
		"incluirOpcionVacia" => false,
		"noListar" => true
	),
	array(
		"campo" => "dia_lis", 
		"tipo" => "texto", 
		"titulo" => "Dia", 
		"maxLen" => 30,
		"customPrintListado" => "%s",
		"noNuevo" => true,
		"noEditar" => true,
		"noMostrarEditar" => true
	),
	array(
		"campo" => "dia",
		"tipo" => "combo",
		"titulo" => "Dia",
		"datos" => array(1=>"LUN A JUEV", 2=>"VIE Y SAB", 3=>"DOMINGO"),
		"noListar" => true
	),
	array(
		"campo" => "hor_lis", 
		"tipo" => "texto", 
		"titulo" => "Horario", 
		"maxLen" => 30,
		"customPrintListado" => "%s",
		"noNuevo" => true,
		"noEditar" => true,
		"noMostrarEditar" => true
	),
	array(
		"campo" => "hor", 
		"tipo" => "combo", 
		"titulo" => "Horario", 
		"datos" => array(1=>"8 A 14 HS", 2=>"14 A 8 HS", 3=>"00 A 8 HS"),
		"noListar" => true
	),
	array(
		"campo" => "val", 
		"tipo" => "texto", 
		"titulo" => "Valor", 
		"maxLen" => 70,
		"requerido" => true
	),
	array(
		"campo" => "tie", 
		"tipo" => "texto", 
		"titulo" => "Tiempo", 
		"maxLen" => 8,
		"hint" => "Formato 00:00:00 hor:min:seg"
	)
);

$abm->generarAbm($g_sql, "Administrar Turnos");


?>