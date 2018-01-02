<?php 
/**
* redaccion.php
*
* redaccion.php se incorpora provisoriamente en la carpeta raiz en tanto resulta el punto inicial del modulo redacción
 * el modulo redacción es una herramienta para la redcción sistematizada de las características de los distritos de un Codigo de Ordenamiento Territorial.
*  
* 
* @package    	TReCC(tm) Procesos Participativos Urbanos.
* @subpackage 	redaccion
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

//if($_SERVER[SERVER_ADDR]=='192.168.0.252')ini_set('display_errors', '1');ini_set('display_startup_errors', '1');ini_set('suhosin.disable.display_errors','0'); error_reporting(-1);


/* verificación de seguridad */
include('./includes/conexion.php');
include('./includes/conexionusuario.php');

/* funciones frecuentes */
include("./includes/fechas.php");
include("./includes/cadenas.php");

$UsuarioI = $_SESSION['USUARIOID'];

//if($UsuarioI==""){header('Location: ./login.php');}
 

// función de consulta de argumentaciones a la base de datos 
include("./redacciones_consulta.php");

$ID = isset($_GET['argumentacion'])?$_GET['argumentacion'] : '';

$Hoy_a = date("Y");
$Hoy_m = date("m");	
$Hoy_d = date("d");
$HOY = $Hoy_a."-".$Hoy_m."-".$Hoy_d;	

 
// medicion de rendimiento lamp 
	$starttime = microtime(true);

// filtro de representación restringe documentos visulazados, no altera datos estadistitico y de agregación 
	$FILTRO=isset($_GET['filtro'])?$_GET['filtro']:'';	
	
// filtro temporal de representación restringe documentos visulazados, no altera datos estadistitico y de agregación 
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

	// función para obtener listado formateado html de argumentaciónes 
	if($_GET['id']!=''){$id=$_GET['id'];}else{$id='';}
	
	if($_GET['modo']=='texto'){
		$Contenido =  redaccionestexto($id);	
		
	}elseif($_GET['modo']=='fichas'){
		$Contenido =  redaccionesfichas($id);
	}elseif($_GET['modo']=='tabla'){
		$Contenido =  redaccionestabla($id);
	}else{
		$Contenido =  redaccioneslistado($id);
	}

	//echo "<pre>";print_r($Contenido);echo"</pre>";
	
	$def = array(
  array("date",     "D"),
  array("name",     "C",  50),
  array("age",      "N",   3, 0),
  array("email",    "C", 128),
  array("ismember", "L")
);


