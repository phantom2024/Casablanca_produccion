<?php
require_once("boot.php");

if($_POST['l']){
    
	?>
    Se llamo a la Habitacion Nro <?php echo $_POST['id_hab']; ?>
    <br />
    <div style="text-align:center;">
		<input type="button" id="llamado_ate" name="llamado_ate" value="Atendio" />
        <input type="button" id="llamado_noate" name="llamado_noate" value="No Atendio" />
    </div>
    <script>
		$("#llamado_ate").button();
		$("#llamado_noate").button();
	</script>
	<?php
    
	exit;

}

if($_POST['gl']){

	$sql = "UPDATE tur SET llamado = '".$_POST['ate']."' WHERE nro = '".$_POST['id_hab']."'";
	$result = mysql_query($sql);
	echo "ok";
	
	exit;
}
