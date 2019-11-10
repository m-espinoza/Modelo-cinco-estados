<?php
//traigo las clases del controlador
require_once("controlador/ctrl_clases.php");

$diseno=new DISENO;
$pestanas=array("FIFO","RoundRobin");
//print_r($pestanas);
$menu=$diseno->menu($pestanas);

$diseno->plantilla_inicio(
	"Modelo de cinco estados",
	"",
	"",
	"Manuel Espinoza",
	"Arquitectura 2",
	"Modelo de cinco estados",
	$menu);

?>
<div class="container" style="display: none">
	
	<div class="row justify-content-md-center ">
		<div class="col-md-4">
			<?=$diseno->cantidad_procesos(4)?>
		</div>
	</div>
	<div class="modelo"></div>
	<div id="proceso"></div>
	<input type="hidden" name="politica" id="politica" value="">
	
	<div id="respuesta"></div>
</div>
	<?

	
	


$diseno->librerias();
//$diseno->plantilla_fin();
?>
