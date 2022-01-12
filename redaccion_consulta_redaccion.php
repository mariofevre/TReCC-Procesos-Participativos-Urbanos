<?php 
/**
* redacciones_consulta.php
*
* redacciones_consulta.php se incorpora en la carpeta raiz como una funci�n complementaria b�sica 
* para aquellas aplicaciones que consultan el listado de redacciones realizdas
* contiene dos funciones: una funci�n que realiza una b�squeda en la base de datos y evalua los resultados devolviendo un array.
* otra funci�n: con el array resultante de la funci�n anterior genera un listado de los resultados en c�digo HTML.
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
* y/o modificarlo bajo los t�rminos de la "GNU AFero General Public License version 3" 
* publicada por la Free Software Foundation
* 
* Este archivo es distribuido por si mismo y dentro de sus proyectos 
* con el objetivo de ser �til, eficiente, predecible y transparente
* pero SIN NIGUNA GARANT�A; sin siquiera la garant�a impl�cita de
* CAPACIDAD DE MERCANTILIZACI�N o utilidad para un prop�sito particular.
* Consulte la "GNU General Public License" para m�s detalles.
* 
* Si usted no cuenta con una copia de dicha licencia puede encontrarla aqu�: <http://www.gnu.org/licenses/>.
*/

/**
* genera listado html de argumentaciones
*
* @param int $ID id de la argumentaci�n. null devuelve la totalidad de argumentaci�nes cargadas por este usuario.
* @param int $seleccion permite definir modos de selecci�n, algunos modos de selecci�n pueder ser restringidos a ciertos tpos de usuarios.
* @return array Retorna el listado de argumentaciones, sus im�genes y sus localizaci�nes
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
		cot_parrafos.id as parrid,    
		cot_parrafos.incluirpost,
		cot_parrafos.id_p_cot_secciones_id,
		cot_parrafos.id_p_cot_distritos_id,
		cot_parrafos.texto
		
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
foreach($fila as $k => $v){
	$Log['data'][$k]=$v;
}

$Log['res']='exito';
terminar($Log);
