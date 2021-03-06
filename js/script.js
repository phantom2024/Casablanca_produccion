


function num_ale(){
	var min = 0;
	var max = 9999;
	var res = Math.floor(Math.random() * (max - min + 1)) + min;
	return res;
}

var error_alert = "Error! Llamar ha Digital Creative Tel: (261)428-8566";
var hab_actual = 0;
var est_actual = 0;
var miAjax;
var sonidos = [];

function actualiza_hab(id_hab, est){
	if(id_hab != 0){
		hab_actual = id_hab;
		est_actual = est;
		$.ajax({
			type: 'post',
			/*url: 'hab.php?a='+num_ale(),*/
			url: 'hab.php',
			data: 'hab='+id_hab+'&est='+est_actual,
			success: function(data){
				$(".hab_det").html(data);
			}
		});
	}
}

function act(){

	$.ajax({
		type: 'post',
		dataType: "json",
		url: 'act.php',
		/*url: 'act.php?a='+num_ale(),*/
		data: 'a=on',
		async: false,
		success: function(data){
			
			if(data == false){
				$("#cerrar_login").click();
				return false;
			}
			
			// recarga reloj
			$(".reloj code").html(data.fecha_act);
			
			// recarga totales usuario
			$("#cont_totales").html("");
			$.each(data.totales, function(key1, val1){
				$("#cont_totales").append("<div><b>"+key1+"</b>"+val1+"</div>");
			});
			
			// carga usuarios totalse del turno
			$("#cont_totales_tur").html("");
			$.each(data.totales_tur, function(key2, val2){
				$("#cont_totales_tur").append("<div><b>"+key2+"</b>"+val2+"</div>");
			});

			// carga turnos pendientes
			$("#pendientes").html("");
			if(data.pendientes != null){
				$.each(data.pendientes, function(key3, val3){
					$("#pendientes").append("<div class='ver_pen' rel='"+val3.nro+"'><b>Hab. "+val3.nro+": </b>"+val3.fec_salida+" - "+val3.total+"</div>");
				});
			}
			
			// recarga habitaciones
			// var hubo_cambio = false;
			$.each(data.datos, function(key, val){
				// mostramos hora
				$("#hab_"+val.nro+" .hora").html(val.hora);
				
				/*************************/
				/*julian bou 7/7 larga un sonido cuando pasa la habitacion de rojo a amarillo*/
				var claseAnterior = $("#hab_"+val.nro).attr('class');
				var estiloAnterior = claseAnterior.split(" ")[1];
				if(estiloAnterior == 'est_3' && val.est == 2)
				{
					console.log("ejecutar sonido");
					var hab = val.nro - 1;
					//sonidos[hab].play();
					//$("#hab_"+val.nro).append("<embed class='sonido_"+val.nro+"' height='1' width='1' src='mp3/"+val.nro+".mp3' >");
					hubo_cambio = true;
				}
				else
				{
					//esto quita los embed q va creando
					var numItems = $('.sonido_'+val.nro).length;
					if(numItems>=3)
					{
						$( ".sonido_"+val.nro ).remove();
					}
				}
				/************************/
				
				// quitamos todas las clases
				$("#hab_"+val.nro).removeClass("est_1");
				$("#hab_"+val.nro).removeClass("est_2");
				$("#hab_"+val.nro).removeClass("est_3");
				$("#hab_"+val.nro).removeClass("est_4");
				
				// colocamos la clase correcta
				$("#hab_"+val.nro).addClass("est_"+val.est);
								
				// alerta si el turno esta ocupado y todabia no sale
				if(val.alerta == 1 && parseInt(val.est) == 3){
					
					setTimeout("alerta_r1("+val.nro+");",500);
					setTimeout("alerta_r2("+val.nro+");",1000);
					setTimeout("alerta_r1("+val.nro+");",1500);
					setTimeout("alerta_r2("+val.nro+");",2000);
					setTimeout("alerta_r1("+val.nro+");",2500);
					setTimeout("alerta_r2("+val.nro+");",3000);
					
				}
				
				// alerta si esta saliendo y el turno ya termina
				if(val.alerta == 1 && parseInt(val.est) == 2){
					
					setTimeout("alerta_a1("+val.nro+");",500);
					setTimeout("alerta_a2("+val.nro+");",1000);
					setTimeout("alerta_a1("+val.nro+");",1500);
					setTimeout("alerta_a2("+val.nro+");",2000);
					setTimeout("alerta_a1("+val.nro+");",2500);
					setTimeout("alerta_a2("+val.nro+");",3000);
					
				}
				
				// alerta si esta saliendo y el tiempo esta bien
				if(val.alerta_sal == 1 && parseInt(val.est) == 2){
					
					setTimeout("alerta_n1("+val.nro+");",500);
					setTimeout("alerta_n2("+val.nro+");",1000);
					setTimeout("alerta_n1("+val.nro+");",1500);
					setTimeout("alerta_n2("+val.nro+");",2000);
					setTimeout("alerta_n1("+val.nro+");",2500);
					setTimeout("alerta_n2("+val.nro+");",3000);
					
				}
				
				// si se llamo o no al cliente
				$("#hab_"+val.nro).children().find(".hora").css({color: "#000"});
				if(val.alerta == 1 && parseInt(val.est) == 3){
					if(val.llamado == 0 || val.llamado == 2){
						$("#hab_"+val.nro).children().find(".hora").css({color: "#F00"});
					}else{
						$("#hab_"+val.nro).children().find(".hora").css({color: "#0F0"});
					}
				}
				
  			});
			/*
			if(!hubo_cambio)//si no hubo cambio de 3 a 2 en ninguna hab
			{//borra todos los embed con mp3 que agrego
				$.each(data.datos, function(key, val){
					$( ".sonido_"+val.nro ).remove();
				});
			}*/
			
			// terminamos de actulizar refrescamos los datos de info
			actualiza_hab(hab_actual,est_actual);
			
			setTimeout("act()", 30000);
			
		}
	});
    
}

