<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #1e272e; /* Cambiar el color de fondo a azul oscuro */
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
            margin-bottom: 15px;
        }
        .sidebar a {
            color: #fff;
            text-decoration: none;
            font-size: 20px;
            font-weight: bold;
            display: block;
            padding: 10px 15px;
            border-radius: 10px;
            transition: background-color 0.3s;
            background-color: #455a64;
        }
        .sidebar a:hover {
            background-color: #607d8b;
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
            background-color: #1e272e; /* Cambiar el color de fondo a azul oscuro */
            padding: 20px;
            flex: 1;
        }
        .welcome-message {
            margin-bottom: 20px;
            font-size: 24px;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 10px;
            color: #fff; /* Cambiar el color del texto a blanco */
        }
        .status-summary {
            margin-bottom: 20px;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 20px;
            color: #fff; /* Cambiar el color del texto a blanco */
        }
        .status-summary h3 {
            font-size: 1.5em; /* Ajustar el tamaño del encabezado */
            margin-bottom: 10px;
        }
        .status-summary p {
            font-size: 18px; /* Ajustar el tamaño del texto del resumen */
            margin: 10px 0;
        }
        .status-summary p span {
            font-weight: bold;
            color: #3498db; /* Cambiar el color del número */
        }
        .status-summary .details {
            margin-top: 15px;
            border-top: 1px solid #34495e;
            padding-top: 15px;
            font-size: 16px;
        }
        .course-table {
            margin-top: 20px;
        }
        .course-table table {
            width: 50%;
            border-collapse: collapse;
        }
        .course-table th, .course-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .course-table th {
            background-color: #3498db;
            color: #fff; /* Cambiar el color del texto a blanco */
        }
        .course-table td {
            background-color: #ecf0f1;
            color: #000; /* Cambiar el color del texto a negro */
        }
        .course-table tr:hover {
            background-color: #f5f5f5;
        }
        .course-table tr:first-child th {
            background-color: #5DADE2; /* Celeste */
        }
        .course-table tr:first-child th {
            color: #fff; /* Blanco */
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
    </style>
</head>
    
<body>
    <div class="container">
        <div class="sidebar" id="sidebar">
            <div class="sidebar-logo">
                <img src="fisi.png" alt="Logo Facultad">
            </div>
            <ul>
                <li><a href="dashboard_tutor.php">Inicio</a></li>
                <li><a href="dashboard_tabla_profesor.php">Alumnos</a></li>
                <li><button onclick="showPopup()">Salir</button></li>
            </ul>
        </div>
        <div class="main-content">
            <?php
            if (isset($_SESSION['nombre'])) {
                echo "<div class='welcome-message'>Bienvenido, " . $_SESSION['nombre'] . "</div>";

                // Bienvenida al tutor
                echo "<div class='welcome-message'>Sistema de ayuda para alumnos observados!</div>";

                // Resumen del estado actual
                echo "<div class='status-summary'>";
                echo "<h3>Resumen del estado actual:</h3>";
                echo "<p>Total de alumnos: <span>XX</span></p>";
                echo "<p>Alumnos con 2 repitencias: <span>XX</span></p>";
                echo "<p>Alumnos con 3 repitencias (aunque no trabajamos con numero de repitencias XD): <span>XX</span></p>";
                // Puedes agregar más detalles según sea necesario
                echo "<div class='details'><h3>Distribución de alumnos";
                echo "</div>";

                // Agregar la tabla de cursos y número de alumnos
                echo "<div class='course-table'>";
                echo "<table>";
                echo "<tr><th>Nombre del curso</th><th>Ciclo </th><th>Número de alumnos</th></tr>";
                echo "<tr><td>Procesos de software</td><td>4</td><td>6</td></tr>"; // Ejemplo de ciclo y datos
                echo "<tr><td>Curso X</td><td>5</td><td>30</td></tr>"; // Ejemplo de ciclo y datos
                // Puedes agregar más filas según sea necesario
                echo "</table>";
                echo "</div>";

            } else {
                echo "<p>Por favor, inicia sesión para ver tus datos.</p>";
            }
            ?>
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
