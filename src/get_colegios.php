<?php
include "../conexion.php";

if (isset($_GET['municipio'])) {
    $id_municipio = intval($_GET['municipio']);
    $query = mysqli_query($conexion, "SELECT id_colegios, nombre FROM colegios WHERE id_municipios = $id_municipio");
    $colegios = [];
    while ($row = mysqli_fetch_assoc($query)) {
        $colegios[] = $row;
    }
    echo json_encode($colegios);
}
?>
