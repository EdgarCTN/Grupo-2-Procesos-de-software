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
            justify-content: space-between; /* Ajustado para alinear la tarjeta y la tabla a los extremos */
            flex-direction: row; /* Mantenido como fila para alinear elementos horizontalmente */
        }

        .user-info {
            max-width: 275px; /* Ajusta el ancho según sea necesario */
            margin-left: 10px; /* Ajustado para mover la tarjeta más a la izquierda */
            margin-top: 21px; /* Ajustado para mover la tarjeta más arriba */
        }

        .user-photo img {
            max-width: 200px;
            max-height: 200px;
            border-radius: 50%; /* Agregado para darle un borde redondeado a la imagen */
        }

        .table-container {
            max-width: 800px; /* Ajusta el ancho máximo de la tabla según sea necesario */
            margin-top: 20px; /* Ajusta el margen superior de la tabla según sea necesario */
            margin-left: 10px; /* Agregado espacio entre la tarjeta y la tabla */
            color: #fff; /* Cambiado a blanco */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px; /* Ajusta el espaciado interno de las celdas */
            text-align: left;
        }

        th {
            background-color: #4caf50; /* Color de fondo del encabezado de la tabla */
            color: white;
        }

        h1 {
            color: #fff; /* Cambiado a blanco */
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
                <!-- Puedes agregar enlaces adicionales según las necesidades del tutor -->
                <li><a href="dashboard_administrador.php">Inicio</a></li>
                <li><a href="agregar_usuarios.php">Agregar Usuarios</a></li>
                <li><a href="eliminar_usuario.php">Eliminar Usuario</a></li>
                <li><a href="agregar_curso.php">Agregar Curso</a></li>
                <li><a href="eliminar_curso.php">Eliminar Curso</a></li>
                <!-- Otros enlaces del menú -->
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

                // Obtener el nombre del administrador desde la base de datos
                $stmt_admin = $conn->prepare("SELECT nombre FROM usuarios WHERE nombre_usuario = :nombre_usuario");
                $stmt_admin->bindParam(':nombre_usuario', $nombre_usuario);
                $stmt_admin->execute();
                $admin_resultado = $stmt_admin->fetch(PDO::FETCH_ASSOC);
                $nombre_admin = $admin_resultado['nombre'];

                // Mostrar solo el nombre del administrador con color celeste
                echo "<div class='admin-info'>";
                echo "<p style='color: #000;'>Administrador: <span style='color: #007BFF;'>$nombre_admin</span></p>";
                echo "</div>";
                echo "</div>";
                echo "</div>";

            } else {
                echo "<p>Por favor, inicia sesión para ver tus datos.</p>";
            }
            ?>
            <!-- Mueve el título fuera del contenedor de la tabla -->
            <div class="table-container">
                <h1>Tutorías Programadas</h1>

                <!-- Tabla para mostrar la información de las tutorías -->
                <table>
                    <thead>
                        <tr>
                            <th>ID Tutoría</th>
                            <th>Código Alumno</th>
                            <th>Código Tutor</th>
                            <th>Código Curso</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Tema</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Aquí debes llenar la tabla con los datos de las tutorías desde la base de datos -->
                        <!-- Puedes utilizar un bucle PHP para recorrer los resultados y llenar las filas de la tabla -->
                        <!-- Ejemplo: -->
                        <?php
                        $conexion = new mysqli("localhost", "pma", "", "sma_unayoe");

                        if ($conexion->connect_error) {
                            die("Error de conexión: " . $conexion->connect_error);
                        }

                        // Consulta para obtener la información de las tutorías
                        $consulta_tutorias = $conexion->query("SELECT * FROM tutoría");

                        while ($fila = $consulta_tutorias->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $fila['id_tutoria'] . "</td>";
                            echo "<td>" . $fila['codalumno'] . "</td>";
                            echo "<td>" . $fila['codtutor'] . "</td>";
                            echo "<td>" . $fila['codcurso'] . "</td>";
                            echo "<td>" . $fila['fecha'] . "</td>";
                            echo "<td>" . $fila['hora'] . "</td>";
                            echo "<td>" . $fila['tema'] . "</td>";
                            echo "</tr>";
                        }

                        // Cierra la conexión
                        $conexion->close();
                        ?>
                    </tbody>
                </table>
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

    <script>
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