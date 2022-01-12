<?php 
/**
* redacciones_consulta.php
*
* redacciones_consulta.php se incorpora en la carpeta raiz como una función complementaria básica 
* para aquellas aplicaciones que consultan el listado de redacciones realizdas
* contiene dos funciones: una función que realiza una búsqueda en la base de datos y evalua los resultados devolviendo un array.
* otra función: con el array resultante de la función anterior genera un listado de los resultados en código HTML.
* 
* @package    	TReCC(tm) paneldecontrol.
* @subpackage 	documentos
* @author     	TReCC SA
* @author     	<mario@trecc.com.ar> <trecc@trecc.com.ar>
* @author    	www.trecc.com.ar  
* @copyright	2013 2014 TReCC SA
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
    "cotID" => "mayor,0",
    "iddist" => "mayor,0"
);
foreach($oblig as $k => $v){
	if(!isset($_POST[$k])){
		$Log['res']='error';
		$Log['mg']='Error falta varaible '.$k;
		terminar($Log);
	}	
}


$Log['data']['iddist']=$_POST['iddist'];

// consulta todos los distritos generados	
$query="
	SELECT 
		cot_distritos.id,
		cot_distritos.orden as disorden,			
		cot_distritos.id_p_cot_grupos_id,
		cot_distritos.nom_clase,
		cot_distritos.des_clase,	    		    
		cot_distritos.id_p_cot_jurisdicciones_id,
		cot_distritos.orden,    
		cot_distritos.co_color,    
		
		cot_grupos.id as idgrupo,
		cot_grupos.nombre as grupo,
		cot_grupos.descripcion as descripciongrupo,
		cot_grupos.co_color as colorgrupo,
		
		cot_jurisdicciones.id as jurid,
		cot_jurisdicciones.nombre as jurisdiccion,
		cot_jurisdicciones.orden as jurisdiccorden,		  
		cot_jurisdicciones.titulo as jurisdictitulo,			      
		cot_jurisdicciones.descripcion as descripcionjurisdiccion	    
		
	FROM trecc_zonificador.cot_distritos
	
	LEFT JOIN 
		trecc_zonificador.cot_grupos
		ON cot_grupos.id= cot_distritos.id_p_cot_grupos_id
		AND cot_grupos.zz_auto_cot_proyectos = '".$_POST['cotID']."'
					
	LEFT JOIN 
		trecc_zonificador.cot_jurisdicciones
		ON cot_jurisdicciones.id = cot_distritos.id_p_cot_jurisdicciones_id
		AND cot_jurisdicciones.zz_auto_cot_proyectos = '".$_POST['cotID']."'	
	WHERE 
		cot_distritos.zz_auto_cot_proyectos='".$_POST['cotID']."'
		AND
		(	cot_distritos.zz_preliminar='0' 
			OR
			cot_distritos.id='".$_POST['iddist']."'
		)
	ORDER BY 
		jurisdiccorden asc, jurisdiccion asc, grupo, disorden asc, cot_distritos.id ASC
";	

$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
}  


$Contenido = '';
$idejec = '';


$indices[1]=0;
$indices[2]=0;
$indices[3]=0;
$indices[4]=0;
$jurviejo='';

while($fila =pg_fetch_assoc($Consulta)){		
	$Log['data']['distritosOrden'][]=$fila['id'];
	$Log['data']['distritos'][$fila['id']]=$fila;
	$Log['data']['distritos'][$fila['id']]['nomenclatura']=$fila['grupo'].$fila['NOMclase']." ".$fila['jurisdiccion'];
	
	if($fila['id_p_cot_jurisdicciones_id']!=$jurviejo){
		$indices[3]++;
		$indices[4]=1;
		$jurviejo=$fila['id_p_cot_jurisdicciones_id'];
	}else{
		$indices[4]++;
	}
	
	$Log['data']['distritos'][$fila['id']]['autonumeracion']=$indices[1].".".$indices[2].".".$indices[3].".".$indices[4];
}




// consulta todos los grupos disponibles
$query="
	SELECT 
		*				
	FROM 
		trecc_zonificador.cot_grupos	
	WHERE 
		cot_grupos.zz_auto_cot_proyectos='".$_POST['cotID']."'
	
	ORDER BY 
		nombre asc
";	

$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
}  


while($fila =pg_fetch_assoc($Consulta)){		
	$Log['data']['gruposOrden'][]=$fila['id'];
	$Log['data']['grupos'][$fila['id']]=$fila;
}


// consulta todas las las secciones de redaccion y su contenido para cada distrito
$query="
	
	SELECT
		cot_secciones.id,
		cot_secciones.nombre,
		cot_secciones.pordefecto,	    
		cot_secciones.postfijo,
		cot_secciones.campo,
		cot_secciones.zz_verentabla
		
	FROM trecc_zonificador.cot_secciones
	
	WHERE
		cot_secciones.zz_auto_cot_proyectos = '".$_POST['cotID']."'
		
	order by orden
";
	
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
}  

while($fila =pg_fetch_assoc($Consulta)){
	$Log['data']['seccionesOrden'][]=$fila['id'];
	$Log['data']['secciones'][$fila['id']]=$fila;
}


// consulta todas las las secciones de redaccion y su contenido para cada distrito
$query="	
	SELECT			
		cot_parrafos.id as parrid,    
		cot_parrafos.incluirpost,
		cot_parrafos.id_p_cot_secciones_id,
		cot_parrafos.id_p_cot_distritos_id,
		cot_parrafos.texto
		
	FROM 
		trecc_zonificador.cot_parrafos
	
	WHERE
		cot_parrafos.zz_auto_cot_proyectos = '".$_POST['cotID']."'
		AND
		cot_parrafos.id_p_cot_distritos_id = '".$_POST['iddist']."'
";
	
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
}  

while($fila =pg_fetch_assoc($Consulta)){

	$Log['data']['distritos'][$fila['id_p_cot_distritos_id']]['secciones'][$fila['id_p_cot_secciones_id']]=$fila;
}


$Log['res']='exito';
terminar($Log);
	
