function cerrarForm(_idform){
	document.querySelector('#'+_idform).setAttribute('estado','inactivo');	
}

/*
function formularDistrito(_iddist){	
		_form=document.querySelector('#formdistrito');
		_form.setAttribute('estado','activo');

		console.log(_iddist);
		_ddat=_DataDistritos.distritos[_iddist];
		
		
		_form.querySelector('[name="iddist"]').value= _iddist;
		_form.querySelector('[name="id_p_cot_grupos_id"]').value= _ddat.id_p_cot_grupos_id;
		
		_dgrupo=_DataDistritos.grupos[_ddat.id_p_cot_grupos_id];
		if(_dgrupo==undefined){
			_form.querySelector('[name="cot_grupos_nombre-n"]').value= '';
			_form.querySelector('[name="cot_grupos_descripcion-n"]').value= '';
			
		}else{
			_form.querySelector('[name="cot_grupos_nombre-n"]').value= _dgrupo.nombre;
			_form.querySelector('[name="cot_grupos_descripcion-n"]').value= _dgrupo.descripcion;
			
		}
		
		_form.querySelector('[name="nom_clase"]').value= _ddat.nom_clase;
		_form.querySelector('[name="des_clase"]').value= _ddat.des_clase;
		_form.querySelector('[name="des_clase"]').focus();
		_form.querySelector('[name="orden"]').value= _ddat.orden;
		_form.querySelector('[name="co_color"]').value= _ddat.co_color;
		
		//opcionarDef(_form.querySelector('#Icot_grupos_nombre-n'));
}
*/
/*
function formularGrupo(_idg){
	
		_form=document.querySelector('#formgrupo');
		_form.setAttribute('estado','activo');
		
		_gdat=_DataDistritos.grupos[_idg];
		_form.querySelector('#cant_dist').innerHTML= _gdat.cant_dist;
		_form.querySelector('[name="idgrupo"]').value= _gdat.id;
		_form.querySelector('[name="nombre"]').value= _gdat.nombre;
		_form.querySelector('[name="nombre"]').focus();
		_form.querySelector('[name="descripcion"]').value= _gdat.descripcion;
		_form.querySelector('[name="co_color"]').value= _gdat.co_color;
		
			
}
*/
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

/*
function formularRedaccion(_data){
		
		_form=document.querySelector('#formredaccion');
		_form.setAttribute('estado','activo');
		
		_ddat=_DataDistritos.distritos[_data.id_p_cot_distritos_id];
		
		_form.querySelector('#distrito').innerHTML= _ddat.grupo + ' ' + _ddat.nom_clase + ' ' +_ddat.descripciongrupo + ' ' + _ddat.des_clase;
		
		
		_form.querySelector('#seccion').innerHTML=_DataDistritos.secciones[_data.id_p_cot_secciones_id].nombre;
		
		_form.querySelector('[name="iddist"]').value=_data.id_p_cot_distritos_id;
		_form.querySelector('[name="idsecc"]').value=_data.id_p_cot_secciones_id;
		if(_res.data.texto==undefined){_res.data.texto='';}
		_form.querySelector('[name="texto"]').value=_data.texto;
		_form.querySelector('[name="texto"]').focus();
		
}
*/
/*
function activaFormShape(){
	_form=document.querySelector('#formshapefile');
	_form.setAttribute('estado','activo');	

	consultarCargaShape();
}
*/

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




function mapaGrande(){
									
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
	_sourcegrupo = new ol.source.Vector({});
	
	_Mapagrande= new ol.Map({
		
		target: 'mapagrande',
		layers: [
			new ol.layer.Tile({
			  source: new ol.source.Stamen({layer: 'toner'})
			}),
			new ol.layer.Vector({
				name:'layerzonas',
				source: _source,
				style: function(feature) {
				  
					if(feature.get('name')!=null){
						labelStyle.getText().setText(feature.get('name'));						
					}else{
						labelStyle.getText().setText('');
					}
					labelStyle.getFill().setColor(feature.get('color'));
					
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
			})
		],
		view: new ol.View({
			center: [0, 0],
			zoom: 0
		})
	});
	_Mapagrande.on('click', function(evt){
		
		consultaPunto(evt.pixel,evt);       
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
			fill: new ol.style.Fill({color : 'rgba('+_frgb['r']+','+_frgb['g']+','+_frgb['b']+',0.5)'})
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
			_feat.set('color','rgba('+_frgb['r']+','+_frgb['g']+','+_frgb['b']+',0.5)');
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
		console.log(_iddis+' vs '+_f_iddis);
		
		
		if(_iddis==-1||_iddis==_f_iddis){
			_features[_nn].set('color','rgba('+_frgb['r']+','+_frgb['g']+','+_frgb['b']+',0.5)');
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
