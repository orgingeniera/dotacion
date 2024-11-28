<?php
session_start();
header("Cache-Control: no-cache, must-revalidate");
include "../conexion.php";

// Verificar si el usuario está autenticado
if (!isset($_SESSION['idUser'])) {
    header("Location: index.php");
    exit;
}

$id_user = $_SESSION['idUser'];

// Verificar permisos del usuario
$permiso = "ventas";
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso'");
$existe = mysqli_fetch_all($sql);
if (empty($existe) && $id_user != 1) {
    header('Location: permisos.php');
    exit;
}

include_once "includes/header.php";

// Obtener el id del usuario al que se quiere consultar las ventas
$id_usuario = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : 0;

if ($id_usuario > 0) {
    // Consulta para obtener las ventas realizadas por el usuario
    $query = "
        SELECT v.id, c.documento, v.fecha, c.dotacion, c.nombre AS cliente, u.nombre AS usuario
        FROM ventas v
        INNER JOIN cliente c ON v.id_cliente = c.idcliente
        INNER JOIN usuario u ON v.id_usuario = u.idusuario
        WHERE v.id_usuario = $id_usuario
        ORDER BY v.fecha DESC
    ";
    $result = mysqli_query($conexion, $query);
    $ventas = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
    $ventas = [];
}
?>

<div class="card">
    <div class="card-body">
        <h2 class="text-center">Ventas Realizadas</h2>
        <button onclick="imprimirVentas()" class="btn btn-info">Imprimir Ventas</button>
        <div class="row">
            <div class="col-md-12">
                <?php
                if (isset($_SESSION['alert'])) {
                    echo $_SESSION['alert'];
                    unset($_SESSION['alert']);
                }
                ?>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="tbl">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Documento</th>
                                <th>Nombre docente</th>
                                
                                <th>Dotación</th>
                                <th>Fecha</th>
                                <th>Vendedor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($ventas) > 0): ?>
                                <?php foreach ($ventas as $venta): ?>
                                    <tr>
                                        <td><?php echo $venta['id']; ?></td>
                                        <td><?php echo $venta['documento']; ?></td>
                                        <td><?php echo $venta['cliente']; ?></td>                                       
                                        <td><?php echo $venta['dotacion']?></td>
                                        <td><?php echo $venta['fecha']; ?></td>
                                        <td><?php echo $venta['usuario']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">No se encontraron ventas para este usuario.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
<script>
    function imprimirVentas() {
        var contenido = document.getElementById("tbl"); // Obtenemos el contenido de la tabla
        var ventanaImpresion = window.open('', '_blank'); // Abrimos una nueva ventana para la impresión
        ventanaImpresion.document.write('<html><head><title>Ventas del Usuario</title>');
        ventanaImpresion.document.write('<style>body { font-family: Arial, sans-serif; margin: 20px; } table { width: 100%; border-collapse: collapse; } th, td { padding: 8px; text-align: left; border: 1px solid #ddd; } th { background-color: #f2f2f2; } </style>');
        ventanaImpresion.document.write('</head><body>');
        ventanaImpresion.document.write('<h2>Ventas Realizadas</h2>');
        ventanaImpresion.document.write(contenido.outerHTML); // Insertamos la tabla con las ventas
        ventanaImpresion.document.write('</body></html>');
        ventanaImpresion.document.close();
        ventanaImpresion.print(); // Ejecutamos la acción de imprimir
    }

</script>
<?php include_once "includes/footer.php"; ?>
