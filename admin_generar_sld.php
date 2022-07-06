<?php
/**
 * * Genera un archivo sld de parcelas y lo asigna al servidor geoserver.
* 
* @package    	TReCC(tm) Procesos Participativos Urbanos
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


//$_POST['capa_ver']='014_v001';
// Open log file
$logfh = fopen("./geoserver/geoserverPHP.log", 'w') or die("can't open log file");
$Log['tx'][]='curl de consulta iniciado';
$service = "http://190.111.246.33:8080/geoserver/"; 
$request = "rest/workspaces/zonificador_quilmes/layers.json"; 
$url = $service . $request;
$ch = curl_init($url);




// Optional settings for debugging
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //option to return string
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_STDERR, $logfh); // logs curl messages
$passwordStr = $_SESSION["GEOSERVER_USERNAME"].":".$_SESSION["GEOSERVER_PASSWORD"]; // replace with your username:password
curl_setopt($ch, CURLOPT_USERPWD, $passwordStr);
$buffer = curl_exec($ch); // Execute the curl request
curl_close($ch); // free resources if curl handle will not be reused
fclose($logfh);  // close logfile

$capas=json_decode($buffer, true); //el parametro true fuerza la salida como array, no stdClass
$Log['tx'][]='curl consulta ejecutado';
$elmiinarantes='no';
$salteracreacion='no';

$Capa="cot_".$_POST['cotID']."_parcelas";


foreach($capas['layers']['layer'] as $layer){
	if($layer['name']==$Capa){
		$Log['tx'][]=utf8_encode('la capa ya esta publicada en el servidor wms, se saltea la creación de una nueva publicación de capa');
		$Log['data']['creacionWMS']='exito';// fue creada en el pasado pero al parecer no fue registrado
		//$elmiinarantes='si';
		$salteracreacion='si';
	}
}


$MODOEJECUCION='interno';
if(!function_exists('terminar')){
	function terminar($Log){
		$res=json_encode($Log);
		if($res==''){$res=print_r($Log,true);}
		echo $res;
		exit;
	}
	$MODOEJECUCION='externo';	
}

	
	// Abre acrvhio log
	$logfh = fopen("./geoserver/geoserverPHP.log", 'w') or die("can't open log file");

	///CONTROLAR SI EL ESTILO EXISTE
	$service = "http://190.111.246.33:8080/geoserver/"; 
	$request = "rest/workspaces/zonificador_quilmes/styles.json"; 
	$url = $service . $request;
	$ch = curl_init($url);

	// Optional settings for debugging
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //option to return string
	curl_setopt($ch, CURLOPT_VERBOSE, true);
	curl_setopt($ch, CURLOPT_STDERR, $logfh); // logs curl messages
	$passwordStr = $_SESSION["GEOSERVER_USERNAME"].":".$_SESSION["GEOSERVER_PASSWORD"]; // replace with your username:password
	curl_setopt($ch, CURLOPT_USERPWD, $passwordStr);
	$buffer = curl_exec($ch); // Execute the curl request
	curl_close($ch); // free resources if curl handle will not be reused
	fclose($logfh);  // close logfile
	$estilos=json_decode($buffer, true); //el parametro true fuerza la salida como array, no stdClass
	$Log['tx'][]='curl consulta styles ejecutado';
	$salteracreacion='no';

	foreach($estilos['styles']['style'] as $style){
		if($style['name']==$Capa){
			$Log['tx'][]=utf8_encode('el estilo existe en el servidor wms, se saltea la creación de un nuevo estilo con este nombre');
			$Log['data']['creacionestilo']='exito';
			//$elmiinarantes='si';
			$salteracreacion='si';
		}
	}


	if($salteracreacion=='no'){
		$logfh = fopen("./geoserver/geoserverPHP.log", 'w') or die("can't open log file");
		///CREAR ESTILO
		$request = "rest/workspaces"; // to add a new workspace
		$url = $service . $request;
		$url.="/zonificador_quilmes/styles";
		$ch = curl_init($url); 
		$Log['tx'][]='curl de creacion de estilo iniciado';
		// Optional settings for debugging
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //option to return string
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_STDERR, $logfh); // logs curl messages
		//Required POST request settings
		curl_setopt($ch, CURLOPT_POST, True);
		$passwordStr = $_SESSION["GEOSERVER_USERNAME"].":".$_SESSION["GEOSERVER_PASSWORD"]; // replace with your username:password
		curl_setopt($ch, CURLOPT_USERPWD, $passwordStr);
		//crea un workspace llamado test_ws
		curl_setopt($ch, CURLOPT_HTTPHEADER,array("Content-type: application/xml"));

		$xmlStr ='<style>
		  <name>'.$Capa.'</name>
		  <filename>'.$Capa.'.sld</filename>
		</style>
		';

		curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlStr);
		//POST return code
		$successCode = 201;
		$buffer = curl_exec($ch); // Execute the curl request
		$Log['tx'][]='curl de estilo ejecutado';
		// Check for errors and process results
		$info = curl_getinfo($ch);

		$Log['tx'][]=$info;   
		if ($info['http_code'] != $successCode) {

		  $msgStr = "# Unsuccessful cURL request to ";
		  $msgStr .= $url." [". $info['http_code']. "]\n";
		  fwrite($logfh, $msgStr);
			$Log['res']='err';
			$Log['tx'][]='error al publicar en geoserver';
			$Log['tx'][]=$msgStr;
			$Log['tx'][]=$xmlStr;
			terminar($Log);		  
		} else {
		  $msgStr = "# Successful cURL request to ".$url."\n";
		  fwrite($logfh, $msgStr);
		  $Log['data']['creacionWMS']='exito';
		}
		fwrite($logfh, $buffer."\n");

		//$Log['tx'][]=$buffer;

		curl_close($ch); // free resources if curl handle will not be reused
		fclose($logfh);  // close logfile
		$Log['tx'][]=utf8_encode("se creó la vista solicitada para la consulta wms");
		$Log['data']['cot_id']=$_POST['cotID'];
		$Log['res']="exito";

		$logfh = fopen("./geoserver/geoserverPHP.log", 'w') or die("can't open log file");

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
			cot_distritos.co_color_final,		
			cot_distritos.zz_cache_tipo,        
			
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

	$reglaS='
	<?xml version="1.0" encoding="ISO-8859-1"?>
	<StyledLayerDescriptor version="1.0.0"
	  xsi:schemaLocation="http://www.opengis.net/sld http://schemas.opengis.net/sld/1.0.0/StyledLayerDescriptor.xsd"
	  xmlns="http://www.opengis.net/sld" xmlns:ogc="http://www.opengis.net/ogc"
	  xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">

	  <NamedLayer>
		<Name>Densidad de Población</Name>
		<UserStyle>
		<Title></Title>
		<FeatureTypeStyle>           
	';
	
	$regla='
		 <Rule>
          <Name>OTRO</Name>
          <Title>OTRO</Title>
          
                     
          <PolygonSymbolizer>
            <Fill>
              <CssParameter name="fill">#000</CssParameter>
            </Fill>
            <Stroke>
              <CssParameter name="stroke">#a00</CssParameter>
              <CssParameter name="stroke-width">0.5</CssParameter>
              <CssParameter name="stroke-linejoin">bevel</CssParameter>
            </Stroke>
          </PolygonSymbolizer>
		</Rule>
	';
	$reglaS.=$regla;	
	while($fila =pg_fetch_assoc($Consulta)){		
	
		if($fila['co_color_final']!=''){
			
			$hex=$fila['co_color_final'];
		}else{
	
			  $c1_p1 = hexdec(substr($fila['grupo_co_color'], 1, 2));
			  $c1_p2 = hexdec(substr($fila['grupo_co_color'], 3, 2));
			  $c1_p3 = hexdec(substr($fila['grupo_co_color'], 5, 2));

			  $c2_p1 = hexdec(substr($fila['co_color'], 1, 2));
			  $c2_p2 = hexdec(substr($fila['co_color'], 3, 2));
			  $c2_p3 = hexdec(substr($fila['co_color'], 5, 2));

			  $r = (round(($c1_p1 + $c2_p1)/2));
			  $g = (round(($c1_p2 + $c2_p2)/2));
			  $b = (round(($c1_p3 + $c2_p3)/2));
			  
			$hex ="#";
			$hex.= str_pad(dechex($r), 2, "0", STR_PAD_LEFT);
			$hex.= str_pad(dechex($g), 2, "0", STR_PAD_LEFT);
			$hex.= str_pad(dechex($b), 2, "0", STR_PAD_LEFT);
		}
		
		$regla='
			 <Rule>
			  <Name>'.$fila['grupo'].'-'.$fila['nom_clase'].'</Name>
			  <Title>'.$fila['grupo'].'-'.$fila['nom_clase'].'</Title>          
				
			 <ogc:Filter>
				<ogc:PropertyIsEqualTo>
				  <ogc:PropertyName>tipo</ogc:PropertyName>
				  <ogc:Literal>'.$fila['grupo'].'-'.$fila['nom_clase'].'</ogc:Literal>
				</ogc:PropertyIsEqualTo>
			  </ogc:Filter>
					 
			  <PolygonSymbolizer>
				<Fill>
				  <CssParameter name="fill">'. $hex .'</CssParameter>
				</Fill>
				<Stroke>
				  <CssParameter name="stroke">#ffffff</CssParameter>
				  <CssParameter name="stroke-width">1</CssParameter>
				  <CssParameter name="stroke-linejoin">bevel</CssParameter>
				</Stroke>
			  </PolygonSymbolizer>
			</Rule>
		';
		$reglaS.=$regla;
	}


	
	
	$reglaS.='
			</FeatureTypeStyle>
		</UserStyle>
	  </NamedLayer>
	</StyledLayerDescriptor>
	';
	
	
	//echo $reglaS;
	$carpeta='./documentos/p_'.str_pad($_POST['cotID'],6,"0",STR_PAD_LEFT);
	if(!file_exists($carpeta)){
		$Log['tx'][]="creando carpeta $carpeta";
		mkdir($carpeta, 0777, true);
		chmod($carpeta, 0777);	
	}
	$archivo=$Capa.'.sld';
	$myfile = fopen($carpeta.'/'.$archivo, "w");
	chmod($carpeta.'/'.$archivo, 0777);
	fwrite($myfile, $reglaS);
	$Log['tx'][]='ruta a sld '.$carpeta.'/'.$archivo;
	$pass=$_SESSION["GEOSERVER_USERNAME"].":".$_SESSION["GEOSERVER_PASSWORD"];
	$exec= 'curl -v -u '.$pass.' -XPUT ';
	$exec.='-d @'.$carpeta.'/'.$archivo.' ';
	$exec.='-H "content-type: application/vnd.ogc.sld+xml" ';
	$exec.='http://190.111.246.33:8080/geoserver/rest/workspaces/zonificador_quilmes/styles/'.$Capa;
	$Log['tx'][]=$exec;
	exec($exec,$info,$returnvar);
		

	$Log['tx'][]=print_r($info,true);
	$Log['tx'][]=print_r($returnvar,true);
	$Log['tx'][]='se cargo la configuracion sld al estilo en geoserver';

	$Log['tx'][]=print_r($info,true);
	$Log['tx'][]=print_r($returnvar,true);
	$Log['tx'][]='se cargo la configuracion sld al estilo en geoserver';


	
if($MODOEJECUCION=='externo'){
	$Log['res']='exito';
	terminar($Log);
}
?>
