<?php 
/**
 * mapa_zonificacion_consulta_punto_parcelas.php
 * 
 * consulta a base de datos datos de parcela y su zonificación en una coordenada geométrica
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
    "cotID" => "mayor,0",
    "x" => "set",
    "y" => "set"
);
foreach($oblig as $k => $v){
	if(!isset($_POST[$k])){
		$Log['res']='error';
		$Log['mg']='Error falta varaible '.$k;
		terminar($Log);
	}	
}



if($_POST['idparcela']>0){
		
	// consulta los datos de la parcela con este id
	$query="

		SELECT 
			id_p_distritos,  
			ST_AsText(geom) as geotx, 
			ST_Area(ST_Transform(geom, 4326),true) as sup_pol,
			id,
			sup_const, 
			nomencla, 
			zz_ref_tipotx
			
		FROM 
			trecc_zonificador.cot_parcelas
		
		WHERE 
			id_p_cot_proyectos = '".$_POST['cotID']."'
			AND
			zz_borrada='0'
			AND
			id= '".$_POST['idparcela']."'
		LIMIT 1
	";	
	$Log['data']['modo']='id';
}else{
	// consulta los datos de la parcela en este punto 
	$query="

		SELECT 
			id_p_distritos,  
			ST_AsText(geom) as geotx, 
			ST_Area(ST_Transform(geom, 4326),true) as sup_pol,
			id,
			sup_const, 
			nomencla, 
			zz_ref_tipotx
			
		FROM 
			trecc_zonificador.cot_parcelas
		
		WHERE 
			id_p_cot_proyectos = '".$_POST['cotID']."'
			AND
			zz_borrada='0'
			AND
			ST_Intersects(geom, 'SRID=3857;POINT(".$_POST['x']." ".$_POST['y'].")')
		LIMIT 1
	";	
	$Log['data']['modo']='coordenada';
}

$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
}  


while($fila =pg_fetch_assoc($Consulta)){		
	$Log['data']['parcela']=$fila;
}



$Log['res']='exito';
terminar($Log);
	
