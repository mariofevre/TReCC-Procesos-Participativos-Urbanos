function consultarContenidosBase(){
	
	
	_parametros = {
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
			mostrarContenidosBase();
			mostrarIndiceGrupos();
			mostrarIndiceDistritos();
		}
	})
	delete _parametros;	
}


function consultarRedaccion(_iddist,_idsecc){
	
	_parametros = {
		'cotID': _COTID,
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
			
		}
	})
	delete _parametros;	
}

function guardarRedaccion(){
	
	
	_parametros = {
		'cotID': _COTID,
		'iddist': document.querySelector('#formredaccion [name="iddist"]').value,
		'idsecc': document.querySelector('#formredaccion [name="idsecc"]').value,
		'texto': document.querySelector('#formredaccion [name="texto"]').value
	};
	document.querySelector('#formredaccion').setAttribute('estado','inactivo');
	$.ajax({
		url:   './redaccion_ed_redaccion_texto_parrafo.php',
		type:  'post',
		data: _parametros,
		error: function(XMLHttpRequest, textStatus, errorThrown){ 
			alert("Estado: " + textStatus); alert("Error: " + errorThrown); 
		},
		success:  function (response){	
			_res = PreprocesarRespuesta(response);
			//_DataDistritos=_res.data;
			//delete _res;		
			consultarContenidosBase();
		}
	})
	delete _parametros;		
}



