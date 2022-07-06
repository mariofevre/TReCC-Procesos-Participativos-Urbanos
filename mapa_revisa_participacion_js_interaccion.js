/**
 * mapa_revisa_participacion_interaccion.js
 * 
 * funciones interactivas de generación y adaptación de formularios y fichas para el seguimiento de opiniones georeferenciadas.
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


function cerrarForm(_idform){
	document.querySelector('#'+_idform).setAttribute('estado','inactivo');	
}

function hexToRgb(hex) {
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

var labelStyle = new ol.style.Style({
		image: new ol.style.Circle({
		       fill: new ol.style.Fill({color: 'rgba(150,150,150,0.8)'}),
		       stroke: new ol.style.Stroke({color: 'rgba(255,255,255,1)', width: 1}),
		       radius: 6
		}),
		fill: new ol.style.Fill({color: 'rgba(150,150,150,0.8)'}),
		stroke: new ol.style.Stroke({color: 'rgba(255,255,255,1)',width: 1}),
		zIndex:100,
		text: new ol.style.Text({
	    	font: '12px Calibri,sans-serif',
	    	overflow: true,
	    	fill: new ol.style.Fill({
	      	color: '#000'
		    }),
		    stroke: new ol.style.Stroke({color: '#fff', width: 2})
	})
});



function cerrarForm(_idform){
	document.querySelector('#'+_idform).setAttribute('estado','inactivo');
}
	





	
function mapaGrande(){
	
	document.querySelector('head title').innerHTML=_DataDistritos.proyecto.nombre
	document.querySelector('#menu #titulopagina').innerHTML=_DataDistritos.proyecto.nombre;
	document.querySelector('#menu #dataproyecto').innerHTML='<b>v:'+_DataDistritos.proyecto.version+'</b> '+_DataDistritos.proyecto.descripcion;
	
									
	_porta=document.querySelector('#portamapagrande');
	_porta.setAttribute('estado','activo');
	
	_mg=_porta.querySelector('#portamapagrande #mapagrande');
	if(_mg!=null){_mg.parentNode.removeChild(_mg);}
	
	_dmap=document.createElement('div');
	_dmap.setAttribute('id','mapagrande');
	_dmap.setAttribute('name','mapagrande');
	_dmap.setAttribute('class','mapagrande');
	_porta.appendChild(_dmap);
	
	
	_source = new ol.source.Vector({});
	_sourcePropuestas =new ol.source.Vector({});
	_sourcePropuestas.on('change', function(event) {
		cambioSourcePropuestas();
	});
	_sourceGuardadas=new ol.source.Vector({});
	
	_MapaEstado='observa';
	_sourcegrupo = new ol.source.Vector({});
	
	
	_estiloGuardada = new ol.style.Style({
				  stroke: new ol.style.Stroke({
					color: [0,0, 0, 0.6],
					width: 2,
					lineDash: [4,8],
					lineDashOffset: 6
				  }),
				  fill: new ol.style.Fill({
					color: 'rgba(0,0,0,0.3)'
					}),
				});
				
	_estiloGuardadaSelecta = new ol.style.Style({
				  stroke: new ol.style.Stroke({
					color: 'rgba(8,175,217,1)',
					width: 2,
					lineDash: [4,8],
					lineDashOffset: 6
				  }),
				  fill: new ol.style.Fill({
					color: 'rgba(8,175,217,0.5)'
					}),
				});
	
	
	_estiloFiltrada = new ol.style.Style({
				  stroke: new ol.style.Stroke({
					color: 'rgba(0,0,0,0)',
					width: 0
				  }),
				  fill: new ol.style.Fill({
					color: 'rgba(0,0,0,0)'
					}),
				});
	
	
	_Mapagrande= new ol.Map({
		
		target: 'mapagrande',
		layers: [
			new ol.layer.Tile({
				className: 'desaturada',
				source: new ol.source.XYZ({
					url:'https://wms.ign.gob.ar/geoserver/gwc/service/tms/1.0.0/capabaseargenmap@EPSG:3857@png/{z}/{x}/{-y}.png'
				})
			}),
			new ol.layer.Tile({
				minZoom: 15,
				className: '',
				source: new ol.source.TileWMS({
					url:'http://190.111.246.33:8080/geoserver/zonificador_quilmes/wms',
					params: {'LAYERS': 'zonificador_quilmes:cot_'+_COTID+'_parcelas', 'TILED': true},
					serverType: 'geoserver',
				})
			}),
			new ol.layer.Vector({
				minZoom: 15,
				name:'layerlimitezonas',
				className: 'multiply',
				source: _source,
				style: function(feature) {
				  
					if(feature.get('name')!=null){
						labelStyle.getText().setText(feature.get('name'));						
					}else{
						labelStyle.getText().setText('');
					}
					labelStyle.getFill().setColor("rgba(0,0,0,0)");								
					labelStyle.getStroke().setColor("rgba(0,0,0,1)");
				  					
					return labelStyle;
				},
				declutter: true  
			}),
			new ol.layer.Vector({
				maxZoom: 15,
				name:'layerzonas',
				className: 'multiply',
				source: _source,
				style: function(feature) {
				  
					if(feature.get('name')!=null){
						labelStyle.getText().setText(feature.get('name'));						
					}else{
						labelStyle.getText().setText('');
					}
					labelStyle.getFill().setColor(feature.get('color'));								
					labelStyle.getStroke().setColor("rgba(255,255,255,1)");
					
					return labelStyle;
				},
				declutter: true  
			}),
			new ol.layer.Vector({
				name:'layerpropuestas',
				source: _sourcePropuestas,
				style: function(feature) {
				  
					if(feature.get('name')!=null){
						labelStyle.getText().setText(feature.get('name'));						
					}else{
						labelStyle.getText().setText('');
					}
					labelStyle.getFill().setColor("rgba(8,175,217,0.5)");					
					labelStyle.getStroke().setColor("rgba(8,175,217,1)");
					
					
					return labelStyle;
				},
				declutter: true  
			}),
			new ol.layer.Vector({
				name:'layerGuardadas',
				source: _sourceGuardadas,
				style: _estiloGuardada,
				declutter: true  
			})
		],
		view: new ol.View({
			center: [0, 0],
			zoom: 0
		})
	});
	_Mapagrande.on('click', function(evt){		
		consultaParticipacionesPunto(evt.pixel,evt);       
	});
		

	for(_nd in _DataDistritos.distritosOrden){
		
		_iddis=_DataDistritos.distritosOrden[_nd];
		_datd=_DataDistritos.distritos[_iddis];
		_datg=_DataDistritos.grupos[_datd.id_p_cot_grupos_id];
		
		if(_iddis==0){
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
			
		
		
		var _sy = new ol.style.Style({
			stroke: new ol.style.Stroke({color : 'rgba('+_frgb['r']+','+_frgb['g']+','+_frgb['b']+',1)', width : 3}),
			fill: new ol.style.Fill({color : 'rgba('+_frgb['r']+','+_frgb['g']+','+_frgb['b']+',1)'})
		});
		
		
		
		for(_nz in _datd.zonas){
			
			_datz=_datd.zonas[_nz];
			//console.log(_datz);		
				
			var format = new ol.format.WKT();
			var _feat = format.readFeature(_datz.geotx, {
				dataProjection: 'EPSG:3857',
				featureProjection: 'EPSG:3857'
			});
			_feat.setProperties({
				'idai':_datz.id,
				'iddis':_iddis,
				'nombre':''
			});
			_feat.set('name',_datd.grupo+'-'+_datd.nom_clase);
			_feat.set('color','rgba('+_frgb['r']+','+_frgb['g']+','+_frgb['b']+',1)');
			_source.addFeature(_feat);
			
		}
	}	
	
	_Mapagrande.getView().fit(_source.getExtent(), _Mapagrande.getSize());	

}

function consultaParticipacionesTexto(_idpart){
	limpiarSeleccionParticipaciones();
	document.querySelector('#listaparticipaciones [idpart="'+_idpart+'"]').setAttribute('selecto','si');
	
	_editor=document.querySelector('#editorrespuesta');
	_editor.setAttribute('cargado','si');
	_editor.querySelector('[name="id"]').value=_idpart;
	_editor.querySelector('[name="respuesta_resultado"]').value=_DataParticipaciones.participaciones[_idpart].respuesta_resultado;
	_editor.querySelector('[name="respuesta_por"]').value=_DataParticipaciones.participaciones[_idpart].respuesta_resultado;
	_editor.querySelector('[name="respuesta_observaciones"]').value=_DataParticipaciones.participaciones[_idpart].respuesta_observaciones;
	
	
	_features = _sourceGuardadas.getFeatures();
	
	for(_nn in _features){  
		_feature=	_features[_nn];
		_prop=_feature.getProperties();
		if(_prop.idpart==_idpart){
			_feature.setStyle(_estiloGuardadaSelecta);	
			//_Mapagrande.getView().fit(_feature.getGeometry().getExtent(), {padding:'40',duration:'2',size:_Mapagrande.getSize()});	
			_Mapagrande.getView().fit(_feature.getGeometry().getExtent(),{duration:'500',padding:[100, 100, 100, 100]});	
		}
	}
	filtrar();		
}
	
	

function limpiarSeleccionParticipaciones(){
	_features = _sourceGuardadas.getFeatures();
	
	for(_nn in _features){  	
		_features[_nn].setStyle(null);
	}		
	
	_as=document.querySelectorAll('#listaparticipaciones > a');
	
	for(_an in _as){
		if(typeof _as[_an] != 'object'){continue;}
		_as[_an].setAttribute('selecto','no');
	}
	
	filtrar();		
}

function consultaParticipacionesPunto(pixel,_ev){
	
	//if(_Dibujando=='si'){return;}
	
	limpiarSeleccionParticipaciones();
	
	var _feature = _Mapagrande.forEachFeatureAtPixel(pixel, function(_feature, _layer){
	   if(_layer.get('name')=='layerGuardadas'){  
		_feature.setStyle(_estiloGuardadaSelecta);
		_prop=_feature.getProperties();
		document.querySelector('#listaparticipaciones [idpart="'+_prop.idpart+'"]').setAttribute('selecto','si');
		document.querySelector('#listaparticipaciones [idpart="'+_prop.idpart+'"]').scrollIntoView();
		_Mapagrande.getView().fit(_feature.getGeometry().getExtent(), _Mapagrande.getSize());	
		
	   }
	});  
		  
	  
	if(_feature==undefined){
		
		if(parent._Aactiva=='si'){	
		}
		return;	
	}
	
	/*
	_Pid=_feature.getProperties().idai;
	consultaPuntoAj(_Pid);
	*/
	
	//formularDitrito(_feature.getProperties().iddis);
	//mapaResaltarDitrito(_feature.getProperties().iddis);
}

