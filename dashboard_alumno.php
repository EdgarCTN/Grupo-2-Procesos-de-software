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
        $ruta_foto = "ruta/por/defecto/foto.jpg";
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

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@700&display=swap">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Dashboard</title>
    <style>
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
                <li><a href="notas.php">Notas</a></li>
                <li><a href="tutorias.php">Tutorías</a></li>
                <li><a href="horario.php">Horario</a></li>
                <li><a href="objetivos.php">Objetivos</a></li>
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
            <!-- Agregar la imagen del usuario -->
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
    </script>
</body>
</html>
