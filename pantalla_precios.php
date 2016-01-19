<?php
require_once("boot.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Casablanca</title>
	
<link href="css/cupertino/jquery-ui-1.10.3.custom.css" rel="stylesheet">
<link href="css/style.css" rel="stylesheet">

<style>
	
	body{
		background-color:#523230;
		padding:0;
		margin:0;
	}
	#contenedor{
		width:100%;
		position:relative;
	}
	.letra{
		float:left;
		
		padding:5px;
		margin:5px;
		
		text-transform: capitalize;
		font-family:"Times New Roman";
		font-size:100%;
		
		font-weight:bold;
		
		border-bottom:#000 2px solid;
	}
	.titulo{
		width:40%;
		color:#FFF;
	}
	.sinpromo{
		width:25%;
		color:#FFCC01;
	}
	.promo{
		width:25%;
		color:#FF9901;
	}
</style>

<script src="js/jquery-1.9.1.js"></script>
<script src="js/jquery-ui-1.10.3.custom.js"></script>

</head>
<body>
<div>
	<div style="float:left;">
        <img src="img/logo_cb.png" width="100%" />
    </div>
    <div style="float:left;">
        <!--<img src="img/hab_cb.png" width="100%" />-->
    </div>
    <div style="clear:both;"></div>
</div>
<?

	$dia_producto = traer_dia_producto();
	$hora_producto = traer_hora_producto();

	$sql = "
	SELECT pro.tip, tip_hab.tip, pro.pro, pro.val, pro.tie FROM pro 
	
	INNER JOIN tip_hab ON tip_hab.id_tip = pro.tip
	
	WHERE pro.bar = 0 AND pro.dia = ".$dia_producto." AND pro.hor = ".$hora_producto."
	";
	$result = mysql_query($sql, $pconnect);
	//echo "mysql_error: ".mysql_error();
	
	?>
    <div id="contenedor">
    <?
	$contar = 0;
	$salto = 0;
	while($fila = mysql_fetch_assoc($result)){
		
		$contar++;
		$salto++;
		//printr($fila);
		
		$tiempo = explode(":", $fila['tie']);
		$fila['tie'] = $tiempo[0].":".$tiempo[1]." Hs."
		
		?>
        	
            <?
			if($salto == 1){

				?>
                <div class="letra titulo">
                
                	<? echo strtolower($fila['tip']); ?>
                
                </div>
                
                <div class="letra sinpromo">
                    
                    <? echo $fila['tie']; ?>
					$ <? echo $fila['val']; ?>
                
                </div>
                <?
				
			}
			if($salto == 2){
				?>
				<div class="letra promo">
						
					<? echo $fila['tie']; ?>
					$ <? echo $fila['val']; ?>
						
				</div>
				<?
            }
            ?>
            
        <?
		
		if($salto == 2){
			$salto = 0;
		}
		if($contar%2==0){
			?>
            <div style="clear:both;"></div>
            <?
		}

	}
	mysql_free_result($result);
	
	?>
    </div>
    <?	
	
?>

</body>
</html>