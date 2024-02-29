<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Archivo de conexión a la base de datos
include 'conn/connection.php';

$nombre_usuario = $_SESSION['nombre'];

try {
    // Preparar la consulta SQL para obtener el ID del usuario basado en el nombre de usuario
    $stmt_usuario = $conn->prepare("SELECT id FROM usuarios WHERE nombre_usuario = :nombre_usuario");
    $stmt_usuario->bindParam(':nombre_usuario', $_SESSION['nombre']);
    $stmt_usuario->execute();

    // Verificar si se encontró el usuario
    if ($stmt_usuario->rowCount() > 0) {
        $resultado_usuario = $stmt_usuario->fetch(PDO::FETCH_ASSOC);
        $id_usuario = $resultado_usuario['id'];

        // Preparar la consulta SQL para obtener el código del tutor usando el ID del usuario
        $stmt_tutor = $conn->prepare("SELECT cod_tutor FROM tutor WHERE id_usuario = :id_usuario");
        $stmt_tutor->bindParam(':id_usuario', $id_usuario);
        $stmt_tutor->execute();

        // Verificar si se encontró el tutor
        if ($stmt_tutor->rowCount() > 0) {
            $resultado_tutor = $stmt_tutor->fetch(PDO::FETCH_ASSOC);
            $codigo_tutor = $resultado_tutor['cod_tutor'];
        } else {
            // Si el tutor no se encuentra, mostrar un mensaje de error o asignar un valor predeterminado
            $codigo_tutor = "No disponible";
        }
    } else {
        // Si no se encuentra el usuario, mostrar un mensaje de error o asignar valores por defecto
        $codigo_tutor = "No disponible";
    }
} catch(PDOException $e) {
    // Manejar errores de base de datos
    $codigo_tutor = "Error";
    echo "Error al obtener el código del tutor: " . $e->getMessage();
}

// Procesamiento del formulario de agregar tutoría
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recuperar y procesar los datos del formulario
    $codalumno = $_POST["codalumno"];
    $codtutor = $_POST["codtutor"];
    $codcurso = $_POST["codcurso"];
    $fecha = $_POST["fecha"];
    $hora = $_POST["hora"];
    $tema = $_POST["tema"];

    // Conectar a la base de datos
    $conexion = new mysqli("localhost", "pma", "root", "sma_unayoe");

    // Verificar la conexión
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    try {
        // Comenzar una transacción
        $conexion->begin_transaction();

        // Preparar la consulta para insertar una nueva tutoría
        $consulta_tutoria = $conexion->prepare("INSERT INTO tutoría (codalumno, codtutor, codcurso, fecha, hora, tema) VALUES (?, ?, ?, ?, ?, ?)");

        // Vincular los parámetros y ejecutar la consulta
        $consulta_tutoria->bind_param("ssssss", $codalumno, $codtutor, $codcurso, $fecha, $hora, $tema);
        $consulta_tutoria->execute();

        // Confirmar la transacción
        $conexion->commit();

        // Cerrar la conexión
        $conexion->close();

        // Redirigir a la página de tutorías después de agregar la tutoría
        header("Location: dashboard_tutor.php");
        exit;
    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        $conexion->rollback();

        // Manejar el error (puedes personalizar según tus necesidades)
        echo "Error al agregar tutoría: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Dashboard</title>
    <link rel="stylesheet" type="text/css" href="css/styles_profesor_tutoria.css">
  


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
                echo "<div class='welcome-message'>Bienvenido, " . $_SESSION['nombre']."</div>";
            } else {
                echo "<p>Por favor, inicia sesión para ver tus datos.</p>";
            }
            ?>

            <!-- Botón para ir a dashboard_tutorias_profesor_2.php -->
            <div>
                <button onclick="location.href='dashboard_tutorias_profesor_2.php';">Crear nueva tutoria</button>
            </div>

            <!-- Formulario para mostrar las tutorías -->
            <div class="cont_general">                       
                <div class="contenedor-datos">
                    <h2>Tutorías asignadas</h2>
                    <!-- Aquí debes agregar la lógica para mostrar las tutorías del tutor -->
                    <?php
                    // Consulta para obtener las tutorías del tutor
                    $stmt_tutorias = $conn->prepare("SELECT * FROM tutoría WHERE codtutor = :codtutor");
                    $stmt_tutorias->bindParam(':codtutor', $codigo_tutor);
                    $stmt_tutorias->execute();

                    // Verificar si hay tutorías
                    if ($stmt_tutorias->rowCount() > 0) {
                        echo "<table border='1'>";
                        echo "<tr><th>Código Alumno</th><th>Código Curso</th><th>Fecha</th><th>Hora</th><th>Tema</th><th>Finalizar Tutoría</th></tr>";
                        while ($fila = $stmt_tutorias->fetch(PDO::FETCH_ASSOC)) {
                            echo "<tr>";
                            echo "<td>" . $fila['codalumno'] . "</td>";
                            echo "<td>" . $fila['codcurso'] . "</td>";
                            echo "<td>" . $fila['fecha'] . "</td>";
                            echo "<td>" . $fila['hora'] . "</td>";
                            echo "<td>" . $fila['tema'] . "</td>";
                            echo "<td><button class='finalizar-btn' onclick='finalizarTutoria(" . $fila['id_tutoria'] . ")'>Finalizar</button></td>";
                            echo "</tr>";
                        }
                        echo "</table>";
                    } else {
                        echo "<p>No hay tutorías asignadas.</p>";
                    }
                    ?>
                </div>
            </div> 
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

        function finalizarTutoria(tutoriaId) {
            // Redirigir a la página para finalizar la tutoría, pasando el ID de la tutoría como parámetro
            window.location.href = 'finalizar_tutoria.php?tutoriaId=' + tutoriaId;
        }
    </script>
</body>
</html>
