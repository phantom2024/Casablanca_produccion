<?php

if($_GET['id'])
{
	echo "Ingrese codigo de tarjeta: <input type='text' id='codigo_tarjeta' value='".$_GET['id']."' />";
}
else
{
	echo "Ingrese codigo de tarjeta: <input type='text' id='codigo_tarjeta' />";
}

echo "<br/>";
echo "<input type='button' id='btn_buscar' value='Buscar' onclick='enviar_codigo();' />";
if($_GET['id'])
{

	if(existeTarjeta($_GET['id'],$db))
	{
		$sql = "
		SELECT SUM(puntos) as puntos 
		FROM sys_tarjetas
		INNER JOIN sys_puntos ON sys_puntos.codigo = sys_tarjetas.codigo
		WHERE sys_tarjetas.codigo = ".$_GET['id']."
		";
		$resultado = $db->query($sql);
		echo "<br/>";
		while ($fila = $db->fetch_array($resultado)) {
			if($fila['puntos'] != ''){
				echo "<br/>";
				echo "Los puntos para el codigo ".$_GET['id']." son ".$fila['puntos'];
				echo "<br/>";
				echo "<br/>";
				echo "Agregar: <input type='text' id='agregar_puntos' value='0'/>";
				echo "<input type='button' id='btn_agregar_puntos' onclick='ag_puntos(".$_GET['id'].");' value='Agregar'/>";
			}else{
				echo "<br/>";
				echo "Los puntos para el codigo ".$_GET['id']." son 0";
				echo "<br/>";
				echo "<br/>";
				echo "Agregar: <input type='text' id='agregar_puntos' value='0'/>";
				echo "<input type='button' id='btn_agregar_puntos' onclick='ag_puntos(".$_GET['id'].");' value='Agregar'/>";
			}
		}
	}
	else
	{
		echo "<h3>No existe la tarjeta</h3>";
	}
	
	
	
	if($_GET['agregar'])
	{
		$sql ="
				INSERT INTO sys_puntos (
				fecha,
				codigo,
				puntos,
				idcomprobante
				)VALUES(
				NOW(),
				".$_GET['id'].",
				".$_GET['agregar'].",
				0
				)
		";
					
		$db->query($sql);
		
		$sql = "
	SELECT SUM(puntos) as puntos 
	FROM sys_tarjetas
	INNER JOIN sys_puntos ON sys_puntos.codigo = sys_tarjetas.codigo
	WHERE sys_tarjetas.codigo = ".$_GET['id']."
	";
	 $resultado = $db->query($sql);
	echo "<br/>";
		while ($fila = $db->fetch_array($resultado)) {
			echo "<br/>";
			echo "Los puntos para el codigo ".$_GET['id']." son <b>".$fila['puntos']."</b>";
		}
	}
}

function existeTarjeta($nroTarjeta,$db)
{
	$sql = "
		SELECT * 
		FROM sys_tarjetas
		WHERE sys_tarjetas.codigo = ".$_GET['id']."
	";
	$resultado = $db->query($sql);
	$retorno=false;
	while ($fila = $db->fetch_array($resultado)) {
		// echo "aaa</br>";
		$retorno = true;
	}
	
	return $retorno;
}

?>
<script>
function enviar_codigo()
{
	window.location = "?fx=puntostarjetas&id="+document.getElementById('codigo_tarjeta').value;
}
function ag_puntos(id)
{
	window.location = "?fx=puntostarjetas&id="+id+"&agregar="+document.getElementById('agregar_puntos').value;
}

</script>