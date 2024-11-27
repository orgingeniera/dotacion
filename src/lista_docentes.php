<?php
session_start();
require_once "../conexion.php";
$id_user = $_SESSION['idUser'];
$permiso = "ventas";
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso'");
$existe = mysqli_fetch_all($sql);
if (empty($existe) && $id_user != 1) {
    header('Location: permisos.php');
}

include_once "includes/header.php";
?>
 <script src="../assets/js/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
<div class="card">
    <div class="card-header">
        <h4>Reporte de Dotaciones</h4>
    </div>
    <div class="card-body">
        <!-- Select para dotación y estado -->
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="dotacion">Selecciona Dotación:</label>
                <select id="dotacion" class="form-control">
                    <option value="">Escaja Dotación</option>
                    <option value="1">Dotación 1</option>
                    <option value="2">Dotación 2</option>
                    <option value="3">Dotación 3</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="estado">Selecciona Estado:</label>
                <select id="estado" class="form-control">
                    <option value="">Escaja Estado</option>
                    <option value="dotado">Entregadas</option>
                    <option value="sin_dotacion">Sin Entregar</option>
                </select>
            </div>
            <div class="col-md-4">
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
            

        </div>
        <div class="row mb-3">
          <div class="col-md-6">
                    <label for="id_colegio" class="font-weight-bold">Colegio</label>
                    <select name="id_colegio" required id="id_colegio" class="form-control">
                        <option value="">Seleccione un colegio</option>
                    </select>
           </div>
                
        
            <div class="col-md-6">
            <label for="anio">Seleccione Año:</label>
                <select id="anio" class="form-control">
                    <option value="2024">2024</option>
                    <option value="2025">2025</option>
                </select>
            </div>
            

        </div>
        <div class="row mb-3">
            <div class="col-md-12 text-center">
                <button id="btnActualizarTabla" class="btn btn-primary">Consultar</button>
                <button id="btnExportarExcel" class="btn btn-success">Exportar Excel</button>
            </div>
        </div>
        <!-- Tabla dinámica -->
        <div class="table-responsive">
        <table class="table table-striped" id="tbl">
                <thead>
                    <tr>
                        <th>Bono</th>
                        <th>Nombre</th>
                        <th>Documento</th>
                        <th>Municipio</th>
                        <th>Colegio</th>
                        <th>Sexo</th>
                        <th>Tipo Funcionario</th>
                        <th>Dotación</th>
                        <th>Estado</th>
                        
                    </tr>
                </thead>
                <tbody id="tabla-cuerpo">
                    <?php
                        

                        $query = mysqli_query($conexion, "
                            SELECT 
                                cliente.dotacion_entregada,
                                cliente.bono, 
                                cliente.nombre, 
                                cliente.documento, 
                                cliente.id_municipios, 
                                municipios.nombre AS nombre_municipio, 
                                cliente.id_colegio, 
                                colegios.nombre AS nombre_colegio, 
                                cliente.sexo, 
                                cliente.tipo_funcionario, 
                                cliente.dotacion
                            FROM cliente
                            LEFT JOIN municipios ON cliente.id_municipios = municipios.id_municipios
                            LEFT JOIN colegios ON cliente.id_colegio = colegios.id_colegios
                        ");

                        // Iterar a través de los resultados de la consulta
                        while ($data = mysqli_fetch_assoc($query)) { 
                            $estado_entrega = ($data['dotacion_entregada'] == 1 ? 'Entregada' : 'No Entregada');
                            ?>
                            <tr>
                                <td><?php echo $data['bono']; ?></td>
                                <td><?php echo $data['nombre']; ?></td>
                                <td><?php echo $data['documento']; ?></td>
                                <td><?php echo $data['nombre_municipio']; ?></td>
                                <td><?php echo $data['nombre_colegio']; ?></td>
                                <td><?php echo $data['sexo']; ?></td>
                                <td><?php echo $data['tipo_funcionario']; ?></td>
                                <td><?php echo $data['dotacion']; ?></td>
                                <td><?php echo $estado_entrega; ?></td>
                               
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

    document.addEventListener('DOMContentLoaded', function () {
        const dotacion = document.getElementById('dotacion');
        const estado = document.getElementById('estado');
        const anio = document.getElementById('anio');
        const idmunicipios = document.getElementById('id_municipios')
        const idcolegio = document.getElementById('id_colegio')
        const tablaCuerpo = document.getElementById('tabla-cuerpo');

        const actualizarTabla = async () => {
            const dotacionSeleccionada = dotacion.value;
            const estadoSeleccionado = estado.value;
            const anioSeleccionado = anio.value;
            const idmunicipiosSelecionado = idmunicipios.value;
            const idcolegioSelecionado = idcolegio.value;
            // Realizar petición AJAX
            const response = await fetch('filtrar_dotaciones.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    dotacion: dotacionSeleccionada,
                    estado: estadoSeleccionado,
                    anio: anioSeleccionado,
                    idmunicipios: idmunicipiosSelecionado,
                    idcolegio: idcolegioSelecionado
                }),
            });

            const data = await response.text();
            tablaCuerpo.innerHTML = data; // Rellenar la tabla
        };

        // Escuchar cambios en los select
       /* dotacion.addEventListener('change', actualizarTabla);
        estado.addEventListener('change', actualizarTabla);*/
        btnActualizarTabla.addEventListener('click', actualizarTabla);
        // Actualizar tabla al cargar la página
        //actualizarTabla();
    });
    $(document).ready(function() {
    // Función para el botón de exportar Excel
    $('#btnExportarExcel').click(function() {
    var dotacion = $('#dotacion').val();
    var estado = $('#estado').val();
    var id_municipios = $('#id_municipios').val();
    var id_colegio = $('#id_colegio').val();
    var anio = $('#anio').val();
    

    // Enviar datos por AJAX
    $.ajax({
        url: 'filtrar_dotaciones_excel.php', // Archivo PHP que genera el Excel
        type: 'POST',
        data: {
            dotacion: dotacion,
            estado: estado,
            id_municipios: id_municipios,
            id_colegio: id_colegio,
            anio: anio
        },
        xhrFields: {
            responseType: 'blob' // Aseguramos que la respuesta es de tipo Blob (archivo binario)
        },
        success: function(response) {
            // Crear un enlace temporal para descargar el archivo Excel
            var blob = response; // El objeto Blob recibido
            var link = document.createElement('a');
            link.href = URL.createObjectURL(blob); // Crea una URL para el archivo
            link.download = 'reporte_dotaciones.xlsx'; // Nombre del archivo de descarga
            link.click(); // Inicia la descarga
        },
        error: function(xhr, status, error) {
            console.error("Error al generar el Excel: " + error);
        }
    });
});

});
</script>
<?php include_once "includes/footer.php"; ?>
