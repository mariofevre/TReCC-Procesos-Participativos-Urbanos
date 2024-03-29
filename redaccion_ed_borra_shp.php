<?php
/**
 * redaccion_ed_borra_shp.php
 * 
 * elimina los archivos shp de proyecto en el servidor.
 * 
*  @package    	TReCC(tm) Procesos Participativos Urbanos
* @author     	TReCC SA
* @author     	<mario@trecc.com.ar> <trecc@trecc.com.ar>
* @author    	www.trecc.com.ar  
* @copyright	2013 2022 TReCC SA
* @license    	http://www.gnu.org/licenses/gpl.html GNU AFFERO GENERAL PUBLIC LICENSE, version 3 (GPL-3.0)
* Este archivo es software libre: tu puedes redistriburlo 
* y/o modificarlo bajo los t�rminos de la "GNU AFFERO GENERAL PUBLIC LICENSE" 
* publicada por la Free Software Foundation, version 3
* 
* Este archivo es distribuido por si mismo y dentro de sus proyectos 
* con el objetivo de ser �til, eficiente, predecible y transparente
* pero SIN NIGUNA GARANT�A; sin siquiera la garant�a impl�cita de
* CAPACIDAD DE MERCANTILIZACI�N o utilidad para un prop�sito particular.
* Consulte la "GNU General Public License" para m�s detalles.
* 
* Si usted no cuenta con una copia de dicha licencia puede encontrarla aqu�: <http://www.gnu.org/licenses/>.
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
