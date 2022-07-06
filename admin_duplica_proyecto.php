<?php 
/**
* admin_duplica_proyectos.php 
*
* Duplica un proyecto, sus componentes y sus geometrías
* 
* @package    	TReCC(tm) Procesos Participativos Urbanos
* @author     	TReCC SA
* @author     	<mario@trecc.com.ar> <trecc@trecc.com.ar>
* @author    	www.trecc.com.ar  
* @copyright	2013 2022 TReCC SA
* @license    	https://www.gnu.org/licenses/agpl-3.0-standalone.html GNU AFFERO GENERAL PUBLIC LICENSE, version 3 (agpl-3.0)
* Este archivo es parte de TReCC(tm) paneldecontrol y de sus proyectos hermanos: baseobra(tm), TReCC(tm) intraTReCC  y TReCC(tm) Procesos Participativos Urbanos.
* Este archivo es software libre: tu puedes redistriburlo 
* y/o modificarlo bajo los términos de la "GNU AFero General Public License version 3" 
* publicada por la Free Software Foundation
* 
* Este archivo es distribuido por si mismo y dentro de sus proyectos 
* con el objetivo de ser útil, eficiente, predecible y transparente
* pero SIN NIGUNA GARANTÍA; sin siquiera la garantía implícita de
* CAPACIDAD DE MERCANTILIZACIÓN o utilidad para un propósito particular.
* Consulte la "GNU General Public License" para más detalles.
* 
* Si usted no cuenta con una copia de dicha licencia puede encontrarla aquí: <http://www.gnu.org/licenses/>.
*/

/**
* genera listado html de argumentaciones
*
* @param int $ID id de la argumentación. null devuelve la totalidad de argumentaciónes cargadas por este usuario.
* @param int $seleccion permite definir modos de selección, algunos modos de selección pueder ser restringidos a ciertos tpos de usuarios.
* @return array Retorna el listado de argumentaciones, sus imágenes y sus localizaciónes
*/



ini_set('display_errors',true);
include('./includes/header.php');
ini_set('display_errors',true);

$Log=array();
global $Log;
$Log['data']=array();
$Log['tx']=array();
$Log['mg']=array();
$Log['acc']=array();
$Log['res']='';
$Log['loc']='';


function terminar($Log){
	$res=json_encode($Log);
	if($res==''){$res=print_r($Log,true);}
	echo $res;
	exit;
}


$oblig=array(
    "cotID" => "mayor,0"
);

foreach($oblig as $k => $v){
	
	if(!isset($_POST[$k])){
		
		$Log['res']='error';
		$Log['mg'][]='Error falta varaible '.$k;
		terminar($Log);
	}	
}




//Duplica contenidos de Tabla Proyectos
$query="
	INSERT INTO 
	trecc_zonificador.cot_proyectos(
		nombre, descripcion, 
		cod_acceso
		)
	SELECT 
		nombre, descripcion, 
		'".cadenaArchivo(7)."'
		
	FROM trecc_zonificador.cot_proyectos as cp
	WHERE cp.id='".$_POST['cotID']."'
	RETURNING id
";	

$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
}  
$fila=pg_fetch_assoc($Consulta);
$Log['data']['nid']=$fila['id'];

$query="
	SELECT * FROM
	trecc_zonificador.cot_proyectos
	WHERE
	id='".$Log['data']['nid']."'
";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
} 
$fila=pg_fetch_assoc($Consulta);
$Log['data']['ncod']=$fila['cod_acceso'];



//Duplica contenidos de Tabla Grupos	
$query="
	INSERT INTO
	trecc_zonificador.cot_grupos(
		nombre, 
		descripcion, 
		zz_auto_cot_proyectos, 
		co_color, 
		orden, 
		zz_borrada, 
		zz_copia_de_id
	)
	SELECT 
		nombre, 
		descripcion, 
		'".$Log['data']['nid']."', 
		co_color, 
		orden, 
		zz_borrada, 
		id
	FROM
	trecc_zonificador.cot_grupos as cg
	WHERE
	cg.zz_auto_cot_proyectos ='".$_POST['cotID']."'
	AND
	zz_borrada = '0'
";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
}  


$query="
	SELECT * FROM
	trecc_zonificador.cot_grupos
	WHERE
	zz_auto_cot_proyectos='".$Log['data']['nid']."'
