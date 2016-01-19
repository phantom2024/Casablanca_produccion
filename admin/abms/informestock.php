
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/cupertino/jquery-ui-1.10.3.custom.css" rel="stylesheet">

<script src="../js/jquery-1.9.1.js"></script>
<script src="../js/jquery-ui-1.10.3.custom.js"></script>
<script>

$(function() {

});
</script>
<?php

?>
</head>
<body>

<table id="tabla">
<thead>
	<th>Producto</th>
	<th>Cantidad</th>
</thead>
	<tbody>

	<?
		//selecciona los usuarios de id_tipo 9 q son los PROOVEDORES
	$sql = "
		select pro, sum(IF(sys_comprobante.idcomp_tipo<>1,sys_comprobante_detalle.cantidad,sys_comprobante_detalle.cantidad*-1)) as total
		from sys_comprobante_detalle 
		INNER JOIN pro ON pro.id_pro=sys_comprobante_detalle.idproducto
		INNER JOIN sys_comprobante ON sys_comprobante_detalle.idcomprobante=sys_comprobante.idcomprobante
		where idcomp_tipo in (1,3,11,15) and tip=0
		group by sys_comprobante_detalle.idproducto
		ORDER BY pro.pro;
	";
	$result = mysql_query($sql);
	while($fila = mysql_fetch_assoc($result)){		
		echo "<tr>";
			echo "<td>".$fila["pro"]."</td>";
			echo "<td>".$fila["total"]."</td>";
		echo "</tr>";		
	}				
	?>

	</tbody>
</table>

</body>
</html>