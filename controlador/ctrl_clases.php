<?php

/*Conexión a la base*/


class BASEDEDATOS
{
	public function CONEXION()
	{

		$datos=parse_ini_file('db_local.ini', true);

		$bd= new mysqli($datos['db_local']['host'], $datos['db_local']['username'], $datos['db_local']['password'], $datos['db_local']['base']);

		if (!$bd) 
		{
    		return "Error: No se pudo conectar a MySQL " . mysqli_connect_error() . PHP_EOL;
    	}
    	else
    	{
    		return $bd;
    	}

		
	}

	public function base64url_encode($data) {
	  return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
	}

	public function base64url_decode($data) {
	  return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
	} 	

	public function saco_comillas($array)
	{
		foreach ($array as $key => $value) 
		{
			$array[$key]=str_replace('"', '', $value);
			$array[$key]=str_replace("'", "", $value);
		}

		return $array;
	}

	/*public function subir_imagen($FILES)
	{
		if($FILES['imagen']['type']=="image/png" or $FILES['imagen']['type']=="image/jpg")
			{
				if($FILES['imagen']['size']<(400*1000))
				{
					$dir_subida = './perfiles/';
					$fichero_subido = $dir_subida . basename($FILES['imagen']['name']);
					move_uploaded_file($FILES['imagen']['tmp_name'], $fichero_subido);

					return $fichero_subido;
					
				}

			}
	}*/
	
		
}


/*Diseño de la pagina*/


class DISENO extends BASEDEDATOS
{

	public function plantilla_inicio($titulo, $keywords, $description, $author, $titulo_principal, $subtitulo, $menu)
	{
		?>
		<!DOCTYPE html>
		<html lang="es">
		<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="description" content="<?=$description?>">
		<meta name="keywords" content="<?=$keywords?>">
		<meta name="author" content="<?=$author?>">
		<link rel="alternate" hreflang="es" href="http://www.socialtravel.com.ar/" />
		<link rel="icon" href="logo/socialtravel.ico" type="image/x-icon"/>
		<title><?=$titulo?></title>

		</head>
		<body>
		<div class="container-fluid cabeza" align="center">
		
		<div class="col-md-12"><h1 class="tituloprincipal"><?=$titulo_principal?></h1><h2 class="subtitulo"><?=$subtitulo?></h2></div>

		</div>

		<nav class="navbar navbar-expand-md navbar-dark nav ">
		    
		    <button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
		      <span class="navbar-toggler-icon"></span>
		    </button>
		    <div class="navbar-collapse justify-content-center collapse" id="navbarCollapse" style="">
		      <ul class="navbar-nav ">

		        <?=$menu?>

		      </ul>
		      
		    </div>
		</nav>
		
			
		<?
	}


	public function plantilla_fin()
	{
		?>
		<footer class="footer" >
		<div class="container-fluid">

		

		</div>
		</footer>
		</body>
		</html>
		<?
	}


	public function librerias()
	{
		?>
		<link rel="stylesheet" href="librerias/bootstrap4/css/bootstrap.min.css">
		<link rel="stylesheet" href="librerias/estilo.min.css">
		<script src="librerias/jquery/jquery.min.js"></script>
		<script src="librerias/jquery/funciones.js"></script>
		<script src="librerias/bootstrap4/js/bootstrap.min.js"></script>
		<script src="librerias/proceso.js"></script>			
		<?	
	}

	public function librerias_extra()
	{
		?>
		<link rel="stylesheet" href="librerias/jquery/confirm.css">
    	<script src="librerias/jquery/confirm.js"></script>
		<?
	}

	public function menu($pestanas)
	{
		$menu="";
		
		foreach ($pestanas as $key => $value) {
		
			//El campo target define si se abre en otra pestaña o en la misma
			$menu.='<li class="nav-item" >
				<label class="nav-link">'.strtoupper($value).'</label>
				</li>';			
		}

		//return utf8_encode($menu);
		return $menu;
		
	}

	public function cantidad_procesos($cantidad)
	{
		$select='<div class="input-group mb-3">
		  <div class="input-group-prepend">
		    <span class="input-group-text" id="basic-addon1">Cantidad de procesos</span>
		  </div>';
		$select.='<select class="form-control" id="cantidad_procesos">
		<option value="">Elegir::</option>';

		for ($i=1; $i <= $cantidad; $i++) { 
			$select.='<option value="'.$i.'">'.$i.'</option>';
		}

		$select.='</select></div>';

		return $select;
	}

	

}


class PROCESO extends BASEDEDATOS
{
	const TIEMPO_SO = 1;
	const TIEMPO_ES = 10;
	const CANTIDAD_MAXIMA_RAFAGAS=5;
	const TIEMPO_MAXIMO = 2000;
	const TIMEPO_EJECUCION_RR = 10;
	const ESTADOS = array(
						'SO',
						'Nuevo',
						'Listo',
						'Ejecucion',
						'Bloqueado',
						'Terminado'
						);

