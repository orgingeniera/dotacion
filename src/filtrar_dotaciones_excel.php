<?php
require_once "../conexion.php";
require '../vendor/autoload.php'; // Asegúrate de que el archivo autoload de Composer esté incluido.

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Obtener datos del cuerpo de la solicitud
$input = json_decode(file_get_contents('php://input'), true);
// Obtener datos del formulario enviados por POST

$dotacion = $_POST['dotacion'];
$estado = $_POST['estado'];
$anio = $_POST['anio'];
$id_municipios = $_POST['id_municipios'];
$id_colegio = $_POST['id_colegio'];

// Construcción de la consulta base
$query = "
    SELECT 
        c.dotacion_entregada, 
        c.bono, 
        c.nombre, 
        c.documento, 
        m.nombre AS nombre_municipio, 
        col.nombre AS nombre_colegio, 
        c.sexo, 
        c.tipo_funcionario, 
        c.dotacion
    FROM cliente c
    LEFT JOIN municipios m ON c.id_municipios = m.id_municipios
    LEFT JOIN colegios col ON c.id_colegio = col.id_colegios
    WHERE YEAR(c.fecha) = '$anio' 
      AND c.dotacion LIKE '%$dotacion%' 
";

// Filtrar según el estado de la dotación
if ($estado == "dotado") {
    $query .= " AND c.dotacion_entregada = 1";
} elseif ($estado == "sin_dotacion") {
    $query .= " AND c.dotacion_entregada = 0";
}

// Filtrar según el municipio si se seleccionó uno
if (!empty($id_municipios)) {
    $query .= " AND c.id_municipios = '$id_municipios'";
}

// Filtrar según el colegio si se seleccionó uno
if (!empty($id_colegio)) {
    $query .= " AND c.id_colegio = '$id_colegio'";
}

$query .= " ORDER BY c.bono ASC";

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