";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
} 
$Log['data']['generados']['grupos']=pg_num_rows($Consulta);
$copia_grupos_n_a_v=array();
$copia_grupos_v_a_n=array();
while($row = pg_fetch_assoc($Consulta)){
	$copia_grupos_n_a_v[$row['id']]=$row['zz_copia_de_id'];
	$copia_grupos_v_a_n[$row['zz_copia_de_id']]=$row['id'];
}

//Duplica contenidos de Tabla Distritos	
$query="
INSERT INTO 
	trecc_zonificador.cot_distritos(
		id_p_cot_grupos_id, 
		des_clase, 
		particular, 
		nom_clase, 
		id_p_cot_jurisdicciones_id,
		orden, 		
		zz_auto_cot_proyectos, 
		zz_preliminar, 
		co_color, 
		zz_cache_tipo, 
		zz_copia_de_id
	)
	SELECT 
		cot_grupos.id, 
		cd.des_clase, 
		cd.particular, 
		cd.nom_clase, 
		'0',
		cd.orden, 
		'".$Log['data']['nid']."', 
		cd.zz_preliminar, 
		cd.co_color, 
		cd.zz_cache_tipo, 
		cd.id
FROM
	trecc_zonificador.cot_distritos as cd
	LEFT JOIN
	trecc_zonificador.cot_grupos
		ON
		cot_grupos.zz_copia_de_id = cd.id_p_cot_grupos_id
		AND
		cot_grupos.zz_auto_cot_proyectos = '".$Log['data']['nid']."'
	WHERE
	cd.zz_auto_cot_proyectos ='".$_POST['cotID']."'
	AND
	cd.zz_borrada = '0'
	AND
	cd.zz_preliminar = '0'
";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
}  


$query="
	SELECT * FROM
	trecc_zonificador.cot_distritos
	WHERE
	zz_auto_cot_proyectos='".$Log['data']['nid']."'
";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
} 
$Log['data']['generados']['distritos']=pg_num_rows($Consulta);
$copia_distrito_n_a_v=array();
$copia_distrito_v_a_n=array();
while($row = pg_fetch_assoc($Consulta)){
	$copia_distrito_n_a_v[$row['id']]=$row['zz_copia_de_id'];
	$copia_distrito_v_a_n[$row['zz_copia_de_id']]=$row['id'];
}
	
	
//Duplica contenidos de Tabla Zonas	
$query="
INSERT INTO 
	trecc_zonificador.cot_zonas(
		id_p_distritos, 
		id_p_cot_proyectos, 
		geom, 
		etiq_partic, 
		etiqueta, 
		zz_copia_de_id		
	)
	SELECT 
	
		cot_distritos.id, 
		'".$Log['data']['nid']."', 
		cz.geom, 
		cz.etiq_partic, 
		cz.etiqueta, 
		cz.id		
	FROM
		trecc_zonificador.cot_zonas as cz
		LEFT JOIN
			trecc_zonificador.cot_distritos
			ON
			cot_distritos.zz_copia_de_id = cz.id_p_distritos
			AND
			cot_distritos.zz_auto_cot_proyectos = '".$Log['data']['nid']."'
		WHERE
	cz.id_p_cot_proyectos ='".$_POST['cotID']."'
	AND
	cz.zz_borrada='0'
";


$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
}  

$query="
	SELECT * FROM
	trecc_zonificador.cot_zonas
	WHERE
	id_p_cot_proyectos='".$Log['data']['nid']."'
";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
} 
$Log['data']['generados']['zonas']=pg_num_rows($Consulta);
$copia_zonas_n_a_v=array();
$copia_zonas_v_a_n=array();
while($row = pg_fetch_assoc($Consulta)){
	$copia_zonas_n_a_v[$row['id']]=$row['zz_copia_de_id'];
	$copia_zonas_v_a_n[$row['zz_copia_de_id']]=$row['id'];
}



//Duplica contenidos de Tabla Secciones		
$query="
	INSERT INTO trecc_zonificador.cot_secciones(
		nombre, pordefecto, postfijo, orden, campo, 
		zz_auto_cot_proyectos, 
		zz_verentabla, 
		zz_copia_de_id
	)
	SELECT 
		nombre, pordefecto, postfijo, orden, campo, 
		'".$Log['data']['nid']."', 
		zz_verentabla,
		id
	FROM
	trecc_zonificador.cot_secciones as cs
	WHERE
	cs.zz_auto_cot_proyectos ='".$_POST['cotID']."'
