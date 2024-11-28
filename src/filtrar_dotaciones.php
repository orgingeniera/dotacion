<?php
require_once "../conexion.php";
$data = json_decode(file_get_contents("php://input"), true);
$dotacion = $data['dotacion'] ?? '';
$estado = $data['estado'] ?? '';
$anio = $data['anio'] ?? '';
$fecha_inicio = $data['fechaInicio'] ?? '';
$fecha_fin = $data['fechaFin'] ?? '';
$id_municipios = $data['idmunicipios'] ?? '';
$id_colegio = $data['idcolegio'] ?? '';
$tipo_funcionario = $data['tipofuncionario'] ?? '';

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
$output = '';
while ($row = mysqli_fetch_assoc($result)) {
    $estado_entrega = $row['dotacion_entregada'] == 1 ? 'Entregada' : 'No Entregada';
    $output .= "<tr>
                    <td>{$row['bono']}</td>
                    <td>{$row['nombre']}</td>
                    <td>{$row['documento']}</td>
                    <td>{$row['nombre_municipio']}</td>
                    <td>{$row['nombre_colegio']}</td>
                    <td>{$row['sexo']}</td>
                    <td>{$row['tipo_funcionario']}</td>
                    <td>{$row['dotacion']}</td>
                    <td>$estado_entrega</td>
                </tr>";
}
echo $output;
?>
