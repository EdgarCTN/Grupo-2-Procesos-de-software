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
        $ruta_foto = empty($resultado['ruta_foto']) ? 'ruta/por/defecto/foto.jpg' : $resultado['ruta_foto'];
    } else {
        // Si el usuario no se encuentra, mostrar la imagen por defecto
        $ruta_foto = 'ruta/por/defecto/foto.jpg';
    }
} catch(PDOException $e) {
    // Manejar errores de base de datos
    $ruta_foto = 'ruta/error/foto.jpg'; // Otra ruta de una imagen de error
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Dashboard</title>
    <link rel="stylesheet" type="text/css" href="css/style_Administrador.css"><!-- Asegúrate de tener un archivo CSS específico para el administrador -->
    <style>
        /* Estilos adicionales para mejorar la apariencia */
        .main-content {
            display: flex;
            align-items: flex-start; /* Ajustado para mover la tarjeta más arriba */
            justify-content: flex-start; /* Ajustado para mantener la parte superior de bienvenida */
        }

        .user-info {
            max-width: 600px; /* Ajusta el ancho según sea necesario */
            margin-left: 20px; /* Ajustado para añadir espacio entre la imagen y la información */
            margin-top: 20px; /* Ajustado para mover la tarjeta más arriba */
        }

        .user-photo img {
            max-width: 200px;
            max-height: 200px;
            border-radius: 50%; /* Agregado para darle un borde redondeado a la imagen */
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
                <li><a href="dashboard_administrador.php">Inicio</a></li>
                <li><a href="agregar_usuarios.php">Agregar Usuarios</a></li>
                <li><a href="eliminar_usuario.php">Eliminar Usuario</a></li>
                <li><a href="agregar_curso.php">Agregar Curso</a></li>
                <li><a href="gestionar_tutoria.php">Gestionar Tutoría</a></li>
                <li><button onclick="showPopup()">Salir</button></li>
            </ul>
        </div>
        <div class="main-content">
            <?php
            if (isset($_SESSION['nombre'])) {
                echo "<div class='user-info card'>";
                echo "<div class='card-header'><h3>Bienvenido, $nombre_usuario</h3></div>";
                echo "<div class='card-body'>";
                echo "<div class='user-photo'>";
                if (file_exists($ruta_foto) && is_readable($ruta_foto)) {
                    echo "<img src='$ruta_foto' alt='Foto del usuario'>";
                } else {
                    echo "<img src='foto_default.png' alt='Foto del usuario por defecto'>";
                }
                echo "</div>";

                echo "<div class='admin-info'>";
                echo "<p>Administrador: <span>$nombre_usuario</span></p>";
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
