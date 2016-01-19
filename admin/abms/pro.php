<?php

$abm->tabla = "pro";
$abm->registros_por_pagina = 15;
$abm->textoTituloFormularioAgregar = "Agregar Producto";
$abm->textoTituloFormularioEdicion = "Editar Producto";
$abm->mostrarBorrar = false;
$abm->campoId = "id_pro";

$g_sql = "

SELECT
	
	pro.*,
	rub.rub,
	uni.uni
	
FROM pro

INNER JOIN rub ON rub.id_rub = pro.id_rub
INNER JOIN uni ON uni.id_uni = pro.id_uni

WHERE pro.bar = 1 AND materia = 0";

$abm->adicionalesInsert = ", bar = 1, materia = 0 ";

$abm->campos = array(
	array(
		"campo" => "pro", 
		"tipo" => "texto", 
		"titulo" => "Producto", 
		"maxLen" => 30,
		"customPrintListado" => "%s",
		"requerido" => true,
		"hint" => "Nombre para el producto"
	),
	array(
		"campo" => "val", 
		"tipo" => "texto", 
		"titulo" => "Valor", 
		"maxLen" => 30,
		"customPrintListado" => "%s",
		"requerido" => true
	),			
	array(
		"campo" => "sto", 
		"tipo" => "texto", 
		"titulo" => "Stock Inicial", 
		"maxLen" => 30,
		"customPrintListado" => "%s",
		"requerido" => true
	),
	array(
		"campo" => "id_rub", 
		"tipo" => "dbCombo", 
		"sqlQuery" => "SELECT id_rub, rub FROM rub ORDER BY rub", 
		"campoValor" => "id_rub", 
		"campoTexto" => "rub", 
		"titulo" => "Rubro",
		"incluirOpcionVacia" => false,
		"noListar" => true
	),			
	array(
		"campo" => "rub", 
		"tipo" => "texto", 
		"titulo" => "Rubro", 
		"customPrintListado" => "%s",
		"noNuevo" => true,
		"noEditar" => true,
		"noMostrarEditar" => true
	),
	array(
		"campo" => "id_uni", 
		"tipo" => "dbCombo", 
		"sqlQuery" => "SELECT id_uni, uni FROM uni ORDER BY uni", 
		"campoValor" => "id_uni", 
		"campoTexto" => "uni", 
		"titulo" => "Unidad",
		"incluirOpcionVacia" => false,
		"noListar" => true
	),
	array(
		"campo" => "uni", 
		"tipo" => "texto", 
		"titulo" => "Unidad", 
		"customPrintListado" => "%s",
		"noNuevo" => true,
		"noEditar" => true,
		"noMostrarEditar" => true
	),
	array(
		"campo" => "promo", 
		"tipo" => "bit", 
		"titulo" => "Promo", 
		"datos" => array("1"=>"SI", "0"=>"NO"),
		"valorPredefinido" => "1",
		"centrarColumna" => true,
		"hint" => "Indica si el Producto es promo"
	),
	array(
		"campo" => "elaborado", 
		"tipo" => "bit", 
		"titulo" => "P. Elaborado", 
		"datos" => array("1"=>"SI", "0"=>"NO"),
		"valorPredefinido" => "1",
		"centrarColumna" => true,
		"hint" => "Indica si el Producto es elaborado"
	)

);

$abm->generarAbm($g_sql, "Administrar Productos");

?>