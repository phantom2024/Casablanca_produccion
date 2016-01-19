<?
require_once("boot.php");

if($_POST['iddetalle']){
		
	// debemos eliminar todo lo de una promo o receta o producto final
	
	//$sql = "DELETE FROM sys_comprobante_detalle WHERE iddetalle = '".$_POST['iddetalle']."'";
	//echo "ok";
	
	//echo "iddetalle: ".$_POST['iddetalle'];
	
	/*
	$sql = "SELECT idcomprobante, idproducto, cantidad FROM sys_comprobante_detalle WHERE iddetalle = '".$_POST['iddetalle']."'";
	$result = mysql_query($sql);
	while($fila = mysql_fetch_assoc($result)){
		$idcomprobante = $fila['idcomprobante'];
		$idproducto = $fila['idproducto'];
		$cantidad = $fila['cantidad'];
	}
	
	function busca_pro_com_eli($idcomp, $id_pro_ingresado){
		
		$sql = "SELECT id_pro, promo, elaborado FROM pro WHERE id_pro = ".(int)$id_pro_ingresado."";
		$result1 = mysql_query($sql);
		while($fila_pro = mysql_fetch_assoc($result1)){
			
			if($fila_pro['promo'] == 1 || $fila_pro['elaborado'] == 1){
				
				$sql = "SELECT * FROM pro_combinado WHERE id_pro = '".$fila_pro['id_pro']."'";
				$result2 = mysql_query($sql);			
				while($fila_rec = mysql_fetch_assoc($result2)){
					
					// trae el precio del producto						
					$sql = "SELECT val FROM pro WHERE id_pro = '".$fila_rec['id_proc']."'";
					$result3 = mysql_query($sql);			
					while($fila_pre = mysql_fetch_assoc($result3)){
						$precio = $fila_pre['val'];
					}
					
					$precio_final = $precio * $fila_rec['can'];
					
					$cantidad = (float)$fila_rec['can'] * (int)$cantidad_pro;
					
					$sql = "DELETE FROM sys_comprobante_detalle WHERE mostrar = 0 AND idcomprobante = '".$idcomp."' AND idproducto = '".$fila_rec['id_proc']."' LIMIT 1";
					mysql_query($sql);
					 
					busca_pro_com_eli($idcomp, $fila_rec['id_proc']);
					
				}
				
			}
	
		}
		
	}
	
	busca_pro_com_eli($idcomprobante, $idproducto, $cantidad);
	
	$sql = "DELETE FROM sys_comprobante_detalle WHERE mostrar = 1 AND idcomprobante = '".$idcomprobante."' AND idproducto = '".$idproducto."' LIMIT 1";
	mysql_query($sql);
	*/
	
	$sql = "DELETE FROM sys_comprobante_detalle WHERE iddetalle_chown = '".$_POST['iddetalle']."'";
	$result = mysql_query($sql);
	
	$sql = "DELETE FROM sys_comprobante_detalle WHERE iddetalle = '".$_POST['iddetalle']."'";
	$result = mysql_query($sql);
	
	exit;
}

?>