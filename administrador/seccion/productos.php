<?php include("../template/cabecera.php");?>

<?php

// si hay algo en txtID ? entonces txtID es igual a $txtID de lo contrario vacio ""
$txtID=(isset($_POST['txtID']))? $_POST['txtID']:"";
$txtNombre=(isset($_POST['txtNombre']))? $_POST['txtNombre']:"";
$txtImagen=(isset($_FILES['txtImagen']['name']))? $_FILES['txtImagen']['name']:"";
$Accion=(isset($_POST['accion']))? $_POST['accion']:"";


include("../configuracion/bd.php");


switch($Accion){

    case "Agregar":
        $sentenciaSQL=$conexion->prepare("INSERT INTO proyectos (nombre, imagen) VALUES (:nombre,:imagen);");
        $sentenciaSQL->bindParam(':nombre',$txtNombre);

        $fecha=new DateTime();
        $nombreArchivo=($txtImagen!="")?$fecha->getTimestamp()."_".$_FILES["txtImagen"]["name"]:"imagen.jpg";

        $tmpImagen=$_FILES["txtImagen"]["tmp_name"];

        if($tmpImagen!=""){

            move_uploaded_file($tmpImagen,"../../img/".$nombreArchivo);
        }

        $sentenciaSQL->bindParam(':imagen',$nombreArchivo);
        $sentenciaSQL->execute();

        header("Location:productos.php");
        break;

    case "Modificar":

        $sentenciaSQL=$conexion->prepare("UPDATE proyectos SET nombre=:nombre WHERE id=:id");
        $sentenciaSQL->bindParam(':nombre',$txtNombre);
        $sentenciaSQL->bindParam(':id',$txtID);
        $sentenciaSQL->execute();    

        if($txtImagen!=""){

            $fecha=new DateTime();
            $nombreArchivo=($txtImagen!="")?$fecha->getTimestamp()."_".$_FILES["txtImagen"]["name"]:"imagen.jpg";
            $tmpImagen=$_FILES["txtImagen"]["tmp_name"];  
            
            move_uploaded_file($tmpImagen,"../../img/".$nombreArchivo);

            $sentenciaSQL=$conexion->prepare("SELECT imagen FROM proyectos WHERE id=:id");
            $sentenciaSQL->bindParam(':id',$txtID);
            $sentenciaSQL->execute();
            $proyecto=$sentenciaSQL->fetch(PDO::FETCH_LAZY);
    
            if (isset($proyecto["imagen"]) && ($proyecto["imagen"]!="imagen.jpg") ) {
                
                if(file_exists("../../img/".$proyecto["imagen"])){
                    unlink("../../img/".$proyecto["imagen"]);
                }
            }

            $sentenciaSQL=$conexion->prepare("UPDATE proyectos SET imagen=:imagen WHERE id=:id");
            $sentenciaSQL->bindParam(':imagen',$nombreArchivo);
            $sentenciaSQL->bindParam(':id',$txtID);
            $sentenciaSQL->execute();    
        }
        header("Location:productos.php");
        break;

    case 'Cancelar':
        header("Location:productos.php");
        break;    


    case "Seleccionar":

        $sentenciaSQL=$conexion->prepare("SELECT * FROM proyectos WHERE id=:id");
        $sentenciaSQL->bindParam(':id',$txtID);
        $sentenciaSQL->execute();
        $proyecto=$sentenciaSQL->fetch(PDO::FETCH_LAZY);

        $txtNombre=$proyecto['nombre'];
        $txtImagen=$proyecto['imagen'];

        //echo "Presionando botón Seleccionar";
        break;

    case "Borrar":

        $sentenciaSQL=$conexion->prepare("SELECT imagen FROM proyectos WHERE id=:id");
        $sentenciaSQL->bindParam(':id',$txtID);
        $sentenciaSQL->execute();
        $proyecto=$sentenciaSQL->fetch(PDO::FETCH_LAZY);

        if (isset($proyecto["imagen"]) && ($proyecto["imagen"]!="imagen.jpg") ) {
            
            if(file_exists("../../img/".$proyecto["imagen"])){
                unlink("../../img/".$proyecto["imagen"]);
            }
        }
        
        $sentenciaSQL=$conexion->prepare("DELETE FROM proyectos WHERE id=:id");
        $sentenciaSQL->bindParam(':id',$txtID);
        $sentenciaSQL->execute();
        
        header("Location:productos.php");
        break;
}
$sentenciaSQL=$conexion->prepare("SELECT * FROM proyectos");
$sentenciaSQL->execute();
$listaProyectos=$sentenciaSQL->fetchAll(PDO::FETCH_ASSOC);


