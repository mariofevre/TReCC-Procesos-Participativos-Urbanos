<?php
/**
* agrega.php
*
* instrucci�n para editar base de datos utilizando las variables de llamada}.
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
* y/o modificarlo bajo los t�rminos de la "GNU AFero General Public License version 3" 
* publicada por la Free Software Foundation
* 
* Este archivo es distribuido por si mismo y dentro de sus proyectos 
* con el objetivo de ser �til, eficiente, predecible y transparente
* pero SIN NIGUNA GARANT�A; sin siquiera la garant�a impl�cita de
* CAPACIDAD DE MERCANTILIZACI�N o utilidad para un prop�sito particular.
* Consulte la "GNU General Public License" para m�s detalles.
* 
* Si usted no cuenta con una copia de dicha licencia puede encontrarla aqu�: <http://www.gnu.org/licenses/>.
*/

/* verificaci�n de seguridad */
include('./includes/conexion.php');
include('./includes/conexionusuario.php');

/* funciones frecuentes */
include("./includes/fechas.php");
include("./includes/cadenas.php");

$UsuarioI = $_SESSION['USUARIOID'];

if($UsuarioI==""){header('Location: ./login.php');}
	
	
	foreach ($_POST as $key => $valor) { // COntrol de seguridad no permite agregar registros con formularios con variables post id_p_paneles... que no coincidan con el panel actual. Se asume una accion maliciosa.
	    $sub= substr($key, 0, 12);
		if($sub=='id_p_paneles'&&$valor!=$PanelI){
			echo $key.": ".$valor."<br>";
		//header('Location: ./mensaje.php?msj=error incongrunecia entre los datos enviados y el panel activo');
		break;
		}
	}
	
	foreach($_POST as $k => $v){// estas variables son pasadas por als aplicaciones comunes manteniendose.
		if(substr($k,0,5)=='PASAR'){
			$PASAR[$k]=$v;
		}
	}
	

	$Id_contrato = $_POST["contrato"];
	$Tabla = $_POST["tabla"];
	$Id = $_POST["id"];
	$Accion = $_POST["accion"];	
	$Origenid = $_POST["Origenid"];      /* variable para cuando la entrada agregada responde a otra que debe ser cerrada (comunicaciones)*/
	$Paraorigen = $_POST["Paraorigen"];	/* variable para incorporar al orgigenid */	
	$Fechaa = $_POST["fechaemisionref_a"];
	$Fecham = $_POST["fechaemisionref_m"];
	$Fechad = $_POST["fechaemisionref_d"];	
	$Fecha = $Fechaa."-".$Fecham."-".$Fechad;
	$Tablahermana = $_POST["tablahermana"];
	$Idhermana = $_POST["idhermana"];		
	$Salida = $_POST['salida'];
	$Salidaid = $_POST['salidaid'];	
	$Salidatabla = $_POST['salidatabla'];		
	$PanelI = $_SESSION['panelcontrol']->PANELI;	
	$Base = $_SESSION['panelcontrol']->DATABASE_NAME;
	$Index = $_SESSION['panelcontrol']->INDEX;		
	$HOY = date("Y-m-d");
	$HOYd = date("d");
	$HOYm = date("m");
	$HOYa = date("Y");
	$Publicacion .= "<br><br>";
 	$result = mysql_query('SHOW FULL COLUMNS FROM `'.$Tabla.'`',$Conec1);
	print_r($_POST);
    if (mysql_num_rows($result) > 0) {
        while ($row = mysql_fetch_assoc($result)) {
        	
        	$campo = $row['Field'];
			$datomas = $_POST[$campo];
			$Type = substr($row['Type'],0,3);
			$Typolink = substr($row['Field'],0,4);
			$Typo = substr($row['Field'],0,3);			
			
			/* para tablas padre */
			if($Typolink == "id_p"){
				$Publicacion .= "<br>padre en: ".$campo. "->".$datomas;
				if($datomas == "n"){
					$Publicacion .= "<br>n solicita nuevo item";
					$Baselink = substr($row['Field'],0,6);
					if($Baselink != "id_p_B")
					{
						$Publicacion .= "<br>padre interno";
						$o = explode("_", $row['Field']);
						$basepadre = $Base;
						$tablapadre = $o[2];
						$campopadre = $o[4];
						if($campopadre==''){
								$_SESSION['DEBUG']['mensajes'][] = "campo padre: indefinido, explorando...";
								$padre = mysql_query('SHOW FULL COLUMNS FROM `'.$tablapadre.'`',$Conec1);
								$seteado='no';
								While($rp= mysql_fetch_assoc($padre)){
									if($seteado=='no'&&$rp['Field']!='id'){
										$seteado='si';
										$campopadre = $rp['Field'];
									}
								}
								$_SESSION['DEBUG']['mensajes'][] = "campo asignado: $campopadre";
							}
						
						$extra = "";
						
						$padre = $basepadre . "." . $tablapadre;
						$campocont = $campo."_n";
						$nuevocontenido=$_POST[$campocont];
						
						
						//Verifica no repetici�n en el nombre para tablas espe�cificas, ej: grupos
						$query="	
							SELECT 
								* 
							FROM
								 $tablapadre
								 WHERE $campopadre='$nuevocontenido' 
						";
						$existe=mysql_query($query,$Conec1);
						$Publicacion .= mysql_error($Conec1);
						if(mysql_num_rows($existe)>0){
							$Publicacion .= "<br>nombre de item existente, creaci�n anulada";
							$Publicacion .= $query;
							$Idnuevo=mysql_result($existe,0,'id');
							$Publicacion .= "<br>id reciclado: ".$Idnuevo;
							$datomas = $Idnuevo;
						}else{						
							$query = "INSERT INTO $tablapadre SET $campopadre='$nuevocontenido'";
							mysql_query($query,$Conec1);
							$Publicacion .= mysql_error($Conec1);
							$Idnuevo = mysql_insert_id($Conec1);
							$Publicacion .= "nuevo id: ".$Idnuevo;
							$datomas = $Idnuevo;
							$Publicacion .= "agregar�: ".$datomas;
						}
					}
				}
				$Publicacion .= "<br>otro;". " - " . $row['Field']. " - " . $datomas."<br>";
				if($datomas != ""){
					$Datos .= " `" . $campo . "`='" .  $datomas . "',";
				}
					
			}elseif($Typo == 'zz_' && $campo == 'zz_AUTOFECHAMODIF'){
				$Datos .= " `" . $campo . "`='" .  $HOY . "',";
			}elseif($Typo == 'zz_'&& $campo == 'zz_AUTOFECHACREACION'){
				$Datos .= " `" . $campo . "`='" .  $HOY . "',"; /* este campo nunca se debe modificar, debe ser una impresi�n del momento de creaci�n del registro */
			}elseif($Typo == 'zz_'&& $campo == 'zz_AUTOPANEL'){
				$Datos .= " `" . $campo . "`='" .  $PanelI . "',"; /* este campo nunca se debe modificar, debe ser una impresi�n del momento de creaci�n del registro */
			}elseif($Typo == 'FI_'){
				if(isset($_FILES['archivo_F'])){
					$imagenid = $_FILES['archivo_F']['name'];	
					$nombre = isset($_POST['archivo_FI_nombre'])? $_POST['archivo_FI_nombre'] : $Tabla."[NID]"; /* el texto [NID] se reemplazar� por la uneva id */
					$path = $_POST['archivo_FI_path'];	
					
					/* verificar y crear directorio */
						$Publicacion.="analizando ruta<br>";
						$carpetas= explode("/",$path);
						$rutaacumulada="";
						
						foreach($carpetas as $valor){
							$Publicacion.="instancia: $valor<br>";
								
							$rutaacumulada.=$valor."/";
							echo $rutaacumulada."<br>";
							if(!file_exists($rutaacumulada)){
								$Publicacion.="la carpeta no existe!<BR>";
								if($valor!=''){
									$Publicacion.="creando: $rutaacumulada<br>";
								    mkdir($rutaacumulada, 0777, true);
									chmod($rutaacumulada, 0777); 
								}
							
							}
						}
					/* FIN verificar y crear directorio */	
					echo "<br>".$imagenid."<br>";
						$b = explode(".",$imagenid);
						$ext = $b[(count($b)-1)];
					if(
						$ext=="JPG"||$ext=="jpg"||$ext=="png"||$ext=="PNG"||$ext=="tif"||$ext=="TIF"||
						$ext=="bmp"||$ext=="BMP"||$ext=="gif"||$ext=="GIF"||
						$ext=="pdf"||$ext=="PDF"||
						$ext=="xls"||$ext=="XLS"||
						$ext=="ods"||$ext=="ODS"||
						$ext=="doc"||$ext=="DOC"||
						$ext=="odt"||$ext=="ODT"
					){
						$nombre = str_replace('[NID]', $Id, $nombre);
						$cod = cadenaArchivo(10); /* define un c�digo que evita la predictividad de los documentos ante b�squedas maliciosas */
						$pathI = $path.$nombre."_".$cod.".".$ext;
						$Publicacion .= "guardado en".$pathI."<br>";
						
						if (!copy($_FILES['archivo_F']['tmp_name'], $pathI)) {
						    $Publicacion .= "Error al copiar $pathI...\n";
						}else{
						$Publicacion .= "imagen guardada";
						$datomas = $pathI;
						$Datos .= " `" . $campo . "`='" .  $datomas . "',";
						}
					}else{
						$Publicacion .= "solo se aceptan los formatos: jpg, png, tif, gif, bmp, pdf, xls, ods, doc, odt";
						$imagenid='';
						print_r($_FILES); 
					}
					
					
				}
			}elseif($row['Field'] != "id"){
				if ($Type == "tex"){
					$datomas = str_replace("<br />","",$_POST[$campo]);
						if($datomas != ""){
							$Datos .= " `" . $campo . "`='" .  $datomas . "',";
						}
				}elseif($Type == "dat"){
				$Publicacion .= "<br>fecha;". " - " . $row['Field']. " - " . $datomas;
					$campo_a = $campo . "_a";
					$campo_m = $campo . "_m";
					$campo_d = $campo . "_d";
					
					$contenidoa = $_POST[$campo_a];
					$contenidom = $_POST[$campo_m];
					$contenidod = $_POST[$campo_d];
					
					/* ojo este comentario tal vez gener conflicto
					if($contenidoa == '' || $contenidom == '' ||$contenidod == ''){
						if($contenidod == ''){$contenidod =$HOYd;}
						if($contenidom == ''){$contenidom =$HOYm;}
						if($contenidoa == ''){$contenidoa =$HOYa;}
					}
					*/
					
					$datomas = $contenidoa . "-" . $contenidom . "-" . $contenidod;
			
						if($datomas != "--"){
							$Datos .= " `" . $campo . "`='" .  $datomas . "',";
						}
				}else{
						if($datomas != ""){
							$Datos .= " `" . $campo . "`='" .  $datomas . "',";
						}
				}

			
        	}
		}
	}


