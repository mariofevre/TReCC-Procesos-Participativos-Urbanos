<?php
ini_set('display_errors',true);
include('./includes/header.php');
?><!DOCTYPE html>

	<head>
		
		<?php include("./includes/meta.php");?>
		<title>POT QUILMES - Zonificación</title>
		<link href="css/treccppu.css" rel="stylesheet" type="text/css">
		<link href="css/redaccion.css" rel="stylesheet" type="text/css">	
		
		
		 <?php
		if($_SESSION['modo']=='pdf'){
			echo '<link href="css/redaccion.css" rel="stylesheet" type="text/css">';
		}
		?>
	
	<style type='text/css'>
	</style>
	
	<style type='text/css' id='modoedicion'>
		
		
		
	</style>
</head>

<body>

		<div class='recuadro' id='recuadro2'>
			<br><br>Descargar: 
			<a target='recuadro3' href='./descargatabla.php'>descargar tabla csv</a>
			<br><br>Índice de grupos: 
			<div id='indicegrupos'>
			<div id='lista'></div>
			</div>
			<br><br>Índice de tipos: 
			<div id='indicedistritos'>
			<div id='lista'></div>
			</div>
		</div>
		
		
		<div id="pageborde">
	<div id="page">
		<div id="menu">
			<h1>Zonas</h1>
			<p>En esta página usted puede acceder a la información técnica de cada zona.</p>
			<p>Los usuarios haibilitados pueden editar el contenido.</p>
		
			<a target='blank' href='./mapa_zonificacion.php'>ver mapa</a>
			<a onclick="alert('funcion en desarrollo')">ver en modo texto</a>
			<a onclick='mostrarContenidosBase()'>ver en modo edición</a>
			<a onclick="alert('funcion en desarrollo')">ver en modo fichas</a>
			<a onclick='mostrarContenidosBase_Tabla()'>ver en modo tabla</a>
			<a onclick='mostrarNombres_Tabla()'>ver en modo tabla de nombres</a>

			
			<input onclick='crearDistrito()' type='submit' value='agregar un nuevo tipo'>
			<input onclick='activaFormShape()' type='submit' value='genera zonas desde shp'>
			
			
		</div>
		
		<div  id='contenido' class='contenido'>
		
		</div>	
	</div>
	</div>




	<div id='formshapefile' class='formulario'>
	
		<div id='botonera'>
			<a onclick='cerrarForm(this.parentNode.parentNode.getAttribute("id"))'>cerrar</a>
		</div>
		
		<h1>Subir geopmetrías desde archivos shapefile</h1>
		<input type='radio' name='contenido' checked>zonas<br>
		<input type='radio' name='contenido' disabled>jurisdicción (en desarrollo)<br>
		<br>
		<input type='radio' name='modo' checked>incorporar a zonas definidas<br>
		<input type='radio' name='modo' disabled>reemplazar zonas definidas<br>
		<div id='candidatos'></div>
		<div id='listadosubido'></div>
		<div id='listadosubiendo'></div>
		<div id='carga'>    
			<label class='upload'>
			<span class='upload' 
					ondrop='event.preventDefault();dropHandler(event);' 
					ondragover='drag_over(event,this)' 
					ondragleave='drag_out(event,this)'
			> - arrastre archivos aquí - </span>
		<!--	
			<input id='uploadinput' class='uploadinput' type='file' name='archivo_FI_documento' value='' onchange='subirDocumentoMPP(this);'></label>			
		-->
		</div>
		
		<div id='contenidos'></div>
		
		<div id='estado'>
			<pre>
				<code>

				</code>
			</pre>
		</div>
		
	</div>



	<div id='formredaccion' class='formulario'>
	
		<div id='botonera'>
			<a onclick='cerrarForm(this.parentNode.parentNode.getAttribute("id"))'>cerrar</a>
			<a onclick='guardarRedaccion()'>guardar</a>
		</div>
		
		<h1 id='distrito'></h1>
		<h2 id='seccion'></h2>
		
		<input type='hidden' name='iddist'>
		<input type='hidden' name='idsecc'>
		<textarea name='texto'></textarea>
		
	</div>


	<div id='formdistrito' class='formulario'>
	
		<div id='botonera'>
			<a onclick='cerrarForm(this.parentNode.parentNode.getAttribute("id"))'>cerrar</a>
			<a onclick='guardarDistrito()'>guardar</a>
			<a class='eliminar' onclick='eliminarDistrito()'>eliminar</a>
		</div>
		
				
		<input type='hidden' name='iddist'>
				
		<label for='nombre'>Nombre</label><br>
		<div id='grupo'>
			<label for='cot_grupos_nombre'>grupo</label>			
			<input 
				type='hidden' 
				id='Iid_p_cot_grupos_id' 
				name='id_p_cot_grupos_id'
			><input 
				name='cot_grupos_nombre-n' 
				id='Icot_grupos_nombre-n' 
				onblur='setTimeout(vaciarOpcionares(event,this),100);if(this.value==""){this.value="-";}' 
				onkeyup='filtrarOpciones(this,event);' 
				onfocus='opcionarDef(this);'><div class='auxopcionar'
			>
				<div class='contenido'></div>
			</div>
			<input name='cot_grupos_descripcion-n'>		
		</div>
		
		<div id='distrito'>
			<label for='nom_clase'>clase</label>
			<input name='nom_clase'>
			<input name='des_clase'>		
		</div>		
		<label for='orden'>Orden de aparición</label><input name='orden'>
		
		<label for='co_color'>Color</label><br>
		<input type='color' name='co_color'><br>
		
	</div>
	
	<div id='formgrupo' class='formulario'>
	
		<div id='botonera'>
			<a onclick='cerrarForm(this.parentNode.parentNode.getAttribute("id"))'>cerrar</a>
			<a onclick='guardarGrupo()'>guardar</a>
			<a class='eliminar' onclick='eliminarGrupo()'>eliminar</a>
		</div>
		
		<input type='hidden' name='idgrupo'>
		<p><span id='cant_dist'></span> Distritos asociados a este grupo</p>
		<label for='nombre'>Código</label><br>
		<input name='nombre'><br>
		
		<label for='descripcion'>Nombre</label><br>
		<input name='descripcion'><br>
		
		<label for='co_color'>Color</label><br>
		<input type='color' name='co_color'><br>
		
	</div>
	
	<div id='portamapagrande' class='portamapagrande' estado='inactivo'>
		<a onclick='cerrarMapaGrande()'><img src='./img/fullscreen.jpg'></a>
	</div>
		
<?php include('./includes/pie.php');?>

<script type="text/javascript" src="./js/jquery/jquery-3.6.0.js"></script>
<script type="text/javascript" src="./js/openlayers_5_3_0/build/ol.js"></script>

<script type="text/javascript" src="./redaccion_js_consultas.js"></script>
<script type="text/javascript" src="./redaccion_js_muestra.js"></script>
<script type="text/javascript" src="./redaccion_js_interaccion.js"></script>

<script type="text/javascript">

function PreprocesarRespuesta(_response){_res=$.parseJSON(_response);return _res;}

var _HabilitadoEdicion='si';

var _COTID='1';
var _DataDistritos={};

var _Opciones={
	'cot_grupos':{}
	}
	
	
var _nFile=0;
var xhr={};

var _Maps={};

consultarContenidosBase();

</script>

</body>
		
