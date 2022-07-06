function mostrarContenidosBase(){
	
	_cont=document.querySelector('#contenido');
	_cont.innerHTML='';
	_cont.setAttribute('modo','edicion');
	
	document.querySelector('head title').innerHTML=_DataDistritos.proyecto.nombre
	document.querySelector('#menu #titulopagina').innerHTML=_DataDistritos.proyecto.nombre;
	document.querySelector('#menu #dataproyecto').innerHTML='<b>v:'+_DataDistritos.proyecto.version+'</b> '+_DataDistritos.proyecto.descripcion;
	
	for(_nd in _DataDistritos.distritosOrden){
		_idd=_DataDistritos.distritosOrden[_nd];
		_datd=_DataDistritos.distritos[_idd];
		_datg=_DataDistritos.grupos[_datd.id_p_cot_grupos_id];
		
		_dis=document.createElement('div');
		_dis.setAttribute('class','distrito');
		
		_dis.setAttribute('id','dis'+_idd);	
		_dis.setAttribute('iddis',_idd);			
		_cont.appendChild(_dis);
		
		_titdist=document.createElement('h2');
		_titdist.setAttribute('onclick','consultarDistrito(this.parentNode.getAttribute("iddis"))');
		_titdist.innerHTML=_datd.grupo+'-'+_datd.nom_clase+': '+ _datd.descripciongrupo + '-' + _datd.des_clase;
		_dis.appendChild(_titdist);
		
		_a=document.createElement('a');
		_a.setAttribute('onclick','event.stopPropagation();mapaGrandeDist(this.parentNode.parentNode.getAttribute("iddis"))');
		_a.innerHTML='<img src="./img/fullscreen.jpg">';
		_titdist.appendChild(_a);
		
		
		_p=document.createElement('p');
		_p.innerHTML='Extensión:'+Math.round(_datd.sup_ha)+' Ha';
		_titdist.appendChild(_p);
		
		
		
		if(_idd==0){
			//esta zona no pertenece a nuingún grupo
			_frgb={
				'r':150,
				'g':150,
				'b':150		
			};
			_col=rgbToHex(_frgb['r'], _frgb['g'], _frgb['b']);
		}else{
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
		}
		
				
		
		
		
		
		
		
		
		
		for(_nz in _datd.zonas){
			
			_datz=_datd.zonas[_nz];
			
			_portamap=document.createElement('div');
			_portamap.setAttribute('idzona',_datz.id);				
			_portamap.setAttribute('class','portamapa');			
			_dis.appendChild(_portamap);
			
			
			if(_datz.geotx.indexOf("EMPTY")>-1){
				console.log('error en zona id: '+_datz.id);
				console.log(_portamap);
				_portamap.inneHTML=='registro sin geometría cargada. Revisar';
				continue;
			}
			
			
			_a=document.createElement('a');
			_a.setAttribute('onclick','mapaGrande(this.parentNode.getAttribute("idzona"),this.parentNode.parentNode.getAttribute("iddis"))');
			_a.innerHTML='<img src="./img/fullscreen.jpg">';
			_portamap.appendChild(_a);
			
			_dd=document.createElement('div');
			_dd.setAttribute('class','sup_ha');
			_dd.innerHTML=Math.round(_datz.sup_ha)+'Ha';
			_portamap.appendChild(_dd);
									
			_dmap=document.createElement('div');
			_dmap.setAttribute('id','mapaz'+_datz.id);				
			_dmap.setAttribute('class','mapa');			
			_portamap.appendChild(_dmap);
			
			//console.log('zonaid:'+_datz.id);
			
			var _sy = new ol.style.Style({
				stroke: new ol.style.Stroke({color : 'rgba('+_frgb['r']+','+_frgb['g']+','+_frgb['b']+',1)', width : 3}),
				fill: new ol.style.Fill({color : 'rgba('+_frgb['r']+','+_frgb['g']+','+_frgb['b']+',0.5)'})
			});
			
			_source = new ol.source.Vector({});
			
			_Maps[_datz.id]= new ol.Map({
				  target: 'mapaz'+_datz.id,
				  layers: [
					new ol.layer.Tile({
					  source: new ol.source.Stamen({layer: 'toner'})
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
			
			
			
			//console.log(_datz.geotx);
			var format = new ol.format.WKT();
			var _feat = format.readFeature(_datz.geotx, {
				dataProjection: 'EPSG:3857',
				featureProjection: 'EPSG:3857'
			});
			_feat.setProperties({
				'idai':_datz.id,
				'nombre':_datd.grupo+'-'+_datd.nom_clase
			});
			_source.addFeature(_feat);
			_Maps[_datz.id].getView().fit(_source.getExtent(), _Maps[_datz.id].getSize());	
		}
		
		if(_idd==0){
			//este es un distrito virtual que contiene a las zonas sin distrito asignado
			return;
		}
		
		for(_ns in _DataDistritos.seccionesOrden){
			_ids=_DataDistritos.seccionesOrden[_ns];
			_dats=_DataDistritos.secciones[_ids];
			
			_secd=document.createElement('div');
			_secd.setAttribute('class','seccion');
			_secd.setAttribute('idsecc',_ids);
			_secd.setAttribute('onclick','consultarRedaccion(this.parentNode.getAttribute("iddis"),this.getAttribute("idsecc"))');
			_dis.appendChild(_secd);
			
			_sectit=document.createElement('h3');
			_sectit.innerHTML=_dats.nombre;
			_secd.appendChild(_sectit);
			
			_secp=document.createElement('p');
			_secd.appendChild(_secp);
			
			if(_DataDistritos.distritos[_idd].secciones[_ids]==undefined){
				_secp.innerHTML=_dats.pordefecto;
				_secp.setAttribute('class','defecto');
				_secp.setAttribute('pordefecto','si');
			}else{
				_secp.innerHTML=_DataDistritos.distritos[_idd].secciones[_ids].texto;
				_secp.setAttribute('pordefecto','no');
			}			
		}		
		
	}
}


function mostrarContenidosBase_Fichas(){

	_cont=document.querySelector('#contenido');
	_cont.setAttribute('modo','fichas');
	_cont.innerHTML='';
	
	document.querySelector('head title').innerHTML=_DataDistritos.proyecto.nombre
	document.querySelector('#menu #titulopagina').innerHTML=_DataDistritos.proyecto.nombre;
	document.querySelector('#menu #dataproyecto').innerHTML='<b>v:'+_DataDistritos.proyecto.version+'</b> '+_DataDistritos.proyecto.descripcion;
	
	for(_nd in _DataDistritos.distritosOrden){

		_idd=_DataDistritos.distritosOrden[_nd];
		if(_idd===''){continue;}
		if(_idd=='0'){continue;}
				
		_ficha = crearFichaVacía();
		_cont.appendChild(_ficha);

		completarFicha(_ficha,_idd);
		
	}
}


	
function crearFichaVacía(){
	
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
		
	

	//////////////////////////////
	
	
	_tr=document.createElement('tr');		
	_ttt.appendChild(_tr);

			////		
			
	_td=document.createElement('td');
	_td.setAttribute('colspan','2');
	_td.setAttribute('id','columna1');
	_tr.appendChild(_td);
	
	
				//
				

		////
	
	_td=document.createElement('td');
	_td.setAttribute('id','columna2');
	_tr.appendChild(_td);	

				
				
	
	_div=document.createElement('div');
	_div.setAttribute('id','mapa');
	_td.appendChild(_div);	
		
	_portamap=document.createElement('div');			
	_portamap.setAttribute('id','portamapa');			
	_div.appendChild(_portamap);
	
	
	_dd=document.createElement('div');
	_dd.setAttribute('id','sup_ha');
	_portamap.appendChild(_dd);
							
	_dmap=document.createElement('div');
	_dmap.setAttribute('class','mapa');			
	_portamap.appendChild(_dmap);
	
			
					

	return(_ttt);
			
}	


function completarFicha(_ficha,_idd){
	
	if(_idd===''){return;}
	_datd=_DataDistritos.distritos[_idd];
	//console.log(_datd)	;
	
	_datg=_DataDistritos.grupos[_datd.id_p_cot_grupos_id];
	
	_ficha.setAttribute('id','dis'+_idd);			
	_ficha.querySelector('#cod_grupo').innerHTML=_datd.grupo;
	_ficha.querySelector('#nom_grupo').innerHTML=_datd.descripciongrupo;
	_ficha.querySelector('#contiene_cod_grupo').style.backgroundColor=_datg.co_color;
	
	_ficha.querySelector('#cod_clase').innerHTML=_datd.nom_clase;
	_ficha.querySelector('#nom_clase').innerHTML=_datd.des_clase;
	_ficha.querySelector('#contiene_cod_clase').style.backgroundColor=_datd.co_color;
	
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
	
	
	_ficha.querySelector('#codigo').innerHTML=_datd.grupo+'-'+_datd.nom_clase;
	_ficha.querySelector('#codigo').style.backgroundColor=_col;
	
	

	_div=_ficha.querySelector('#cuadrito');
		
	_comp=JSON.parse(_DataDistritos.proyecto.contenido_cuadrito);
	
	if(_comp.tipo=='seccion'){
	
		_sp=document.createElement('span');
		_sp.setAttribute('class','titulito');
		
		_sp.innerHTML=_DataDistritos.secciones[_comp.id].nombre;
		_div.appendChild(_sp);
		
		_sp=document.createElement('span');
		_sp.setAttribute('id','contenido');
		_div.appendChild(_sp);
		if(_datd.secciones[_comp.id]!=undefined){
			_sp.innerHTML=_datd.secciones[_comp.id].texto;
		}
	}
	
	
	
	
	_col=_ficha.querySelector('#columna1');
	//console.log(_DataDistritos.proyecto.contenido_columna_izq);
	_contenido_columna_izq=JSON.parse(_DataDistritos.proyecto.contenido_columna_izq);
	
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
	
			_sp=document.createElement('span');
			_div.appendChild(_sp);
			if(_datd.secciones[_comp.id]!=undefined){
				_sp.innerHTML=_datd.secciones[_comp.id].texto;
			}
		}
	}


	_col=_ficha.querySelector('#columna2');
	
	
	_contenido_columna_der=JSON.parse(_DataDistritos.proyecto.contenido_columna_der);
	
	for(_nc in _contenido_columna_der){
		_comp=_contenido_columna_der[_nc];		
		
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
	
			_sp=document.createElement('span');
			if(_datd.secciones[_comp.id]!=undefined){
				_sp.innerHTML=_datd.secciones[_comp.id].texto;
			}
			_div.appendChild(_sp);
			
		}

	}

		
	_ficha.querySelector('#portamapa .mapa').setAttribute('id','mapad'+_idd);


	var _sy = new ol.style.Style({
		stroke: new ol.style.Stroke({color : 'rgba('+_frgb['r']+','+_frgb['g']+','+_frgb['b']+',1)', width : 3}),
		fill: new ol.style.Fill({color : 'rgba('+_frgb['r']+','+_frgb['g']+','+_frgb['b']+',0.5)'})
	});	
	_source = new ol.source.Vector({});
	
	
	var _sj = new ol.style.Style({
		stroke: new ol.style.Stroke({color : 'rgba(0,0,0,1)', width : 2, lineDash:[3,5], lineCap: 'butt'})		
	});	
	_sourcej = new ol.source.Vector({});	
	
	_Maps[_idd]= new ol.Map({
		  target: 'mapad'+_idd,
		  layers: [
		  		  
			new ol.layer.Tile({
				source:new ol.source.TileWMS({
				url: 'https://wms.ign.gob.ar/geoserver/mapabase_gris/gwc/service/wms',
				params: {
					'VERSION': '1.1.1',
					tiled: true,
					LAYERS: 'mapabase_gris',
					STYLES: '',
				}
				})
			}),
			new ol.layer.Vector({
			  source: _source,
			  style: _sy
			}),
			new ol.layer.Vector({
			  source: _sourcej,
			  style: _sj
			})
		  ],
		  view: new ol.View({
			center: [0, 0],
			zoom: 0
		  })
		});
	
	_cant_geoms=0;
	for(_nz in _datd.zonas){
		
		_datz = _datd.zonas[_nz];
		_cant_geoms++;
		//console.log(_datz.geotx);
		//console.log(_datz.geotx);
		var format = new ol.format.WKT();
		var _feat = format.readFeature(_datz.geotx, {
			dataProjection: 'EPSG:3857',
			featureProjection: 'EPSG:3857'
		});
		_feat.setProperties({
			'idai':_datz.id,
			'nombre':_datd.grupo+'-'+_datd.nom_clase
		});
		_source.addFeature(_feat);	
					
	}
	
	
	_cant_geomsB=0;
	for(_nz in _DataDistritos.jurisdiccion.zonas){
		
		_datz = _DataDistritos.jurisdiccion.zonas[_nz];
		_cant_geomsB++;
		//console.log(_datz.geotx);
		//console.log(_datz);
		var format = new ol.format.WKT();
		var _feat = format.readFeature(_datz, {
			dataProjection: 'EPSG:3857',
			featureProjection: 'EPSG:3857'
		});
		_feat.setProperties({
		});
		_sourcej.addFeature(_feat);						
	}
	
	if(_cant_geomsB>0){		
		_Maps[_idd].getView().fit(_sourcej.getExtent(), _Maps[_idd].getSize());
	}else if(_cant_geoms>0){
		_Maps[_idd].getView().fit(_source.getExtent(), _Maps[_idd].getSize());
		_Maps[_idd].getView().setZoom(11);
	}
	
}

	
function mostrarContenidosBase_Tabla(){
	
	_cont=document.querySelector('#contenido');
	_cont.innerHTML='';

	document.querySelector('head title').innerHTML=_DataDistritos.proyecto.nombre
	document.querySelector('#menu #titulopagina').innerHTML=_DataDistritos.proyecto.nombre;
	document.querySelector('#menu #dataproyecto').innerHTML='<b>v:'+_DataDistritos.proyecto.version+'</b> '+_DataDistritos.proyecto.descripcion;
	
	for(_nd in _DataDistritos.distritosOrden){
		
		_ttt=document.createElement('table');
		_ttt.setAttribute('iddis',_idd);			
		_cont.appendChild(_ttt);
		
		_idd=_DataDistritos.distritosOrden[_nd];
		_datd=_DataDistritos.distritos[_idd];
		
		_datg=_DataDistritos.grupos[_datd.id_p_cot_grupos_id];
		_dis=document.createElement('tr');
		_dis.setAttribute('class','distrito');
		
		_dis.setAttribute('id','dis'+_idd);			
		_ttt.appendChild(_dis);
	
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
			_ttt.appendChild(_secd);
			
			_sectit=document.createElement('td');
			_sectit.innerHTML='<span>'+_dats.nombre+'</span>';
			_secd.appendChild(_sectit);
			
			_secp=document.createElement('td');
			_secd.appendChild(_secp);
			
			
			if(_DataDistritos.distritos[_idd].secciones[_ids]==undefined){
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
				_secp.innerHTML=_DataDistritos.distritos[_idd].secciones[_ids].texto;
				_secp.setAttribute('pordefecto','no');
				_secp.innerHTML='<span id="desarrollo">'+_DataDistritos.distritos[_idd].secciones[_ids].texto+'</span>';
			}	
			
		}		
	}
}


function mostrarNombres_Tabla(){
	
	document.querySelector('head title').innerHTML=_DataDistritos.proyecto.nombre
	document.querySelector('#menu #titulopagina').innerHTML=_DataDistritos.proyecto.nombre;
	document.querySelector('#menu #dataproyecto').innerHTML='<b>v:'+_DataDistritos.proyecto.version+'</b> '+_DataDistritos.proyecto.descripcion;
	
	_cont=document.querySelector('#contenido');
	_cont.innerHTML='';

	
	_ttt=document.createElement('table');			
	_cont.appendChild(_ttt);

	_dis=document.createElement('tr');
	_dis.setAttribute('class','distrito');
	
	_ttt.appendChild(_dis);
	_titgru=document.createElement('th');
	_titgru.innerHTML='Grupo';		
	_dis.appendChild(_titgru);	
	
	_titdist=document.createElement('th');
	_titdist.innerHTML='Clase';
	_dis.appendChild(_titdist);	
		
	_titdist=document.createElement('th');
	_titdist.innerHTML='Tipo';
	_dis.appendChild(_titdist);
	
	_titdist=document.createElement('th');
	_titdist.innerHTML='Nombre Anterior';
	_dis.appendChild(_titdist);
		
		
	for(_nd in _DataDistritos.distritosOrden){
		
		
		
		_idd=_DataDistritos.distritosOrden[_nd];
		_datd=_DataDistritos.distritos[_idd];
		
		_datg=_DataDistritos.grupos[_datd.id_p_cot_grupos_id];
		_dis=document.createElement('tr');
		_dis.setAttribute('class','distrito');
		
		_dis.setAttribute('id','dis'+_idd);	
		_dis.setAttribute('iddis',_idd);			
		_ttt.appendChild(_dis);
	
		_titgru=document.createElement('th');
		_sp=document.createElement('span');
		_sp.innerHTML=_datd.grupo+': '+_datd.descripciongrupo;
		_sp.style.backgroundColor=_datg.co_color;
		_titgru.style.backgroundColor=_datg.co_color;
		_titgru.appendChild(_sp);
		_dis.appendChild(_titgru);
		
		
		_titdist=document.createElement('th');
		_titdist.setAttribute('onclick','consultarDistrito(this.parentNode.getAttribute("iddis"))');
		_sp=document.createElement('span');
		_sp.innerHTML=_datd.nom_clase+': '+ _datd.des_clase;
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
		
		
		_sp.style.backgroundColor=_col;
		_titdist.style.backgroundColor=_col;
		_titdist.appendChild(_sp);
		
		_dis.appendChild(_titdist);
		
		_titdist=document.createElement('th');
		_titdist.setAttribute('onclick','consultarDistrito(this.parentNode.getAttribute("iddis"))');
		_sp=document.createElement('span');
		_sp.innerHTML=_datd.grupo+'-'+_datd.nom_clase+': '+ _datd.descripciongrupo + ' ' + _datd.des_clase;
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
		
		_sp.style.backgroundColor=_col;
		_titdist.style.backgroundColor=_col;
		_titdist.appendChild(_sp);
		
		_dis.appendChild(_titdist);
		
		_ids= _DataDistritos.seccionesOrden[0];
		
		if(_DataDistritos.distritos[_idd].secciones[_ids]==undefined){
			_tx='';
		}else{
			_tx=_DataDistritos.distritos[_idd].secciones[_ids].texto;
		}
		
		
		_titdist=document.createElement('td');
		_sp=document.createElement('span');
		_sp.innerHTML=_tx;
		_titdist.appendChild(_sp);
		
		_dis.appendChild(_titdist);
		
		
			
	}
}



function mostrarContenidosBase_Texto(){
	
	_cont=document.querySelector('#contenido');
	_cont.innerHTML='';
	
	document.querySelector('head title').innerHTML=_DataDistritos.proyecto.nombre
	document.querySelector('#menu #titulopagina').innerHTML=_DataDistritos.proyecto.nombre;
	document.querySelector('#menu #dataproyecto').innerHTML='<b>v:'+_DataDistritos.proyecto.version+'</b> '+_DataDistritos.proyecto.descripcion;
	
	for(_nd in _DataDistritos.distritosOrden){
		

		
		_idd=_DataDistritos.distritosOrden[_nd];
		_datd=_DataDistritos.distritos[_idd];
		
		_datg=_DataDistritos.grupos[_datd.id_p_cot_grupos_id];
		_dis=document.createElement('h1');
		//_dis.setAttribute('onclick','consultarDistrito(this.parentNode.getAttribute("iddis"))');
		_dis.setAttribute('class','distrito');
		
		_dis.setAttribute('id','dis'+_idd);			
		_cont.appendChild(_dis);
	
		_sp=document.createElement('span');
		_sp.innerHTML=_datd.grupo+': '+_datd.descripciongrupo;
		//_sp.style.backgroundColor=_datg.co_color;
		_dis.appendChild(_sp);
		
		
		_sp=document.createElement('span');
		_sp.innerHTML=_datd.grupo+'-'+_datd.nom_clase+': '+ _datd.descripciongrupo + ' ' + _datd.des_clase;
		/*
		_grgb = hexToRgb(_datg.co_color);
		_drgb = hexToRgb(_datd.co_color);
		_frgb={
			'r':(Math.round((_grgb['r']+_drgb['r'])/2)),
			'g':(Math.round((_grgb['g']+_drgb['g'])/2)),
			'b':(Math.round((_grgb['b']+_drgb['b'])/2))		
		};
		_col=rgbToHex(_frgb['r'], _frgb['g'], _frgb['b']);
		_sp.style.backgroundColor=_col;
		*/
		_dis.appendChild(_sp);
		
		_p=document.createElement('p');
		_p.innerHTML='Extensión:'+Math.round(_datd.sup_ha)+' Ha';
		_cont.appendChild(_p);
		
		
		for(_ns in _DataDistritos.seccionesOrden){
			_ids=_DataDistritos.seccionesOrden[_ns];
			_dats=_DataDistritos.secciones[_ids];
			if(_dats.zz_verentabla=='0'){continue;}
			
			_secd=document.createElement('h2');
			_secd.setAttribute('class','seccion');
			_secd.setAttribute('idsecc',_ids);
			_secd.innerHTML=_dats.nombre;
			//_secd.setAttribute('onclick','consultarRedaccion(this.parentNode.getAttribute("iddis"),this.getAttribute("idsecc"))');
			_cont.appendChild(_secd);
			
			
			_ppp=document.createElement('p');
			_cont.appendChild(_ppp);
			if(_DataDistritos.distritos[_idd].secciones[_ids]==undefined){
				if(_dats.pordefecto!=null){
					_ppp.innerHTML='<span id="desarrollo">'+_dats.pordefecto+'</span>';
					_ppp.setAttribute('class','defecto');
					_ppp.setAttribute('pordefecto','si');
				}else{
					_ppp.innerHTML='<span id="desarrollo"></span>';
					_ppp.setAttribute('class','defecto');
					_ppp.setAttribute('pordefecto','si');
				}
			}else{
				_ppp.innerHTML=_DataDistritos.distritos[_idd].secciones[_ids].texto;
				_ppp.setAttribute('pordefecto','no');
				_ppp.innerHTML='<span id="desarrollo">'+_DataDistritos.distritos[_idd].secciones[_ids].texto+'</span>';
			}	
			
		}		
	}
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


function mostrarIndiceGrupos(){
	_cont=document.querySelector('#indicegrupos #lista');
	_cont.innerHTML='';
	
	for(_ng in _DataDistritos.gruposOrden){
		_idg=_DataDistritos.gruposOrden[_ng];
		_datg=_DataDistritos.grupos[_idg];
		
		_gru=document.createElement('a');
		_gru.innerHTML=_datg.nombre+' '+_datg.descripcion;
		_gru.title=_datg.cant_dist+' distritos asociados';
		_gru.setAttribute('onclick','formularGrupo(this.getAttribute("idgru"))');
		_gru.setAttribute('class','grupo');			
		_gru.setAttribute('idgru',_idg);
		_gru.style.backgroundColor=_datg.co_color;
		_cont.appendChild(_gru);		
	}	
}
