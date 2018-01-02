<?php 
/**
* argumentaciones_consulta.php
*
* argumentaciones_consulta.php se incorpora en la carpeta raiz como una función complementaria básica 
* para aquellas aplicaciones que consultan el listado de argumentaciones presentadas 
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
function argumentacionesconsulta($ID,$seleccion){

	global $UsuarioI, $PanelI, $FILTRO, $Freportedesde, $Freportehasta, $FILTROFECHAD, $FILTROFECHAH, $config, $Conec1;
	
	if($seleccion==''||$seleccion=='usuario'){
		$Whereusuario = "id_p_PARTICactores_id='".$UsuarioI."'"; 
	}elseif($seleccion=='todo'){
		if($UsuarioI==9){
			$Whereusuario = "id_p_PARTICactores_id!='0'";
		}else{header('Location: ./login.php');}
	}
	
	if(!isset($Freportedesde)){$Freportedesde = '9999-12-30';}
/*medicion de rendimiento lamp*/
	$starttimef = microtime(true);
	if(!isset($Freportehasta)||$Freportehasta=='0000-00-00'){$Freportehasta = '9999-12-30';}


	/* consulta todas las argumentaciónes para el usuaurio habilitado, si está seteado $ID la busqueda se restringe a la argumentación cuya id coincida ocn el valor de $ID*/	
	if($ID!=''){$whereid = " AND `PARTICargumentaciones`.`id` = '".$ID."'";}else{$whereid='';}
	$query="
		SELECT 
			`PARTICargumentaciones`.`id`,
		    `PARTICargumentaciones`.`resumen`,
		    `PARTICargumentaciones`.`argumentacion`,
		    `PARTICargumentaciones`.`id_p_PARTICactores_id`,
		    `PARTICargumentaciones`.`zz_AUTOUSUARIO`,
		    `PARTICargumentaciones`.`zz_AUTOFECHACREACION`,
		    
			PARTICactores.nombre as usunombre,
			PARTICactores.apellido as usuapellido
	   
		FROM `sigsao`.`PARTICargumentaciones`
		
		LEFT JOIN 
			PARTICactores
			ON PARTICactores.id = PARTICargumentaciones.id_p_PARTICactores_id

		WHERE 
			".$Whereusuario."
			".$whereid."
			AND PARTICargumentaciones.zz_borrada = '0'
			AND PARTICactores.id IS NOT null
			
		ORDER BY 
		 	PARTICargumentaciones.id_p_PARTICactores_id			
	";	
	$ConsultaARG = mysql_query($query,$Conec1);

	echo mysql_error($Conec1);
	while($fila=mysql_fetch_assoc($ConsultaARG)){
		$ARG[$fila['id']]=$fila;
	}
	if($ID!=''&&mysql_num_rows($ConsultaARG)==0){
		$ARG[]['resumen']="error en la selección de la argumentación";
	}
	
	/* consulta todas las imágenes cargadas por este usuario */
	$query="
		SELECT 
			`PARTICargumentacionesIMG`.`id`,
		    `PARTICargumentacionesIMG`.`id_p_PARTICargumentaciones`,
		    `PARTICargumentacionesIMG`.`FI_documento`,
		    `PARTICargumentacionesIMG`.`zz_AUTOUSUARIOCREACION`
		FROM `sigsao`.`PARTICargumentacionesIMG`
		LEFT JOIN 
			`sigsao`.`PARTICargumentaciones`
			ON PARTICargumentaciones.id = PARTICargumentacionesIMG.id_p_PARTICargumentaciones	
		WHERE 
		
			`PARTICargumentacionesIMG`.zz_borrada='0' 
			AND PARTICargumentaciones.".$Whereusuario."
			
		ORDER BY 
		 	PARTICargumentaciones.id_p_PARTICactores_id
		";
	$ConsultaIMG = mysql_query($query,$Conec1);
	/*echo $query;*/
	echo mysql_error($Conec1);
	while($fila=mysql_fetch_assoc($ConsultaIMG)){
		$IMG[$fila['id_p_PARTICargumentaciones']][]=$fila;
	}	
		
	/* consulta todas las localizaciones cargadas por este usuario */	
	$query="
		SELECT `PARTICargumentacionesLOC`.`id`,
		    `PARTICargumentacionesLOC`.`id_p_PARTICargumentaciones`,
		    `PARTICargumentacionesLOC`.`zz_AUTOUSUARIOCREACION`,
		    `PARTICargumentacionesLOC`.`latitud`,
		    `PARTICargumentacionesLOC`.`longitud`
		FROM `sigsao`.`PARTICargumentacionesLOC`
		LEFT JOIN 
			`sigsao`.`PARTICargumentaciones`
			ON PARTICargumentaciones.id = `PARTICargumentacionesLOC`.`id_p_PARTICargumentaciones`
		WHERE
			PARTICargumentaciones.".$Whereusuario."
			AND PARTICargumentacionesLOC.zz_borrada='0'
			AND PARTICargumentaciones.zz_borrada='0'  
		ORDER BY 
		 	PARTICargumentaciones.id_p_PARTICactores_id			
		";
	$ConsultaLOC = mysql_query($query,$Conec1);
	
	/*echo $query;*/
	
	echo mysql_error($Conec1);
	while($fila=mysql_fetch_assoc($ConsultaLOC)){
		$LOC[$fila['id_p_PARTICargumentaciones']][]=$fila;		
	}

	/* incorpora al array de resultados las imágenes y localizaciónes correspondientes a cada argumentación */
	foreach($ARG as $argid => $argum){
		$ARG[$argid]['imagenes']=$IMG[$argid];
		$ARG[$argid]['localizaciones']=$LOC[$argid];		
	}		

	return $ARG;
}	


