<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recuperar y procesar los datos del formulario
    $cod_curso = $_POST["cod_curso"];
    $nombre_curso = $_POST["nombre_curso"];
    $ciclo = $_POST["ciclo"];
    $creditos = $_POST["creditos"];

    // Conectar a la base de datos
    $conexion = new mysqli("localhost", "pma", "", "sma_unayoe");

    // Verificar la conexión
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    try {
        // Preparar la consulta para insertar un nuevo curso
        $consulta_curso = $conexion->prepare("INSERT INTO curso (cod_curso, nombre_curso, ciclo, creditos) VALUES (?, ?, ?, ?)");

        // Vincular los parámetros y ejecutar la consulta
        $consulta_curso->bind_param("ssii", $cod_curso, $nombre_curso, $ciclo, $creditos);
        $consulta_curso->execute();

        // Cerrar la conexión
        $conexion->close();

        // Redirigir a la página de administrador después de agregar el curso
        header("Location: dashboard_administrador.php");
        exit;
    } catch (Exception $e) {
        // Manejar el error (puedes personalizar según tus necesidades)
        echo "Error al agregar curso: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Curso - Dashboard</title>
    <link rel="stylesheet" type="text/css" href="css/style_Administrador.css"><!-- Asegúrate de tener un archivo CSS específico para el administrador -->
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
                <li><a href="dashboard_administrador.php">Inicio</a></li>
                <li><a href="agregar_usuarios.php">Agregar Usuarios</a></li>
                <li><a href="eliminar_usuario.php">Eliminar Usuario</a></li>
                <li><a href="agregar_curso.php">Agregar Curso</a></li>
                <li><a href="eliminar_curso.php">Eliminar Curso</a></li>
                <li><button onclick="showPopup()">Salir</button></li>
            </ul>
        </div>
        <div class="main-content">
            <h1>Agregar Curso</h1>

            <!-- Formulario para agregar cursos -->
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label for="cod_curso">Código del Curso:</label>
                <input type="text" id="cod_curso" name="cod_curso" required>

                <label for="nombre_curso">Nombre del Curso:</label>
                <input type="text" id="nombre_curso" name="nombre_curso" required>

                <label for="ciclo">Ciclo:</label>
                <input type="number" id="ciclo" name="ciclo" required>

                <label for="creditos">Créditos:</label>
                <input type="number" id="creditos" name="creditos" required>

                <button type="submit">Agregar Curso</button>
            </form>
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
            document.getElementById('popup').style.display = 'flex';
        }

        function hidePopup() {
            document.getElementById('popup').style.display = 'none';
        }

        function confirmLogout() {
            // Redirigir a la página de inicio de sesión o cerrar sesión según sea necesario
            window.location.href = 'logout.php';
        }
    </script>
</body>
</html>
