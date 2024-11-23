<?php
session_start();
include "../conexion.php";
$id_user = $_SESSION['idUser'];
$permiso = "clientes";
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso'");
$existe = mysqli_fetch_all($sql);
if (empty($existe) && $id_user != 1) {
    header('Location: permisos.php');
}

if (!empty($_POST)) {
    $alert = "";
    if (empty($_POST['nombre']) || empty($_POST['documento']) || empty($_POST['municipio'])) {
        $alert = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        Todo los campos son obligatorio
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
    } else {
        $id = $_POST['id'];
        $nombre = $_POST['nombre'];
        $documento = $_POST['documento'];
        $municipio = $_POST['municipio'];
        $result = 0;
        if (empty($id)) {
            $query = mysqli_query($conexion, "SELECT * FROM cliente WHERE nombre = '$nombre'");
            $result = mysqli_fetch_array($query);
            if ($result > 0) {
                $alert = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        El cliente ya existe
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
            } else {
                $query_insert = mysqli_query($conexion, "INSERT INTO cliente(nombre,documento,municipio) values ('$nombre', '$documento', '$municipio')");
                if ($query_insert) {
                    $alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        Cliente registrado
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
                } else {
                    $alert = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Error al registrar
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
                }
            }
        }else{
            $sql_update = mysqli_query($conexion, "UPDATE cliente SET nombre = '$nombre' , documento = '$documento', municipio = '$municipio' WHERE idcliente = $id");
            if ($sql_update) {
                $alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        Cliente Modificado
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
            } else {
                $alert = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Error al modificar
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
            }
        }
    }
    mysqli_close($conexion);
}
include_once "includes/header.php";
?>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <?php echo (isset($alert)) ? $alert : '' ; ?>
                <form action="" method="post" autocomplete="off" id="formulario">
                    <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="bono" class="text-dark font-weight-bold">bono</label>
                            <select name="bono" id="bono" class="form-control">
                                <?php
                                for ($i = 1; $i <= 9999; $i++) {
                                    $numero = str_pad($i, 4, '0', STR_PAD_LEFT); // Rellena con ceros a la izquierda
                                    echo "<option value=\"$numero\">$numero</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                            <div class="form-group">
                                <label for="documento" class="text-dark font-weight-bold">Documento</label>
                                <input type="number" placeholder="Ingrese Teléfono" name="documento" id="documento" class="form-control">
                                <input type="hidden" name="id" id="id">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre" class="text-dark font-weight-bold">Nombre Completo</label>
                                <input type="text" placeholder="Ingrese Nombre Completo" name="nombre" id="nombre" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="municipio" class="text-dark font-weight-bold">Municipio</label>
                                <select name="municipio" id="municipio" class="form-control">
                                    <option value="">Seleccione un municipio</option>
                                    <?php
                                    // Conexión a la base de datos
                                    include "../conexion.php";
                                    $query_municipios = mysqli_query($conexion, "SELECT * FROM municipios");
                                    while ($row = mysqli_fetch_assoc($query_municipios)) {
                                        echo "<option value='" . $row['id_municipios'] . "'>" . $row['nombre'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="colegio" class="text-dark font-weight-bold">Colegio</label>
                                <select name="colegio" id="colegio" class="form-control">
                                    <option value="">Seleccione un colegio</option>
                                    <!-- Opciones se cargarán dinámicamente con JavaScript -->
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                        <div class="form-group">
                            <label for="sexo" class="text-dark font-weight-bold">Sexo</label>
                            <select name="sexo" id="sexo" class="form-control">
                                <option value="">Seleccione Sexo</option>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="rol" class="text-dark font-weight-bold">Tipo Funcionario</label>
                            <select name="rol" id="rol" class="form-control">
                                <option value="">Tipo Funcionario</option>
                                <option value="Administrativo">Administrativo</option>
                                <option value="Docente">Docente</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                            <div class="form-group">
                                <label for="dotacion" class="text-dark font-weight-bold">Dotación</label>
                                <select name="dotacion" id="dotacion" class="form-control">
                                    <option value="">Seleccione una dotación</option>
                                    <option value="1">Dotación 1</option>
                                    <option value="2">Dotación 2</option>
                                    <option value="3">Dotación 3</option>
                                </select>
                            </div>
                        </div>
                   
                       
            
                       
                    </div>
                    <div class="col-md-4 mt-3">
                            <input type="submit" value="Registrar" class="btn btn-primary" id="btnAccion">
                            <input type="button" value="Nuevo" class="btn btn-success" id="btnNuevo" onclick="limpiar()">
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
                                <th>Documento</th>
                                <th>Dirección</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            include "../conexion.php";

                            $query = mysqli_query($conexion, "SELECT * FROM cliente");
                            $result = mysqli_num_rows($query);
                            if ($result > 0) {
                                while ($data = mysqli_fetch_assoc($query)) { ?>
                                    <tr>
                                        <td><?php echo $data['idcliente']; ?></td>
                                        <td><?php echo $data['nombre']; ?></td>
                                        <td><?php echo $data['documento']; ?></td>
                                        <td><?php echo $data['municipio']; ?></td>
                                        <td>
                                            <a href="#" onclick="editarCliente(<?php echo $data['idcliente']; ?>)" class="btn btn-primary"><i class='fas fa-edit'></i></a>
                                            <form action="eliminar_cliente.php?id=<?php echo $data['idcliente']; ?>" method="post" class="confirmar d-inline">
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
    document.getElementById('municipio').addEventListener('change', function () {
        const municipioId = this.value;

        // Si no hay municipio seleccionado, vaciar el select de colegios
        if (municipioId === "") {
            document.getElementById('colegio').innerHTML = '<option value="">Seleccione un colegio</option>';
            return;
        }

        // Realizar la solicitud AJAX para obtener los colegios del municipio seleccionado
        fetch(`get_colegios.php?municipio=${municipioId}`)
            .then(response => response.json())
            .then(data => {
                let options = '<option value="">Seleccione un colegio</option>';
                data.forEach(colegio => {
                    options += `<option value="${colegio.id_colegios}">${colegio.nombre}</option>`;
                });
                document.getElementById('colegio').innerHTML = options;
            })
            .catch(error => console.error('Error:', error));
    });
</script>

<?php include_once "includes/footer.php"; ?>