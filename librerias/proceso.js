$(document).ready(function(){

	//cad vez que inicio la pagina pongo el select de cantidad de procesos en cero
	$("#cantidad_procesos").val("");

	$(".nav-link").click(function(){
		//alert($(this).text());
		$("#politica").val($(this).text());

		$(".container").css("display","block");
		$(".modelo").text($(this).text());
	});


	//cantidad de procesos
	$("#cantidad_procesos").change(function(){
		var cantidad = $(this).val();
		if(cantidad){
			$.post( 
				"controlador/proceso.php",
				{ 'cantidad_procesos': cantidad},
				function( data ) {
				  //alert(data);
				  $("#proceso").html(data);
			});
		}
		else{
			$("#proceso").html("");
		}
	});

	//Procesar procesos
	$("#proceso").on("click","#Btn_procesar", function(){

		var campos = $(this).closest("form").serialize();
		var cantidad = $("#cantidad_procesos").val();
		var politica = $("#politica").val();

		$.post( 
			"controlador/proceso.php",
			{
				'accion':'procesos',
				'cantidad':cantidad,
				'politica':politica,
				campos
			},
			function( data ) {
			  //alert(data);
			  $("#respuesta").html(data);
			  scrollToID("respuesta",1000);
		});

	});

	//Cuando apreto enter simulo hacer click en el boton procesar
	$('body').keypress(function (e) {
	    if(e.which == 13){
	       	$( "#Btn_procesar" ).trigger( "click" );
	    }
	});
	



	$("#proceso").on("change",".rafagas",function(){		

		var rafagas = $(this).val();
		var input="";
		var name = $(this).closest("select").attr("data");		

		for (var i = 0; i < rafagas; i++) {
			input+='<input type="number" data="'+name+'" class="form-control" name="'+name+'-rafaga_'+i+'" id="rafaga_'+i+'" placeholder="Duración rafaga '+i+'">';
		}

		$(this).closest("fieldset").find(".contenedor_rafagas").html(input	);

	});


	function scrollToID(id, speed) {
        var offSet = 70;
        var obj = $('#' + id);
        if(obj.length){
          var offs = obj.offset();
          var targetOffset = offs.top - offSet;
          $('html,body').animate({ scrollTop: targetOffset }, speed);
        }
    }


	
});