<?

/* ------------------------------------------------------------------------- *\
|                                                                             |
|						CLASE MYSQL | MARCOS TRENTACOSTE |
|                                                                             |
\* ------------------------------------------------------------------------- */

$comillas=chr(34);
$salto=chr(13)&chr(10);

function printr($t){
	echo '<pre>';
	print_r($t);
	echo '</pre>';
}

class MySqlClass{

	var $varTiempoSql;
	var $varSQLQueries;
	var $num_rows;
	var $database;
	var $user;
	var $pass;
	var $host;
	var $link;

	var $debug=false;
	var $showsql=false;
	
	function conectar($database){
	   // CONECTA A LA BASE DE DATOS Y DEVUELVE TRUE O FALSE
	   
		switch ($_SERVER['HTTP_HOST']){
			default:
				$server = "localhost";
				$user = "casablancanew";
				$conta = "HNEN2sMGjTZ3Vvu3";
				$database = "casablancanew";
			
		}

	   if (!($link=mysql_connect($server,$user,$conta))){
		  echo "Error conectando a la base de datos.";
		  exit();
	   }else{
		   @mysql_query("SET NAMES 'utf8'"); 
	   }
	   if (!mysql_select_db($database)){
		  echo "Error seleccionando la base de datos.";
		  exit();
	   }
	   else{return true;}
	}

	function ejecutar($SQLtext){
		//EJECUTA UNA SENTENCIA SQL
		//COMPRUEBA SI SE ENVIARON DATOS Y SI LA CONSULTA ES VALIDA SY DEVUELVE FALSE
		if ($this->debug==true){
			echo "<b>ejecutar</b> ".$SQLtext."<BR><BR>";
			return true;
		}else{
			if ($SQLtext){
				$tmp = mysql_query($SQLtext);
				if (!$tmp){
					//escribir("La consulta no se ejecuto [ $SQLtext ]");
					return false;
				}else{
					$error="ejecutar $SQLtext".mysql_error($this->linkdb);
					
					if ($this->showsql==true){
						echo "<b>ejecutar</b> ".$SQLtext."<BR><BR>";
					}
					return true;
				}
			}
			//mysql_free_result($result);
		}
	}
        /**
         * Devuelve un array de datos.
         * 
         * @return array Devuelve un array si tiene exito, false si falla.
         */
	public function extraer($sql) {
            $datos = false;
            $result = mysql_query($sql);
            if ($result) {
                while ($dato = mysql_fetch_assoc($result)) {
                    $datos[] = $dato; 
                }
            }else{
				$error="extraer ".mysql_error($this->linkdb);
				return false;
			}
            return $datos;
        }
        
	function extraerDato($SQLtext){
		//DEVUELVE UN DATO A UNA VARIABLE COMUN
		//COMPRUEBA SI SE ENVIARON DATOS Y SI LA CONSULTA ES VALIDA SY DEVUELVE FALSE
		if ($SQLtext){
			$tmp = mysql_query($SQLtext);
			if (@mysql_num_rows($tmp)<>0){
				$row = mysql_result($tmp,0);
				if ($this->debug==true or $this->showsql==true){
					echo "<b>extraerDato</b> ".$SQLtext." <b> RESULTADO=$row</B><BR><BR>";
				}
				return $row;
			}else{
				$error="ejecutar ".mysql_error($this->linkdb);
				return false;
			}
			
		}
	}
	

	function cstrSave($cadena){
		//QUITA LAS COMILLAS SIMPLES Y DOBLES Y SALTO DE LINEA
		$cadena=str_replace("'","",$cadena);
		$cadena=str_replace($comillas,"",$cadena);
		$cadena=str_replace($salto,"<br>",$cadena);
		return $cadena;
	}

	function cdecimalSave($cantidad){
		//CORRIJE DECIMALES
		$cantidad=str_replace(".","",$cantidad);
		$cantidad=str_replace(",",".",$cantidad);
		return $cantidad;
	}

	function formatea_fecha($fecha){
		//FORMATEA LA FECHA GRABARLA EN LA BASE
		if ($fecha){
		$fecha=str_replace("-","/",$fecha);
		if (ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $fecha, $mifecha)) {
    	$lafecha=$mifecha[3]."-".$mifecha[2]."-".$mifecha[1];
	    return $lafecha;}else{
		return false;}
		}else{return false;}
	}
	
	function guardar($tabla,$names,$values){
		$cantidad_variables = count($values);
		for($i=0;$i<$cantidad_variables;$i++){
			$values[$i]="'$values[$i]'";
		}
		$campos=implode(',',$names);
		$valores=implode(',',$values);
		
		if ($this->ejecutar("INSERT INTO $tabla ($campos) VALUES($valores)")){
			//echo "Registro Guardado";
			return true;
		}else{echo "error al guardar";}
	}
	
	
	function guardar2($tabla,$names,$values){
			$cantidad_variables = count($names);
			//$patron="^[[:digit:]]+$";
			for($i=0;$i<$cantidad_variables;$i++){
				if ($this->comprueba_numero($values[$i])){
					$values[$i]=str_replace(",", ".",$values[$i]);
				}
				$values[$i]="'$values[$i]'";
				//$$names[$i]=$values[$i]; crea variables con el titulo
			}
			//echo count($values);
			count($names);			
			$campos= implode(',',$names);
			$valores=implode(',',$values);
			if ($this->ejecutar("INSERT INTO $tabla ($campos) VALUES($valores)")){
				echo "Registro Guardado";
				return true;
			}else{
				echo "error al guardar";
			}
	}
	
	function comprueba_numero($numero){
		$partes=split(",",trim($numero));
		if (is_numeric($partes[0]) and is_numeric($partes[1])){
			return true;
		}else{
			return false;
		}
	}
}
?>