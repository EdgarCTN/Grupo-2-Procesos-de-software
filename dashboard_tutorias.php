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
            
        </div>
    </div>

    <!-- mensaje confirmacion para salir sesion -->
    <div class="popup" id="popup">
        <div class="popup-content">
            <p>Confirmar salida</p>
            <div class="popup-buttons">
                <button onclick="confirmLogout()">Confirmar</button>
                <button onclick="hidePopup()">Rechazar</button>
            </div>
        </div>
    </div>

    <!-- ocultar menu -->
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
