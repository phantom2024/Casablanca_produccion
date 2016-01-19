<?php

$abm->tabla = "log_hab";
$abm->registros_por_pagina = 15;
$abm->mostrarBorrar = false;
$abm->campoId = "id_log";

$g_sql = "

SELECT
	
	log_hab.*,
	CONCAT(hab.nro, ' ', tip_hab.tip) As hab
	
FROM log_hab

INNER JOIN hab ON hab.nro = log_hab.nro
INNER JOIN tip_hab ON tip_hab.id_tip = hab.tip

"; 

$abm->campos = array(
	array(
		"campo" => "hab", 
		"tipo" => "texto", 
		"titulo" => "Habitacion ", 
		"maxLen" => 30,
		"customPrintListado" => "%s",
	),
	array(
		"campo" => "est", 
		"tipo" => "texto", 
		"titulo" => "Estado ", 
		"maxLen" => 30,
		"customPrintListado" => "%s",
		"centrarColumna" => true
	),
	array(
		"campo" => "sin", 
		"tipo" => "bit", 
		"titulo" => "Sin Turno",
		"datos" => array("1"=>"SI", "0"=>"NO"),
		"valorPredefinido" => "1",
		"centrarColumna" => true
	),
	array(
		"campo" => "fec", 
		"tipo" => "texto", 
		"titulo" => "Fecha",
		"centrarColumna" => true
	)
);

$abm->generarAbm($g_sql, "Log de Estados");

?>