	public function formulario_proceso($filas)
	{
		

		

		$input='<div class="row justify-content-md-center contenedor">
				<div class="col-sm-8">
				<form id="formulario_proceso" name="formulario_proceso">';

				for ($i=0; $i < $filas; $i++) { 
					$input.='<fieldset id="field_'.$i.'">
					<legend>Proceso '.$i.'</legend>
						<input type="hidden" class="form-control" id="nombre_'.$i.'" name="nombre_'.$i.'" value="'.$i.'">
						<input type="number" class="form-control" id="entrada_'.$i.'" name="entrada_'.$i.'" placeholder="Tiempo de entrada en nuevo">';
					$input.='<div class="input-group mb-3">
					  <div class="input-group-prepend">
					    <span class="input-group-text" id="basic-addon1">Cantidad de rafagas</span>
					  </div>';
					$input.='<select class="rafagas form-control" data="selectrafaga_'.$i.'" name="cantrafaga_'.$i.'" ><option value="">Cantidad de rafagas</option>';
					for ($n=1; $n <= self::CANTIDAD_MAXIMA_RAFAGAS; $n++) { 
						$input.='<option value="'.$n.'">'.$n.'</option>';
					}	
					$input.='</select></div>';

					$input.="<div class='contenedor_rafagas'></div>";
					$input.='</fieldset>';
				}

		$input.='<input type="button" class="btn btn-success btn-lg" id="Btn_procesar" value="PROCESAR">
				</form>
			</div>
		</div>';

		return $input;
	}

	public function ordernar_formulario($cantidad,$campos)
	{
		if(!$campos){
			return false;
		}

		/*
		$campos Viene así
		Array
		(
		    [nombre_0] => P0
		    [entrada_0] => sasd
		    [selectrafaga_0] => 2
		    [selectrafaga_0-rafaga_0] => asdasd
		    [selectrafaga_0-rafaga_1] => asdasd
		    [nombre_1] => P1
		    [entrada_1] => asdasd
		    [selectrafaga_1] => 4
		    [selectrafaga_1-rafaga_0] => jjsjdsjjs
		    [selectrafaga_1-rafaga_1] => jasjdjasjd
		    [selectrafaga_1-rafaga_2] => jajsdaksjd
		    [selectrafaga_1-rafaga_3] => lasl
		    [nombre_2] => P2
		    [entrada_2] => nnnn
		    [selectrafaga_2] => 4
		    [selectrafaga_2-rafaga_0] => j
		    [selectrafaga_2-rafaga_1] => jk
		    [selectrafaga_2-rafaga_2] => j
		    [selectrafaga_2-rafaga_3] => k
		)

		*/

		$formulario = array();
		
		//Cuanto la cantidad procesos
		for ($i=0; $i < $cantidad; $i++) { 
			
			//Recorro todos los campos
			foreach ($campos as $key => $value) {

				//valido que sean numeros y que esten dentro del rango permitido

				if(!is_numeric($value) or ($value < 0 or $value > self::TIEMPO_MAXIMO)){
					return "<div id='error_input'>Los campos deben ser números naturales, ya que son unidades de tiempo.</div>";
					break 2;
				}

				//Filtro los campos de segundo nivel explotandolo por guion

				$partes=explode("-", $key);
				$count=count($partes);


				if($count > 1){
					$nombre=substr($partes[0], 0,-2);
					if(substr($partes[0], -1) == $i ){
						if(!is_numeric($value) or ($value < 1 or $value > self::TIEMPO_MAXIMO)){
							return "<div id='error_input'>Los campos deben ser números naturales, ya que son unidades de tiempo.</div>";
							break 2;
						}
						else{
							$formulario[$i][$nombre][]=$value;
						}
						
					}			
				}
				else{
					$nombre=substr($key, 0,-2);
					if(substr($key, -1) == $i ){
						if(!is_numeric($value) or ($value < 0 or $value > self::TIEMPO_MAXIMO)){
							return "<div id='error_input'>Los campos deben ser números mayores o iguales a cero, ya que es la unidad de tiempo en que arranca la Rafaga.</div>";
							break 2;
						}
						else{
							$formulario[$i][$nombre]=$value;
						}
						
						
					}
					
				}

				
			}
		}


		return self::ordenar_por_entrada($formulario);

	}

	public function ordenar_por_entrada($formulario)
	{
		$aux=array();
		#Genero un array con solo las entradas
		foreach ($formulario as $key => $row) {
		    $aux[$key] = $row['entrada'];
		}
		#Ordena por el campo entrada
		array_multisort($aux, SORT_ASC, $formulario);
		return $formulario;
	}


	public function no_repetir_entradas($formulario){

		$entrada=0;

		//Si se repite la entrada se suma uno
		for ($i=0; $i < count($formulario); $i++) { 

			if($formulario[$i]['entrada'] < $entrada){				
				$entrada++;
				$formulario[$i]['entrada'] = $entrada;
			}
			else{
				$entrada=$formulario[$i]['entrada'];
			}
		}

		return $formulario;
	}

	public function calcular_tiempos_es($formulario){

		$tiempo_es=array();

		for ($i=0; $i < count($formulario); $i++) { 
			for ($j=0; $j < count($formulario[$i]["selectrafaga"]); $j++) { 
				$tiempo_es[$i][$j]=self::TIEMPO_ES;
			}
		}

		return $tiempo_es;
	}

	

