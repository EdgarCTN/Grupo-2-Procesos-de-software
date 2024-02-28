
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-02-2024 a las 09:09:38
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

--
-- Volcado de datos para la tabla `alumno`
--

INSERT INTO `alumno` (`cod_alumno`, `id_usuario`, `nombre`, `apellidos`, `correo`, `facultad`, `numero_celular`) VALUES
('22200309', 4, 'Andrew Gabriel', 'Serna Quiroz', 'andrew.serna@unmsm.edu.pe', 'FISI', '999040445'),
('22200845', 1, 'Anthony Paolo', 'Romani Moscoso', 'ejemplo@correo.com', 'FISI', '965841231');

--
-- Disparadores `alumno`
--
DELIMITER $$
CREATE TRIGGER `before_insert_alumno` BEFORE INSERT ON `alumno` FOR EACH ROW BEGIN
    DECLARE v_nombre_apellidos VARCHAR(255);
    DECLARE v_nombre VARCHAR(255);
    DECLARE v_apellidos VARCHAR(255);
    
    -- Obtener el nombre completo del usuario a insertar en la tabla alumno
    SELECT nombre INTO v_nombre FROM usuarios WHERE id = NEW.id_usuario;
    
    -- Separar el nombre completo en apellidos y nombres
    SET v_apellidos = SUBSTRING_INDEX(v_nombre, ',', 1);
    SET v_nombre = TRIM(SUBSTRING_INDEX(v_nombre, ',', -1));
    
    -- Actualizar las columnas apellidos y nombre en la fila a insertar en la tabla alumno
    SET NEW.apellidos = v_apellidos;
    SET NEW.nombre = v_nombre;
END
$$
DELIMITER ;

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

--
-- Volcado de datos para la tabla `curso`
--

INSERT INTO `curso` (`cod_curso`, `nombre_curso`, `ciclo`, `creditos`) VALUES
('202W0405', 'PROBABILIDADES', 4, 3),
('202W0406', 'PROCESOS DE SOFTWARE', 4, 3);

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
-- Estructura de tabla para la tabla `frases_motivadoras`
--

