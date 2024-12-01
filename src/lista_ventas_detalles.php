<?php
session_start();
if (!isset($_SESSION['idUser'])) {
    // Redirigir al usuario al index si no está definido
    header("Location: index.php");
    exit;
}
require_once "../conexion.php";

$id_user = $_SESSION['idUser'];
$permiso = "ventas";

// Verificar permisos
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso'");
$existe = mysqli_fetch_all($sql);
if (empty($existe) && $id_user != 1) {
    header('Location: permisos.php');
}

// Validar que se recibió el parámetro id_venta
if (!isset($_GET['id_venta']) || empty($_GET['id_venta'])) {
    echo "ID de venta no proporcionado.";
    exit;
}

$id_venta = intval($_GET['id_venta']);

// Consulta para obtener los detalles de la venta
$query = mysqli_query($conexion, "
    SELECT 
        dv.id AS detalle_id, 
        p.codigo, 
        dv.cantidad, 
        dv.precio, 
        dv.total 
    FROM 
        detalle_venta dv
    INNER JOIN 
        producto p ON dv.id_producto = p.codproducto
    WHERE 
        dv.id_venta = $id_venta
");
// Consulta para obtener el total general de la venta
$query_total = mysqli_query($conexion, "SELECT SUM(total) AS total_venta FROM detalle_venta WHERE id_venta = $id_venta");
$total_venta = mysqli_fetch_assoc($query_total)['total_venta'];

// Consulta para obtener el nombre del usuario que realizó la venta
$query_user = mysqli_query($conexion, "
    SELECT u.nombre AS usuario_nombre 
    FROM ventas v
    INNER JOIN usuario u ON v.id_usuario = u.idusuario
    WHERE v.id = $id_venta
");

// Verifica si se obtuvo el usuario
$usuario = mysqli_fetch_assoc($query_user)['usuario_nombre'] ?? 'Desconocido';


include_once "includes/header.php";
?>

<div class="card">
    
    <div class="card-header">
        Detalles de la venta #<?php echo $id_venta; ?>
        <button class="btn btn-primary float-right mr-2" onclick="printTable()">Imprimir</button>
    </div>
    <div class="card-header">
        <span class="float-right">Vendedor: <strong><?php echo htmlspecialchars($usuario); ?></strong></span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-light" id="tbl">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $contador = 1;
                    while ($row = mysqli_fetch_assoc($query)) { ?>
                        <tr>
                            <td><?php echo $contador++; ?></td>
                            <td><?php echo $row['codigo']; ?></td>
                            <td><?php echo $row['cantidad']; ?></td>
                            <td>$<?php echo $row['precio']; ?></td>
                            <td>$<?php echo $row['total']; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" class="text-right">Total de la venta:</th>
                        <th>$<?php echo number_format($total_venta, 2); ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<?php include_once "includes/footer.php"; ?>
<script>
   function printTable() {
    // Clonar la tabla para la impresión
    const printContents = document.querySelector('#tbl').outerHTML;
    const vendedor = "<?php echo htmlspecialchars($usuario); ?>";
    const printWindow = window.open('', '', 'width=800,height=600');

    // Generar el contenido del documento a imprimir
    printWindow.document.write('<html><head><title>Imprimir Tabla</title>');
    printWindow.document.write('<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">');
    printWindow.document.write('<style>table { width: 100%; border-collapse: collapse; } th, td { border: 1px solid #000; padding: 8px; text-align: left; }</style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write('<h3>Detalles de la venta #<?php echo $id_venta; ?></h3>');
    printWindow.document.write('<p>Vendedor: <strong>' + vendedor + '</strong></p>');
    printWindow.document.write(printContents);
    printWindow.document.write('</body></html>');

    // Cerrar el documento y ejecutar la impresión
    printWindow.document.close();
    printWindow.print();
}

    
</script>