<?php
session_start();

if($_SESSION['usuario']['id_tipo'] != 1){
	header("Location: ../index.php");
	exit;
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=windows-1252">
	<title>Abm's</title>
	
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
	
	<style>
	.alternar:hover
	{	
		background-color:#B9F8F8;
	}
	
	#tabla_hab{
		display: table;
		border: 1px #E5E5E5 solid;
		border-collapse: collapse;
		border-spacing: 2px;
		font-family: Verdana,Helvetica;
		font-size: 10px;
	}
	
	#tabla_hab tbody{
		display: table-row-group;
		vertical-align: middle;
		border-color: inherit;
		background : #FDFDFD;
	}
	
	#tabla_hab tr{
		display: table-row;
		vertical-align: inherit;
		border-color: inherit;
	}
	
	#tabla_hab td{
		padding: 5px;
		border-left: 1px solid #E5E5E5;
		border-right: 1px solid #E5E5E5;
	}
	</style>

</head>
<body>

<?
require("comun/class_db.php");
require("comun/class_abm.php");
require("comun/class_paginado.php");
require("comun/class_orderby.php");

require_once("../config_db.php");

$db = new class_db(VAR_HOST, VAR_USERDB, VAR_PASSDB, VAR_DB);
$db->mostrarErrores = true;
$db->connect();

$abm = new class_abm();