function alerta_r1(hab){
	$("#hab_"+hab).removeClass("est_3");
	$("#hab_"+hab).addClass("est_5");
}
function alerta_r2(hab){
	$("#hab_"+hab).removeClass("est_5");
	$("#hab_"+hab).addClass("est_3");
}
function alerta_a1(hab){
	$("#hab_"+hab).removeClass("est_2");
	$("#hab_"+hab).addClass("est_5");
}
function alerta_a2(hab){
	$("#hab_"+hab).removeClass("est_5");
	$("#hab_"+hab).addClass("est_2");
}
function alerta_n1(hab){
	$("#hab_"+hab).removeClass("est_2");
	$("#hab_"+hab).addClass("est_6");
}
function alerta_n2(hab){
	$("#hab_"+hab).removeClass("est_6");
	$("#hab_"+hab).addClass("est_2");
}

$(function() {
    
	
	//var c = document.getElementById("myCanvas");
	//var ctx = c.getContext("2d");
	//var snd = new Audio("mp3/1.mp3");
	// snd.play();
	
	for(var i = 1; i<=36; i++){
		console.log("cargados");
		sonidos.push(new Audio("mp3/"+i+".mp3"));
	}
	
	
	$(".hab").click(function(event){
	
		var hab = $(this).attr("id");
		var n = hab.split("_");
		var id_hab = n[1];
		actualiza_hab(id_hab, 1);
	
	});
	
	$(".cont_pendientes").delegate(".ver_pen", "click", function(){
		
		var nro = $(this).attr("rel");
		actualiza_hab(nro, 2);
		
	});
	
	
	/*nuevo julian bou 13/3 implementacion del boton agregar nuevo periodo*/
	
	$(".hab_det").delegate(".button_nue_tur", "click", function(){
		var hab = $(this).attr("id");
		var n = hab.split("_");
		var id_hab = n[1];
		
		$("#id_hab").val(id_hab);
		
		$.ajax({
			type: 'post',
			url: 'turno.php',
			data: 'agregar=on&id_hab='+id_hab,
			success: function(data){
				$("#turnos").html(data);
			}
		});
			
		$("#turnos").dialog("open");
		
	});
	
	$(".hab_det").delegate(".buttonc", "click", function(){
		
		var hab = $(this).attr("id");
		var n = hab.split("_");
		var id_hab = n[1];
		
		//if(confirm("¿ Esta seguro de cerrar el turno para la habitacion Nro: "+id_hab+" ?")){
		
			$.ajax({
				type: 'post',
				url: 'cerrar.php',
				data: 'c=on&id_hab='+id_hab,
				success: function(data){
					//if(data == 'ok'){
						//actualiza_hab(id_hab);
					//}
				}
			});
				
		//}

	});
	
	$(".hab_det").delegate(".buttonb", "click", function(){

		var hab = $(this).attr("id");
		var n = hab.split("_");
		var id_hab = n[1];
		
		
		
		$("#id_hab").val(id_hab);
		
		$.ajax({
			type: 'post',
			url: 'bar.php',
			data: 'b=on&id_hab='+id_hab,
			success: function(data){
				$("#bar").html(data);
			}
		});
			
		$("#bar").dialog("open");

	});
	
	$("#turnos").dialog({
		autoOpen: false,
		width: 600,
		buttons: [
			{
				text: "Guardar",
				click: function(){
						
					var value = $("input:radio[name=group]:checked").val();
					if(value == undefined){
						alert("Deve seleccionar una opcion para poder continiuar.");
						return false;
					}
					
					var id_hab = $("#id_hab").val();
					var str = $("#form_tur_det").serialize();
					
					$.ajax({
						type: 'POST',
						data: 'guardar=on&val_radio='+value+'&id_hab='+id_hab+'&'+str,
						url: 'turno.php'
					});
					
					$(this).dialog("close");
						
				}
			},{
				text: "Cerrar",
				click: function(){
					$("#id_hab").val(0);
					$(this).dialog("close");
				}
			}
		]
	});
	
	$("#bar").dialog({
		autoOpen: false,
		width: 600,
		buttons: [
			{
				text: "Guardar",
				click: function(){
									
					var id_hab_array = new Array();
					var pro_bar_array = new Array();
					var pro_can_array = new Array();
					var probar_v_array = new Array();
					
					/*$('input[name="por_bar_id[]"]').each(function(){
					   id_hab_array.push($(this).val());
					});*/
					$('input[name="pro_bar[]"]').each(function(){
					   pro_bar_array.push($(this).val());
					});
					$('input[name="pro_bar_can[]"]').each(function(){
					   pro_can_array.push($(this).val());
					});
					$('input[name="probar_v[]"]').each(function(){
					   probar_v_array.push($(this).val());
					});
					
					//var json_id_hab = JSON.stringify(id_hab_array);
					var json_pro_bar = JSON.stringify(pro_bar_array);
					var json_pro_can = JSON.stringify(pro_can_array);
					var json_probar_v = JSON.stringify(probar_v_array);	
					var data = {
						gb		  : 'on',
						id_hab    : $("#id_hab").val(),
						pro_bar   : json_pro_bar,
						pro_can   : json_pro_can,
						probar_v  : json_probar_v
					}
					$.ajax({
						type: 'POST',
						data: data,
						dataType: 'json',
						url: 'bar.php'
					});

					$(this).dialog("close");
				}
			},
			{
				text: "Cerrar",
				click: function(){
					$("#id_hab").val(0);
					$(this).dialog("close");
				}
			}
		]
	});
	
	$("#bar").delegate(".eli_itm_bar", "click", function(event){
		if(confirm("¿ Esta seguro de eliminar el item ?")){
			$(this).parent().remove();
		}
	});
	
	$("#cerrar_login").button();
	
	$("#abre_admin").button();
	
	$("#cerrar_turno").button();
	
	$("#abre_admin").click(function(event){
		window.open("admin/", "_blank");
	});
	
	/*
	$("#puntos").dialog({
		autoOpen: false,
		width: 600,
		buttons: [
			{
				text: "Cerrar",
				click: function(){
					$("#id_hab").val(0);
					$(this).dialog("close");
				}
			}
		]
	});
	
	$(".hab_det").delegate(".buttonp", "click", function(){

		var hab = $(this).attr("id");
		var n = hab.split("_");
		var id_hab = n[1];
		
		$("#id_hab").val(id_hab);
		
		$.ajax({
			type: 'post',
			url: 'puntos.php',
			data: 'p=on&id_hab='+id_hab,
			success: function(data){
				$("#puntos").html(data);
				$("#con_pun").focus();
				$("#puntos").dialog("open");
			}
		});

	});
	
	function consulta_puntos(){
		
		var tar = $("#tar_premium").val();
		
		if(isNaN(parseInt(tar)) || parseInt(tar) == 0){
			alert("La Tarjeta premium ingresada no es correcta!");
			$("#tar_premium").val("");
			$("#tar_premium").focus();
			return false;
		}
		
		$.ajax({
			type: 'post',
			url: 'puntos.php',
			data: 'pa=on&tar='+tar,
			success: function(data){
				$("#pun_acumulados").html(data);
			}
		});
		
	}
	
	$("#puntos").delegate("#tar_premium", "keypress", function(event){
		if(event.which == 13){
			consulta_puntos();
			event.preventDefault();
		}
	});

	$("#puntos").delegate("#but_con_puntos", "click", function(event){
		consulta_puntos();
	});
	
	$("#puntos").delegate("#but_car_puntos", "click", function(event){
		
		var tar = $("#tar_premium").val();
		
		if(isNaN(parseInt(tar)) || parseInt(tar) == 0){
			alert("La Tarjeta premium ingresada no es correcta!");
			$("#tar_premium").val("");
			$("#tar_premium").focus();
			return false;
		}
		
		var id_hab = $("#id_hab").val();
		
		$.ajax({
			type: 'post',
			url: 'puntos.php',
			data: 'gp=on&id_hab='+id_hab+'&tar='+tar,
			success: function(data){
				$("#puntos_cargados").html(data);
			}
		});
		
	});
	
	$("#puntos").delegate("#but_can_puntos", "click", function(event){

		var tar = $("#tar_premium").val();
		
		if(isNaN(parseInt(tar)) || parseInt(tar) == 0){
			alert("La Tarjeta premium ingresada no es correcta!");
			$("#tar_premium").val("");
			$("#tar_premium").focus();
			return false;
		}
		
		var id_hab = $("#id_hab").val();
		
		$.ajax({
			type: 'post',
			url: 'puntos.php',
			data: 'cp=on&id_hab='+id_hab+'&tar='+tar,
			success: function(data){
				$("#canjear_puntos").html(data);
			}
		});
		
	});
	
	$("#puntos").delegate("#tar_premium", "click", function(event){
		$(this).select();
		return false;
	});
	*/
	
	$("#but_tar_pre_home").button();
	
	function consulta_puntos_home(){
		
		var tar = $("#tar_pre_home").val();
		
		if(isNaN(parseInt(tar)) || parseInt(tar) == 0){
			return false;
		}
		
		$.ajax({
			type: 'post',
			url: 'puntos.php',
			data: 'pa=on&tar='+tar,
			success: function(data){
				$("#cont_pun_pre").html(data);
			}
		});
		
	}
	$("#but_tar_pre_home").click(function(event){
		consulta_puntos_home();
	});
	
	$("#tar_pre_home").keypress(function(event){
		if(event.which == 13){
			consulta_puntos_home();
			event.preventDefault();
		}
	});
	
	$("#tar_pre_home").click(function(event){
		$(this).select();
		return false;
	});
	
	$(".ver_resumen").click(function(event){
		
		var cont = $(this).attr("id");
		var part = cont.split("-");
		
		$("#" + part[1]).toggle();
		
		if($(this).text() == "Cerrar Resumen"){
			$(this).text("Ver Resumen");
		}else{
			$(this).text("Cerrar Resumen");
		}
		
	});
	
	$("#descuento").dialog({
		autoOpen: false,
		width: 600,
		buttons: [
			{
				text: "Guardar",
				click: function(){

					var imp_descuento = parseFloat($("#imp_descuento").val());
					imp_descuento = Math.round(imp_descuento * 100) / 100;
					var imp_porcentaje = parseFloat($("#imp_porcentaje").val());
					imp_porcentaje = Math.round(imp_porcentaje * 100) / 100;
					var imp_total_des = parseFloat($("#imp_total_des").val());
					imp_total_des = Math.round(imp_total_des * 100) / 100;
					var imp_total_hab = $("#imp_total_hab").val();
					imp_total_hab = Math.round(imp_total_hab * 100) / 100;
					
					if(imp_descuento > imp_total_hab){
						alert("No puede realizar un descuento mayor al importe!");
						return false;
					}
					
					if(isNaN(imp_descuento) || isNaN(imp_porcentaje) || imp_descuento == 0 || imp_porcentaje == 0){
						alert("Debe colocar el importe a descontar o un porsentaje!");
						return false;
					}
					
					/*
					// no funciona se ejecuta despues
					if(!isNaN(imp_descuento)){
						cal_imp_des();
						return false;
					}
					
					if(!isNaN(imp_porcentaje)){
						cal_por_des();
						return false;
					}
					*/
					
					if(imp_total_des == 0){
						if(!confirm("¿ Esta seguro de descontar el total del importe ?")){
							return false;
						}
					}
					
					var id_hab = $("#id_hab").val();
					var est = $("#est_tur_des").val();
					
					$.ajax({
						type: 'post',
						url: 'descuento.php',
						data: 'gd=on&id_hab='+id_hab+'&imp_des='+imp_descuento+'&est='+est,
						success: function(data){
							if(data == "error"){
								alert(error_alert);
							}
						}
					});
					
					$("#id_hab").val(0);
					$(this).dialog("close");
					
				}
			},
			{
				text: "Cerrar",
				click: function(){
					$("#id_hab").val(0);
					$(this).dialog("close");
				}
			}
		]
	});
	
	$(".hab_det").delegate(".buttond", "click", function(){

		var hab = $(this).attr("id");
		var n = hab.split("_");
		var id_hab = n[1];
		
		$("#id_hab").val(id_hab);
		
		$.ajax({
			type: 'post',
			url: 'descuento.php',
			data: 'd=on&id_hab='+id_hab+'&est='+est_actual,
			success: function(data){
				$("#descuento").html(data);
				$("#descuento").dialog("open");
			}
		});

	});
	
	function cal_imp_des(){
		
		$("#imp_descuento").removeAttr("disabled");
		$("#imp_porcentaje").attr("disabled","disabled");
		
		var imp_total_hab = $("#imp_total_hab").val();
		imp_total_hab = Math.round(imp_total_hab * 100) / 100;
		var imp_descuento = $("#imp_descuento").val();
		imp_descuento = Math.round(imp_descuento * 100) / 100;
		
		if(imp_descuento > imp_total_hab){
			$("#imp_descuento").val('');
			$("#imp_descuento").focus();
			alert("No puede realizar un descuento mayor al importe!");
			return false;
		}
		
		// por
		var imp_porcentaje = (imp_descuento * 100) / imp_total_hab
		
		imp_porcentaje = Math.round(imp_porcentaje * 100) / 100;
		
		$("#imp_porcentaje").val(imp_porcentaje);
		
		// imp
		imp_total = imp_total_hab - imp_descuento;

		$("#imp_total_des").val(imp_total);
		
	}
	
	$("#descuento").delegate("#des_p_imp", "click", function(){

		cal_imp_des();
		
	});
	
	function cal_por_des(){
	
		$("#imp_descuento").attr("disabled","disabled");
		$("#imp_porcentaje").removeAttr("disabled");
		
		var imp_total_hab = $("#imp_total_hab").val();
		var imp_porcentaje = $("#imp_porcentaje").val();
		
		if(imp_porcentaje > 100){
			$("#imp_porcentaje").val('');
			$("#imp_porcentaje").focus();
			alert("No puede realizar un descuento mayor al 100%!");
			return false;
		}
		
		// por
		var imp_descuento = (imp_total_hab * imp_porcentaje) / 100
		
		imp_descuento = Math.round(imp_descuento * 100) / 100;
		
		$("#imp_descuento").val(imp_descuento);
		
		// imp
		imp_total = imp_total_hab - imp_descuento;
		
		$("#imp_total_des").val(imp_total);
	
	}
	
	$("#descuento").delegate("#des_p_por", "click", function(){

		cal_por_des();
		
	});
	
	//medio_pago
	$("#medio_pago").dialog({
		autoOpen: false,
		width: 600,
		buttons: [
			{
				text: "Guardar",
				click: function(){
					
					var id_hab = $("#id_hab").val();
					
					//if(confirm("¿ Esta seguro de cerrar el turno para la habitacion Nro: "+id_hab+" ?")){
					
						var saldo = $("#imp_saldo").val();
						if(saldo != 0){
							alert("No puede cerrar la habitacion si todavía queda saldo!!");
							return false;
						}
						
						$.ajax({
							type: 'post',
							url: 'medio_pago.php',
							data: "gp=on&id_hab="+id_hab+"&"+$("#form_list_pago").serialize(),
							success: function(data){
								
								$("#id_hab").val(0);
								$("#medio_pago").dialog("close");
								
								if(data == "error"){
									alert(error_alert);
								}else{
									
									var est = $("#medio_pago_est").val();
									$.ajax({
										type: 'post',
										url: 'cerrar.php',
										data: 'c=on&id_hab='+id_hab+'&est='+est,
										success: function(data){
											if(data == 'ok'){
												actualiza_hab(id_hab, 1);
											}
										}
									});
									
								}
								
							}
						});
					}
					
				//}
			},
			{
				text: "Cerrar",
				click: function(){
					$("#id_hab").val(0);
					$(this).dialog("close");
				}
			}
		]
	});
	
	$(".hab_det").delegate(".buttonm", "click", function(){

		var hab = $(this).attr("id");
		var n = hab.split("_");
		var id_hab = n[1];
		
		$("#id_hab").val(id_hab);
		
		$.ajax({
			type: 'post',
			url: 'medio_pago.php',
			data: 'm=on&id_hab='+id_hab+'&est='+est_actual,
			success: function(data){
				$("#medio_pago").html(data);
				$("#medio_pago").dialog("open");
				calcula_total_apagar();
			}
		});

	});
	
	$("#medio_pago").delegate("#sel_medio_pago", "change", function(){
		
		calcula_total_apagar();
		
		var id_sel = parseInt($(this).val());
		
		$("#cont_med_efe").hide();
		$("#cont_med_tar").hide();
		$("#cont_med_tar_cre").hide();
		$("#cont_med_pun").hide();
		
		switch(id_sel){
			case 1:
				$("#imp_med_efe").val($("#imp_saldo").val());
				$("#cont_med_efe").show();
			break;
			case 2:
				$("#imp_med_tar").val($("#imp_saldo").val());
				$("#cont_med_tar").show();
			break;
			case 3:
				
				// buscamos si ya se cargo con tarjeta de cred
				// para no volver a calcular
				var calculo_ok = false;
				$(".id_mediob").each(function( index ) {
					if($(this).val() == 3){
						calculo_ok = true;
					}
				});
				
				var saldo = parseFloat($("#imp_saldo").val());
				
				if(calculo_ok == false){
					
					var aumento = ((saldo * 0) / 100);
					aumento = Math.round(aumento * 100) / 100;
					saldo = saldo + aumento;
					
					$("#imp_total_aumento").val(aumento);
					
					$("#imp_saldo").val(saldo);
					$("#totalpagado").html(saldo);
					
				}

				$("#imp_med_tar_cre").val(saldo);
				$("#cont_med_tar_cre").show();
				
			break;
			case 4:
				$("#cont_med_pun").show();
			break;
		}
		
	});

	function consulta_puntos(){
		
		var tar = $("#tar_premium").val();
		
		if(isNaN(parseInt(tar)) || parseInt(tar) == 0){
			alert("La Tarjeta premium ingresada no es correcta!");
			$("#tar_premium").val("");
			$("#tar_premium").focus();
			return false;
		}
		
		$.ajax({
			type: 'post',
			url: 'puntos.php',
			data: 'pa=on&tar='+tar,
			success: function(data){
				$("#pun_acumulados").html(data);
			}
		});
		
	}
	
	$("#medio_pago").delegate("#tar_premium", "keypress", function(event){
		if(event.which == 13){
			consulta_puntos();
			event.preventDefault();
		}
	});

	$("#medio_pago").delegate("#but_con_puntos", "click", function(event){
		consulta_puntos();
	});
	
	$("#medio_pago").delegate("#but_car_puntos", "click", function(event){
		
		var tar = $("#tar_premium").val();
		
		if(isNaN(parseInt(tar)) || parseInt(tar) == 0){
			alert("La Tarjeta premium ingresada no es correcta!");
			$("#tar_premium").val("");
			$("#tar_premium").focus();
			return false;
		}
		
		var id_hab = $("#id_hab").val();
		var est = $("#medio_pago_est").val();
		
		$.ajax({
			type: 'post',
			url: 'puntos.php',
			data: 'gp=on&id_hab='+id_hab+'&tar='+tar+'&est='+est,
			success: function(data){
				$("#puntos_cargados").html(data);
			}
		});
		
	});
	
	$("#medio_pago").delegate("#but_can_puntos", "click", function(event){

		var tar = $("#tar_premium").val();
		
		if(isNaN(parseInt(tar)) || parseInt(tar) == 0){
			alert("La Tarjeta premium ingresada no es correcta!");
			$("#tar_premium").val("");
			$("#tar_premium").focus();
			return false;
		}
		
		var id_hab = $("#id_hab").val();
		var est = $("#medio_pago_est").val();
		var imp_med_pun = $("#imp_med_pun").val();
		$.ajax({
			type: 'post',
			url: 'puntos.php',
			data: 'cp=on&id_hab='+id_hab+'&tar='+tar+'&est='+est+'&imp_med_pun='+imp_med_pun,
			success: function(data){
				$("#canjear_puntos").html(data);
				
				if(data != "La Tarjeta no es correcta!!"){
					
					if(data != "No tiene la cantidad de puntos necesarios!!"){
						
						// mostramos el campo con los puntos canjeados
						var id_medio = $("#sel_medio_pago").val();
						var medio = $("#medio_pago option:selected").text();
						var tar_premium = $("#tar_premium").val();
						
						$.ajax({
							type: 'post',
							url: 'medio_pago.php',
							data: 'cm=on&id_medio='+id_medio+'&medio='+medio+'&imp_medio='+imp_med_pun+'&cod_pre='+tar_premium,
							success: function(data){
								$("#medio_a_grabar").append(data);
								calcula_total_apagar();
							}
						});
						
					}
				}
				
			}
		});

	});
	
	$("#medio_pago").delegate("#but_imp_med_efe", "click", function(event){

		var id_medio = $("#sel_medio_pago").val();
		var medio = $("#medio_pago option:selected").text();
		var imp_med_efe = parseFloat($("#imp_med_efe").val());
		
		if(isNaN(imp_med_efe) || imp_med_efe <= 0){
			alert("El importe ingresado no es correcto");
			$("#imp_med_efe").val('');
			$("#imp_med_efe").focus();
			return false;	
		}
		
		$.ajax({
			type: 'post',
			url: 'medio_pago.php',
			data: 'cm=on&id_medio='+id_medio+'&medio='+medio+'&imp_medio='+imp_med_efe,
			success: function(data){
				$("#medio_a_grabar").append(data);
				calcula_total_apagar();
				$("#imp_med_efe").val('');
			}
		});

	});
	
	$("#medio_pago").delegate("#but_imp_med_tar", "click", function(event){

		var id_medio = $("#sel_medio_pago").val();
		var medio = $("#medio_pago option:selected").text();
		var imp_med_tar = parseFloat($("#imp_med_tar").val());
		var imp_med_tar_cod = $("#imp_med_tar_cod").val();
		
		if(isNaN(imp_med_tar) || imp_med_tar <= 0){
			alert("El importe ingresado no es correcto");
			$("#imp_med_tar").val('');
			$("#imp_med_tar").focus();
			return false;	
		}
		
		$.ajax({
			type: 'post',
			url: 'medio_pago.php',
			data: 'cm=on&id_medio='+id_medio+'&medio='+medio+'&imp_medio='+imp_med_tar+'&cod_tar='+imp_med_tar_cod,
			success: function(data){
				$("#medio_a_grabar").append(data);
				calcula_total_apagar();
				$("#imp_med_tar").val('');
				$("#imp_med_tar_cod").val('');
			}
		});

	});
	
	$("#medio_pago").delegate("#but_imp_med_tar_cre", "click", function(event){

		var id_medio = $("#sel_medio_pago").val();
		var medio = $("#medio_pago option:selected").text();
		var imp_med_tar_cre = parseFloat($("#imp_med_tar_cre").val());
		var imp_med_tar_cod_cre = $("#imp_med_tar_cod_cre").val();
		
		if(isNaN(imp_med_tar_cre) || imp_med_tar_cre <= 0){
			alert("El importe ingresado no es correcto");
			$("#imp_med_tar_cre").val('');
			$("#imp_med_tar_cre").focus();
			return false;	
		}
		
		// buscamos si ya se cargo con tarjeta de cred
		// para no volver a calcular
		var calculo_ok = false;
		$(".id_mediob").each(function( index ) {
			if($(this).val() == 3){
				calculo_ok = true;
			}
		});
		
		var saldo = parseFloat($("#imp_saldo").val());
		
		if(calculo_ok == false){		
			// modificamos el saldo y el total de la habitacion
			$(".imp_total_hab_medio_pago").children('span').html(saldo);
			$('#imp_total_hab_medio_pago').val(saldo);
		}

		$.ajax({
			type: 'post',
			url: 'medio_pago.php',
			data: 'cm=on&id_medio='+id_medio+'&medio='+medio+'&imp_medio='+imp_med_tar_cre+'&cod_tar='+imp_med_tar_cod_cre,
			success: function(data){
				$("#medio_a_grabar").append(data);
				calcula_total_apagar();
				$("#imp_med_tar_cre").val('');
				$("#imp_med_tar_cod_cre").val('');
			}
		});

	});

	// auto seleccion del contenido del imput
	$("#medio_pago").delegate("#imp_med_efe", "click", function(event){
		$(this).select();
		return false;
	});
	
	$("#medio_pago").delegate("#imp_med_tar", "click", function(event){
		$(this).select();
		return false;
	});
	
	$("#medio_pago").delegate("#imp_med_tar_cre", "click", function(event){
		$(this).select();
		return false;
	});
	
	$("#medio_pago").delegate("#tar_premium", "click", function(event){
		$(this).select();
		return false;
	});
	
	// modal llamado
	$("#llamado").dialog({
		autoOpen: false,
		width: 600,
		buttons: [
			{
				text: "Cerrar",
				click: function(){
					$("#id_hab").val(0);
					$(this).dialog("close");
				}
			}
		]
	});

	$(".hab_det").delegate(".open_llamar", "click", function(event){
		
		var hab = $(this).attr("id");
		var n = hab.split("_");
		var id_hab = n[1];
		
		$("#id_hab").val(id_hab);
		
		$.ajax({
			type: 'post',
			url: 'llamado.php',
			data: 'l=on&id_hab='+id_hab,
			success: function(data){
				$("#llamado").html(data);
				$("#llamado").dialog("open");
			}
		});
		
	});
	
	$("#llamado").delegate("#llamado_ate", "click", function(event){
		
		var id_hab = $("#id_hab").val();
		$.ajax({
			type: 'post',
			url: 'llamado.php',
			data: 'gl=on&ate=1&id_hab='+id_hab,
			success: function(data){
				$("#llamado").dialog("close");
			}
		});
		
	});
	
	$("#llamado").delegate("#llamado_noate", "click", function(event){
		
		var id_hab = $("#id_hab").val();
		$.ajax({
			type: 'post',
			url: 'llamado.php',
			data: 'gl=on&ate=2&id_hab='+id_hab,
			success: function(data){
				$("#llamado").dialog("close");
			}
		});
		
	});
	
	$(".hab_det").delegate(".buttonmos", "click", function(){
		
		var hab = $(this).attr("id");
		var n = hab.split("_");
		var id_hab = n[1];
		$(this).hide();
		$.ajax({
			type: 'post',
			url: 'mostrar.php',
			data: 'mos=on&mos=1&id_hab='+id_hab+'&est='+est_actual,
			success: function(data){
				if(data == 'ok'){
					$(this).hide();
				}
			}
		});

	});
	
	$(".hab_det").delegate(".buttonnomos", "click", function(){
		
		var hab = $(this).attr("id");
		var n = hab.split("_");
		var id_hab = n[1];
		$(this).hide();
		$.ajax({
			type: 'post',
			url: 'mostrar.php',
			data: 'mos=on&mos=0&id_hab='+id_hab+'&est='+est_actual,
			success: function(data){
				if(data == 'ok'){
					$(this).hide();
				}
			}
		});

	});
	
	$("#turnos").delegate(".rad_tur", "click", function(){
		
		$(".cont_tur").hide();
		$(this).parent().parent().find(".cont_tur").show();
		
		$(".cont_tur_fin").show();
		
		var str = $(this).attr('alt');
		var tur = str.split("_");
		
		$("#pro_f").val(tur[0]);
		$("#hor_f").val(tur[1]);
		$("#tie_f").val(tur[2]);
		$("#pre_f").val(tur[3]);
		$("#tie_fh").val(tur[2]);
		$("#pre_fh").val(tur[3]);
		$("#tie_fs").val(tur[4]);
		
	});

	$(".hab_det").delegate(".eli_itm_bar_det", "click", function(event){		
		if(confirm("¿ Esta seguro de eliminar el item ?")){
			
			var hre = $(this).attr('href');
			iddetalle = hre.replace('#','');
			$.ajax({
				type: 'post',
				url: 'eli_item.php',
				data: 'iddetalle='+iddetalle,
				success: function(data){
					if(data == 'ok'){
						$(this).parent().parent().remove();
						actualiza_hab(hab_actual,est_actual);
					}
				}
			});
			
		}
	});
	
	$("#aumento").dialog({
		autoOpen: false,
		width: 600,
		buttons: [
			{
				text: "Guardar",
				click: function(){
		
					var imp_aumento = parseFloat($("#imp_aumento").val());
					imp_aumento = Math.round(imp_aumento * 100) / 100;
					var imp_total_hab_aum = $("#imp_total_hab_aum").val();
					imp_total_hab_aum = Math.round(imp_total_hab_aum * 100) / 100;
					
					if(isNaN(imp_aumento) || imp_aumento == 0){
						alert("Debe colocar el importe de recargo!");
						return false;
					}
					
					var id_hab = $("#id_hab").val();
					
					$.ajax({
						type: 'post',
						url: 'recargo.php',
						data: 'gr=on&id_hab='+id_hab+'&imp_aum='+imp_aumento+'&est='+est_actual,
						success: function(data){
							if(data == "error"){
								alert(error_alert);
							}
						}
					});
					
					$("#id_hab").val(0);
					$(this).dialog("close");
					
				}
			},{
				text: "Cerrar",
				click: function(){
					$("#id_hab").val(0);
					$(this).dialog("close");
				}
			}
		]
	});
	
	//fabian
	$(".hab_det").delegate(".buttonr", "click", function(){

		var hab = $(this).attr("id");
		var n = hab.split("_");
		var id_hab = n[1];
		
		$("#id_hab").val(id_hab);
		$.ajax({
			type: 'post',
			url: 'recargo.php',
			data: 'r=on&id_hab='+id_hab+'&est='+est_actual,
			success: function(data){
				$("#aumento").html(data);
				$("#aumento").dialog("open");
			}
		});

	});

	// llamada para comenzar las recargas
	act();
	
});
	
