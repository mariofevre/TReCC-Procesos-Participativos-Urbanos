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
    "idsecc"   => "mayor,0",
    "texto"  => "set"
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
$Log['data']['id_p_cot_secciones_id']=$_POST['idsecc'];
$Log['data']['id_p_cot_distritos_id']=$_POST['iddist'];

// consulta todas las las secciones de redaccion y su contenido para cada distrito
$query="	
	SELECT
		count(*) as cant
	FROM
		trecc_zonificador.cot_parrafos
	WHERE
		cot_parrafos.zz_auto_cot_proyectos = '".$_POST['cotID']."'
		AND
		id_p_cot_secciones_id= '".$_POST['idsecc']."'
		AND
		id_p_cot_distritos_id = '".$_POST['iddist']."'
";
	
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
}  
$fila=pg_fetch_assoc($Consulta);
if($fila['cant']==0){

	$query="	
		INSERT INTO
			trecc_zonificador.cot_parrafos
			(
				zz_auto_cot_proyectos,
				id_p_cot_secciones_id,
				id_p_cot_distritos_id
			
			)VALUES(
			
				'".$_POST['cotID']."',
				'".$_POST['idsecc']."',
				'".$_POST['iddist']."'
			
			)
	
	";
		
	$Consulta = pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
		$Log['tx'][]='query: '.$query;
		$Log['res']='err';
		terminar($Log);
	}  

}





$query="	
	UPDATE
		trecc_zonificador.cot_parrafos
		
	SET
		texto='".$_POST['texto']."'
	
	WHERE
		cot_parrafos.zz_auto_cot_proyectos = '".$_POST['cotID']."'
		AND
		id_p_cot_secciones_id= '".$_POST['idsecc']."'
		AND
		id_p_cot_distritos_id = '".$_POST['iddist']."'
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
