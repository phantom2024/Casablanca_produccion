<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Casablanca Admin</title>
	
<link href="../css/cupertino/jquery-ui-1.10.3.custom.css" rel="stylesheet">
<script src="../js/jquery-1.9.1.js"></script>
<script src="../js/jquery-ui-1.10.3.custom.js"></script>
<script>
$(function() {
	
	$(".button").button();
	
	$(".button").click(function(event){

		var hab = $(this).attr("id");
		var n = hab.split("_");
		var abm = n[1];
		
		window.open("abm.php?fx="+abm, "ifr_abm");

		event.preventDefault();
	});
	
});
</script>
<style>
.con_but{
	float:left;
	margin:4px;
	padding:4px;
}
</style>
</head>
<body>

	<div>
		
        <div>
            <div class="con_but">
                <button class="button" id="button_hab">Habitaciones</button>
            </div>
            <div class="con_but">
                <button class="button" id="button_tip">Tipo de Habitaciones</button>
            </div>
            <div class="con_but">
                <button class="button" id="button_tur">Turnos</button>
            </div>
            <div class="con_but">
                <button class="button" id="button_pro">Productos</button>
            </div>
            <div class="con_but">
                <button class="button" id="button_log">Log de Estados</button>
            </div>
			<div class="con_but">
                <button class="button" id="button_dat">Informe</button>
            </div>
            <div style="clear:both;"></div>
        </div>
        
    </div>
    <iframe id="ifr_abm" name="ifr_abm" width="740" height="580" frameborder="0"></iframe>
    
</body>
</html>