<?php

$abm->tabla = "sys_usuario";
$abm->registros_por_pagina = 15;
$abm->textoTituloFormularioAgregar = "Agregar Proveedor";
$abm->textoTituloFormularioEdicion = "Editar";
$abm->mostrarBorrar = false;
$abm->campoId = "id_usuario";

$g_sql = 
"SELECT sys_usuario.*, sys_tipo_usuario.tipo FROM sys_usuario
INNER JOIN sys_tipo_usuario ON sys_tipo_usuario.id_tipo = sys_usuario.id_tipo
where sys_usuario.id_tipo=9
"; 

$abm->campos = array(

	array(
		"campo" => "razonsocial", 
		"tipo" => "texto", 
		"titulo" => "Razon Social", 
		"customPrintListado" => "%s"
	),
	array(
		"campo" => "usuario", 
		"tipo" => "texto", 
		"titulo" => "Contacto", 
		"customPrintListado" => "%s"
	),
	array(
		"campo" => "id_tipo", 
		"tipo" => "dbCombo", 
		"sqlQuery" => "SELECT id_tipo, tipo FROM sys_tipo_usuario WHERE id_tipo = 9", 
		"campoValor" => "id_tipo", 
		"campoTexto" => "tipo", 
		"titulo" => "Tipo",
		"incluirOpcionVacia" => false,
		"noListar" => true
	),
	array(
		"campo" => "tipo", 
		"tipo" => "texto", 
		"titulo" => "Tipo", 
		"customPrintListado" => "%s",
		"noNuevo" => true,
		"noEditar" => true,
		"noMostrarEditar" => true
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

$abm->generarAbm($g_sql, "Administrar Proveedores");

?>