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


if(!isset($_POST['campolink'])){
	$Log['res']='error';
	$Log['tx'][]='error en la variable campolink';	
	terminar($Log);
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

$Hoy_a = date("Y");
$Hoy_m = date("m");	
$Hoy_d = date("d");
$HOY = date("Y-m-d");







// consulta todas las las secciones de redaccion y su contenido para cada distrito
$query="	
	SELECT 
		id, 
		id_p_cot_grupos_id, des_clase, particular, 
		nom_clase, id_p_cot_jurisdicciones_id, orden, 
		zz_auto_cot_proyectos, zz_preliminar, co_color, zz_borrada, zz_cache_tipo
	FROM trecc_zonificador.cot_distritos
	
	WHERE
		cot_distritos.zz_auto_cot_proyectos = '".$_POST['cotID']."'
";
	
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
}  

while($fila =pg_fetch_assoc($Consulta)){
	$Links[$fila['zz_cache_tipo']]=$fila['id'];
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
	//$Log['tx'][]=$v.' vs '.$nombreSinExt;
	if($_POST['archivo']!=$nombreSinExt){continue;}
	
	$Log['data']['archivos guardados'][]=$v;	
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
			//$Log['tx'][]= get_class_methods($ShapeFile);
			
			$Log['data']['shapes'][$nombreSinExt]['estado']='viable';
			
		}catch (ShapeFileException $e) {
			// Print detailed error information
			$Log['data']['shapes'][$nombreSinExt]['Error']='Error '.$e->getCode().' ('.$e->getErrorType().'): '.$e->getMessage();	
			$Log['data']['shapes'][$nombreSinExt]['estado']='no viable: error al leer shapefile';
		}
	}	
	
	
	if(!isset($Log['data']['shapes'][$nombreSinExt]['campos'][$_POST['campolink']])){
		$Log['res']='error';
		$Log['tx'][]='no se encontro el campo link';	
		terminar($Log);	
	}
	
	
	$carga=0;
	 while ($Geometry = $ShapeFile->fetchRecord()){

		//$carga++;
		//$_POST['avance']++;		
		//$ShapeFile->setCurrentRecord($_POST['avance']);		
		//$Geometry = $ShapeFile->fetchRecord();
		$srid='5348';
		
		$valor=$Geometry->getData($_POST['campolink']);
		echo $valor;
		
		$wkt =$Geometry->getWKT();
		echo $wkt;
		
		$geomTX= "ST_GeomFromText('".$wkt."',".$srid.")";
		$geomTX= "ST_Transform(".$geomTX.", 3857)";
		
		
		if(isset($Links[$valor])){
			$iddist=$Links[$valor];
		}else{
			$iddist='0';
		}
		
		$query="
			INSERT INTO 
				trecc_zonificador.cot_zonas(
					id_p_distritos, 
					id_p_cot_proyectos, 
					geom
				)VALUES (
					'".$iddist."', 
					'".$_POST['cotID']."', 
					".$geomTX."
				)
				RETURNING id
		";
		$Consulta = pg_query($ConecSIG,utf8_encode($query));
		//$Log['tx'][]=$query;
		if(pg_errormessage($ConecSIG)!=''){
			$Log['res']='error';
			$Log['tx'][]='error al insertar registro en la base de datos';
			$Log['tx'][]=pg_errormessage($ConecSIG);
			$Log['tx'][]=$query;
			terminar($Log);
		}	
		$f=pg_fetch_assoc($Consulta);
		$Log['data']['inserts'][]=$f['id'];

		$tot = $ShapeFile->getTotRecords();
		$Log['data']['avanceP']=round((100/$tot)*$_POST['avance']);

		if($_POST['avance']==$tot){		
			$Log['tx'][]="se alcanzo la cantidad total de ".$ShapeFile->getTotRecords()." registros";
			$Log['data']['avance']='final';
			$Log['res']='exito';
			terminar($Log);
		}
	
	}

	$Log['data']['avance']=$_POST['avance'];	
	$Log['res']='exito';	
	terminar($Log);

}


$Log['res']="exito";
terminar($Log);
