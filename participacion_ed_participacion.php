<?php 
/**
 * participacion_ed_participacion.php
 * 
 * actualiza la base insertando una nueva opinión georeferenciada
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
    "geotx" => "set",
    "nombre" => "set",
    "descripcion" => "set",
    "autor" => "set",
    "organizacion" => "set",
    "mail" => "set",
    "nPar" => "set"
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
	INSERT INTO trecc_zonificador.cot_participaciones
		(	
			titulo, 
			desarrollo, 
			autor, 
			organizacion, 
			contacto, 
			ip, 			
			ip2, 		
			fechaunix,
			geom,
			zz_auto_cot_proyectos,
			id_p_cot_parcelas
		
		)
	VALUES (
			'".$_POST['nombre']."', 
			'".$_POST['descripcion']."', 
			'".$_POST['autor']."', 
			'".$_POST['organizacion']."',  
			'".$_POST['mail']."', 			 
			'".$_SERVER['HTTP_X_FORWARDED_FOR']."', 
			'".$_SERVER['REMOTE_ADDR']."', 	
			'".time()."',		
			ST_GeomFromText('".$_POST['geotx']."'),
			'".$_POST['cotID']."',
			'".$_POST['id_p_cot_parcelas']."'					
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
$fila=pg_fetch_assoc($Consulta);
$Log['data']['nid']=$fila['id'];


$Log['res']='exito';
terminar($Log);
