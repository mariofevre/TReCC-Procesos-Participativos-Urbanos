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
		/*
		            // Creamos una capa base
		            var capa = new OpenLayers.Layer.WMS( 
		                "Base de calles OSM",
		                "http://full.wms.geofabrik.de/web/975d3dc24139f06ce8306f9353d28c10?", 
		                {layers: 'basic'},
		                {attribution:"Base OSM bajo servidor GEOFABRIK"}
		            );
		*/
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




?>
</body>

