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
$permiso = "colegios";
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso'");
$existe = mysqli_fetch_all($sql);
if (empty($existe) && $id_user != 1) {
    header('Location: permisos.php');
}

if (!empty($_POST)) {
   
    if (empty($_POST['nombre']) || empty($_POST['id_municipios'])) {
        $_SESSION['alert'] =  '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        El nombre del colegio y el municipio son obligatorios
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
    } else {
        $id_colegio = $_POST['id'];
        $nombre = $_POST['nombre'];
        $id_municipios = $_POST['id_municipios'];
        $result = 0;
        
        if (empty($id_colegio)) {
            $query = mysqli_query($conexion, "SELECT * FROM colegios WHERE nombre = '$nombre'");
            $result = mysqli_fetch_array($query);
            if ($result > 0) {
                $_SESSION['alert'] = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        El colegio ya existe
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
            } else {
                $query_insert = mysqli_query($conexion, "INSERT INTO colegios(nombre, id_municipios) VALUES ('$nombre', $id_municipios)");
                if ($query_insert) {
                    $_SESSION['alert'] = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        Colegio registrado
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
                } else {
                    $_SESSION['alert'] = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Error al registrar el colegio
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
                }
            }
        } else {
            $sql_update = mysqli_query($conexion, "UPDATE colegios SET nombre = '$nombre', id_municipios = $id_municipios WHERE id_colegios = $id_colegio");
            if ($sql_update) {
                $_SESSION['alert'] = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        Colegio modificado
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
            } else {
                $_SESSION['alert'] = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Error al modificar el colegio
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
            }
        }
    }
    mysqli_close($conexion);
   
    // Redirige después de procesar el formulario
    header("Location: colegios.php"); 
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
                                <label for="nombre" class="text-dark font-weight-bold">Nombre del Colegio</label>
                                <input type="text" required placeholder="Ingrese Nombre" name="nombre" id="nombre" class="form-control">
                                <input type="hidden"required name="id" id="id">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_municipios" class="text-dark font-weight-bold">Municipio</label>
                                <select name="id_municipios" id="id_municipios" class="form-control">
                                    <option value="">Escoja Municipio</option>
                                    <?php
                                    $query_municipios = mysqli_query($conexion, "SELECT * FROM municipios");
                                    while ($mun = mysqli_fetch_assoc($query_municipios)) {
                                        echo "<option value='{$mun['id_municipios']}'>{$mun['nombre']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12 mt-3">
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
                                <th>Municipio</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = mysqli_query($conexion, "SELECT c.id_colegios, c.nombre AS colegio, m.nombre AS municipio FROM colegios c INNER JOIN municipios m ON c.id_municipios = m.id_municipios");
                            $result = mysqli_num_rows($query);
                            if ($result > 0) {
                                while ($data = mysqli_fetch_assoc($query)) { ?>
                                    <tr>
                                        <td><?php echo $data['id_colegios']; ?></td>
                                        <td><?php echo $data['colegio']; ?></td>
                                        <td><?php echo $data['municipio']; ?></td>
                                        <td>
                                            <a href="#" onclick="editarColegio(<?php echo $data['id_colegios']; ?>)" class="btn btn-primary"><i class='fas fa-edit'></i></a>
                                            <form action="eliminar_colegio.php?id=<?php echo $data['id_colegios']; ?>" method="post" class="confirmar d-inline">
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
    function editarColegio(id) {
        const action = "editarColegio";
        $.ajax({
            url: 'ajax.php',
            type: 'GET',
            async: true,
            data: { editarColegio: action, id: id },
            success: function (response) {
                const datos = JSON.parse(response);
                $('#nombre').val(datos.nombre);
                $('#id_municipios').val(datos.id_municipios);
                $('#id').val(datos.id_colegios);
                $('#btnAccion').val('Modificar');
            },
            error: function (error) {
                console.log(error);
            }
        });
    }
</script>
<?php include_once "includes/footer.php"; ?>
