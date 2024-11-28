<?php
session_start();
header("Cache-Control: no-cache, must-revalidate");
include "../conexion.php";
if (!isset($_SESSION['idUser'])) {
    // Redirigir al usuario al index si no está definido
    header("Location: index.php");
    exit;
}
// Verificar permisos
$id_user = $_SESSION['idUser'];
$permiso = "clientes";
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p 
    INNER JOIN detalle_permisos d ON p.id = d.id_permiso 
    WHERE d.id_usuario = $id_user AND p.nombre = '$permiso'");
$existe = mysqli_fetch_all($sql);
if (empty($existe) && $id_user != 1) {
    header('Location: permisos.php');
    exit();
}

// Manejo de formulario
$alert = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $bono = $_POST['bono'] ?? null;
    $nombre = $_POST['nombre'] ?? null;
    $documento = $_POST['documento'] ?? null;
    $municipio = $_POST['id_municipios'] ?? null;
    $colegio = $_POST['id_colegio'] ?? null;
    $sexo = $_POST['sexo'] ?? null;
    $tipo_funcionario = $_POST['tipo_funcionario'] ?? null;
    $dotacion = $_POST['dotacion'] ?? null;

    if (empty($nombre) || empty($documento) || empty($municipio)) {
          $_SESSION['alert'] = '<div class="alert alert-warning">Todos los campos son obligatorios</div>';
    } else {
        if (empty($id)) {
            // Insertar cliente
            $query = mysqli_query($conexion, "SELECT * FROM cliente WHERE documento = '$documento' AND dotacion = '$dotacion'");
            if (mysqli_num_rows($query) > 0) {
                $row = mysqli_fetch_assoc($query); // Obtiene la fila como un arreglo asociativo
                $bono = $row['bono']; // Accede al campo 'bono'
                $_SESSION['alert'] = '<div class="alert alert-warning">El docente ya existe con bono: ' . $bono . '</div>';
            }  else {
                $query_insert = mysqli_query($conexion, 
                    "INSERT INTO cliente(nombre, documento, id_municipios, id_colegio, sexo, tipo_funcionario, dotacion, bono, fecha) 
                     VALUES ('$nombre', '$documento', '$municipio', '$colegio', '$sexo', '$tipo_funcionario', '$dotacion', '$bono', NOW())");
                  $_SESSION['alert']  = $query_insert 
                    ? '<div class="alert alert-success">Docente registrado</div>' 
                    : '<div class="alert alert-danger">Error al registrar</div>';
            }
        } else {
            // Actualizar cliente
            $sql_update = mysqli_query($conexion, 
                "UPDATE cliente 
                 SET nombre = '$nombre',bono = '$bono', documento = '$documento', id_municipios = '$municipio', 
                     id_colegio = '$colegio', sexo = '$sexo', tipo_funcionario = '$tipo_funcionario', dotacion = '$dotacion' 
                 WHERE idcliente = $id");
             $_SESSION['alert'] =  $sql_update 
                ? '<div class="alert alert-success">Docente modificado</div>' 
                : '<div class="alert alert-danger">Error al modificar</div>';
        }
    }
   // mysqli_close($conexion);
   header("Location: docentes.php"); 
   exit;
}
include_once "includes/header.php";
?>

<div class="card">
    <div class="card-body">
             <?php 
                if (isset($_SESSION['alert'])) {
                    echo $_SESSION['alert'];
                    unset($_SESSION['alert']);
                }
                
                ?>
        <form action="" method="post" autocomplete="off" id="formulario">
            <input type="hidden" name="id" id="id">
            <div class="row">
                     <div class="col-md-3">
                        <div class="form-group">
                            <label for="bono" class="text-dark font-weight-bold">bono</label>
                            <select name="bono" required id="bono" class="form-control">
                                <option value="">Escoja Bono</option>
                                <?php
                                for ($i = 1; $i <= 9999; $i++) {
                                    $numero = str_pad($i, 4, '0', STR_PAD_LEFT); // Rellena con ceros a la izquierda
                                    echo "<option value=\"$numero\">$numero</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                <!-- Nombre -->
                <div class="col-md-6">
                    <label for="nombre" class="font-weight-bold">Nombre Completo</label>
                    <input type="text" name="nombre" id="nombre" required class="form-control" placeholder="Ingrese Nombre" >
                </div>
                <!-- Documento -->
                <div class="col-md-6">
                    <label for="documento" class="font-weight-bold">Documento</label>
                    <input type="text" name="documento" required id="documento" class="form-control" placeholder="Ingrese Documento">
                </div>
                <!-- Municipio -->
                <div class="col-md-6">
                    <label for="id_municipios" class="font-weight-bold">Municipio</label>
                    <select name="id_municipios" required id="id_municipios" class="form-control">
                        <option value="">Seleccione un municipio</option>
                        <?php
                        $query_municipios = mysqli_query($conexion, "SELECT * FROM municipios");
                        while ($row = mysqli_fetch_assoc($query_municipios)) {
                            echo "<option value='" . $row['id_municipios'] . "'>" . $row['nombre'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <!-- Colegio -->
                <div class="col-md-6">
                    <label for="id_colegio" class="font-weight-bold">Colegio</label>
                    <select name="id_colegio" required id="id_colegio" class="form-control">
                        <option value="">Seleccione un colegio</option>
                    </select>
                </div>
                <!-- Sexo -->
                <div class="col-md-6">
                    <label for="sexo" class="font-weight-bold">Sexo</label>
                    <select name="sexo" required id="sexo" class="form-control">
                        <option value="">Seleccione</option>
                        <option value="M">Masculino</option>
                        <option value="F">Femenino</option>
                    </select>
                </div>
                <!-- Rol -->
                <div class="col-md-6">
                    <label for="tipo_funcionario" class="font-weight-bold">Tipo Funcionario</label>
                    <select name="tipo_funcionario" required id="tipo_funcionario" class="form-control">
                        <option value="">Seleccione</option>
                        <option value="Administrativo">Administrativo</option>
                        <option value="Docente">Docente</option>
                    </select>
                </div>
                <!-- Dotación -->
                <div class="col-md-6">
                    <label for="dotacion" class="font-weight-bold">Dotación</label>
                    <select name="dotacion" required id="dotacion" class="form-control">
                        <option value="">Seleccione</option>
                        <option value="1">Dotación 1</option>
                        <option value="2">Dotación 2</option>
                        <option value="3">Dotación 3</option>
                    </select>
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <button type="button" class="btn btn-success" onclick="limpiar()">Nuevo</button>
            </div>
        </form>

        <!-- Tabla de clientes -->
        <div class="mt-4">
            <table class="table table-striped" id="tbl">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Bono</th>
                        <th>Nombre</th>
                        <th>Documento</th>
                        <th>Municipio</th>
                        <th>Colegio</th>
                        <th>Sexo</th>
                        <th>tipo_funcionario</th>
                        <th>Dotación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $query = mysqli_query($conexion, "SELECT cliente.idcliente, cliente.bono, cliente.nombre, cliente.documento, cliente.id_municipios, municipios.nombre AS nombre_municipio, cliente.id_colegio, colegios.nombre AS nombre_colegio,cliente.sexo, cliente.tipo_funcionario, cliente.dotacion
                        FROM cliente
                        LEFT JOIN municipios ON cliente.id_municipios = municipios.id_municipios
                        LEFT JOIN colegios ON cliente.id_colegio = colegios.id_colegios
                    ");
                        while ($data = mysqli_fetch_assoc($query)) { ?>
                            <tr>
                                <td><?php echo $data['idcliente']; ?></td>
                                <td><?php echo $data['bono']; ?></td>
                                <td><?php echo $data['nombre']; ?></td>
                                <td><?php echo $data['documento']; ?></td>
                                <td><?php echo $data['nombre_municipio']; ?></td>
                                <td><?php echo $data['nombre_colegio']; ?></td>
                                <td><?php echo $data['sexo']; ?></td>
                                <td><?php echo $data['tipo_funcionario']; ?></td>
                                <td><?php echo $data['dotacion']; ?></td>
                                <td>
                                    <a href="#" onclick="editarClientess(<?php echo $data['idcliente']; ?>)" class="btn btn-primary"><i class='fas fa-edit'></i></a>
                                    <form action="eliminar_cliente.php?id=<?php echo $data['idcliente']; ?>" method="post" class="confirmar d-inline">
                                                <button class="btn btn-danger" type="submit"><i class='fas fa-trash-alt'></i> </button>
                                            </form>
                                </td>
                            </tr>
                        <?php } ?>
                
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    document.getElementById('id_municipios').addEventListener('change', function () {
        const municipioId = this.value;
           
        // Si no hay municipio seleccionado, vaciar el select de colegios
        if (municipioId === "") {
            document.getElementById('id_colegio').innerHTML = '<option value="">Seleccione un colegio</option>';
            return;
        }

        // Realizar la solicitud AJAX para obtener los colegios del municipio seleccionado
        fetch(`get_colegios.php?id_municipios=${municipioId}`)
            .then(response => response.json())
            .then(data => {
                let options = '<option value="">Seleccione un colegio</option>';
                data.forEach(colegio => {
                    options += `<option value="${colegio.id_colegios}">${colegio.nombre}</option>`;
                });
                document.getElementById('id_colegio').innerHTML = options;
            })
            .catch(error => console.error('Error:', error));
    });



</script>
<?php include_once "includes/footer.php"; ?>
