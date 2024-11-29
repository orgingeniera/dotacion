-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 28-11-2024 a las 16:48:45
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sistema`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `id_categoria` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`id_categoria`, `nombre`) VALUES
(1, 'zapatos'),
(3, 'blusas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `idcliente` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `documento` varchar(15) NOT NULL,
  `id_municipios` varchar(200) NOT NULL,
  `bono` varchar(30) NOT NULL,
  `id_colegio` int(15) NOT NULL,
  `sexo` varchar(10) NOT NULL,
  `tipo_funcionario` varchar(20) NOT NULL,
  `dotacion` int(5) NOT NULL,
  `fecha` date DEFAULT NULL,
  `fecha_entrega` date DEFAULT NULL,
  `dotacion_entregada` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`idcliente`, `nombre`, `documento`, `id_municipios`, `bono`, `id_colegio`, `sexo`, `tipo_funcionario`, `dotacion`, `fecha`, `fecha_entrega`, `dotacion_entregada`) VALUES
(49, 'IMER JOSEFA MARTINEZ CHOLES', '40923432', '5', '0073', 13, 'F', 'ADMINISTRATIVO', 2, '2024-11-28', '2024-11-28', 1),
(50, 'DANIVIS LEOMAR GARCIA NARVAEZ', '40924486', '5', '0074', 13, 'F', 'ADMINISTRATIVO', 2, '2024-11-28', NULL, 0),
(51, 'YOGLEDIS JOSEFINA ROMERO MOSCOTE', '40933381', '5', '0075', 13, 'F', 'DOCENTE', 2, '2024-11-28', '2024-11-28', 1),
(52, 'ALCIDES RAFAEL REDONDO REDONDO', '17807693', '5', '0076', 13, 'M', 'ADMINISTRATIVO', 2, '2024-11-28', NULL, 0),
(53, 'HUBER CALIXTO COTES BARRIOS', '17809345', '5', '0077', 13, 'M', 'ADMINISTRATIVO', 2, '2024-11-28', NULL, 0),
(54, 'LISVENYS BEATRIZ OÑATE BARROS', '36559883', '5', '0078', 13, 'F', 'ADMINISTRATIVO', 2, '2024-11-28', NULL, 0),
(55, 'VICENTA  MOSCOTE GUALE', '40919240', '5', '0079', 13, 'F', 'ADMINISTRATIVO', 2, '2024-11-28', NULL, 0),
(56, 'DIANIS KARINA REDONDO BERMUDEZ', '40919324', '5', '0080', 13, 'F', 'ADMINISTRATIVO', 2, '2024-11-28', NULL, 0),
(57, 'RITA CARMELA LOPEZ CAMPO', '40924185', '5', '0081', 13, 'F', 'ADMINISTRATIVO', 2, '2024-11-28', NULL, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `colegios`
--

CREATE TABLE `colegios` (
  `id_colegios` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `id_municipios` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `colegios`
--

INSERT INTO `colegios` (`id_colegios`, `nombre`, `id_municipios`) VALUES
(1, 'INSTITUCION EDUCATIVA SAN RAFAEL DE ALBANIA', 3),
(2, 'CENTRO EDUCATIVO EDUARDO PINTO ARAGON DE PORCIOSA', 3),
(3, 'CENTRO EDUCATIVO LOS REMEDIOS', 3),
(4, 'CENTRO ETNOEDUCATIVO RURAL UTPURAI', 3),
(5, 'CENTRO ETNOEDUCATIVO WARE WAREN', 3),
(6, 'INSTITUCION EDUCATIVA RURAL MARIA AUXILIADORA DE CUESTECITAS', 3),
(7, 'INSTITUCION ETNOEDUCATIVA RURAL  AKUAIPA INTERNADO INDIGENA', 3),
(8, 'CENTRO EDUCATIVO RURAL LUIS A. BRITO DE SAN PEDRO', 4),
(9, 'CENTRO ETNOEDUCATIVO RURAL BALLENAS', 4),
(10, 'CENTRO ETNOEDUCATIVO RURAL PROVINCIAL', 4),
(12, 'prueba', 4),
(13, 'INSTITUCION EDUCATIVA RURAL ADOLFO ANTONIO MINDIOLA ROBLES', 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion`
--

