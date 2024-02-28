<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Coloca aquí el código para gestionar la tutoría si es necesario
    // Recupera los datos del formulario
    $tutoriaId = $_POST["tutoria_id"];  // Asegúrate de que el campo del formulario coincida con el que estás utilizando
    $accion = $_POST["accion"];

    // Realiza la lógica correspondiente según la acción
    switch ($accion) {
        case "aprobar":
            // Coloca aquí la lógica para aprobar la tutoría
            break;
        case "rechazar":
            // Coloca aquí la lógica para rechazar la tutoría
            break;
        // Agrega más casos según las acciones necesarias

        default:
            // Maneja el caso por defecto o muestra un mensaje de error
            break;
    }

    // Redirigir nuevamente a la página de gestión de tutorías después de realizar la acción correspondiente
    header("Location: gestionar_tutoria.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Tutorías - Dashboard</title>
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
            color: #fff; /* Cambiado a blanco */
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
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
            color: #fff; /* Cambiado a blanco */
        }

        th {
            background-color: #4caf50; /* Color de fondo del encabezado de la tabla */
            color: white;
        }

        .evidence-button {
            background-color: #4caf50; /* Color de fondo del botón de evidencia */
            color: #fff;
            cursor: pointer;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
        }

        .evidence-button:hover {
            background-color: #45a049; /* Cambio de color al pasar el ratón */
        }

        .evidence-container {
            display: none;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-top: 20px;
        }

        .evidence-container h2 {
            color: #000;
        }

        .evidence-info {
            margin-top: 10px;
            color: #000;
        }

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
            color: #000;
        }

        .popup button {
            background-color: #4caf50;
            color: #fff;
            cursor: pointer;
            padding: 10px;
            border: none;
            border-radius: 4px;
            margin-right: 10px;
        }

        .popup button:hover {
            background-color: #45a049;
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
                <li><a href="gestionar_tutoria.php">Gestionar Tutorías</a></li>
                <!-- Otros enlaces del menú -->
                <li><button onclick="showPopup()">Salir</button></li>
            </ul>
        </div>
        <div class="main-content">
            <h1>Gestionar Tutorías</h1>

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
                        <th>Acciones</th>
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
                        echo "<td>
                                <button class='evidence-button' onclick='showEvidence(" . $fila['id_tutoria'] . ")'>Ver Evidencia</button>
                                <form method='post' action='gestionar_tutoria.php'>
                                    <input type='hidden' name='tutoria_id' value='" . $fila['id_tutoria'] . "'>
                                    <select name='accion'>
                                        <option value='aprobar'>Aprobar</option>
                                        <option value='rechazar'>Rechazar</option>
                                    </select>
                                    <button type='submit'>Aplicar</button>
                                </form>
                            </td>";
                        echo "</tr>";
                    }

                    $conexion->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Contenedor de evidencia (inicialmente oculto) -->
    <div class="evidence-container" id="evidence-container" style="display: none;">
        <!-- Aquí mostrarás la evidencia y descripción de la tutoría -->
        <!-- Puedes utilizar JavaScript para cambiar el contenido de este contenedor al hacer clic en el botón de evidencia -->
    </div>

    <!-- Popup de confirmación para salir -->
    <div class="popup" id="popup">
        <p>     Confirmar Salida</p>
        <button onclick="confirmLogout()">Confirmar</button>
        <button onclick="hidePopup()">Rechazar</button>
    </div>

    <script>
        function showEvidence(tutoriaId) {
            // Puedes implementar aquí la lógica para mostrar la evidencia y la descripción asociada a la tutoría con el ID tutoriaId
            // Puedes utilizar AJAX para cargar dinámicamente la información desde el servidor
            // Muestra el contenedor de evidencia (cambia display a "block" o utiliza otra técnica de visibilidad)
            document.getElementById("evidence-container").style.display = "block";
        }

        function hidePopup() {
            document.getElementById("popup").style.display = "none";
        }

        function showPopup() {
            document.getElementById("popup").style.display = "block";
        }

        function confirmLogout() {
            // Redirigir al usuario al login
            window.location.href = 'login.php';
        }
    </script>
</body>
</html>
