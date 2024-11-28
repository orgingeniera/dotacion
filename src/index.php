<?php
require "../conexion.php";
$anio_actual = date('Y');
$usuarios = mysqli_query($conexion, "SELECT * FROM usuario");
$total['usuarios'] = mysqli_num_rows($usuarios);
$clientes = mysqli_query($conexion, "SELECT * FROM cliente");
$total['clientes'] = mysqli_num_rows($clientes);
$productos = mysqli_query($conexion, "SELECT * FROM producto");
$total['productos'] = mysqli_num_rows($productos);
$ventas = mysqli_query($conexion, "SELECT * FROM ventas WHERE fecha > CURDATE()");
$total['ventas'] = mysqli_num_rows($ventas);
$entregadas=0;
$no_entregadas=0;
$query = "
    SELECT 
        SUM(CASE WHEN dotacion_entregada = 1 THEN 1 ELSE 0 END) AS entregadas,
        SUM(CASE WHEN dotacion_entregada = 0 THEN 1 ELSE 0 END) AS no_entregadas
    FROM cliente
    WHERE YEAR(fecha) = '$anio_actual'
";
$result = mysqli_query($conexion, $query);
if ($result) {
    $data = mysqli_fetch_assoc($result);
    $entregadas = $data['entregadas'] ?? 0;
    $no_entregadas = $data['no_entregadas'] ?? 0;
} else {
    echo "Error en la consulta: " . mysqli_error($conexion);
}
session_start();
include_once "includes/header.php";
?>
<!-- Content Row -->
<div class="row">
    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="card card-stats">
            <div class="card-header card-header-warning card-header-icon">
                <div class="card-icon">
                    <i class="fas fa-user fa-2x"></i>
                </div>
                <a href="usuarios.php" class="card-category text-warning font-weight-bold">
                    Usuarios
                </a>
                <h3 class="card-title"><?php echo $total['usuarios']; ?></h3>
            </div>
            <div class="card-footer bg-warning text-white">
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="card card-stats">
            <div class="card-header card-header-success card-header-icon">
                <div class="card-icon">
                    <i class="fas fa-users fa-2x"></i>
                </div>
                <a href="docentes.php" class="card-category text-success font-weight-bold">
                    Docentes
                </a>
                <h3 class="card-title"><?php echo $total['clientes']; ?></h3>
            </div>
            <div class="card-footer bg-secondary text-white">
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="card card-stats">
            <div class="card-header card-header-danger card-header-icon">
                <div class="card-icon">
                    <i class="fab fa-product-hunt fa-2x"></i>
                </div>
                <a href="productos.php" class="card-category text-danger font-weight-bold">
                    Productos
                </a>
                <h3 class="card-title"><?php echo $total['productos']; ?></h3>
            </div>
            <div class="card-footer bg-primary">
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="card card-stats">
            <div class="card-header card-header-success card-header-icon">
                <div class="card-icon">
                    <i class="fas fa-solid fa-thumbs-up fa-2x"></i>
                </div>
                <a href="lista_docentes.php" class="card-category text-success font-weight-bold">
                    Dot. Entregadas
                </a>
                <h3 class="card-title"><?php echo $entregadas; ?></h3>
            </div>
            <div class="card-footer bg-success text-white">
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="card card-stats">
            <div class="card-header card-header-danger card-header-icon">
                <div class="card-icon">
                    <i class="fas fa-solid fa-thumbs-down fa-2x"></i> 
                  
                </div>
                <a href="lista_docentes.php" class="card-category text-danger font-weight-bold">
                Dot. No entregadas
                </a>
                <h3 class="card-title"><?php echo $no_entregadas; ?></h3>
            </div>
            <div class="card-footer bg-danger text-white">
            </div>
        </div>
    </div>
    <div class="col-md-12 card-header card-header-primary">
    <div >
                <h3 class="title-2 m-b-40">Productos con stock mínimo</h3>
            </div>
    <div class="table-responsive">
        <table class="table table-striped table-bordered" id="tbl">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Productos</th>
                    <th>Tallas</th>
                   
                    <th>Stock</th>
                    <th>Categoría</th>
                   
                </tr>
            </thead>
            <tbody>
                <?php
                include "../conexion.php";

                // Consulta para obtener productos con existencia <= 10
                $query = mysqli_query($conexion, "SELECT p.*, c.nombre AS 'nombre_categoria' 
                                                  FROM producto p 
                                                  JOIN categoria c ON p.id_categoria = c.id_categoria
                                                  WHERE p.existencia <= 10");
                $result = mysqli_num_rows($query);

                if ($result > 0) {
                    while ($data = mysqli_fetch_assoc($query)) {
                        // Determinar el color según la existencia
                        $color = '';
                        if ($data['existencia'] <= 5) {
                            $color = 'table-danger'; // Rojo
                        } elseif ($data['existencia'] <= 7) {
                            $color = 'table-warning'; // Amarillo
                        } elseif ($data['existencia'] <= 10) {
                            $color = 'table-success'; // Verde
                        }
                        ?>
                        <tr class="<?php echo $color; ?>">
                            <td><?php echo $data['codproducto']; ?></td>
                            <td><?php echo $data['codigo']; ?></td>
                            <td><?php echo $data['descripcion']; ?></td>
                           
                            <td><?php echo $data['existencia']; ?></td>
                            <td><?php echo $data['nombre_categoria']; ?></td>
                            
                        </tr>
                        <?php
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

    <div class="col-lg-6">
		<div class="card">
            <div class="card-header card-header-primary">
                <h3 class="title-2 m-b-40">Productos con stock mínimo</h3>
            </div>
            <div class="card-body">
			<canvas id="stockMinimo"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
	<div class="card">
            <div class="card-header card-header-primary">
                <h3 class="title-2 m-b-40">Productos con mas salidas</h3>
            </div>
            <div class="card-body">
			<canvas id="ProductosVendidos"></canvas>
            </div>
        </div>
    </div>
</div>

<?php include_once "includes/footer.php"; ?>