	public function fifo($formulario)
	{
		//Si el tiempo de entrada se repite se suma uno
		$formulario=self::no_repetir_entradas($formulario);
		//print_r($formulario);
		//El primero que entra es el primero que sale.
		$fifo=array();
		$estado=array();

		$tiempo_penultimo;
		$tiempo_antepenultimo;
		$tiempo_proximo;
		$tiempo_es=self::calcular_tiempos_es($formulario);		

		#Bucle de tiempo total
		for ($tiempo=0; $tiempo < self::TIEMPO_MAXIMO; $tiempo++) { 
			
			if($tiempo>0){
				$tiempo_penultimo=$tiempo-1;				
			}

			if($tiempo>1){
				$tiempo_antepenultimo=$tiempo-2;
			}

			if($tiempo>=0){
				$tiempo_proximo=$tiempo+1;				
			}
			
			/*
			Array
			(
			    [0] => Array
			        (
			            [nombre] => P0
			            [entrada] => asdasd
			            [cantrafaga] => 2
			            [selectrafaga] => Array
			                (
			                    [0] => 2
			                    [1] => 3
			                )

			        )

			    [1] => Array
			        (
			            [nombre] => P1
			            [entrada] => asdas
			            [cantrafaga] => 2
			            [selectrafaga] => Array
			                (
			                    [0] => sd
			                    [1] => asda
			                )

			        )

			)
			*/

			#Bucle de procesos		
			for ($proceso=0; $proceso < count($formulario); $proceso++) {				

				#Entra el proceso
				if($formulario[$proceso]['entrada']==$tiempo)
				{
					if(empty($fifo[$tiempo_penultimo][$proceso]['estado'])){

						if(!$estado[$tiempo][self::ESTADOS[0]] and !$estado[$tiempo][self::ESTADOS[3]]){
								$fifo[$tiempo][$proceso]['estado']=self::ESTADOS[0];
								$fifo[$tiempo][$proceso]['proceso']=$formulario[$proceso]["nombre"];

								$estado[$tiempo][self::ESTADOS[0]]=true;
							}
							else{
								$formulario[$proceso]['entrada']=$formulario[$proceso]['entrada']+1;
							}						

					}
				}



				#SO
				//Si en el tiempo anterior está en SO revisar el antepenultimo y se decide que estado va
				if($fifo[$tiempo_penultimo][$proceso]['estado']==self::ESTADOS[0]){

					//Si el tiempo antepenultimo es vacio paso a nuevo
					if(empty($tiempo_antepenultimo)){
						$fifo[$tiempo][$proceso]['estado']=self::ESTADOS[1];
						$fifo[$tiempo][$proceso]['proceso']=$formulario[$proceso]["nombre"];

						$estado[$tiempo][self::ESTADOS[1]]=true;
					}

					//Si tiene tiempo antepenultimo pero no se a guardado en ningun lado
					if(empty($fifo[$tiempo_antepenultimo][$proceso]['estado'])){
						$fifo[$tiempo][$proceso]['estado']=self::ESTADOS[1];
						$fifo[$tiempo][$proceso]['proceso']=$formulario[$proceso]["nombre"];

						$estado[$tiempo][self::ESTADOS[1]]=true;
					}

					//Si el tiempo antenpenultimo fue nuevo pasa a listo
					if($fifo[$tiempo_antepenultimo][$proceso]['estado']==self::ESTADOS[1]){
						$fifo[$tiempo][$proceso]['estado']=self::ESTADOS[2];
						$fifo[$tiempo][$proceso]['proceso']=$formulario[$proceso]["nombre"];

						$estado[$tiempo][self::ESTADOS[2]]=true;
					}

					//Si el tiempo antenpenultimo fue listo pasa a ejecucion
					if($fifo[$tiempo_antepenultimo][$proceso]['estado']==self::ESTADOS[2]){


						//si no hay ninguno en SO ni en Ejecución pasa a ejecución
						
							
						foreach (($formulario[$proceso]["selectrafaga"]) as $key => $value) {

							if($value > 0){
								$value=$value-1;
								$formulario[$proceso]["selectrafaga"][$key]=$value;

								$fifo[$tiempo][$proceso]['estado']=self::ESTADOS[3];
								$fifo[$tiempo][$proceso]['proceso']=$formulario[$proceso]["nombre"];

								$estado[$tiempo][self::ESTADOS[3]]=true;

								break;
							}
						}

						
					}

					//Si el tiempo antenpenultimo fue ejecucion y tiene más rafagas pasa a bloqueado si no tiene más rafagas pasa a terminado
					if($fifo[$tiempo_antepenultimo][$proceso]['estado']==self::ESTADOS[3]){

						if(count($formulario[$proceso]["selectrafaga"]) > 0){


							foreach ($tiempo_es[$proceso] as $key => $value) {

								if($value > 0){
									$value=$value-1;
									
									$tiempo_es[$proceso][$key]=$value;
									$fifo[$tiempo][$proceso]['estado']=self::ESTADOS[4];
									$fifo[$tiempo][$proceso]['proceso']=$formulario[$proceso]["nombre"];

									$estado[$tiempo][self::ESTADOS[4]]=true;

									break;
								}
							}

						}
						else{
							$fifo[$tiempo][$proceso]['estado']=self::ESTADOS[5];
							$fifo[$tiempo][$proceso]['proceso']=$formulario[$proceso]["nombre"];

							$estado[$tiempo][self::ESTADOS[5]]=true;
						}
					}


					//Si el tiempo antepenultimo es bloqueado debe ir a listo
					if($fifo[$tiempo_antepenultimo][$proceso]['estado']==self::ESTADOS[4]){

						$fifo[$tiempo][$proceso]['estado']=self::ESTADOS[2];
						$fifo[$tiempo][$proceso]['proceso']=$formulario[$proceso]["nombre"];

						$estado[$tiempo][self::ESTADOS[2]]=true;
					}

					
				}		




				#Nuevo
				//Si en el tiempo anterior está en Nuevo entra a SO
				if($fifo[$tiempo_penultimo][$proceso]['estado']==self::ESTADOS[1]){

					//si no hay ninguno en SO ni en Ejecución
					if(!$estado[$tiempo][self::ESTADOS[0]] and !$estado[$tiempo][self::ESTADOS[3]]){
						$fifo[$tiempo][$proceso]['estado']=self::ESTADOS[0];
						$fifo[$tiempo][$proceso]['proceso']=$formulario[$proceso]["nombre"];

						$estado[$tiempo][self::ESTADOS[0]]=true;
					}
					else{
						$fifo[$tiempo][$proceso]['estado']=self::ESTADOS[1];
						$fifo[$tiempo][$proceso]['proceso']=$formulario[$proceso]["nombre"];
					}
				}




				#Listo
				//Si el tiempo anterior esta en listo y no hay nada ejecutandose pasa a ejecución
				if($fifo[$tiempo_penultimo][$proceso]['estado']==self::ESTADOS[2]){

					//si no hay ninguno en SO ni en Ejecución ni en listo
					if(!$estado[$tiempo][self::ESTADOS[0]] and !$estado[$tiempo][self::ESTADOS[3]] ){
						$fifo[$tiempo][$proceso]['estado']=self::ESTADOS[0];
						$fifo[$tiempo][$proceso]['proceso']=$formulario[$proceso]["nombre"];

						$estado[$tiempo][self::ESTADOS[0]]=true;
					}
					else{
						$fifo[$tiempo][$proceso]['estado']=self::ESTADOS[2];
						$fifo[$tiempo][$proceso]['proceso']=$formulario[$proceso]["nombre"];

						$estado[$tiempo][self::ESTADOS[2]]=true;
					}

				}




				#Ejecución
				//Si en el tiempo penultimo está en ejecucion, sigue en ejecucion hasta  
				if($fifo[$tiempo_penultimo][$proceso]['estado']==self::ESTADOS[3]){
					
					//Hace un bucle de la cantidad de rafagas y va restando a cada array un valor hasta que este queda en cero, no se recorre todo el array, solo uno a la vez y parando.					
					foreach ($formulario[$proceso]["selectrafaga"] as $key => $value) {

						if($value > 0){
							$value=$value-1;
							$formulario[$proceso]["selectrafaga"][$key]=$value;

							$fifo[$tiempo][$proceso]['estado']=self::ESTADOS[3];
							$fifo[$tiempo][$proceso]['proceso']=$formulario[$proceso]["nombre"];

							$estado[$tiempo][self::ESTADOS[3]]=true;

							break;
						}

						//Una ves que queda en cero se borra ese array y se pasa el tiempo a sistema operativo
						if($value == 0){


							unset($formulario[$proceso]["selectrafaga"][$key]);

							$fifo[$tiempo][$proceso]['estado']=self::ESTADOS[0];
							$fifo[$tiempo][$proceso]['proceso']=$formulario[$proceso]["nombre"];

							$estado[$tiempo][self::ESTADOS[0]]=true;

							break;
						}
					}				
				}



				#Bloqueado
				if($fifo[$tiempo_penultimo][$proceso]['estado']==self::ESTADOS[4]){

					
					foreach ($tiempo_es[$proceso] as $key => $value) {

						if($value > 0){
							$value=$value-1;
							
							$tiempo_es[$proceso][$key]=$value;
							$fifo[$tiempo][$proceso]['estado']=self::ESTADOS[4];
							$fifo[$tiempo][$proceso]['proceso']=$formulario[$proceso]["nombre"];

							$estado[$tiempo][self::ESTADOS[4]]=true;

							break;
						}

						if($value == 0){

							//si no hay ninguno en SO ni en Ejecución
							if(!$estado[$tiempo][self::ESTADOS[0]] and !$estado[$tiempo][self::ESTADOS[3]] and !$estado[$tiempo_penultimo][self::ESTADOS[3]] and !$estado[$tiempo_penultimo][self::ESTADOS[2]]  and !$estado[$tiempo_antepenultimo][self::ESTADOS[2]]){
								
								unset($tiempo_es[$proceso][$key]);

								$fifo[$tiempo][$proceso]['estado']=self::ESTADOS[0];
								$fifo[$tiempo][$proceso]['proceso']=$formulario[$proceso]["nombre"];

								$estado[$tiempo][self::ESTADOS[0]]=true;

								break;

							}
							else{
								$fifo[$tiempo][$proceso]['estado']=self::ESTADOS[4];
								$fifo[$tiempo][$proceso]['proceso']=$formulario[$proceso]["nombre"];

								$estado[$tiempo][self::ESTADOS[4]]=true;

								break;
							}
							
						}
					}				

					
				}
			
			}#procesos

		}#Tiempo

		return $fifo;

	}#fifo

