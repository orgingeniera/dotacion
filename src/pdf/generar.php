<?php
require_once '../../conexion.php';
require_once 'fpdf/fpdf.php';
$pdf = new FPDF('P', 'mm', array(80, 200));
$pdf->AddPage();
$pdf->SetMargins(5, 0, 0);
$pdf->SetTitle("Ventas");
$pdf->SetFont('Arial', 'B', 12);
$id = $_GET['v'];
$idcliente = $_GET['cl'];
$config = mysqli_query($conexion, "SELECT * FROM configuracion");
$datos = mysqli_fetch_assoc($config);
$clientes = mysqli_query($conexion, "SELECT * FROM cliente WHERE idcliente = $idcliente");
$datosC = mysqli_fetch_assoc($clientes);
$ventasid = mysqli_query($conexion, "SELECT * FROM ventas WHERE id = $id");
$ventaidotacion = mysqli_fetch_assoc($ventasid);
$ventas = mysqli_query($conexion, "SELECT d.*, p.codproducto, p.descripcion, p.codigo FROM detalle_venta d INNER JOIN producto p ON d.id_producto = p.codproducto WHERE d.id_venta = $id");
$pdf->Cell(65, 5, mb_convert_encoding($datos['nombre'], 'ISO-8859-1', 'UTF-8'));
//$pdf->image("../../assets/img/logo.png", 60, 15, 15, 15, 'PNG');
$pdf->SetFont('Arial', 'B', 7);
$pdf->Cell(15, 5, utf8_decode(" "), 0, 1, 'L');
$pdf->SetFont('Arial', '', 7);
$pdf->Cell(15, 5, "Telefono: ".$datos['telefono'], 0, 1, 'L');
$pdf->SetFont('Arial', 'B', 7);
$pdf->Cell(15, 5, utf8_decode("Dirección: "), 0, 0, 'L');
$pdf->SetFont('Arial', '', 7);
$pdf->Cell(15, 5, utf8_decode($datos['direccion']), 0, 1, 'L');
$pdf->SetFont('Arial', 'B', 7);
$pdf->Cell(15, 5, "Correo: ", 0, 0, 'L');
$pdf->SetFont('Arial', '', 7);
$pdf->Cell(15, 5, utf8_decode($datos['email']), 0, 1, 'L');
$pdf->Ln();
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetFillColor(0, 0, 0);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(70, 5, "Datos del cliente", 0, 1, 'C', 0);
$pdf->SetTextColor(0, 0, 0);

$pdf->SetFont('Arial', '', 7);
$pdf->Cell(30, 5, utf8_decode($datosC['nombre']), 0, 1, 'L');
$pdf->Cell(20, 5, utf8_decode('Bono'), 0, 0, 'L');
$pdf->Cell(20, 5, utf8_decode('Dotación'), 0, 1, 'L');
$pdf->SetFont('Arial', '', 7);

$pdf->Cell(20, 5, utf8_decode($datosC['bono']), 0, 0, 'L');
$pdf->Cell(20, 5, utf8_decode($ventaidotacion['dataciones']), 0, 1, 'L');


$pdf->Ln(3);
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(75, 5, "Detalle de Producto", 0, 1, 'C', 0); // Mantiene el borde solo para el título
$pdf->SetTextColor(0, 0, 0);

// Encabezados
$pdf->Cell(30, 5, utf8_decode('Producto'), 0, 0, 'L'); // Sin bordes
$pdf->Cell(30, 5, utf8_decode('Talla'), 0, 0, 'L');   // Sin bordes
$pdf->Cell(30, 5, utf8_decode('Cantidad'), 0, 1, 'L'); // Sin bordes
$pdf->SetFont('Arial', '', 7);

// Filas dinámicas
while ($row = mysqli_fetch_assoc($ventas)) {
    $pdf->Cell(30, 5, $row['codigo'], 0, 0, 'L');         // Producto (sin bordes)
    $pdf->Cell(30, 5, utf8_decode($row['descripcion']), 0, 0, 'L'); // Talla (sin bordes)
    $pdf->Cell(30, 5, $row['cantidad'], 0, 1, 'L');      // Cantidad (sin bordes)
}



$pdf->Output("ventas.pdf", "I");

?>