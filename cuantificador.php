<?php
/**
 * cuantificador.php
 * 
 * espacio html para explorar la cuantificación de superficies asociada a cada zona
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
		
		.contenido{
			z-index:10000;
			position:absolute;
			top:0px;
			left:0px;
			margin-top:0px;
		  padding-top: 600px;
		  border: #000 solid 3px;
		  padding-bottom: 200px;
		  width:2000px;
		  background-color:#fff;
		}
			
		
.titulo{	
	position:relative;
	top: 23px;
	font-family: Arial;
	font-weight: normal;
	font-size: 8px;
}
.cant{
	position:absolute;
	top: 100px;
	font-family: Arial;
	font-weight: normal;
	font-size: 8px;
}
.barra{
	position:absolute;
	bottom:0px;
	height:600px;
	width:40px;
	border:none;
}

.construido{	
	position:absolute;
	bottom:0px;
	width:80%;
left: 10%;
z-index: -1;
}
.construible{	
	position:absolute;
	bottom:0px;
	width:90%;
	border:2px solid #000;
}
.parcelas{	
	position:absolute;
	background-color:#444;
	bottom:0px;
	left:5px;
	width:5%;
}
.distrito{
	width:40px;
	display:inline-block;
	height:1px;
	position: relative;
  vertical-align: bottom;

}

	</style>
</head>

<body>


		
	<div id="pageborde">
	<div id="page">
		<div id="menu">
			<h1 id='titulopagina'></h1>
			<p id='dataproyecto'></p>
			<p>En esta página usted puede acceder a la información técnica de cada zona.</p>
			<p>Los usuarios haibilitados pueden editar el contenido.</p>
		
			<a target='blank' onclick='window.open("./mapa_zonificacion.php?id="+_COTID+"&cod="+_COTCOD , "_blank")'>ver mapa</a>
			<a target='blank' onclick='window.open("./mapa_participacion.php?id="+_COTID+"&cod="+_COTCOD , "_blank")'>ver mapa participativo</a>
			<a target='blank' onclick='window.open("./mapa_revisa_participacion.php?id="+_COTID+"&cod="+_COTCOD , "_blank")'>ver mapa de revisión de participaciones</a>
			<a onclick='mostrarContenidosBase_Texto()'>ver xxxx</a>
			<a onclick='mostrarContenidosBase()'>ver en xxxxx</a>
			<a onclick='mostrarContenidosBase_Fichas()'>ver en modo fichas</a>
			
			
		</div>
		
		
	</div>
	</div>
<div  id='contenido' class='contenido'>
		
		</div>	



<script type="text/javascript" src="./js/jquery/jquery-3.6.0.js"></script>
<script type="text/javascript" src="./js/openlayers_5_3_0/build/ol.js"></script>


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



function consultarContenidosBase(){
	
	
	_parametros = {
		'cotCOD': _COTCOD,		
		'cotID': _COTID
	};
	$.ajax({
		url:   './redaccion_consulta_redacciones.php',
		type:  'post',
		data: _parametros,
		error: function(XMLHttpRequest, textStatus, errorThrown){ 
			alert("Estado: " + textStatus); alert("Error: " + errorThrown); 
		},
		success:  function (response){	
			_res = PreprocesarRespuesta(response);
			_DataDistritos=_res.data;
			delete _res;		
			//if(_res===false){return;}
			//_DatosGrupos=_res.data;
			//consultarEstructura();
			//mostrarContenidosBase();
			mostrarContenidosBase();
			//mostrarIndiceDistritos();
		}
	})
	delete _parametros;	
}


function mostrarContenidosBase(){
	
	_cont=document.querySelector('#contenido');
	_cont.innerHTML='';
	_cont.setAttribute('modo','edicion');
	
	document.querySelector('head title').innerHTML=_DataDistritos.proyecto.nombre
	document.querySelector('#menu #titulopagina').innerHTML=_DataDistritos.proyecto.nombre;
	document.querySelector('#menu #dataproyecto').innerHTML='<b>v:'+_DataDistritos.proyecto.version+'</b> '+_DataDistritos.proyecto.descripcion;

	_max_val=1;
	for(_nd in _DataDistritos.distritosOrden){
		_idd=_DataDistritos.distritosOrden[_nd];
		_datd=_DataDistritos.distritos[_idd];
		
		console.log(parseInt(_datd.superficie_const));
		console.log(parseInt(_datd.superficie_max));
		console.log(parseInt(_datd.superficie_pc));
		
		
		_max_val=Math.max(
			_max_val,
			parseInt(_datd.superficie_const),
			parseInt(_datd.superficie_max),
			parseInt(_datd.superficie_pc)
		);
		console.log(_max_val);
		
	}
	
		
	
	for(_nd in _DataDistritos.distritosOrden){
		_idd=_DataDistritos.distritosOrden[_nd];
		_datd=_DataDistritos.distritos[_idd];
		_datg=_DataDistritos.grupos[_datd.id_p_cot_grupos_id];
		
		_dis=document.createElement('div');
		_dis.setAttribute('class','distrito');
		_dis.setAttribute('id','dis'+_idd);	
		_dis.setAttribute('iddis',_idd);			
		_cont.appendChild(_dis);
		
		
		if(_idd==0){
			//esta zona no pertenece a nuingún grupo
			_frgb={
				'r':150,
				'g':150,
				'b':150		
			};
		}else{
			_grgb = hexToRgb(_datg.co_color);
			_drgb = hexToRgb(_datd.co_color);
			_frgb={
				'r':(Math.round((_grgb['r']+_drgb['r'])/2)),
				'g':(Math.round((_grgb['g']+_drgb['g'])/2)),
				'b':(Math.round((_grgb['b']+_drgb['b'])/2))		
			};
		}
		
		_col=rgbToHex(_frgb['r'], _frgb['g'], _frgb['b']);
		
				
		_titdist=document.createElement('div');
		_titdist.setAttribute('class','titulo');
		console.log(_col);
		_titdist.style.backgroundColor=_col;		
		_titdist.innerHTML=_datd.grupo+'-'+_datd.nom_clase+': '+ _datd.descripciongrupo + '-' + _datd.des_clase;
		_dis.appendChild(_titdist);
		
		
				
		_cant=document.createElement('div');
		_cant.setAttribute('class','cant');	
		_cant.innerHTML='<b>construible:</b><br>';
		_cant.innerHTML+=_datd.superficie_max+'m²<br>';
		_cant.innerHTML+='<b>construido:</b><br>';
		_cant.innerHTML+=_datd.superficie_const+'m² ('+Math.round(100*_datd.superficie_const/_datd.superficie_max)+'%)<br>';
		_cant.innerHTML+='<b>superficie terreno:</b><br>';
		_cant.innerHTML+=_datd.superficie_pc+'<br>';
		
		_dis.appendChild(_cant);
		
		
				
		_barra=document.createElement('div');
		_barra.setAttribute('class','barra');
		_dis.appendChild(_barra);
		
		_const=document.createElement('div');
		_const.setAttribute('class','construido');
		
		_const.style.backgroundColor=_col;	
		console.log((100*_datd.superficie_const/_max_val)+'%');
		_const.style.height=(100*_datd.superficie_const/_max_val)+'%';
		_barra.appendChild(_const);
		
		_constble=document.createElement('div');
		_constble.setAttribute('class','construible');	
		_constble.style.height=(100*_datd.superficie_max/_max_val)+'%';
		_barra.appendChild(_constble);
				
		_parc=document.createElement('div');
		_parc.setAttribute('class','parcelas');	
		_parc.style.height=(100*_datd.superficie_pc/_max_val)+'%';
		_barra.appendChild(_parc);
	
		
	}
}


function hexToRgb(hex) {
	
	if(hex==null){return Array(0,0,0);}
  // Expand shorthand form (e.g. "03F") to full form (e.g. "0033FF")
  var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
  hex = hex.replace(shorthandRegex, function(m, r, g, b) {
    return r + r + g + g + b + b;
  });

  var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
  return result ? {
    r: parseInt(result[1], 16),
    g: parseInt(result[2], 16),
    b: parseInt(result[3], 16)
  } : null;
}

function componentToHex(c) {
  var hex = c.toString(16);
  return hex.length == 1 ? "0" + hex : hex;
}

function rgbToHex(r, g, b) {
  return "#" + componentToHex(r) + componentToHex(g) + componentToHex(b);
}
</script>

</body>
		
