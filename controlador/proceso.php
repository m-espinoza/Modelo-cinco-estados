<?php
require_once("ctrl_clases.php");
$proceso=new PROCESO;
//print_r($_POST);
if($_POST['cantidad_procesos']>0){

	echo $proceso->formulario_proceso($_POST['cantidad_procesos']);
}

if($_POST['accion']=='procesos'){
	#print_r($_POST);
	#die;

	$campos = array();
	parse_str($_POST['campos'], $campos);
	


	//ORDENAMOS FORMLULARIO

	$formulario=$proceso->ordernar_formulario($_POST['cantidad'],$campos);
	//print_r($formulario);

	if(is_array($formulario)){

		if($_POST['politica']=="FIFO"){
			$fifo=$proceso->fifo($formulario);
			//print_r($formulario);
			//echo $proceso->generar_tabla_vertical($fifo);
			echo $proceso->generar_tabla_horizontal($fifo);
		}

		if($_POST['politica']=="ROUNDROBIN"){
			$roundrobin=$proceso->roundrobin($formulario);
			//print_r($roundrobin);
			//echo $proceso->generar_tabla_vertical($roundrobin);
			echo $proceso->generar_tabla_horizontal($roundrobin);
		}
		
	}
	else{
		echo $formulario;
	}
	

	//print_r($formulario);



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
	                    [0] => dasd
	                    [1] => asdas
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


	

}
?>