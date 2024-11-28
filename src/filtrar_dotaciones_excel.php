<?php
require_once "../conexion.php";
require '../vendor/autoload.php'; // Asegúrate de que el archivo autoload de Composer esté incluido.

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Obtener datos del cuerpo de la solicitud
$input = json_decode(file_get_contents('php://input'), true);
// Obtener datos del formulario enviados por POST
$dotacion = $_POST['dotacion'] ?? '';
$estado = $_POST['estado'] ?? '';
$anio = $_POST['anio'] ?? '';
$fecha_inicio = $_POST['fechaInicio'] ?? '';
$fecha_fin = $_POST['fechaFin'] ?? '';
$id_municipios = $_POST['id_municipios'] ?? '';
$id_colegio = $_POST['id_colegio'] ?? '';
$tipo_funcionario = $_POST['tipofuncionario'] ?? '';

$filtro = "WHERE 1=1";
if ($dotacion) $filtro .= " AND cliente.dotacion = '$dotacion'";
if ($estado) $filtro .= " AND cliente.dotacion_entregada = $estado";
if ($anio) $filtro .= " AND YEAR(cliente.fecha) = '$anio'";
if ($fecha_inicio && $fecha_fin) $filtro .= " AND cliente.fecha_entrega IS NOT NULL AND cliente.fecha_entrega BETWEEN '$fecha_inicio' AND '$fecha_fin'";
if ($id_municipios) $filtro .= " AND cliente.id_municipios = '$id_municipios'";
if ($id_colegio) $filtro .= " AND cliente.id_colegio = '$id_colegio'";
if ($tipo_funcionario) $filtro .= " AND cliente.tipo_funcionario = '$tipo_funcionario'";

$query = "SELECT 
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
          $filtro";

$result = mysqli_query($conexion, $query);

// Crear un nuevo archivo de Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Encabezados de la tabla
$headers = ['Bono', 'Nombre', 'Documento', 'Municipio', 'Colegio', 'Sexo', 'Tipo Funcionario', 'Dotación', 'Estado Entrega'];
$sheet->fromArray($headers, NULL, 'A1');

// Agregar los datos a la hoja
$rowNumber = 2;
while ($row = mysqli_fetch_assoc($result)) {
    $estado_entrega = ($row['dotacion_entregada'] == 1 ? 'Entregada' : 'No Entregada');
    $sheet->fromArray([
        $row['bono'],
        $row['nombre'],
        $row['documento'],
        $row['nombre_municipio'],
        $row['nombre_colegio'],
        $row['sexo'],
        $row['tipo_funcionario'],
        $row['dotacion'],
        $estado_entrega
    ], NULL, 'A' . $rowNumber);
    $rowNumber++;
}

// Crear un archivo Excel y enviarlo al navegador
$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="reporte_dotaciones.xlsx"');
header('Cache-Control: max-age=0');

// Limpiar el búfer de salida y evitar cualquier otra salida antes de enviar el archivo
ob_clean(); 
flush();

$writer->save('php://output'); // Enviar el archivo directamente al navegador
exit;
?>
