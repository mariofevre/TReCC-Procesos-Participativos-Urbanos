<?php 
/**
 * participacion_ed_distrito.php
 * 
 * actualiza la base actualizando las caracteísticas de un distrito y sus secciones de redaccion
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
ini_set('display_errors',true);

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
    "iddist" => "mayor,0",
    "id_p_cot_grupos"  => "set",
    "cot_grupos_nombre-n"  => "set",
    "cot_grupos_descripcion-n"  => "set",
    "nom_clase"  => "set",
	"des_clase"  => "set",
    "orden"  => "set",
    "co_color"  => "set"	
);

foreach($oblig as $k => $v){
	
	if(!isset($_POST[$k])){
		
		$Log['res']='error';
		$Log['mg'][]='Error falta varaible '.$k;
		terminar($Log);
	}	
}


$Log['data']['iddist']=$_POST['iddist'];


// consulta todos los distritos generados	
$query="
	SELECT 
		*				
	FROM 
		trecc_zonificador.cot_grupos	
	WHERE 
		cot_grupos.zz_auto_cot_proyectos='".$_POST['cotID']."'
	
	ORDER BY 
		nombre asc
";	

$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
}

while($fila=pg_fetch_assoc($Consulta)){		
	$Log['data']['gruposOrden'][]=$fila['id'];
	$Log['data']['grupos'][$fila['id']]=$fila;
}


if($_POST['id_p_cot_grupos']!='n'){
	
	if(!isset($Log['data']['grupos'][$_POST['id_p_cot_grupos']])){
		
		$Log['mg'][]='error al entender grupo designado!!!: '.$_POST['id_p_cot_grupos'];
		
	}else{

		
		
		$gdat=$Log['data']['grupos'][$_POST['id_p_cot_grupos']];
	
	
		if($gdat['decripcion']!=$_POST['cot_grupos_descripcion-n']){
			$query="
				UPDATE 
					trecc_zonificador.cot_grupos	
				SET
					descripcion	= '".$_POST['cot_grupos_descripcion-n']."'
									
				WHERE 
					cot_grupos.zz_auto_cot_proyectos='".$_POST['cotID']."'
				AND
					id='".$_POST['id_p_cot_grupos']."'
			";	

			$Consulta = pg_query($ConecSIG, $query);
			if(pg_errormessage($ConecSIG)!=''){
				$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
				$Log['tx'][]='query: '.$query;
				$Log['res']='err';
				terminar($Log);
			}  	
		}		
	}
}else{
			
	$str=limpiaCodigo($_POST['cot_grupos_nombre-n']);
	$Log['tx'][]='codigo de grupo solicitado: '.$_POST['cot_grupos_nombre-n'].' -validado-> '.$str;
	$preexistente='no';
	
	foreach($Log['data']['grupos'] as $idg => $gdat){
		if($str==$gdat['nombre']){
			$Log['mg'][]='El código enviado ya existe. asimilando a grupo existente: '.$idg.' :'.$gdat['descripcion'];
			$preexistente=$idg;
		}
	}
	
	if($preexistente=='no'){
		$query="
			INSERT INTO 
				trecc_zonificador.cot_grupos(
					nombre, descripcion, zz_auto_cot_proyectos
				)VALUES(
					'".$str."',
					'".$_POST['cot_grupos_descripcion-n']."',
					'".$_POST['cotID']."'
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
		$_POST['id_p_cot_grupos']=$fila['id'];	
		$Log['tx'][]='crado grupo '.$fila['id'];
	}else{
		$_POST['id_p_cot_grupos']=$preexistente;	
	}
}


if(!is_numeric($_POST['orden'])){
	$_POST['orden']=0;
}

$query="	
	UPDATE 
		trecc_zonificador.cot_distritos
	SET 
		id_p_cot_grupos_id='".$_POST['id_p_cot_grupos']."',
		des_clase='".$_POST['des_clase']."',
		nom_clase='".$_POST['nom_clase']."',
		orden='".$_POST['orden']."',
		co_color='".$_POST['co_color']."',
		co_color_final='".$_POST['co_color_final']."',
		zz_preliminar='0'
	WHERE
		id='".$_POST['iddist']."'
		AND
		zz_auto_cot_proyectos='".$_POST['cotID']."'
";
	
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
}  
$fila=pg_fetch_assoc($Consulta);
foreach($filas as $k => $V){
	$Log['data'][$k]=$v;
}

$Log['res']='exito';
terminar($Log);
