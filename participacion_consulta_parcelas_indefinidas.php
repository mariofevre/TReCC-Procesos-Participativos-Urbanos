<?php 
/**
 * participacion_consulta_parcelas_indefinidas.php
 * 
 * consulta a base de datos de parcelas sin tipo de zona asignada
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






$query="	
SELECT 
		id, id_p_distritos, nomencla
	FROM 
		trecc_zonificador.cot_parcelas
	WHERE
		id_p_distritos ='0'
		AND
		id_p_cot_proyectos='".$_POST['cotID']."'		
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
while($fila =pg_fetch_assoc($Consulta)){		
	$Log['data']['parcelasIndefinidasOrden'][]=$fila['id'];
	$Log['data']['parcelasIndefinidas'][$fila['id']]=$fila;
	$Log['data']['parcelasIndefinidas'][$fila['id']]['participaciones']=array();
}



$query="	
SELECT 
	id, titulo, desarrollo, autor, organizacion, contacto, ip, fechaunix, 
	geom, respuesta_resultado, 
	respuesta_observaciones, respuesta_por, id_p_cot_parcelas
	
	FROM 
	trecc_zonificador.cot_participaciones
	
	WHERE
	(
	zz_copia_de_id is null
	OR
	respuesta_resultado is null
	OR
	respuesta_resultado ='pendiente'
	)
	AND
	id_p_cot_parcelas is not null
";
	
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
}  
while($fila =pg_fetch_assoc($Consulta)){		
	if(!isset($Log['data']['parcelasIndefinidas'][$fila['id_p_cot_parcelas']])){continue;}
	$Log['data']['parcelasIndefinidas'][$fila['id_p_cot_parcelas']]['participaciones'][$fila['id']]=$fila;
}



$Log['res']='exito';
terminar($Log);
