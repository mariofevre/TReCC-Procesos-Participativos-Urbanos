<?php
/**
*argumentacionlocalizacion.php
*
* argumentacionlocaliazcion.php se incorpora en la carpeta raiz 
* esta aplicación está diseñada para ser cargada dentro de in iframe en la ventana principal 
* para la carga y esdicion de localizaciones en coordenadas geográficas asociadas a una argumentación.
* 
* 
* @package    	TReCC(tm) Procesos Participativos Urbanos.
* @subpackage 	documentos
* @author     	TReCC SA
* @author     	<mario@trecc.com.ar> <trecc@trecc.com.ar>
* @author    	www.trecc.com.ar  
* @copyright	2013 TReCC SA
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

include('./includes/conexion.php');
include('./includes/conexionusuario.php');
include("./includes/fechas.php");
include("./includes/cadenas.php");

$UsuarioI = $_SESSION['USUARIOID'];
if($UsuarioI==""){header('Location: ./login.php');}


$FILTRO=isset($_GET['filtro'])?$_GET['filtro']:'';

$MODO = $_GET['modo'];


?>
<head>
	<title>Panel de control</title>
	<?php 
	include("./includes/meta.php");
	?>
	 <style type='text/css'>
	 	body, input, form{
	 		font-size:9px;
	 		font-family:arial;
	 		margin:0;
	 		
	 	}
	 	input{
	 		width:200px;
	 	}
	 	
	 	input[type='submit']{
	 		width:80px;
	 		display:block;
	 		margin:2px;
	 		margin-top:4px;
	 	}	 	
	 	input[readonly='readonly']{
	 		width:42px;
	 		display:inline;
	 		margin:0px;
	 		background-color:transparent;
	 		border:none;
	 	}
	 	
	 	label{
	 		margin-top:5px;
	 		display: inline-block;
	 		font-size:12px;
	 		font-weight:bold;
	 	}
	 a{
	 	display: inline-block;
	    max-height: 13px;
	    max-width: 110px;
	    overflow: hidden;
	 }
	 p{
	 	margin:0px;
	 }
 	 #divMapa { 
 	 	 
 	 	height: 200px; 
		border: solid 2px #808080; 
		}
		
		a.olControlZoomOut.olButton{
			font-size:10px;
			line-height:10px;
			width:13px;
		}
		a.olControlZoomIn.olButton{
			font-size:10px;
			line-height:10px;
			width:13px;
		}		
		div.olControlZoom.olControlNoSelect {
		    left: 3px;
		    top: 3px;
		}
		.olControlAttribution olControlNoSelect{
		 	bottom: 10px;
		}
	 </style>
	 
	
<?php


/* define si se esta defininedo una argumentación, de no ser así esta aplicación solo devuelve un mensaje escrito*/
if(isset($_POST['argumentacion'])){
	$ARGUMENTACION=$_POST['argumentacion'];
	$ACCION = $_POST['accion'];
}elseif(isset($_GET['argumentacion'])){
	$ARGUMENTACION=$_GET['argumentacion'];
}else{
	$ARGUMENTACION='';
}


