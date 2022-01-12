<?php
/**
 * 
 aplicación para guardar archivos cargados en el sevidor 
 * 
* @package    	geoGEC
* @author     	GEC - Gestión de Espacios Costeros, Facultad de Arquitectura, Diseño y Urbanismo, Universidad de Buenos Aires.
* @author     	<mario@trecc.com.ar>
* @author    	http://www.municipioscosteros.org
* @author		based on https://github.com/mariofevre/TReCC-Mapa-Visualizador-de-variables-Ambientales
* @copyright	2018 Universidad de Buenos Aires
* @copyright	esta aplicación se desarrolló sobre una publicación GNU 2017 TReCC SA
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


if(!isset($_POST['archivo'])){
	$Log['res']='error';
	$Log['tx'][]='error en la variable archivo';	
	terminar($Log);
}

if(!isset($_POST['cotID']) || $_POST['cotID']<1){
	$Log['res']='error';
	$Log['tx'][]='falta id de proyecto';	
	terminar($Log);
}

if(strpos($_POST['archivo'],"..")){
	$Log['res']='error';
	$Log['mg'][]='el nombre de archivo que intenta borrar no cumple con los criterios de seguridad';	
	terminar($Log);			
}
if(strpos($_POST['archivo'],"/")){
	$Log['res']='error';
	$Log['mg'][]='el nombre de archivo que intenta borrar no cumple con los criterios de seguridad';	
	terminar($Log);			
}

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
	
	$b = explode(".",$v);
	$extO = $b[(count($b)-1)];	
	$nombreSinExt = substr($v,0,(-1*(1+strlen($extO))));	
	if($nombreSinExt==$_POST['archivo']){
		unlink($carpeta.'/'.$v);
		$Log['data']['archivos elimiados'][]=$v;	
	}
}

$Log['res']="exito";
terminar($Log);
