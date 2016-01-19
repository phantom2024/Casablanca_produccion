<?
include("mysql.class.php");
$db= new MySqlClass();
$db->conectar("casablancanew");


$dia=date('w');
$hora=date('H');

if ($hora>=8 and $hora<14){
	$hora=1;
}

if ($hora>=14 and $hora<=23){
	$hora=2;
}

if ($dia>=1 and $dia<=4){
	$dia=1;
}

if ($dia==6 or $dia==5){
	if ($hora>=8 and $hora<14){
		$hora=1;
	}
	if ($hora>=14 and $hora<=23){
		$hora=2;
	}
	$dia=2;
}

if ($dia==0){
	if ($hora>=8 and $hora<14){
		$hora=1;
	}
	if ($hora>=14 and $hora<=23){
		$hora=2;
	}
	$dia=3;
}



/*
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
*/

$sql="select *,concat(hour(tie),'.',minute(tie)) as tiempo from pro where dia=$dia and hor=$hora";
$hab=$db->extraer($sql);
$i=1;

foreach($hab as $a){
	
	if ($a['promo_hab']==1){
		if ($i<>1){echo '&';}
		echo 'varPromo_'.$a['tip'].'='.$a['tiempo']."hs $".$a['val'];
	}else{
		if ($i<>1){echo '&';}
		echo 'varOriginal_'.$a['tip'].'='.$a['tiempo']."hs $".$a['val'];
	}
	
	$i++;
}

?>