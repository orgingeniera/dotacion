<?php
session_start();
header("Cache-Control: no-cache, must-revalidate");
include "../conexion.php";

$id_user = $_SESSION['idUser'];
$permiso = "categorias";
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso'");
$existe = mysqli_fetch_all($sql);
if (empty($existe) && $id_user != 1) {
    header('Location: permisos.php');
}

if (!empty($_POST)) {
    if (empty($_POST['nombre'])) {
        $_SESSION['alert'] =  '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        El nombre de la categoría es obligatorio
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
    } else {
        $id_categoria = $_POST['id'];
        $nombre = $_POST['nombre'];
        $result = 0;
        
        if (empty($id_categoria)) {
            $query = mysqli_query($conexion, "SELECT * FROM categoria WHERE nombre = '$nombre'");
            $result = mysqli_fetch_array($query);
            if ($result > 0) {
                $_SESSION['alert'] =   '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        La categoría ya existe
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
            } else {
                $query_insert = mysqli_query($conexion, "INSERT INTO categoria(nombre) VALUES ('$nombre')");
                if ($query_insert) {
                    $_SESSION['alert'] =  '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        Categoría registrada
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
                } else {
                    $_SESSION['alert'] =  '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Error al registrar la categoría
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
                }
            }
        } else {
            $sql_update = mysqli_query($conexion, "UPDATE categoria SET nombre = '$nombre' WHERE id_categoria = $id_categoria");
            if ($sql_update) {
                $_SESSION['alert'] = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        Categoría modificada
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
            } else {
                $_SESSION['alert'] =  '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Error al modificar la categoría
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
            }
        }
    }
    mysqli_close($conexion);
    // Redirige después de procesar el formulario
    header("Location: categorias.php"); 
    exit;

}
include_once "includes/header.php";
?>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
            <?php 
                if (isset($_SESSION['alert'])) {
                    echo $_SESSION['alert'];
                    unset($_SESSION['alert']);
                }
                ?>
                <form action="" method="post" autocomplete="off" id="formulario">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre" class="text-dark font-weight-bold">Nombre de la Categoría</label>
                                <input type="text" required placeholder="Ingrese Nombre" name="nombre" id="nombre" class="form-control">
                                <input type="hidden" required name="id" id="id">
                            </div>
                        </div>
                        <div class="col-md-6 mt-3">
                            <input type="submit" required value="Registrar" class="btn btn-primary" id="btnAccion">
                            <input type="button" required value="Nuevo" class="btn btn-success" id="btnNuevo" onclick="limpiar()">
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="tbl">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            include "../conexion.php";

                            $query = mysqli_query($conexion, "SELECT * FROM categoria");
                            $result = mysqli_num_rows($query);
                            if ($result > 0) {
                                while ($data = mysqli_fetch_assoc($query)) { ?>
                                    <tr>
                                        <td><?php echo $data['id_categoria']; ?></td>
                                        <td><?php echo $data['nombre']; ?></td>
                                        <td>
                                            <a href="#" onclick="editarCategoria(<?php echo $data['id_categoria']; ?>)" class="btn btn-primary"><i class='fas fa-edit'></i></a>
                                            <form action="eliminar_categoria.php?id=<?php echo $data['id_categoria']; ?>" method="post" class="confirmar d-inline">
                                                <button class="btn btn-danger" type="submit"><i class='fas fa-trash-alt'></i> </button>
                                            </form>
                                        </td>
                                    </tr>
                            <?php }
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function editarCategoria(id) {
    const action = "editarCategoria";
     $.ajax({
         url: 'ajax.php',
         type: 'GET',
         async: true,
         data: {
            editarCategoria: action,
             id: id
         },
         success: function (response) {
             const datos = JSON.parse(response);
             console.log(datos)
             $('#nombre').val(datos.nombre);           
             $('#id').val(datos.id_categoria);
             $('#btnAccion').val('Modificar');
         },
         error: function (error) {
             console.log(error);
 
         }
     });
 }
</script>
<?php include_once "includes/footer.php"; ?>