function cerrarForm(_idform){	
	document.querySelector('#'+_idform).setAttribute('estado','inactivo');		
}

function mapaResaltarDitrito(_iddis){
	
	_features = _source.getFeatures();
	
	
	for(_nn in _features){  
		
		_f_iddis=_features[_nn].getProperties().iddis;
		
		_datd=_DataDistritos.distritos[_f_iddis];
		console.log(_datd.id_p_cot_grupos_id);
		if(_datd.id_p_cot_grupos_id!=undefined){
			_datg=_DataDistritos.grupos[_datd.id_p_cot_grupos_id];
			
			_grgb = hexToRgb(_datg.co_color);
			_drgb = hexToRgb(_datd.co_color);
			_frgb={
				'r':(Math.round((_grgb['r']+_drgb['r'])/2)),
				'g':(Math.round((_grgb['g']+_drgb['g'])/2)),
				'b':(Math.round((_grgb['b']+_drgb['b'])/2))		
			};
			_col=rgbToHex(_frgb['r'], _frgb['g'], _frgb['b']);	
		}else{
			_frgb={
				'r':150,
				'g':150,
				'b':150	
			};
		}
		console.log(_iddis+' vs '+_f_iddis);
		
		
		if(_iddis==-1||_iddis==_f_iddis){
			_features[_nn].set('color','rgba('+_frgb['r']+','+_frgb['g']+','+_frgb['b']+',1)');
			console.log('color');		
		}else{
			_features[_nn].set('color','rgba(200,200,200,0.5)');	
			console.log('gris');		
		}
		//_features[_nn].setStyle(_sty);
		
		
		if(_features[_nn].getProperties().sel=='si'){
			//console.log('ab');
			_features[_nn].setStyle(null);
			
			_features[_nn].getProperties().sel='no'
			
			//console.log(_features[_nn].getProperties());
			//console.log('a[idgeo="'+_features[_nn].getProperties().idai+'"]');
			document.querySelector('a[idgeo="'+_features[_nn].getProperties().idai+'"]').setAttribute('stat','');
		}
	}	
}

