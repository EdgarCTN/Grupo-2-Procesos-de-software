<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <!-- Vincular al archivo CSS -->
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <!-- Menú de navegación -->
            <ul>
                <li><a href="menu.php">Inicio</a></li>
                <li><a href="agendar_tutoria.php">Agendar Tutoría</a></li>
                <li><a href="tutorias.php">Tutorías</a></li>
            </ul>
        </div>
        <div class="main-content">
            <!-- Aquí se mostrarán los datos del usuario -->
            <?php
            // Asegúrate de que el usuario ha iniciado sesión
            if (isset($_SESSION['nombre'])) {
                echo "<h2>Bienvenido, " . $_SESSION['nombre'] . "</h2>";
                // Aquí puedes agregar más código para mostrar los datos del usuario y del tutor
            } else {
                echo "<p>Por favor, inicia sesión para ver tus datos.</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>