$Datos = substr($Datos,0,(strlen($Datos)-1));


$Publicacion .= "<br>id base: ";
$Publicacion .= $Id_contrato;
$Publicacion .= "<br>tabla: ";
$Publicacion .= $Tabla;
$Publicacion .= "<br>id: ";
$Publicacion .= $Id;
$Publicacion .= "<br>accion: ";
$Publicacion .= $Accion;
$Publicacion .= "<br>";
$Publicacion .= "<br>datos: ";
$Publicacion .= $Datos;
$Publicacion .= "<br>";
$Publicacion .= "<br>";


$query="INSERT INTO $Tabla SET $Datos";
mysql_query($query,$Conec1);
$Publicacion .= $query;
$Id = mysql_insert_id($Conec1);
$NID = $Id;
$Publicacion .= $Id . "<br>";

$Publicacion .= "error mysql: ". mysql_error($_SESSION['panelcontrol']->Conec1);


if(isset($_POST['__NID_valor'])){
	echo "<script type='text/javascript'>";
	echo "parent.document.getElementById('".$_POST['__NID_valor']."').value = '".$NID."';";
	echo "parent.document.getElementById('".$_POST['__NID_valor']."').parentNode.submit();";	
	echo "</script>";
}

if($Salidatabla != ""){$Salidatabla = $Tabla;}
if($Salidaid == ""){$Salidaid = $Id;}

$Publicacion .= $Salida;
$Publicacion .=".php?tabla=";
$Publicacion .= $Tabla;
$Publicacion .="&id=";
$Publicacion .= $Salidaid;

$_SESSION['DEBUG']['mensajes'][]=$Publicacion;


if($Salida!='__ALTO'){
	if($Salida!=''){
		$Publicacion .= "saliendo...";
		echo $Publicacion;
		$cadenapasar='';
		foreach($PASAR as $k => $v){
			$cadenapasar.='&'.substr($k,5).'='.$v;
		}	
		$location="./".$Salida.".php?tabla=".$Tabla."&id=".$Salidaid.$cadenapasar;
		
		if($Tabla=='comunicaciones'){$location="./agrega_fcom.php?tabla=".$Tabla."&accion=cambia&salida=comunicaciones&id=".$Id;}
		?><SCRIPT LANGUAGE="javascript">location.href = "<?php echo $location;?>";</SCRIPT><?php  	
		
	}else{
		?>
			<button onclick="window.close();">cerrar esta ventana</button>
		<?php  
	}
}


?>
