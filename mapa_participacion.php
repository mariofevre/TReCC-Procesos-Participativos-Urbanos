<?php
/**
 * mapa_participacion.php
 * 
 * espacio html para explorar un mapa interactivo con funciones para registrar opiniones georeferenciadas.
 * 
*  @package    	TReCC(tm) Procesos Participativos Urbanos
* @author     	TReCC SA
* @author     	<mario@trecc.com.ar> <trecc@trecc.com.ar>
* @author    	www.trecc.com.ar  
* @copyright	2013 2022 TReCC SA
* @license    	http://www.gnu.org/licenses/gpl.html GNU AFFERO GENERAL PUBLIC LICENSE, version 3 (GPL-3.0)
* Este archivo es software libre: tu puedes redistriburlo 
* y/o modificarlo bajo los términos de la "GNU AFFERO GENERAL PUBLIC LICENSE" 
* publicada por la Free Software Foundation, version 3
* 
* Este archivo es distribuido por si mismo y dentro de sus proyectos 
* con el objetivo de ser útil, eficiente, predecible y transparente
* pero SIN NIGUNA GARANTÍA; sin siquiera la garantía implícita de
* CAPACIDAD DE MERCANTILIZACIÓN o utilidad para un propósito particular.
* Consulte la "GNU General Public License" para más detalles.
* 
* Si usted no cuenta con una copia de dicha licencia puede encontrarla aquí: <http://www.gnu.org/licenses/>.
*/

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
			overflow: auto;
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
		
		
		.switch {
		  position: relative;
		  display: inline-block;
		  width: 40px;
		  height: 24px;
		}

		.switch input { 
		  opacity: 0;
		  width: 0;
		  height: 0;
		}

		.slider {
		  position: absolute;
		  cursor: pointer;
		  top: 0;
		  left: 0;
		  right: 0;
		  bottom: 0;
		  background-color: #ccc;
		  -webkit-transition: .4s;
		  transition: .4s;
		}

		.slider:before {
		  position: absolute;
		  content: "";
		  height: 16px;
		  width: 16px;
		  left: 4px;
		  bottom: 4px;
		  background-color: white;
		  -webkit-transition: .4s;
		  transition: .4s;
		}

		input:checked + .slider {
		  background-color: #2196F3;
		}

		input:focus + .slider {
		  box-shadow: 0 0 1px #2196F3;
		}

		input:checked + .slider:before {
		  -webkit-transform: translateX(16px);
		  -ms-transform: translateX(16px);
		  transform: translateX(16px);
		}

		/* Rounded sliders */
		.slider.round {
		  border-radius: 24px;
		}

		.slider.round:before {
		  border-radius: 50%;
		}
		
		label > span {
			width: auto;
		}
		
		#formpropuesta[estado="activo"] {
			display: block;
		}
		#formpropuesta {
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
			overflow:auto;
		}
		
		#formpropuesta input{
				width:calc(100% - 8px);
		}
		#formpropuesta textarea{
				width:calc(100% - 8px);
				height:170px;
		}
		#formpropuesta label{
				width:auto;
		}
		.multiply{
		   mix-blend-mode: multiply;
		}
		
		.desaturada{
		   filter: grayscale(100%);
		 }
		.ol-overlaycontainer-stopevent{
			display:none;
		}

	</style>
</head>

<body>	
		
	<div id="pageborde">
	<div id="page">			
		<div  id='menu'>
			<h1 id='titulopagina'></h1>
			<p id='dataproyecto'></p>
		</div>
		<div  id='contenido' class='contenido'>
			<h1 id='titulopagina'></h1>
			<p id='dataproyecto'></p>
			<p>Te proponemos que uses este mapa interactivo para explorar el estado actual de la propuesta de zonificación y compuartas tu opinión sobre las zonas que te interesen.</p>


			<p>
			<span>Explorar</span>			
			<label class="switch">
			  <input id='activadibujo' type="checkbox" onchange='toogleEdit(this)' autocomplete="off">
			  <span class="slider round"></span>
			</label>
			<span>Cargar propuesta</span>
			</p>
			
			<div id='portamapagrande' class='portamapagrande' estado='inactivo'>
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


	<div id='ListaParcelasIndefinidas'>
		<div id='listadosinpartic'></div>
		<div id='listadosconpartic'></div>
	</div>
	

	<div id='formpropuesta' class='formulario'>
	
		<div id='botonera'>
			<a onclick='cerrarForm(this.parentNode.parentNode.getAttribute("id"))'>cerrar</a>
			<a onclick='guardarPropuesta()'>guardar</a>
		</div>
		
		<input type='hidden' name='id_p_cot_parcelas'>
		<input type='hidden' name='idpropuesta'>
		<input type='hidden' name='geometría'>
		<p>¿Cual es tu opinión para esta zona?</p>
		<label for='nombre'>Resumen (o título)</label><br>
		<input name='nombre' autocomplete="off"><br>
		
		<label for='descripcion'>Desarrollo de la idea</label><br>
		<textarea name='descripcion' autocomplete="off"></textarea>

		<label for='nombre'>Tu nombre y apellido (opcional)</label><br>
		<input name='autor' autocomplete="off"><br>
		
		<label for='nombre'>¿Representás a alguna organización? ¿A cual? (opcional)</label><br>
		<input name='organizacion' autocomplete="off"><br>
				
		<label for='nombre'>Mail de contacto (opcional)</label><br>
		<input name='mail' autocomplete="off"><br>
		
	</div>		
	
	
