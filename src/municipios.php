<?php
session_start();
header("Cache-Control: no-cache, must-revalidate");
if (!isset($_SESSION['idUser'])) {
    // Redirigir al usuario al index si no está definido
    header("Location: index.php");
    exit;
}
include "../conexion.php";
$id_user = $_SESSION['idUser'];
$permiso = "municipios";
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso'");
$existe = mysqli_fetch_all($sql);
if (empty($existe) && $id_user != 1) {
    header('Location: permisos.php');
}

if (!empty($_POST)) {
    
    if (empty($_POST['nombre'])) {
        $_SESSION['alert'] = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        Todo los campos son obligatorio
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
    } else {
        $id = $_POST['id'];
        $nombre = $_POST['nombre'];
        $result = 0;
        if (empty($id)) {
            $query = mysqli_query($conexion, "SELECT * FROM municipios WHERE nombre = '$nombre'");
            $result = mysqli_fetch_array($query);
            if ($result > 0) {
                $_SESSION['alert'] =  '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        El municipio ya existe
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
            } else {
                $query_insert = mysqli_query($conexion, "INSERT INTO municipios(nombre) values ('$nombre')");
                if ($query_insert) {
                    $_SESSION['alert'] =  '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        Municipio registrado
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
                } else {
                    $_SESSION['alert'] =  '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Error al registrar
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
                }
            }
        }else{
            $sql_update = mysqli_query($conexion, "UPDATE municipios SET nombre = '$nombre' WHERE id_municipios = $id");
            if ($sql_update) {
                $_SESSION['alert'] = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        Municipio Modificado
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
            } else {
                $_SESSION['alert'] =  '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Error al modificar
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
            }
        }
    }
    mysqli_close($conexion);
    // Redirige después de procesar el formulario
    header("Location: municipios.php"); 
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
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="nombre"required class="text-dark font-weight-bold">Nombre</label>
                                <input type="text" required placeholder="Ingrese Nombre del Municipio" name="nombre" id="nombre" class="form-control">
                                <input type="hidden"required id="id" name="id">
                            </div>
                        </div>
                        <div class="col-md-4 mt-3">
                            <input type="submit"required value="Registrar" class="btn btn-primary" id="btnAccion">
                            <input type="button"required value="Nuevo" class="btn btn-success" id="btnNuevo" onclick="limpiar()">
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
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            include "../conexion.php";

                            $query = mysqli_query($conexion, "SELECT * FROM municipios");
                            $result = mysqli_num_rows($query);
                            if ($result > 0) {
                                while ($data = mysqli_fetch_assoc($query)) { ?>
                                    <tr>
                                        <td><?php echo $data['id_municipios']; ?></td>
                                        <td><?php echo $data['nombre']; ?></td>
                                        <td>
                                            <a href="#" onclick="editarMunicipio(<?php echo $data['id_municipios']; ?>)" class="btn btn-primary"><i class='fas fa-edit'></i></a>
                                            <form action="eliminar_municipio.php?id=<?php echo $data['id_municipios']; ?>" method="post" class="confirmar d-inline">
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
    
    function editarMunicipio(id) {
    const action = "editarMunicipio";
    $.ajax({
        url: 'ajax.php',
        type: 'GET',
        async: true,
        data: {
            editarMunicipio: action,
            id: id
        },
        success: function (response) {
            const datos = JSON.parse(response);
            console.log(datos)
            $('#nombre').val(datos.nombre);
            $('#id').val(datos.id_municipios);
            $('#btnAccion').val('Modificar');
        },
        error: function (error) {
            console.log(error);

        }
    });
}

</script>

<?php include_once "includes/footer.php"; ?>  