function formularDitrito(_iddis){
	
	_cont=document.querySelector('#fichaDistrito');
	_cont.setAttribute('estado','activo');
	
	_tabla=document.querySelector('#fichaDistrito table');
	_tabla.innerHTML='';

	_datd=_DataDistritos.distritos[_iddis]
	_datg=_DataDistritos.grupos[_datd.id_p_cot_grupos_id];
	
	_dis=document.createElement('tr');
	_dis.setAttribute('class','distrito');
	_dis.setAttribute('id','dis'+_iddis);			
	_tabla.appendChild(_dis);
	
	_titgru=document.createElement('th');
	_sp=document.createElement('span');
	_sp.innerHTML=_datd.grupo+': '+_datd.descripciongrupo;
	_sp.style.backgroundColor=_datg.co_color;
	_titgru.appendChild(_sp);
	_dis.appendChild(_titgru);
		
	_titdist=document.createElement('th');
	_titdist.setAttribute('onclick','consultarDistrito(this.parentNode.getAttribute("iddis"))');
	_sp=document.createElement('span');
	_sp.innerHTML=_datd.grupo+'-'+_datd.nom_clase+': '+ _datd.descripciongrupo + ' ' + _datd.des_clase;
	_grgb = hexToRgb(_datg.co_color);
	_drgb = hexToRgb(_datd.co_color);
	_frgb={
		'r':(Math.round((_grgb['r']+_drgb['r'])/2)),
		'g':(Math.round((_grgb['g']+_drgb['g'])/2)),
		'b':(Math.round((_grgb['b']+_drgb['b'])/2))		
	};
	_col=rgbToHex(_frgb['r'], _frgb['g'], _frgb['b']);
	_sp.style.backgroundColor=_col;
	_titdist.appendChild(_sp);
	_dis.appendChild(_titdist);
	

	for(_ns in _DataDistritos.seccionesOrden){
		_ids=_DataDistritos.seccionesOrden[_ns];
		_dats=_DataDistritos.secciones[_ids];
		if(_dats.zz_verentabla=='0'){continue;}
		
		_secd=document.createElement('tr');
		_secd.setAttribute('class','seccion');
		_secd.setAttribute('idsecc',_ids);
		_secd.setAttribute('onclick','consultarRedaccion(this.parentNode.getAttribute("iddis"),this.getAttribute("idsecc"))');
		_tabla.appendChild(_secd);
		
		_sectit=document.createElement('td');
		_sectit.innerHTML='<span>'+_dats.nombre+'</span>';
		_secd.appendChild(_sectit);
		
		_secp=document.createElement('td');
		_secd.appendChild(_secp);
		
		
		if(_DataDistritos.distritos[_iddis].secciones[_ids]==undefined){
			if(_dats.pordefecto!=null){
				_secp.innerHTML='<span id="desarrollo">'+_dats.pordefecto+'</span>';
				_secp.setAttribute('class','defecto');
				_secp.setAttribute('pordefecto','si');
			}else{
				_secp.innerHTML='<span id="desarrollo"></span>';
				_secp.setAttribute('class','defecto');
				_secp.setAttribute('pordefecto','si');
			}
		}else{
			_secp.innerHTML=_DataDistritos.distritos[_iddis].secciones[_ids].texto;
			_secp.setAttribute('pordefecto','no');
			_secp.innerHTML='<span id="desarrollo">'+_DataDistritos.distritos[_iddis].secciones[_ids].texto+'</span>';
		}
	}				
}

