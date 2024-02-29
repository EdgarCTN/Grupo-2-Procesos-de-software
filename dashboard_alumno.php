<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Archivo de conexión a la base de datos
include 'conn/connection.php';

// Obtener el nombre de usuario de la sesión
$nombre_usuario = $_SESSION['nombre'];

try {
    // Preparar la consulta SQL para obtener la ruta de la foto del usuario
    $stmt = $conn->prepare("SELECT ruta_foto FROM usuarios WHERE nombre_usuario = :nombre_usuario");
    $stmt->bindParam(':nombre_usuario', $nombre_usuario);

    // Ejecutar la consulta
    $stmt->execute();

    // Verificar si se encontró el usuario
    if ($stmt->rowCount() > 0) {
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        $ruta_foto = $resultado['ruta_foto'];
    } else {
        // Si el usuario no se encuentra, mostrar un mensaje de error o una imagen por defecto
        $ruta_foto = "http://localhost/foto_defecto.png";
    }
} catch(PDOException $e) {
    // Manejar errores de base de datos
    $ruta_foto = "ruta/error/foto.jpg"; // Otra ruta de una imagen de error
}

try {
    // Preparar la consulta SQL para obtener el ID del usuario basado en el nombre de usuario
    $stmt_usuario = $conn->prepare("SELECT id FROM usuarios WHERE nombre_usuario = :nombre_usuario");
    $stmt_usuario->bindParam(':nombre_usuario', $nombre_usuario);
    $stmt_usuario->execute();

    // Verificar si se encontró el usuario
    if ($stmt_usuario->rowCount() > 0) {
        $resultado_usuario = $stmt_usuario->fetch(PDO::FETCH_ASSOC);
        $id_usuario = $resultado_usuario['id'];

        // Preparar la consulta SQL para obtener los datos del alumno usando el ID del usuario
        $stmt_alumno = $conn->prepare("SELECT cod_alumno, apellidos, nombre, facultad FROM alumno WHERE id_usuario = :id_usuario");
        $stmt_alumno->bindParam(':id_usuario', $id_usuario);
        $stmt_alumno->execute();

        // Verificar si se encontró el alumno
        if ($stmt_alumno->rowCount() > 0) {
            $resultado_alumno = $stmt_alumno->fetch(PDO::FETCH_ASSOC);
            $cod_alumno = $resultado_alumno['cod_alumno'];
            $apellidos = $resultado_alumno['apellidos'];
            $nombre = $resultado_alumno['nombre'];
            $facultad = $resultado_alumno['facultad'];
        } else {
            // Si el alumno no se encuentra, mostrar un mensaje de error o asignar valores por defecto
            $cod_alumno = "No disponible";
            $apellidos = "No disponible";
            $nombre = "No disponible";
            $facultad = "No disponible";
        }
    } else {
        // Si no se encuentra el usuario, mostrar un mensaje de error o asignar valores por defecto
        $cod_alumno = "No disponible";
        $apellidos = "No disponible";
        $nombre = "No disponible";
        $facultad = "No disponible";
    }
} catch(PDOException $e) {
    // Manejar errores de base de datos
    $cod_alumno = "Error";
    $apellidos = "Error";
    $nombre = "Error";
    $facultad = "Error";
}

try {
    // Generar un número aleatorio entre 1 y 30
    $random_id = rand(1, 30);
    
    // Consultar la base de datos para obtener la frase motivadora aleatoria
    $stmt = $conn->prepare("SELECT frase FROM frases_motivadoras WHERE id = :id");
    $stmt->bindParam(':id', $random_id);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $frase_motivadora = $stmt->fetch(PDO::FETCH_ASSOC)['frase'];
    } else {
        $frase_motivadora = "No hay frase disponible en este momento.";
    }
} catch(PDOException $e) {
    $frase_motivadora = "Error al obtener la frase motivadora.";
}

