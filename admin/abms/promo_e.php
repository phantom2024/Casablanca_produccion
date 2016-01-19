<?php

$id_receta = $_REQUEST['id'];		
$resultado = $db->query("SELECT pro FROM pro WHERE id_pro = '".$id_receta."'");
while ($fila = $db->fetch_array($resultado)) {
	$nom_receta = $fila['pro'];
}

$abm->tabla = "pro_combinado";
$abm->registros_por_pagina = 15;
$abm->textoTituloFormularioAgregar = "Agregar Producto a ".$nom_receta."";
$abm->textoTituloFormularioEdicion = "Editar Producto a ".$nom_receta."";
$abm->mostrarBorrar = true;
$abm->campoId = "id_pro_com";

$g_sql = "

SELECT

	*,
	pro.pro as nom_pro,
	uni.uni
FROM pro_combinado

INNER JOIN pro ON pro.id_pro = pro_combinado.id_proc
INNER JOIN uni ON uni.id_uni = pro.id_uni

WHERE pro_combinado.id_pro = ".$id_receta." AND pro_combinado.tipo = 2 ";

$abm->adicionalesInsert = ", id_pro = ".$id_receta.", tipo = 2 ";

$abm->campos = array(

	array(
		"campo" => "id_proc", 
		"tipo" => "dbCombo", 
		"sqlQuery" => "
			SELECT pro.id_pro, CONCAT(pro.pro, ' - ', uni.uni) As nom_pro FROM pro 
			
			INNER JOIN uni ON uni.id_uni = pro.id_uni
			
			WHERE pro.bar = 1 AND pro.venta = 1 AND pro.promo = 0 ORDER BY pro
			", 
		"campoValor" => "id_pro",
		"campoTexto" => "nom_pro", 
		"titulo" => "Materia Prima",
		"incluirOpcionVacia" => false,
		"noListar" => true
	),			
	array(
		"campo" => "nom_pro", 
		"tipo" => "texto", 
		"titulo" => "Materia Prima", 
		"customPrintListado" => "%s",
		"noNuevo" => true,
		"noEditar" => true,
		"noMostrarEditar" => true
	),
	array(
		"campo" => "can", 
		"tipo" => "texto", 
		"titulo" => "Cantidad", 
		"maxLen" => 30,
		"customPrintListado" => "%s",
		"requerido" => true
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
	
);

$abm->generarAbm($g_sql, "Administrar Promos a ".$nom_receta."");

?>
<a href="abm.php?fx=promos">Volver</a>
