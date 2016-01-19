<script>
//script nuevo julian bou 14/02
var tagsAutocomplete = new Array();

function agregar()
{

	var producto = document.getElementById("tags");
	var cantidad = document.getElementById("pro_can");
	var id = document.getElementById("id_producto");
	var precio = document.getElementById("precio_producto");
	
	$("#lista_productos").append('<div class="lista"><span>'+cantidad.value+" - "+producto.value+" - $"+cantidad.value * precio.value+'</span><a href="#" class="eli_itm_bar">X</a><input id="pro_bar" type="hidden" name="pro_bar[]" value="'+id.value+'"><input id="probar_v" type="hidden" name="probar_v[]" value="'+precio.value+'"><input id="pro_bar_can" type="hidden" name="pro_bar_can[]" value="'+cantidad.value+'"></div>');	
	
	/*
	var producto = document.getElementById("tags");
	var cantidad = document.getElementById("pro_can");
	var id = document.getElementById("id_producto");
	var precio = document.getElementById("precio_producto");
	
	//console.log(producto.value);
	//console.log(cantidad.value);
	//console.log(id.value);
	//console.log(precio.value);
	
	var lista = document.getElementById("lista_productos");
	var parrafo = document.createElement('p');
    var texto = document.createTextNode(cantidad.value+"  "+producto.value);
    parrafo.appendChild(texto);
	lista.appendChild(parrafo);
	
	var inputPro = document.createElement('input');
	inputPro.type = "hidden";
	inputPro.id = "pro_bar";
	inputPro.name = "pro_bar[]";
	inputPro.value = id.value;
	lista.appendChild(inputPro);				
				
	var inputVal = document.createElement('input');
	inputVal.type = "hidden";
	inputVal.id = "probar_v";
	inputVal.name = "probar_v[]";
	inputVal.value = precio.value;
	lista.appendChild(inputVal);
	
	var inputCant = document.createElement('input');
	inputCant.type = "hidden";
	inputCant.id = "pro_bar_can";
	inputCant.name = "pro_bar_can[]";
	inputCant.value = cantidad.value;
	lista.appendChild(inputCant);
	*/
}

var contador = 0;
  $(function() {
    $( "#tags" ).autocomplete({
      source: tagsAutocomplete,
	  change: function (event, ui) 
			{
				if(ui.item != null)
				{
					var id_producto=document.getElementById('id_producto');
					id_producto.value=ui.item.id;
					var precio_producto=document.getElementById('precio_producto');
					precio_producto.value=ui.item.precio;				
				}
				else
				{
					var id_producto=document.getElementById('id_producto');
					id_producto.value=0;
					var precio_producto=document.getElementById('precio_producto');
					precio_producto.value=0;	
				}
				/*
				var probar=document.getElementById('pro_bar');
				probar.value=ui.item.id;
				
				var probar_v=document.getElementById('probar_v');
				probar_v.value=ui.item.precio;
				*/
			}
    });
  });
 </script>

<?php

require_once("boot.php");

if($_POST['b']){
    
	echo "<br />Agregar:<br />";
	
	$sql = "SELECT id_pro, pro, val FROM pro WHERE bar = 1 AND venta = 1 OR promo = 1 OR elaborado = 1";
	$result = mysql_query($sql, $pconnect);
	
	//echo '<input type="hidden" id="probar_v" name="probar_v">';	
	
	//echo '<input type="hidden" id="pro_bar" name="pro_bar">';
	
	echo '<input type="hidden" id="id_producto" name="pro_bar">';//este lo usa para guardar el id del producto seleccionado
	echo '<input type="hidden" id="precio_producto" name="precio_producto">';//este lo usa para guardar el precio del producto seleccionado
	echo "<input id='tags' type='text' style='width:350px'>";
	while($fila = mysql_fetch_assoc($result)){
		
		$datos[] = $fila;
		
		echo "<script>
		tagsAutocomplete[contador] = {'label':'$fila[pro] - $$fila[val]', 'value':'$fila[pro] - $$fila[val]', 'id':'$fila[id_pro]', 'precio':'$fila[val]'};
		contador++;
		</script>";
		
	}
	
	echo ' <input type="text" id="pro_can" name="pro_can" maxlength="3" style="width:50px;" value="1" /><input type="button" value="+" onclick="agregar();">';
	echo '</br>';
	echo '<div id="lista_productos"></div>';
	
	unset($result);
	mysql_free_result($pconnect);

	exit;
	
}

if($_POST['gb']){
	
	$id_hab = $_POST['id_hab'];
	//echo "id_hab: ".$id_hab;
	//echo "<br/>";
	$pro_bar = json_decode($_POST['pro_bar']);
	//echo "pro_bar: ".$pro_bar;
	//echo "<br/>";
	$pro_can = json_decode($_POST['pro_can']);
	//echo "pro_can: ".$pro_can;
	//echo "<br/>";
	$probar_v = json_decode($_POST['probar_v']);
	//echo "probar_v: ".$probar_v;
	//echo "<br/>";
	
	//echo "tamanio: ".count($pro_bar);
	/*
	if($pro_can <= 0){
		echo "error_can";
	}*/
	for($i=0; $i < count($pro_bar); $i++)
	{
		//echo "VUELTA: ".$i;
		$sql = "
		SELECT sys_comprobante.idcomprobante FROM tur 
		INNER JOIN sys_comprobante ON sys_comprobante.id_turno = tur.id_tur
		WHERE est_tur = 1 AND nro = '".$id_hab."'
		";
		$result = mysql_query($sql, $pconnect);
		if(mysql_affected_rows($pconnect) > 0){
			
			while($fila = mysql_fetch_assoc($result)){
				$idcomprobante = $fila['idcomprobante'];
			}
			if($pro_bar[$i] != 0){
				
				$sql = "
				INSERT INTO sys_comprobante_detalle (
				iddetalle_chown,
				idcomprobante,
				idproducto,
				cantidad,
				importe,
				detalle,
				num_serie,
				num_inventario,
				fecha_garantia,
				idestado,
				mostrar,
				id_usuario_carga
				) VALUES (
				0, ".$idcomprobante.", '".$pro_bar[$i]."', '".$pro_can[$i]."', '".$probar_v[$i]."', '', '', '', '', '', '1', '".$_SESSION['usuario']['id_usuario']."')";
				mysql_query($sql);
				
				$iddetalle_chown = mysql_insert_id();
				
				busca_pro_com($idcomprobante, $pro_bar[$i], $pro_can[$i], $iddetalle_chown);
				
			}

		}else{
			echo "error";
		}
	}

	unset($result);
	mysql_free_result($pconnect);

	exit;
}