?>

	<title>SIGSAO - Código de Ordenamiento Territorial - Zonificación</title>
	<?php include("./includes/meta.php");?>
	<link href="css/treccppu.css" rel="stylesheet" type="text/css">
	<link href="css/redaccion.css" rel="stylesheet" type="text/css">	
	
	<style type='text/css'>
		
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
		 
		 h1{
		    font-size: 20px;
		    margin-bottom: 2px;
		    margin-top: 15px;
		 }
		h2 {
		    font-size: 16px;
		    margin-bottom: 2px;
		    margin-top: 20px;
		}
		 h3{
		 	font-size:12px;
		 	margin-bottom:1px;
		 	margin-top:3px;
		 }		 
		 a{
		 	display:block;
		 }
		 table {
		    border-collapse: collapse;
		}
		table, th, td {
		    border: 1px solid black;
		    font-size:11px;
		}
		th{
			width:80px;
		}
		
		.mapaloc{
			height:60px;
		}
		td{
			min-width:45px;
		} 
		 .ficha{
		 	width:100%;
		 	border-top:4px solid #000;
		 	border-bottom:4px solid #000;
		 	margin-bottom:10px;
		 	margin-top:20px;
		 	page-break-after: always;
		 	background=color:#fff;
		 }
		 
		 .encabezado{
		 	border-bottom:2px solid #000;
		 	width:100%;
		 }
		 .medio{
		 	border-bottom:2px solid #000;
		 	width:100%;
		 }
		 		
		 .c1, .c2{
		 	vertical-align:top;
		 } 
		 .c1{
		 	width:40%;
		 	display:inline-block;
		 	border-right:2px solid #000;
		 }
		 .c2{
		 	width:59%;
		 	display:inline-block;
		 	position:relative;
		 	left:-2px;
		 	border-left:2px solid #000;
		 }
		 .c1 > div{
		 	border-bottom:1px solid #000;
		 	display:inline-block;
		 	width:100%;		 	
		 }
		 .c2 > div{
		 	border-bottom:1px solid #000;
		 	display:inline-block;
		 	width:100%;
		 }

	 
		 .titulo{
		 	font-weight:bold;
		 }
		 
		 .nomenclatura{
		 	font-size:60px;
		 }
		 .nombre{
		 	font-size:20px;
		 }
		 div.caracter{
		 	border:0px;
		 }
		 
		 .ocupacion > div{
		 	border-bottom:1px solid #000;
		 	border-top:1px solid #000;
		 }
		 
		 .fos{
		 	width:49%;
		 	display:inline-block;
		 }
		 .fos > .cont{
		 	font-size:25px;		 	
		 }
		 
		 .fot{
		 	width:50%;
		 	display:inline-block;
		 	border-right:1px solid #000;		 	
		 }
		 .fot > .cont{
		 	font-size:25px;		 	
		 }		
		 
		 .altura > .titulo{
		 	width:35%;
		 	display:inline-block;
		 } 		 
		 .altura > .cont{
		 	width:40%;
		 	display:inline-block;
		 	font-size:25px;
		 } 	
		 
		 .ladosup{
		 	border-top:1px solid #000;
		 	border-bottom:1px solid #000;		 	
		 }
		 
		 .lado{
		 	width:49%;
		 	display:inline-block;
		 	border-right:1px solid #000;
		 	
		 }
		 .lado > .cont{
		 	font-size:25px;		 	
		 }
		 
		 .aclaracion{
		 	font-size:10px;
		 }
		 
		 .sup{
		 	width:50%;
		 	display:inline-block;
		 	
		 	position:relative;
		 	left:-1px;
		 	border-left:1px solid #000;
		 			 	
		 }
		  .sup > .cont{
		 	font-size:22px;		 	
		 }	
		 div.subdivision{
		 	border-top:1px solid #000;
		 	border-bottom:0px solid #000;
		 }		
		 
		 div.des:last-child{
		 	border-bottom:0px;
		 }

		.observ{
			font-size:12px;
		}
		
		 .pie > div{
		 	border-bottom:1px solid #000;
		 	display:inline-block;
		 	width:100%;		 	
		 }	
		 
		 .mapa{
		 	border:1px solid #aaa;
		 	margin:5px auto 5px auto;
		 	height:300px;
		 	width:600px;
		 }
		 .autonumeracion{
		 	font-size:24px;
		 }
		 
		 #recuadro3{
		 	
		 }
		 table{
		 	margin: 0px auto;
		 }
	<?php
	if($_SESSION['modo']=='pdf'){
		echo "
		#pageborde{
			background-color:#fff;;
			margin-top:0;
			border:0px;
		}
		#page{
			margin:0;
			background-color:transparent;
			width:754
		}
		#contenido{
			margin:0;
			border:0px;
		}
		body{
			margin:0;	
			background-image:none;		
		}		
		.ficha{
			border:2px solid #000;
		}
		.caracter{
			font-size:11px;
		}
		.espub > p{
			font-size:11px;			
		}
		p{
		 	font-size:13px;
		 	text-align : justify;
		 }
		 .defecto{
		 	color:#000;
		 }
		";
	}
	
	if($_SESSION['modo']=='html'){
	echo "
	
	.defecto{
		color:#000;
	}
	";	
	}	
	?>
	
	@media print{
		body{
			
		}
		.recuadro{
			display:none;
		}
		#menu{
			display:none;
		}
		#contenido{
			margin:0;
		}
		.ficha{
			margin:0;
			border:2px solid #000;
		}
		#pie{
			display:none;
		}
	}		 
	</style>
	
	
</head>