/**
* genera listado html de argumentaciones
*
 * 
* @param int $ID id de la argumentación. null devuelve la totaclidad de argumentaciónes cargadas por este usuario.
* @return string Retorna el listado en formato html
*/
function argumentacioneslistado($ID,$seleccion){
	global $UsuarioI, $PanelI, $FILTRO, $Freportedesde, $Freportehasta, $FILTROFECHAD, $FILTROFECHAH, $config;
	
	/* consulta array de argumentaicónes */
	$argumentaciones = 	argumentacionesconsulta($ID,$seleccion);

	/* la cadeana $fila contendrá código HTLM */
	$fila="
	<div class='fila'>
		<div class='titulo dato descripcion'>
		Descripción
		</div>
		<div class='titulo dato fecha'>
		Fecha
		</div>
		<div class='titulo dato autor'>
		Autor
		</div>
		<div class='titulo dato imagenes'>
		Imágenes
		</div>
		<div class='titulo dato localizaciones'>
		Localizaciones
		</div>
	</div>
	";
	$filas[]=$fila;	
	
	
	foreach($argumentaciones as $argumentacion){
		if($argumentacion['resumen']!=''){$resumen=$argumentacion['resumen'];}else{$resumen="-vacio-";}		
		$fila="
			<div class='fila'>
				<div class='dato descripcion'>
					<a href='./participacion.php?argumentacion=".$argumentacion['id']."'>".$resumen."</a>
				</div>	
				<div class='dato fecha'>
					".$argumentacion['zz_AUTOFECHACREACION']."
				</div>	
				<div class='dato autor'>
					".$argumentacion['usunombre']." ".$argumentacion['usuapellido']."</a>
				</div>	
				<div class='dato imagenes'>
		";
		$imgasoc=$argumentacion['imagenes'];
		$datoimagen='';
		foreach($imgasoc as $img){
			$datoimagen.="<img class='elemento' src='".$img['FI_documento']."'>";
		}
		if($datoimagen==''){$datoimagen="No se han guardado imágenes para esta argumentación";}
		
		$fila.=$datoimagen;
		$fila.=	"
				</div>
				<div class='dato localizaciones'>
		";
		$localiz=$argumentacion['localizaciones'];
		
		$datoloc='';
				
		foreach($localiz as $loc){
			$datoloc.="<div class='elemento' title='lat:".$loc['latitud']."\nlon:".$loc['longitud']."'>".$loc['latitud']."/".$loc['longitud']."</div>";
		}
		if($datoloc==''){$datoloc="No se han guaradado localizaciones para esta argumentación";}
		$fila.=$datoloc;
		$fila.=	"</div>";
					
		$fila.="						
			</div>
		";		
		$filas[]=$fila;
	}
	$resultado="";
	foreach($filas as $f){
		$resultado.=$f;
	}
	if(count($argumentaciones)==0){
		$resultado='No se han registrado argumentaciones.';
	}
	
	return $resultado;
}




function argumentacionesreporte($ID,$seleccion){
	global $UsuarioI, $PanelI, $FILTRO, $Freportedesde, $Freportehasta, $FILTROFECHAD, $FILTROFECHAH, $config;
	
	/* consulta array de argumentaicónes */
	$argumentaciones = 	argumentacionesconsulta($ID,$seleccion);

	/* la cadeana $fila contendrá código HTLM 
			`PARTICargumentaciones`.`id`,
		    `PARTICargumentaciones`.`resumen`,
		    `PARTICargumentaciones`.`argumentacion`,
		    `PARTICargumentaciones`.`id_p_PARTICactores_id`,
		    `PARTICargumentaciones`.`zz_AUTOUSUARIO`,
		    `PARTICargumentaciones`.`zz_AUTOFECHACREACION`,
		    
			PARTICactores.nombre as usunombre,
			PARTICactores.apellido as usuapellido*/
	
	foreach($argumentaciones as $argumentacion){
		if($argumentacion['resumen']!=''){$resumen=$argumentacion['resumen'];}else{$resumen="-vacio-";}		
		$fila="
			<div class='argumentacion'>
			
				<h2>".$argumentacion['usunombre']." ".$argumentacion['usuapellido']."<span>(idusuario nº:".$argumentacion['id_p_PARTICactores_id'].")</span></h2>
				<h2>".$resumen."</h2>(creada:".$argumentacion['zz_AUTOFECHACREACION'].")
				<p>".$argumentacion['argumentacion']."</p>
		";
		$imgasoc=$argumentacion['imagenes'];
		$datoimagen='';
		foreach($imgasoc as $img){
			$datoimagen.="<img class='ilustracion' src='".$img['FI_documento']."'>";
		}
		if($datoimagen==''){$datoimagen="No se han guardado imágenes para esta argumentación";}
		
		$fila.=$datoimagen;
		
		$localiz=$argumentacion['localizaciones'];
		if(count($localiz)==0){
			$fila.="no se cargaron localizaciónes para esta argumentación";
		}else{
			if($seleccion=='todo'){
				$fila.="<iframe class='mapa' src='./argumentacionmuestralocalizacion.php?argumentacion=".$argumentacion['id']."'></iframe>";
			}else{
				$fila.="<iframe class='mapa' src='./argumentacionlocalizacion.php?argumentacion=".$argumentacion['id']."'></iframe>";	
			}
			
		}
		$fila.="</div>";
		$filas[]=$fila;
	}
	$resultado="";
	foreach($filas as $f){
		$resultado.=$f;
	}
	if(count($argumentaciones)==0){
		$resultado='No se han registrado argumentaciones.';
	}
	
	return $resultado;
}
?>