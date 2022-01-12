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
			//mostrarContenidosBase();
			//mostrarIndiceGrupos();
			//mostrarIndiceDistritos();
			mapaGrande();
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
			//mapaGrande();
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
