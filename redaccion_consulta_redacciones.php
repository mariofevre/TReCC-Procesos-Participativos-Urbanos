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
    "cotID" => "mayor,0"
);
foreach($oblig as $k => $v){
	if(!isset($_POST[$k])){
		$Log['res']='error';
		$Log['mg']='Error falta varaible '.$k;
		terminar($Log);
	}	
}




// consulta todos los grupos disponibles
$query="
	SELECT 
		*				
	FROM 
		trecc_zonificador.cot_grupos	
	WHERE 
		cot_grupos.zz_auto_cot_proyectos='".$_POST['cotID']."'
	AND
		zz_borrada='0'
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


while($fila =pg_fetch_assoc($Consulta)){		
	$Log['data']['gruposOrden'][]=$fila['id'];
	$Log['data']['grupos'][$fila['id']]=$fila;
	$Log['data']['grupos'][$fila['id']]['cant_dist']=0;
}






	// consulta todos los distritos generados	
	$query="
		SELECT 
			cot_distritos.id,
			cot_distritos.orden as disorden,			
			cot_distritos.id_p_cot_grupos_id,
			cot_distritos.nom_clase,
			cot_distritos.des_clase,		    		    
			cot_distritos.id_p_cot_jurisdicciones_id,	    
			cot_distritos.co_color,		    
			
			cot_grupos.id as idgrupo,
			cot_grupos.nombre as grupo,
			cot_grupos.descripcion as descripciongrupo,
			cot_grupos.co_color as grupo_co_color,
			
			cot_jurisdicciones.id as jurid,
			cot_jurisdicciones.nombre as jurisdiccion,
			cot_jurisdicciones.orden as jurisdiccorden,		  
			cot_jurisdicciones.titulo as jurisdictitulo,			      
			cot_jurisdicciones.descripcion as descripcionjurisdiccion	    
			
			
			
		FROM trecc_zonificador.cot_distritos
		
		LEFT JOIN 
			trecc_zonificador.cot_grupos
			ON cot_grupos.id= cot_distritos.id_p_cot_grupos_id
			AND cot_grupos.zz_auto_cot_proyectos = '".$_POST['cotID']."'
						
		LEFT JOIN 
			trecc_zonificador.cot_jurisdicciones
			ON cot_jurisdicciones.id = cot_distritos.id_p_cot_jurisdicciones_id
			AND cot_jurisdicciones.zz_auto_cot_proyectos = '".$_POST['cotID']."'	
		WHERE 
			cot_distritos.zz_auto_cot_proyectos='".$_POST['cotID']."'
			AND
			cot_distritos.zz_preliminar='0' 
			AND
			cot_distritos.zz_borrada='0' 
		ORDER BY 
			jurisdiccorden asc, jurisdiccion asc, grupo, disorden asc, cot_distritos.id ASC
	";	

	$Consulta = pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
		$Log['tx'][]='query: '.$query;
		$Log['res']='err';
		terminar($Log);
	}  


	$Contenido = '';
	$idejec = '';


	$indices[1]=0;
	$indices[2]=0;
	$indices[3]=0;
	$indices[4]=0;
	$jurviejo='';

	while($fila =pg_fetch_assoc($Consulta)){		
		$Log['data']['distritosOrden'][]=$fila['id'];
		$Log['data']['distritos'][$fila['id']]=$fila;
		$Log['data']['distritos'][$fila['id']]['nomenclatura']=$fila['grupo'].$fila['NOMclase']." ".$fila['jurisdiccion'];
		$Log['data']['distritos'][$fila['id']]['secciones']=array();
		$Log['data']['distritos'][$fila['id']]['zonas']=array();		
		$Log['data']['distritos'][$fila['id']]['sup_ha']=0;
		
		$Log['data']['grupos'][$fila['id_p_cot_grupos_id']]['cant_dist']++;
		
		
	
		if($fila['id_p_cot_jurisdicciones_id']!=$jurviejo){
			$indices[3]++;
			$indices[4]=1;
			$jurviejo=$fila['id_p_cot_jurisdicciones_id'];
		}else{
			$indices[4]++;
		}
		
		$Log['data']['distritos'][$fila['id']]['autonumeracion']=$indices[1].".".$indices[2].".".$indices[3].".".$indices[4];
	}


	// consulta todas las las secciones de redaccion y su contenido para cada distrito
	$query="		
		SELECT
			cot_secciones.id,
			cot_secciones.nombre,
			cot_secciones.pordefecto,	    
			cot_secciones.postfijo,
			cot_secciones.campo,
			cot_secciones.zz_verentabla
			
		FROM trecc_zonificador.cot_secciones
		
		WHERE
			cot_secciones.zz_auto_cot_proyectos = '".$_POST['cotID']."'
			
		order by orden
	";
		
	$Consulta = pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
		$Log['tx'][]='query: '.$query;
		$Log['res']='err';
		terminar($Log);
	}  

	while($fila =pg_fetch_assoc($Consulta)){
		$Log['data']['seccionesOrden'][]=$fila['id'];
		$Log['data']['secciones'][$fila['id']]=$fila;
	}


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
	";
		
	$Consulta = pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
		$Log['tx'][]='query: '.$query;
		$Log['res']='err';
		terminar($Log);
	}  

	while($fila =pg_fetch_assoc($Consulta)){
		$Log['data']['distritos'][$fila['id_p_cot_distritos_id']]['secciones'][$fila['id_p_cot_secciones_id']]=$fila;
	}





