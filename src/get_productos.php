<?php
include "../conexion.php";
    $query = mysqli_query($conexion, "SELECT codigo,codproducto  FROM producto");
    $productos = [];
    while ($row = mysqli_fetch_assoc($query)) {
        $productos[] = $row;
    }
    echo json_encode($productos);

?>