function calcula_total_apagar(){
	
	var imp_total = $("#imp_total_hab_medio_pago").val();
	var imp_medio = 0;
	$(".imp_medio").each(function(index){
		imp_medio = imp_medio + parseFloat($(this).val());
	});
	saldo = imp_total - imp_medio;
	saldo = Math.round(saldo * 100) / 100;
	
	if(saldo < 0){
		$("#imp_vuelto").val(saldo);
		$("#totalvuelto").html(saldo);
		$("#cont_totalvuelto").show();
		saldo = 0;
	}
	
	$("#imp_saldo").val(saldo);
	$("#totalpagado").html(saldo);
	
}
	
function ya_canjeo(imp, pre){
	
	// mostramos el campo con los puntos canjeados
	// error si cambiamos el valor de id en la base de datos
	
	$("#medio_pago option[value=4]").attr("selected","selected");
	var id_medio = $("#sel_medio_pago").val();
	var medio = $("#medio_pago option:selected").text();
	
	$.ajax({
		type: 'post',
		url: 'medio_pago.php',
		data: 'cm=on&id_medio='+id_medio+'&medio='+medio+'&imp_medio='+imp+'&cod_pre='+pre,
		success: function(data){
			$("#medio_a_grabar").append(data);
			calcula_total_apagar();
		}
	});
	
}


