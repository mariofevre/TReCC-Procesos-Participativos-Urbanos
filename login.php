<?php 
/**
* login.php
*
* pagina de inicio y formulario de acceso a la plataorma.
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

include('./includes/conexion.php');
include("./includes/fechas.php");
include("./includes/cadenas.php");

session_destroy();

if(isset($_POST['loguear'])){
	$pass = md5($_POST['pass']);
	
	$query ="	
		SELECT `PARTICactores`.`id`,
		    `PARTICactores`.`pass`,
		    `PARTICactores`.`log`,
		    `PARTICactores`.`zz_activo`	    
		FROM `sigsao`.`PARTICactores`
		WHERE log='".$_POST['log']."'
	";
	$Consulta = mysql_query($query,$Conec1);
	echo mysql_error($Conec1);
		
	if(mysql_num_rows($Consulta)>0){
		if(mysql_result($Consulta, 0, 'zz_activo')=='1'){
			$passbase = mysql_result($Consulta, 0, 'pass');
			if($passbase==$pass){
				session_start();
		   		session_regenerate_id (true);
				$_SESSION['USUARIOID']= mysql_result($Consulta, 0, 'id');
				header("location: ./participacion.php");
			}else{
				$mensaje="La contraseña no coincide con el usuario solicitado.";
			}
		}else{
			$mensaje="La cuenta solicitada no ha sido activada aún.";
		}
	}else{
		$mensaje="El usuario requerido no se encuentra registrado.";
	}
}
?>
<html>
<head>
	<title>SIGSAO - Registro de Actores para los procesos participativos</title>
	<?php include("./includes/meta.php");?>
	<link href="./css/treccppu.css" rel="stylesheet" type="text/css">
</head>

<body>
	<div id="pageborde">
		<div id="page">
			<h1>Procesos Participativos Urbanos</h1>
			<p>
				Bienvenido a nuestra web de Procesos Participativos Urbanos.<br>
		    </p>
		    
			<p class='error'>
				<?php echo $mensaje;?><br>
		    </p>	
		    
		    <h2>Acceso de usuarios</h2>    
			<form action="login.php" method="post">
				<input name="loguear" type="hidden" value="loguear"/>
				<div>
				<label for="log">Usuario :</label>
				<input name="log" type="text" value="<?php echo $_POST['log'];?>"/>
				</div>	
				
				<div>
				<label for="pass">Contraseña :</label>
				<input name="pass" type="password" value=""/>
				</div>
					
				<div>
				<input type="submit" value="Ingresar"/>
				</div>			
				<div>
				<!--- <a class='boton' href="registro.php">Regístrese aquí para participar si no posee un usuario</a> --->	
				<a class='boton'>Regístrese aquí para participar si no posee un usuario</a><br>No se permite la generación de nuevos usuarios actualmente, el proceso participativo ha finalizado.
				</div>
				<div>
				<!--- <a class='boton' href='./registrorecuperar.php'>Olvidé mi contraseña</a> --->
				<a DISABLED class='boton' >Olvidé mi contraseña</a><br>No se permite la restitución de usuarios actualmente, el proceso participativo ha finalizado.
				</div>
				<div>
				<img style='float:left;height:50px;' src='./img/lgofirefox.png'>
				Este sitio está optimizado para el navegador mozilla Firefox.<br>
				Recomendamos utilizar software libre.<br>
				Descargue mozilla firefox <a xref='http://support.mozilla.org/es/products/firefox'>aqui</a>.
				</div>			
		</form>
		<p>	
			</p>
			<h2>Documentos publicados</h2>
				
			<p>
				<h3><a download='IA1.pdf' href='./publicaciones/IA1.pdf'>Informe de Avance 01.</a></h3> 
				Documento completo en baja resolución en formato pdf.<br>
		    </p>
			
			<p>
				<h3><a download='IA2.pdf' href='./publicaciones/IA2.pdf'>Informe de Avance 02.</a></h3> 
				Documento completo en baja resolución en formato pdf.<br>
				Se excluyeron los anexos 3 y 4 en tanto han quedado desactuaizados por las actividades de los talleres realizados.
		    </p>
			<p>
				<h3><a download='IA3.pdf' href='./publicaciones/IA3.pdf'>Informe de Avance 03.</a></h3> 
				Documento en baja resolución en formato pdf.<br>
				Se excluyeron los anexos en tanto han quedado desactuaizados por las actividades de los talleres realizados.
		    </p>					

			<p>
				<h3><a download='GEO.zip' href='./publicaciones/GEO.zip'>Compilado de información territorial generada.</a></h3> 
				Documento comprimido.<br>
				Se incluyó la base de datos ambiental generada.
			</p>	
			
			<p>
				<h3><a download='GEO_COT.zip' href='./publicaciones/GEO_COT.zip'>Mapas SIG del COT.</a></h3> 
				Documento comprimido.<br>
				Se incluyó la información cartógráfica de delimitación de zonas y una tabla resumen de cada zona.
		    </p>
		    
			<p>
				<h3><a target=_blank href='./publicaciones/COTinteractivo/COT_tabla.html'>Resumen COT on-line.</a></h3> 
				Aplicación web.<br>
				Se presta acceso de visualización al documento premliminar incluyendo carctarísticas y localización de cada zona.
		    </p>
		    		    		
			<p>
				<h3><a download='ZONAS_COT.zip' href='./publicaciones/ZONAS_COT.zip'>Mapas de Zonificación del COT.</a></h3> 
				Documento comprimido.<br>
				Se incluyeron los mapas de zonificación del COT en formato pdf.
		    </p>			    	    
		</div>
	</div>
	<?php
	include('./includes/pie.php');
	?>
</body>
</html>