<?php
/**
 * listado_parcelas.php
 * 
 * espacio html y funciones js para representar un listado de parcelas asociado a cada tipo de zona
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
		
		#cargandoinicial[estado='inactivo']{
				display:none;
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
			<h2>Índice de tipos de zona: </h2>
			<div id='indicedistritos'>
			<div id='lista'></div>
			</div>
		</div>
		
		
		<div id="pageborde">
	<div id="page">
		<div id="menu">
			<h1 id='titulopagina'></h1>
			<p id='dataproyecto'></p>
			<p>Listado de parcelas para cada tipo de zona</p>
			<div id='cargandoinicial' estado='activo'><img src='./img/cargando.gif'>Cargar todas las parcelas en esta página puede tardar un poco. paciencia.</div>
			
		</div>
		
		<div  id='contenido' class='contenido'>
		
		</div>	
	</div>
	</div>

		
<?php include('./includes/pie.php');?>

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



function consultarContenidosBase(){	
	_parametros = {
		'cotCOD': _COTCOD,		
		'cotID': _COTID,
		'discrimina_parcelas':'si'
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
			mostrarLista_Parcelas();
			mostrarIndiceDistritos();
			document.querySelector('#cargandoinicial').setAttribute('estado','inactivo');
		}
	})
	delete _parametros;	
}

consultarContenidosBase();


function mostrarLista_Parcelas(){

	_cont=document.querySelector('#contenido');
	_cont.setAttribute('modo','listaparcelas');
	_cont.innerHTML='';
	
	document.querySelector('head title').innerHTML=_DataDistritos.proyecto.nombre
	document.querySelector('#menu #titulopagina').innerHTML=_DataDistritos.proyecto.nombre;
	document.querySelector('#menu #dataproyecto').innerHTML='<b>v:'+_DataDistritos.proyecto.version+'</b> '+_DataDistritos.proyecto.descripcion;

	for(_nd in _DataDistritos.distritosOrden){
			
		_idd=_DataDistritos.distritosOrden[_nd];
		_datd=_DataDistritos.distritos[_idd];	
		_datg=_DataDistritos.grupos[_datd.id_p_cot_grupos_id];
		
		if(_idd===''){continue;}
		if(_idd=='0'){continue;}
				
		_lista = crearListaVacía();
		_cont.appendChild(_lista);

		
		_lista.setAttribute('id','dis'+_idd);			
		_lista.querySelector('#cod_grupo').innerHTML=_datd.grupo;
		_lista.querySelector('#nom_grupo').innerHTML=_datd.descripciongrupo;
		_lista.querySelector('#contiene_cod_grupo').style.backgroundColor=_datg.co_color;
		
		_lista.querySelector('#cod_clase').innerHTML=_datd.nom_clase;
		_lista.querySelector('#nom_clase').innerHTML=_datd.des_clase;
		_lista.querySelector('#contiene_cod_clase').style.backgroundColor=_datd.co_color;
		
		
		_grgb = hexToRgb(_datg.co_color);
		_drgb = hexToRgb(_datd.co_color);
			
			
			if(_datd.co_color_final==''){
				_frgb={
					'r':(Math.round((_grgb['r']+_drgb['r'])/2)),
					'g':(Math.round((_grgb['g']+_drgb['g'])/2)),
					'b':(Math.round((_grgb['b']+_drgb['b'])/2))		
				};
				_col=rgbToHex(_frgb['r'], _frgb['g'], _frgb['b']);
			}else{
				_col=_datd.co_color_final;	
			_frgb=hexToRgb(_col);	
			}
			
		_lista.querySelector('#codigo').innerHTML=_datd.grupo+'-'+_datd.nom_clase;
		_lista.querySelector('#codigo').style.backgroundColor=_col;

		_tr=document.createElement('tr');
		_lista.appendChild(_tr);
		
		_c=0;
		for(_np in _datd.parcelas){
			
			_td=document.createElement('td');
			_td.innerHTML=_datd.parcelas[_np];
			_tr.appendChild(_td);
			
			_c++;
			
			if(_c>6){
				_tr=document.createElement('tr');
				_lista.appendChild(_tr);
				_c=0;
			}			
		}	
	}
}





	
function crearListaVacía(){
	
	_ttt=document.createElement('table');		
	
	/////////////////////////	
	
	_tr=document.createElement('tr');		
	_ttt.appendChild(_tr);
	
			////
			
	_td=document.createElement('td');		
	_td.setAttribute('rowspan','3');
	_td.setAttribute('id','codigo');
	_tr.appendChild(_td);
	
			////
				
	_td=document.createElement('td');		
	_tr.appendChild(_td);
	
	_div=document.createElement('div');
	_div.setAttribute('id','contiene_cod_grupo');
	_td.appendChild(_div);
	
	_sp=document.createElement('span');
	_sp.setAttribute('class','titulito');
	_sp.innerHTML='grupo';
	_div.appendChild(_sp);
	
	_sp=document.createElement('span');
	_sp.setAttribute('id','cod_grupo');
	_div.appendChild(_sp);
	
			////
				
	_td=document.createElement('td');	
	_td.setAttribute('rowspan','3');
	_td.setAttribute('colspan','5');
	_td.setAttribute('id','nombre');	
	_tr.appendChild(_td);	
	
	_sp=document.createElement('div');
	_sp.setAttribute('id','nom_grupo');	
	_td.appendChild(_sp);
	
	_sp=document.createElement('div');
	_sp.setAttribute('id','nom_clase');	
	_td.appendChild(_sp);
		
	//////////////////////////////
	
	_tr=document.createElement('tr');		
	_ttt.appendChild(_tr);
	
			////
				
	_td=document.createElement('td');			
	_tr.appendChild(_td);
	
	_div=document.createElement('div');
	_div.setAttribute('id','contiene_cod_clase');	
	_td.appendChild(_div);
	
	_sp=document.createElement('span');
	_sp.setAttribute('class','titulito');
	_sp.innerHTML='clase';
	_div.appendChild(_sp);
	
	_sp=document.createElement('span');
	_sp.setAttribute('id','cod_clase');
	_div.appendChild(_sp);

			////
				

	
	//////////////////////////////
	
	
	_tr=document.createElement('tr');		
	_ttt.appendChild(_tr);

			////
					
	_td=document.createElement('td');
	_tr.appendChild(_td);

	_div=document.createElement('div');
	_div.setAttribute('id','cuadrito');
	_td.appendChild(_div);
	


	return(_ttt);
	
}



function mostrarIndiceDistritos(){
	_cont=document.querySelector('#indicedistritos #lista');
	_cont.innerHTML='';
	
	for(_nd in _DataDistritos.distritosOrden){
		_idd=_DataDistritos.distritosOrden[_nd];
		_datd=_DataDistritos.distritos[_idd];
		
		_dis=document.createElement('a');
		_dis.innerHTML=_datd.grupo+' '+_datd.nom_clase+' - '+ _datd.descripciongrupo + ' ' + _datd.des_clase;
		_dis.setAttribute('href','#dis'+_idd);
		_dis.setAttribute('class','distrito');			
		_dis.setAttribute('iddis',_idd);			
		_cont.appendChild(_dis);		
		
		_grgb = hexToRgb(_datd.grupo_co_color)
		_drgb = hexToRgb(_datd.co_color);
		
		
		
		if(_datd.co_color_final==''){
			_frgb={
				'r':(Math.round((_grgb['r']+_drgb['r'])/2)),
				'g':(Math.round((_grgb['g']+_drgb['g'])/2)),
				'b':(Math.round((_grgb['b']+_drgb['b'])/2))		
			};
			_col=rgbToHex(_frgb['r'], _frgb['g'], _frgb['b']);
		}else{
			_col=_datd.co_color_final;	
			_frgb=hexToRgb(_col);	
		}
		
		
		_dis.style.backgroundColor=_col;

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
		