CREATE TABLE `frases_motivadoras` (
  `id` int NOT NULL,
  `frase` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `frases_motivadoras`
--

INSERT INTO `frases_motivadoras` (`id`, `frase`) VALUES
(1, 'El éxito no es definitivo, el fracaso no es fatal: lo que cuenta es el coraje para continuar.'),
(2, 'Cree en ti mismo y todo será posible.'),
(3, 'El único modo de hacer un gran trabajo es amar lo que haces.'),
(4, 'No te detengas hasta que estés orgulloso.'),
(5, 'Cada logro comienza con la decisión de intentarlo.'),
(6, 'La mejor manera de predecir el futuro es crearlo.'),
(7, 'El éxito es la suma de pequeños esfuerzos repetidos día tras día.'),
(8, 'Los desafíos son lo que hacen la vida interesante. Superarlos es lo que hace la vida significativa.'),
(9, 'El fracaso es la oportunidad de comenzar de nuevo, pero esta vez de forma más inteligente.'),
(10, 'No importa cuántas veces te caigas, lo que cuenta es cuántas veces te levantas.'),
(11, 'El futuro pertenece a aquellos que creen en la belleza de sus sueños.'),
(12, 'La única forma de hacer un gran trabajo es amar lo que haces.'),
(13, 'Si no te gusta algo, cámbialo. Si no puedes cambiarlo, cambia tu actitud.'),
(14, 'El éxito no es la clave de la felicidad. La felicidad es la clave del éxito.'),
(15, 'No te limites a soñar, trabaja duro para hacer realidad tus sueños.'),
(16, 'La diferencia entre lo imposible y lo posible radica en la determinación de una persona.'),
(17, 'El único lugar donde el éxito viene antes que el trabajo es en el diccionario.'),
(18, 'Nunca te des por vencido en algo que realmente te importa.'),
(19, 'El verdadero éxito es ser feliz con lo que eres y lo que tienes.'),
(20, 'La vida es 10% lo que me pasa y 90% cómo reacciono a ello.'),
(21, 'Cree que puedes y ya estás a medio camino.'),
(22, 'No dejes que tus miedos ocupen el lugar de tus sueños.'),
(23, 'El secreto del éxito es comenzar antes de estar listo.'),
(24, 'El optimismo es la fe que conduce al logro.'),
(25, 'La verdadera medida de tu éxito es la cantidad de veces que puedes volver a levantarte después de la caída.'),
(26, 'No esperes la oportunidad, créala.'),
(27, 'La mente es todo; lo que piensas, te conviertes.'),
(28, 'El éxito es encontrar satisfacción en dar un paso cada día hacia adelante hacia un objetivo significativo.'),
(29, 'El único límite para tu realización de mañana será tus dudas de hoy.'),
(30, 'El único camino hacia el éxito es a través de la autodisciplina.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `objetivo`
--

CREATE TABLE `objetivo` (
  `id_objetivo` int NOT NULL,
  `id_usuario` int NOT NULL,
  `nombre_objetivo` varchar(50) NOT NULL,
  `fecha` varchar(10) NOT NULL,
  `hora` time NOT NULL,
  `duracion` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `objetivo`
--

INSERT INTO `objetivo` (`id_objetivo`, `id_usuario`, `nombre_objetivo`, `fecha`, `hora`, `duracion`) VALUES
(8, 1, 'Hola', '2024-02-26', '16:30:00', 2),
(10, 1, 'Proyecto 1', '2024-03-01', '16:00:00', 2),
(11, 1, 'Nashe', '2024-02-26', '20:00:00', 2),
(12, 4, 'Trabajo', '2024-03-01', '20:00:00', 2),
(13, 1, 'Trabajo 1', '2024-03-06', '20:04:00', 2),
(14, 1, 'Trabajo 2', '2024-03-09', '22:06:00', 1);

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

--
-- Volcado de datos para la tabla `tutor`
--

INSERT INTO `tutor` (`cod_tutor`, `id_usuario`, `nombre`, `apellidos`, `correo`, `numero_celular`) VALUES
('15647852', 2, 'Manuel Jesús', 'Ibarra Cabrera', 'tutor@correo.com', '989475123'),
('15675842', 5, 'Zoraida Judith', 'Huamán Gutiérrez', 'ejemplo.tutor2@correo.com', '969457213');

--
-- Disparadores `tutor`
--
DELIMITER $$
CREATE TRIGGER `before_insert_tutor` BEFORE INSERT ON `tutor` FOR EACH ROW BEGIN
    DECLARE v_nombre_apellidos VARCHAR(255);
    DECLARE v_nombre VARCHAR(255);
    DECLARE v_apellidos VARCHAR(255);
    
    -- Obtener el nombre completo del usuario a insertar en la tabla tutor
    SELECT nombre INTO v_nombre FROM usuarios WHERE id = NEW.id_usuario;
    
    -- Separar el nombre completo en apellidos y nombres
    SET v_apellidos = SUBSTRING_INDEX(v_nombre, ',', 1);
    SET v_nombre = TRIM(SUBSTRING_INDEX(v_nombre, ',', -1));
    
    -- Actualizar las columnas apellidos y nombre en la fila a insertar en la tabla tutor
    SET NEW.apellidos = v_apellidos;
    SET NEW.nombre = v_nombre;
END
$$
DELIMITER ;

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

--
-- Volcado de datos para la tabla `tutoría`
--

INSERT INTO `tutoría` (`id_tutoria`, `codalumno`, `codtutor`, `codcurso`, `fecha`, `hora`, `tema`) VALUES
(1, '22200309', '15647852', '202W0406', '2024-03-01', '16:00:00', 'SCRUM'),
(2, '22200309', '15675842', '202W0405', '2024-02-29', '16:00:00', 'Variables aleatorias');

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
(3, 'Lic. Karla Sánchez Nava', 'admi', 'admi123', 'Administrador', ''),
(4, 'Serna Quiroz, Andrew Gabriel', 'sernak', 'alum321', 'Alumno', 'http://localhost/foto_2.jpg'),
(5, 'Huamán Gutiérrez, Zoraida Judith', 'tutor2', 'tutor123', 'Tutor', '');

--
-- Disparadores `usuarios`
--
DELIMITER $$
CREATE TRIGGER `actualizar_alumno_desde_usuarios` AFTER INSERT ON `usuarios` FOR EACH ROW BEGIN
    UPDATE alumno
    SET 
        apellidos = SUBSTRING_INDEX(NEW.nombre, ',', 1), -- Extraer los apellidos antes de la coma
        nombre = TRIM(SUBSTRING_INDEX(NEW.nombre, ',', -1)) -- Extraer los nombres después de la coma y quitar espacios en blanco al inicio
    WHERE id_usuario = NEW.id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `consulta`
--
CREATE TABLE consulta (
  id_consulta int NOT NULL AUTO_INCREMENT,
  id_alumno int NOT NULL,
  asunto varchar(100) NOT NULL,
  contenido text NOT NULL,
  fecha_creacion datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  respondida tinyint(1) NOT NULL DEFAULT '0',
  respuesta text,
  PRIMARY KEY (id_consulta),
  KEY id_alumno (id_alumno),
  CONSTRAINT fk_alumno_consulta FOREIGN KEY (id_alumno) REFERENCES alumno (id_usuario) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


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
-- Indices de la tabla `frases_motivadoras`
--
ALTER TABLE `frases_motivadoras`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `objetivo`
--
ALTER TABLE `objetivo`
  ADD PRIMARY KEY (`id_objetivo`),
  ADD KEY `FK_id_usuario` (`id_usuario`) USING BTREE;

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
-- AUTO_INCREMENT de la tabla `frases_motivadoras`
--
ALTER TABLE `frases_motivadoras`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `objetivo`
--
ALTER TABLE `objetivo`
  MODIFY `id_objetivo` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `tutoría`
--
ALTER TABLE `tutoría`
  MODIFY `id_tutoria` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
-- Filtros para la tabla `objetivo`
--
ALTER TABLE `objetivo`
  ADD CONSTRAINT `objetivo_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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


ALTER TABLE consulta
ADD COLUMN imagen_path VARCHAR(255);

ALTER TABLE consulta
ADD COLUMN cod_tutor varchar(20) NOT NULL;

ALTER TABLE consulta
ADD CONSTRAINT fk_tutor_consulta
FOREIGN KEY (cod_tutor)
REFERENCES tutor (cod_tutor)
ON DELETE CASCADE
ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
