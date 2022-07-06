<?php
/**
 * redaccion_consulta_carpetas_shp.php
 * 
 * analiza en el servidor el estado de consistencia de los shapefile cargados previo a su procesamiento.
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


if(!isset($_POST['tipo'])){
	$Log['res']='error';
	$Log['tx'][]='error en la variable cont';	
	terminar($Log);
}


if(!isset($_POST['cotID']) || $_POST['cotID']<1){
	$Log['res']='error';
	$Log['tx'][]='falta id de proyecto';	
	terminar($Log);
}

$Hoy_a = date("Y");
$Hoy_m = date("m");	
$Hoy_d = date("d");
$HOY = date("Y-m-d");




$carpeta='./documentos/p_'.str_pad( $_POST['cotID'],6,"0",STR_PAD_LEFT).'/subiendo_shapefile/zonas';
if(!file_exists($carpeta)){
	$Log['data']['carperta existente']='no';	
	$Log['res']='exito';
	terminar($Log);
}


$sc=scandir($carpeta);

foreach($sc as $v){
	
	if($v=='.'){continue;}
	if($v=='..'){continue;}
	
	$Log['data']['archivos guardados'][]=$v;	
	
	$b = explode(".",$v);
	$extO = $b[(count($b)-1)];	
	$nombreSinExt = substr($v,0,(-1*(1+strlen($extO))));
	//$Log['tx'][]=
	$Log['data']['shapes'][$nombreSinExt]['extensiones'][$extO]['cargada']='si';	
}




// Register autoloader
require_once('./terceros/php-shapefile_3.4/src/Shapefile/ShapefileAutoloader.php');
\Shapefile\ShapefileAutoloader::register();

// Import classes
use Shapefile\Shapefile;
use Shapefile\ShapefileException;
use Shapefile\ShapefileReader;

 
$exts=array(
	'shp'=>'obligatorio',
	'dbf'=>'obligatorio',
	'shx'=>'obligatorio',
	'prj'=>'obligatorio'
);
	
foreach($Log['data']['shapes'] as $nombreSinExt => $dat){
	
	$Log['data']['shapes'][$nombreSinExt]['estado']='viable';//definicion preliminar.
		
	foreach($exts as $em =>$st){		
		if(!isset($dat['extensiones'][$em])){
			$Log['data']['shapes'][$nombreSinExt]['estado']='no viable: falta '.$em;
		}else{
			if($dat['extensiones'][$em]['cargada']!='si'){
				$Log['data']['shapes'][$nombreSinExt]['estado']='no viable: falta '.$em;
			}
		}
	}	
	
	
	if($Log['data']['shapes'][$nombreSinExt]['estado']=='viable'){	
		ini_set('display_errors',true);

		$Log['data']['shapes'][$nombreSinExt]['estado']='no viable';//redefinicion preliminar.
		try {		
			$ShapeFile = new ShapefileReader($carpeta.'/'.$nombreSinExt);
			$Log['data']['shapes'][$nombreSinExt]['validez']=$ShapeFile->valid();
			$Log['data']['shapes'][$nombreSinExt]['cant']=$ShapeFile->getTotRecords();
			$Log['data']['shapes'][$nombreSinExt]['tipo']=$ShapeFile->getShapeType(ShapeFile::FORMAT_STR);
			$Log['data']['shapes'][$nombreSinExt]['campos']=$ShapeFile->getFields();
			$Log['data']['shapes'][$nombreSinExt]['prj']=$ShapeFile->getPRJ();
			$Log['data']['shapes'][$nombreSinExt]['charset']=$ShapeFile->getCharset();
			
			$Log['data']['shapes'][$nombreSinExt]['mg']='reconocido '.$ShapeFile->getTotRecords(ShapeFile::FORMAT_STR).' registros '.$ShapeFile->getShapeType(ShapeFile::FORMAT_STR);
			$Log['tx'][]= get_class_methods($ShapeFile);
			
			$Log['data']['shapes'][$nombreSinExt]['estado']='viable';
			
		}catch (ShapeFileException $e) {
			// Print detailed error information
			$Log['data']['shapes'][$nombreSinExt]['Error']='Error '.$e->getCode().' ('.$e->getErrorType().'): '.$e->getMessage();	
			$Log['data']['shapes'][$nombreSinExt]['estado']='no viable: error al leer shapefile';
		}
	}	
}





$Log['res']="exito";
terminar($Log);
