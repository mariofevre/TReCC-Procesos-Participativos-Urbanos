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
	"cotCOD" => "set"
);
foreach($oblig as $k => $v){
	if(!isset($_POST[$k])){
		$Log['res']='error';
		$Log['mg']='Error falta varaible '.$k;
		terminar($Log);
	}	
}



// consulta proyecto
$query="
	
	SELECT 
		cot_proyectos.id, cot_proyectos.nombre, cot_proyectos.descripcion, cot_proyectos.cod_acceso, cot_proyectos.version,
		
		cot_config_fichas.id_p_cot_proyectos, cot_config_fichas.id_p_cot_secciones, cot_config_fichas.contenido_columna_izq, 
		 
		cot_config_fichas.contenido_columna_der, cot_config_fichas.contenido_cuadrito,
		cot_config_fichas.ficha_pc_col_izq,  cot_config_fichas.ficha_pc_col_der,  cot_config_fichas.ficha_pc_cuadrito
		
	FROM 
		trecc_zonificador.cot_proyectos
		LEFT JOIN
		trecc_zonificador.cot_config_fichas 
			ON cot_config_fichas.id_p_cot_proyectos = cot_proyectos.id
	WHERE 
		cot_proyectos.id='".$_POST['cotID']."'
";	

$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
}  




$fila = pg_fetch_assoc($Consulta);
$Log['data']['proyecto']=$fila;

if($Log['data']['proyecto']['cod_acceso']!=$_POST['cotCOD']){
	$Log['mg'][]=utf8_encode('error: en el código de validación verifique la dirección url a la que se está conectando.'.$Log['data']['proyecto']['cod_acceso'].'vs'.$_POST['cotCOD']);
	
	$Log['res']='err';
	terminar($Log);	
}
unset($Log['data']['proyecto']['cod_acceso']);


// consulta todos los grupos disponibles
$query="
	SELECT 
		*				
	FROM 
		trecc_zonificador.cot_grupos	
	WHERE 
		cot_grupos.zz_auto_cot_proyectos='".$_POST['cotID']."'
	AND
		zz_borrada='0'
	ORDER BY 
		orden asc
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
	$Log['data']['grupos'][$fila['id']]['cant_dist']=0;
}






	// consulta todos los distritos generados	
	$query="
		SELECT 
			cot_distritos.id,
			cot_distritos.orden as disorden,			
			cot_distritos.id_p_cot_grupos_id,
			cot_distritos.nom_clase,
			cot_distritos.des_clase,		    		    
			cot_distritos.id_p_cot_jurisdicciones_id,	    
			cot_distritos.co_color,		
			cot_distritos.co_color_final,		
			cot_distritos.zz_cache_tipo,        
			
			cot_grupos.id as idgrupo,
			cot_grupos.nombre as grupo,
			cot_grupos.descripcion as descripciongrupo,
			cot_grupos.co_color as grupo_co_color,
			
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
			cot_distritos.zz_preliminar='0' 
			AND
			cot_distritos.zz_borrada='0' 
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
		$Log['data']['distritos'][$fila['id']]['secciones']=array();
		$Log['data']['distritos'][$fila['id']]['zonas']=array();		
		$Log['data']['distritos'][$fila['id']]['sup_ha']=0;
		$Log['data']['distritos'][$fila['id']]['parcelas']=array();
		$Log['data']['distritos'][$fila['id']]['superficie_pc']=0;
		$Log['data']['distritos'][$fila['id']]['superficie_const']=0;
		$Log['data']['distritos'][$fila['id']]['superficie_max']=0;
		$Log['data']['distritos'][$fila['id']]['fot']=0;
		
		$Log['data']['grupos'][$fila['id_p_cot_grupos_id']]['cant_dist']++;
		
		$tipo_dinamico=$Log['data']['grupos'][$fila['id_p_cot_grupos_id']]['nombre'].'-'.$fila['nom_clase'];
			
		if($tipo_dinamico!=$fila['zz_cache_tipo']){			
			// actualiza el campo zz_cache_tipo que se construye por la combinacion del nombre de grupo y nombre de distirto
			$query="
					UPDATE 
						trecc_zonificador.cot_distritos
						SET
						zz_cache_tipo='".$tipo_dinamico."'
						WHERE
						id='".$fila['id']."'
						AND
						zz_auto_cot_proyectos='".$_POST['cotID']."'
			";
			$Consultab = pg_query($ConecSIG, $query);
			if(pg_errormessage($ConecSIG)!=''){
				$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
				$Log['tx'][]='query: '.$query;
				$Log['res']='err';
				terminar($Log);
			}  
			$Log['data']['distritos'][$fila['id']]['zz_cache_tipo']=$tipo_dinamico;
		}
		
			
		if($fila['id_p_cot_jurisdicciones_id']!=$jurviejo){
			$indices[3]++;
			$indices[4]=1;
			$jurviejo=$fila['id_p_cot_jurisdicciones_id'];
		}else{
			$indices[4]++;
		}
		
		$Log['data']['distritos'][$fila['id']]['autonumeracion']=$indices[1].".".$indices[2].".".$indices[3].".".$indices[4];
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

	$Fotseccs=array();
	
	while($fila =pg_fetch_assoc($Consulta)){
		$Log['data']['seccionesOrden'][]=$fila['id'];
		$Log['data']['secciones'][$fila['id']]=$fila;
		
		$hash=str_replace('.','',$fila['nombre']);
		
		$hash=strtolower($hash);
		if(strpos($hash, "fot")!==false){
			$Fotseccs[$fila['id']]='si';
		}
	}
	
	$Log['data']['fotseccs']=$Fotseccs;

			
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
		
		         
		if(isset($Fotseccs[$fila['id_p_cot_secciones_id']])){
			
			$Log['data']['distritos'][$fila['id_p_cot_distritos_id']]['fot']=max($Log['data']['distritos'][$fila['id_p_cot_distritos_id']]['fot'],(float)(str_replace(',','.',$fila['texto'])));
		}
	}





