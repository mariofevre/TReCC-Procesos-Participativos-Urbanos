/**
 * mapa_participacion_consultas.js
 * 
 * funciones de consulta ajax para interactuar con la base de datos desde el mapa interactivo con funciones para registrar opiniones georeferenciadas.
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
			if(_Modo =='revision'){
				consultarParticipaciones();
			}
		}
	})
	delete _parametros;	
}


function consultarParcelasIndefinidas(){	
	_parametros = {
		'cotCOD': _COTCOD,		
		'cotID': _COTID
	};
	$.ajax({
		url:   './participacion_consulta_parcelas_indefinidas.php',
		type:  'post',
		data: _parametros,
		error: function(XMLHttpRequest, textStatus, errorThrown){ 
			alert("Estado: " + textStatus); alert("Error: " + errorThrown); 
		},
		success:  function (response){	
			_res = PreprocesarRespuesta(response);
			_DataParcelasIndefinidas=_res.data;
			delete _res;		
			
			document.querySelector('#ListaParcelasIndefinidas #listadosinpartic').innerHTML='';
			document.querySelector('#ListaParcelasIndefinidas #listadosconpartic').innerHTML='';
			for(_np in _DataParcelasIndefinidas.parcelasIndefinidasOrden){
				_idp=_DataParcelasIndefinidas.parcelasIndefinidasOrden[_np];
				_datp=_DataParcelasIndefinidas.parcelasIndefinidas[_idp];
				_a=document.createElement('a');
				_a.innerHTML=_datp.nomencla;
				_a.setAttribute('onclick','consultarPuntoParcela(null,"'+_idp+'")');
				if(Object.keys(_datp.participaciones).length>0){
					document.querySelector('#ListaParcelasIndefinidas #listadosconpartic').appendChild(_a);
				
				}else{
					document.querySelector('#ListaParcelasIndefinidas #listadosinpartic').appendChild(_a);
				}
			}
		}
	})
	delete _parametros;	
}


function consultarParticipaciones(_modo){
	if(_modo==undefined){_modo='';}	
	_parametros = {
		'cotCOD': _COTCOD,		
		'cotID': _COTID,
		'modo':_modo
	};
	$.ajax({
		url:   './participacion_consulta_participaciones.php',
		type:  'post',
		data: _parametros,
		error: function(XMLHttpRequest, textStatus, errorThrown){ 
			alert("Estado: " + textStatus); alert("Error: " + errorThrown); 
		},
		success:  function (response){	
			_res = PreprocesarRespuesta(response);
			_DataParticipaciones=_res.data;
			delete _res;		
			
			if(typeof listaParticipaciones === "function"){
				listaParticipaciones();
			}
			mapeaParticipaciones();
		}
	})
	delete _parametros;	
}



function guardarPropuesta(){	
	
	_features=_sourcePropuestas.getFeatures();
	_feat = _features[_features.length - 1];
	_geom=_feat.getGeometry();
	_format = new ol.format.WKT();
	_geotx=_format.writeGeometry(_geom);
	
	_nPar++;
	document.querySelector('#formpropuesta').setAttribute('nPar',_nPar);
	
	_parametros = {
		'cotID': _COTID,
		'cotCOD': _COTCOD,		
		'nPar':_nPar,
		'geotx': _geotx,
		'id_p_cot_parcelas':document.querySelector('#formpropuesta [name="id_p_cot_parcelas"]').value,
		'nombre':document.querySelector('#formpropuesta [name="nombre"]').value,
		'descripcion':document.querySelector('#formpropuesta [name="descripcion"]').value,
		'autor':document.querySelector('#formpropuesta [name="autor"]').value,
		'organizacion':document.querySelector('#formpropuesta [name="organizacion"]').value,
		'mail':document.querySelector('#formpropuesta [name="mail"]').value
	};
	$.ajax({
		url:   './participacion_ed_participacion.php',
		type:  'post',
		data: _parametros,
		error: function(XMLHttpRequest, textStatus, errorThrown){ 
			alert("Estado: " + textStatus); alert("Error: " + errorThrown); 
		},
		success:  function (response){	
			_res = PreprocesarRespuesta(response);
			
			delete _res;		
			//if(_res===false){return;}
			//_DatosGrupos=_res.data;
			//consultarEstructura();
			//mostrarContenidosBase();
			//mostrarIndiceGrupos();
			//mostrarIndiceDistritos();
			featureGuardada();
			cerrarForm("formpropuesta");
			consultarParcelasIndefinidas();
			cerarFormParcela();
		}
	})
	delete _parametros;	
}


function consultarPuntoParcela(_coords,_idparcela){
	if(_coords==null){_coords=Array(0,0);}
	_parametros={
		'x':_coords[0],
		'y':_coords[1],
		'idparcela':_idparcela,
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
