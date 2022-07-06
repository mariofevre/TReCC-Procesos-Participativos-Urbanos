/**
 * mapa_participacion_interaccion.js
 * 
 * funciones interactivas de generación y adaptación de formularios y fichas para el mapa interactivo con funciones para registrar opiniones georeferenciadas.
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
	
	_view= new ol.View({
			center: [0, 0],
			zoom: 0
		});
		
	_Mapagrande= new ol.Map({
		
		target: 'mapagrande',
		layers: [
			/*new ol.layer.Tile({
			  source: new ol.source.Stamen({layer: 'toner'})
			}),*/

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
					labelStyle.getStroke().setColor("rgba(0,0,0,0)");
				  					
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
				style:  new ol.style.Style({
				  stroke: new ol.style.Stroke({
					color: [0,0, 0, 0.6],
					width: 2,
					lineDash: [4,8],
					lineDashOffset: 6
				  }),
				  fill: new ol.style.Fill({
					color: 'rgba(0,0,0,0.3)'
					}),
				}),

				declutter: true  
			})
		],
		view: _view
	});
	_Mapagrande.on('click', function(evt){
		
		consultaPunto(evt.pixel,evt);       
	});
	
	_view.on('change:resolution', function(evt){
		console.log(_view.getZoom());
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


function consultaPunto(pixel,_ev){
	
	//if(_Dibujando=='si'){return;}
	
	var _feature = _Mapagrande.forEachFeatureAtPixel(pixel, function(_feature, _layer){
	   if(_layer.get('name')=='layerzonas'){
		  return _feature;
	   }
	});  
	
	_zoom=_Mapagrande.getView().getZoom();
	//console.log(_zoom);
	//console.log(_ev.coordinate);
	if(_zoom>=15){
		consultarPuntoParcela(_ev.coordinate,null);		
	}
	  
	  
	if(_feature==undefined){
		
		if(parent._Aactiva=='si'){
			
		}
		
		return;
		
	}
	/*
	_Pid=_feature.getProperties().idai;
	consultaPuntoAj(_Pid);
	*/
	console.log(_feature.getProperties());
	formularDitrito(_feature.getProperties().iddis);
	mapaResaltarDitrito(_feature.getProperties().iddis);
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
		//console.log(_iddis+' vs '+_f_iddis);
		
		
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


function cerarFormParcela(){
	_form=document.querySelector('#fichaparcela');
	_form.setAttribute('estado','inactivo');
	
}

function  formularParcela(_datap){
	
	_sourcePropuestas.clear();
	cerrarForm("formpropuesta");
	
	_form=document.querySelector('#fichaparcela');
	_form.setAttribute('estado','activo');
	_form.setAttribute('idparcela',_datap.parcela.id);
	
	_form.querySelector('#nombre').innerHTML=_datap.parcela.nomencla;
	
	_form.querySelector('#sup_pc').innerHTML=Math.round(Number(_datap.parcela.sup_pol))+'m²';
	_form.querySelector('#sup_cons').innerHTML=Math.round(Number(_datap.parcela.sup_const))+'m²';

	
	
	_col=_form.querySelector('#columna1');
	_col.innerHTML='';
	_contenido_columna_izq=JSON.parse(_DataDistritos.proyecto.ficha_pc_col_izq);
	
	
	
	if(_datap.parcela.id_p_distritos==0){
		//esta zona no pertenece a nuingún grupo
		_frgb={
			'r':50,
			'g':50,
			'b':50		
		};
		
		_datd={
			'grupo':'S/D',
			'grupo_co_color':'#aaa',
			'co_color':'#ccc',
			'des_clase':'Sin Datos',
			'descripciongrupo':'Sin Datos',
			'zz_cache_tipo':'S/D'
			}
	}else{
		
		_datd= _DataDistritos.distritos[_datap.parcela.id_p_distritos];
	
	
		_grgb = hexToRgb(_datd.grupo_co_color);
		_drgb = hexToRgb(_datd.co_color);
		_frgb={
			'r':(Math.round((_grgb['r']+_drgb['r'])/2)),
			'g':(Math.round((_grgb['g']+_drgb['g'])/2)),
			'b':(Math.round((_grgb['b']+_drgb['b'])/2))		
		};
	}
	_colo=rgbToHex(_frgb['r'], _frgb['g'], _frgb['b']);	
	
	_colo_rgb=_frgb['r']+', '+_frgb['g']+', '+_frgb['b'];
	
	
	_form.querySelector('#codigo').innerHTML=_datd.zz_cache_tipo;
	_form.querySelector('#codigo').style.backgroundColor=_colo;
	_form.querySelector('#contiene_cod_grupo').innerHTML=_datd.grupo;
	_form.querySelector('#contiene_cod_grupo').style.backgroundColor=_datd.grupo_co_color;
	
	
	_form.querySelector('#contiene_cod_clase').innerHTML=_datd.nom_clase;
	_form.querySelector('#contiene_cod_clase').style.backgroundColor=_datd.co_color;
	
	_form.querySelector('#tipo').innerHTML=_datd.descripciongrupo+' <br>'+_datd.des_clase;	
	_form.querySelector('#tipo').style.backgroundColor=_colo;	
	
	_fotmax=0;
	for(_nc in _contenido_columna_izq){
		
		_comp=_contenido_columna_izq[_nc];
		
		_div=document.createElement('div');
		_col.appendChild(_div);
		
		if(_comp.tipo=='titulo'){
		
			_div.setAttribute('class','titulo');
			_div.innerHTML=_comp.texto;	
				
		}else if(_comp.tipo=='seccion'){
			
			_div.setAttribute('class',_comp['class']);
			
			_sp=document.createElement('span');
			_sp.setAttribute('class','titulito');
			_sp.innerHTML=_DataDistritos.secciones[_comp.id].nombre;
			_div.appendChild(_sp);
	
			if(
				_DataDistritos.secciones[_comp.id].nombre.includes('FOT')
				||
				_DataDistritos.secciones[_comp.id].nombre.includes('F.O.T.')
			){
				if(_datd.secciones!=undefined){
				if(_datd.secciones[_comp.id]!=undefined){
					_fotmax=Math.max(_fotmax,Number(_datd.secciones[_comp.id].texto.replace(',','.')));
				}
				}
			}
			
			_sp=document.createElement('span');
			_div.appendChild(_sp);
			if(_datd.secciones!=undefined){
			if(_datd.secciones[_comp.id]!=undefined){
				_sp.innerHTML=_datd.secciones[_comp.id].texto;
			}
			}
		}
	}
	
	_maxedif = _fotmax * Number(_datap.parcela.sup_pol);
	
	_form.querySelector('#sup_max').innerHTML=Math.round(_maxedif)+'m²';
	_form.querySelector('#por_const').innerHTML=Math.round(Number(_datap.parcela.sup_const)*100/_maxedif)+'%';
	
	_form.querySelector('#mapaA').innerHTML='';
	_sourceA = new ol.source.Vector({});
	
	_MapaA= new ol.Map({
		
		target: 'mapaA',
		layers: [
			new ol.layer.Tile({
				className: 'desaturada',
				source: new ol.source.XYZ({
					url:'https://wms.ign.gob.ar/geoserver/gwc/service/tms/1.0.0/capabaseargenmap@EPSG:3857@png/{z}/{x}/{-y}.png'
				})
			}),
			new ol.layer.Vector({
				name:'layerlimitezonas',
				className: 'multiply',
				source: _sourceA,
				style: 	new ol.style.Style({
						stroke: new ol.style.Stroke({color : 'rgba(0,0,0,1)', width : 3}),
						fill: new ol.style.Fill({color : 'rgba('+_colo_rgb+',1)'})
				})			
			})
		],
		view: new ol.View({
			center: [0, 0],
			maxZoom: 18
		})
	});
	
	var format = new ol.format.WKT();
	var _feat = format.readFeature(_datap.parcela.geotx, {
		dataProjection: 'EPSG:3857',
		featureProjection: 'EPSG:3857'
	});

	_sourceA.addFeature(_feat);
	
	_MapaA.getView().fit(_sourceA.getExtent(), _MapaA.getSize());
	
	_form.querySelector('#mapaB').innerHTML='';
	_sourceB = new ol.source.Vector({});
	
	_MapaB= new ol.Map({
		
		target: 'mapaB',
		layers: [
			new ol.layer.Tile({
				className: 'normal',
				source: new ol.source.BingMaps({
					key: 'CygH7Xqd2Fb2cPwxzhLe~qz3D2bzJlCViv4DxHJd7Iw~Am0HV9t9vbSPjMRR6ywsDPaGshDwwUSCno3tVELuob__1mx49l2QJRPbUBPfS8qN',
					imagerySet:  'Aerial'
				})
			}),
			new ol.layer.Vector({
				name:'layerlimitezonas',
				className: 'normal',
				source: _sourceB,
				style: 	new ol.style.Style({
						stroke: new ol.style.Stroke({color : 'rgba(250,250,250,1)', width : 1}),
						fill: new ol.style.Fill({color : 'rgba('+_colo_rgb+',0.6)'})
				})			
			})
		],
		view: new ol.View({
			center: [0, 0],
			maxZoom: 17
		})
	});
	
	var format = new ol.format.WKT();
	var _feat = format.readFeature(_datap.parcela.geotx, {
		dataProjection: 'EPSG:3857',
		featureProjection: 'EPSG:3857'
	});

	_sourceB.addFeature(_feat);
	
	_MapaB.getView().fit(_sourceB.getExtent(), _MapaB.getSize());
	
	
	copiarParcelaAComentarios(_datap.modo);
	
}

function copiarParcelaAComentarios(_modo){
	_feats=_sourceA.getFeatures();
	_feat=_feats[0]; 
	_clon = _feat; 	
	_sourcePropuestas.clear();
	_sourcePropuestas.addFeature(_clon);
	
	if(_modo=='id'){
		_Mapagrande.getView().fit(_feat.getGeometry(), {maxZoom:18, minZoom:16, padding:[0,300,300,0]});
	}
}

function comentarParcela(){
	
	copiarParcelaAComentarios('');
		
	generarFormularioPropuesta();
	
	_idpc=document.querySelector('#fichaparcela').getAttribute('idparcela');
	console.log(_idpc);
	document.querySelector('#formpropuesta [name="id_p_cot_parcelas"]').value=_idpc;
	
	
}



function toogleEdit(_this){
	
	
	if(_this.checked){
		_sourcePropuestas.clear();
		_draw = new ol.interaction.Draw({
		  source: _sourcePropuestas,
		  type: 'Polygon',
		  freehand: true,
		});
		_Mapagrande.addInteraction(_draw);
		_MapaEstado='dibuja';		
		
	}else{
		desactivaDibujo();
	
	}
}

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
	
	document.querySelector('#formpropuesta [name="id_p_cot_parcelas"]').value='0';
	
	document.querySelector('#formpropuesta [name="nombre"]').value='';
	document.querySelector('#formpropuesta [name="descripcion"]').value='';
	document.querySelector('#formpropuesta [name="autor"]').value='';
	document.querySelector('#formpropuesta [name="organizacion"]').value='';
	document.querySelector('#formpropuesta [name="mail"]').value='';
	document.querySelector('#formpropuesta [name="nombre"]').value='';
	document.querySelector('#formpropuesta').removeAttribute('nPar');
	
}



function mapeaParticipaciones(){
	
	for(_np in _DataParticipaciones.participacionesOrden){
		_idp=_DataParticipaciones.participacionesOrden[_np];
		_datp=_DataParticipaciones.participaciones[_idp];
		
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

}
