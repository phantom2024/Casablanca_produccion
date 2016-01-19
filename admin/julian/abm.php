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
	</style>

</head>
<body>

<?
require("comun/class_db.php");
require("comun/class_abm.php");
require("comun/class_paginado.php");
require("comun/class_orderby.php");

//conexión a la bd
$db = new class_db("192.168.7.118", "casa_blanca", "Lf43Zf9YcsMtR2nM", "casa_blanca");
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
			tip_hab.tip AS tip_m
			
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
				"campo" => "dia",
				"tipo" => "combo",
				"titulo" => "Dia",
				"datos" => array(1=>"LUN A JUEV", 2=>"VIE Y SAB", 3=>"DOMINGO"),
			),
			array(
				"campo" => "hor", 
				"tipo" => "combo", 
				"titulo" => "Horario", 
				"datos" => array(1=>"8 A 14 HS", 2=>"14 A 8 HS", 3=>"00 A 8 HS"),
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

	case 'pro':

		$abm->tabla = "pro";
		$abm->registros_por_pagina = 15;
		$abm->textoTituloFormularioAgregar = "Agregar Producto";
		$abm->textoTituloFormularioEdicion = "Editar Producto";
		$abm->mostrarBorrar = false;
		$abm->campoId = "id_pro";
		
		$g_sql = "SELECT * FROM pro WHERE pro.bar = 1";
		
		$abm->adicionalesInsert = ", bar=1 ";
		
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
				"titulo" => "Stock", 
				"maxLen" => 30,
				"customPrintListado" => "%s",
				"requerido" => true
			)

		);
		
		$abm->generarAbm($g_sql, "Administrar Turnos");
					
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
	
	case 'dat':
		?>
		
		<link href="../css/cupertino/jquery-ui-1.10.3.custom.css" rel="stylesheet">
		<script src="../js/jquery-1.9.1.js"></script>
		<script src="../js/jquery-ui-1.10.3.custom.js"></script>
		<script>
		$(function() {
			
			$( "#fec" ).datepicker();
			
		});
		$(document).ready(function() {
			
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
		<th>Estado</th>
		<th>Producto</th>
		</tr>
		<tr>
		<td><input type="text" id="fec" name="fec" value="<? echo $_GET['fec']; ?>"></td>
		<td><select id ="sel" name="sel">
		  <option value="sel_todos">Todos</option>
		  <option value="sel_cerrado">Cerrado</option>
		  <option value="sel_abierto">Abierto</option>
		</select></td>
		<td>
		<select id ="prod" name="prod">
		<option value="prod_todos">TODOS</option>
		<?
			$consulta = "SELECT pro FROM pro";
			$resultado = mysql_query($consulta);
			echo "<br/>";
			while ($fila = mysql_fetch_array($resultado)) {
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
		
			if($_GET['fec'])
			{
			
				$date = new DateTime($_GET['fec']);
				$fec = $date->format('Y-m-d');	

				$g_sql = "
				
				SELECT tur.id_tur,est_tur,fec,fec_in,fec_out,tur_det.val,tur_det.tie,pro.pro,tur_det.can FROM tur 
				
				INNER JOIN tur_det ON tur_det.id_tur = tur.id_tur 
				INNER JOIN pro ON pro.id_pro = tur_det.id_pro

				
				WHERE date(tur.fec) = '".$fec."'
				
				".$estado.$prod."
				GROUP BY id_tur
				";
				
				$query1= "
				SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(tur_det.tie))) FROM tur_det
				
				INNER JOIN tur ON tur_det.id_tur = tur.id_tur
				INNER JOIN pro ON pro.id_pro = tur_det.id_pro
				
				WHERE date(tur.fec) = '".$fec."'
				".$estado.$prod;
				
				$consult = mysql_query($query1) ;
				
				//echo $g_sql;
				$resultado = $db->query($g_sql);
				//$datos = mysql_fetch_array($resultado);
				//var_dump($datos);		
			}
			else if($_GET['fec']=='')
			{
				$g_sql ="SELECT tur.id_tur,est_tur,fec,fec_in,fec_out,tur_det.val,tur_det.tie,pro.pro,tur_det.can FROM tur 
				
				INNER JOIN tur_det ON tur_det.id_tur = tur.id_tur 
				INNER JOIN pro ON pro.id_pro = tur_det.id_pro
				".$estado.$prod.
				" GROUP BY id_tur ORDER BY fec DESC LIMIT 10";
				
				$query1= "
				
				SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(tur_det.tie))) FROM tur_det
				
				INNER JOIN tur ON tur_det.id_tur = tur.id_tur
				INNER JOIN pro ON pro.id_pro = tur_det.id_pro
				
				".$estado.$prod." ORDER BY fec DESC LIMIT 10";
				
				$consult = mysql_query($query1) ;
				$resultado = $db->query($g_sql);
			}
			echo "<br/>";
			echo '<table border="1" id="Exportar_a_Excel" width="100%" cellpadding="2" cellspacing="2"><tr>';	
			echo "<th>Id Turno</th>";
			echo "<th>Estado</th>";
			echo "<th>Fecha</th>";
			echo "<th>Fecha entrada</th>";
			echo "<th>Fecha salida</th>";
			echo "<th>Valor</th>";
			echo "<th>Ver</th>";
			//echo "<th>Tiempo</th>";
			//echo "<th>Producto</th></tr>";
			
			$totalValor;
			$totalTiempo;
			
			while ($fila = mysql_fetch_assoc($resultado)) 
			{
				//print_r($fila);
				echo "<tr class='alternar'>";
				echo "<td>$fila[id_tur]</td>";
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
				
				
				$valor = 0;
								
				$query = "
				SELECT tur_det.val,tur_det.tie,tur_det.can,pro.pro FROM tur_det
				INNER JOIN pro ON tur_det.id_pro = pro.id_pro
				WHERE id_tur = $fila[id_tur]
				";
								
				$con = mysql_query($query);
				while($tab = mysql_fetch_array($con))
				{
					$valor += $tab['val']*$tab['can']; 
				}
				$totalValor += $valor;
				
				echo "<td>$ "."$valor</td>";
				echo "<td><center><a class='ver_tur' href='#".$fila['id_tur']."'>Ver</a></center></td>";

				echo "</tr>";
				echo '<tr><td colspan=8><div class="tab_oc" id="tab_'.$fila['id_tur'].'" style="display:none;">';
				/*
				$query = "
				SELECT val,tie,can FROM tur_det
				WHERE id_tur = $fila[id_tur]
				";*/
				
				$query = "
				SELECT tur_det.val,tur_det.tie,tur_det.can,pro.pro FROM tur_det
				
				INNER JOIN pro ON tur_det.id_pro = pro.id_pro
				
				WHERE id_tur = $fila[id_tur]
				";
				
				
				$con = mysql_query($query);
				echo '<table width="100%" style="text-align:center;">';
				echo "<tr><th>Cantidad</th><th>Tiempo</th><th>Valor</th><th>Subtotal</th><th>Producto</th></tr>";
				while($tab = mysql_fetch_array($con))
				{
					echo "<tr>";
					echo "<td>$tab[can]</td>";
					
					if($tab['tie'] != null)
					{
						$date = new DateTime($tab['tie']);
						echo "<td>Hs. ".$date->format('H:i')."</td>";
					}
					else
					{
						
						echo "<td></td>";
					}
					echo "<td>$tab[val]</td>";
					$subtotal = $tab['val'] * $tab['can'];
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
				/*
				if($sumaTiempo=mysql_fetch_array($consult))
				{
				$date = new DateTime($sumaTiempo[0]);
				echo "<td>Hs. ".$date->format('H:i')."</td>";
				}*/
				//echo "<td></td>";				
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
	break;	
}


?>

</body>
</html>