function consultarDistrito(_iddist){
	_parametros = {
		'cotID': _COTID,
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

 
function crearDistrito(){

	
	_parametros = {
		'cotID': _COTID
	};
	$.ajax({
		url:   './redaccion_ed_distrito_crear.php',
		type:  'post',
		data: _parametros,
		error: function(XMLHttpRequest, textStatus, errorThrown){ 
			alert("Estado: " + textStatus); alert("Error: " + errorThrown); 
		},
		success:  function (response){	
			_res = PreprocesarRespuesta(response);
			
			consultarDistrito(_res.data.nid);
			
			delete _res;		
			
		}
	})
	delete _parametros;			
		
}

function guardarDistrito(){

	document.querySelector('#formdistrito').setAttribute('estado','inactivo');
	
	_parametros = {
		'cotID': _COTID,		
		"iddist":document.querySelector('#formdistrito [name="iddist"]').value,
		"id_p_cot_grupos":document.querySelector('#formdistrito [name="id_p_cot_grupos_id"]').value,
		"cot_grupos_nombre-n":document.querySelector('#formdistrito [name="cot_grupos_nombre-n"]').value,
		"cot_grupos_descripcion-n" :document.querySelector('#formdistrito [name="cot_grupos_descripcion-n"]').value,
		"nom_clase":document.querySelector('#formdistrito [name="nom_clase"]').value,
		"des_clase":document.querySelector('#formdistrito [name="des_clase"]').value,
		"orden":document.querySelector('#formdistrito [name="orden"]').value,
		"co_color":document.querySelector('#formdistrito [name="co_color"]').value			
	};
	$.ajax({
		url:   './redaccion_ed_distrito.php',
		type:  'post',
		data: _parametros,
		error: function(XMLHttpRequest, textStatus, errorThrown){ 
			alert("Estado: " + textStatus); alert("Error: " + errorThrown); 
		},
		success:  function (response){	
			_res = PreprocesarRespuesta(response);
			
			consultarContenidosBase();
			
			delete _res;		
			
		}
	})
	delete _parametros;				
}
   
  
function guardarGrupo(){

	document.querySelector('#formgrupo').setAttribute('estado','inactivo');
	
	_parametros = {
		'cotID': _COTID,		
		"idgrupo":document.querySelector('#formgrupo [name="idgrupo"]').value,
		"nombre":document.querySelector('#formgrupo [name="nombre"]').value,
		"descripcion":document.querySelector('#formgrupo [name="descripcion"]').value,
		"co_color" :document.querySelector('#formgrupo [name="co_color"]').value	
	};
	$.ajax({
		url:   './redaccion_ed_grupo.php',
		type:  'post',
		data: _parametros,
		error: function(XMLHttpRequest, textStatus, errorThrown){ 
			alert("Estado: " + textStatus); alert("Error: " + errorThrown); 
		},
		success:  function (response){	
			_res = PreprocesarRespuesta(response);
			
			consultarContenidosBase();
			
			delete _res;		
			
		}
	})
	delete _parametros;			
		
}

function  eliminarGrupo(){

	_idgrupo=document.querySelector('#formgrupo [name="idgrupo"]').value;
	_gdat=_DataDistritos.grupos[_idgrupo];
	
	
	if(_gdat.cant_dist>0){alert('No podemos eliminar este grupo. Contiene '+_gdat.cant_dist+' distritos asociados');return;}
	
	if(!confirm(' ¿Eliminamos este grupo? ('+_gdat.nombre+' '+_gdat.descripcion+')... ¿Segure?')){return;}
	
	document.querySelector('#formgrupo').setAttribute('estado','inactivo');
	
	_parametros = {
		'cotID': _COTID,
		'idgrupo': _idgrupo	
	};
	$.ajax({
		url:   './redaccion_ed_grupo_borra.php',
		type:  'post',
		data: _parametros,
		error: function(XMLHttpRequest, textStatus, errorThrown){ 
			alert("Estado: " + textStatus); alert("Error: " + errorThrown); 
		},
		success:  function (response){	
			_res = PreprocesarRespuesta(response);
			

			_div=document.querySelector('#indicegrupos .grupo[idgru="'+_res.data.idgrupo+'"]');
			_div.parentNode.removeChild(_div);
			
			delete _res;		
			
		}
	})
	delete _parametros;				



	
}
   
function eliminarDistrito(){
	
	_iddist=document.querySelector('#formdistrito [name="iddist"]').value;
	_ddat=_DataDistritos.distritos[_iddist];
	_dgrupo=_DataDistritos.grupos[_ddat.id_p_cot_grupos_id];
	
	if(!confirm(' ¿Eliminamos este ditrito? ('+_dgrupo.nombre+'-'+_ddat.nom_clase+')... ¿Segure?')){return;}
	
	document.querySelector('#formdistrito').setAttribute('estado','inactivo');
	
	_parametros = {
		'cotID': _COTID,
		'iddist': _iddist	
	};
	$.ajax({
		url:   './redaccion_ed_distrito_borra.php',
		type:  'post',
		data: _parametros,
		error: function(XMLHttpRequest, textStatus, errorThrown){ 
			alert("Estado: " + textStatus); alert("Error: " + errorThrown); 
		},
		success:  function (response){	
			_res = PreprocesarRespuesta(response);
			
			_div=document.querySelector('#contenido .distrito[iddis="'+_res.data.iddist+'"]');
			_div.parentNode.removeChild(_div);
			
			_div=document.querySelector('#indicedistritos .distrito[iddis="'+_res.data.iddist+'"]');
			_div.parentNode.removeChild(_div);
			
			delete _res;		
			
		}
	})
	delete _parametros;			
}



function consultarCargaShape(){
	document.querySelector('#formshapefile #candidatos').innerHTML='';
	_parametros = {
		'cotID': _COTID,
		'tipo': 'zonas'
	};
	$.ajax({
		url:   './redaccion_consulta_carpetas_shp.php',
		type:  'post',
		data: _parametros,
		error: function(XMLHttpRequest, textStatus, errorThrown){ 
			alert("Estado: " + textStatus); alert("Error: " + errorThrown); 
		},
		success:  function (response){	
			_res = PreprocesarRespuesta(response);
			
			document.querySelector('#formshapefile #estado code').innerHTML=JSON.stringify(_res, null, '\t');
			
			for(_shapef in _res.data.shapes){
				
				_dat=_res.data.shapes[_shapef];
				
				_div=document.createElement('div');
				_div.setAttribute('class','candidato');
				_div.setAttribute('shapefile',_shapef);
				document.querySelector('#formshapefile #candidatos').appendChild(_div);
				
				_h=document.createElement('h2');
				_h.innerHTML=_shapef;
				_div.appendChild(_h);
				
				_a=document.createElement('a');
				_a.innerHTML='Borrar archivos';
				_a.setAttribute('class','eliminar');
				_a.setAttribute('onclick','borrarArchivos("'+_shapef+'")');
				_div.appendChild(_a);				
				
				_p=document.createElement('p');
				_p.innerHTML=_dat.estado;
				_p.setAttribute('estado',_dat.estado);
				_div.appendChild(_p);
				
				_p=document.createElement('p');
				_p.innerHTML='registros:'+_dat.cant;
				_p.innerHTML+='<br>tipo:'+_dat.tipo;
				_div.appendChild(_p);
				
				
				if(_dat.campos!=undefined){
									
					_sel=document.createElement('select');
					_sel.setAttribute('name','campolink');
					_div.appendChild(_sel);
					
					
					for(_campo in _dat.campos){
						_op=document.createElement('option');
						_op.setAttribute('value',_campo);
						_op.innerHTML=_campo;
						_sel.appendChild(_op);
					}
				
					if(_dat.estado=='viable'){
						_in=document.createElement('input');
						_in.setAttribute('value','cargar');
						_in.setAttribute('type','button');
						_in.setAttribute('onclick','procesarShapefile("'+_shapef+'",0)');
						_in.setAttribute('estado',_dat.estado);
						_div.appendChild(_in);
											
					}
				}	
			}
			
			delete _res;		
			
		}
	})
	delete _parametros;		
 	
}



function borrarArchivos(_archivo){
	
	if(!confirm('¿Borramos este shapefile '+_archivo+'.shp?... Segure?')){return;}
		
	_parametros = {
		'cotID': _COTID,
		'archivo': _archivo
	};
	$.ajax({
		url:   './redaccion_ed_borra_shp.php',
		type:  'post',
		data: _parametros,
		error: function(XMLHttpRequest, textStatus, errorThrown){ 
			alert("Estado: " + textStatus); alert("Error: " + errorThrown); 
		},
		success:  function (response){	
			_res = PreprocesarRespuesta(response);
			
			consultarCargaShape();
		}
	})
}



function procesarShapefile(_archivo,_avance){
	if(!confirm('¿procesamos este shapefile '+_archivo+'.shp?... Segure?')){return;}
		
	_parametros = {
		'cotID': _COTID,
		'archivo': _archivo,
		'campolink':document.querySelector('#formshapefile .candidato[shapefile="'+_archivo+'"] [name="campolink"]').value,
		'avance':_avance
	};
	$.ajax({
		url:   './redaccion_ed_procesa_shp.php',
		type:  'post',
		data: _parametros,
		error: function(XMLHttpRequest, textStatus, errorThrown){ 
			alert("Estado: " + textStatus); alert("Error: " + errorThrown); 
		},
		success:  function (response){	
			_res = PreprocesarRespuesta(response);
			
			consultarCargaShape();
		}
	})
}



function drag_over(_event,_this){
				
	_event.preventDefault();
	
	_ini = _event.dataTransfer.getData("text/plain").split(',');
	if(_ini[0]==''){
		//sin datos tal vez un archivo, se asume que debe ser suspendida esta aación
		_this.setAttribute('estadodrag','archivo');
		return;
	}	
	return false; 
}

function drag_out(_event,_this){

	_event.preventDefault();			
	_this.setAttribute('estadodrag','');
	
}

function dropHandler(ev) {
	  console.log('File(s) dropped');
	  ev.preventDefault();
	  document.querySelector('#formshapefile #carga span.upload').setAttribute('estadodrag','terminado');
	  // Prevent default behavior (Prevent file from being opened)
	  ev.preventDefault();
	  if (ev.dataTransfer.items) {
		// Use DataTransferItemList interface to access the file(s)
		for (var i = 0; i < ev.dataTransfer.items.length; i++) {
		  // If dropped items aren't files, reject them
		  if (ev.dataTransfer.items[i].kind === 'file') {		      	
			_nFile++;
			var file = ev.dataTransfer.items[i].getAsFile();
			console.log('... file[' + i + '].name = ' + file.name);
			//crearCuadroCarga(file,_NFile);
			subirDocumento(file,_nFile);
		  }
		}
	  } else {
		// Use DataTransfer interface to access the file(s)
		for (var i = 0; i < ev.dataTransfer.files.length; i++) {
			_nFile++;
			console.log('... file[' + i + '].name = ' + ev.dataTransfer.files[i].name);
			//crearCuadroCarga(ev.dataTransfer.files[i],_NFile);
			subirDocumento(ev.dataTransfer.file[i],_nFile);
		}
	  } 
	  // Pass event to removeDragData for cleanup
	  removeDragData(ev);
}
	
function crearCuadroCarga(_filedata,_nfile){
	/*
	_cuadro=document.querySelector('#cuadrocarga.modelo').cloneNode(true);
	document.querySelector('#columnaCarga').appendChild(_cuadro);
	_cuadro.removeAttribute('class');
	_cuadro.setAttribute('nfile',_nfile);
	console.log(_filedata);
	_cuadro.querySelector('#nombre').innerHTML=_filedata.name;
	_cuadro.querySelector('[name="nombre"]').value=_filedata.name;
	_cuadro.querySelector('#avance #numero').innerHTML='0 %';
	_cuadro.querySelector('#avance #barra').style.width='0%';*/
}

		
function removeDragData(ev) {
  console.log('Removing drag data');	
  if (ev.dataTransfer.items) {
	// Use DataTransferItemList interface to remove the drag data
	ev.dataTransfer.items.clear();
  } else {
	// Use DataTransfer interface to remove the drag data
	ev.dataTransfer.clearData();
  }
}

function subirDocumento(_filedata,_nfile){
	if(_HabilitadoEdicion!='si'){
        alert('su usuario no tiene permisos de edicion');
        return;
    }      
    
	var parametros = new FormData();
	parametros.append('upload',_filedata);
	parametros.append('nfile',_nfile);
	parametros.append('tipo','zona');
	parametros.append('cotID', _COTID);

	var _nombre=_filedata.name;
	
	//_upF=document.querySelector('#columnaCarga [nfile="'+_nfile+'"]');
	_upF=document.createElement('a');
	document.querySelector('#listadosubiendo').appendChild(_upF);
	_upF.setAttribute('nf',_nFile);
	_upF.setAttribute('class',"archivo");
	_upF.setAttribute('size',Math.round(_filedata.size/1000));
	_upF.innerHTML=_filedata.name;
	_im=document.createElement('img');
	_im.setAttribute('class','cargando');
	_im.setAttribute('src','./img/cargando.gif');
	_upF.appendChild(_im);

	_nn=_nfile;
	xhr[_nn] = new XMLHttpRequest();
	xhr[_nn].open('POST', './redaccion_carga_shp.php', true);
	xhr[_nn].upload.li=_upF;
	xhr[_nn].upload.addEventListener("progress", updateProgressMPP, false);

	xhr[_nn].onreadystatechange = function(evt){
		//console.log(evt);

		if(evt.explicitOriginalTarget.readyState==4){
			var _res = $.parseJSON(evt.explicitOriginalTarget.response);
			//console.log(_res);

			if(_res.res=='exito'){				
							
				_file=document.querySelector('#listadosubiendo .archivo[nf="'+_res.data.nfile+'"]');
				
				_file.setAttribute('estado','terminado');
				_file.setAttribute('idfi',_res.data.nid);
				
				_DataConservas[_res.data.conserva.id]=_res.data.conserva;
				//crearFila(_res.data.conserva,'');
				consultarPlanes();
									
			} else {
				_file=document.querySelector('#listadosubiendo .archivo[nf="'+_res.data.nfile+'"]');
				_file.innerHTML+=' ERROR';
				_file.style.color='red';
			}
		}
	};
	xhr[_nn].send(parametros);

}	

function updateProgressMPP(evt) {
	if (evt.lengthComputable) {
		var percentComplete = 100 * evt.loaded / evt.total;		   
		this.li.style.width="calc("+Math.round(percentComplete)+"% - ("+Math.round(percentComplete)/100+" * 6px))";
	} else {
		// Unable to compute progress information since the total size is unknown
	} 
}

