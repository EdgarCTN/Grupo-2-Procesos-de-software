-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-02-2024 a las 20:19:03
-- Versión del servidor: 8.0.35
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sma_unayoe`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumno`
--

CREATE TABLE `alumno` (
  `cod_alumno` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `id_usuario` int NOT NULL,
  `nombre` varchar(40) COLLATE utf8mb4_general_ci NOT NULL,
  `apellidos` varchar(40) COLLATE utf8mb4_general_ci NOT NULL,
  `correo` varchar(40) COLLATE utf8mb4_general_ci NOT NULL,
  `facultad` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `numero_celular` varchar(10) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `curso`
--

CREATE TABLE `curso` (
  `cod_curso` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `nombre_curso` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `ciclo` int NOT NULL,
  `creditos` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evidencia`
--

CREATE TABLE `evidencia` (
  `id_evidencia` int NOT NULL,
  `idtutoria` int DEFAULT NULL,
  `imagen` blob,
  `descripción` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tutor`
--

CREATE TABLE `tutor` (
  `cod_tutor` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `id_usuario` int NOT NULL,
  `nombre` varchar(40) COLLATE utf8mb4_general_ci NOT NULL,
  `apellidos` varchar(40) COLLATE utf8mb4_general_ci NOT NULL,
  `correo` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `numero_celular` varchar(10) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tutoría`
--

CREATE TABLE `tutoría` (
  `id_tutoria` int NOT NULL,
  `codalumno` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `codtutor` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `codcurso` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `tema` varchar(50) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int NOT NULL,
  `nombre` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `nombre_usuario` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `contraseña` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `rol` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  `ruta_foto` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `nombre_usuario`, `contraseña`, `rol`, `ruta_foto`) VALUES
(1, 'Romani Moscoso, Anthony Paolo', 'antorm', 'alum123', 'Alumno', 'http://localhost/foto_1.jpg'),
(2, 'Ibarra Cabrera, Manuel Jesús', 'tutor', 'tutor123', 'Tutor', ''),
(3, 'Lic. Karla Sánchez Nava', 'admi', 'admi123', 'Administrador', '');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alumno`
--
ALTER TABLE `alumno`
  ADD PRIMARY KEY (`cod_alumno`),
  ADD KEY `alumno_ibfk_1` (`id_usuario`);

--
-- Indices de la tabla `curso`
--
ALTER TABLE `curso`
  ADD PRIMARY KEY (`cod_curso`);

--
-- Indices de la tabla `evidencia`
--
ALTER TABLE `evidencia`
  ADD PRIMARY KEY (`id_evidencia`),
  ADD KEY `idtutoria` (`idtutoria`);

--
-- Indices de la tabla `tutor`
--
ALTER TABLE `tutor`
  ADD PRIMARY KEY (`cod_tutor`),
  ADD KEY `tutor_ibfk_1` (`id_usuario`);

--
-- Indices de la tabla `tutoría`
--
ALTER TABLE `tutoría`
  ADD PRIMARY KEY (`id_tutoria`),
  ADD KEY `tutoría_ibfk_1` (`codalumno`),
  ADD KEY `tutoría_ibfk_2` (`codtutor`),
  ADD KEY `tutoría_ibfk_3` (`codcurso`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `evidencia`
--
ALTER TABLE `evidencia`
  MODIFY `id_evidencia` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tutoría`
--
ALTER TABLE `tutoría`
  MODIFY `id_tutoria` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alumno`
--
ALTER TABLE `alumno`
  ADD CONSTRAINT `alumno_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `evidencia`
--
ALTER TABLE `evidencia`
  ADD CONSTRAINT `evidencia_ibfk_1` FOREIGN KEY (`idtutoria`) REFERENCES `tutoría` (`id_tutoria`);

--
-- Filtros para la tabla `tutor`
--
ALTER TABLE `tutor`
  ADD CONSTRAINT `tutor_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `tutoría`
--
ALTER TABLE `tutoría`
  ADD CONSTRAINT `tutoría_ibfk_1` FOREIGN KEY (`codalumno`) REFERENCES `alumno` (`cod_alumno`),
  ADD CONSTRAINT `tutoría_ibfk_2` FOREIGN KEY (`codtutor`) REFERENCES `tutor` (`cod_tutor`),
  ADD CONSTRAINT `tutoría_ibfk_3` FOREIGN KEY (`codcurso`) REFERENCES `curso` (`cod_curso`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
