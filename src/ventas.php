<?php
session_start();
require("../conexion.php");
$id_user = $_SESSION['idUser'];
$permiso = "nueva_venta";
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso'");
$existe = mysqli_fetch_all($sql);
if (empty($existe) && $id_user != 1) {
    header('Location: permisos.php');
}
include_once "includes/header.php";
?>
<!--Para el select2-->
 <script src="../assets/js/jquery-3.6.0.min.js" crossorigin="anonymous"></script>

<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <h4 class="text-center">Datos del Docente</h4>
        </div>
        <div class="card">
            <div class="card-body">
                <form method="post">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <input type="hidden" id="idcliente" value="1" name="idcliente" required>

                                <label>Documento</label>
                                 <input type="text" name="documentos" id="documentos" class="form-control" placeholder="Ingrese Cedula del docente" required>
                                </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Nombre</label>
                                <input type="text" name="nombre" id="nombre" class="form-control" disabled required>
                    </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Dotación</label>

                                <select name="dotaciones" id="dotaciones"  class="form-control">

                                </select>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-header bg-primary text-white text-center">
                Buscar Productos
            </div>
            <div class="card-body">
            
                <div class="row">
                    <div class="col-lg-5">
                        <div class="form-group">
                            <label for="producto">Código o Nombre</label>

                            <select class="js-example-basic-single" id="producto"  name="producto"  name="states[]" style="width: 50%;">
                                
                            </select>
                         <!--   <input id="producto" class="form-control" type="text" name="producto" placeholder="Ingresa el código o nombre">-->
                            <input id="id" type="hidden" name="id">
     

                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label for="cantidad">Cantidad</label>
                            <input id="cantidad" required class="form-control" type="text" name="cantidad" placeholder="Cantidad" onkeyup="calcularPrecio(event)">
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">

                            <label for="stock">Stock</label>
                            <input  type="text" id="stock" class="form-control" name="stock" placeholder="stock" disabled>
                    
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">

                            <label for="sub_total">Stock futuro</label>
                            <input id="sub_total" class="form-control" type="text" name="sub_total" placeholder="Sub Total" disabled>

                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover" id="tblDetalle">
                <thead class="thead-dark">
                    <tr>
                        <th>Id</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Tallas</th>
                        <th>Accion</th>
                    </tr>
                </thead>
                <tbody id="detalle_venta">

                </tbody>
                <tfoot>
                    <tr class="font-weight-bold">
                        <td>Total Pagar</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>

        </div>
    </div>
    <div class="col-md-6">
        <a href="#" class="btn btn-primary" id="btn_generar"><i class="fas fa-save"></i> Generar Venta</a>
    </div>

</div>
<script>
 $(document).ready(function() {
    borrarTemp()
  // Inicializar Select2
  $('.js-example-basic-single').select2({
            placeholder: "Selecciona un producto", // Texto de ayuda
            allowClear: true // Permite limpiar la selección
        });
});

$.ajax({
        url: "get_productos.php", // Endpoint que devuelve los productos
        dataType: "json",
        success: function (productos) {
            // Limpiar y llenar el select con las opciones
            $('#producto').empty();
            $('#producto').append('<option value="">Seleccione un producto</option>');
            productos.forEach(function (producto) {
                $('#producto').append('<option value="' + producto.codproducto + '">' + producto.codproducto+"-"+producto.codigo + '</option>');
            });
        },
        error: function () {
            alert("Error al cargar los productos.");
        }
    });
</script>

<?php include_once "includes/footer.php"; ?>