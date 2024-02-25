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

        // Preparar la consulta SQL para obtener los datos del tutor usando el ID del usuario
        $stmt_tutor = $conn->prepare("SELECT cod_tutor, apellidos, nombre, correo, numero_celular FROM tutor WHERE id_usuario = :id_usuario");
        $stmt_tutor->bindParam(':id_usuario', $id_usuario);
        $stmt_tutor->execute();

        // Verificar si se encontró el tutor
        if ($stmt_tutor->rowCount() > 0) {
            $resultado_tutor = $stmt_tutor->fetch(PDO::FETCH_ASSOC);
            $cod_tutor = $resultado_tutor['cod_tutor'];
            $apellidos = $resultado_tutor['apellidos'];
            $nombre = $resultado_tutor['nombre'];
            $correo = $resultado_tutor['correo'];
        } else {
            // Si el tutor no se encuentra, mostrar un mensaje de error o asignar valores por defecto
            $cod_tutor = "No se ha asignado un tutor al id del usuario";
            $apellidos = "No se ha asignado un tutor al id del usuario";
            $nombre = "No se ha asignado un tutor al id del usuario";
            $correo = "No se ha asignado un tutor al id del usuario";
        }
    } else {
        // Si no se encuentra el usuario, mostrar un mensaje de error o asignar valores por defecto
        $cod_tutor = "No disponible";
        $apellidos = "No disponible";
        $nombre = "No disponible";
        $correo = "No disponible";
    }
} catch(PDOException $e) {
    // Manejar errores de base de datos
    $cod_tutor = "Error";
    $apellidos = "Error";
    $nombre = "Error";
    $correo = "Error";
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Dashboard</title>
    <link rel="stylesheet" type="text/css" href="css/style_Tutor.css"><!-- Aquí se agrega el enlace al archivo CSS -->
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
                <li><a href="dashboard_tutorias_profesor.php">Tutorias</a></li>
                <li><button onclick="showPopup()">Salir</button></li>
            </ul>
        </div>
        <div class="main-content">
            <?php
            if (isset($_SESSION['nombre'])) {
                echo "<div class='welcome-message'>Bienvenido, " . $nombre_usuario . "</div>";

                // Bienvenida al usuario
                echo "<div class='welcome-message'>Sistema de monitoreo de alumnos observados</div>";

                // Mostrar información del tutor
                echo "<div class='tutor-info card'>";
                echo "<div class='card-header'><h3>Información del Tutor:</h3></div>";
                echo "<div class='card-body'>";
                echo "<p>Código de Tutor: <span>$cod_tutor</span></p>";
                echo "<p>Nombre: <span>$nombre</span></p>";
                echo "<p>Apellidos: <span>$apellidos</span></p>";
                echo "<p>Correo: <span>$correo</span></p>";
                echo "</div>";
                echo "</div>";

                // Resumen del estado actual
                echo "<div class='status-summary card'>";
                echo "<div class='card-header'><h3>Resumen del estado actual:</h3></div>";
                echo "<div class='card-body'>";
                echo "<p>Total de alumnos: <span>XX</span></p>";
                echo "<p>Alumnos con 2 repitencias: <span>XX</span></p>";
                echo "<p>Alumnos con 3 repitencias (aunque no trabajamos con número de repitencias XD): <span>XX</span></p>";
                echo "</div>";
                echo "</div>";

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
                echo "</div>";
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