<body>
	
	<?php

	 
	if($_SESSION['modo']!='html'){
		
		include('./includes/encabezado.php');	
		echo "<div class='recuadro' id='recuadro2'>";
			echo "Modo: ".$_SESSION['modo'];
			echo "<a target='recuadro3' href='./redacc_cambiamodo.php?modo=pdf'>poner en modo pdf</a>"; 
			echo "<a target='recuadro3' href='./redacc_cambiamodo.php?modo=html'>poner en modo html</a>"; 
			echo "<a target='recuadro3' href='./redacc_cambiamodo.php?modo=php'>poner en modo php</a>"; 
			
			echo "<br><br>Descargar: ";
			echo "<a target='recuadro3' href='./descargatabla.php'>descargar tabla csv</a>";
		echo "</div>";
		echo "<iframe class='recuadro' name='recuadro3' id='recuadro3' src=''></iframe>";
		
	}
	
	 	
	echo ' 
	<div id="pageborde"><div id="page">
		<div id="menu">
			<h1>Distritos</h1>
			<p>En esta página usted puede acceder a la información técnica de cada distrito.</p>
			<p>Los usuarios haibilitados pueden editar el contenido.</p>';
			
			
		if($_SESSION['modo']!='html'){
			$link="./redaccion.php?modo=texto";
		}else{
			$link="./COT_texto.html";
		}	
		echo "<a href='$link'>ver en modo texto</a>";
		
		
		if($_SESSION['modo']!='html'){
			echo "<a href='./redaccion.php?modo=edicion'>ver en modo edición</a>";
		}
		
		if($_SESSION['modo']!='html'){
			$link="./redaccion.php?modo=fichas";
		}else{
			$link="./COT_fichas.html";
		}
						
		echo "<a href='$link'>ver en modo fichas</a>";
		
		if($_SESSION['modo']!='html'){
			$link="./redaccion.php?modo=tabla";
		}else{
			$link="./COT_tabla.html";
		}	
		echo "<a href='$link'>ver en modo tabla</a>";
		
	 	
		

			//formulario para agregar una nueva argumentación
			
					
		if($ID==''){
			if($_SESSION['modo']!='html'){
				echo "<form method='post' action='./agrega_f.php?salida=redaccion&accion=agrega&tabla=COUdistritos'>";
				echo "<input type='submit' value='agregar una nueva zona'>";
				echo "</form>";
				}
				echo "</div>";	
			echo "<div class='contenido'>";
			echo $Contenido;
			echo "</div>";	
					
		}
	 
		?>
	
	</div></div>
	

<?php
include('./includes/pie.php');
	/*medicion de rendimiento lamp*/
	$endtime = microtime(true);
	$duration = $endtime - $starttime;
	$duration = substr($duration,0,6);
	//echo "<br>tiempo de respuesta : " .$duration. " segundos";
	
		// carga sistema de mensajes de desarrollo
	include("./mensajesdesarrollo.php");

?>
</body>

<!-- este proyecto recurre al proyecto tiny_mce para las funciones de edición de texto -->
<script type="text/javascript" src="./js/editordetexto/tiny_mce.js"></script>
<script type="text/javascript">

		tinyMCE.init({
				
	        // General options
	        mode : "textareas",
	        theme : "advanced",
	        plugins : "autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
	
			forced_root_block : "",
		    remove_linebreaks : true,
		    remove_trailing_nbsp : true,
		    force_br_newlines : false,
	        force_p_newlines : true,
		    fix_list_elements : false,
		    remove_linebreaks : true,
			width : "550px",
			height : "600px",
	
	        // Theme options
	        theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,fontsizeselect|cut,copy,paste,pastetext,pasteword,|,bullist,numlist,|,link,unlink|visualchars,nonbreaking,blockquote|tablecontrols,|,removeformat,visualaid,",
	        theme_advanced_toolbar_location : "top",
	        theme_advanced_toolbar_align : "left",
	        theme_advanced_statusbar_location : "bottom",
	
	        // Skin options
	        skin : "o2k7",
	        skin_variant : "silver",
	
	        // Example content CSS (should be your site CSS)
	        content_css : "",
	
	        // Drop lists for link/image/media/template dialogs
	        template_external_list_url : "js/template_list.js",
	        external_link_list_url : "js/link_list.js",
	        external_image_list_url : "js/image_list.js",
	        media_external_list_url : "js/media_list.js",
	
	        // Replace values for the template plugin
	        template_replace_values : {
	                username : "Some User",
	                staffid : "991234"
	        }
		});
</script>