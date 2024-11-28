<?php
session_start();
header("Cache-Control: no-cache, must-revalidate");
include "../conexion.php";

// Verifica que el usuario tiene permisos
if (!isset($_SESSION['idUser'])) {
    header("Location: index.php");
    exit;
}
$id_user = $_SESSION['idUser'];
$permiso = "productos";
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso'");
$existe = mysqli_fetch_all($sql);
if (empty($existe) && $id_user != 1) {
    header('Location: permisos.php');
}
/*if (!isset($_GET['id_producto']) || empty($_GET['id_producto'])) {
    $_SESSION['alert'] = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
        ID del producto no especificado.
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>';
    header("Location: productos.php");
    exit;
}*/
$id_producto = intval($_GET['id_producto']);
if (!empty($_POST)) {
    if (empty($_POST['id_producto']) || empty($_POST['cantidad'])) {
        $_SESSION['alert'] = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        Todos los campos son obligatorios.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
    } else {
      
        $cantidad = intval($_POST['cantidad']);

        // Inicia la transacción
        mysqli_autocommit($conexion, false);

        try {
            // Actualizar el campo existencia
            $sql_update = "UPDATE producto SET existencia = existencia + $cantidad WHERE codproducto = $id_producto";
            $result_update = mysqli_query($conexion, $sql_update);

            if (!$result_update) {
                throw new Exception("Error al actualizar la existencia.");
            }

            // Insertar registro en historia_existencias
            $sql_insert = "INSERT INTO historia_existencias (id_producto, cantidad, fecha) VALUES ($id_producto, $cantidad, CURDATE())";
            $result_insert = mysqli_query($conexion, $sql_insert);

            if (!$result_insert) {
                throw new Exception("Error al registrar la operación en el historial.");
            }

            // Confirmar la transacción
            mysqli_commit($conexion);
            $_SESSION['alert'] = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        Existencia actualizada correctamente.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
        } catch (Exception $e) {
            // Revertir la transacción
            mysqli_rollback($conexion);
            $_SESSION['alert'] = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        ' . $e->getMessage() . '
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
        }
        mysqli_autocommit($conexion, true);
    }
    mysqli_close($conexion);
    header("Location: ingresarexistencias.php?id_producto=".$id_producto);
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
                 <?php
                    // Obtener datos del producto
                    
                    $query = mysqli_query($conexion, "SELECT codigo, existencia FROM producto WHERE codproducto = $id_producto");
                    $product = mysqli_fetch_assoc($query);
                ?>
                <form action="" method="post" autocomplete="off">
                    <div class="row">
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="cantidad" class="text-dark font-weight-bold">Cantidad a Añadir</label>
                                <input type="number" required placeholder="Ingrese Cantidad" name="cantidad" id="cantidad" class="form-control">
                                <input type="hidden" value="<?php echo $id_producto;  ?>"   name="id_producto" id="id_producto">

                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="nombre_producto" class="text-dark font-weight-bold">Nombre del Producto</label>
                                <p id="nombre_producto" class="form-control-plaintext text-dark font-weight-normal">
                                    <?php echo $product['codigo']; ?>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="existencia_actual" class="text-dark font-weight-bold">Existencia Actual</label>
                                <p id="existencia_actual" class="form-control-plaintext text-dark font-weight-normal">
                                    <?php echo $product['existencia']; ?>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12 mt-3">
                            <input type="submit" value="Añadir Existencia" class="btn btn-primary">
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-12">
                <div class="table-responsive">
                <table class="table table-striped table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>ID Producto</th>
                                <th>Descripción</th>
                                <th>Existencia Actual</th>
                                <th>Precio</th>
                                <th>Cantidad Añadida</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Consulta que une las tablas producto e historia_existencias
                            $query = mysqli_query($conexion, "
                                SELECT 
                                    p.codproducto, 
                                    p.codigo, 
                                    p.existencia, 
                                    p.precio, 
                                    h.cantidad, 
                                    h.fecha 
                                FROM 
                                    historia_existencias h
                                INNER JOIN 
                                    producto p 
                                ON 
                                    h.id_producto = p.codproducto
                                WHERE 
                                    p.codproducto = $id_producto
                                ORDER BY 
                                    h.fecha DESC
                            ");
                            
                            $result = mysqli_num_rows($query);
                            if ($result > 0) {
                                while ($data = mysqli_fetch_assoc($query)) { ?>
                                    <tr>
                                        <td><?php echo $data['codproducto']; ?></td>
                                        <td><?php echo $data['codigo']; ?></td>
                                        <td><?php echo $data['existencia']; ?></td>
                                        <td><?php echo number_format($data['precio'], 2); ?></td>
                                        <td><?php echo $data['cantidad']; ?></td>
                                        <td><?php echo $data['fecha']; ?></td>
                                    </tr>
                            <?php }
                            } else { ?>
                                <tr>
                                    <td colspan="6" class="text-center">No hay registros de existencias para este producto</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>
<?php include_once "includes/footer.php"; ?>
