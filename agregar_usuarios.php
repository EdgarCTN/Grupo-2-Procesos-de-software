<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recuperar y procesar los datos del formulario
    $nombre_usuario = $_POST["nombre_usuario"];
    $password = $_POST["password"];
    $rol = ucfirst($_POST["rol"]); // Convertir la primera letra a mayúscula
    $nombre = $_POST["nombre"];
    $apellidos = $_POST["apellidos"];
    $cod_usuario = $_POST["codigo"];
    $facultad = $_POST["facultad"];
    $correo = $_POST["correo"];
    $numero_celular = $_POST["numero_celular"];

    // Conectar a la base de datos
    $conexion = new mysqli("localhost", "pma", "", "sma_unayoe");

    // Verificar la conexión
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    try {
        // Comenzar una transacción
        $conexion->begin_transaction();

        // Modificar el formato del nombre antes de la inserción en usuarios
        $nombre_completo = "$apellidos, $nombre";

        // Preparar la consulta para insertar un nuevo usuario en la tabla usuarios
        $consulta_usuario = $conexion->prepare("INSERT INTO usuarios (nombre, nombre_usuario, contraseña, rol, ruta_foto) VALUES (?, ?, ?, ?, ?)");
        $consulta_usuario->bind_param("sssss", $nombre_completo, $nombre_usuario, $password, $rol, $valor_por_defecto_ruta_foto);

        // Asignar una cadena vacía como valor por defecto para la columna ruta_foto
        $valor_por_defecto_ruta_foto = "";

        // Vincular los parámetros y ejecutar la consulta
        $consulta_usuario->execute();

        // Obtener el ID del usuario recién insertado
        $id_usuario = $conexion->insert_id;

        // Según el rol, insertar datos adicionales en la tabla correspondiente
        switch ($rol) {
            case "Alumno":
                $consulta_alumno = $conexion->prepare("INSERT INTO alumno (cod_alumno, id_usuario, nombre, apellidos, correo, facultad, numero_celular) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $consulta_alumno->bind_param("sisssss", $cod_usuario, $id_usuario, $nombre, $apellidos, $correo, $facultad, $numero_celular);
                $consulta_alumno->execute();
                break;

            case "Tutor":
                $consulta_tutor = $conexion->prepare("INSERT INTO tutor (cod_tutor, id_usuario, nombre, apellidos, correo, numero_celular) VALUES (?, ?, ?, ?, ?, ?)");
                $consulta_tutor->bind_param("sissss", $cod_usuario, $id_usuario, $nombre, $apellidos, $correo, $numero_celular);
                $consulta_tutor->execute();
                break;

            // Agregar casos para otros roles si es necesario

            default:
                // Otro código si no se trata de alumno ni tutor
                break;
        }

        // Confirmar la transacción
        $conexion->commit();

        // Cerrar la conexión
        $conexion->close();

        // Mensaje de confirmación
        echo "<script>alert('Usuario agregado exitosamente');</script>";
    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        $conexion->rollback();

        // Manejar el error (puedes personalizar según tus necesidades)
        echo "Error al agregar usuario: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Usuarios - Dashboard</title>
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
                <li><a href="dashboard_administrador.php">Inicio</a></li>
                <li><a href="agregar_usuarios.php">Agregar Usuarios</a></li>
                <li><a href="eliminar_usuario.php">Eliminar Usuario</a></li>
                <li><a href="agregar_curso.php">Agregar Curso</a></li>
                <li><a href="eliminar_curso.php">Eliminar Curso</a></li>
                <li><button onclick="showPopup()">Salir</button></li>
            </ul>
        </div>
        <div class="main-content">
            <h1>Agregar Usuarios</h1>

            <!-- Formulario para agregar usuarios -->
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label for="nombre_usuario">Nombre de Usuario:</label>
                <input type="text" id="nombre_usuario" name="nombre_usuario" required>

                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>

                <label for="rol">Rol:</label>
                <select id="rol" name="rol" required>
                    <!-- Opciones de roles (puedes personalizar según tus necesidades) -->
                    <option value="tutor">Tutor</option>
                    <option value="alumno">Alumno</option>
                    <option value="administrador">Administrador</option>
                </select>

                <!-- Campos adicionales según el rol -->
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required>

                <label for="apellidos">Apellidos:</label>
                <input type="text" id="apellidos" name="apellidos" required>

                <label for="codigo">Código:</label>
                <input type="text" id="codigo" name="codigo" required>

                <label for="facultad">Facultad:</label>
                <input type="text" id="facultad" name="facultad" required>

                <label for="correo">Correo:</label>
                <input type="email" id="correo" name="correo" required>

                <label for="numero_celular">Número de Celular:</label>
                <input type="text" id="numero_celular" name="numero_celular" required>

                <button type="submit">Agregar Usuario</button>
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
        function showAgregarAlumnoForm() {
            // Redirigir a la página de agregar alumno
            window.location.href = 'dashboard_agregar_alumno.php';
        }

        function showAgregarTutorForm() {
            // Redirigir a la página de agregar tutor
            window.location.href = 'dashboard_agregar_tutor.php';
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
