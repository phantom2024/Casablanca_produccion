
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
<script>

function mostrar_precios_hab()
{
	window.location = "?fx=precios&index="+ document.getElementById('sel_dia_precios').value;
}

</script>