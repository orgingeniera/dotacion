<?php
require_once "../conexion.php";

// Obtener datos del cuerpo de la solicitud
$input = json_decode(file_get_contents('php://input'), true);
$dotacion = $input['dotacion']; // Dotación seleccionada
$estado = $input['estado']; // Estado (dotado o sin dotación)
$anio = $input['anio']; // Año seleccionado
$id_municipios = $input['idmunicipios']; // Municipio seleccionado
$id_colegio = $input['idcolegio']; // Colegio seleccionado


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
    $query .= " AND c.dotacion_entregada = 1";  // Solo los que tienen la dotación entregada
} elseif ($estado == "sin_dotacion") {
    $query .= " AND c.dotacion_entregada = 0";  // Solo los que no han recibido la dotación
}

// Filtrar según el municipio si se seleccionó uno
if (!empty($id_municipios)) {
    $query .= " AND c.id_municipios = '$id_municipios'";
}

// Filtrar según el colegio si se seleccionó uno
if (!empty($id_colegio)) {
    $query .= " AND c.id_colegio = '$id_colegio'";
}

$query .= " ORDER BY c.nombre ASC";

$result = mysqli_query($conexion, $query);

// Construir tabla para mostrar resultados
$html = "";
while ($row = mysqli_fetch_assoc($result)) {
    // Determinar el estado de la dotación
    $estado_entrega = ($row['dotacion_entregada'] == 1 ? 'Entregada' : 'No Entregada');
    
    // Crear el HTML para la fila de la tabla con los nuevos datos
    $html .= "<tr>
                <td>{$row['bono']}</td>
                <td>{$row['nombre']}</td>
                <td>{$row['documento']}</td>
                <td>{$row['nombre_municipio']}</td>
                <td>{$row['nombre_colegio']}</td>
                <td>{$row['sexo']}</td>
                <td>{$row['tipo_funcionario']}</td>
                <td>{$row['dotacion']}</td>
                <td>{$estado_entrega}</td>
              </tr>";
}

echo $html;
?>