if($ARGUMENTACION!=''){
/* consulta la argumentación solicitada */	
$query="
	SELECT 
		`PARTICargumentaciones`.`id`,
	    `PARTICargumentaciones`.`resumen`,
	    `PARTICargumentaciones`.`argumentacion`,
	    `PARTICargumentaciones`.`id_p_PARTICactores_id`,
	    `PARTICargumentaciones`.`zz_AUTOUSUARIO`,
	    `PARTICargumentaciones`.`zz_AUTOFECHACREACION`,
	    
		`PARTICargumentacionesLOC`.`id`,
	    `PARTICargumentacionesLOC`.`id_p_PARTICargumentaciones`,
	    `PARTICargumentacionesLOC`.`zz_AUTOUSUARIOCREACION`,
	    `PARTICargumentacionesLOC`.`latitud`,
	    `PARTICargumentacionesLOC`.`longitud`,
	    `PARTICargumentacionesLOC`.`descripcion`	
	        
	FROM 
		`sigsao`.`PARTICargumentaciones`
	LEFT JOIN 
		(SELECT * FROM 
			`sigsao`.`PARTICargumentacionesLOC`
			WHERE zz_borrada='0'
		)as PARTICargumentacionesLOC
		ON `PARTICargumentacionesLOC`.`id_p_PARTICargumentaciones`=`PARTICargumentaciones`.id
	WHERE 
		`PARTICargumentaciones`.`id`='".$ARGUMENTACION."'
	
	";
	$Consulta = mysql_query($query,$Conec1);
	echo mysql_error();
	
	while($row=mysql_fetch_assoc($Consulta)){
		if($row['latitud']!=''&&$row['llongitud']!=''){
			$LocalizacionesArg[]=$row;
		}
	}

	/* verifica el permiso del usuario para su acceso */	
	$autor = mysql_result($Consulta,0,'id_p_PARTICactores_id');
	if($autor == $UsuarioI || $UsuarioI==9){//permie el acceso a usuario con acesos especiales
		/* habilitado para agregar data*/
		
		/* si el formulario de reingreso así lo definió crea un nuevo registro de lo0calización */		
		if($ACCION=="cargar"&&$autor == $UsuarioI){
			
			$query="
						
				INSERT INTO 
					`sigsao`.`PARTICargumentacionesLOC`
				SET 							
					`id_p_PARTICargumentaciones`='".$ARGUMENTACION."',
					`zz_AUTOUSUARIOCREACION`='".$UsuarioI."',
					`latitud`='".$_POST['latitud']."',
					`longitud`='".$_POST['longitud']."',
					descripcion='".$_POST['descripcion']."'
			";
			$Consulta = mysql_query($query,$Conec1);
			echo mysql_error($Conec1);
			$NID=mysql_insert_id($Conec1);
			echo $NID;
			echo "<p>localización guardada:</p>
			<p>MAPA</p>";
			?>
			<script type='text/javascript'>			
			// el elemento creado será representado dentro de un div en la ventana principal
			var _nuevaloc = document.createElement('div');
			_nuevaloc.setAttribute('class', 'elemento');
			_nuevaloc.setAttribute('id', 'loc<?php echo $NID;?>');
			_nuevaloc.setAttribute('onclick', "document.getElementById('recuadro3').src='./argumentacionlocalizacion.php?argumentacion=<?php echo $ARGUMENTACION;?>&localizacion=<?php echo $NID;?>';");
			_nuevaloc.innerHTML="<?php echo $_POST['latitud']." / ".$_POST['longitud'];?>'";
			_padre=window.parent.document.getElementById('contenedordelocalizaciones');
			_padre.innerHTML=_padre.innerHTML+' ';
			_padre.appendChild(_nuevaloc);
			</script>
			<?php
			
		}elseif($ACCION=="borrar"&&$autor == $UsuarioI){
			/* si el formulario de reingreso así lo definió modifica el registro como borrado */
			$query="
				UPDATE 
					`sigsao`.`PARTICargumentacionesLOC`
				SET 							
					`zz_borrada`='1'
				WHERE
					id = ".$_POST['localizacion']."
					AND id_p_PARTICargumentaciones='".$ARGUMENTACION."'
					AND `zz_AUTOUSUARIOCREACION`='".$UsuarioI."'
			";
			$Consulta = mysql_query($query,$Conec1);
			echo mysql_error($Conec1);
			
			echo "<p>localizacion borrada:</p>";
			?>
			<script type='text/javascript'>
			// el elemento div que represanta esta imagen en la ventana principal deja de ser mostrado //
			window.parent.document.getElementById('loc<?php echo $_POST['localizacion'];?>').style.display='none';
			</script>
			<?php
		}elseif(isset($_GET['localizacion'])){
			/* si no hay acciones definidas en el formulario de reingreso se consulta el registro para mostrar su información */
			$query="
			SELECT 
				`PARTICargumentacionesLOC`.`id`,
			    `PARTICargumentacionesLOC`.`id_p_PARTICargumentaciones`,
			    `PARTICargumentacionesLOC`.`zz_AUTOUSUARIOCREACION`,
			    `PARTICargumentacionesLOC`.`latitud`,
			    `PARTICargumentacionesLOC`.`longitud`,
			    `PARTICargumentacionesLOC`.`descripcion`
			FROM
			`sigsao`.`PARTICargumentacionesLOC`
			WHERE 
				`id`='".$_GET['localizacion']."' 
				AND `id_p_PARTICargumentaciones`='".$ARGUMENTACION."' 
			";
			
			$Consulta = mysql_query($query,$Conec1);
			echo mysql_error($Conec1);
			$lat=mysql_result($Consulta,0,'latitud');
			$lon=mysql_result($Consulta,0,'longitud');
			echo "Localizacion:";
			
			echo "<p>".mysql_result($Consulta,0,'descripcion')."</p>";
			
			/* esta aplicación utiliza las librerías de openlayers para mostrar 
			 * un mapa interactivo que permite definir las localizaciónes*/
			?>
		   <script language="javascript">      
		      function configure() {
				var template = document.demo.template.options[document.demo.template.selectedIndex].value;
		        var snippet = " TEMPLATE " + template;
		        document.demo.map_web.value = snippet;
		        if(template.indexOf("frame") != -1) document.demo.action = "frames.html";
		        if(template.indexOf("dhtml") != -1) document.demo.action = "frames_dhtml.html";
		      }      
		    </script>
		    
		    <script src="http://www.openlayers.org/api/OpenLayers.js"></script>	    
		    
		    <script type="text/javascript">
		        // Definimos las variables globales 'mapa' y 'capa'
		        var mapa, capa, marcklayer, vectorLayer, controls, drawPoint, point_style;
		        
		
		        // Definimos una función que arranque al cargar la página
		        window.onload= function() {
		            // Creamos el mapa
		            var mapa = new OpenLayers.Map("divMapa");
		
		            // Creamos una capa base
		            var capa = new OpenLayers.Layer.WMS( 
		                "Base de calles OSM",
		                "http://129.206.228.72/cached/osm?LAYERS=osm_auto:all&STYLES=&FORMAT=image%2Fpng&SERVICE=WMS&VERSION=1.1.1&REQUEST=GetMap", 
		                {layers: 'osm_auto:all'},
		                {attribution:"Base OSM bajo servidor GEOFABRIK"}
		            );
		
		 			//creamos capa de puntos de carga
		            vectorLayer = new OpenLayers.Layer.Vector("Nuevos puntos");

					var point =
				        new OpenLayers.Geometry.Point(<?php echo $lon.", ".$lat;?>);
				    var pointFeature =
				        new OpenLayers.Feature.Vector(point, null);
			
					// Añadir el feature creado a la capa de puntos existentes       
					vectorLayer.addFeatures(pointFeature);

		            // Añadimos las capas al mapa
		            mapa.addLayers([capa, vectorLayer]);
		            // Fijamos centro y zoom
		            mapa.zoomToMaxExtent();
		
		            mapa.addControl(new OpenLayers.Control.MousePosition());
		
		            mapa.setCenter(new OpenLayers.LonLat(<?php echo $lon.", ".$lat;?>), 12);
		            
		            vectorLayer.events.register('beforefeatureadded', vectorLayer, function (limpiar) {
		            vectorLayer.removeAllFeatures();
					});   
				
		        }

			</script>		
			</head>
			<body>
			
			<div id="divMapa"></div>
			
			<form method='POST' action='./argumentacionlocalizacion.php' enctype='multipart/form-data'>
			<input type='hidden' value='borrar' name='accion'>
			<input type='hidden' value='<?php echo $ARGUMENTACION;?>' name='argumentacion'>
			<input type='hidden' value='<?php echo $_GET['localizacion'];?>' name='localizacion'>
			<input type='button' value='borrar localizacion' onclick="this.nextSibling.nextSibling.style.display='block';">
			<input style='display:none;' type='button' value='confirmo borrar' onclick='this.parentNode.submit();'>
			</form>
			<?php
			/*echo $query;*/
		}else{
			// si se no se ingresa ninguna localización se asume que se creará una nueva
			
			/* esta aplicación utiliza las librerías de openlayers para mostrar 
			 * un mapa interactivo que permite definir las localizaciónes*/	
			?>
			<script language="javascript">
			   
				function configure() {
					var template = document.demo.template.options[document.demo.template.selectedIndex].value;
					var snippet = " TEMPLATE " + template;
			        document.demo.map_web.value = snippet;
					if(template.indexOf("frame") != -1) document.demo.action = "frames.html";
			        if(template.indexOf("dhtml") != -1) document.demo.action = "frames_dhtml.html";
			   }      
			</script>
			<script src="http://www.openlayers.org/api/OpenLayers.js"></script>		
				 
			<script type="text/javascript">
			        // Definimos las variables globales 'mapa' y 'capa'
			        var mapa, capa, marcklayer, vectorLayer, controls, drawPoint, point_style;
			        
			
			        // Definimos una función que arranque al cargar la página
			        window.onload= function() {
			            // Creamos el mapa
			            var mapa = new OpenLayers.Map("divMapa");
			
			            // Creamos una capa base
			            var capa = new OpenLayers.Layer.WMS( 
			                "Base de calles OSM",
			                "http://full.wms.geofabrik.de/web/975d3dc24139f06ce8306f9353d28c10?", 
			                {layers: 'basic'},
			                {attribution:"Base OSM bajo servidor GEOFABRIK"}
			            );
			
			 			//creamos capa de puntos de carga
			            vectorLayer = new OpenLayers.Layer.Vector("Nuevos puntos");

			            // Añadimos las capas al mapa
			            mapa.addLayers([capa, vectorLayer]);
			            // Fijamos centro y zoom
			            mapa.zoomToMaxExtent();
			
			            mapa.addControl(new OpenLayers.Control.MousePosition());
			
			            drawPoint=new OpenLayers.Control.DrawFeature(vectorLayer,OpenLayers.Handler.Point);
						drawPoint.featureAdded = featAdded;
						mapa.addControl(drawPoint);
			
			            mapa.setCenter(new OpenLayers.LonLat(-64.99855041504, -40.763397216798), 10);
			            
			            vectorLayer.events.register('beforefeatureadded', vectorLayer, function (limpiar) {
			            vectorLayer.removeAllFeatures();
						});   
						
						drawPoint.activate();
			        }
			
			        function featAdded() {
						var _lat = document.getElementById("lat");
						_lat.value=drawPoint.handler.point.geometry.y;
						var _lon = document.getElementById("lon");
						_lon.value=drawPoint.handler.point.geometry.x;
						document.getElementById('submit').style.display='block';
			        }

			</script>
			<?php
			
			/* si no hay registros de imagen definidos se muestra un formulario de reingreso para crear un nuevo registro para la argumentación solicitada */			
			echo "indique el punto en el mapa";
				echo "<form method='POST' action='./argumentacionlocalizacion.php' enctype='multipart/form-data'>";			
				echo "<p>Agregar Localización:";
				echo '<input readonly="readonly" name="latitud" type="text" value="" id="lat"/"> / ';
				echo '<input readonly="readonly" name="longitud" type="text" value="" id="lon"/>';
				echo "</p>";			
				echo '<div id="divMapa"></div>';
				echo "<input type='hidden' name='accion' value='cargar'>";	
				echo "<input type='hidden' name='argumentacion' value='".$ARGUMENTACION."'>";	
				echo "<label>descripción de la localización:</label><input type='text' name='descripcion'>";
				echo "<input id='submit' style='display:none' type='submit' value='guardar'>";			
			echo "</form>";
			
			
		}
		
	}else{
		echo "error e la identificación de permisos locales";
	}
}else{
	
	$query="
			SELECT 
				`PARTICargumentacionesLOC`.`id`,
			    `PARTICargumentacionesLOC`.`id_p_PARTICargumentaciones`,
			    `PARTICargumentacionesLOC`.`zz_AUTOUSUARIOCREACION`,
			    `PARTICargumentacionesLOC`.`latitud`,
			    `PARTICargumentacionesLOC`.`longitud`,
			    `PARTICargumentacionesLOC`.`descripcion`
			FROM
			`sigsao`.`PARTICargumentacionesLOC`
			WHERE 
				`id_p_PARTICargumentaciones`='".$ARGUMENTACION."' 
			";
			$Consulta = mysql_query($query,$Conec1);
			echo mysql_error($Conec1);
			$lat=mysql_result($Consulta,0,'latitud');
			$lon=mysql_result($Consulta,0,'longitud');
			echo "Localizacion:";
	

	?>
		   <script language="javascript">
		      function configure() {
				var template = document.demo.template.options[document.demo.template.selectedIndex].value;
		        var snippet = " TEMPLATE " + template;
		        document.demo.map_web.value = snippet;
		        if(template.indexOf("frame") != -1) document.demo.action = "frames.html";
		        if(template.indexOf("dhtml") != -1) document.demo.action = "frames_dhtml.html";
		      }      
		    </script>
		    
		    <script src="http://www.openlayers.org/api/OpenLayers.js"></script>	    
		    
		    <script type="text/javascript">
		        // Definimos las variables globales 'mapa' y 'capa'
		        var mapa, capa, marcklayer, vectorLayer, controls, drawPoint, point_style;
		        
		
		        // Definimos una función que arranque al cargar la página
		        window.onload= function() {
		            // Creamos el mapa
		            var mapa = new OpenLayers.Map("divMapa");
		
		            // Creamos una capa base
		            /*
		            var capa = new OpenLayers.Layer.WMS( 
		                "Base de calles OSM",
		                "http://full.wms.geofabrik.de/web/975d3dc24139f06ce8306f9353d28c10?", 
		                {layers: 'basic'},
		                {attribution:"Base OSM bajo servidor GEOFABRIK"}
		            );*/
		           
		            // Creamos una capa base
		            
		            var capa = new OpenLayers.Layer.WMS( 
		                "Base de calles OSM",
		                "http://129.206.228.72/cached/osm?", 
		                {layers: 'basic'},
		                {attribution:"Base OSM bajo servidor GEOFABRIK"}
		            );
		            
		            		
		 			//creamos capa de puntos de carga
		            vectorLayer = new OpenLayers.Layer.Vector("Nuevos puntos");


					var point =
				        new OpenLayers.Geometry.Point(<?php echo $lon.", ".$lat;?>);
				    var pointFeature =
				        new OpenLayers.Feature.Vector(point, null);
			
					// Añadir el feature creado a la capa de puntos existentes       
					vectorLayer.addFeatures(pointFeature);

		            // Añadimos las capas al mapa
		            mapa.addLayers([capa, vectorLayer]);
		            // Fijamos centro y zoom
		            mapa.zoomToMaxExtent();
		
		            mapa.addControl(new OpenLayers.Control.MousePosition());
		
		            mapa.setCenter(new OpenLayers.LonLat(<?php echo $lon.", ".$lat;?>), 12);
		            
		            vectorLayer.events.register('beforefeatureadded', vectorLayer, function (limpiar) {
		            vectorLayer.removeAllFeatures();
					});   
				
		        }

			</script>		
			</head>
			<body>
			
			<div id="divMapa"></div>
			
			<form method='POST' action='./argumentacionlocalizacion.php' enctype='multipart/form-data'>
			<input type='hidden' value='<?php echo $ARGUMENTACION;?>' name='argumentacion'>
			<input type='hidden' value='<?php echo $_GET['localizacion'];?>' name='localizacion'>
			<input style='display:none;' type='button' value='confirmo borrar' onclick='this.parentNode.submit();'>
			</form>
			<?php
}



?>
</body>

