<?php
require_once "../conexion.php";
session_start();
$anio_actual = date('Y');
if (isset($_GET['q'])) {
    $datos = array();
    $nombre = $_GET['q'];
   
    $cliente = mysqli_query($conexion, "SELECT * FROM cliente WHERE documento LIKE '%$nombre%' AND YEAR(fecha) = '$anio_actual'");
    while ($row = mysqli_fetch_assoc($cliente)) {
        $data['id'] = $row['idcliente'];
        $data['label'] = $row['nombre'];
        $data['dotacion'] = $row['dotacion'];
        $data['documento'] = $row['documento'];
        array_push($datos, $data);
    }
    echo json_encode($datos);
    die();
}else if (isset($_GET['pro'])) {
    $datos = array();
    $nombre = $_GET['pro'];
    $producto = mysqli_query($conexion, "SELECT * FROM producto WHERE codproducto = $nombre ");
    if ($row = mysqli_fetch_assoc($producto)) {
        $data['id'] = $row['codproducto'];
        $data['existencia'] = $row['existencia'];
        $data['precio'] = $row['precio'];
        echo json_encode($data);
    } else {
        echo json_encode(null); // No se encontró ningún producto
    }
    die();
}else if (isset($_GET['detalle'])) {
    $id = $_SESSION['idUser'];
    $datos = array();
    $detalle = mysqli_query($conexion, "SELECT d.*, p.codproducto, p.codigo, p.descripcion FROM detalle_temp d INNER JOIN producto p ON d.id_producto = p.codproducto WHERE d.id_usuario = $id");
    while ($row = mysqli_fetch_assoc($detalle)) {
        $data['id'] = $row['id'];
        $data['descripcion'] = $row['codigo'];
        $data['cantidad'] = $row['cantidad'];
        $data['talla'] = $row['descripcion'];
       /* $data['descuento'] = $row['descuento'];*/
        $data['precio_venta'] = $row['precio_venta'];
        $data['sub_total'] = $row['total'];
        array_push($datos, $data);
    }
    echo json_encode($datos);
    die();
} else if (isset($_GET['delete_detalle'])) {
    $id_detalle = $_GET['id'];
    $query = mysqli_query($conexion, "DELETE FROM detalle_temp WHERE id = $id_detalle");
    if ($query) {
        $msg = "ok";
    } else {
        $msg = "Error";
    }
    echo $msg;
    die();
} else if (isset($_GET['procesarVenta'])) {
    $id_cliente = $_GET['id'];
    $dotaciones = $_GET['dotaciones'];
    $documentos = $_GET['documentos'];
    $id_user = $_SESSION['idUser'];
    $consultaValidacion = mysqli_query($conexion, "
       SELECT * FROM ventas 
        WHERE id_cliente = $id_cliente 
        AND dataciones = '$dotaciones' 
        AND YEAR(fecha) = $anio_actual
    ");
 
    if (mysqli_num_rows($consultaValidacion) > 0) {
        // Si ya existe, enviar un mensaje de error
        $msg = array('mensaje' => '2');
        echo json_encode($msg);
        die();
    } else {
        $consulta = mysqli_query($conexion, "SELECT total, SUM(total) AS total_pagar FROM detalle_temp WHERE id_usuario = $id_user");
        $result = mysqli_fetch_assoc($consulta);
        $total = $result['total_pagar'];
        $insertar = mysqli_query($conexion, "INSERT INTO ventas(id_cliente, total, id_usuario, dataciones) VALUES ($id_cliente, '$total', $id_user, '$dotaciones')");
    
        if ($insertar) {
            $id_maximo = mysqli_query($conexion, "SELECT MAX(id) AS total FROM ventas");
            $resultId = mysqli_fetch_assoc($id_maximo);
            $ultimoId = $resultId['total'];
            $consultaDetalle = mysqli_query($conexion, "SELECT * FROM detalle_temp WHERE id_usuario = $id_user");
            while ($row = mysqli_fetch_assoc($consultaDetalle)) {
                $id_producto = $row['id_producto'];
                $cantidad = $row['cantidad'];
                $desc = $row['descuento'];
                $precio = $row['precio_venta'];
                $total = $row['total'];
                $insertarDet = mysqli_query($conexion, "INSERT INTO detalle_venta (id_producto, id_venta, cantidad, precio, descuento, total) VALUES ($id_producto, $ultimoId, $cantidad, '$precio', '$desc', '$total')");
                $stockActual = mysqli_query($conexion, "SELECT * FROM producto WHERE codproducto = $id_producto");
                $stockNuevo = mysqli_fetch_assoc($stockActual);
                $stockTotal = $stockNuevo['existencia'] - $cantidad;
                $stock = mysqli_query($conexion, "UPDATE producto SET existencia = $stockTotal WHERE codproducto = $id_producto");
                //actualizo el estado de la dotacion
                $dotacion_entregadaActual = mysqli_query($conexion, "UPDATE cliente SET dotacion_entregada = 1, fecha_entrega = CURDATE()  WHERE documento = $documentos AND dotacion='$dotaciones' AND  YEAR(fecha) = '$anio_actual'");
                
            
            } 
            if ($insertarDet) {
                $eliminar = mysqli_query($conexion, "DELETE FROM detalle_temp WHERE id_usuario = $id_user");
                $msg = array('id_cliente' => $id_cliente, 'id_venta' => $ultimoId);
            } 
        }else{
            $msg = array('mensaje' => '1');
        }


        echo json_encode($msg);
        die();
   } 
}else if (isset($_GET['descuento'])) {
    $id = $_GET['id'];
    $desc = $_GET['desc'];
    $consulta = mysqli_query($conexion, "SELECT * FROM detalle_temp WHERE id = $id");
    $result = mysqli_fetch_assoc($consulta);
    $total_desc = $desc + $result['descuento'];
    $total = $result['total'] - $desc;
    $insertar = mysqli_query($conexion, "UPDATE detalle_temp SET descuento = $total_desc, total = '$total'  WHERE id = $id");
    if ($insertar) {
        $msg = array('mensaje' => 'descontado');
    }else{
        $msg = array('mensaje' => 'error');
    }
    echo json_encode($msg);
    die();
}else if(isset($_GET['editarCliente'])){
    $idcliente = $_GET['id'];
    $sql = mysqli_query($conexion, "SELECT * FROM cliente WHERE idcliente = $idcliente");
    $data = mysqli_fetch_array($sql);
    echo json_encode($data);
    exit;
} else if (isset($_GET['editarUsuario'])) {
    $idusuario = $_GET['id'];
    $sql = mysqli_query($conexion, "SELECT * FROM usuario WHERE idusuario = $idusuario");
    $data = mysqli_fetch_array($sql);
    echo json_encode($data);
    exit;
} else if (isset($_GET['editarProducto'])) {
    $id = $_GET['id'];
    $sql = mysqli_query($conexion, "SELECT * FROM producto WHERE codproducto = $id");
    $data = mysqli_fetch_array($sql);
    echo json_encode($data);
    exit;
}else if (isset($_GET['editarProveedor'])) {
    $id = $_GET['id'];
    $sql = mysqli_query($conexion, "SELECT * FROM proveedor WHERE idproveedor = $id");
    if ($sql) {
        $data = mysqli_fetch_array($sql);
        
        echo json_encode($data);
    } else {
        echo json_encode(["error" => "Proveedor no encontrado"]);
    }
    exit;
}else if (isset($_GET['editarCategoria'])) {
    $id = $_GET['id'];
    $sql = mysqli_query($conexion, "SELECT * FROM categoria WHERE id_categoria = $id");
    $data = mysqli_fetch_array($sql);
    echo json_encode($data);
    exit;
}else if (isset($_GET['editarMunicipio'])) {
    $id = $_GET['id'];
    $sql = mysqli_query($conexion, "SELECT * FROM municipios WHERE id_municipios = $id");
    $data = mysqli_fetch_array($sql);
    echo json_encode($data);
    exit;
}
else if (isset($_GET['editarColegio'])) {
    $id = $_GET['id'];
    $sql = mysqli_query($conexion, "SELECT * FROM colegios WHERE id_colegios = $id");
    $data = mysqli_fetch_array($sql);
    echo json_encode($data);
    exit;
}
if (isset($_POST['regDetalle'])) {
    
    $id = $_POST['id'];
    $cant = $_POST['cant'];
    $precio = $_POST['stock'];
    $precio = $_POST['precio'];
    $id_user = $_SESSION['idUser'];
    //$total =   $cant - $precio;
    $totaln = $precio * $cant;
    $verificar = mysqli_query($conexion, "SELECT * FROM detalle_temp WHERE id_producto = $id AND id_usuario = $id_user");
    $result = mysqli_num_rows($verificar);
    $datos = mysqli_fetch_assoc($verificar);

    if ($result > 0) {
       
        $cantidad = $datos['cantidad'] + $cant;
        $total_precio = ($cantidad * $totaln);
       // $total_precio = ($cantidad * $total);
        $query = mysqli_query($conexion, "UPDATE detalle_temp SET cantidad = $cantidad, total = '$total_precio' WHERE id_producto = $id AND id_usuario = $id_user");
        if ($query) {
                    // Consultar la existencia en la tabla `producto`
            $consulta_existencia = mysqli_query($conexion, "SELECT existencia FROM producto WHERE codproducto = $id");
            $producto = mysqli_fetch_assoc($consulta_existencia);

            if ($producto) {
                $existencia = $producto['existencia'];

                // Verificar si la existencia es menor que la cantidad actualizada
            if ($existencia < $cantidad) {
                    // Si no hay suficiente existencia
                    $msg = "errorexistencia";
                    
                // Opcional: puedes deshacer la actualización en `detalle_temp` si deseas
                mysqli_query($conexion, "UPDATE detalle_temp SET cantidad = cantidad - $cant, total = total - ($cant * $totaln) WHERE id_producto = $id AND id_usuario = $id_user");
            } else {
                // Si todo está correcto
                $msg = "actualizado";
            }
        } else {
            $msg = "Error al ingresar";
        }
    }
  }else{
        $query = mysqli_query($conexion, "INSERT INTO detalle_temp(id_usuario, id_producto, cantidad ,precio_venta, total) VALUES ($id_user, $id, $cant,'$precio', '$totaln')");
        if ($query) {
            $msg = "registrado";
        }else{
            $msg = "Error al ingresar";
        }
    }
    echo json_encode($msg);
    die();
}else if (isset($_POST['cambio'])) {
    if (empty($_POST['actual']) || empty($_POST['nueva'])) {
        $msg = 'Los campos estan vacios';
    } else {
        $id = $_SESSION['idUser'];
        $actual = md5($_POST['actual']);
        $nueva = md5($_POST['nueva']);
        $consulta = mysqli_query($conexion, "SELECT * FROM usuario WHERE clave = '$actual' AND idusuario = $id");
        $result = mysqli_num_rows($consulta);
        if ($result == 1) {
            $query = mysqli_query($conexion, "UPDATE usuario SET clave = '$nueva' WHERE idusuario = $id");
            if ($query) {
                $msg = 'ok';
            }else{
                $msg = 'error';
            }
        } else {
            $msg = 'dif';
        }
        
    }
    echo $msg;
    die();
    
}else if (isset($_GET['borrartemp'])) {
    $id = $_SESSION['idUser'];
    $eliminar = mysqli_query($conexion, "DELETE FROM detalle_temp WHERE id_usuario = $id");

    // Verifica si se eliminó algún registro
    if (mysqli_affected_rows($conexion) > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Registro eliminado exitosamente']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No se encontró ningún registro para eliminar']);
    }
    exit;
}

