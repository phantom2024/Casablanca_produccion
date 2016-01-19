<?
require_once("boot.php");
$hab = $_POST['hab'];
$estado = $_POST['estado'];

$sql = "
UPDATE tur SET chequeo = ".$estado."
WHERE est_tur = 1
AND nro = ".$hab;

mysql_query($sql, $pconnect);
//echo "hab: ".$hab."  - estado: ".$estado;
?>