/*
function mapaGrandeDist(_iddis){
	console.log(_iddis);							
	_porta=document.querySelector('#portamapagrande');
	_porta.setAttribute('estado','activo');
	_mg=_porta.querySelector('#portamapagrande #mapagrande');
	if(_mg!=null){_mg.parentNode.removeChild(_mg);}
	
	_dmap=document.createElement('div');
	_dmap.setAttribute('id','mapagrande');
	_dmap.setAttribute('name','mapagrande');
	_dmap.setAttribute('class','mapagrande');
	_porta.appendChild(_dmap);
	
	_datd=_DataDistritos.distritos[_iddis];
	
	_datg=_DataDistritos.grupos[_datd.id_p_cot_grupos_id];	

	if(_iddis==0){
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
		
	
	
	var _sy = new ol.style.Style({
		stroke: new ol.style.Stroke({color : 'rgba('+_frgb['r']+','+_frgb['g']+','+_frgb['b']+',1)', width : 3}),
		fill: new ol.style.Fill({color : 'rgba('+_frgb['r']+','+_frgb['g']+','+_frgb['b']+',0.5)'})
	});
	
	
	var _sg = new ol.style.Style({
		stroke: new ol.style.Stroke({color : 'rgba(256,256,256,1)', width : 1}),
		fill: new ol.style.Fill({color : 'rgba(150,150,150,0.5)'})
	});	
	_source = new ol.source.Vector({});	
	_sourcegrupo = new ol.source.Vector({});
	
	
	_Mapagrande= new ol.Map({
		  target: 'mapagrande',
		  layers: [
			new ol.layer.Tile({
			  source: new ol.source.Stamen({layer: 'toner'})
			}),
			new ol.layer.Vector({
			  source: _sourcegrupo,
			  style: function(feature) {
					if(feature.get('name')!=null){
						labelStyle.getText().setText(feature.get('name'));						
					}else{
						labelStyle.getText().setText('');
					}
				   //console.log(feature.get('estado'));
					//if(feature.get('estado')==='0'){
					//	labelStyle.getFill().setColor("rgba(255,0,0,0.5)");
					//}else if(feature.get('estado')==='1'){
					//	labelStyle.getFill().setColor("rgba(0,255,100,0.8)");
					//}else{
					//	labelStyle.getFill().setColor('rgba(256,256,256,1)');
					//}
				   
					return labelStyle;
				},
				declutter: true  
			}),
			new ol.layer.Vector({
			  source: _source,
			  style: _sy
			})
		  ],
		  view: new ol.View({
			center: [0, 0],
			zoom: 0
		  })
		});
		
	console.log(_datd.zonas);
	for(_nz in _datd.zonas){
		
		_datz=_datd.zonas[_nz];
					
		var format = new ol.format.WKT();
		var _feat = format.readFeature(_datz.geotx, {
			dataProjection: 'EPSG:3857',
			featureProjection: 'EPSG:3857'
		});
		
		_feat.setProperties({
			'idai':_datz.id,
			'nombre':_datd.grupo+'-'+_datd.nom_clase
		});
		_feat.set('name',_datd.grupo+'-'+_datd.nom_clase);
		_source.addFeature(_feat);

	}
	
	
	for(_nd in _DataDistritos.distritosOrden){
		_idd2=_DataDistritos.distritosOrden[_nd];
		_datd2=_DataDistritos.distritos[_idd2];
		_datg=_DataDistritos.grupos[_datd2.id_p_cot_grupos_id];
		for(_nz in _datd2.zonas){
			
			_datz2=_datd2.zonas[_nz];
							
			var format = new ol.format.WKT();
			var _feat = format.readFeature(_datz2.geotx, {
				dataProjection: 'EPSG:3857',
				featureProjection: 'EPSG:3857'
			});
			_feat.setProperties({
				'idai':_datz2.id,
				'nombre':''
			});
			_feat.set('name',_datd2.grupo+'-'+_datd2.nom_clase);
			_sourcegrupo.addFeature(_feat);
			
		}
	}	
	
	_Mapagrande.getView().fit(_source.getExtent(), _Mapagrande.getSize());	

}

function cerrarMapaGrande(){
	_porta=document.querySelector('#portamapagrande');
	_porta.setAttribute('estado','inactivo');
	_mg=_porta.querySelector('#mapagrande');
	_mg.parentNode.removeChild(_mg);
}

*/
