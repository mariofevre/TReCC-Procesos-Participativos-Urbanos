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


		#portamapagrande {
			display: none;
			position: relative;
			top: 0px;
			left: 0;
			height: auto;
			width: 780px;
			background-color: #fff;
			border: 1px solid #08afd9;
			box-shadow: 10px 10px 5px rgba(0,0,0,0.8);
			z-index: 100;
		}
		.mapagrande  {
			
			width: 770px;
			
		}
		
		#fichaDistrito[estado="activo"] {
			display: block;
		}
		#fichaDistrito {
			display: none;
			position: fixed;
			left: 2vw;
			bottom: 2vh;
			height: 400px;
			width: 400px;
			background-color: #fff;
			border: 1px solid #08afd9;
			box-shadow: 10px 10px 5px rgba(0,0,0,0.8);
			z-index: 100;
		}
		
		#fichaDistrito th{
			width:auto;
			
		}
		#fichaDistrito table{
			font-size:13px;
		}
		#fichaDistrito table th{
			font-size:16px;
		}
		#fichaDistrito table td{
			font-size:13px;
		}
	</style>
</head>

<body>	
		
	<div id="pageborde">
	<div id="page">			
		<div  id='contenido' class='contenido'>
			<h1>Propuestas de Zonificación</h1>
			
			<div id='portamapagrande' class='portamapagrande' estado='inactivo'>
				<a onclick='cerrarMapaGrande()'><img src='./img/fullscreen.jpg'></a>
			</div>
		
		</div>	
	</div>
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
	
	<div id='fichaDistrito' estado='inactivo' class='formulario'>
		<div id='botonera'><a onclick='cerrarForm(this.parentNode.parentNode.getAttribute("id"));mapaResaltarDitrito(-1)'>cerrar</a></div>
		<h1 id='tipo'></h1>
		<table></table>
	
	</div>

		
<?php include('./includes/pie.php');?>

<script type="text/javascript" src="./js/jquery/jquery-3.6.0.js"></script>
<script type="text/javascript" src="./js/openlayers_5_3_0/build/ol.js"></script>

<script type="text/javascript" src="./mapa_zonificacion_js_consultas.js"></script>
<script type="text/javascript" src="./mapa_zonificacion_js_muestra.js"></script>
<script type="text/javascript" src="./mapa_zonificacion_js_interaccion.js"></script>

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
		
