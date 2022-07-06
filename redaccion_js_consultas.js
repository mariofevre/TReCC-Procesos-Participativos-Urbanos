/**
 * redaccion_js_consultas.js
 * 
 * funciones de consulta ajax para interactuar con la base de datos desde la redaccion general del proyecto
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
			mostrarContenidosBase_Fichas();
			mostrarIndiceGrupos();
			mostrarIndiceDistritos();
		}
	})
	delete _parametros;	
}


function descargarZonas(){
	
	alert('Función en desarrollo');
}
	
function eliminarParcelas(){
	if(!confirm('¿Eliminamos todas las geometrías de parcelas este proyecto? Vas a tener que volver a subir un archivo shapefile. \n Las definiciones y las geomnetrías de zona no se eliminarán.')){return;}
	
	_parametros = {
		'cotCOD': _COTCOD,		
		'cotID': _COTID
	};
	$.ajax({
		url:   './admin_elimina_parcelas.php',
		type:  'post',
		data: _parametros,
		error: function(XMLHttpRequest, textStatus, errorThrown){ 
			alert("Estado: " + textStatus); alert("Error: " + errorThrown); 
		},
		success:  function (response){	
			_res = PreprocesarRespuesta(response);
			
			if(_res.res!='exito'){alert('falló la accion solicitada');}
			
			consultarContenidosBase();
		}
	})
	delete _parametros;	
}
		
function eliminarZonas(){
	if(!confirm('¿Eliminamos todas las geometrías de zonas de este proyecto? Vas a tener que volver a subir un archivo shapefile. \nLas definiciones de grupos y clase para los tipos de zona no se eliminarán.')){return;}
	
	_parametros = {
		'cotCOD': _COTCOD,		
		'cotID': _COTID
	};
	$.ajax({
		url:   './admin_elimina_zonas.php',
		type:  'post',
		data: _parametros,
		error: function(XMLHttpRequest, textStatus, errorThrown){ 
			alert("Estado: " + textStatus); alert("Error: " + errorThrown); 
		},
		success:  function (response){	
			_res = PreprocesarRespuesta(response);
			
			if(_res.res!='exito'){alert('falló la accion solicitada');}
			
			consultarContenidosBase();
		}
	})
	delete _parametros;	
}


			
			

function duplicarProyecto(){
	if(!confirm('¿Generamos una nueva versión de este proyecto, dejando este espacio obsoleto?')){return;}
	
	_parametros = {
		'cotID': _COTID,
		'cotCOD': _COTCOD		
	};
	$.ajax({
		url:   './admin_duplica_proyecto.php',
		type:  'post',
		data: _parametros,
		error: function(XMLHttpRequest, textStatus, errorThrown){ 
			alert("Estado: " + textStatus); alert("Error: " + errorThrown); 
		},
		success:  function (response){	
			_res = PreprocesarRespuesta(response);
			
			if(_res.res!='exito'){alert('falló la accion solicitada');}
			
			_str='Se ha generado un nuevo poryeco con estas referencias que deberá copiar';
			_str+='<br> id: '+_res.data.nid;
			_str+='<br> codigo: '+_res.data.ncod
			_str+='<br> link: http://190.111.246.33/extranet/zonificador/redaccion.php?id='+_res.data.nid+'&cod='+_res.data.ncod;
			
			alert(_p);
			_p=document.createElement('p');
			_p.innerHTML=_str;
			document.querySelector('#contenido').insertBefore(_p,document.querySelector('#contenido').firstChild);
		}
	})
	delete _parametros;	
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
			
		}
	})
	delete _parametros;	
}

function guardarRedaccion(){
	
	
	_parametros = {
		'cotID': _COTID,
		'cotCOD': _COTCOD,		
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
		'cotID': _COTID,
		'cotCOD': _COTCOD		
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
	
	if(document.querySelector('#formdistrito [name="color_final_definido"]').checked==true){
		_co_final=document.querySelector('#formdistrito [name="color_final"]').value;
	}else{
		_co_final='';
	}
	
	_parametros = {
		'cotID': _COTID,		
		'cotCOD': _COTCOD,		
		"iddist":document.querySelector('#formdistrito [name="iddist"]').value,
		"id_p_cot_grupos":document.querySelector('#formdistrito [name="id_p_cot_grupos_id"]').value,
		"cot_grupos_nombre-n":document.querySelector('#formdistrito [name="cot_grupos_nombre-n"]').value,
		"cot_grupos_descripcion-n" :document.querySelector('#formdistrito [name="cot_grupos_descripcion-n"]').value,
		"nom_clase":document.querySelector('#formdistrito [name="nom_clase"]').value,
		"des_clase":document.querySelector('#formdistrito [name="des_clase"]').value,
		"orden":document.querySelector('#formdistrito [name="orden"]').value,
		"co_color":document.querySelector('#formdistrito [name="co_color"]').value,
		"co_color_final":_co_final
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
		'cotCOD': _COTCOD,		
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
		'cotCOD': _COTCOD,		
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
		'cotCOD': _COTCOD,		
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
				
				
				generarCandidato(_shapef,_dat);
				
				
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


function regenerarSLD(){
document.querySelector('#formshapefile #candidatos').innerHTML='';
	_parametros = {
		'cotID': _COTID,
		'cotCOD': _COTCOD
	};	
	
	$.ajax({
		url:   './admin_generar_sld.php',
		type:  'post',
		data: _parametros,
		error: function(XMLHttpRequest, textStatus, errorThrown){ 
			alert("Estado: " + textStatus); alert("Error: " + errorThrown); 
		},
		success:  function (response){	
			_res = PreprocesarRespuesta(response);
			
			if(_res.res!='exito'){alert('error');return;}
			
			
		}
	})
	
}
			
			
			
			
function procesarShapefile(_archivo,_avance,_safemode){
	if(_safemode!='no'){
		if(!confirm('¿procesamos este shapefile '+_archivo+'.shp?... Segure?')){return;}
	}
	
	_parametros = {
		'cotID': _COTID,
		'cotCOD': _COTCOD,	
		'archivo':_archivo,	
		'campolink':document.querySelector('#formshapefile .candidato[shapefile="'+_archivo+'"] [name="campolink"]').value,
		'campolinkparcelas':document.querySelector('#formshapefile .candidato[shapefile="'+_archivo+'"] .modoparcelas [name="campolink"]').value,
		'camponomencla':document.querySelector('#formshapefile .candidato[shapefile="'+_archivo+'"] .modoparcelas [name="camponomencla"]').value,
		'camposuperf':document.querySelector('#formshapefile .candidato[shapefile="'+_archivo+'"] .modoparcelas [name="camposuperf"]').value,
		'contenido':document.querySelector('#formshapefile [name="contenido"]:checked').value,
		'avance':_avance
	};
	
	if(_avance==0){
		_avance=document.querySelector('#formshapefile .candidato[shapefile="'+_archivo+'"] #avance');
		_avance.innerHTML='<img src="./img/cargando.gif">0%';
	}
	$.ajax({
		url:   './redaccion_ed_procesa_shp.php',
		type:  'post',
		data: _parametros,
		error: function(XMLHttpRequest, textStatus, errorThrown){ 
			alert("Estado: " + textStatus); alert("Error: " + errorThrown); 
		},
		success:  function (response){	
			_res = PreprocesarRespuesta(response);
			
			
			
			if(_res.res!='exito'){alert('error');return;}
			
			_avance=document.querySelector('#formshapefile .candidato[shapefile="'+_res.data.archivo+'"] #avance');
			_avance.innerHTML='<img src="./img/cargando.gif">'+_res.data.avanceP+'%';			
			
			if(_res.data.avance!='final'){
				procesarShapefile(_res.data.archivo,_res.data.avance,'no');
				return;
			}
			
			consultarContenidosBase();
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
				
				consultarCargaShape();
				 				
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

