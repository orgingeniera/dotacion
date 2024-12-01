<?php
session_start();
if (!isset($_SESSION['idUser'])) {
    // Redirigir al usuario al index si no está definido
    header("Location: index.php");
    exit;
}
require_once "../conexion.php";
$id_user = $_SESSION['idUser'];

// Obtener usuarios para el select basado en el tipo de sesión
if ($_SESSION['tipo'] == 1) {
    // Administrador: mostrar todos los usuarios
    $query_users = mysqli_query($conexion, "SELECT idusuario, nombre FROM usuario");
} else {
    // Usuario estándar: mostrar solo su propio usuario
    $query_users = mysqli_query($conexion, "SELECT idusuario, nombre FROM usuario WHERE idusuario = $id_user");
}
// Filtrar datos según los parámetros
$where = '';
if (isset($_POST['buscar'])) {
    $usuario = $_POST['usuario'] ?? '';
    $fecha_inicio = $_POST['fecha_inicio'] ?? '';
    $fecha_final = $_POST['fecha_final'] ?? '';

    if (!empty($usuario)) {
        $where .= " AND v.id_usuario = $usuario";
    }
    if (!empty($fecha_inicio) && !empty($fecha_final)) {
        $where .= " AND v.fecha BETWEEN '$fecha_inicio' AND '$fecha_final'";
    }
}
/*
$permiso = "ventas";
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso'");
$existe = mysqli_fetch_all($sql);
if (empty($existe) && $id_user != 1) {
    header('Location: permisos.php');
}*/

if($_SESSION['tipo'] == 1){
    $query = mysqli_query($conexion, "SELECT v.*, c.idcliente, c.nombre, c.documento 
    FROM ventas v 
    INNER JOIN cliente c ON v.id_cliente = c.idcliente
    WHERE 1=1 $where");
    //$query = mysqli_query($conexion, "SELECT v.*, c.idcliente, c.nombre, c.documento FROM ventas v INNER JOIN cliente c ON v.id_cliente = c.idcliente");
}else{
    $query = mysqli_query($conexion, "SELECT v.*, c.idcliente, c.nombre, c.documento FROM ventas v INNER JOIN cliente c ON v.id_cliente = c.idcliente WHERE v.id_usuario = $id_user $where");

}
include_once "includes/header.php";
?>
<div class="card">
    <div class="card-header">
        Historial ventas
    </div>
    <div class="card-body">
         <!-- Filtros -->
         <form method="POST" class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <label for="usuario">Usuario</label>
                    <select name="usuario" id="usuario" class="form-control">
                        <option value="">Todos</option>
                        <?php while ($user = mysqli_fetch_assoc($query_users)) { ?>
                            <option value="<?php echo $user['idusuario']; ?>">
                                <?php echo $user['nombre']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="fecha_inicio">Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="fecha_final">Fecha Final</label>
                    <input type="date" name="fecha_final" id="fecha_final" class="form-control">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" name="buscar" class="btn btn-primary w-100">Filtrar</button>
                </div>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-light" id="tbl">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Documento</th>
                        <th>Docente</th>
                        <th>Dotación</th>
                        <th>Total venta</th>
                        <th>Fecha</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($query)) { ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['documento']; ?></td>
                            <td><?php echo $row['nombre']; ?></td>
                            <td><?php echo $row['dataciones']; ?></td>
                            <td>$<?php echo $row['total']; ?></td>
                            <td><?php echo $row['fecha']; ?></td>
                            <td>
                                <a href="pdf/generar.php?cl=<?php echo $row['id_cliente'] ?>&v=<?php echo $row['id'] ?>" target="_blank" class="btn btn-danger"><i class="fas fa-file-pdf"></i></a>
                                <a href="lista_ventas_detalles.php?id_venta=<?php echo $row['id']; ?>" class="btn btn-warning">
                                <i class="fas fa-folder-open"></i>
                                        </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include_once "includes/footer.php"; ?>