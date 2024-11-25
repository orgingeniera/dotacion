<?php
if (empty($_SESSION['active'])) {
    header('Location: ../');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Panel de Administración</title>
    <link href="../assets/css/material-dashboard.css" rel="stylesheet" />
    <link href="../assets/css/select2.css" rel="stylesheet" />

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
     <link href="../assets/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
    <link rel="stylesheet" href="../assets/js/jquery-ui/jquery-ui.min.css">
    <script src="../assets/js/selec2.min.js" crossorigin="anonymous"></script>
    <script src="../assets/js/all.min.js" crossorigin="anonymous"></script>
<style>
    /* Fondo personalizado para el menú desplegable */
.custom-dropdown-menu {
    background-color: #343a40; /* Gris oscuro */
    border-radius: 8px; /* Bordes redondeados */
    padding: 10px 0; /* Espaciado interno */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2); /* Sombra */
}

/* Estilo para cada elemento del submenú */
.custom-dropdown-item {
    color: #ffffff; /* Texto blanco */
    padding: 10px 20px; /* Espaciado interno */
    transition: background-color 0.3s ease; /* Transición suave */
}

.custom-dropdown-item:hover {
    background-color: #495057; /* Color de fondo al pasar el ratón */
    color: #f8f9fa; /* Color de texto al pasar el ratón */
}

/* Espaciado entre los elementos */
.custom-dropdown-item + .custom-dropdown-item {
    margin-top: 5px; /* Separación entre los elementos */
}
</style>
</head>

<body>
    <div class="wrapper ">
        <div class="sidebar" data-color="purple" data-background-color="black" data-image="../assets/img/sidebar-1.jpg">
            <div class="logo"><a href="./" class="simple-text logo-normal">
                    Sistemas Free
                </a></div>
            <div class="sidebar-wrapper">
                <ul class="nav">
                    <li class="nav-item">
                        <a class="nav-link d-flex" href="usuarios.php">
                            <i class="fas fa-user mr-2 fa-2x"></i>
                            <p> Usuarios</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex" href="config.php">
                            <i class="fas fa-cogs mr-2 fa-2x"></i>
                            <p> Configuración</p>
                        </a>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link d-flex dropdown-toggle" href="#" id="ventasMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-cash-register mr-2 fa-2x"></i>
                            <p> Docentes</p>
                        </a>
                        <div class="dropdown-menu custom-dropdown-menu" aria-labelledby="ventasMenu">
                            <a class="dropdown-item custom-dropdown-item" href="docentes.php">
                                <i class="fas fa-cash-register mr-2"></i>Administrar Docentes
                            </a>
                           <a class="nav-link d-flex" href="municipios.php">
                            <i class="fab fa-product-hunt mr-2 fa-2x"></i>
                            <p> municipios</p>
                             </a>
                             <a class="nav-link d-flex" href="colegios.php">
                            <i class="fab fa-product-hunt mr-2 fa-2x"></i>
                            <p> Colegios</p>
                             </a>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link d-flex" href="categorias.php">
                            <i class=" fas fa-users mr-2 fa-2x"></i>
                            <p> Categorias</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex" href="productos.php">
                            <i class="fab fa-product-hunt mr-2 fa-2x"></i>
                            <p> Productos</p>
                        </a>
                    </li>
                   
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link d-flex dropdown-toggle" href="#" id="ventasMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-cash-register mr-2 fa-2x"></i>
                            <p> Ventas</p>
                        </a>
                        <div class="dropdown-menu custom-dropdown-menu" aria-labelledby="ventasMenu">
                            <a class="dropdown-item custom-dropdown-item" href="ventas.php">
                                <i class="fas fa-cash-register mr-2"></i> Nueva Venta
                            </a>
                            <a class="dropdown-item custom-dropdown-item" href="lista_ventas.php">
                                <i class="fas fa-cart-plus mr-2"></i> Historial Ventas
                            </a>
                        </div>
                    </li>


                </ul>
            </div>
        </div>
        <div class="main-panel">
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg navbar-absolute fixed-top bg-dark">
                <div class="container-fluid">
                    <div class="navbar-wrapper">
                        <a class="navbar-brand" href="javascript:;">Sistema de Venta</a>
                    </div>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="navbar-toggler-icon icon-bar"></span>
                        <span class="navbar-toggler-icon icon-bar"></span>
                        <span class="navbar-toggler-icon icon-bar"></span>
                    </button>
                    <div class="collapse navbar-collapse justify-content-end">

                        <ul class="navbar-nav">
                            <li class="nav-item dropdown">
                                <a class="nav-link" href="javascript:;" id="navbarDropdownProfile" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-user"></i>
                                    <p class="d-lg-none d-md-block">
                                        Cuenta
                                    </p>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownProfile">
                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#nuevo_pass">Perfil</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="salir.php">Cerrar Sesión</a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            <!-- End Navbar -->
            <div class="content">
                <div class="container-fluid">