<?php
session_start();
if (!empty($_SESSION['active'])) {
    header('location: src/');
} else {
    if (!empty($_POST)) {
        $alert = '';
        if (empty($_POST['usuario']) || empty($_POST['clave'])) {
            $alert = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        Ingrese usuario y contraseña
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
        } else {
            require_once "conexion.php";
            $user = mysqli_real_escape_string($conexion, $_POST['usuario']);
            $clave = md5(mysqli_real_escape_string($conexion, $_POST['clave']));
            $query = mysqli_query($conexion, "SELECT * FROM usuario WHERE usuario = '$user' AND clave = '$clave'");
            mysqli_close($conexion);
            $resultado = mysqli_num_rows($query);
            if ($resultado > 0) {
                $dato = mysqli_fetch_array($query);
                $_SESSION['active'] = true;
                $_SESSION['idUser'] = $dato['idusuario'];
                $_SESSION['nombre'] = $dato['nombre'];
                $_SESSION['user'] = $dato['usuario'];
                $_SESSION['tipo'] = $dato['tipo'];
                if($dato['tipo'] == 1){
                      header('Location: src/');
                }else{
                    header('Location: src/ventas.php');
                }
            } else {
                $alert = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Usuario o contraseña incorrecta.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
                session_destroy();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/material-dashboard.css">

    <style>
        body {
        background: url('assets/img/dotacion.png') no-repeat center center fixed;
        background-size: cover;
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
    }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .login-container .title {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 20px;
        }
        .input-field {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .input-field:focus {
            border-color: #007bff;
            outline: none;
        }
        .button input {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .button input:hover {
            background-color: #0056b3;
        }
        .checkbox-container {
            text-align: center;
        }
        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <center><b class="title">Iniciar Sesión</b><br>Dotación docente</center>
        <form action="" id="login-form" method="POST">
            <div class="user-details">
                <div class="input-box">
                    <input type="text" class="input-field" name="usuario" id="usuario" placeholder="Usuario" autocomplete="off" required>
                </div>
                <div class="input-box">
                    <input type="password" class="input-field" name="clave" id="clave" placeholder="Contraseña" autocomplete="off" required>
                </div>
                <?php echo (isset($alert)) ? $alert : '' ; ?>
                <div class="button">
                    <input type="submit" value="Login">
                </div><br>
                <center>
                <a 
                    class="btn btn-primary d-inline-flex align-items-center" 
                    target="_blank" 
                    href="assets/archivos/MANUAL DE USUARIO SOFTWARE DOTACION DOCENTE.pdf">
                    <i class="bi bi-file-earmark-text me-2"></i> Ver documentación del sistema
                </a>
                </center>
            </div>
        </form>
        <div class="checkbox-container">
           <!-- <input type="checkbox" id="toggle" onclick="changeMode();"> Cambiar Modo-->
        </div>
    </div>

    <script src="assets/js/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
    <script src="assets/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    <script>
        var rootProp = document.documentElement.style;
        var mode = true;

        function changeMode() {
            if (mode) {
                darkMode();
            } else {
                lightMode();
            }
            mode = !mode;
        }

        function lightMode() {
            rootProp.setProperty("--background1", "rgba(230, 230, 230)");
            rootProp.setProperty("--shadow1", "rgba(119, 119, 119, 0.5)");
            rootProp.setProperty("--shadow2", "rgba(255, 255, 255, 0.85)");
            rootProp.setProperty("--labelColor", "black");
        }

        function darkMode() {
            rootProp.setProperty("--background1", "rgb(9 25 33)");
            rootProp.setProperty("--shadow1", "rgb(0 0 0 / 45%)");
            rootProp.setProperty("--shadow2", "rgb(255 255 255 / 12%)");
            rootProp.setProperty("--labelColor", "rgb(255 255 255 / 59%)");
        }
    </script>

</body>
</html>
