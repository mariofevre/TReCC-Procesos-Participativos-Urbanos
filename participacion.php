<?php 
/**
* participacion.php
*
* participacion.php se incorpora provisoriamente en la carpeta raiz en tanto resulta el punto inicial del de participación 
* se prevé a futuro renombrar este código como un m´çodulo componente.
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

/* función de consulta de argumentaciones a la base de datos */
include("./argumentaciones_consulta.php");

$ID = isset($_GET['argumentacion'])?$_GET['argumentacion'] : '';

$Hoy_a = date("Y");
$Hoy_m = date("m");	
$Hoy_d = date("d");
$HOY = $Hoy_a."-".$Hoy_m."-".$Hoy_d;	


/* el reingreso a esta dirección desde su propio formulario php crea o modifica un registro de argumentación */
if(isset($_POST['accion'])){
	$accion =$_POST['accion'];
	
	if($accion=='crear'){
		$query="
		INSERT INTO 
			`sigsao`.`PARTICargumentaciones`
			SET
			`id_p_PARTICactores_id`='".$UsuarioI."',
			`zz_AUTOUSUARIO`='".$UsuarioI."',
			`zz_AUTOFECHACREACION`='".$HOY."'
		";
		mysql_query($query,$Conec1);
		$NID=mysql_insert_id($Conec1);
		if($NID!=''){
			$ID=$NID;
		}else{
			$mensaje="<div class='error'>no se ha podido crear el nuevo registro, por favor vuelva a intentar}";
		}
	}
			
	if($_POST['accion']=='guardar'){
		$query = "
			UPDATE `sigsao`.`PARTICargumentaciones`
			SET
			`resumen` = '".$_POST['resumen']."',
			`argumentacion` = '".$_POST['argumentacion']."'
			WHERE `id` = '".$_POST['id']."'
			AND id_p_PARTICactores_id = '".$UsuarioI."'";
		mysql_query($query,$Conec1);
		echo mysql_error($Conec1);
	}
}

	
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
	$Contenido =  argumentacioneslistado($ID);
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
		<h1>Argumentaciones</h1>
		<p>En esta página usted puede presentar argumentaciones a considerar para el desarrollo del proyecto urbano.</p>
		<p>Se limita la presentación de hasta cuatro argumentaciones por persona.</p>

		
		<?php

			/* formulario para agregar una nueva argumentación */		
		if($ID==''){
			echo "<form method='post' action='participacion.php'>";
			echo "<input type='submit' value='agregar una nueva argumentación'>";
			echo "<input type='hidden' name='accion' value='crear'>";
			echo "</form>";
			echo "<div class='contenido'>";
			echo $Contenido;
			echo "</div>";				
		}else{
			/* formulario para modificar una argumentación */
			$datosargumentacion = argumentacionesconsulta($ID);
			if($datosargumentacion[$ID]['zz_AUTOUSUARIO']==$UsuarioI){
			?>
			<div class='contenido'>
				<form id='principal' action="participacion.php" method="post">
				<input name="accion" type="hidden" value="guardar"/>
				<input name="id" type="hidden" value="<?php echo $ID;?>"/>
							
				<div>
				<input type='submit' value='guardar los cambios en esta argumentación' onclick="document.getElementById('principal').submit();">
				</div>
				<div>
				<label for="resumen">Descripción<span>Describa en no más de 10 palabra su argumentación</span></label>
				<input name="resumen" type="text" class="required" value="<?php echo $datosargumentacion[$ID]['resumen'];?>"/>
				</div>
				<div>
				<label for="imagenes">Imagenes<span>Acompañe su argumentación escrita con archivos de imagen ilustrativos (max. 4)</span></label>
					<div class='contenedordatos' id='contenedordeimagenes'>
					<?php
					/* Una misma argumentación puede contar con N imágenes asociadas 
					 * que son cargada y modificadas dentro de un iframe 
					 * al cual se llama desde los botones a continuación*/					
					$imagenes = $datosargumentacion[$ID]['imagenes'];
					foreach($imagenes as $img){
						$nimg++;
						?>
						<img 
							id='img<?php echo $img['id'];?>'
							class='elemento' 
							src='<?php echo $img['FI_documento'];?>' 
							onclick="document.getElementById('recuadro2').src='./argumentacionimagen.php?argumentacion=<?php echo $ID;?>&imagen=<?php echo $img['id'];?>'"
						>
						<?php
					}
					?>
					</div>

				<a title='añadir imagen' class="elemento"
				onclick="document.getElementById('recuadro2').src='./argumentacionimagen.php?argumentacion=<?php echo $ID;?>'"
				>+
				</a>
				</div>
				<div>
				<label for="resumen">Localizaciones<span>Acompañe su argumentación escrita con puntos localizados en el territorio</span></label>
					<div class='contenedordatos' id='contenedordelocalizaciones'>
					<?php
					/* Una misma argumentación puede contar con N localizaciones asociadas 
					 * que son cargada y modificadas dentro de un iframe 
					 * al cual se llama desde los botones a continuación*/
					$localizaciones = $datosargumentacion[$ID]['localizaciones'];
					foreach($localizaciones as $loc){
						?>
						<div 
							id='loc<?php echo $loc['id'];?>'
							class='elemento'
							onclick="document.getElementById('recuadro3').src='./argumentacionlocalizacion.php?argumentacion=<?php echo $ID;?>&localizacion=<?php echo $loc['id'];?>'"
						> 
							<?php echo $loc['latitud']."<br>".$loc['longitud'];?>
						</div>
						<?php
					}
					?>
					</div>
				<a title="añadir localización" class="elemento"
				onclick="document.getElementById('recuadro3').src='./argumentacionlocalizacion.php?argumentacion=<?php echo $ID;?>'"
				>+</a>
				</div>
				<div>
				<label for="argumentacion">Desarrollo<span>Desarrolle su argumentación <br>(puede escribirla en un procesador de texto y luego pegarla)</span></label>				
				<textarea name="argumentacion" type="text" class="required"><?php echo $datosargumentacion[$ID]['argumentacion'];?></textarea>							
				</div>
				</form>

			
			<?php			
			}
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
	echo "<br>tiempo de respuesta : " .$duration. " segundos";
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