	public function generar_tabla_vertical($bcp){

		$tabla='<table class="table table-bordered table-dark">
		<tr>
			<th>Tiempo</th>
			<th>SO</th>
			<th>Nuevo</th>
			<th>Listo</th>
			<th>Ejecución</th>
			<th>Bloqueado</th>
			<th>Terminado</th>
		</tr>';

		$SO="";
		$nuevo="";
		$listo="";
		$ejecucion="";
		$bloqueado="";
		$terminado="";

		foreach ($bcp as $tiempo => $value) {
			
			foreach ($bcp[$tiempo] as $proceso => $estado) { 

				if($bcp[$tiempo][$proceso]['estado']==self::ESTADOS[0]){
					$SO=$bcp[$tiempo][$proceso]['proceso'];
				}				

				if($bcp[$tiempo][$proceso]['estado']==self::ESTADOS[1]){
					$nuevo=$bcp[$tiempo][$proceso]['proceso'];
				}

				if($bcp[$tiempo][$proceso]['estado']==self::ESTADOS[2]){
					$listo.=$bcp[$tiempo][$proceso]['proceso'];
				}

				if($bcp[$tiempo][$proceso]['estado']==self::ESTADOS[3]){
					$ejecucion=$bcp[$tiempo][$proceso]['proceso'];
				}

				if($bcp[$tiempo][$proceso]['estado']==self::ESTADOS[4]){
					$bloqueado.=$bcp[$tiempo][$proceso]['proceso'];
				}

				if($bcp[$tiempo][$proceso]['estado']==self::ESTADOS[5]){
					$terminado=$bcp[$tiempo][$proceso]['proceso'];
				}

				
			}

			$tabla.='
				<tr>
					<td>'.$tiempo.'</td>
					<td class="P'.$SO.'">'.$SO.'</td>
					<td class="P'.$nuevo.'">'.$nuevo.'</td>
					<td class="P'.$listo.'">'.$listo.'</td>
					<td class="P'.$ejecucion.'">'.$ejecucion.'</td>
					<td class="P'.$bloqueado.'">'.$bloqueado.'</td>
					<td class="P'.$terminado.'">'.$terminado.'</td>
				</tr>';

			unset($SO,$nuevo,$listo,$ejecucion,$bloqueado,$terminado);
		}

		$tabla.="</table>";

		return $tabla;
	}#tabla