try {
    // Preparar la consulta SQL para obtener los datos de la primera tutoría del alumno
    $stmt_tutoria = $conn->prepare("SELECT t.fecha, t.hora, t.tema, c.nombre_curso, tu.apellidos, tu.nombre
                                    FROM tutoría AS t
                                    INNER JOIN curso AS c ON t.codcurso = c.cod_curso
                                    INNER JOIN tutor AS tu ON t.codtutor = tu.cod_tutor
                                    INNER JOIN alumno AS a ON t.codalumno = a.cod_alumno
                                    INNER JOIN usuarios AS u ON a.id_usuario = u.id
                                    WHERE u.nombre_usuario = :nombre_usuario");
    $stmt_tutoria->bindParam(':nombre_usuario', $nombre_usuario);
    $stmt_tutoria->execute();

    // Verificar si se encontró una tutoría para el estudiante
    if ($stmt_tutoria->rowCount() > 0) {
        $resultado_tutoria = $stmt_tutoria->fetch(PDO::FETCH_ASSOC);
        $fecha_tutoria = "<span class='texto'><strong>Fecha:</strong></span> " . $resultado_tutoria['fecha'];
        $hora_tutoria = "<span class='texto'><strong>Hora:</strong></span> " . $resultado_tutoria['hora'];
        $tema_tutoria = "<span class='texto'><strong>Tema:</strong></span> " . $resultado_tutoria['tema'];
        $nombre_curso = $resultado_tutoria['nombre_curso'];
        $apellidos_tutor = "<span class='texto'><strong>Tutor:</strong></span> " . $resultado_tutoria['apellidos'];
        $nombre_tutor = "<span class='texto'><strong>Tutor:</strong></span> " . $resultado_tutoria['nombre'];
    } else {
        // Si no se encontró una tutoría, mostrar un mensaje o asignar valores por defecto
        $fecha_tutoria = "No hay tutorías programadas";
        $hora_tutoria = "";
        $tema_tutoria = "";
        $nombre_curso = "";
        $apellidos_tutor = "";
        $nombre_tutor = "";
    }

    // Preparar la consulta SQL para obtener la lista de tutorías del mismo alumno
    $stmt_lista_tutorias = $conn->prepare("SELECT t.fecha, t.hora, t.tema, c.nombre_curso, tu.apellidos, tu.nombre
                                           FROM tutoría AS t
                                           INNER JOIN curso AS c ON t.codcurso = c.cod_curso
                                           INNER JOIN tutor AS tu ON t.codtutor = tu.cod_tutor
                                           INNER JOIN alumno AS a ON t.codalumno = a.cod_alumno
                                           INNER JOIN usuarios AS u ON a.id_usuario = u.id
                                           WHERE u.nombre_usuario = :nombre_usuario");
    $stmt_lista_tutorias->bindParam(':nombre_usuario', $nombre_usuario);
    $stmt_lista_tutorias->execute();

    // Guardar las tutorías del alumno en un array
    $tutorias = $stmt_lista_tutorias->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // Manejar errores de base de datos
    $fecha_tutoria = "Error al obtener las tutorías";
    $hora_tutoria = "";
    $tema_tutoria = "";
    $nombre_curso = "";
    $apellidos_tutor = "";
    $nombre_tutor = "";
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@700&display=swap">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Dashboard</title>
    <style>
        /* Estilos generales */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            background-color: #2c3e50;
            color: #fff;
            padding: 20px;
            width: 200px;
            transition: width 0.5s;
            overflow-x: hidden;
        }
        .sidebar.closed {
            width: 60px;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
            visibility: visible;
            opacity: 1;
            transition: opacity 0.5s;
        }
        .sidebar.closed ul {
            visibility: hidden;
            opacity: 0;
        }
        .sidebar li {
            margin-bottom: 15px; /* Separación entre cada opción */
        }
        .sidebar a {
            color: #fff;
            text-decoration: none;
            font-size: 20px;
            font-weight: bold;
            display: block;
            padding: 10px 15px; /* Espaciado interno para los botones */
            border-radius: 10px; /* Esquinas redondeadas */
            transition: background-color 0.3s; /* Transición de color de fondo */
            background-color: #455a64; /* Color de fondo de los botones (similar al de la barra lateral pero más claro) */
        }
        .sidebar a:hover {
            background-color: #607d8b; /* Cambia el color de fondo al pasar el mouse (ligeramente más claro) */
        }
        .sidebar-logo {
            text-align: center;
            margin-bottom: 20px;
            transition: all 0.5s;
        }
        .sidebar-logo img {
            max-width: 150px;
            margin-top: 30px;
            transition: all 0.5s;
        }
        .sidebar.closed .sidebar-logo {
            margin-bottom: 10px;
        }
        .sidebar.closed .sidebar-logo img {
            max-width: 50px;
        }
        .main-content {
            background-color: #ecf0f1;
            padding: 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            position: relative;
        }
        .welcome-message {
            margin-bottom: 20px;
            font-size: 24px;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 10px;
            width: 100%;
        }
        .user-photo-container {
            width: 300px; /* Ajuste para el espacio alrededor de la imagen */
            height: 300px; /* Misma altura y ancho */
            border-radius: 50%; /* Forma circular */
            overflow: hidden; /* Recorta cualquier exceso fuera del círculo */
            margin-bottom: 20px;
            position: absolute;
            top: 17%; /* Mover la imagen hacia arriba */
            left: 40px;
        }
        .alumno-info-container {
            position: absolute;
            top: 17%;
            left: 400px; /* Ajusta este valor según sea necesario */
        }
        .user-photo {
            width: 100%; /* Ocupa todo el contenedor */
            height: 100%; /* Ocupa todo el contenedor */
            object-fit: cover; /* Ajusta la imagen para mantener la forma circular */
            border-radius: 50%; /* Forma circular */
        }
        .menu-toggle {
            position: absolute;
            top: 10px;
            left: 10px;
            cursor: pointer;
        }
        .menu-toggle img {
            width: 30px;
        }
        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3);
            z-index: 9999;
        }
        .popup-content {
            text-align: center;
        }
        .popup-buttons {
            margin-top: 20px;
        }
        .popup-buttons button {
            padding: 10px 20px;
            margin: 0 10px;
            cursor: pointer;
        }
        .large-rectangle {
            background-color: #455a64;
            border-radius: 10px;
            margin-bottom: 20px;
            width: calc(50% - 10px); /* Ajuste para la separación */
            height: 50vh;
        }
        /* Nuevos estilos para el rectángulo duplicado */
        .large-rectangle.duplicate {
            position: absolute;
            top: 80px; /* Misma posición que el rectángulo original */
            left: calc(50% + 10px); /* Se ubica a la derecha del rectángulo original con la separación */
        }

        .center-square {
            position: absolute;
            top: calc(50% - 375px); /* Mitad de la altura del contenedor menos la mitad del alto del cuadrado */
            left: calc(50% + 10px + 18vh); /* Ajuste de la mitad del ancho del cuadrado más el espacio entre los rectángulos */
            width: 500px; /* Ancho del cuadrado */
            height: 400px; /* Alto del cuadrado */
            background-color: #FFEFD5; /* Color de fondo del cuadrado */
            border: 2px solid #FF4500; /* Borde del cuadrado */
        }

        
        .large-rectangle.new {
            background-color: #f9e46e;
            border: 4px solid #ffd700; /* Borde amarillo un poco más fuerte */
            border-radius: 10px;
            margin-bottom: 20px;
            width: calc(70% - 10px); /* Ajuste para la separación y aumento del ancho */
            height: 35vh; /* Altura reducida */
            position: relative; /* Cambiado a relative */
            bottom: 0px; /* Posición un poco más abajo */
            left: 50%; /* Centrado horizontalmente */
            transform: translateX(-50%); /* Centrado horizontalmente */
        }

        .large-rectangle.new::before {
            content: ""; /* Contenido vacío necesario para que se muestre la pseudo-clase */
            background-image: url('http://localhost/icono_cerebro.png');
            background-size: cover; /* Ajusta la imagen para que cubra todo el espacio */
            background-repeat: no-repeat; /* Evita que la imagen se repita */
            background-position: center; /* Centra la imagen */
            position: absolute;
            top: 50%; /* Centra verticalmente */
            left: -100px; /* Ajusta para posicionar desde el borde izquierdo */
            transform: translateY(-50%); /* Centra verticalmente */
            width: 200px; /* Ancho de la imagen */
            height: 200px; /* Alto de la imagen */
        }


        /* Estilos para los datos del alumno */
        .alumno-info p {
            font-family: 'Roboto', sans-serif;
            font-size: 30px;
            font-weight: bold; /* Asegúrate de especificar el peso de la fuente como 'bold' para la versión negrita */
            color: #fff;
            margin-bottom: 10px;
        }
        /* Estilos para el título "Datos del estudiante" */
        .title {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #fff;
            position: absolute;
            top: -40px; /* Mover el título hacia arriba */
            left: 0;
            background-color: rgba(0, 0, 0, 0.5); /* Color de fondo semitransparente */
            padding: 10px 20px;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }
        /* Estilos para el contenedor de texto */
        .text-container {
            background-color: #607d8b; /* Color de fondo */
            color: #fff; /* Color del texto */
            padding: 8px; /* Espaciado interno */
            border-radius: 10px; /* Esquinas redondeadas */
            margin-bottom: 10px; /* Espaciado inferior */
            font-size: 22px; /* Tamaño de fuente */
            font-weight: bold; /* Negrita */
        }
        /* Estilos para la frase motivadora */
        .motivational-quote {
            font-size: 60px;
            color: #455a64;
            font-family: 'Roboto', sans-serif;
            font-weight: bold;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
            position: absolute;
            top: 50%;
            right: 20px;
            transform: translateY(-50%);
            width: calc(100% - 40px); /* Ancho igual al 100% del contenedor menos el espacio de los márgenes */
            max-width: calc(95% - 40px); /* Máximo ancho igual al 70% del contenedor menos el espacio de los márgenes */
            padding: 0 20px; /* Añadido para dar espacio entre el texto y los bordes del contenedor */
            box-sizing: border-box; /* Hace que el padding no afecte al ancho total */
            
        }

        /* Estilos para el nombre del curso */
        .curso {
            font-weight: bold; /* Negrita */
            color: #455a64; /* Color blanco para el nombre del curso */
            margin-left: 20px;
            overflow: hidden; /* Oculta cualquier contenido que sobresalga del contenedor */
            text-overflow: ellipsis; /* Agrega puntos suspensivos (...) al final del texto si es demasiado largo */
            max-width: 300px; /* Establece el ancho máximo del contenedor */
            font-size: 28px;
        }

        /* Estilos para los datos de la tutoría */
        .texto {
            font-size: 28px; /* Tamaño del texto */
            font-weight: bold; /* Negrita */
            margin-left: 20px; /* Ajusta el margen izquierdo según sea necesario */
        }

        /* Estilos para el botón "Ir a la Tutoría" */
        .boton-tutoria {
            background-color: #455a64; /* Color de fondo */
            color: #fff; /* Color del texto */
            font-size: 28px; /* Tamaño de la fuente */
            font-weight: bold; /* Negrita */
            padding: 20px 40px; /* Espaciado interno aumentado */
            border: none; /* Sin borde */
            border-radius: 5px; /* Esquinas redondeadas */
            cursor: pointer; /* Cursor al pasar el mouse */
            transition: background-color 0.3s; /* Transición de color de fondo */
            margin-top: 20px; /* Margen superior */
            display: block; /* Convertir en bloque para centrar */
            margin-left: auto; /* Centrar horizontalmente */
            margin-right: auto; /* Centrar horizontalmente */
        }

        /* Estilos para el contenedor de los botones de flecha */
        .arrow-buttons-container {
            position: relative; /* Cambiado a relativo */
            margin-top: 20px; /* Espacio entre el cuadrado y los botones de flecha */
            width: 100%; /* Ancho igual al 100% del contenedor */
        }

        /* Estilos para los botones de flecha */
        .arrow-button {
            font-size: 24px; /* Tamaño de la flecha */
            color: #FF4500; /* Color de la flecha */
            cursor: pointer;
            transition: color 0.3s;
            width: 50px; /* Ancho del botón */
            height: 50px; /* Alto del botón */
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%; /* Forma circular */
            border: 2px solid #FF4500; /* Borde del botón */
            background-color: transparent; /* Fondo transparente */
            position: absolute; /* Cambiado a absoluto */
            top: calc(50% - 85px); /* Centrar verticalmente */
        }

        /* Estilos adicionales para resaltar la flecha al pasar el mouse */
        .arrow-button:hover {
            background-color: #FF4500; /* Cambia el color de fondo al pasar el mouse */
            color: #fff; /* Cambia el color del texto al pasar el mouse */
        }

        /* Estilos para la flecha izquierda */
        .arrow-left {
            left: 40px; /* Posición a la izquierda del contenedor */
        }

        /* Estilos para la flecha derecha */
        .arrow-right {
            right: 40px; /* Posición a la derecha del contenedor */
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="sidebar" id="sidebar">
            <div class="sidebar-logo">
                <img src="fisi.png" alt="Logo Facultad">
            </div>
            <ul>
                <li><a href="dashboard_alumno.php">Inicio</a></li>
                <li><a href="dashboard_tutorias.php">Tutorías</a></li>
                <li><a href="horario.php">Horario</a></li>
                <li><a href="objetivos.php">Objetivos</a></li>
                <li><a href="consultas.php">Consultas</a></li>
                <li><button onclick="showPopup()">Salir</button></li>
            </ul>
        </div>
        <div class="main-content">
            <?php
            if (isset($_SESSION['nombre'])) {
                echo "<div class='welcome-message'>Bienvenido, " . $_SESSION['nombre'] . "</div>";
            } else {
                echo "<p>Por favor, inicia sesión para ver tus datos.</p>";
            }
            ?>
            <!-- Rectángulo grande que ocupa la mitad del espacio disponible -->
            <div class="large-rectangle"></div>
            <!-- Rectángulo duplicado -->
            <div class="large-rectangle duplicate"></div>
            <!-- Cuadrado en el centro -->
            <div class="center-square">
                <!-- Datos de la tutoría -->
                <p><span class="curso"><?php echo $nombre_curso; ?>:</span> <span class="texto"><?php?></span></p>
                <!-- Resto de datos de la tutoría -->
                <p id="nombre-tutor"><?php echo $nombre_tutor; ?></p>
                <p id="fecha-tutoria"><?php echo $fecha_tutoria; ?></p>
                <p id="hora-tutoria"><?php echo $hora_tutoria; ?></p>
                <p id="tema-tutoria"><?php echo $tema_tutoria; ?></p>
                <!-- Botón Ir a la Tutoría -->
                <button class="boton-tutoria" onclick="window.location.href='dashboard_tutorias.php';">Ir a Tutorías</button>
                <div class="arrow-buttons-container">
                <button class="arrow-button arrow-left" onclick="cambiarTutoria('anterior')">←</button>
                <button class="arrow-button arrow-right" onclick="cambiarTutoria('siguiente')">→</button>
                </div>
            </div>
            <!-- Nuevo rectángulo adicional -->
            <div class="large-rectangle new">
                <!-- Frase motivadora -->
                <div class="motivational-quote">
                    <?php echo $frase_motivadora; ?>
                </div>
            </div>
            <div class="user-photo-container">
                <!-- Imagen del usuario -->
                <img src="<?php echo $ruta_foto; ?>" alt="Foto del usuario" class="user-photo">
            </div>
            <div class="alumno-info-container">
                <div class="alumno-info">
                    <!-- Agregar el título "Datos del estudiante" -->
                    <div class="title">Datos del estudiante</div>
                    <!-- Agregar el contenedor de texto -->
                    <div class="text-container">
                        <p>Código: <?php echo $cod_alumno; ?></p>
                        <p>Apellidos: <?php echo $apellidos; ?></p>
                        <p>Nombre: <?php echo $nombre; ?></p>
                        <p>Facultad: <?php echo $facultad; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="popup" id="popup">
        <div class="popup-content">
            <p>Confirmar salida</p>
            <div class="popup-buttons">
                <button onclick="confirmLogout()">Confirmar</button>
                <button onclick="hidePopup()">Rechazar</button>
            </div>
        </div>
    </div>
    <div class="menu-toggle" onclick="toggleSidebar()">
        <img src="icono_menu.png" alt="Menú">
    </div>

    
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('closed');
        }

        function showPopup() {
            document.getElementById('popup').style.display = 'block';
        }

        function hidePopup() {
            document.getElementById('popup').style.display = 'none';
        }

        function confirmLogout() {
            // Redirigir al usuario al login
            window.location.href = 'login.php';
        }

            // Obtener las tutorías del mismo alumno
        var tutorias = <?php echo json_encode($tutorias); ?>;
        var posicionActual = 0;

        function cambiarTutoria(direccion) {
            if (direccion === 'anterior') {
                if (posicionActual > 0) {
                    posicionActual--;
                } else {
                    // Si estamos en la primera tutoría, ir a la última
                    posicionActual = tutorias.length - 1;
                }
            } else if (direccion === 'siguiente') {
                if (posicionActual < tutorias.length - 1) {
                    posicionActual++;
                } else {
                    // Si estamos en la última tutoría, ir a la primera
                    posicionActual = 0;
                }
            }

            // Actualizar los datos de la tutoría
            var tutoria = tutorias[posicionActual];
            document.getElementById("nombre-tutor").innerHTML = "<span class='texto'><strong>Tutor:</strong></span> " + tutoria.nombre;
            document.getElementById("fecha-tutoria").innerHTML = "<span class='texto'><strong>Fecha:</strong></span> " + tutoria.fecha;
            document.getElementById("hora-tutoria").innerHTML = "<span class='texto'><strong>Hora:</strong></span> " + tutoria.hora;
            document.getElementById("tema-tutoria").innerHTML = "<span class='texto'><strong>Tema:</strong></span> " + tutoria.tema;
            document.querySelector(".curso").textContent = tutoria.nombre_curso + ":";
        }

    
    </script>
</body>
</html>
