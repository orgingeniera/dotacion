<?php
session_start();
require_once "../conexion.php";
if (!isset($_SESSION['idUser'])) {
    // Redirigir al usuario al index si no está definido
    header("Location: index.php");
    exit;
}
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
                    <option value="true">Entregada</option>
                    <option value="false">No Entregada</option>
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
                    <label for="tipo_funcionario" class="font-weight-bold">Tipo Funcionario</label>
                    <select name="tipo_funcionario" required id="tipo_funcionario" class="form-control">
                        <option value="">Seleccione</option>
                        <option value="ADMINISTRATIVO">ADMINISTRATIVO</option>
                        <option value="DOCENTE">DOCENTE</option>
                        <option value="DIRECTIVO DOCENTE">DIRECTIVO DOCENTE</option>
                    </select>
                </div>
                
        
                    <!-- <div class="col-md-6">
            <label for="anio">Seleccione Año:</label>
                <select id="anio" class="form-control">
                    <option value="2024">2024</option>
                    <option value="2025">2025</option>
                </select>
            </div>-->
            

        </div>
        <div class="row mb-3">
            <div class="col-md-12">
                <label class="font-weight-bold">Seleccione el tipo de consulta:</label>
                <div>
                    <input type="radio" id="radioAnio" name="tipoConsulta" value="anio" checked>
                    <label for="radioAnio">Por Año</label>

                    <input type="radio" id="radioIntervalo" name="tipoConsulta" value="intervalo">
                    <label for="radioIntervalo">Por Intervalo de Fechas</label>
                </div>
            </div>
        </div>
        <div class="row mb-3" id="anio-container">
            <div class="col-md-6">
                <label for="anio">Seleccione Año:</label>
                <select id="anio" class="form-control">
                    <option value="2024">2024</option>
                    <option value="2025">2025</option>
                </select>
            </div>
        </div>

        <div class="row mb-3 d-none" id="intervalo-container">
            <div class="col-md-6">
                <label for="fechaInicio">Fecha Inicio:</label>
                <input type="date" id="fechaInicio" class="form-control">
            </div>
            <div class="col-md-6">
                <label for="fechaFin">Fecha Fin:</label>
                <input type="date" id="fechaFin" class="form-control">
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
    const radioAnio = document.getElementById('radioAnio');
    const radioIntervalo = document.getElementById('radioIntervalo');
    const anioContainer = document.getElementById('anio-container');
    const intervaloContainer = document.getElementById('intervalo-container');

    // Cambiar entre Año y Fecha según la selección
    const toggleInputs = () => {
        if (radioAnio.checked) {
            anioContainer.classList.remove('d-none');
            intervaloContainer.classList.add('d-none');
        } else if (radioIntervalo.checked) {
            intervaloContainer.classList.remove('d-none');
            anioContainer.classList.add('d-none');
        }
    };

    // Listeners para los radios
    radioAnio.addEventListener('change', toggleInputs);
    radioIntervalo.addEventListener('change', toggleInputs);

    // Inicializar estado al cargar
    toggleInputs();

    // Modificar la función actualizarTabla para enviar los datos correctos
    const btnActualizarTabla = document.getElementById('btnActualizarTabla');
    btnActualizarTabla.addEventListener('click', async () => {
        const dotacion = document.getElementById('dotacion').value;
        const estado = document.getElementById('estado').value;
        const idmunicipios = document.getElementById('id_municipios').value;
        const idcolegio = document.getElementById('id_colegio').value;
        const tipofuncionario = document.getElementById('tipo_funcionario').value;
        
        let anioSeleccionado = null;
        let fechaInicio = null;
        let fechaFin = null;

        if (radioAnio.checked) {
            anioSeleccionado = document.getElementById('anio').value;
        } else if (radioIntervalo.checked) {
          
            fechaInicio = document.getElementById('fechaInicio').value;
            fechaFin = document.getElementById('fechaFin').value;
        }

        // Validar datos antes de enviar
        if (radioIntervalo.checked && (!fechaInicio || !fechaFin)) {
            alert('Por favor, seleccione el intervalo de fechas.');
            return;
        }
          // Realizar petición AJAX
        const response = await fetch('filtrar_dotaciones.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                dotacion,
                estado,
                idmunicipios,
                idcolegio,
                tipofuncionario,
                anio: anioSeleccionado,
                fechaInicio,
                fechaFin
            }),
        });

        const data = await response.text();
        document.getElementById('tabla-cuerpo').innerHTML = data; // Rellenar la tabla
    });
});


    $(document).ready(function() {
        // Función para el botón de exportar Excel
        $('#btnExportarExcel').click(function() {
            const radioAnio = document.getElementById('radioAnio');
            const radioIntervalo = document.getElementById('radioIntervalo');
            let anioSeleccionado = null;
            var fechaInicio = null;
            var fechaFin = null;

            if (radioAnio.checked) {
                anioSeleccionado = document.getElementById('anio').value;
            } else if (radioIntervalo.checked) {
            console.log("entro")
                fechaInicio = document.getElementById('fechaInicio').value;
                fechaFin = document.getElementById('fechaFin').value;
            }
                // Validar datos antes de enviar
            if (radioIntervalo.checked && (!fechaInicio || !fechaFin)) {
                alert('Por favor, seleccione el intervalo de fechas.');
                return;
            }
            var dotacion = $('#dotacion').val();
            var estado = $('#estado').val();
            var id_municipios = $('#id_municipios').val();
            var id_colegio = $('#id_colegio').val();
            var tipofuncionario = $('#tipo_funcionario').val();

            //var anio = $('#anio').val();

            // Enviar datos por AJAX
            $.ajax({
                url: 'filtrar_dotaciones_excel.php', // Archivo PHP que genera el Excel
                type: 'POST',
                data: {
                    dotacion: dotacion,
                    estado: estado,
                    id_municipios: id_municipios,
                    id_colegio: id_colegio,
                    anio: anioSeleccionado,
                    fechaInicio: fechaInicio,
                    tipofuncionario: tipofuncionario,
                    fechaFin: fechaFin
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
