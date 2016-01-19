<?
require("comun/class_db.php");
require("comun/class_abm.php");
require("comun/class_paginado.php");
require("comun/class_orderby.php");

require_once("../config_db.php");

$db = new class_db(VAR_HOST, VAR_USERDB, VAR_PASSDB, VAR_DB);
$db->mostrarErrores = true;
$db->connect();


$g_sql = "
SELECT * FROM pro WHERE bar=0
";

$resultado = $db->query($g_sql);

while ($fila = mysql_fetch_assoc($resultado)) 
{
	//echo $fila['id_pro'].": ".$fila['pro']." --------- ";
	if(strpos($fila['pro'],'PROMO'))
	{
		//echo "PROMO<BR/>";
		$sql="UPDATE pro SET promo_hab = 1, promo = 0 where id_pro = ".$fila['id_pro'];
		$res = $db->query($sql);
	}
	else if(strpos($fila['pro'],'ORIGINAL'))
	{
		//echo "ORIGINAL<BR/>";
		$sql="UPDATE pro SET promo_hab = 0, promo = 0 where id_pro = ".$fila['id_pro'];
		$res = $db->query($sql);
	}
	else
	{
		//echo "NADA<br/>";
		$sql="UPDATE pro SET promo_hab = 0, promo = 0 where id_pro = ".$fila['id_pro'];
		$res = $db->query($sql);
	}
}
?>