/**
 * mapa_zonificacion_js_consultas.js
 * 
 * funciones de consulta ajax para interactuar con la base de datos desde el mapa interactivo de visualizacion.
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
			//mostrarIndiceGrupos();
			//mostrarIndiceDistritos();
			mapaGrande();
		}
	})
	delete _parametros;	
}


function consultarPuntoParcela(_coords){
	
	_parametros={
		'x':_coords[0],
		'y':_coords[1],
		'cotID': _COTID
	}
	$.ajax({
		url:   './mapa_zonificacion_consulta_punto_parcelas.php',
		type:  'post',
		data: _parametros,
		error: function(XMLHttpRequest, textStatus, errorThrown){ 
			alert("Estado: " + textStatus); alert("Error: " + errorThrown); 
		},
		success:  function (response){	
			_res = PreprocesarRespuesta(response);
			
			formularParcela(_res.data);
			
			delete _res;		
			//if(_res===false){return;}
			//_DatosGrupos=_res.data;
			//consultarEstructura();
			//mostrarContenidosBase();
			//mostrarIndiceGrupos();
			//mostrarIndiceDistritos();
			//mapaGrande();
		}
	})	
}

function consultarRedaccion(_iddist,_idsecc){
	
	_parametros = {
		'cotID': _COTID,
		'cotCOD': _COTCOD,		
		'iddist': _iddist,
		'idsecc': _idsecc
	};
	
	$.ajax({
		url:   './redaccion_consulta_redaccion.php',
		type:  'post',
		data: _parametros,
		error: function(XMLHttpRequest, textStatus, errorThrown){ 
			alert("Estado: " + textStatus); alert("Error: " + errorThrown); 
		},
		success:  function (response){	
			_res = PreprocesarRespuesta(response);
			//_DataDistritos=_res.data;
			//delete _res;		
			formularRedaccion(_res.data);
			//mapaGrande();
		}
	})
	delete _parametros;	
}

function consultarDistrito(_iddist){
	_parametros = {
		'cotID': _COTID,
		'cotCOD': _COTCOD,		
		'iddist': _iddist
	};
	$.ajax({
		url:   './redaccion_consulta_distrito.php',
		type:  'post',
		data: _parametros,
		error: function(XMLHttpRequest, textStatus, errorThrown){ 
			alert("Estado: " + textStatus); alert("Error: " + errorThrown); 
		},
		success:  function (response){	
			_res = PreprocesarRespuesta(response);
			_DataDistritos=_res.data;
			
			_iddist=_res.data.iddist;
			_DataDistritos.distritosOrden=_res.data.distritosOrden;
			_DataDistritos.distritos[_iddist]=_res.data.distritos[_iddist];
			delete _res;		
			
			formularDistrito(_iddist);
		}
	})
	delete _parametros;	
}
