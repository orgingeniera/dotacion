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
$permiso = "productos";
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso'");
$existe = mysqli_fetch_all($sql);
if (empty($existe) && $id_user != 1) {
    header('Location: permisos.php');
}
if (!empty($_POST)) {

    $id = $_POST['id'];
    $codigo = $_POST['codigo'];
    $producto = $_POST['producto'];
    $precio = $_POST['precio'];
    $cantidad = $_POST['cantidad'];
    $id_categoria = $_POST['id_categoria'];
  
    if (empty($codigo) || empty($producto) || empty($cantidad) || $cantidad <  0) {
        $_SESSION['alert'] = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        Todo los campos son obligatorios
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
    } else {
        if (empty($id)) {
            $query = mysqli_query($conexion, "SELECT * FROM producto WHERE codigo = '$codigo'");
            $result = mysqli_fetch_array($query);
            if ($result > 0) {
                $_SESSION['alert'] ='<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        El codigo ya existe
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
            } else {
                $query_insert = mysqli_query($conexion, "INSERT INTO producto(codigo,descripcion,precio,existencia,id_categoria,fecha) values ('$codigo', '$producto', 0, '$cantidad', '$id_categoria',CURDATE())");
                if ($query_insert) {
                    $_SESSION['alert'] ='<div class="alert alert-success alert-dismissible fade show" role="alert">
                        Producto registrado
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
                } else {
                    $_SESSION['alert'] = '<div class="alert alert-danger" role="alert">
                    Error al registrar el producto
                  </div>';
                }
            }
        } else {
            $query_update = mysqli_query($conexion, "UPDATE producto SET codigo = '$codigo', descripcion = '$producto', precio= 0, existencia = $cantidad, id_categoria=$id_categoria WHERE codproducto = $id");
            if ($query_update) {
                $_SESSION['alert'] ='<div class="alert alert-success alert-dismissible fade show" role="alert">
                        Producto Modificado
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
            } else {
                $_SESSION['alert'] ='<div class="alert alert-warning alert-dismissible fade show" role="alert">
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
    header("Location: productos.php"); 
    exit;
    
}

include_once "includes/header.php";
?>
<div class="card shadow-lg">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <form action="" method="post" autocomplete="off" id="formulario">
                <?php 
                if (isset($_SESSION['alert'])) {
                    echo $_SESSION['alert'];
                    unset($_SESSION['alert']);
                }
                ?>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="codigo"requred  class=" text-dark font-weight-bold"><i class="fas fa-barcode"></i> Nombre</label>
                                <input type="text"required  placeholder="Ingrese nombre del producto" name="codigo" id="codigo" class="form-control">
                                <input type="hidden"required  id="id" name="id">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="producto" class=" text-dark font-weight-bold">Talla</label>
                                <input type="text"required placeholder="Ingrese la talla" name="producto" id="producto" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="precio" class=" text-dark font-weight-bold">Precio</label>
                                <input type="text" value="0" readonly placeholder="Ingrese precio" class="form-control" name="precio" id="precio">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="cantidad" class=" text-dark font-weight-bold">Cantidad</label>
                                <input type="number"required placeholder="Ingrese cantidad" class="form-control" name="cantidad" id="cantidad">
                            </div>
                        </div>
                        <div class="col-md-2">
                        <div class="form-group">
                            <label for="categoria" class="text-dark font-weight-bold">Categorías</label>
                            <select class="form-control" name="id_categoria" id="id_categoria">
                                <option value="">Seleccione una categoría</option>
                                <?php
                                include "../conexion.php";
                                $query = mysqli_query($conexion, "SELECT id_categoria, nombre FROM categoria");
                                while ($categoria = mysqli_fetch_assoc($query)) {
                                    echo '<option value="' . $categoria['id_categoria'] . '">' . $categoria['nombre'] . '</option>';
                                }
                                mysqli_close($conexion);
                                ?>
                            </select>
                        </div>
                        </div>
                            <div class="col-md-6">
                                <input type="submit"  value="Registrar" class="btn btn-primary" id="btnAccion">
                                <input type="button"  value="Nuevo" onclick="limpiar()" class="btn btn-success" id="btnNuevo">
                            </div>
                    </div>

                </form>
                <div class="text-center mt-4">
                        <!-- Checkbox para mostrar/ocultar el contenido -->
                        <div class="form-check form-switch d-inline-block">
                            <input  type="checkbox" id="toggleContainer">
                            <label for="toggleContainer">Insertar Masivamente</label>
                        </div>
                    </div>

                    <!-- Contenedor principal oculto por defecto -->
                    <div class="container mt-5" id="mainContainer" style="display: none;">
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <div class="card shadow">
                                    <div class="card-header bg-primary text-white text-center">
                                        <h3>Cargar Archivo Excel</h3>
                                    </div>
                                    <div class="card-body">
                                        <!-- Formulario para cargar archivo -->
                                        <form id="uploadForm" action="procesar_excel_productos.php" method="POST" enctype="multipart/form-data">
                                            <div class="mb-3">
                                                <label for="archivo" class="form-label">Seleccione el archivo Excel</label>
                                                <input type="file" class="form-control" name="archivo" id="archivo" accept=".xlsx, .xls" required>
                                                <small class="form-text text-muted">Solo se permiten archivos .xls y .xlsx.</small>
                                            </div>
                                            <div class="d-grid">
                                                <button type="submit" class="btn btn-success">Cargar Datos</button>
                                                <a href="../assets/archivos/productos.xlsx">Descargar plantilla</a>
                                            </div>
                                        </form>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div> 
                                

             
            </div>
        </div>
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="tbl">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>Talla</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Categoria</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        include "../conexion.php";

                        $query = mysqli_query($conexion, "SELECT p.*, c.nombre AS 'nombre_categoria' FROM producto p JOIN categoria c ON p.id_categoria = c.id_categoria");
                        $result = mysqli_num_rows($query);
                        if ($result > 0) {
                            while ($data = mysqli_fetch_assoc($query)) { ?>
                                <tr>
                                    <td><?php echo $data['codproducto']; ?></td>
                                    <td><?php echo $data['codigo']; ?></td>
                                    <td><?php echo $data['descripcion']; ?></td>
                                    <td><?php echo $data['precio']; ?></td>
                                    <td><?php echo $data['existencia']; ?></td>
                                    <td><?php echo $data['nombre_categoria']; ?></td>
                                    <td>
                                        <a href="#" onclick="editarProducto(<?php echo $data['codproducto']; ?>)" class="btn btn-primary"><i class='fas fa-edit'></i></a>

                                        <form action="eliminar_producto.php?id=<?php echo $data['codproducto']; ?>" method="post" class="confirmar d-inline">
                                            <button class="btn btn-danger" type="submit"><i class='fas fa-trash-alt'></i> </button>
                                        </form>
                                        <a href="ingresarexistencias.php?id_producto=<?php echo $data['codproducto']; ?>" class="btn btn-warning">
                                            <i class="fas fa-plus"></i>
                                        </a>

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
<script>
    document.getElementById('toggleContainer').addEventListener('change', function() {
            const container = document.getElementById('mainContainer');
            container.style.display = this.checked ? 'block' : 'none';
    });
    $(document).ready(function() {
        $('#categoria').select2({
            placeholder: "Seleccione una categoría",
            allowClear: true
        });
    });
    function editarProducto(id) {
    const action = "editarProducto";
    $.ajax({
        url: 'ajax.php',
        type: 'GET',
        async: true,
        data: {
            editarProducto: action,
            id: id
        },
        success: function (response) {
            const datos = JSON.parse(response);
            console.log(datos)
            $('#codigo').val(datos.codigo);
            $('#producto').val(datos.descripcion);
            $('#precio').val(datos.precio);
            $('#cantidad').val(datos.existencia);
            $('#id_categoria').val(datos.id_categoria);
            $('#id').val(datos.codproducto);
            $('#btnAccion').val('Modificar');
        },
        error: function (error) {
            console.log(error);

        }
    });
}

</script>

<?php include_once "includes/footer.php"; ?>