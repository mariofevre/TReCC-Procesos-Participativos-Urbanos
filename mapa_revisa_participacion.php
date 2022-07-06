<?php
/**
 * mapa_revisa_participacion.php
 * 
 * espacio html para explorar el seguimiento de opiniones georeferencisdas.
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
			right: 2vw;
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
		
		#participaciones{
			display: block;
			position: fixed;
			right: 2vw;
			top: 2vh;
			height: 90vh;
			width: 400px;
			background-color: #fff;
			border: 1px solid #08afd9;
			box-shadow: 10px 10px 5px rgba(0,0,0,0.8);
			z-index: 100;
		}	
		
		#listaparticipaciones{
			display: block;
			height: calc(90vh - 30px);
			width: 400px;
			overflow:auto;
		}
		
		#listaparticipaciones >a{
			color:#444;
			background-color:#fff;
			font-family:arial;
			border-bottom:2px solid #000;
		}		
		
		#listaparticipaciones >a[filtrado='si']{
			display:none;
		}				
		
		#listaparticipaciones >a >p{
			border-bottom:1px solid #aaa;
		}
		#listaparticipaciones >a:hover{
			background-color:lightblue;
			color:#08afd9;
		}		
		#listaparticipaciones >a[selecto='si']{			
			background-color:lightblue;
		}
		
		#participaciones [res="resuelto"]{
				color:green;
		}
		#participaciones [res="resuelto observado"]{
				color:green;
		}
		#participaciones [res="pendiente"]{
				color:orange;
		}
		
		#participaciones [res="suspendido"]{
				color:red;
		}
			
		#editorrespuesta{
			display:none;
			position:absolute;
			top: 150px;
			left: 40px;
			background-color: #fff;
			border: 1px solid #08afd9;
			box-shadow: 10px 10px 5px rgba(0,0,0,0.8);
			z-index: 100;
			overflow:auto;
		}
		#editorrespuesta[cargado='si']{
			display:block;
		}
		
		#editorrespuesta #formulario{
			display:none;
		}
		#editorrespuesta[estado='abierto'] #formulario{
			display:block;
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
				
		#pageborde{
			margin-left: 1vw;
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
			<p>En este mapa se visualizan el conjunto de opiniones volcadas a la base de datos</p>

			
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



	<div id='formpropuesta' class='formulario'>
	
		<div id='botonera'>
			<a onclick='cerrarForm(this.parentNode.parentNode.getAttribute("id"))'>cerrar</a>
			<a onclick='guardarPropuesta()'>guardar</a>
		</div>
		
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
	
	<div id='participaciones'>
		
		<div id='filtroparticipaciones'>
			<div><span res="">sin revisión</span><input onchange='filtrar()' type='checkbox' checked='checked' name='null'><span id='contador_null'></span></div>
			<div><span res="suspendido">suspendido</span><input  onchange='filtrar()' type='checkbox' checked='checked' name='suspendido'><span id='contador_suspendido'></span></div>
			<div><span res="pendiente">pendiente</span><input  onchange='filtrar()' type='checkbox' checked='checked' name='pendiente'><span id='contador_pendiente'></span></div>
			<div><span res="resuelto observado">resuelto observado</span><input  onchange='filtrar()' type='checkbox' checked='checked' name='resuelto observado'><span id='contador_resuelto observado'></span></div>
			<div><span res="resuelto">resuelto</span><input  onchange='filtrar()' type='checkbox' checked='checked' name='resuelto'><span id='contador_resuelto'></span></div>
		</div>	
		<div id='listaparticipaciones'>
			
			
		</div>
	</div>
	
	<div id='editorrespuesta' estado='cerrado'>	
		<a onclick='toogleEditorRespuestas()'>editar</a>
		<div id='formulario'>
			<input type='hidden' name='id'>
			<p>estado: 
				<select name='respuesta_resultado'>
					<option value=''>- sin revisión -</option>
					<option value='suspendido'>suspendido</option>
					<option value='pendiente'>pendiente</option>
					<option value='resuelto observado'>resuelto observado</option>
					<option value='resuelto'>resuelto</option>
				</select>
			</p>
			<p>por: <input type='text' name='respuesta_por'>
			
			<p>observaciones: 
			<textarea name='respuesta_observaciones'></textarea>
			</p>
			<input type='button' value='guardar' onclick='guardarRespuesta()'>
		</div>
		
	</div>
<?php include('./includes/pie.php');?>

<script type="text/javascript" src="./js/jquery/jquery-3.6.0.js"></script>
<script type="text/javascript" src="./js/openlayers_6_12_0/build/ol.js"></script>

<script type="text/javascript" src="./mapa_participacion_js_consultas.js"></script>
<script type="text/javascript" src="./mapa_zonificacion_js_muestra.js"></script>
<script type="text/javascript" src="./mapa_revisa_participacion_js_interaccion.js"></script>

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

var _Opciones={
	'cot_grupos':{}
	}
	
var _nPar=0; //numero de participación 


var _nFile=0;
var xhr={};

var _Maps={};

var _Modo='revision';
consultarContenidosBase();






function cambioSourcePropuestas(){
	
	if(_MapaEstado=='dibuja'){
		generarFormularioPropuesta();
		document.querySelector('#activadibujo').checked=false;
		desactivaDibujo();
	}
}


function featureGuardada(){	
	desactivaDibujo();
	_features=_sourcePropuestas.getFeatures();
	_feat = _features[_features.length - 1];
	_sourceGuardadas.addFeature(_feat);
	_sourcePropuestas.clear();
}



function desactivaDibujo(){	
		_Mapagrande.removeInteraction(_draw);
		_MapaEstado='observa';		
}
	
function generarFormularioPropuesta(){	
	document.querySelector('#formpropuesta').setAttribute('estado','activo');	
	
	document.querySelector('#formpropuesta [name="nombre"]').value='';
	document.querySelector('#formpropuesta [name="descripcion"]').value='';
	document.querySelector('#formpropuesta [name="autor"]').value='';
	document.querySelector('#formpropuesta [name="organizacion"]').value='';
	document.querySelector('#formpropuesta [name="mail"]').value='';
	document.querySelector('#formpropuesta [name="nombre"]').value='';
	document.querySelector('#formpropuesta').removeAttribute('nPar');
	
}

function filtrar(){
	_filtros=document.querySelectorAll('#filtroparticipaciones input[type="checkbox"]');
	
	for(_nf in _filtros){
		if(typeof _filtros[_nf] !='object'){continue;}
		
		_parts=document.querySelectorAll('#participaciones #listaparticipaciones > a');
		
		for(_np in _parts){
				if(typeof _parts[_np] !='object'){continue;}
			_idpart=_parts[_np].getAttribute('idpart');
			if(_DataParticipaciones.participaciones[_idpart].respuesta_resultado==_filtros[_nf].name){
				if(_filtros[_nf].checked){
					_parts[_np].setAttribute('filtrado','no');
				}else{
					_parts[_np].setAttribute('filtrado','si');
				}
			}
		}
		
		
		_fts=_sourceGuardadas.getFeatures();
		for(_nft in _fts){
			_prop=_fts[_nft].getProperties();
			_idpart=_prop['idpart'];
			if(_DataParticipaciones.participaciones[_idpart].respuesta_resultado==_filtros[_nf].name){
				if(_filtros[_nf].checked){
					_fts[_nft].setStyle(_estiloGuardada);
				}else{
					_fts[_nft].setStyle(_estiloFiltrada);
				}
			}
		}
		
		
		_q='#participaciones #filtroparticipaciones [id="contador_'+_filtros[_nf].name+'"]';
		console.log(_q);
		
		if(_DataParticipaciones.estadisticas[_filtros[_nf].name]==undefined){			
			document.querySelector(_q).innerHTML=0;			
		}else{
			document.querySelector(_q).innerHTML=_DataParticipaciones.estadisticas[_filtros[_nf].name];			
		}
	
	}
		
}

		
				
function toogleEditorRespuestas(){
	
	_editor=document.querySelector('#editorrespuesta');
	_est=_editor.getAttribute('estado');
	if(_est=='abierto'){
		_editor.setAttribute('estado','cerrado');
		
	}else{
		_editor.setAttribute('estado','abierto');
	}
}

function listaParticipaciones(){
	_divlista=document.querySelector('#listaparticipaciones');
	_divlista.innerHTML='';
	for(_np in _DataParticipaciones.participacionesOrden){
		_idp=_DataParticipaciones.participacionesOrden[_np];
		_datp=_DataParticipaciones.participaciones[_idp];
		
		_part=document.createElement('a');
		_part.setAttribute('idpart',_idp);		
		_part.setAttribute('onclick','consultaParticipacionesTexto(this.getAttribute("idpart"))');
		_divlista.appendChild(_part);
		
		_p=document.createElement('p');				
		_part.appendChild(_p);
		_p.innerHTML='<b>titulo:</b> '+_datp.titulo;

		_p=document.createElement('p');				
		_part.appendChild(_p);
		_p.innerHTML='<b>Desarrollo:</b> '+_datp.desarrollo;

		_p=document.createElement('p');				
		_part.appendChild(_p);
		_p.innerHTML='<b>Firma:</b> '+_datp.autor;

		_p=document.createElement('p');				
		_part.appendChild(_p);
		_p.innerHTML='<b>Contacto:</b> '+_datp.contacto;

		_p=document.createElement('p');				
		_part.appendChild(_p);
		_p.innerHTML='<b>Organización:</b> '+_datp.organizacion;

		_date = new Date(parseInt(_datp.fechaunix)*1000);
		//console.log(_datp.fechaunix);
		//console.log(_date);
		_a=_date.getFullYear();
		_m=_date.getMonth()+1;
		_d=_date.getDate();
		_f=_d+'-'+_m+'-'+_a;
		_p=document.createElement('p');				
		_part.appendChild(_p);
		_p.innerHTML='<b>ip:</b> '+_datp.ip+' '+_datp.ip2 +'<b>Fecha:</b> '+_f;				
		
		_p=document.createElement('p');				
		_part.appendChild(_p);
		_p.innerHTML='<b>Resultado:</b> <span res="'+_datp.respuesta_resultado+'">'+_datp.respuesta_resultado+'</span><b>Por:</b> '+_datp.respuesta_por;				
		
		_p=document.createElement('p');				
		_part.appendChild(_p);
		_p.innerHTML='<b>Observaciones:</b> '+_datp.respuesta_observaciones;				
		

		var format = new ol.format.WKT();
		var _feat = format.readFeature(_datp.geotx, {
			dataProjection: 'EPSG:3857',
			featureProjection: 'EPSG:3857'
		});
		_feat.setProperties({
			'idpart':_idp,
			'nombre':''
		});
		_feat.set('name','part'+_idp);
		_sourceGuardadas.addFeature(_feat);
	}
	
	filtrar();
	
	
	
}


function guardarRespuesta(){
	
	_editor=document.querySelector('#editorrespuesta');	
	
	_parametros = {
		'cotCOD': _COTCOD,		
		'cotID': _COTID,
		'idpart':_editor.querySelector('[name="id"]').value,
		'respuesta_resultado':_editor.querySelector('[name="respuesta_resultado"]').value,
		'respuesta_por':_editor.querySelector('[name="respuesta_por"]').value,
		'respuesta_observaciones':_editor.querySelector('[name="respuesta_observaciones"]').value
	};
	
	$.ajax({
		url:   './participacion_ed_guarda_respuesta.php',
		type:  'post',
		data: _parametros,
		error: function(XMLHttpRequest, textStatus, errorThrown){ 
			alert("Estado: " + textStatus); alert("Error: " + errorThrown); 
		},
		success:  function (response){	
			_res = PreprocesarRespuesta(response);
			
			_editor=document.querySelector('#editorrespuesta');
			_editor.setAttribute('cargado','no');
			_editor.querySelector('[name="id"]').value='';
			_editor.querySelector('[name="respuesta_resultado"]').value='';
			_editor.querySelector('[name="respuesta_por"]').value='';
			_editor.querySelector('[name="respuesta_observaciones"]').value='';
			
			
			delete _res;		
			
			consultarContenidosBase();
		}
	})
	delete _parametros;	
	
}
</script>

</body>
		
