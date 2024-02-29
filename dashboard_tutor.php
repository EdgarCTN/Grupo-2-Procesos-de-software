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
try {
    $stmt_alumnos = $conn->prepare("SELECT COUNT(*) AS total_alumnos FROM sma_unayoe.alumno");
    $stmt_alumnos->execute();
    $total_alumnos_result = $stmt_alumnos->fetch(PDO::FETCH_ASSOC);
    $total_alumnos = $total_alumnos_result['total_alumnos'];
} catch(PDOException $e) {
    // Manejar errores de base de datos al obtener el número de alumnos
    $total_alumnos = "Error";
}

try {
    $stmt_tutores = $conn->prepare("SELECT COUNT(*) AS total_tutores FROM sma_unayoe.tutor");
    $stmt_tutores->execute();
    $total_tutores_result = $stmt_tutores->fetch(PDO::FETCH_ASSOC);
    $total_tutores = $total_tutores_result['total_tutores'];
} catch(PDOException $e) {
    // Manejar errores de base de datos al obtener el número de alumnos
    $total_tutores = "Error";
}
try {
    // Consulta SQL para obtener el número de tutorías
    $stmt_tutorias = $conn->prepare("SELECT COUNT(*) AS total_tutorias FROM sma_unayoe.tutoría");
    $stmt_tutorias->execute();
    $total_tutorias_result = $stmt_tutorias->fetch(PDO::FETCH_ASSOC);
    $total_tutorias = $total_tutorias_result['total_tutorias'];
} catch(PDOException $e) {
    // Manejar errores de base de datos al obtener el número de tutorías
    $total_tutorias = "Error";
}


try {
    // Consulta SQL para obtener el número total de objetivos
    $stmt_objetivo = $conn->prepare("SELECT COUNT(*) AS total_objetivo FROM sma_unayoe.objetivo");
    $stmt_objetivo->execute();
    $total_objetivo_result = $stmt_objetivo->fetch(PDO::FETCH_ASSOC);
    $total_objetivo = $total_objetivo_result['total_objetivo'];

    // Calcular el número promedio de objetivos por alumno
    if ($total_alumnos > 0) {
        $total_objetivo_promedio = $total_objetivo / $total_alumnos;
    } else {
        $total_objetivo_promedio = 0; // Evitar división por cero
    }
} catch(PDOException $e) {
    // Manejar errores de base de datos al obtener el número de objetivos
    $total_objetivo = "Error";
    $total_objetivo_promedio = "Error";
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Dashboard</title>
    <link rel="stylesheet" type="text/css" href="css/style_Tutor.css"><!-- Aquí se agrega el enlace al archivo CSS -->
    <style>
        .tutor-info .card-body {
            display: flex;
            align-items: center;
        }

        .tutor-image img {
            width: 100px; /* Ajusta el ancho según sea necesario */
            height: auto; /* Para mantener la proporción de la imagen */
            margin-right: 20px; /* Espacio entre la imagen y los detalles */
        }

        .tutor-details {
            flex: 1; /* Para que los detalles ocupen todo el espacio disponible */
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
                echo "<div class='tutor-image'>";
                echo "<img src='$ruta_foto' alt='Foto del Tutor'>";
                echo "</div>";
                echo "<div class='tutor-details'>";
                echo "<p>Código de Tutor: <span>$cod_tutor</span></p>";
                echo "<p>Nombre: <span>$nombre</span></p>";
                echo "<p>Apellidos: <span>$apellidos</span></p>";
                echo "<p>Correo: <span>$correo</span></p>";
                echo "</div>";
                echo "</div>";
                echo "</div>";

                // Resumen del estado actual
                echo "<div class='status-summary card'>";
                echo "<div class='card-header'><h3>Resumen del estado actual:</h3></div>";
                echo "<div class='card-body'>";
                echo "<p>Total de alumnos: <span>$total_alumnos</span></p>";
                echo "<p>Total de tutores: <span>$total_tutores</span></p>";
                echo "<p>Tutorias pendientes totales: <span>$total_tutorias</span></p>";
                echo "<p>Objetivos promedio por alumno: <span>" . number_format($total_objetivo_promedio, 1) . "</span></p>";
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

