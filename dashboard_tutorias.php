<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Archivo de conexión a la base de datos
include 'conn/connection.php';

$nombre_usuario = $_SESSION['nombre'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recuperar y procesar los datos del formulario
    $codalumno = $_POST["codalumno"];
    $codtutor = $_POST["codtutor"];
    $codcurso = $_POST["codcurso"];
    $fecha = $_POST["fecha"];
    $hora = $_POST["hora"];
    $tema = $_POST["tema"];

    // Conectar a la base de datos
    $conexion = new mysqli("localhost:3307", "pma", "", "sma_unayoe");

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
        header("Location: dashboard_tutorias.php");
        exit;
    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        $conexion->rollback();

        // Manejar el error (puedes personalizar según tus necesidades)
        echo "Error al agregar tutoría: " . $e->getMessage();
    }
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
                <li><a href="dashboard_alumno.php">Inicio</a></li>
                <li><a href="notas.php">Notas</a></li>
                <li><a href="dashboard_tutorias.php">Tutorías</a></li>
                <li><a href="horario.php">Horario</a></li>
                <li><a href="objetivos.php">Objetivos</a></li>
                <li><button onclick="showPopup()">Salir</button></li>
            </ul>
        </div>
        <div class="main-content">
            <?php
            if (isset($_SESSION['nombre'])) {
                echo "<div class='welcome-message'>Bienvenido, " . $_SESSION['nombre'] .$cod_alumno. "</div>";
                           
            } else {
                echo "<p>Por favor, inicia sesión para ver tus datos.</p>";
            }
            ?>
            <h1>Agregar Tutoría</h1>
            
            <!-- Formulario para agregar tutoría -->
            <!-- Formulario para agregar tutoría -->
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label for="codalumno">Código Alumno:</label>
                
                <input type="text" name="codalumno" value="<?php echo htmlspecialchars($cod_alumno); ?>" readonly required>

                <label for="codtutor">Código Tutor:</label>
                <select name="codtutor" required>
                    <!-- Opciones para seleccionar el código del tutor -->
                    <?php
                    $conexion = new mysqli("localhost:3307", "pma", "", "sma_unayoe");

                    if ($conexion->connect_error) {
                        die("Error de conexión: " . $conexion->connect_error);
                    }

                    // Consulta para obtener los códigos de los tutores ordenados alfabéticamente
                    $consulta_tutores = $conexion->query("SELECT cod_tutor FROM tutor ORDER BY nombre");

                    while ($fila = $consulta_tutores->fetch_assoc()) {
                        echo "<option value='" . $fila['cod_tutor'] . "'>" . $fila['cod_tutor'] . "</option>";
                    }

                    $conexion->close();
                    ?>
                </select>

                <label for="codcurso">Código Curso:</label>
                <select name="codcurso" required>
                    <!-- Opciones para seleccionar el código del curso -->
                    <?php
                    $conexion = new mysqli("localhost:3307", "pma", "", "sma_unayoe");

                    if ($conexion->connect_error) {
                        die("Error de conexión: " . $conexion->connect_error);
                    }

                    // Consulta para obtener los códigos de los cursos ordenados alfabéticamente
                    $consulta_cursos = $conexion->query("SELECT cod_curso FROM curso ORDER BY nombre_curso");

                    while ($fila = $consulta_cursos->fetch_assoc()) {
                        echo "<option value='" . $fila['cod_curso'] . "'>" . $fila['cod_curso'] . "</option>";
                    }

                    $conexion->close();
                    ?>
                </select>

                <label for="fecha">Fecha:</label>
                <input type="date" name="fecha" required>

                <label for="hora">Hora:</label>
                <input type="time" name="hora" required>

                <label for="tema">Tema:</label>
                <input type="text" name="tema" required>

                <button type="submit">Agregar Tutoría</button>
            </form>        
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