CREATE TABLE `configuracion` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `direccion` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `configuracion`
--

INSERT INTO `configuracion` (`id`, `nombre`, `telefono`, `email`, `direccion`) VALUES
(1, 'Dotación Docente', '3017683510', 'ogringenieria@gamil.com', 'Riohacha');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_permisos`
--

CREATE TABLE `detalle_permisos` (
  `id` int(11) NOT NULL,
  `id_permiso` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_permisos`
--

INSERT INTO `detalle_permisos` (`id`, `id_permiso`, `id_usuario`) VALUES
(35, 3, 9),
(36, 4, 9),
(37, 5, 9),
(38, 6, 9),
(39, 1, 1),
(40, 2, 1),
(41, 3, 1),
(42, 4, 1),
(43, 5, 1),
(44, 6, 1),
(45, 7, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_temp`
--

CREATE TABLE `detalle_temp` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `descuento` decimal(10,2) NOT NULL DEFAULT 0.00,
  `precio_venta` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_venta`
--

CREATE TABLE `detalle_venta` (
  `id` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `id_venta` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `descuento` decimal(10,2) NOT NULL DEFAULT 0.00,
  `precio` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_venta`
--

INSERT INTO `detalle_venta` (`id`, `id_producto`, `id_venta`, `cantidad`, `descuento`, `precio`, `total`) VALUES
(51, 21, 38, 2, 0.00, 40.00, -38.00),
(52, 24, 38, 3, 0.00, 25.00, -22.00),
(53, 27, 38, 1, 0.00, 41.00, -40.00),
(54, 26, 39, 2, 0.00, 18.00, -16.00),
(55, 27, 39, 1, 0.00, 40.00, -39.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historia_existencias`
--

CREATE TABLE `historia_existencias` (
  `id_historiaexistencias` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `historia_existencias`
--

INSERT INTO `historia_existencias` (`id_historiaexistencias`, `id_producto`, `cantidad`, `fecha`) VALUES
(1, 9, 4, '2024-11-27'),
(2, 9, 2, '2024-11-28'),
(3, 9, 1, '2024-11-28'),
(4, 10, 10, '2024-11-28'),
(5, 27, 5, '2024-11-28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `municipios`
--

CREATE TABLE `municipios` (
  `id_municipios` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `municipios`
--

INSERT INTO `municipios` (`id_municipios`, `nombre`) VALUES
(3, 'ALBANIA'),
(4, 'BARRANCAS'),
(5, 'DIBULLA'),
(6, 'DISTRACCION'),
(7, 'EL MOLINO'),
(8, 'FONSECA'),
(9, 'HATONUEVO'),
(10, 'LA JAGUA DEL PILAR'),
(11, 'MANAURE'),
(12, 'NIVEL CENTRAL'),
(13, 'SAN JUAN DEL CESAR '),
(14, 'URUMITA'),
(15, 'VILLANUEVA');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`id`, `nombre`) VALUES
(1, 'configuración'),
(2, 'usuarios'),
(3, 'clientes'),
(4, 'productos'),
(5, 'ventas'),
(6, 'nueva_venta'),
(7, 'proveedor');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `codproducto` int(11) NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `descripcion` varchar(200) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `existencia` int(11) NOT NULL,
  `id_categoria` int(15) NOT NULL,
  `fecha` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`codproducto`, `codigo`, `descripcion`, `precio`, `existencia`, `id_categoria`, `fecha`) VALUES
(20, 'blusas', 'M', 0.00, 50, 3, '2024-11-28'),
(21, 'blusas 1', 'S', 0.00, 38, 3, '2024-11-28'),
(22, 'blusas 2', 'L', 0.00, 30, 3, '2024-11-28'),
(23, 'blusas 3', 'XL', 0.00, 70, 1, '2024-11-28'),
(24, 'blusas 4', 'M', 0.00, 22, 1, '2024-11-28'),
(25, 'blusas 5', 'M', 0.00, 32, 1, '2024-11-28'),
(26, 'blusas 6', 'L', 0.00, 16, 3, '2024-11-28'),
(27, 'blusas 7', 'M', 0.00, 39, 3, '2024-11-28'),
(28, 'blusa 8', 'S', 0.00, 80, 3, '2024-11-28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor`
--

CREATE TABLE `proveedor` (
  `idproveedor` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `direccion` varchar(200) NOT NULL,
  `descripcion` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `proveedor`
--

INSERT INTO `proveedor` (`idproveedor`, `nombre`, `apellido`, `telefono`, `direccion`, `descripcion`) VALUES
(1, 'oscar', 'gomez', '234234', 'tal', 'cerveza lithg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `idusuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `usuario` varchar(20) NOT NULL,
  `clave` varchar(50) NOT NULL,
  `tipo` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`idusuario`, `nombre`, `correo`, `usuario`, `clave`, `tipo`) VALUES
(1, 'Oscar Gomez', 'ogrnigenieria@gmail.com', 'admin', '21232f297a57a5a743894a0e4a801fc3', 1),
(9, 'Maria Sanchez', 'maria@gmail.com', 'maria', '263bce650e68ab4e23f28263760b9fa5', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `dataciones` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `id_cliente`, `total`, `id_usuario`, `fecha`, `dataciones`) VALUES
(38, 51, -100.00, 1, '2024-11-28 14:21:52', '2'),
(39, 49, -55.00, 9, '2024-11-28 14:56:59', '2');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`idcliente`);

--
-- Indices de la tabla `colegios`
--
ALTER TABLE `colegios`
  ADD PRIMARY KEY (`id_colegios`),
  ADD KEY `id_municipios` (`id_municipios`);

--
-- Indices de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `detalle_permisos`
--
ALTER TABLE `detalle_permisos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_permiso` (`id_permiso`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `detalle_temp`
--
ALTER TABLE `detalle_temp`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_producto` (`id_producto`),
  ADD KEY `id_venta` (`id_venta`);

--
-- Indices de la tabla `historia_existencias`
--
ALTER TABLE `historia_existencias`
  ADD PRIMARY KEY (`id_historiaexistencias`);

--
-- Indices de la tabla `municipios`
--
ALTER TABLE `municipios`
  ADD PRIMARY KEY (`id_municipios`);

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`codproducto`),
  ADD KEY `categorias` (`id_categoria`);

--
-- Indices de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  ADD PRIMARY KEY (`idproveedor`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`idusuario`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_cliente` (`id_cliente`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `idcliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT de la tabla `colegios`
--
ALTER TABLE `colegios`
  MODIFY `id_colegios` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `detalle_permisos`
--
ALTER TABLE `detalle_permisos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT de la tabla `detalle_temp`
--
ALTER TABLE `detalle_temp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT de la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT de la tabla `historia_existencias`
--
ALTER TABLE `historia_existencias`
  MODIFY `id_historiaexistencias` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `municipios`
--
ALTER TABLE `municipios`
  MODIFY `id_municipios` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `codproducto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  MODIFY `idproveedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `idusuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `colegios`
--
ALTER TABLE `colegios`
  ADD CONSTRAINT `colegios_ibfk_1` FOREIGN KEY (`id_municipios`) REFERENCES `municipios` (`id_municipios`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_permisos`
--
ALTER TABLE `detalle_permisos`
  ADD CONSTRAINT `detalle_permisos_ibfk_1` FOREIGN KEY (`id_permiso`) REFERENCES `permisos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detalle_permisos_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`idusuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_temp`
--
ALTER TABLE `detalle_temp`
  ADD CONSTRAINT `detalle_temp_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`codproducto`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detalle_temp_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`idusuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  ADD CONSTRAINT `detalle_venta_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`codproducto`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detalle_venta_ibfk_2` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `producto`
--
ALTER TABLE `producto`
  ADD CONSTRAINT `categorias` FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id_categoria`);

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`idcliente`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ventas_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`idusuario`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
