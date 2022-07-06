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
		
		#listadosubiendo .archivo[estado="terminado"] img{
			display:none;
			
		}
		
		#listadosubiendo .archivo[estado="terminado"]{
		  background-color: rgb(50,150,50);
		  color: #000;
		  font-size: 10px;
		}
		
		
		#indicegrupos a{
				color:#000;
				cursor:pointer;
		}
		#indicegrupos a:hover{
			color:#08afd9;
		}
		#indicedistritos a{
				color:#000;
		}
		#indicedistritos a:hover{
			color:#08afd9;
		}
		
		
		#formdistrito #paleta label{
				width:50px;
		}
		.distrito{
			page-break-after: always;
			page-break-inside: avoid;
		}
		.distrito{
			border:3px solid #000;
		}
		.ol-overlaycontainer-stopevent{
			display:none;
			}
		@media print{
			body{
				background-image:unset;
			}
			#page{
					border:none;
			}
			#pageborde{
					border:none;
					background-color:transparent;
			}	
		}
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
			<h1 id='titulopagina'></h1>
			<p id='dataproyecto'></p>
			<p>En esta página usted puede acceder a la información técnica de cada zona.</p>
			<p>Los usuarios haibilitados pueden editar el contenido.</p>
		
			<a target='blank' onclick='window.open("./mapa_zonificacion.php?id="+_COTID+"&cod="+_COTCOD , "_blank")'>abrir mapa de consulta</a>
			<a target='blank' onclick='window.open("./mapa_participacion.php?id="+_COTID+"&cod="+_COTCOD , "_blank")'>abrir mapa participativo</a>
			<a target='blank' onclick='window.open("./mapa_revisa_participacion.php?id="+_COTID+"&cod="+_COTCOD , "_blank")'>abrir mapa de revisión de participaciones</a>
			<a target='blank' onclick='window.open("./listado_parcelas.php?id="+_COTID+"&cod="+_COTCOD , "_blank")'>abrir listado de parcelas</a>
			<br>
			<a onclick='mostrarContenidosBase_Texto()'>ver en modo texto</a>
			<a onclick='mostrarContenidosBase()'>ver en modo edición</a>
			<a onclick='mostrarContenidosBase_Fichas()'>ver en modo fichas</a>
			<a onclick='mostrarContenidosBase_Tabla()'>ver en modo tabla</a>
			<a onclick='mostrarNombres_Tabla()'>ver en modo tabla de nombres</a>
			<br>
			<input onclick='crearDistrito()' type='submit' value='agregar un nuevo tipo'>
			<input onclick='activaFormShape()' type='submit' value='genera geometrías desde shp'>
			<input onclick='regenerarSLD()' type='submit' value='regenerar color parcelas sld'>			
			<input onclick='duplicarProyecto()' type='submit' value='duplicar proyecto'>			
			<input onclick='eliminarZonas()' type='submit' value='eliminar geometrías de zonas'>
			<input onclick='eliminarParcelas()' type='submit' value='eliminar geometrías de parcelas'>			
			<input onclick='descargarZonas()' type='submit' value='descargar shapefile'>
			
		</div>
		
		<div  id='contenido' class='contenido'>
		
		</div>	
	</div>
	</div>




	<div id='formshapefile' class='formulario'>
	
		<div id='botonera'>
			<a onclick='cerrarForm(this.parentNode.parentNode.getAttribute("id"))'>cerrar</a>
		</div>
		
		<h1>Subir geometrías desde archivos shapefile</h1>
		<input type='radio' name='contenido' onchange='modoCandidato();' value='zonas' checked>zonas<br>
		<input type='radio' name='contenido' onchange='modoCandidato();' value='jurisdiccion' >jurisdicción (en desarrollo)<br>
		<input type='radio' name='contenido' onchange='modoCandidato();' value='parcelas' >parcelas (en desarrollo)<br>
		<input type='radio' name='contenido' onchange='modoCandidato();' value='calles' >calles (en desarrollo)<br>
		<br>
		<input type='radio' name='modo' value='agrega' checked>incorporar a la geometría previa<br>
		<input type='radio' name='modo' value='reemplaza' disabled>reemplazar la geometría previa<br>
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
		
		
		<table id='paleta'> 
			<td>
		<label for='colorgrupo'>Color del grupo</label><br>
		<input disabled type='color' name='colorgrupo'><br>
		</td>
		<td>
		<label for='co_color'>Color</label><br>
		<input type='color' name='co_color'><br>
		</td>
		<td>
		<label for='color_mezcla'>Mezcla automática</label><br>
		<input disabled type='color' name='color_mezcla'><br>
		</td>
		<td>
		<label for='color_final'>Color final</label><br>
		<input  type='color' name='color_final'><br>
		usar:<input  type='checkbox' name='color_final_definido'>(desmarcado usa mezcla en los mapas)<br>
		</td>
		</table>
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

function PreprocesarRespuesta(_response){
	_res=$.parseJSON(_response);
	for(_nm in _res.mg){alert(_res.mg[_nm]);}
	return _res;	
}

var _HabilitadoEdicion='si';
/*
var _COTID='15';
var _COTCOD='hUeDTp8';
var _COTID='22';
var _COTCOD='v8wevKw';
var _COTID='23';
var _COTCOD='yLSkHqT';
var _COTID='24';
var _COTCOD='LHuKeej';
var _COTID='25';
var _COTCOD='k56G7hI';
var _COTID='26';
var _COTCOD='aR5PFmF';
*/
var _COTID='27';
var _COTCOD='kIz23JT';

var _get_id='<?php echo $_GET["id"];?>';
var _get_cod='<?php echo $_GET["cod"];?>';
if(_get_id!=''){_COTID=_get_id;}
if(_get_cod!=''){_COTCOD=_get_cod;}

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
		
