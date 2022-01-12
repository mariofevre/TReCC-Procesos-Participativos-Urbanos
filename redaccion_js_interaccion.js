function cerrarForm(_idform){
	
	document.querySelector('#'+_idform).setAttribute('estado','inactivo');
		
}




//OPCIONES EN FORMULARIO DISTRITO
var _opcionarActivo=null;

function filtrarOpciones(_this,_event){
	//if(_this.getAttribute('soloeditores')=='cambia'&&_HabilitadoEdicion!='si'){return;}
	
	if(_event!=null){
		console.log(_event.keyCode);
		if(_event.keyCode==40){ //flecha pabajo
			_event.preventDefault();
			_op=_this.parentNode.querySelector('.auxopcionar .contenido a[filtrado="no"]');
			console.log(_op);
			_opcionarActivo=_op;
			_op.focus();
			_op.setAttribute('foco','enfoco');
			return;
		}
	}
	_name=_this.getAttribute('name');
	_iname='id_p_'+_name.replace('nombre-n','id');
	_that=_this.parentNode.querySelector('[name="'+_iname+'"]');
	_that.value='n';
	_str=_this.value;
	
	//_str=_str.replace(/\s+/g,"");
	
	_str=_str.normalize('NFD').replace(/[\u0300-\u036f]/g, "");
	_str=_str.replace('/[^A-Za-z0-9\-]/gi', '');
	_str=_str.replace(/ /g, '');
	_str=_str.toLowerCase();
	
	console.log(_str);
	
	if(_str=='-'){_str='';}
	_str=_str.toLowerCase();
	
	_ops=_this.parentNode.querySelectorAll('.auxopcionar .contenido a');
	for(_no in _ops){
		console.log(_ops[_no]);
		if(typeof _ops[_no] != 'object'){continue;}
		_str2=_ops[_no].innerHTML;		
		_str2=_str2.normalize('NFD').replace(/[\u0300-\u036f]/g, "");
		_str2=_str2.replace('/[^A-Za-z0-9\-]/gi', '');
		_str2=_str2.replace(/ /g, '');
		_str2=_str2.toLowerCase();
		console.log(_str2+' vs '+_str);
		if(_str2.includes(_str)){
			_ops[_no].setAttribute('filtrado','no');
		}else{
			_ops[_no].setAttribute('filtrado','si');
		}		
	}
}


function opcionarDef(_this){
	//if(_this.getAttribute('soloeditores')=='cambia'&&_HabilitadoEdicion!='si'){return;}
	//console.log(_this.getAttribute('soloeditores'));
	//console.log(_HabilitadoEdicion);
	if(_this.value=='-'){_this.value='';}
    vaciarOpcionares();
    _nn=_this.getAttribute('name');
    _ss=_nn.split('-');//para separar el final -n utilizado en un input de texto que refiere a una categoría con id
    _spl=_ss[0].split('_');
    _cat='id_'+_spl[6];
    _this.nextSibling.style.display="inline-block";
    _destino=_this.nextSibling.querySelector(".contenido");
    _id=_this.getAttribute('id');		
    _destino.innerHTML='';
    
    for(_nn in _DataDistritos.gruposOrden){
        _regid= _DataDistritos.gruposOrden[_nn];
        _dat= _DataDistritos.grupos[_regid];
        //console.log(_dat);
        //console.log(_regid);
        _anc=document.createElement('a');
        _anc.setAttribute('onclick','cargaOpcion(this);');
        _anc.setAttribute('regid',_regid);
        _anc.innerHTML=_dat.nombre+' : '+_dat.descripcion;
        _destino.appendChild(_anc);
    }
}	


function vaciarOpcionares(_event,_this){	
	
	if(_this!=undefined){
		if(_opcionarActivo!=null){
		if(_opcionarActivo.parentNode.parentNode.parentNode!=null){
		if(_this.parentNode==_opcionarActivo.parentNode.parentNode.parentNode){
			//este opcionar está abierto.
			//_opcionarActivo=null;
			return;
		} 
		}
		}
	}

    if(_event!=undefined){    
        if(
            _event.explicitOriginalTarget.parentNode.parentNode.parentNode.previousSibling==_event.originalTarget
            ||
            _event.explicitOriginalTarget.parentNode.parentNode.previousSibling==_event.originalTarget
            ){
            return;
        }
    }
    
    _vaciaresA=document.querySelectorAll('.auxopcionar');
    
    for(_nn in _vaciaresA){
        if(_vaciaresA[_nn].style!=undefined){
        //console.log(_vaciaresA[_nn]);
        _vaciaresA[_nn].style.display='none';
        }
    }
    
    _vaciares=document.querySelectorAll('.auxopcionar .contenido');
    for(_nn in _vaciares){
        _vaciares[_nn].innerHTML='';
    }
}

function cargaOpcion(_this){
    //console.log(_this);
    _idcat=_this.getAttribute('regid');
    //console.log(_idcat);
    
    
    _ddat=_DataDistritos.grupos[_idcat];
    
    _regnom=_this.innerHTML;
    //console.log(_regnom);
    _regtit=_ddat.nombre;	
            
    _inputN=_this.parentNode.parentNode.previousSibling;
    _inputN.value=_ddat.nombre;
    _name=_inputN.getAttribute('name');
    _nname=_name.replace('nombre','descripcion');
    
    _inputN.parentNode.querySelector("[name='"+_nname+"']").value=_ddat.descripcion;
    
    _inputN.focus();
    _id=_inputN.getAttribute('id');
    _ff=_id.substring(0,(_id.length-2));
    			
    _iname='id_p_'+_name.replace('nombre-n','id');
    console.log(_iname);
    _input=_inputN.parentNode.querySelector("[name='"+_iname+"']").value=_ddat.id;
    
    _opcionarActivo=null;
    vaciarOpcionares();
}


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

function activaFormShape(){
	_form=document.querySelector('#formshapefile');
	_form.setAttribute('estado','activo');	

	consultarCargaShape();
}


var labelStyle = new ol.style.Style({
		image: new ol.style.Circle({
		       fill: new ol.style.Fill({color: 'rgba(150,150,150,0.8)'}),
		       stroke: new ol.style.Stroke({color: 'rgba(256,256,256,1)', width: 1}),
		       radius: 6
		}),
		fill: new ol.style.Fill({color: 'rgba(150,150,150,0.8)'}),
		stroke: new ol.style.Stroke({color: 'rgba(256,256,256,1)',width: 1}),
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


function mapaGrande(_idzona,_iddis){
									
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
	_datz=_datd.zonas[_idzona];		
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


	for(_nd in _DataDistritos.distritosOrden){
		_idd=_DataDistritos.distritosOrden[_nd];
		_datd=_DataDistritos.distritos[_idd];
		_datg=_DataDistritos.grupos[_datd.id_p_cot_grupos_id];
		for(_nz in _datd.zonas){
			
			_datz=_datd.zonas[_nz];
							
			var format = new ol.format.WKT();
			var _feat = format.readFeature(_datz.geotx, {
				dataProjection: 'EPSG:3857',
				featureProjection: 'EPSG:3857'
			});
			_feat.setProperties({
				'idai':_datz.id,
				'nombre':''
			});
			_feat.set('name',_datd.grupo+'-'+_datd.nom_clase);
			_sourcegrupo.addFeature(_feat);
			
		}
	}	
	
	_Mapagrande.getView().fit(_source.getExtent(), _Mapagrande.getSize());	

}



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