$query="
SELECT id, id_p_distritos, ST_AsText(geom) as geotx, etiq_partic, ST_Area(geom)/10000 as sup_ha
	FROM trecc_zonificador.cot_zonas
	WHERE
	id_p_cot_proyectos = '".$_POST['cotID']."'
";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
}  
$Log['data']['distritos'][0]=array();
$Log['data']['distritos'][0]['sup_ha']=0;
$Log['data']['distritosOrden'][]=0;
$Log['data']['distritos'][0]['zonas']=array();
while($fila =pg_fetch_assoc($Consulta)){
	if(!isset($Log['data']['distritos'][$fila['id_p_distritos']])){continue;}
	$Log['data']['distritos'][$fila['id_p_distritos']]['zonas'][$fila['id']]=$fila;
	
	$Log['data']['distritos'][$fila['id_p_distritos']]['sup_ha']+=$fila['sup_ha'];
}


	
	

$Log['res']='exito';
terminar($Log);
	
	
/*
// genera listado para la edicion de zonificaciones

function redaccioneslistado($id){
	global $UsuarioI, $PanelI, $FILTRO, $config;
	
	// consulta array de argumentaicónes 
	$redacciones = redaccionesconsulta($id);
	
	//echo "<pre>";print_r($redacciones);echo"</pre>";

	// la cadeana $fila contendrá código HTLM 
	$fila="
	<div class='fila'>
		<div class='titulo dato nombre'>
		Nombre
		</div>
		<div class='titulo dato variable'>
		Descripción
		</div>
		<div class='titulo dato descripcion'>
		-
		</div>
		<div class='titulo dato imagenes'>
		-
		</div>
		<div class='titulo dato localizaciones'>
		-
		</div>
	</div>
	";
	$filas[]=$fila;	
	
	
	foreach($redacciones as $red){
		//if($argumentacion['resumen']!=''){$resumen=$argumentacion['resumen'];}else{$resumen="-vacio-";}
				
		$fila="
			<div class='fila'>
				<div class='dato nombre'>
					<a href='./agrega_f.php?salida=redaccion&salidaid=".$id."&accion=cambia&tabla=cot_distritos&id=".$red['id']."'>".$red['nomenclatura']."</a>
				</div>			
				<div class='dato descripcion'>
					".$red['descripciongrupo']." ".$red['DESclase']." / <a href='./agrega_f.php?salida=redaccion&salidaid=".$id."&accion=cambia&tabla=cot_jurisdicciones&id=".$red['jurid']."'>".$red['descripcionjurisdiccion']."</a> - ".$red['particular']."
				</div>
			</div>	
		";		
		$filas[]=$fila;
		
		
			foreach($red['redaccion'] as $secid => $seccion){
				$contenido='';
				$postfijo='';
				//if($argumentacion['resumen']!=''){$resumen=$argumentacion['resumen'];}else{$resumen="-vacio-";}
				
				if($seccion['parrid']!=''){$acc='cambia';$clasepost='';}else{$acc='agrega';$clasepost='preliminar';}
				
				if($seccion['incluirpost']!='0'){$postfijo=" <span class='$clasepost'>".$seccion['secpostfijo']."</span>";}else{$postfijo='';}

				if($seccion['parrid']!=''){$contenido = $seccion['texto'];}else{$contenido = "<span class='defecto'>".$seccion['pordefecto']."</span>";}				
				$fila="
					<div name='D".$red['id']."' class='fila'>
						<div class='dato nombre'>
						</div>	
						<div class='dato variable'>
						
							<a href='./agrega_f.php?campofijo=id_p_cot_secciones_id&campofijo_c=".$secid."&campofijob=id_p_cot_distritos_id&campofijob_c=".$red['id']."&salida=redaccion&salidaid=".$id."&accion=$acc&tabla=cot_parrafos&id=".$seccion['parrid']."'>".$seccion['secnombre']."</a>
						</div>
						<div class='dato descripcion'>
							".$contenido.$postfijo."
						</div>	
					</div>	
				";		
				$filas[]=$fila;
			}
			
			
			//if($argumentacion['resumen']!=''){$resumen=$argumentacion['resumen'];}else{$resumen="-vacio-";}		
				$fila="
					<div class='fila'>
						<div class='dato nombre'>
						</div>	
						<div class='dato variable'>
						
							<a href='./agrega_f.php?salida=redaccion&accion=cambia&tabla=cot_distritos&id=".$red['id']."&pathFI_localizacion=codigo/img'>cargar imagen</a>
						</div>
						<div class='dato variable'>
							<img class='mapaloc' src='".str_replace('documentos/p_1/codigo','publicaciones/COTinteractivo/documentos/p_1/codigo',$red['FI_localizacion'])."'>
						</div>
					</div>	
				";		
				$filas[]=$fila;	
				
				
				//if($argumentacion['resumen']!=''){$resumen=$argumentacion['resumen'];}else{$resumen="-vacio-";}		
				$fila="
					<div class='fila'>
						<div class='dato nombre'>
						</div>	
						<div class='dato variable'>
							<a href='./agrega_f.php?salida=redaccion&salidaid=".$red['id']."&accion=agrega&tabla=cot_parrafos&campofijo=id_p_cot_distritos_id&campofijo_c=".$red['id']."'>agregar dato</a>
						</div>
					</div>	
				";		
				$filas[]=$fila;			
			
		
	}
	

	foreach($filas as $f){
		$resultado.=$f;
	}

	
	return $resultado;
}



// genera texto html de zonificaciones
function redaccionestexto($id){
	global $UsuarioI, $PanelI, $FILTRO, $config;
	
	// consulta array de argumentaicónes 
	$redacciones = redaccionesconsulta($id);
	
	//echo "<pre>";print_r($redacciones);echo"</pre>";
	
	$jurisviejo='';
	
	foreach($redacciones as $red){
		if($red['jurisdiccion']!=$jurisviejo){
			if($red['jurisdictitulo']!=''){
				$tit=$red['jurisdictitulo'];
			}else{
				$tit=$red['descripcionjurisdiccion'];				
			}
			
			
			$filas[] = "<h1>".$tit."</h1>";
			$jurisviejo=$red['jurisdiccion'];
		}
		//if($argumentacion['resumen']!=''){$resumen=$argumentacion['resumen'];}else{$resumen="-vacio-";}
		if($red['particular']!=''){$part=' - '.$red['particular'];}else{$part='';}		
		$fila="
			<a id='D".$red['id']."'></a>
			<h2>".$red['nomenclatura']." - ".$red['descripciongrupo']." ".$red['DESclase']." de ".$red['descripcionjurisdiccion'].$part."</h2>
		";		
		$filas[]=$fila;
		
		
			foreach($red['redaccion'] as $secid => $seccion){
				//echo"<pre>";print_r($seccion);echo "</pre>";
				//if($argumentacion['resumen']!=''){$resumen=$argumentacion['resumen'];}else{$resumen="-vacio-";}
				
				if($seccion['parrid']!=''){$acc='cambia';$clasepost='';}else{$acc='agrega';$clasepost='preliminar';}
				
				if($seccion['incluirpost']!='0'){$postfijo=" <span class='$clasepost'>".$seccion['secpostfijo']."</span>";}else{$postfijo='';}

				if($seccion['parrid']!=''){$contenido = $seccion['texto'];}elseif($seccion['pordefecto']!=''){$contenido = "<span class='defecto'>".$seccion['pordefecto']."</span>";}else{$contenido='';}				
				
				if($contenido!=''&&$contenido!=' '){
					if(substr($seccion['secnombre'],0,1)!='['){$secnombrepublico="<h3>".$seccion['secnombre']."</h3>";}else{$secnombrepublico='';}	
					$fila="
						$secnombrepublico
						<p>
						$contenido $postfijo
						</p>
					";	
				$filas[]=$fila;	
				}
				
			}
		
	}
	

	foreach($filas as $f){
		$resultado.=$f;
	}

	
	return $resultado;
}


// genera fichas html de zonificaciones
function redaccionesfichas($id){
	global $UsuarioI, $PanelI, $FILTRO, $config;
	
	// consulta array de argumentaicónes 
	$redacciones = redaccionesconsulta($id);
	
	//echo "<pre>";print_r($redacciones);echo"</pre>";
	
	foreach($redacciones as $red){
		
		unset($contenidos);
		//repasa todos los parrafos en este distrito
		foreach($red['redaccion'] as $secid => $seccion){
			//echo"<pre>";print_r($seccion);echo "</pre>";
			//if($argumentacion['resumen']!=''){$resumen=$argumentacion['resumen'];}else{$resumen="-vacio-";}
			
			if($seccion['parrid']!=''){$acc='cambia';$clasepost='';}else{$acc='agrega';$clasepost='preliminar';}
			
			if($seccion['incluirpost']!='0'){$postfijo=" <span class='$clasepost'>".$seccion['secpostfijo']."</span>";}else{$postfijo='';}

			if($seccion['parrid']!=''){$contenido = $seccion['texto'];}elseif($seccion['pordefecto']!=''){$contenido = "<span class='defecto'>".$seccion['pordefecto']."</span>";}else{$contenido='';}		
			if($contenido!=''&&$contenido!=' '){				
				$contenidos[$seccion['secnombre']]=nl2br($contenido)." ".$postfijo;
			}
		}
		
		if($red['particular']!=''){$part=' - '.$red['particular'];}else{$part='';}		
		
		
		$ficha='';
			$ficha.="<a id='D".$red['id']."'></a>";
		$ficha.="<div class='ficha'>";		
		
			$ficha.="<div class='encabezado'>";
				$ficha.="<div class='c1 sello'>";
					$ficha.="<p>DISTRITO</p>";
					$ficha.="<p class='nomenclatura'>".$red['nomenclatura']."</p>";
					$ficha.="<p class='autonumeracion'>".$red['autonumeracion']."</p>";
				$ficha.="</div>";
				$ficha.="<div class='c2 descripcion'>";
					$ficha.="<div class='nombre'>";
						$ficha.=$red['descripciongrupo']." ".$red['DESclase']." de ".$red['descripcionjurisdiccion'].$part;
					$ficha.="</div>";		
					$ficha.="<div class='caracter'>";
						$ficha.=$contenidos['Carácter'];
					$ficha.="</div>";						
				$ficha.="</div>";
			$ficha.="</div>";	
							
			$ficha.="<div class='medio'>";
				$ficha.="<div class='c1 indicadores'>";
					
					if($contenidos['Ocupación del Suelo']!=''||$contenidos['F.O.T.']!=''||$contenidos['F.O.S.']!=''){
					$ficha.="<div class='ocupacion'>";
						
						$ficha.="<p class='titulo'>Ocupación del Suelo</p>";
						
						if($contenidos['Ocupación del Suelo']!=''){
						$ficha.="<p class='cont'>".$contenidos['Ocupación del Suelo']."</p>";
						}
						
						if($contenidos['F.O.T.']!=''||$contenidos['F.O.S.']!=''){
						$ficha.="<div class='fosfot'>";
							
							if($contenidos['F.O.T.']!=''){
							$ficha.="<div class='fot'>";	
								$ficha.="<p class='titulo'>F.O.T.</p>";
								$ficha.="<p class='cont'>".$contenidos['F.O.T.']."</p>";							
							$ficha.="</div>";
							}
							if($contenidos['F.O.S.']!=''){				
							$ficha.="<div class='fos'>";	
								$ficha.="<p class='titulo'>F.O.S.</p>";
								$ficha.="<p class='cont'>".$contenidos['F.O.S.']."</p>";							
							$ficha.="</div>";	
							}													
						$ficha.="</div>";
						}
						
						$ficha.="<p>".$contenidos['[ocupacion del suelo fin]']."</p>";												
					$ficha.="</div>";
					}
								
				
					if($contenidos['Retiros Mínimos Obligatorios']!=''){
					$ficha.="<div>";
						$ficha.="<p class='titulo'>Retiros Mínimos Obligatorios</p>";
						$ficha.="<p class='cont'>".$contenidos['Retiros Mínimos Obligatorios']."</p>";
					$ficha.="</div>";
					}
					
					if($contenidos['Altura Máxima de Edificación']!=''){
					$ficha.="<div class='altura'>";					
						$ficha.="<p class='titulo'>Altura Máxima de Edificación</p>";
						$ficha.="<p class='cont'>".$contenidos['Altura Máxima de Edificación']."</p>";		
						$ficha.="<p class='observ'>".$contenidos['[altura maxima obs]']."</p>";										
					$ficha.="</div>";
					}
					
					if($contenidos['Subdivisión del Suelo']!=''||$contenidos['Lado Mínimo']!=''||$contenidos['Superficie Mínima']!=''||$contenidos['[subdivisión obs]']!=''){
						$ficha.="<div class='subdivision'>";
							$ficha.="<p class='titulo'>Subdivisión del Suelo</p>";
							
							$ficha.="<p class='cont'>".$contenidos['Subdivisión del Suelo']."</p>";
							
							$ficha.="<p class='cont'>".$contenidos['Lado Mínimo']."</p>";		
							
							if($contenidos['Lado Mínimo de Parcela']!=''||$contenidos['Superficie Mínima de Parcela']!=''){
							$ficha.="<div class='ladosup'>";
								if($contenidos['Lado Mínimo de Parcela']!=''){
									$ficha.="<div class='lado'>";	
										$ficha.="<p class='titulo'>Lado Mínimo</p>";
										$ficha.="<span class='aclaracion'>de Parcela</span>";
										$ficha.="<p class='cont'>".$contenidos['Lado Mínimo de Parcela']."</p>";							
									$ficha.="</div>";	
								}
								if($contenidos['Superficie Mínima de Parcela']!=''){			
									$ficha.="<div class='sup'>";	
										$ficha.="<p class='titulo'>Superficie Mínima </p>";
										$ficha.="<span class='aclaracion'>de Parcela</span>";
										$ficha.="<p class='cont'>".$contenidos['Superficie Mínima de Parcela']."</p>";																
									$ficha.="</div>";	
								}					
							$ficha.="</div>";	
							}
							
						if($contenidos['[subdivisión obs]']!=''){
						//$ficha.="<div class='des'>";	
							$ficha.="<p class='observ'>".$contenidos['[subdivisión obs]']."</p>";
						//$ficha.="</div>";
						}											
						$ficha.="</div>";	
						

					}
				$ficha.="</div>";
				
				$ficha.="<div class='c2 descripciones'>";
				
					if($contenidos['Uso Predominante']!=''||$contenidos['Uso Complementario']!=''||$contenidos['Uso No Conforme']!=''){
					$ficha.="<div class='usos'>";					
						if($contenidos['Uso Predominante']!=''){
							$ficha.="<p class='titulo'>Uso Predominante</p>";
							$ficha.="<p class='cont'>".$contenidos['Uso Predominante']."</p>";
						}
						if($contenidos['Uso Complementario']!=''){	
							$ficha.="<p class='titulo'>Uso Complementario</p>";
							$ficha.="<p class='cont'>".$contenidos['Uso Complementario']."</p>";
						} 
						if($contenidos['Uso No Conforme']!=''){
							$ficha.="<p class='titulo'>Uso No Confome</p>";
							$ficha.="<p class='cont'>".$contenidos['Uso No Conforme']."</p>";	
						}	
					$ficha.="</div>";	
					}
					
					if($contenidos['Espacio Público y Paisajes a Promover']!=''){
					$ficha.="<div class='des espub'>";						
						$ficha.="<p class='titulo'>Espacio Público y Paisajes a Promover</p>";		
						$ficha.="<p class='cont'>".$contenidos['Espacio Público y Paisajes a Promover']."</p>";	
					$ficha.="</div>";	
					}
					
					if($contenidos['Sistema de tratamiento de residuos cloacales y drenajes pluviales en zona de dotación incompleta de servicios por red']!=''){
					$ficha.="<div class='des'>";									
						$ficha.="<p class='titulo'>Sistema de tratamiento de residuos cloacales y drenajes pluviales en zona de dotación incompleta de servicios por red</p>";		
						$ficha.="<p class='cont'>".$contenidos['Sistema de tratamiento de residuos cloacales y drenajes pluviales en zona de dotación incompleta de servicios por red']."</p>";								
					$ficha.="</div>";
					}
					
					
					if($contenidos['Servicios Públicos Obligatorios']!=''){	
					$ficha.="<div class='des'>";									
						$ficha.="<p class='titulo'>Servicios Públicos Obligatorios</p>";		
						$ficha.="<p class='cont'>".$contenidos['Servicios Públicos Obligatorios']."</p>";								
					$ficha.="</div>";	
					}
					if($contenidos['Estacionamiento, Carga y Descarga']){	
					$ficha.="<div class='des'>";									
						$ficha.="<p class='titulo'>Estacionamiento, Carga y Descarga</p>";		
						$ficha.="<p class='cont'>".$contenidos['Estacionamiento, Carga y Descarga']."</p>";								
					$ficha.="</div>";		
					}				
				$ficha.="</div>";	
							
			$ficha.="</div>";
			
			$ficha.="<div class='pie'>";
				if($contenidos['Disposiciones Particulares']!=''){	
				$ficha.="<div class='des'>";
					$ficha.="<p class='titulo'>Disposiciones Particulares</p>";
					$ficha.="<p class='cont'>".$contenidos['Disposiciones Particulares']."</p>";	
				$ficha.="</div>";	
				}	
				if($contenidos['Observaciones']!=''){						
				$ficha.="<div class='des'>";		
					$ficha.="<p class='titulo'>Observaciones</p>";
					$ficha.="<p class='cont'>".$contenidos['Observaciones']."</p>";			
				$ficha.="</div>";
				}
				$ficha.="<div class='des'>";
					$ficha.="<p class='titulo'>Mapa Orientativo de Localización</p>";					
				$ficha.="<div class='mapa'>";	
						$ficha.="<img src='".str_replace('documentos/p_1/codigo','publicaciones/COTinteractivo/documentos/p_1/codigo',$red['FI_localizacion'])."'>";
				$ficha.="</div>";										
				$ficha.="</div>";								
			$ficha.="</div>";
		//if($argumentacion['resumen']!=''){$resumen=$argumentacion['resumen'];}else{$resumen="-vacio-";}
		$ficha.="</div>";
		$fichas[]=$ficha;
	}
	foreach($fichas as $f){
		$resultado.=$f;
	}	
	return $resultado;
}

// genera fichas html de zonificaciones
function redaccionestabla($id){
	global $UsuarioI, $PanelI, $FILTRO, $config;
	
	if($_SESSION['modo']=='html'){
		$linkT="COT_texto.html";
		$linkF="COT_fichas.html";
	}else{
		$linkT="redaccion.php?modo=texto";
		$linkF="redaccion.php?modo=fichas";
	}
	// consulta array de argumentaicónes
	$redacciones = redaccionesconsulta($id);
	
	//echo "<pre>";print_r($redacciones);echo"</pre>";

	foreach($redacciones as $redid => $red){
		$fila="";
		//if($argumentacion['resumen']!=''){$resumen=$argumentacion['resumen'];}else{$resumen="-vacio-";}
		if($red['particular']!=''){$part=' - '.$red['particular'];}else{$part='';}		
		$fila.="<tr name='D".$red['id']."'>";
		
		
		if($_SESSION['modo']!='html'){
			$fila.="<td><a href='./redaccion.php?modo=edicion&id=".$redid."'>".$red['nomenclatura']." - ".$red['descripciongrupo']." ".$red['DESclase']." de ".$red['descripcionjurisdiccion'].$part."</a>".$red['grupo']." -> ".$red['disorden']."</td>";
		}else{
			$fila.="<td>".$red['nomenclatura']." - ".$red['descripciongrupo']." ".$red['DESclase']." de ".$red['descripcionjurisdiccion'].$part."</td>";
		}
				
		
		$c=0;			
		foreach($red['redaccion'] as $secid => $seccion){
			$c++;
			if($seccion['incluirpost']!='0'){$postfijo="".$seccion['secpostfijo']."";}else{$postfijo='';}
			if($seccion['parrid']!=''){$contenido = $seccion['texto'];}elseif($seccion['pordefecto']!=''){$contenido = "".$seccion['pordefecto']."";}else{$contenido='';}	
			if(($c==1||$c==1||$c==5)){
				$cont=$contenido ." " .$postfijo;
				
				if(strlen($cont)>300){
					$cont=substr($cont,0,300)."...";
				}
				$fila.="<td>".$cont."</td>";
			}	
		}
		
		$fila.="<td><a href='./$linkT#D$redid'>ver texto</a><a href='./$linkF#D$redid'>ver ficha</a></td>";
	
		$fila.="<td><img class='mapaloc' src='".str_replace('documentos/p_1/codigo','publicaciones/COTinteractivo/documentos/p_1/codigo',$red['FI_localizacion'])."'></td>";
		
		$fila.="</tr>";
		$filas[]=$fila;
	}
		
	$resultado="<table>";
	foreach($filas as $f){
		$resultado.=$f;
	}
	$resultado.="</table>";
	
	return $resultado;
}

// genera listado para la edicion de zonificaciones
function redaccionestablacsv(){
	global $UsuarioI, $PanelI, $FILTRO, $config;
	
	// consulta array de argumentaicónes 
	$redacciones = redaccionesconsulta($id);
	
	foreach($redacciones as $red){
		//if($argumentacion['resumen']!=''){$resumen=$argumentacion['resumen'];}else{$resumen="-vacio-";}				
		foreach($red['redaccion'] as $secid => $seccion){
			if($seccion['seccampo']!=''){
				$camp=$seccion['seccampo'];
			}else{
				$camp=$seccion['secnombre'];
			}
			
			$Columnas[$secid]['nombre']=$camp.",C,200";
			$Columnas[$secid]['data']=$seccion;
		}
	}

	foreach($filas as $f){
		$resultado.=$f;
	}
	
	
	$csv.="nomenclatura,C,200;";
	foreach($Columnas as $colid => $coln){
		$csv.=$coln['nombre'].";";
	}
	$csv.="\n";	
	
	foreach($redacciones as $red){
		//if($argumentacion['resumen']!=''){$resumen=$argumentacion['resumen'];}else{$resumen="-vacio-";}
		//print_r($red['redaccion']);
		
		$csv.=$red['nomenclatura'].";";
		
		foreach($Columnas as $colid => $col){				

			if($red['redaccion'][$colid]['incluirpost']!='0'&&$col['data']['secpostfijo']!=''&&$red['redaccion'][$colid]['texto']!=''){$postfijo=" ".$col['data']['secpostfijo'];}else{$postfijo='';}
			
			if($red['redaccion'][$colid]['parrid']!=''){
				$contenido = $red['redaccion'][$colid]['texto'];
			}elseif($col['data']['pordefecto']!=''){
				$contenido = $col['data']['pordefecto'];
			}else{
				$contenido='';
			}		
			
			$contenidos=$contenido.$postfijo;			
			
			$ntx=	str_replace(";","",$contenidos)	;
			$ntx=	str_replace("\n","",$ntx)	;
			$ntx=	str_replace("\r","",$ntx);	
			$ntx=str_replace("<br>","",$ntx);
			$csv.=substr($ntx,0,200);
			$csv.=";";
		}
		$csv.="\n";
	}
	
	return $csv;
}

?>