?>



<div class="col-md-4">

    <div class="card">
        <div class="card-header">
            Datos de Proyectos
        </div>
        <div class="card-body">

        <form method="POST" enctype="multipart/form-data">

            <div class = "form-group">
            <label for="txtID">ID:</label>
            <input type="text" required readonly class="form-control" value="<?php echo $txtID; ?>" name="txtID" id="txtID" placeholder="ID">
            </div>

            <div class = "form-group">
            <label for="txtNombre">Nombre: </label>
            <input type="text" required class="form-control" value="<?php echo $txtNombre; ?>" name="txtNombre" id="txtNombre" placeholder="Nombre del Proyecto">
            </div>

            <div class = "form-group">
            <label for="txtImagen">Imagen: </label>
             
            <br/>
            <?php if($txtImagen!=""){ ?>

                <img class="img-thumbnail rounded" src="../../img/<?php echo $txtImagen;?>" width="50" alt="" srcset="">
                

            <?php } ?>

            <input type="file"  class="form-control" name="txtImagen" id="txtImagen" placeholder="Nombre del Proyecto">
            </div>

            <div class="btn-group" role="group" aria-label="">
                <button type="submit" name="accion" <?php echo ($Accion=="Seleccionar")?"disabled":""; ?> value="Agregar" class="btn btn-success">Agregar</button>
                <button type="submit" name="accion" <?php echo ($Accion!="Seleccionar")?"disabled":""; ?> value="Modificar" class="btn btn-warning">Modificar</button>
                <button type="submit" name="accion" <?php echo ($Accion!="Seleccionar")?"disabled":""; ?> value="Cancelar" class="btn btn-info">Cancelar</button>
            </div>
        </form>

        </div>
        <div class="card-footer text-muted">
            EIPR
        </div>
    </div>    
        
</div>
<div class="col-md-8">

<div class="table-responsive">
    <table class="table table-primary">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Nombre</th>
                <th scope="col">Imagen</th>
                <th scope="col">Acciones</th>
            </tr>
        </thead>
        <tbody>

            <?php foreach($listaProyectos as $proyecto) { ?>
                <tr class="">
                    <td scope="row"><?php echo $proyecto['id']; ?></td>
                    <td><?php echo $proyecto['nombre']; ?></td>
                    <td>
                        
                        <img class="img-thumbnail rounded" src="../../img/<?php echo $proyecto['imagen'];?>" width="50" alt=""  srcset="" > 
                        <!--<embed src="../../img/<?php echo $proyecto['imagen'];?>" width="30%" alt="25%" > OJO PARA PDF-->
                        
                    
                    </td>

                    <td>
                        <form method="post">
                            <input type="hidden" name="txtID" id="txtID" value="<?php echo $proyecto['id']; ?>"/>
                            <input type="submit" name="accion" value="Seleccionar" class="btn btn-primary"/>
                            <input type="submit" name="accion" value="Borrar" class="btn btn-danger"/>

                        </form>
                    
                    </td>     
                </tr>
            <?php } ?>

        </tbody>
    </table>
</div>


</div>

<?php include("../template/pie.php");?>