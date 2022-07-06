<?php 
/**
 * participacion_consulta_participaciones.php
 * 
 * consulta a base de datos de opiniones cargadas y su estado de seguimiento
 * output: json
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
    "cotID" => "mayor,0"
);
foreach($oblig as $k => $v){
	if(!isset($_POST[$k])){
		$Log['res']='error';
		$Log['mg']='Error falta varaible '.$k;
		terminar($Log);
	}	
}

if(!isset($_POST['cotID'])){
	$Log['res']='error';
	$Log['mg']='Error falta varaible idcot';
	terminar($Log);
}

$Log['data']['nPar']=$fila['nPar'];


if(isset($_POST['modo'])){
	if($_POST['modo']=='actual'){
		$whereactual=" AND ( zz_copia_de_id is null OR respuesta_resultado is null OR respuesta_resultado = 'pendiente')";
	}
	
}


$query="	
	SELECT 
			id,
			titulo, 
			desarrollo, 
			autor, 
			organizacion, 
			contacto, 
			ip, 			
			ip2, 		
			fechaunix,
			respuesta_resultado,
			respuesta_por,
			respuesta_observaciones,			
			ST_AsTExt(geom) as geotx
		FROM
			trecc_zonificador.cot_participaciones
		
		WHERE
			zz_auto_cot_proyectos='".$_POST['cotID']."'		
			$whereactual
		ORDER by fechaunix desc
";
	
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.utf8_encode($query);
	$Log['res']='err';
	terminar($Log);
}  
$Log['tx'][]='query: '.utf8_encode($query);
while($fila =pg_fetch_assoc($Consulta)){		
	$Log['data']['participacionesOrden'][]=$fila['id'];
	$Log['data']['participaciones'][$fila['id']]=$fila;
}




$query="	
	SELECT 
			respuesta_resultado,
			count(id) as cant
			
		FROM
			trecc_zonificador.cot_participaciones
		WHERE
			zz_auto_cot_proyectos='".$_POST['cotID']."'		
		
		GROUP BY respuesta_resultado;
";
	
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
}  
$Log['data']['estadisticas'];
while($fila =pg_fetch_assoc($Consulta)){	
	
	$res=$fila['respuesta_resultado'];
	if($res==''){$res='null';}
	$Log['data']['estadisticas'][$res]=$fila['cant'];
}




$Log['res']='exito';
terminar($Log);