		public function generar_tabla_horizontal($bcp){

		$tabla='<table class="table table-bordered table-dark table-responsive">';

		$columnas='<td>Tiempo</td>';
		
		$s='<td class="P">SO</td>';
		$n='<td class="P">Nuevo</td>';
		$l='<td class="P">Listo</td>';
		$e='<td class="P">Ejecución</td>';
		$b='<td class="P">Bloqueado</td>';
		$t='<td class="P">Terminado</td>';

		$SO="";
		$nuevo="";
		$listo="";
		$ejecucion="";
		$bloqueado="";
		$terminado="";

		foreach ($bcp as $tiempo => $value) {
			
			
			$columnas.='<td>'.$tiempo.'</td>';

			foreach ($bcp[$tiempo] as $proceso => $estado) { 

				if($bcp[$tiempo][$proceso]['estado']==self::ESTADOS[0]){
					$SO=$bcp[$tiempo][$proceso]['proceso'];
				}				

				if($bcp[$tiempo][$proceso]['estado']==self::ESTADOS[1]){
					$nuevo=$bcp[$tiempo][$proceso]['proceso'];
				}

				if($bcp[$tiempo][$proceso]['estado']==self::ESTADOS[2]){
					$listo.=$bcp[$tiempo][$proceso]['proceso'];
				}

				if($bcp[$tiempo][$proceso]['estado']==self::ESTADOS[3]){
					$ejecucion=$bcp[$tiempo][$proceso]['proceso'];
				}

				if($bcp[$tiempo][$proceso]['estado']==self::ESTADOS[4]){
					$bloqueado.=$bcp[$tiempo][$proceso]['proceso'];
				}

				if($bcp[$tiempo][$proceso]['estado']==self::ESTADOS[5]){
					$terminado=$bcp[$tiempo][$proceso]['proceso'];
				}
			}

			$s.='<td class="P'.$SO.'">'.$SO.'</td>';
			$n.='<td class="P'.$nuevo.'">'.$nuevo.'</td>';
			$l.='<td class="P'.$listo.'">'.$listo.'</td>';
			$e.='<td class="P'.$ejecucion.'">'.$ejecucion.'</td>';
			$b.='<td class="P'.$bloqueado.'">'.$bloqueado.'</td>';
			$t.='<td class="P'.$terminado.'">'.$terminado.'</td>';

			unset($SO,$nuevo,$listo,$ejecucion,$bloqueado,$terminado);
		}

		$tabla.='<thead>'.$columnas.'</thead>';
		$tabla.='<tbody>
			<tr>'.$s.'</tr>
			<tr>'.$n.'</tr>
			<tr>'.$l.'</tr>
			<tr>'.$e.'</tr>
			<tr>'.$b.'</tr>
			<tr>'.$t.'</tr>
		</tbody>';
		$tabla.="</table>";

		return $tabla;
	}#tabla


