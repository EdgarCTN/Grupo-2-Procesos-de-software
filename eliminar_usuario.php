<?php
// Agrega tu código de conexión a la base de datos aquí
$conexion = new mysqli("localhost", "pma", "", "sma_unayoe");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["eliminar"])) {
    // Recuperar y procesar los datos del formulario
    $codigo_usuario = $_POST["codigo"];
    $nombre_usuario = $_POST["nombre_usuario"];
    $rol = ucfirst($_POST["rol"]);

    // Desactivar restricciones de clave externa temporalmente
    $conexion->query("SET foreign_key_checks = 0");

    // Realizar la eliminación
    $resultado = eliminarUsuario($codigo_usuario, $rol);

    // Volver a activar restricciones de clave externa
    $conexion->query("SET foreign_key_checks = 1");

    // Mostrar el resultado
    echo $resultado;
}

// Obtener la lista de usuarios (alumnos y tutores)
$sql = "SELECT id, nombre, nombre_usuario, rol FROM usuarios WHERE rol IN ('Alumno', 'Tutor')";
$resultado = $conexion->query($sql);

// Función para eliminar al usuario y la tutoría asociada si existe
function eliminarUsuario($codigo_usuario, $rol)
{
    global $conexion;

    // Eliminar la tutoría asociada si existe
    $sql_eliminar_tutoria = ($rol === 'Tutor') ? "DELETE FROM tutoría WHERE codtutor = ?" : "DELETE FROM tutoría WHERE codalumno = ?";
    $stmt_eliminar_tutoria = $conexion->prepare($sql_eliminar_tutoria);
    $stmt_eliminar_tutoria->bind_param("s", $codigo_usuario);

    if (!$stmt_eliminar_tutoria->execute()) {
        return "Error al eliminar tutoría: " . $stmt_eliminar_tutoria->error;
    }

    // Eliminar el usuario de las tablas dependiendo del rol
    $sql_eliminar_usuario = "DELETE usuarios, $rol FROM usuarios LEFT JOIN $rol ON usuarios.id = $rol.id_usuario WHERE usuarios.id = ?";
    $stmt_eliminar_usuario = $conexion->prepare($sql_eliminar_usuario);
    $stmt_eliminar_usuario->bind_param("s", $codigo_usuario);

    if ($stmt_eliminar_usuario->execute()) {
        $stmt_eliminar_usuario->close();
        return "Usuario eliminado correctamente.";
    } else {
        return "Error al eliminar el usuario: " . $stmt_eliminar_usuario->error;
    }
}

// Cerrar la conexión
$conexion->close();
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Usuario - Dashboard</title>
    <link rel="stylesheet" type="text/css" href="css/style_Tutor.css">
    <!-- Asegúrate de tener un archivo CSS específico para el tutor -->
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            color: #fff; /* Color blanco para el texto en la tabla */
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #218838; /* Color verde oscuro para los encabezados */
            color: #fff; /* Color blanco para el texto en los encabezados */
        }

        th:first-child, td:first-child {
            background-color: #28a745; /* Color verde para la primera columna (ID) */
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
            background-color: #e74c3c; /* Color de fondo del botón */
            color: #fff;
            cursor: pointer;
        }

        form button:hover {
            background-color: #c0392b; /* Cambio de color al pasar el ratón */
        }

        /* Popup de confirmación */
        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .popup p {
            color: #000; /* Texto negro en el mensaje de confirmación */
        }

        .popup-buttons {
            display: flex;
            justify-content: space-between;
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
            <h1>Eliminar Usuario</h1>

            <!-- Tabla de usuarios con opción de eliminar -->
            <?php
            if ($resultado->num_rows > 0) {
                echo "<table>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Nombre de Usuario</th>
                            <th>Rol</th>
                            <th>Acciones</th>
                        </tr>";

                while ($fila = $resultado->fetch_assoc()) {
                    echo "<tr>
                            <td>{$fila['id']}</td>
                            <td>{$fila['nombre']}</td>
                            <td>{$fila['nombre_usuario']}</td>
                            <td>{$fila['rol']}</td>
                            <td>
                                <form method='post' action='eliminar_usuario.php' onsubmit='return confirmarEliminacion(\"{$fila['id']}\", \"{$fila['nombre_usuario']}\", \"{$fila['rol']}\")'>
                                    <input type='hidden' name='codigo' value='{$fila['id']}'>
                                    <input type='hidden' name='nombre_usuario' value='{$fila['nombre_usuario']}'>
                                    <input type='hidden' name='rol' value='{$fila['rol']}'>
                                    <input type='hidden' name='contrasena' value=''>
                                    <button type='submit' name='eliminar'>Eliminar</button>
                                </form>
                            </td>
                          </tr>";
                }

                echo "</table>";
            } else {
                echo "No se encontraron usuarios.";
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
    <script>
        function confirmarEliminacion(codigoUsuario, nombreUsuario, rol) {
            return confirm(`¿Estás seguro que deseas eliminar al usuario ${nombreUsuario}?`);
        }
    </script>
</body>
</html>