";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
}  


$query="
	SELECT * FROM
	trecc_zonificador.cot_secciones
	WHERE
	zz_auto_cot_proyectos='".$Log['data']['nid']."'
";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
} 
$Log['data']['generados']['secciones']=pg_num_rows($Consulta);
$copia_secc_n_a_v=array();
$copia_secc_v_a_n=array();
while($row = pg_fetch_assoc($Consulta)){
	$copia_secc_n_a_v[$row['id']]=$row['zz_copia_de_id'];
	$copia_secc_v_a_n[$row['zz_copia_de_id']]=$row['id'];
}
	


//Duplica contenidos de Tabla Parrafos	
$query="
INSERT INTO 
	trecc_zonificador.cot_parrafos(
		id_p_cot_secciones_id, 
		texto, 
		id_p_cot_distritos_id, 
		incluirpost, 
		zz_auto_cot_proyectos, 
		zz_copia_de_id
	)
	SELECT 
		cot_secciones.id,
		cp.texto, 
		cot_distritos.id, 
		cp.incluirpost, 
		'".$Log['data']['nid']."', 
		cp.id
FROM
	trecc_zonificador.cot_parrafos as cp
	LEFT JOIN
	trecc_zonificador.cot_secciones
		ON
		cot_secciones.zz_copia_de_id = cp.id_p_cot_secciones_id
		AND
		cot_secciones.zz_auto_cot_proyectos = '".$Log['data']['nid']."'
	
	LEFT JOIN
	trecc_zonificador.cot_distritos
		ON
		cot_distritos.zz_copia_de_id = cp.id_p_cot_distritos_id
		AND
		cot_distritos.zz_auto_cot_proyectos = '".$Log['data']['nid']."'
			
		
	WHERE
	cp.zz_auto_cot_proyectos ='".$_POST['cotID']."'
";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
}  


$query="
	SELECT * FROM
	trecc_zonificador.cot_parrafos
	WHERE
	zz_auto_cot_proyectos='".$Log['data']['nid']."'
";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
} 
$Log['data']['generados']['parrafos']=pg_num_rows($Consulta);
$copia_parrafos_n_a_v=array();
$copia_parrafos_v_a_n=array();
while($row = pg_fetch_assoc($Consulta)){
	$copia_parrafos_n_a_v[$row['id']]=$row['zz_copia_de_id'];
	$copia_parrafos_v_a_n[$row['zz_copia_de_id']]=$row['id'];
}


//Duplica jurisidicciones del proyecto

	
$query="
INSERT INTO 
	trecc_zonificador.cot_jurisdicciones(
		nombre, descripcion, orden, titulo, 
		zz_auto_cot_proyectos, 
		zz_copia_de_id, 
		geom
		
	)
	SELECT 
		nombre, descripcion, orden, titulo, 
		'".$Log['data']['nid']."',
		id, 
		geom
			
	FROM
		trecc_zonificador.cot_jurisdicciones	
		WHERE
		zz_auto_cot_proyectos='".$_POST['cotID']."'
";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
}  


	

//Duplica contenidos de Tabla cot_participaciones	
$query="
INSERT INTO 
	trecc_zonificador.cot_participaciones(
		titulo, desarrollo, autor, 
		organizacion, contacto, ip, 
		fechaunix, geom, 
		zz_auto_cot_proyectos, 
		ip2,
		zz_copia_de_id,
		respuesta_resultado, 
		respuesta_observaciones, 
		respuesta_por
		
	)
	SELECT 
		concat('".utf8_encode('de versión anterior... ')."',titulo), desarrollo, autor, 
		organizacion, contacto, ip, 
		fechaunix, geom, 
		'".$Log['data']['nid']."', 
		ip2,
		id,
		respuesta_resultado, 
		respuesta_observaciones, 
		respuesta_por
FROM
	trecc_zonificador.cot_participaciones	
	WHERE
	zz_auto_cot_proyectos='".$_POST['cotID']."'
";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
}  


