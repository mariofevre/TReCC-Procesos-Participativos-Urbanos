<?php
/**
* argumentacionimagen.php
*
* argumentacionimagen.php se incorpora en la carpeta raiz 
* esta aplicación está diseñada para ser cargada dentro de in iframe en la ventana principal para la carga y edicion de argumentaicones.
* identificada una argumentación permite guardar un archivo de imagen y generar un registro asociado. 
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
	 		width:90%;
	 	}
	 	
	 	input[type='submit']{
	 		width:80px;
	 		display:block;
	 		margin:2px;
	 		margin-top:8px;
	 	}	 	
	 	iframe{
	 		width:160px;
	 		height: 105px;
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
	 img{
	 	max-width:210px;
	 	max-height:140px;	
	 	margin:5px; 	
	 }
	 </style>
</head>
<body>
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
	    `PARTICargumentaciones`.`zz_AUTOFECHACREACION`
	FROM 
		`sigsao`.`PARTICargumentaciones`
	WHERE 
		`id`='".$ARGUMENTACION."'
	
	";
	$Consulta = mysql_query($query,$Conec1);
	echo mysql_error();
	
	/* verifica el permiso del usuario para su acceso */
	$autor = mysql_result($Consulta,0,'id_p_PARTICactores_id');
	if($autor == $UsuarioI){
		
		/* si el formulario de reingreso así lo definió crea un nuevo registro de imagen */		
		if($ACCION=="cargar"){
			
			$query="
				INSERT INTO 
					`sigsao`.`PARTICargumentacionesIMG`
				SET 							
					`id_p_PARTICargumentaciones`='".$ARGUMENTACION."',
					`zz_AUTOUSUARIOCREACION`='".$UsuarioI."',
					descripcion='".$_POST['descripcion']."'
			";
			$Consulta = mysql_query($query,$Conec1);
			$NID=mysql_insert_id($Conec1);
			$Tabla='PARTICargumentacionesIMG';
			
			/* creado enl registro se llama a cargaarchivos.php, que ejecuta y verifica la correcta crga del archivo en cuestión*/
			include("./cargaarchivos.php");
			
			echo "<p>imagen guardada:</p><img src='$pathI'>";
			?>
			
			<script type='text/javascript'>
			// el elemento creado será representado dentro de un div en la ventana principal
			var _nuevaimg = document.createElement('img');
			_nuevaimg.setAttribute('class', 'elemento');
			_nuevaimg.setAttribute('id', 'img<?php echo $NID;?>');
			_nuevaimg.setAttribute('src', '<?php echo $pathI;?>');			
			window.parent.document.getElementById('contenedordeimagenes').appendChild(_nuevaimg);
			</script>
			<?php
		}elseif($ACCION=="borrar"){
			/* si el formulario de reingreso así lo definió modifica el registro como borrado */			
			$query="
				UPDATE 
					`sigsao`.`PARTICargumentacionesIMG`
				SET 							
					`zz_borrada`='1'
				WHERE
					id = ".$_POST['imagen']."
					AND id_p_PARTICargumentaciones='".$ARGUMENTACION."'
					AND `zz_AUTOUSUARIOCREACION`='".$UsuarioI."'
			";
			$Consulta = mysql_query($query,$Conec1);
			echo mysql_error($Conec1);
			
			echo "<p>imagen borrada:</p>";			
			?>
			<script type='text/javascript'>			
			// el elemento div que represanta esta imagen en la ventana principal deja de ser mostrado //
			window.parent.document.getElementById('img<?php echo $_POST['imagen'];?>').style.display='none';
			</script>
			<?php
		
		}elseif(isset($_GET['imagen'])){
		
			/* si no hay acciones definidas en el formulario de reingreso se consulta el registro para mostrar su información */
			$query="
			SELECT 
			`PARTICargumentacionesIMG`.`id`,
			`PARTICargumentacionesIMG`.`id_p_PARTICargumentaciones`,
			`PARTICargumentacionesIMG`.`FI_documento`,
			`PARTICargumentacionesIMG`.`zz_AUTOUSUARIOCREACION`,
			`PARTICargumentacionesIMG`.`descripcion`
			FROM
			`sigsao`.`PARTICargumentacionesIMG`
			WHERE 
				`id`='".$_GET['imagen']."' 
				AND `id_p_PARTICargumentaciones`='".$ARGUMENTACION."' 
			";
			$Consulta = mysql_query($query,$Conec1);
			echo mysql_error($Conec1);
			$archivo=mysql_result($Consulta,0,'FI_documento');
			echo "<img src='$archivo'>";
			echo "<p>".mysql_result($Consulta,0,'descripcion')."</p>";
			?>
			<form method='POST' action='./argumentacionimagen.php' enctype='multipart/form-data'>
			<input type='hidden' value='borrar' name='accion'>
			<input type='hidden' value='<?php echo $ARGUMENTACION;?>' name='argumentacion'>
			<input type='hidden' value='<?php echo $_GET['imagen'];?>' name='imagen'>
			<input type='button' value='borrar imagen' onclick="this.nextSibling.nextSibling.style.display='block';">
			<input style='display:none;' type='button' value='confirmo borrar' onclick='this.parentNode.submit();'>
			</form>
			<?php
			/*echo $query;*/
		}else{
			/* si no hay registros de imagen definidos se muestra un formulario de reingreso para crear un nuevo registro para la argumentación solicitada */			
			echo "<p>Agregar imagen:</p>";
			echo "<form method='POST' action='./argumentacionimagen.php' enctype='multipart/form-data'>";
				echo "<input type='hidden' name='accion' value='cargar'>";	
				echo "<input type='hidden' name='argumentacion' value='".$ARGUMENTACION."'>";		
				echo "<input type='hidden' name='archivo_FI_path' value='./documentos/p_1/argumentaciones/imagenes/'>";
	
				echo "<label>descripción de la imagen:</label><input type='text' name='descripcion'>";
				
				echo "<br><label>seleccionar archivo:</label><input name='archivo_F' type='file'";
				?>
				onchange="this.nextSibling.style.display='block';"
				<?php			
				echo ">";
				echo "<input style='display:none' type='submit' value='guardar'>";			
			echo "</form>";			
		}
		
	}else{
		echo "error e la identificación de permisos locales";
	}
}else{
	echo "carga de imágenes";
}



?>
</body>