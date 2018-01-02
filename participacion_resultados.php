<?php 
/**
* participacion_resultados.php
*
* participacion_resultados.php se incorpora provisoriamente en la carpeta raiz en tanto resulta una salida básica del sistema
*  
* 
* @package    	TReCC(tm) Procesos Participativos Urbanos.
* @subpackage 	argumentaciones
* @author     	TReCC SA
* @author     	<mario@trecc.com.ar> <trecc@trecc.com.ar>
* @author    	www.trecc.com.ar  
* @copyright	2014 TReCC SA
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

/*if($_SERVER[SERVER_ADDR]=='192.168.0.252')ini_set('display_errors', '1');ini_set('display_startup_errors', '1');ini_set('suhosin.disable.display_errors','0'); error_reporting(-1);*/

/* verificación de seguridad */
include('./includes/conexion.php');
include('./includes/conexionusuario.php');

/* funciones frecuentes */
include("./includes/fechas.php");
include("./includes/cadenas.php");

$UsuarioI = $_SESSION['USUARIOID'];
if($UsuarioI==""){header('Location: ./login.php');}


//provisoariamente sono el id de usuario del programador del sistema puede acceder a esta información
if($UsuarioI!=9){
	header('Location: ./login.php');
}

/* función de consulta de argumentaciones a la base de datos */
include("./argumentaciones_consulta.php");

$ID = isset($_GET['argumentacion'])?$_GET['argumentacion'] : '';

$Hoy_a = date("Y");
$Hoy_m = date("m");	
$Hoy_d = date("d");
$HOY = $Hoy_a."-".$Hoy_m."-".$Hoy_d;	


/* medicion de rendimiento lamp */
	$starttime = microtime(true);

/* filtro de representación restringe documentos visulazados, no altera datos estadistitico y de agregación */
	$FILTRO=isset($_GET['filtro'])?$_GET['filtro']:'';	
	
/* filtro temporal de representación restringe documentos visulazados, no altera datos estadistitico y de agregación */	
	$fechadesde_a=isset($_GET['fechadesde_a'])?str_pad($_GET['fechadesde_a'], 4, "0", STR_PAD_LEFT):'0000';
	$fechadesde_m=isset($_GET['fechadesde_m'])?str_pad($_GET['fechadesde_m'], 2, "0", STR_PAD_LEFT):'00';
	$fechadesde_d=isset($_GET['fechadesde_d'])?str_pad($_GET['fechadesde_d'], 2, "0", STR_PAD_LEFT):'00';
	if($fechadesde_a!='0000'&&$fechadesde_m!='00'&&$fechadesde_d!='00'){
		$FILTROFECHAD=$fechadesde_a."-".$fechadesde_m."-".$fechadesde_d;
	}else{
		$FILTROFECHAD='';
	}
	$fechahasta_a=isset($_GET['fechahasta_a'])?str_pad($_GET['fechahasta_a'], 4, "0", STR_PAD_LEFT):'0000';
	$fechahasta_m=isset($_GET['fechahasta_m'])?str_pad($_GET['fechahasta_m'], 2, "0", STR_PAD_LEFT):'00';
	$fechahasta_d=isset($_GET['fechahasta_d'])?str_pad($_GET['fechahasta_d'], 2, "0", STR_PAD_LEFT):'00';
	if($fechahasta_a!='0000'&&$fechahasta_m!='00'&&$fechahasta_d!='00'){
		$FILTROFECHAH=$fechahasta_a."-".$fechahasta_m."-".$fechahasta_d;
	}else{	
		$FILTROFECHAH='';
	}

	/* función para obtener listado formateado html de argumentaciónes */
	$Contenido =  argumentacionesreporte($ID,'todo');
	
	
?>

	<title>SIGSAO - Registro de Actores para los procesos participativos</title>
	<?php include("./includes/meta.php");?>
	<link href="css/treccppu.css" rel="stylesheet" type="text/css">
	
	
	<style type='text/css'>
		.dato.fecha{
		    width: 60px;
		}
		.dato.autor{
		    width: 90px;
		}		
		.dato.descripcion{
		    width: 260px;
		}		
		
		.dato.localizaciones, .dato.imagenes{
			font-size: 11px;
		}
		
		.elemento {
		    background-color: #ADD8E6;
		    border: 2px solid #08AFD9;
		    cursor: pointer;
		    display: inline-block;
		    font-size: 10px;
		    height: 14px;
		    overflow: hidden;
		    padding: 2px 1px;
		    position: relative;
		    width: 16px;
		 }
		 
		.ilustracion{
		    background-color: #ADD8E6;
		    border: 2px solid #08AFD9;
		    cursor: pointer;
		    display: inline-block;
		    font-size: 10px;
		    overflow: hidden;
		    padding: 2px 1px;
		    position: relative;
		    width: 160px;
		 }		
		 
		 .argumentacion{
		 	border-top:2px solid #000;
		 } 
		 
		 @media print{
		 	body{
		 		background-image:none;
		 	}
		 	#recuadro1{
		 		display:none;
		 	}
		 	
		 	#pageborde{
		 		background-color:#fff;
		 	}
		 	#page{
		 		width:600px;
		 	}
		 	#pie{
		 		display:none;
		 	}		 	
		 }

	</style>
	
	
</head>

<body>
	
	<?php
	include('./includes/encabezado.php');
	
	if($ID!=''){
	?>	
	<iframe class='recuadro' id="recuadro2" src="./argumentacionimagen.php"></iframe>
	<iframe class='recuadro' id="recuadro3" src="./argumentacionlocalizacion.php"></iframe>
	<?php
	}
	?>
	
	<div id="pageborde"><div id="page">
		<h1>Reporte de Argumentaciones</h1>
		<p>En esta página se presenta un resumen de las argumentaciónes presentadas por la comunidad.</p>
		
		<?php
			/* formulario para agregar una nueva argumentación */		
		if($ID==''){
			echo $Contenido;			
		}
		?>
	
	</div></div>
	

<?php
include('./includes/pie.php');
	/*medicion de rendimiento lamp*/
	$endtime = microtime(true);
	$duration = $endtime - $starttime;
	$duration = substr($duration,0,6);
	echo "<br>tiempo de respuesta : " .$duration. " segundos";
?>
</body>