$query="
	SELECT * FROM
	trecc_zonificador.cot_participaciones
	WHERE
	zz_auto_cot_proyectos='".$Log['data']['nid']."'
";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
} 
$Log['data']['generados']['participaciones']=pg_num_rows($Consulta);
$copia_participaciones_n_a_v=array();
$copia_participaciones_v_a_n=array();
while($row = pg_fetch_assoc($Consulta)){
	$copia_participaciones_n_a_v[$row['id']]=$row['zz_copia_de_id'];
	$copia_participaciones_v_a_n[$row['zz_copia_de_id']]=$row['id'];
}


//Duplica configuración de ficha

$query="
	SELECT 
	id, id_p_cot_proyectos, id_p_cot_secciones, 
	contenido_columna_izq, contenido_columna_der, contenido_cuadrito,
	ficha_pc_col_izq, ficha_pc_col_der, ficha_pc_cuadrito
	FROM 
	trecc_zonificador.cot_config_fichas
	WHERE
	id_p_cot_proyectos='".$_POST['cotID']."'
";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
} 
if(pg_num_rows($Consulta)>0){
	$row = pg_fetch_assoc($Consulta);
	
	
	$izq=json_decode($row['contenido_columna_izq'],true);
	
	foreach($izq as $cn => $dat){	
			
		if($izq[$cn]['tipo']=='seccion'){
			if(!isset($copia_secc_v_a_n[$izq[$cn]['id']])){continue;}
			$izq[$cn]['id']=$copia_secc_v_a_n[$izq[$cn]['id']];			
		}
	}
	
	$der=json_decode($row['contenido_columna_der'],true);
	foreach($der as $cn => $dat){		
		if($der[$cn]['tipo']=='seccion'){
			if(!isset($copia_secc_v_a_n[$der[$cn]['id']])){continue;}
			$der[$cn]['id']=$copia_secc_v_a_n[$der[$cn]['id']];			
		}
	}
	
	$cuad=json_decode($row['contenido_cuadrito'],true);	
	if($cuad['tipo']=='seccion'){
		if(!isset($copia_secc_v_a_n[$cuad['id']])){continue;}
		$cuad['id']=$copia_secc_v_a_n[$cuad['id']];			
	}

	$pc_izq=json_decode($row['ficha_pc_col_izq'],true);
	foreach($pc_izq as $cn => $dat){				
		if($pc_izq[$cn]['tipo']=='seccion'){
			if(!isset($copia_secc_v_a_n[$pc_izq[$cn]['id']])){continue;}
			$pc_izq[$cn]['id']=$copia_secc_v_a_n[$pc_izq[$cn]['id']];			
		}
	}
	
	$pc_der=json_decode($row['ficha_pc_col_der'],true);
	foreach($pc_der as $cn => $dat){		
		if($pc_der[$cn]['tipo']=='seccion'){
			if(!isset($copia_secc_v_a_n[$pc_der[$cn]['id']])){continue;}
			$pc_der[$cn]['id']=$copia_secc_v_a_n[$pc_der[$cn]['id']];			
		}
	}
	
	$pc_cuad=json_decode($row['ficha_pc_cuadrito'],true);	
	if($pc_cuad['tipo']=='seccion'){
		if(!isset($copia_secc_v_a_n[$pc_cuad['id']])){continue;}
		$pc_cuad['id']=$copia_secc_v_a_n[$pc_cuad['id']];			
	}
	
		
	$query="
		INSERT INTO
		trecc_zonificador.cot_config_fichas(
			id_p_cot_proyectos, 
			contenido_columna_izq, 
			contenido_columna_der, 
			contenido_cuadrito,
			ficha_pc_col_izq, 
			ficha_pc_col_der, 
			ficha_pc_cuadrito
		)VALUES(
			'".$Log['data']['nid']."', 
			'".json_encode($izq)."', 
			'".json_encode($der)."', 
			'".json_encode($cuad)."', 
			'".json_encode($pc_izq)."', 
			'".json_encode($pc_der)."', 
			'".json_encode($pc_cuad)."'
		)
		RETURNING id
	";
	$Consulta = pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
		$Log['tx'][]='query: '.$query;
		$Log['res']='err';
		terminar($Log);
	} 

}



$Log['res']='exito';
terminar($Log);
