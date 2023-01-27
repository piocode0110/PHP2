<?php include ("template/cabecera.php"); ?>

<?php
include("administrador/configuracion/bd.php");
$sentenciaSQL=$conexion->prepare("SELECT * FROM proyectos");
$sentenciaSQL->execute();
$listaProyectos=$sentenciaSQL->fetchAll(PDO::FETCH_ASSOC);
?>


<?php foreach($listaProyectos as $proyecto) {?>

<div class="col-md-3">
    <!--b4-card-columns -->
    <div class="card">
        <img class="card-img-top" src="./img/<?php echo $proyecto['imagen'];?>" alt="">
        <div class="card-body">
            <h4 class="card-title"><?php echo $proyecto['nombre'];?> </h4>
            <a name="" id="" class="btn btn-primary" href="https://unipaz.edu.co/escuela_ingenieria_produccion.html" role="button" target="_blank" >Ver más</a>
        </div>
    </div>  
</div>

<?php }?>


<?php include ("template/pie.php"); ?>