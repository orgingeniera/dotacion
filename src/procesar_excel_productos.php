<?php
session_start();
require '../vendor/autoload.php';
require_once "../conexion.php"; // Incluye tu archivo de conexión existente

use PhpOffice\PhpSpreadsheet\IOFactory;

// Verificar si se ha cargado un archivo
if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['archivo']['tmp_name'];
    $fileName = $_FILES['archivo']['name'];
    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

    // Validar el tipo de archivo
    if (in_array($fileExtension, ['xls', 'xlsx'])) {
        try {
            // Cargar el archivo Excel
            $spreadsheet = IOFactory::load($fileTmpPath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Procesar las filas del Excel
            foreach ($rows as $index => $row) {
                if ($index == 0) {
                    // Omite la primera fila si es un encabezado
                    continue;
                }

                // Asigna los valores de las columnas a las variables
                $nombre = mysqli_real_escape_string($conexion, $row[0]);
                $talla = mysqli_real_escape_string($conexion, $row[1]);
                $existencia = mysqli_real_escape_string($conexion, $row[2]);
                $precio = mysqli_real_escape_string($conexion, $row[3]);
                $idcategoria = mysqli_real_escape_string($conexion, $row[4]);
               
                $fecha = date('Y-m-d'); // Fecha actual

                // Insertar los datos en la base de datos
                $sql = "INSERT INTO producto (codigo, descripcion, precio, existencia, id_categoria,fecha) 
                        VALUES ('$nombre', '$talla',$precio, '$existencia', '$idcategoria', '$fecha')";

                $query = mysqli_query($conexion, $sql);

                if (!$query) {
                    $_SESSION['alert'] = '<div class="alert alert-warning">Error al insertar fila:'. mysqli_error($conexion).'</div>';
                    header("Location: productos.php"); 
                }
            }

                     $_SESSION['alert']  = $query 
                    ? '<div class="alert alert-success">Productos registrados correctamente</div>' 
                    : '<div class="alert alert-danger">Error al registrar</div>';
            header("Location: productos.php"); 
        } catch (Exception $e) {
            $_SESSION['alert'] = '<div class="alert alert-warning">Error al procesar el archivo: '. $e->getMessage().'</div>';
            header("Location: productos.php"); 
        }
    } else {
        $_SESSION['alert'] = 'Error: El archivo debe ser de tipo Excel (.xls o .xlsx).';
        header("Location: productos.php"); 
    }
} else {
    $_SESSION['alert'] = 'Error: No se ha cargado ningún archivo.';
    header("Location: productos.php"); 
  
}
?>
