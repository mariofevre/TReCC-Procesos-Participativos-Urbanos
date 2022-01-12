<?php 
/**
* redacciones_consulta.php
*
* redacciones_consulta.php se incorpora en la carpeta raiz como una función complementaria básica 
* para aquellas aplicaciones que consultan el listado de redacciones realizdas
* contiene dos funciones: una función que realiza una búsqueda en la base de datos y evalua los resultados devolviendo un array.
* otra función: con el array resultante de la función anterior genera un listado de los resultados en código HTML.
* 
* @package    	TReCC(tm) paneldecontrol.
* @subpackage 	documentos
* @author     	TReCC SA
* @author     	<mario@trecc.com.ar> <trecc@trecc.com.ar>
* @author    	www.trecc.com.ar  
* @copyright	2013 2014 TReCC SA
* @license    	https://www.gnu.org/licenses/agpl-3.0-standalone.html GNU AFFERO GENERAL PUBLIC LICENSE, version 3 (agpl-3.0)
* Este archivo es parte de TReCC(tm) paneldecontrol y de sus proyectos hermanos: baseobra(tm), TReCC(tm) intraTReCC  y TReCC(tm) Procesos Participativos Urbanos.
* Este archivo es software libre: tu puedes redistriburlo 
* y/o modificarlo bajo los términos de la "GNU AFero General Public License version 3" 
* publicada por la Free Software Foundation
* 
* Este archivo es distribuido por si mismo y dentro de sus proyectos 
* con el objetivo de ser útil, eficiente, predecible y transparente
* pero SIN NIGUNA GARANTÍA; sin siquiera la garantía implícita de
* CAPACIDAD DE MERCANTILIZACIÓN o utilidad para un propósito particular.
* Consulte la "GNU General Public License" para más detalles.
* 
* Si usted no cuenta con una copia de dicha licencia puede encontrarla aquí: <http://www.gnu.org/licenses/>.
*/

/**
* genera listado html de argumentaciones
*
* @param int $ID id de la argumentación. null devuelve la totalidad de argumentaciónes cargadas por este usuario.
* @param int $seleccion permite definir modos de selección, algunos modos de selección pueder ser restringidos a ciertos tpos de usuarios.
* @return array Retorna el listado de argumentaciones, sus imágenes y sus localizaciónes
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
	
	if(isset($Log['data']['grupos'][$_POST['id_p_cot_grupos']])){
		
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
