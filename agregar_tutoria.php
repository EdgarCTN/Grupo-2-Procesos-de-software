<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recuperar y procesar los datos del formulario
    $codalumno = $_POST["codalumno"];
    $codtutor = $_POST["codtutor"];
    $codcurso = $_POST["codcurso"];
    $fecha = $_POST["fecha"];
    $hora = $_POST["hora"];
    $tema = $_POST["tema"];

    // Conectar a la base de datos
    $conexion = new mysqli("localhost", "pma", "", "sma_unayoe");

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
        header("Location: dashboard_administrador.php");
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
    <title>Agregar Tutoría - Dashboard</title>
    <link rel="stylesheet" type="text/css" href="css/style_Tutor.css"><!-- Asegúrate de tener un archivo CSS específico para el tutor -->
    <style>
        /* Estilos adicionales para mejorar la apariencia */
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background-color: #0A3D62; /* Fondo celeste oscuro */
            color: #fff; /* Texto blanco */
        }

        .container {
            display: flex;
            max-width: 1200px;
            margin: 20px auto;
            background-color: #fff; /* Color de fondo de la tarjeta */
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Sombra de la tarjeta */
            color: #1e3a5c;
        }

        .sidebar {
            width: 250px;
            padding: 20px;
            background-color: #333; /* Color de fondo de la barra lateral */
            color: #fff;
        }

        .main-content {
            flex: 1;
            padding: 20px;
        }

        .main-content h1 {
            color: #fff; /* Color blanco para el encabezado h1 */
        }

        form {
            max-width: 400px;
            margin: auto;
            padding: 20px;
            background-color: #fff; /* Color de fondo del formulario */
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Sombra del formulario */
        }

        form label {
            display: block;
            margin-bottom: 8px;
        }

        form input,
        form select,
        form button {
            width: 100%;
            padding: 10px;
            margin-bottom: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        form button {
            background-color: #4caf50; /* Color de fondo del botón */
            color: #fff;
            cursor: pointer;
        }

        form button:hover {
            background-color: #45a049; /* Cambio de color al pasar el ratón */
        }

        .popup-content {
            background-color: transparent; /* Hace que el fondo del mensaje de confirmación sea transparente */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .popup-content p {
            color: #000; /* Texto negro en el mensaje de confirmación */
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
                <li><a href="dashboard_tutor.php">Inicio</a></li>
                <li><a href="agregar_tutoria.php">Agregar Tutoría</a></li>
                <li><a href="ver_tutorias.php">Ver Tutorías</a></li>
                <li><button onclick="showPopup()">Salir</button></li>
            </ul>
        </div>
        <div class="main-content">
            <h1>Agregar Tutoría</h1>

            <!-- Formulario para agregar tutoría -->
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label for="codalumno">Código Alumno:</label>
                <select name="codalumno" required>
                    <!-- Opciones para seleccionar el código del alumno -->
                    <?php
                    $conexion = new mysqli("localhost", "pma", "", "sma_unayoe");

                    if ($conexion->connect_error) {
                        die("Error de conexión: " . $conexion->connect_error);
                    }

                    // Consulta para obtener los códigos de los alumnos ordenados alfabéticamente
                    $consulta_alumnos = $conexion->query("SELECT cod_alumno FROM alumno ORDER BY nombre");

                    while ($fila = $consulta_alumnos->fetch_assoc()) {
                        echo "<option value='" . $fila['cod_alumno'] . "'>" . $fila['cod_alumno'] . "</option>";
                    }

                    $conexion->close();
                    ?>
                </select>

                <label for="codtutor">Código Tutor:</label>
                <select name="codtutor" required>
                    <!-- Opciones para seleccionar el código del tutor -->
                    <?php
                    $conexion = new mysqli("localhost", "pma", "", "sma_unayoe");

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
                    $conexion = new mysqli("localhost", "pma", "", "sma_unayoe");

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

    <!-- Popup de confirmación para salir -->
    <div class="popup" id="popup">
        <div class="popup-content">
            <p>¿Seguro que deseas salir?</p>
            <button onclick="location.href='index.php'">Sí</button>
            <button onclick="hidePopup()">No</button>
        </div>
    </div>

    <script>
        function showPopup() {
            document.getElementById("popup").style.display = "flex";
        }

        function hidePopup() {
            document.getElementById("popup").style.display = "none";
        }
    </script>
</body>
</html>