	public function tiempo_ejecucion_rr($formulario)
	{
		$tiempo_ej;
		$count;
		$tiempo_es=array();
		//$resultado;
		#bucle de procesos
		for ($i=0; $i < count($formulario); $i++) { 

			#bucle rafagas
			for ($j=0; $j < count($formulario[$i]["selectrafaga"]); $j++) { 

				//variable contadora de arrays nuevos
				if(!empty($tiempo_ej[$i])){
					$count=sizeof($tiempo_ej[$i]);
				}
				else{
					$count=0;
				}

				//Si la rafaga es menor al tiempo Round Robin
				if($formulario[$i]["selectrafaga"][$j] > self::TIMEPO_EJECUCION_RR){
		
					while($formulario[$i]["selectrafaga"][$j] > 0) { 
						
						if($formulario[$i]["selectrafaga"][$j] > self::TIMEPO_EJECUCION_RR){

							$tiempo_ej[$i][$count] = self::TIMEPO_EJECUCION_RR;
							$formulario[$i]["selectrafaga"][$j]=$formulario[$i]["selectrafaga"][$j]-self::TIMEPO_EJECUCION_RR;

							$tiempo_es[$i][$count]=null;
							

						}
						else{

							
							
							if(count($formulario[$i]["selectrafaga"])==($j+1)){
								$tiempo_ej[$i][$count]=$formulario[$i]["selectrafaga"][$j];
								$formulario[$i]["selectrafaga"][$j]=0;
								$tiempo_es[$i][$count]=null;
							}
							else{
								$tiempo_ej[$i][$count]=$formulario[$i]["selectrafaga"][$j];

								$tiempo_es[$i][$count]=self::TIEMPO_ES;
								$formulario[$i]["selectrafaga"][$j]=0;
							}
							
						}
						
						$count++;

					}					
										
				}else{

					if(count($formulario[$i]["selectrafaga"])==($j+1)){
						$tiempo_ej[$i][$count] = $formulario[$i]["selectrafaga"][$j];
						$tiempo_es[$i][$count] = null;
								
					}
					else{
						$tiempo_ej[$i][$count] = $formulario[$i]["selectrafaga"][$j];
						$tiempo_es[$i][$count] =self::TIEMPO_ES;
					}
					
					
				}


			}

			unset($formulario[$i]["selectrafaga"]);
			$formulario[$i]["selectrafaga"]=$tiempo_ej[$i];

		}

		//$resultado=array($formulario,$tiempo_es);

		return array('formulario'=>$formulario,'tiempo_es'=>$tiempo_es);


	}

	


	



