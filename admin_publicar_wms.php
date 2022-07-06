<?php
/**
* admin_publicar_wms
* 
* genera una capa de datos en el servidor geoserver asociada a una nueva vista en la base de datos y ejecuta el generador de etilo correspondiente.
* 
* @package    	TReCC(tm) Procesos Participativos Urbanos
* @author     	TReCC SA
* @author     	<mario@trecc.com.ar> <trecc@trecc.com.ar>
* @author    	www.trecc.com.ar  
* @copyright	2013 2022 TReCC SA
* @copyright	esta aplicación se desarrolló sobre una publicación GNU 2017 2022 Universidad de Buenos Aires
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


//consultar geometría
$query="
	SELECT 
		ST_Extent(geom) as bextent,
		ST_Extent(ST_Transform(geom,4326)) as bextentg
		
		FROM
			trecc_zonificador.".$Capa."
";

$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}

$fila=pg_fetch_assoc($Consulta);


if($fila['bextent']==''){
	$Log['tx'][]='sin geometria accesible para epublica en wms';
	$Log['res']="exito";
	//terminar($Log);
}else{



	$Log['tx'][]='bextent: '.$fila['bextent'];

	$coords=substr($fila['bextent'],4,-1);
	$Log['tx'][]='coord: '.$coords;
	$co=explode(',',$coords);
	$c=explode(' ',$co[0]);
	$xmin=$c[0];
	$ymin=$c[1];
	$c=explode(' ',$co[1]);
	$xmax=$c[0];
	$ymax=$c[1];



	$Log['tx'][]='bextentg: '.$fila['bextentg'];

	$coords=substr($fila['bextentg'],4,-1);
	$Log['tx'][]='coord: '.$coords;
	$co=explode(',',$coords);
	$c=explode(' ',$co[0]);
	$gxmin=$c[0];
	$gymin=$c[1];
	$c=explode(' ',$co[1]);
	$gxmax=$c[0];
	$gymax=$c[1];


	if($salteracreacion=="no"){
		/////////////////CREAR CAPA

		// Abre acrvhio log
		$logfh = fopen("./geoserver/geoserverPHP.log", 'w') or die("can't open log file");

		// Initiate cURL session
		$service = "http://190.111.246.33:8080/geoserver/"; // replace with your URL
		$request = "rest/workspaces"; // to add a new workspace
		$url = $service . $request;
		$url.="/zonificador_quilmes/datastores/zonificador_quilmes/featuretypes";
		$ch = curl_init($url); 
		$Log['tx'][]='curl de creacion iniciado';

		// Optional settings for debugging
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //option to return string
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_STDERR, $logfh); // logs curl messages

		//Required POST request settings
		curl_setopt($ch, CURLOPT_POST, True);
		$passwordStr = $_SESSION["GEOSERVER_USERNAME"].":".$_SESSION["GEOSERVER_PASSWORD"];
		curl_setopt($ch, CURLOPT_USERPWD, $passwordStr);
		curl_setopt($ch, CURLOPT_HTTPHEADER,
				array("Content-type: application/xml"));
				$xmlStr = '
		<featureType>
		  <name>'.$Capa.'</name>
		  <nativeName>'.$Capa.'</nativeName>
		  <namespace>
			<name>zonificador_quilmes</name>						 
			<atom:link xmlns:atom="http://www.w3.org/2005/Atom" rel="alternate" href="'.$service.'rest/namespaces/zonificador_quilmes.xml" type="application/xml"/>
		  </namespace>
		  <title>'.$Capa.'</title>
		  <keywords>
			<string>features</string>
			<string>'.$Capa.'</string>
		  </keywords>
		  <srs>EPSG:3857</srs>
		  <nativeBoundingBox>
			<minx>'.$xmin.'</minx>
			<maxx>'.$xmax.'</maxx>
			<miny>'.$ymin.'</miny>
			<maxy>'.$ymax.'</maxy>
		  </nativeBoundingBox>
		  <latLonBoundingBox>
			<minx>'.$gxmin.'</minx>
			<maxx>'.$gxmax.'</maxx>
			<miny>'.$gymin.'</miny>
			<maxy>'.$gymax.'</maxy>
			
			
			<crs>GEOGCS[&quot;WGS84(DD)&quot;, 
		  DATUM[&quot;WGS84&quot;, 
			SPHEROID[&quot;WGS84&quot;, 6378137.0, 298.257223563]], 
		  PRIMEM[&quot;Greenwich&quot;, 0.0], 
		  UNIT[&quot;degree&quot;, 0.017453292519943295], 
		  AXIS[&quot;Geodetic longitude&quot;, EAST], 
		  AXIS[&quot;Geodetic latitude&quot;, NORTH]]</crs>'
		  /*<crs>EPSG:4326</crs>'*/
		  .'</latLonBoundingBox>
		  <projectionPolicy>FORCE_DECLARED</projectionPolicy>
		  <enabled>true</enabled>
		  <metadata>
			<entry key="elevation">
			  <dimensionInfo>
				<enabled>false</enabled>
			  </dimensionInfo>
			</entry>
			<entry key="time">
			  <dimensionInfo>
				<enabled>false</enabled>
				<defaultValue/>
			  </dimensionInfo>
			</entry>
			<entry key="cachingEnabled">false</entry>
		  </metadata>
		  <store class="dataStore">
			<name>zonificador_quilmes:zonificador_quilmes</name>
			<atom:link xmlns:atom="http://www.w3.org/2005/Atom" rel="alternate" href="'.$service.'/rest/workspaces/zonificador_quilmes/datastores/zonificador_quilmes.xml" type="application/xml"/>
		  </store>
		  <maxFeatures>0</maxFeatures>
		  <numDecimals>0</numDecimals>
		  <overridingServiceSRS>false</overridingServiceSRS>
		  <skipNumberMatched>false</skipNumberMatched>
		  <circularArcPresent>true</circularArcPresent>
		 
		</featureType>
		';

		curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlStr);
		//POST return code
		$successCode = 201;


		$buffer = curl_exec($ch); // Execute the curl request

		$Log['tx'][]='curl ejecutado';

		// Check for errors and process results
		$info = curl_getinfo($ch);

		$Log['tx'][]=$info;   
		if ($info['http_code'] != $successCode) {

		  $msgStr = "# Unsuccessful cURL request to ";
		  $msgStr .= $url." [". $info['http_code']. "]\n";
		  fwrite($logfh, $msgStr);
			$Log['res']='err';
			$Log['tx'][]='error al publicar en geoserver';
			$Log['mg'][]='error al publicar en geoserver. tendrá que solicitar al administrador que habilite manualmetne la capa de parcelas.';
			$Log['tx'][]=$msgStr;
			$Log['tx'][]=$xmlStr;
			terminar($Log);		  
		} else {
		  $msgStr = "# Successful cURL request to ".$url."\n";
		  fwrite($logfh, $msgStr);
		  $Log['data']['creacionWMS']='exito';
		}
		fwrite($logfh, $buffer."\n");

		$Log['tx'][]=$buffer;

		curl_close($ch); // free resources if curl handle will not be reused
		fclose($logfh);  // close logfile
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
		$passwordStr = $_SESSION["GEOSERVER_USERNAME"].":".$_SESSION["GEOSERVER_PASSWORD"];
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

		$Log['tx'][]=$buffer;

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
	while($fila =pg_fetch_assoc($Consulta)){		
	
	
	  $c1_p1 = hexdec(substr($fila['grupo_co_color'], 1, 2));
	  $c1_p2 = hexdec(substr($fila['grupo_co_color'], 3, 2));
	  $c1_p3 = hexdec(substr($fila['grupo_co_color'], 5, 2));

	  $c2_p1 = hexdec(substr($fila['co_color'], 1, 2));
	  $c2_p2 = hexdec(substr($fila['co_color'], 3, 2));
	  $c2_p3 = hexdec(substr($fila['co_color'], 5, 2));

	  $r = sprintf('%02x', (round(($c1_p1 + $c2_p1)/2)));
	  $g = sprintf('%02x', (round(($c1_p2 + $c2_p2)/2)));
	  $b = sprintf('%02x', (round(($c1_p3 + $c2_p3)/2)));
	  
	  $hex ="#";
		$hex.= str_pad(dechex($r), 2, "0", STR_PAD_LEFT);
		$hex.= str_pad(dechex($g), 2, "0", STR_PAD_LEFT);
		$hex.= str_pad(dechex($b), 2, "0", STR_PAD_LEFT);
	
		$regla='
			 <Rule>
			  <Name>'.$fila['grupo'].'-'.$fila['nom_clase'].'</Name>
			  <Title>'.$fila['grupo'].'-'.$fila['nom_clase'].'</Title>          
						 
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
	
	$reglaS.='
			</FeatureTypeStyle>
		</UserStyle>
	  </NamedLayer>
	</StyledLayerDescriptor>
	';
		
	$carpeta='./documentos/p_'.str_pad($_POST['cotID'],6,"0",STR_PAD_LEFT);
	if(!file_exists($carpeta)){
		$Log['tx'][]="creando carpeta $carpeta";
		mkdir($carpeta, 0777, true);
		chmod($carpeta, 0777);	
	}
	$archivo=$Capa.'.sld';
	$myfile = fopen($carpeta.'/'.$archivo, "w");
	chmod($carpeta.'/'.$archivo, 0777);
	fwrite($myfile, $Log['data']['capa']['sld']);

	$strpass=$_SESSION["GEOSERVER_USERNAME"].":".$_SESSION["GEOSERVER_PASSWORD"];
	$exec= 'curl -v -u '.$strpass.' -XPUT ';
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



}

?>