$query="
SELECT id, id_p_distritos, ST_AsText(geom) as geotx, etiq_partic, ST_Area(geom)/10000 as sup_ha
	FROM trecc_zonificador.cot_zonas
	WHERE
	id_p_cot_proyectos = '".$_POST['cotID']."'
	AND
	zz_borrada='0'
";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
}  
$Log['data']['distritos'][0]=array();
$Log['data']['distritos'][0]['sup_ha']=0;
$Log['data']['distritosOrden'][]=0;
$Log['data']['distritos'][0]['zonas']=array();
while($fila =pg_fetch_assoc($Consulta)){
	if(!isset($Log['data']['distritos'][$fila['id_p_distritos']])){continue;}
	$Log['data']['distritos'][$fila['id_p_distritos']]['zonas'][$fila['id']]=$fila;
	
	$Log['data']['distritos'][$fila['id_p_distritos']]['sup_ha']+=$fila['sup_ha'];
}


if(isset($_POST['discrimina_parcelas'])){
	if($_POST['discrimina_parcelas']=='si'){

		$query="


		SELECT id_p_distritos, nomencla
			FROM trecc_zonificador.cot_parcelas
			WHERE
			id_p_cot_proyectos = '".$_POST['cotID']."'
			AND
			zz_borrada='0'
			ORDER BY nomencla ASC
		";
		$Consulta = pg_query($ConecSIG, $query);
		if(pg_errormessage($ConecSIG)!=''){
			$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
			$Log['tx'][]='query: '.$query;
			$Log['res']='err';
			terminar($Log);
		}  
		while($fila =pg_fetch_assoc($Consulta)){
			if(!isset($Log['data']['distritos'][$fila['id_p_distritos']])){continue;}
			$Log['data']['distritos'][$fila['id_p_distritos']]['parcelas'][]=$fila['nomencla'];
			
		}		
		
	}
}

	
$query="
	SELECT 
		id_p_distritos,
		ROUND(SUM(ST_Area(geom))) as superficie_pc,
		ROUND(SUM(sup_const)) as superficie_const

		FROM trecc_zonificador.cot_parcelas
		WHERE
		id_p_cot_proyectos = '27'
		AND
		zz_borrada='0'
		GROUP BY id_p_distritos
";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
}  
while($fila =pg_fetch_assoc($Consulta)){
	if(!isset($Log['data']['distritos'][$fila['id_p_distritos']])){continue;}
	$Log['data']['distritos'][$fila['id_p_distritos']]['superficie_pc']=$fila['superficie_pc'];
	$Log['data']['distritos'][$fila['id_p_distritos']]['superficie_const']=$fila['superficie_const'];
	$Log['data']['distritos'][$fila['id_p_distritos']]['superficie_max']=$fila['superficie_pc']*$Log['data']['distritos'][$fila['id_p_distritos']]['fot'];
}
	

$query="
SELECT  ST_AsText(geom) as geotx, id, nombre, descripcion, orden, titulo, zz_auto_cot_proyectos, zz_copia_de_id
	FROM trecc_zonificador.cot_jurisdicciones
	WHERE
	zz_auto_cot_proyectos = '".$_POST['cotID']."'
";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
}  
$Log['data']['jurisdiccion']=array();
$Log['data']['jurisdiccion']['zonas']=array();
while($fila =pg_fetch_assoc($Consulta)){
	
	$Log['data']['jurisdiccion']=$fila;
	unset($Log['data']['jurisdiccion']['geotx']);
	$Log['data']['jurisdiccion']['zonas'][]=$fila['geotx'];
	
}


$Log['res']='exito';
terminar($Log);
	
?>
