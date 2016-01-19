<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=windows-1252">
	<title>Demo abm</title>
	
	<!-- Estilos -->
	<link href="css/sitio.css" rel="stylesheet" type="text/css">
	<link href="css/abm.css" rel="stylesheet" type="text/css">

	<!-- MooTools -->
	<script type="text/javascript" src="js/mootools-1.2.3-core.js"></script>
	<script type="text/javascript" src="js/mootools-1.2.3.1-more.js"></script>
	
	<!--FormCheck-->
	<script type="text/javascript" src="js/formcheck/lang/es.js"></script>
	<script type="text/javascript" src="js/formcheck/formcheck.js"></script>
	<link rel="stylesheet" href="js/formcheck/theme/classic/formcheck.css" type="text/css" media="screen"/>

	<!--Datepicker-->
	<link rel="stylesheet" href="js/datepicker/datepicker_vista/datepicker_vista.css" type="text/css" media="screen"/>
	<script type="text/javascript" src="js/datepicker/datepicker.js"></script>

</head>
<body>

<?
require("comun/class_db.php");
require("comun/class_abm.php");
require("comun/class_paginado.php");
require("comun/class_orderby.php");

//conexión a la bd
$db = new class_db("localhost", "root", "", "demoabm");
$db->mostrarErrores = true;
$db->connect();

$abm = new class_abm();
$abm->tabla = "usuarios";
$abm->registros_por_pagina = 5;
$abm->textoTituloFormularioAgregar = "Agregar usuario";
$abm->textoTituloFormularioEdicion = "Editar usuario";
$abm->adicionalesInsert = ", fechaAlta=NOW()";

$abm->campos = array(
		array("campo" => "usuario", 
					"tipo" => "texto", 
					"titulo" => "Usuario", 
					"maxLen" => 30,
					"customPrintListado" => "<a href='javascript:alert(\"Ejemplo customPrintListado. id={id}\")' title='Ver usuario'>%s</a>",
					"requerido" => true,
					"hint" => "El usuario no debe existir"
					), 
		array("campo" => "pass", 
					"tipo" => "texto", 
					"titulo" => "Contraseña", 
					"maxLen" => 30,
					"noListar" => true,
					"requerido" => true,
					"hint" => "Elija una contraseña segura"
					),
		array("campo" => "activo", 
					"tipo" => "bit", 
					"titulo" => "Activo", 
					"datos" => array("1"=>"SI", "0"=>"NO"),
					"valorPredefinido" => "1",
					"centrarColumna" => true,
					"hint" => "Indica si el usuario estará activo"
					),
		array("campo" => "nivel", 
					"tipo" => "combo", 
					"titulo" => "Nivel", 
					"datos" => array("ADMIN"=>"Administrador", "USUARIO"=>"Usuario"),
					"valorPredefinido" => "USUARIO"
					),
		array("campo" => "fechaAlta", 
					"tipo" => "fecha", 
					"titulo" => "Fecha alta", 
					"noNuevo" => true
					),
		array("campo" => "email", 
					"tipo" => "texto", 
					"titulo" => "Email", 
					"maxLen" => 70,
					"requerido" => true
					),
		array("campo" => "nombre", 
					"tipo" => "texto", 
					"titulo" => "Nombre", 
					"maxLen" => 50
					),
		array("campo" => "apellido", 
					"tipo" => "texto", 
					"titulo" => "Apellido", 
					"maxLen" => 50
					),
		array("campo" => "comentarios", 
					"tipo" => "textarea", 
					"titulo" => "Comentarios", 
					"noListar" => true,
					"hint" => "Ingrese cualquier comentario que desee pero no se abuse porque este es un ejemplo de hint largo."
					),
		array("campo" => "paisId", 
					"tipo" => "dbCombo", 
					"sqlQuery" => "SELECT * FROM paises ORDER BY pais", 
					"campoValor" => "id", 
					"campoTexto" => "pais", 
					"titulo" => "País",
					"incluirOpcionVacia" => true,
					"noListar" => true,
					"requerido" => true
					),
		array("campo" => "ultimoLogin", 
					"tipo" => "texto", 
					"titulo" => "Ultimo login",
					"noEditar" => true, 
					"noListar" => true,
					"noNuevo" => true
					)
		);
$abm->generarAbm("", "Administrar usuarios");

echo "<br><br>";

if ( $_GET['vercodigo'] ){
	highlight_file(__FILE__);
}else{
	echo "<a href='?vercodigo=1'>Ver código fuente</a>";
}
?>

</body>
</html>