<table id='fichaparcela' estado='inactivo' idparcela=''>
	<tr>
		<td rowspan="3" id="codigo" style="background-color: rgb(64, 173, 50);">D-Ex</td>
		<td>
			<div id="contiene_cod_grupo" style="background-color: rgb(47, 172, 11);">
				<span class="titulito">grupo</span><span id="cod_grupo"></span>
			</div>
		</td>
		<td rowspan="3" id="nombre">
			<div id="nom_grupo"></div><div id="nom_clase"></div>
		</td>
	</tr>
	<tr>
		<td>
			<div id="contiene_cod_clase" style="background-color: rgb(81, 174, 88);">
				<span class="titulito">clase</span><span id="cod_clase">Ex</span>
			</div>
		</td>
	</tr>
	<tr>
		<td>
			<div id="cuadrito">
				<span class="titulito">Antes</span><span id="contenido"></span>
			</div>
		</td>
	</tr>
	<tr>
		
		<td colspan="2">
			
			<div id="tipo"></div>
			<div id="columna1"></div>
		</td>
		
		<td id="columna2">
			<div id='botonera'>
			<a onclick='cerarFormParcela();'>Cerrar</a>
			<a onclick='comentarParcela()'>Comentar</a>
			</div>
			<div id="nomencla"></div>
			<div id="superficie"></div>
			<div id="nomencla"></div>
			<div class="horizontal">Superficie Polígono: <span id="sup_pc"></span></div>
			<div class="horizontal">Superficie Edificada Registrada: <span id="sup_cons"></span></div>
			<div class="horizontal">Máxima superficie edificable teórica: <span id="sup_max"></span></div>
			<div class="horizontal">Constructividad utilizada: <span id="por_const"></span></div>
			
			<div>
				<div id="portamapa">
					<div class="mapa" id="mapaA"></div>
				</div>
			</div>
			<div>
				<div id="portamapa">
					<div class="mapa" id="mapaB"></div>
				</div>
			</div>
			
		</td>
	</tr>
</table>
	
	
<?php include('./includes/pie.php');?>

<script type="text/javascript" src="./js/jquery/jquery-3.6.0.js"></script>
<script type="text/javascript" src="./js/openlayers_6_12_0/build/ol.js"></script>

<script type="text/javascript" src="./mapa_participacion_js_consultas.js"></script>
<script type="text/javascript" src="./mapa_zonificacion_js_muestra.js"></script>
<script type="text/javascript" src="./mapa_participacion_js_interaccion.js"></script>

<script type="text/javascript">

function PreprocesarRespuesta(_response){_res=$.parseJSON(_response);return _res;}

var _HabilitadoEdicion='si';

var _COTID='15';
var _COTCOD='hUeDTp8';
var _get_id='<?php echo $_GET["id"];?>';
var _get_cod='<?php echo $_GET["cod"];?>';
if(_get_id!=''){_COTID=_get_id;}
if(_get_cod!=''){_COTCOD=_get_cod;}

var _DataDistritos={};

var _DataParcelasIndefinidas={};

var _Opciones={
	'cot_grupos':{}
	}
	
var _nPar=0; //numero de participación 


var _nFile=0;
var xhr={};

var _Maps={};

var _Modo='consulta';

consultarContenidosBase();
consultarParcelasIndefinidas();
consultarParticipaciones('actual');

let _draw; // global so we can remove it later

</script>

</body>
		