switch($_GET['fx']){

	case 'hab':
		
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
	break;
	
	case 'tur':

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
					
	break;

	case 'uni':

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
					
	break;
	
	case 'rub':

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
					
	break;

	case 'tip':
		
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
	break;
	
	case 'log':
		
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
	break;
	
	case 'caja':
		?>
		
		<link href="../css/cupertino/jquery-ui-1.10.3.custom.css" rel="stylesheet">
		<script src="../js/jquery-1.9.1.js"></script>
		<script src="../js/jquery-ui-1.10.3.custom.js"></script>
		<br/>
		<form action="abm.php" method="get">
		
		<input type="hidden" id="fx" name="fx" value="dat">
		<table>
		<tr>
		<th>Fecha</th>
		<th>Fecha hasta</th>
		<th>Turno</th>
		<th>Empleado</th>
		</tr>
		<tr>
        
        <?
		if($_GET['fec']){
        	$fecha_input = $_GET['fec'];
			$fecha_hasta = $_GET['fec_has'];
        }else{
			$fecha_input = date("d-m-Y");
			$fecha_hasta = date("d-m-Y");
        }		
		?>
        
		<td><input type="text" id="fec" name="fec" value="<? echo $fecha_input; ?>"></td>
		<td><input type="text" id="fec_has" name="fec_has" value="<? echo $fecha_hasta; ?>"></td>
		<td>
			<select id ="sel" name="sel">
			  <option value="sel_todos">Todos</option>
			  <option value="sel_cerrado">Cerrado</option>
			  <option value="sel_abierto">Abierto</option>
			</select>
		</td>
		<td>
		<select id ="prod" name="prod">
		<option value="prod_todos">TODOS</option>
		<?
		
			$consulta = "SELECT pro FROM pro";
			$resultado = mysql_query($consulta);
			echo "<br/>";
			while ($fila = mysql_fetch_array($resultado)) 
			{
				echo "<option value='".$fila['pro']."'>".$fila['pro']."</option>";
			}
		?>	  
		</select>
		</td>
		<td>
		<input type="submit" id="listar_but" value="Listar">
		</td>	
		</table>
		
		
		<?
		
		$estado="";
		if($_GET['sel'])
		{
			if($_GET['sel']=='sel_todos')
			{
				$estado="";
			}
			else if($_GET['sel']=='sel_cerrado')
			{
				$estado=" AND est_tur = 0";
			}
			else if($_GET['sel']=='sel_abierto')
			{
				$estado= " AND est_tur = 1";
			}
		}
		$prod="";
		if($_GET['prod'])
		{
			if($_GET['prod']=='prod_todos')
			{
				$prod="";
			}
			else
			{
				$prod= " AND pro.pro = '".$_GET['prod']."'";
			}
		}
			
				$date = new DateTime($fecha_input);
				$fec = $date->format('Y-m-d');
				
				$date2 = new DateTime($fecha_hasta);
				$fec_h = $date2->format('Y-m-d');
				
 
				$g_sql = "
				SELECT
					
					tur.id_tur,
					est_tur,
					fec,
					fec_in,
					fec_out,
					cambio_ama,
					sys_comprobante_detalle.importe,
					pro.pro,
					sys_comprobante_detalle.cantidad,
					hab.nro,
					tip_hab.tip,
					TIMEDIFF(cambio_ama, fec_in) AS tiempo_hab
					
				
				FROM tur 
				
				INNER JOIN sys_comprobante ON sys_comprobante.id_turno = tur.id_tur 
				INNER JOIN sys_comprobante_detalle ON sys_comprobante_detalle.idcomprobante = sys_comprobante.idcomprobante 
				INNER JOIN pro ON pro.id_pro = sys_comprobante_detalle.idproducto
				INNER JOIN hab ON hab.nro = tur.nro
				INNER JOIN tip_hab ON tip_hab.id_tip = hab.tip
				
				WHERE date(tur.fec) >= '".$fec."' AND
					  date(tur.fec) <= '".$fec_h."'
				
				".$estado.$prod."
				
				GROUP BY id_tur
				";
				
				$resultado = $db->query($g_sql);
			
		if(mysql_affected_rows() > 0)
		{
			echo "<br/>";
			echo '<table border="1" id="Exportar_a_Excel" width="100%" cellpadding="2" cellspacing="2"><tr>';	
			echo "<th>Id Turno</th>";
			
			// fabian vallejo agrege numero y tipo de habitacion
			echo "<th>Habitación</th>";
			
			echo "<th>Estado</th>";
			echo "<th>Fecha</th>";
			echo "<th>Fecha in</th>";
			echo "<th>Fecha out</th>";
			echo "<th>Fecha salida</th>";
			echo "<th>Valor</th>";
			echo "<th>Ver</th>";
			//echo "<th>Tiempo</th>";
			//echo "<th>Producto</th></tr>";
			
			$totalValor;
			$totalTiempo;
			
			while ($fila = mysql_fetch_assoc($resultado)) 
			{
				
				$tiempo_hab = $fila['tiempo_hab'];	
			
				
				//print_r($fila);
				echo "<tr class='alternar'>";
				echo "<td>$fila[id_tur]</td>";
				
				// fabian vallejo agrege numero y tipo de habitacion
				echo "<td>$fila[nro] - $fila[tip]</td>";
				
				if($fila[est_tur]=='0')
				{
					echo "<td>Cerrado</td>";
				}
				else
				{
					echo "<td>Abierto</td>";
				}
				
				$date = new DateTime($fila['fec']);
				echo "<td>".$date->format('d-m-Y H:i')."</td>";
				
				$date = new DateTime($fila['fec_in']);
				echo "<td>".$date->format('d-m-Y H:i')."</td>";
				
				$date = new DateTime($fila['fec_out']);
				echo "<td>".$date->format('d-m-Y H:i')."</td>";
				
				
				if($fila['cambio_ama'] == NULL || $fila['cambio_ama'] == '0000-00-00 00:00:00'){
					echo "<td>Sin Salida</td>";
				}else{
					$date = new DateTime($fila['cambio_ama']);
					echo "<td>".$date->format('d-m-Y H:i')."</td>";
				}
				
				//echo "<td>".$fila['cambio_ama']."</td>";
				
				$valor = 0;
				/*			
				$query = "
				
				SELECT
					
					tur_det.val,
					tur_det.tie,
					tur_det.can,
					pro.pro
					
				FROM tur_det
				
				INNER JOIN pro ON tur_det.id_pro = pro.id_pro
				
				WHERE id_tur = $fila[id_tur]
				
				";
				*/
				$query = "
				
				SELECT
					
					sys_comprobante_detalle.importe,
					sys_comprobante_detalle.cantidad,
					pro.pro
					
				FROM sys_comprobante_detalle
				
				INNER JOIN sys_comprobante ON sys_comprobante.idcomprobante = sys_comprobante_detalle.idcomprobante 
				
				INNER JOIN pro ON pro.id_pro = sys_comprobante_detalle.idproducto
				
				WHERE sys_comprobante.id_turno = $fila[id_tur]
				
				";		
				$con = mysql_query($query);
				while($tab = mysql_fetch_array($con))
				{
					$valor += $tab['importe']*$tab['cantidad']; 
				}
				$totalValor += $valor;
				
				echo "<td>$ "."$valor</td>";
				echo "<td><center><a class='ver_tur' href='#".$fila['id_tur']."'>Ver</a></center></td>";

				echo "</tr>";
				echo '<tr><td colspan=9><div class="tab_oc" id="tab_'.$fila['id_tur'].'" style="display:none;">';
				/*
				$query = "
				SELECT val,tie,can FROM tur_det
				WHERE id_tur = $fila[id_tur]
				";*/
				
				/*
				$query = "
				SELECT tur_det.val,tur_det.tie,tur_det.can,pro.pro FROM tur_det
				
				INNER JOIN pro ON tur_det.id_pro = pro.id_pro
				
				WHERE id_tur = $fila[id_tur]
				";
				*/
				
				$sql = "
				
				SELECT
					
					sys_comprobante_detalle.importe,
					sys_comprobante_detalle.cantidad,
					pro.pro
					
				FROM sys_comprobante_detalle
				
				INNER JOIN sys_comprobante ON sys_comprobante.idcomprobante = sys_comprobante_detalle.idcomprobante 
				
				INNER JOIN pro ON pro.id_pro = sys_comprobante_detalle.idproducto
				
				WHERE sys_comprobante.id_turno = $fila[id_tur]
				
				";
				
				$con = mysql_query($query);
				echo '<table width="100%" style="text-align:center;">';
				echo "<tr><th>Cantidad</th><th>Tiempo</th><th>Valor</th><th>Subtotal</th><th>Producto</th></tr>";
				while($tab = mysql_fetch_array($con))
				{
					echo "<tr>";
					echo "<td>$tab[cantidad]</td>";
					
					/*
					if($tab['tie'] != null)
					{
						$date = new DateTime($tab['tie']);
						echo "<td>Hs. ".$date->format('H:i')."</td>";
					}
					else
					{
						
						echo "<td></td>";
					}
					*/
					echo "<td>Hs. ".$tiempo_hab."</td>";
					
					
					echo "<td>$tab[importe]</td>";
					$subtotal = $tab['importe'] * $tab['cantidad'];
					echo "<td>".$subtotal."</td>";
					echo "<td>$tab[pro]</td>";
					echo "</tr>";
				}
				echo "</table>";
				
				echo "</div></td></tr>";
			}
				
				echo "<tr>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td style='text-align:right'>Total: </td>";
				echo "<td>$ "."$totalValor</td>";
				echo "<td></td>";
				echo "<td></td>";
				
				echo "</tr>";
				
			echo "</table>";
			?>
			</form>
			<br>
			<form action="ficheroExcel.php" method="post" target="_blank" id="FormularioExportacion">
			<input value="Exportar Excel" type="submit" class="botonExcel" />
			<input type="hidden" id="datos_a_enviar" name="datos_a_enviar" />
			</form>
			<?
			
		}else{
			
			?>
			<br>
            <div style="color:#F00;">No hay datos para los campos solicitados.</div>
			<?
			
		}
	break;
	
	case 'dat':
		?>
		
		<link href="../css/cupertino/jquery-ui-1.10.3.custom.css" rel="stylesheet">
		<script src="../js/jquery-1.9.1.js"></script>
		<script src="../js/jquery-ui-1.10.3.custom.js"></script>
		<script>
		$(function() {
			
			$("#fec").datepicker({ dateFormat: "dd-mm-yy" });
			$("#fec_has").datepicker({ dateFormat: "dd-mm-yy" });
			
			$(".botonExcel").click(function(event) {
				$("#datos_a_enviar").val( $("<div>").append( $("#Exportar_a_Excel").eq(0).clone()).html());
				$("#FormularioExportacion").submit();
			});
			
			$(".ver_tur").click(function(event) {
				
				$(".tab_oc").hide();
				
				var hr = $(this).attr('href');
				hr = hr.replace("#", "");
				console.log(hr);
				
				$("#tab_"+hr).show();
				
			});
			
		});
		</script>
		<br/>
		<form action="abm.php" method="get">
		
		<input type="hidden" id="fx" name="fx" value="dat">
		<table>
		<tr>
		<th>Fecha</th>
		<th>Fecha hasta</th>
		<th>Estado</th>
		<th>Producto</th>
		</tr>
		<tr>
        
        <?
		if($_GET['fec']){
        	$fecha_input = $_GET['fec'];
			$fecha_hasta = $_GET['fec_has'];
        }else{
			$fecha_input = date("d-m-Y");
			$fecha_hasta = date("d-m-Y");
        }		
		?>
        
		<td><input type="text" id="fec" name="fec" value="<? echo $fecha_input; ?>"></td>
		<td><input type="text" id="fec_has" name="fec_has" value="<? echo $fecha_hasta; ?>"></td>
		
		<td>
			<select id ="sel" name="sel">
			  <option value="sel_todos">Todos</option>
			  <option value="sel_cerrado">Cerrado</option>
			  <option value="sel_abierto">Abierto</option>
			</select>
		</td>
		<td>
		<select id ="prod" name="prod">
		<option value="prod_todos">TODOS</option>
		<?
			$consulta = "SELECT pro FROM pro";
			$resultado = mysql_query($consulta);
			echo "<br/>";
			while ($fila = mysql_fetch_array($resultado)) 
			{
				echo "<option value='".$fila['pro']."'>".$fila['pro']."</option>";
			}
		?>	  
		</select>
		</td>
		<td>
		<input type="submit" id="listar_but" value="Listar">
		</td>	
		</table>
		
		
		<?
		
		$estado="";
		if($_GET['sel'])
		{
			if($_GET['sel']=='sel_todos')
			{
				$estado="";
			}
			else if($_GET['sel']=='sel_cerrado')
			{
				$estado=" AND est_tur = 0";
			}
			else if($_GET['sel']=='sel_abierto')
			{
				$estado= " AND est_tur = 1";
			}
		}
		$prod="";
		if($_GET['prod'])
		{
			if($_GET['prod']=='prod_todos')
			{
				$prod="";
			}
			else
			{
				$prod= " AND pro.pro = '".$_GET['prod']."'";
			}
		}
			
				$date = new DateTime($fecha_input);
				$fec = $date->format('Y-m-d');
				
				$date2 = new DateTime($fecha_hasta);
				$fec_h = $date2->format('Y-m-d');
				
 
				$g_sql = "
				SELECT
					
					tur.id_tur,
					est_tur,
					fec,
					fec_in,
					fec_out,
					cambio_ama,
					sys_comprobante_detalle.importe,
					pro.pro,
					sys_comprobante_detalle.cantidad,
					hab.nro,
					tip_hab.tip,
					TIMEDIFF(cambio_ama, fec_in) AS tiempo_hab
					
				
				FROM tur 
				
				INNER JOIN sys_comprobante ON sys_comprobante.id_turno = tur.id_tur 
				INNER JOIN sys_comprobante_detalle ON sys_comprobante_detalle.idcomprobante = sys_comprobante.idcomprobante 
				INNER JOIN pro ON pro.id_pro = sys_comprobante_detalle.idproducto
				INNER JOIN hab ON hab.nro = tur.nro
				INNER JOIN tip_hab ON tip_hab.id_tip = hab.tip
				
				WHERE date(tur.fec) >= '".$fec."' AND
					  date(tur.fec) <= '".$fec_h."'
				
				".$estado.$prod."
				
				GROUP BY id_tur
				";
				
				$resultado = $db->query($g_sql);
			
		if(mysql_affected_rows() > 0)
		{
			echo "<br/>";
			echo '<table border="1" id="Exportar_a_Excel" width="100%" cellpadding="2" cellspacing="2"><tr>';	
			echo "<th>Id Turno</th>";
			
			// fabian vallejo agrege numero y tipo de habitacion
			echo "<th>Habitación</th>";
			
			echo "<th>Estado</th>";
			echo "<th>Fecha</th>";
			echo "<th>Fecha in</th>";
			echo "<th>Fecha out</th>";
			echo "<th>Fecha salida</th>";
			echo "<th>Valor</th>";
			echo "<th>Ver</th>";
			//echo "<th>Tiempo</th>";
			//echo "<th>Producto</th></tr>";
			
			$totalValor;
			$totalTiempo;
			
			while ($fila = mysql_fetch_assoc($resultado)) 
			{
				
				$tiempo_hab = $fila['tiempo_hab'];	
			
				
				//print_r($fila);
				echo "<tr class='alternar'>";
				echo "<td>$fila[id_tur]</td>";
				
				// fabian vallejo agrege numero y tipo de habitacion
				echo "<td>$fila[nro] - $fila[tip]</td>";
				
				if($fila[est_tur]=='0')
				{
					echo "<td>Cerrado</td>";
				}
				else
				{
					echo "<td>Abierto</td>";
				}
				
				$date = new DateTime($fila['fec']);
				echo "<td>".$date->format('d-m-Y H:i')."</td>";
				
				$date = new DateTime($fila['fec_in']);
				echo "<td>".$date->format('d-m-Y H:i')."</td>";
				
				$date = new DateTime($fila['fec_out']);
				echo "<td>".$date->format('d-m-Y H:i')."</td>";
				
				
				if($fila['cambio_ama'] == NULL || $fila['cambio_ama'] == '0000-00-00 00:00:00'){
					echo "<td>Sin Salida</td>";
				}else{
					$date = new DateTime($fila['cambio_ama']);
					echo "<td>".$date->format('d-m-Y H:i')."</td>";
				}
				
				//echo "<td>".$fila['cambio_ama']."</td>";
				
				$valor = 0;
				/*			
				$query = "
				
				SELECT
					
					tur_det.val,
					tur_det.tie,
					tur_det.can,
					pro.pro
					
				FROM tur_det
				
				INNER JOIN pro ON tur_det.id_pro = pro.id_pro
				
				WHERE id_tur = $fila[id_tur]
				
				";
				*/
				$query = "
				
				SELECT
					
					sys_comprobante_detalle.importe,
					sys_comprobante_detalle.cantidad,
					pro.pro
					
				FROM sys_comprobante_detalle
				
				INNER JOIN sys_comprobante ON sys_comprobante.idcomprobante = sys_comprobante_detalle.idcomprobante 
				
				INNER JOIN pro ON pro.id_pro = sys_comprobante_detalle.idproducto
				
				WHERE sys_comprobante.id_turno = $fila[id_tur]
				
				";		
				$con = mysql_query($query);
				while($tab = mysql_fetch_array($con))
				{
					$valor += $tab['importe']*$tab['cantidad']; 
				}
				$totalValor += $valor;
				
				echo "<td>$ "."$valor</td>";
				echo "<td><center><a class='ver_tur' href='#".$fila['id_tur']."'>Ver</a></center></td>";

				echo "</tr>";
				echo '<tr><td colspan=9><div class="tab_oc" id="tab_'.$fila['id_tur'].'" style="display:none;">';
				/*
				$query = "
				SELECT val,tie,can FROM tur_det
				WHERE id_tur = $fila[id_tur]
				";*/
				
				/*
				$query = "
				SELECT tur_det.val,tur_det.tie,tur_det.can,pro.pro FROM tur_det
				
				INNER JOIN pro ON tur_det.id_pro = pro.id_pro
				
				WHERE id_tur = $fila[id_tur]
				";
				*/
				
				$sql = "
				
				SELECT
					
					sys_comprobante_detalle.importe,
					sys_comprobante_detalle.cantidad,
					pro.pro
					
				FROM sys_comprobante_detalle
				
				INNER JOIN sys_comprobante ON sys_comprobante.idcomprobante = sys_comprobante_detalle.idcomprobante 
				
				INNER JOIN pro ON pro.id_pro = sys_comprobante_detalle.idproducto
				
				WHERE sys_comprobante.id_turno = $fila[id_tur]
				
				";
				
				$con = mysql_query($query);
				echo '<table width="100%" style="text-align:center;">';
				echo "<tr><th>Cantidad</th><th>Tiempo</th><th>Valor</th><th>Subtotal</th><th>Producto</th></tr>";
				while($tab = mysql_fetch_array($con))
				{
					echo "<tr>";
					echo "<td>$tab[cantidad]</td>";
					
					/*
					if($tab['tie'] != null)
					{
						$date = new DateTime($tab['tie']);
						echo "<td>Hs. ".$date->format('H:i')."</td>";
					}
					else
					{
						
						echo "<td></td>";
					}
					*/
					echo "<td>Hs. ".$tiempo_hab."</td>";
					
					
					echo "<td>$tab[importe]</td>";
					$subtotal = $tab['importe'] * $tab['cantidad'];
					echo "<td>".$subtotal."</td>";
					echo "<td>$tab[pro]</td>";
					echo "</tr>";
				}
				echo "</table>";
				
				echo "</div></td></tr>";
			}
				
				echo "<tr>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td style='text-align:right'>Total: </td>";
				echo "<td>$ "."$totalValor</td>";
				echo "<td></td>";
				echo "<td></td>";
				
				echo "</tr>";
				
			echo "</table>";
			?>
			</form>
			<br>
			<form action="ficheroExcel.php" method="post" target="_blank" id="FormularioExportacion">
			<input value="Exportar Excel" type="submit" class="botonExcel" />
			<input type="hidden" id="datos_a_enviar" name="datos_a_enviar" />
			</form>
			<?
			
		}else{
			
			?>
			<br>
            <div style="color:#F00;">No hay datos para los campos solicitados.</div>
			<?
			
		}
	
	break;
	
	case 'pro':

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
			/*
			array(
				"campo" => "venta", 
				"tipo" => "bit", 
				"titulo" => "A la Venta", 
				"datos" => array("1"=>"SI", "0"=>"NO"),
				"valorPredefinido" => "1",
				"centrarColumna" => true,
				"hint" => "Indica si el Producto se puede vender"
			),
			array(
				"campo" => "materia", 
				"tipo" => "bit", 
				"titulo" => "Materia Prima", 
				"datos" => array("1"=>"SI", "0"=>"NO"),
				"valorPredefinido" => "1",
				"centrarColumna" => true,
				"hint" => "Indica si el Producto es materia prima"
			),
			*/
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
					
	break;

	case 'materia':

		$abm->tabla = "pro";
		$abm->registros_por_pagina = 15;
		$abm->textoTituloFormularioAgregar = "Agregar Materia Prima";
		$abm->textoTituloFormularioEdicion = "Editar Materia Prima";
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
		
		WHERE pro.bar = 1 AND materia = 1";
		
		$abm->adicionalesInsert = ", bar = 1, materia = 1, promo = 0, venta = 0 ";
		
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
			/*
			array(
				"campo" => "venta", 
				"tipo" => "bit", 
				"titulo" => "A la Venta", 
				"datos" => array("1"=>"SI", "0"=>"NO"),
				"valorPredefinido" => "1",
				"centrarColumna" => true,
				"hint" => "Indica si el Producto se puede vender"
			),
			array(
				"campo" => "materia", 
				"tipo" => "bit", 
				"titulo" => "Materia Prima", 
				"datos" => array("1"=>"SI", "0"=>"NO"),
				"valorPredefinido" => "1",
				"centrarColumna" => true,
				"hint" => "Indica si el Producto es materia prima"
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
			*/
		);
		
		$abm->generarAbm($g_sql, "Administrar Materias Prima");
					
	break;
	
	case 'promos':
		
		$abm->tabla = "pro";
		$abm->registros_por_pagina = 15;
		$abm->textoTituloFormularioAgregar = "Agregar Promos";
		$abm->textoTituloFormularioEdicion = "Editar Promos";
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
		
		WHERE pro.bar = 1 AND promo = 1 ";
		
		$abm->adicionalesInsert = ", bar = 1, sto = 0, promo = 1, materia = 0, venta = 1, elaborado = 0 ";
		
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
				"campo" => "id_pro", 
				"tipo" => "texto", 
				"titulo" => "Editar", 
				"customPrintListado" => "<a href='abm.php?fx=promos_e&id=%s'>Editar</a>",
				"noNuevo" => true,
				"noEditar" => true,
				"noMostrarEditar" => true
			)
			
		);
		
		$abm->generarAbm($g_sql, "Administrar Promos");
		
	break;
	
	case 'promos_e':
	
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
        
        <?
	
	
	break;
	
	case 'recetas':
		
		$abm->tabla = "pro";
		$abm->registros_por_pagina = 15;
		$abm->textoTituloFormularioAgregar = "Agregar Recetas";
		$abm->textoTituloFormularioEdicion = "Editar Recetas";
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
		
		WHERE pro.bar = 1 AND promo = 0 AND elaborado = 1";
		
		$abm->adicionalesInsert = ", bar = 1, sto = 0, promo = 0, materia = 0, venta = 1, elaborado = 1 ";
		
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
				"campo" => "id_pro", 
				"tipo" => "texto", 
				"titulo" => "Editar", 
				"customPrintListado" => "<a href='abm.php?fx=receta_e&id=%s'>Editar</a>",
				"noNuevo" => true,
				"noEditar" => true,
				"noMostrarEditar" => true
			)
			
		);
		
		$abm->generarAbm($g_sql, "Administrar Recetas");
		
	break;
	
	case 'receta_e':
	
		$id_receta = $_REQUEST['id'];
		
		$resultado = $db->query("SELECT pro FROM pro WHERE id_pro = '".$id_receta."'");
		while ($fila = $db->fetch_array($resultado)) {
			$nom_receta = $fila['pro'];
		}
		
		$abm->tabla = "pro_combinado";
		$abm->registros_por_pagina = 15;
		$abm->textoTituloFormularioAgregar = "Agregar Materia Prima a ".$nom_receta."";
		$abm->textoTituloFormularioEdicion = "Editar Materia Prima a ".$nom_receta."";
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
		
		WHERE pro_combinado.id_pro = ".$id_receta." AND pro_combinado.tipo = 1 ";
		
		$abm->adicionalesInsert = ", id_pro = ".$id_receta.", tipo = 1 ";
		
		$abm->campos = array(

			array(
				"campo" => "id_proc", 
				"tipo" => "dbCombo", 
				"sqlQuery" => "
					SELECT pro.id_pro, CONCAT(pro.pro, ' - ', uni.uni) As nom_pro FROM pro 
					
					INNER JOIN uni ON uni.id_uni = pro.id_uni
					
					WHERE pro.bar = 1 AND pro.materia = 1 ORDER BY pro
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
		
		$abm->generarAbm($g_sql, "Administrar Receta a ".$nom_receta."");
		
		?>
        	
            <a href="abm.php?fx=recetas">Volver</a>
        
        <?
	break;	
	
	case 'turnos':
	
	if($_POST){
	   $misDatosJSON = json_decode($_POST["datos"]);
	   
	   $when = "";
	   $when2 = "";
	   $when3 = "";
	   $when4 = "";
	   $in = "";
	   
	   for ($i = 0; $i < count($misDatosJSON); $i++) {
			$when .= " WHEN ".$misDatosJSON[$i][0]." THEN ".$misDatosJSON[$i][1]." ";
			$when2 .= " WHEN ".$misDatosJSON[$i][0]." THEN ".$misDatosJSON[$i][2]." ";
			$when3 .= " WHEN ".$misDatosJSON[$i][0]." THEN ".$misDatosJSON[$i][3]." ";
			
			$hora = $misDatosJSON[$i][4].":".$misDatosJSON[$i][5].":"."00";
			$when4 .= " WHEN ".$misDatosJSON[$i][0]." THEN '".$hora."' ";
			
			$in .= $misDatosJSON[$i][0];
			if($i != (count($misDatosJSON)-1))
			{
				$in .= ",";
			}
	   }
	   $consulta ="
					UPDATE pro SET dia = CASE id_pro ".
						$when." 
                        END,
								   hor = CASE id_pro ".
						$when2."
						END,
								   val = CASE id_pro ".
						$when3."
						END,
								   tie = CASE id_pro ".
						$when4."
						END						
						WHERE id_pro IN (".$in.")
					";
					
						$db->query($consulta);
	}
	
	$g_sql1 = "
	SELECT
		id_tip,
		tip
		FROM
		
		tip_hab
		
		WHERE
		act =1
	
	";
	$resultado = $db->query($g_sql1);
	?>
	<div>
	Tipo de Habitaci&oacute;n:
	<select id ="sel_hab" name="sel_hab">
	<?
	while ($fila = $db->fetch_array($resultado)) {
	?>
		  <option value="<?echo $fila['id_tip'];?> "><?echo $fila['tip'];?></option>	
	<?
	}	
	?>
	</select>
	<input type="button" value="Mostrar" onClick="mostrar_tabla()"/>
	</div>
	
	<?
	$index = -1;
	if (isset($_GET['index']))
	{
		$index= $_GET['index'];
	?>
	<div style="font-size: 14px;FONT-FAMILY: Verdana,Helvetica;line-height: 25px;FONT-WEIGHT: bold;text-align: left;color: #464646;margin-top:15px;">Administrar Turnos</div>
		<table id="tabla_hab" style="margin-top:15px">
		<thead>
		<th>Nombre</th>
		<th>Dia</th>
		<th>Horario</th>
		<th>Valor</th>
		<th>Tiempo</th>
		</thead>
		<tbody>
		</tbody>
		</table>
		<input id="boton_guardar" type="button" value="Guardar" onclick="guardar()" style="margin-top:10px;"/>
	
		<script>
		var lista_ids = new Array();
		var contador=0;
		<?
		echo "var tab=document.getElementById('sel_hab'); ";
		$var = 1;
		//echo "console.log('index: '+$index);";
		$g_sql2 = "
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
			
			WHERE pro.bar = 0 AND tip_hab.id_tip = ".$index."
			
			ORDER BY pro.dia, pro.hor, pro.promo_hab DESC
			";

		$resultado = $db->query($g_sql2);

		while ($fila = $db->fetch_array($resultado)) {
	
		echo "lista_ids[contador] = '$fila[id_pro]';";
		echo "contador++;";
		echo "var table = document.getElementById('tabla_hab');";
		echo "var rowCount = table.rows.length;";
		echo "var row = table.insertRow(rowCount);";
		echo "var celda1 = row.insertCell(0);";

		//celda1
		echo "celda1.innerHTML='$fila[pro]';";

		//celda 2 va con select
		echo "var celda2 = row.insertCell(1);";
		echo "var select = document.createElement('select');";
		
		//crea los 3 elementos del select dias
		echo "var option = document.createElement('option');";
		echo "option.setAttribute('value', '1');";
		echo "option.innerHTML = 'LUN A JUEV';";		
		echo "select.appendChild(option);";
		
		echo "option = document.createElement('option');";
		echo "option.setAttribute('value', '2');";
		echo "option.innerHTML = 'VIE Y SAB';";		
		echo "select.appendChild(option);";
		
		echo "option = document.createElement('option');";
		echo "option.setAttribute('value', '3');";
		echo "option.innerHTML = 'DOMINGO';";		
		echo "select.appendChild(option);";
		
		if($fila['dia_lis'] == 'LUN A JUEV')
		{
			echo "select.selectedIndex='0';";
		}
		else if($fila['dia_lis'] == 'VIE Y SAB')
		{
			echo "select.selectedIndex='1';";
		}
		else if($fila['dia_lis'] == 'DOMINGO')
		{
			echo "select.selectedIndex='2';";
		}
		
		echo "celda2.appendChild(select);";
		
		//celda 3 va con select
		echo "select = document.createElement('select');";
		echo "var celda3 = row.insertCell(2);";
		echo "option = document.createElement('option');";
		echo "option.setAttribute('value', '1');";
		echo "option.innerHTML = '8 A 14 HS';";		
		echo "select.appendChild(option);";
		
		echo "option = document.createElement('option');";
		echo "option.setAttribute('value', '2');";
		echo "option.innerHTML = '14 A 8 HS';";		
		echo "select.appendChild(option);";
		
		echo "option = document.createElement('option');";
		echo "option.setAttribute('value', '3');";
		echo "option.innerHTML = '00 A 8 HS';";		
		echo "select.appendChild(option);";
		if($fila['hor_lis'] == '8 A 14 HS')
		{
			echo "select.selectedIndex='0';";
		}
		else if($fila['hor_lis'] == '14 A 8 HS')
		{
			echo "select.selectedIndex='1';";
		}
		else if($fila['hor_lis'] == '00 A 8 HS')
		{
			echo "select.selectedIndex='2';";
		}
		echo "celda3.appendChild(select);";

		echo "var celda4 = row.insertCell(3);";
		
		//celda 4 va con un input y text valor anterior
               
		echo "var elemento = document.createElement('input');";
		echo "elemento.type = 'number';";
		echo "elemento.value = '$fila[val]';";
		echo "celda4.innerHTML= '$';";
		echo "celda4.appendChild(elemento);";
		
		$tiempo = explode(":",$fila['tie']);
		
		//celda 5 va con 2 select
		echo "select = document.createElement('select');";
		echo "var celda5 = row.insertCell(4);";		
		
		for ($i = 1; $i <= 12; $i++) {
			echo "option = document.createElement('option');";
			echo "option.setAttribute('value', '$i');";
			echo "option.innerHTML = '$i';";		
			echo "select.appendChild(option);";
		}
		
		echo "select.selectedIndex='".($tiempo[0]-1)."';";
		echo "celda5.appendChild(select);";
		
		echo "var puntos=document.createTextNode(':');";
		echo "celda5.appendChild(puntos);";
		
		echo "select = document.createElement('select');";
		echo "option = document.createElement('option');";
		echo "option.setAttribute('value', '0');";
		echo "option.innerHTML = '00';";		
		echo "select.appendChild(option);";
		echo "option = document.createElement('option');";
		echo "option.setAttribute('value', '15');";
		echo "option.innerHTML = '15';";		
		echo "select.appendChild(option);";
		echo "option = document.createElement('option');";
		echo "option.setAttribute('value', '30');";
		echo "option.innerHTML = '30';";		
		echo "select.appendChild(option);";
		echo "option = document.createElement('option');";
		echo "option.setAttribute('value', '45');";
		echo "option.innerHTML = '45';";		
		echo "select.appendChild(option);";
		echo "select.selectedIndex='".($tiempo[1]/15)."';";
		echo "celda5.appendChild(select);";
		
		
	}
	?>

	</script>
	<?
	}
	break;

	case 'feriados':
		
		?>
		
		<link href="../css/cupertino/jquery-ui-1.10.3.custom.css" rel="stylesheet">
		<script src="../js/jquery-1.9.1.js"></script>
		<script src="../js/jquery-ui-1.10.3.custom.js"></script>

		<?
		
		$abm->tabla = "feriados";
		$abm->registros_por_pagina = 15;
		$abm->textoTituloFormularioAgregar = "Agregar Feriado";
		$abm->textoTituloFormularioEdicion = "Editar Feriado";
		$abm->mostrarBorrar = true;
		$abm->campoId = "id_fer";
		
		$g_sql = "SELECT *, DATE_FORMAT(fec, '%d-%m-%Y') AS fecha FROM feriados "; 
		
		$abm->campos = array(
			array(
				"campo" => "fecha", 
				"tipo" => "texto", 
				"titulo" => "Feriado", 
				"customPrintListado" => "%s",
				"noNuevo" => true,
				"noEditar" => true,
				"noMostrarEditar" => true
			),
			array(
				"campo" => "fec", 
				"tipo" => "texto", 
				"titulo" => "Feriado",
				"customPrintListado" => "%s",
				"noListar" => true
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
	
		$abm->generarAbm($g_sql, "Administrar Feriados");
		
		?>
        
		<script>
		$(function() {
			
			$("#fec").datepicker({ dateFormat: "yy-mm-dd" });
			
		});
		</script>
        <?
		
	break;
	
	case 'precios':
	?>
		<select id ="sel_dia_precios" name="sel_dia_precios">
			<option value="1">Lunes a Jueves 08 a 20 hs</option>
			<option value="2">Lunes a Jueves 20 a 08 hs</option>
			<option value="3">Viernes a Sabado 08 a 20 hs</option>
			<option value="4">Viernes a Sabado 20 a 08 hs</option>
			<option value="5">Domingos</option>
		</select>
		<input type="submit" value="Mostrar" onClick="mostrar_precios_hab()"/>
		
		<?
		
		$index = -1;
		if (isset($_GET['index']))
		{
			$index= $_GET['index'];
		?>
		<table id="tabla_precios" style="margin-top:15px">
		<thead>
		<th></th>
		<th>Original</th>
		<th></th>
		<th>Promo</th>
		<th>Habitaciones</th>
		<th>Categoria</th>
		</thead>
		<tbody>
		</tbody>
		</table>
		<?
		}
		
		$g_sql2 = "
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
			
			WHERE pro.bar = 0 AND tip_hab.id_tip = ".$index;
			/*	
		$g_sql2 = "
		
		SELECT * pro
		WHERE dia=1 AND hor 
		
		";

		$resultado = $db->query($g_sql2);	*/
		
		?>
	<?
	break;

	case 'usuarios':
		
		
		$abm->tabla = "sys_usuario";
		$abm->registros_por_pagina = 15;
		$abm->textoTituloFormularioAgregar = "Agregar Usuario";
		$abm->textoTituloFormularioEdicion = "Editar Usuario";
		$abm->mostrarBorrar = false;
		$abm->campoId = "id_usuario";
		
		$g_sql = 
		"SELECT sys_usuario.*, sys_tipo_usuario.tipo FROM sys_usuario
		INNER JOIN sys_tipo_usuario ON sys_tipo_usuario.id_tipo = sys_usuario.id_tipo
		"; 
		
		$abm->campos = array(
		
			array(
				"campo" => "mail", 
				"tipo" => "texto", 
				"titulo" => "Mail", 
				"customPrintListado" => "%s"
			),
			array(
				"campo" => "usuario", 
				"tipo" => "texto", 
				"titulo" => "Usuario", 
				"customPrintListado" => "%s"
			),
			array(
				"campo" => "clave", 
				"tipo" => "texto", 
				"titulo" => "Clave", 
				"customPrintListado" => "%s",
			),
			
			array(
				"campo" => "id_tipo", 
				"tipo" => "dbCombo", 
				"sqlQuery" => "SELECT id_tipo, tipo FROM sys_tipo_usuario WHERE activo = 1 ORDER BY id_tipo", 
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
	
		$abm->generarAbm($g_sql, "Administrar Usuarios");
		
	break;
	
	case 'tarjetas':
		
		$abm->tabla = "sys_tarjetas";
		$abm->registros_por_pagina = 15;
		$abm->textoTituloFormularioAgregar = "Agregar Tarjeta";
		$abm->textoTituloFormularioEdicion = "Editar Tarjeta";
		$abm->mostrarBorrar = false;
		$abm->campoId = "id_tarjeta";
		
		$g_sql = ""; 
		
		$abm->campos = array(
		
			array(
				"campo" => "codigo", 
				"tipo" => "texto", 
				"titulo" => "Codigo de Barra", 
				"customPrintListado" => "%s"
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
	
		$abm->generarAbm($g_sql, "Administrar Tarjetas");
		
	break;
	
}


?>
<script>

function mostrar_precios_hab()
{
	window.location = "?fx=precios&index="+ document.getElementById('sel_dia_precios').value;
}
function mostrar_tabla()
{
	window.location = "?fx=turnos&index="+ document.getElementById('sel_hab').value;
}

function guardar()
{
var arrayDatos = new Array();
var contadorGrande = 0;

var table = document.getElementById('tabla_hab');

	var sel = table.getElementsByTagName('select');
	var inputs = table.getElementsByTagName('input');
		for (var r = 1, n = table.rows.length; r < n; r++) {
			arrayDatos[contadorGrande] = new Array();
			arrayDatos[contadorGrande][0] = lista_ids[contadorGrande];
			arrayDatos[contadorGrande][1] = sel[(r-1)*4].options[sel[(r-1)*4].selectedIndex].value;
			arrayDatos[contadorGrande][2] = sel[(r-1)*4+1].options[sel[(r-1)*4+1].selectedIndex].value;
			arrayDatos[contadorGrande][3] = inputs[contadorGrande].value;
			arrayDatos[contadorGrande][4] = sel[(r-1)*4+2].options[sel[(r-1)*4+2].selectedIndex].value;
			arrayDatos[contadorGrande][5] = sel[(r-1)*4+3].options[sel[(r-1)*4+3].selectedIndex].value;
			contadorGrande++;
		}

	var miJSON = JSON.encode(arrayDatos);
	var miAjax = new Request({
	   url: "abm.php?fx=turnos",
	   data: "datos=" + miJSON,
	   onSuccess: function(textoRespuesta){
		  alert("se enviaron los datos");
	   },
	   onFailure: function(){
		  alert("no se enviaron los datos");
	   }
	})
	miAjax.send();
	//console.log("array: "+lista_ids.length);
}
</script>

</body>
</html>