	public function roundrobin($formulario)
	{

		
	
		//Si el tiempo de entrada se repite se suma uno
		$formulario = self::no_repetir_entradas($formulario);

		//Modifoco los tiempos de las rafagas para round robin
		$arrays = self::tiempo_ejecucion_rr($formulario);

		$formulario=$arrays['formulario'];
		$tiempo_es=$arrays['tiempo_es'];
		//print_r($arrays);
		#die;
		//El primero que entra es el primero que sale.
		$roundrobin=array();
		$estado=array();

		$tiempo_penultimo;
		$tiempo_antepenultimo;
		$tiempo_proximo;
		$cola_listo;



		//print_r($formulario);	

		#Bucle de tiempo total
		for ($tiempo=0; $tiempo < self::TIEMPO_MAXIMO; $tiempo++) { 
			
			if($tiempo>0){
				$tiempo_penultimo=$tiempo-1;				
			}

			if($tiempo>1){
				$tiempo_antepenultimo=$tiempo-2;
			}

			if($tiempo>0){
				$tiempo_proximo=$tiempo+1;				
			}
			
			/*
			Array
			(
			    [0] => Array
			        (
			            [nombre] => P0
			            [entrada] => asdasd
			            [cantrafaga] => 2
			            [selectrafaga] => Array
			                (
			                    [0] => 2
			                    [1] => 3
			                )

			        )

			    [1] => Array
			        (
			            [nombre] => P1
			            [entrada] => asdas
			            [cantrafaga] => 2
			            [selectrafaga] => Array
			                (
			                    [0] => sd
			                    [1] => asda
			                )

			        )

			)
			*/

			#Bucle de procesos		
			for ($proceso=0; $proceso < count($formulario); $proceso++) {				

				#Entra el proceso
				if($formulario[$proceso]['entrada']==$tiempo)
				{
					if(!$roundrobin[$tiempo_penultimo][$proceso]['estado']){

						if(!$estado[$tiempo][self::ESTADOS[0]] and !$estado[$tiempo][self::ESTADOS[3]]){
								$roundrobin[$tiempo][$proceso]['estado']=self::ESTADOS[0];
								$roundrobin[$tiempo][$proceso]['proceso']=$formulario[$proceso]["nombre"];

								$estado[$tiempo][self::ESTADOS[0]]=true;
							}
							else{

								$formulario[$proceso]['entrada']=$formulario[$proceso]['entrada']+1;

							}
						

					}
				}



				#SO
				//Si en el tiempo anterior está en SO revisar el antepenultimo y se decide que estado va
				if($roundrobin[$tiempo_penultimo][$proceso]['estado']==self::ESTADOS[0]){

					//Si el tiempo antepenultimo es vacio paso a nuevo
					if(empty($tiempo_antepenultimo)){
						$roundrobin[$tiempo][$proceso]['estado']=self::ESTADOS[1];
						$roundrobin[$tiempo][$proceso]['proceso']=$formulario[$proceso]["nombre"];

						$estado[$tiempo][self::ESTADOS[1]]=true;
					}

					//Si tiene tiempo antepenultimo pero no se a guardado en ningun lado
					if(empty($roundrobin[$tiempo_antepenultimo][$proceso]['estado'])){
						$roundrobin[$tiempo][$proceso]['estado']=self::ESTADOS[1];
						$roundrobin[$tiempo][$proceso]['proceso']=$formulario[$proceso]["nombre"];

						$estado[$tiempo][self::ESTADOS[1]]=true;
					}

					//Si el tiempo antenpenultimo fue nuevo pasa a listo
					if($roundrobin[$tiempo_antepenultimo][$proceso]['estado']==self::ESTADOS[1]){
						$roundrobin[$tiempo][$proceso]['estado']=self::ESTADOS[2];
						$roundrobin[$tiempo][$proceso]['proceso']=$formulario[$proceso]["nombre"];

						$estado[$tiempo][self::ESTADOS[2]]=true;
					}

					//Si el tiempo antenpenultimo fue listo pasa a ejecucion
					if($roundrobin[$tiempo_antepenultimo][$proceso]['estado']==self::ESTADOS[2]){

						//si no hay ninguno en SO ni en Ejecución pasa a ejecución					
						foreach ($formulario[$proceso]["selectrafaga"] as $key => $value) {

							if($value > 0){
								$value=$value-1;
								$formulario[$proceso]["selectrafaga"][$key]=$value;

								$roundrobin[$tiempo][$proceso]['estado']=self::ESTADOS[3];
								$roundrobin[$tiempo][$proceso]['proceso']=$formulario[$proceso]["nombre"];

								$estado[$tiempo][self::ESTADOS[3]]=true;

								break;
							}
						}

						
					}

					//Si el tiempo antenpenultimo fue ejecucion 
					if($roundrobin[$tiempo_antepenultimo][$proceso]['estado']==self::ESTADOS[3]){

						$cantidad_tes=count($tiempo_es[$proceso]);
						foreach ($tiempo_es[$proceso] as $key => $value) {

							if($value > 0){
								$value=$value-1;
									
								$tiempo_es[$proceso][$key]=$value;
								$roundrobin[$tiempo][$proceso]['estado']=self::ESTADOS[4];
								$roundrobin[$tiempo][$proceso]['proceso']=$formulario[$proceso]["nombre"];

								$estado[$tiempo][self::ESTADOS[4]]=true;

								break;
							}

							
							//si es nulo
							if(is_null($value)){	

								//si es la ultima rafaga
								if($cantidad_tes==1){
									$roundrobin[$tiempo][$proceso]['estado']=self::ESTADOS[5];
									$roundrobin[$tiempo][$proceso]['proceso']=$formulario[$proceso]["nombre"];
									$estado[$tiempo][self::ESTADOS[5]]=true;
									unset($tiempo_es[$proceso][$key]);
									break;
								}//si no es la ultima rafaga
								else{
									$roundrobin[$tiempo][$proceso]['estado']=self::ESTADOS[2];
									$roundrobin[$tiempo][$proceso]['proceso']=$formulario[$proceso]["nombre"];

									$estado[$tiempo][self::ESTADOS[2]]=true;

									unset($tiempo_es[$proceso][$key]);
									break;
								}
								


							}


						}

						
					}


					//Si el tiempo antepenultimo es bloqueado debe ir a listo
					if($roundrobin[$tiempo_antepenultimo][$proceso]['estado']==self::ESTADOS[4]){

						$roundrobin[$tiempo][$proceso]['estado']=self::ESTADOS[2];
						$roundrobin[$tiempo][$proceso]['proceso']=$formulario[$proceso]["nombre"];

						$estado[$tiempo][self::ESTADOS[2]]=true;
					}

					
				}		




				#Nuevo
				//Si en el tiempo anterior está en Nuevo entra a SO
				if($roundrobin[$tiempo_penultimo][$proceso]['estado']==self::ESTADOS[1]){

					//si no hay ninguno en SO ni en Ejecución
					if(!$estado[$tiempo][self::ESTADOS[0]] and !$estado[$tiempo][self::ESTADOS[3]]){
						$roundrobin[$tiempo][$proceso]['estado']=self::ESTADOS[0];
						$roundrobin[$tiempo][$proceso]['proceso']=$formulario[$proceso]["nombre"];

						$estado[$tiempo][self::ESTADOS[0]]=true;
					}
					else{
						$roundrobin[$tiempo][$proceso]['estado']=self::ESTADOS[1];
						$roundrobin[$tiempo][$proceso]['proceso']=$formulario[$proceso]["nombre"];
					}
				}




				#Listo
				//Si el tiempo anterior esta en listo y no hay nada ejecutandose pasa a ejecución
				if($roundrobin[$tiempo_penultimo][$proceso]['estado']==self::ESTADOS[2]){

					//si no hay ninguno en SO ni en Ejecución ni en listo
					if(!$estado[$tiempo][self::ESTADOS[0]] and !$estado[$tiempo][self::ESTADOS[3]] and !$estado[$tiempo_penultimo][self::ESTADOS[3]]){
						$roundrobin[$tiempo][$proceso]['estado']=self::ESTADOS[0];
						$roundrobin[$tiempo][$proceso]['proceso']=$formulario[$proceso]["nombre"];

						$estado[$tiempo][self::ESTADOS[0]]=true;

						$estado[$tiempo_proximo][self::ESTADOS[3]]=true;

						//print_r($roundrobin[$tiempo_antepenultimo]);
						//die;
					}
					else{
						$roundrobin[$tiempo][$proceso]['estado']=self::ESTADOS[2];
						$roundrobin[$tiempo][$proceso]['proceso']=$formulario[$proceso]["nombre"];

						$estado[$tiempo][self::ESTADOS[2]]=true;
					}

				}




				#Ejecución
				//Si en el tiempo penultimo está en ejecucion, sigue en ejecucion hasta  
				if($roundrobin[$tiempo_penultimo][$proceso]['estado']==self::ESTADOS[3]){
					
					//Hace un bucle de la cantidad de rafagas y va restando a cada array un valor hasta que este queda en cero, no se recorre todo el array, solo uno a la vez y parando.					
					foreach ($formulario[$proceso]["selectrafaga"] as $key => $value) {

						if($value > 0){
							$value=$value-1;
							$formulario[$proceso]["selectrafaga"][$key]=$value;

							$roundrobin[$tiempo][$proceso]['estado']=self::ESTADOS[3];
							$roundrobin[$tiempo][$proceso]['proceso']=$formulario[$proceso]["nombre"];

							$estado[$tiempo][self::ESTADOS[3]]=true;

							break;
						}

						//Una ves que queda en cero se borra ese array y se pasa el tiempo a sistema operativo
						if($value == 0){


							unset($formulario[$proceso]["selectrafaga"][$key]);
							//$proximo=$key+1;

							//Si existe un tiempo de entrada y salida que coincida con la key va a entrada y salida
							if($tiempo_es[$proceso][$key]){

								$roundrobin[$tiempo][$proceso]['estado']=self::ESTADOS[0];
								$roundrobin[$tiempo][$proceso]['proceso']=$formulario[$proceso]["nombre"];
								//$roundrobin[$tiempo][$proceso]['desalojo']=false;

								$estado[$tiempo][self::ESTADOS[0]]=true;
							}
							//si no existe ese tiempo de entrada y salida va a desalojo
							else{
								$roundrobin[$tiempo][$proceso]['estado']=self::ESTADOS[0];
								$roundrobin[$tiempo][$proceso]['proceso']=$formulario[$proceso]["nombre"];
								//$roundrobin[$tiempo][$proceso]['desalojo']=true;

								$estado[$tiempo][self::ESTADOS[0]]=true;
							}

							

							break;
						}
					}				
				}



				#Bloqueado
				if($roundrobin[$tiempo_penultimo][$proceso]['estado']==self::ESTADOS[4]){

					
					foreach ($tiempo_es[$proceso] as $key => $value) {

						if($value > 0){
							$value=$value-1;
							
							$tiempo_es[$proceso][$key]=$value;
							$roundrobin[$tiempo][$proceso]['estado']=self::ESTADOS[4];
							$roundrobin[$tiempo][$proceso]['proceso']=$formulario[$proceso]["nombre"];

							$estado[$tiempo][self::ESTADOS[4]]=true;

							break;
						}

						if($value == 0){

							//si no hay ninguno en SO ni en Ejecución ni...
							if(!$estado[$tiempo][self::ESTADOS[0]] and !$estado[$tiempo][self::ESTADOS[3]] and !$estado[$tiempo_penultimo][self::ESTADOS[3]] and !$estado[$tiempo_penultimo][self::ESTADOS[2]]  and !$estado[$tiempo_antepenultimo][self::ESTADOS[2]]){
								
								unset($tiempo_es[$proceso][$key]);

								$roundrobin[$tiempo][$proceso]['estado']=self::ESTADOS[0];
								$roundrobin[$tiempo][$proceso]['proceso']=$formulario[$proceso]["nombre"];

								$estado[$tiempo][self::ESTADOS[0]]=true;

								break;

							}
							else{
								$roundrobin[$tiempo][$proceso]['estado']=self::ESTADOS[4];
								$roundrobin[$tiempo][$proceso]['proceso']=$formulario[$proceso]["nombre"];

								$estado[$tiempo][self::ESTADOS[4]]=true;

								break;
							}
							
						}
					}				

					
				}
			
			}#procesos

		}#Tiempo

		return $roundrobin;

	}#roundrobin



}#clase

?>