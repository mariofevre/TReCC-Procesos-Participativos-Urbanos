<?php
/**
 * redaccion_carga_shp.php
 * 
 * guarda archivos componentes de un shapefile para su posterior procesamiento recibe shapefiles de parcelas, zonas y jurisdicciones
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
	$Log['tx'][]='error en la variable tipo';	
	terminar($Log);
}


if(!isset($_POST['nfile'])){
	$Log['res']='error';
	$Log['tx'][]='falta nfile';	
	terminar($Log);
}
$Log['data']['nfile']=$_POST['nfile'];

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
    $Log['tx'][]="creando carpeta $carpeta";
    mkdir($carpeta, 0777, true);
    chmod($carpeta, 0777);	
}

$nombre = $_FILES['upload']['name'];
$Log['data']['nombreorig'] = $nombre;
$b = explode(".",$nombre);
$extO = $b[(count($b)-1)];
$ext = strtolower($extO);
$nombreSinExt = substr($nombre,0,(-1*(1+strlen($extO))));

$extVal['prj']='1';
$extVal['qpj']='1';
$extVal['dbf']='1';
$extVal['shp']='1';
$extVal['shx']='1';
$extVal['cpg']='1';
$extVal['xlsx']='1';

if(!isset($extVal[strtolower($ext)])){	
    $Log['tx'][]="solo se aceptan los formatos:";
    foreach($extVal as $k => $v){$Log['tx'][]=" $k,";}
    $Log['tx'][]="archivo cargado: ".$nombre;
    $Log['res']='err';
    terminar($Log);
}

$Log['tx'][]="cargando archivo en modo shapefile.";
$Log['data']['cargado']='shapefile parcial';

$nuevonombre= $carpeta.'/'.$nombreSinExt.'.'.strtolower($ext);
//echo "<br>var:".$e[0] ."esc:".$e[1]."per:".$e[2].alt:".$e[3];	

if (!copy($_FILES['upload']['tmp_name'], $nuevonombre)){
    $Log['tx'][]="Error al copiar $nuevonombre";
    $Log['res']="err";
    terminar($Log);
}

$Log['tx'][]="copiado archivo punto ".strtolower($ext);
$Log['res']="exito";
terminar($Log);
