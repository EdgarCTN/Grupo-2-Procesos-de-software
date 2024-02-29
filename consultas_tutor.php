<?php
date_default_timezone_set('America/Lima');

session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Archivo de conexión a la base de datos
include 'conn/connection.php';

// Obtener el nombre de usuario de la sesión
$nombre_usuario = $_SESSION['nombre'];

// Función para obtener el id del usuario a partir del nombre
function obtenerIdUsuario($conexion, $nombreUsuario) {
    $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE nombre_usuario = :nombre");
    $stmt->bindParam(':nombre', $nombreUsuario);
    $stmt->execute();
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($resultado && isset($resultado['id'])) {
        return $resultado['id'];
    } else {
        return false;
    }
}

// Función para obtener el código del tutor a partir del ID del usuario
function obtenerCodTutor($conexion, $idUsuario) {
    $stmt = $conexion->prepare("SELECT cod_tutor FROM tutor WHERE id_usuario = :idUsuario");
    $stmt->bindParam(':idUsuario', $idUsuario);
    $stmt->execute();
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($resultado && isset($resultado['cod_tutor'])) {
        return $resultado['cod_tutor'];
    } else {
        return false;
    }
}

// Obtener el ID del usuario a partir del nombre de usuario
$id_usuario = obtenerIdUsuario($conn, $nombre_usuario);

// Obtener el código del tutor del usuario
$cod_tutor = obtenerCodTutor($conn, $id_usuario);


// Verificar si se ha enviado el formulario de respuesta
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener la respuesta y el ID de la consulta del formulario
    $respuesta = htmlspecialchars($_POST['respuesta']);
    $consultaId = htmlspecialchars($_POST['consulta_id']);

    // Preparar la consulta SQL para insertar la respuesta en la tabla de consultas
    $stmt = $conn->prepare("UPDATE consulta SET respuesta = :respuesta WHERE id_consulta = :consultaId");
    $stmt->bindParam(':respuesta', $respuesta);
    $stmt->bindParam(':consultaId', $consultaId);
  
    // Ejecutar la consulta
    if ($stmt->execute()) {
        // La consulta se ejecutó con éxito
        echo "Respuesta enviada correctamente.";
    } else {
        // Error al ejecutar la consulta
        echo "Error al enviar la respuesta.";
    }
    exit; // Terminar la ejecución del script después de procesar la respuesta
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@700&display=swap">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultas - Dashboard del Profesor</title>
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
        .consultas-title {
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #455a64;
        }
        .consultas-section {
            margin-top: 20px;
            overflow-y: auto;
            max-height: 400px; /* Altura máxima de la sección de consultas */
            width: 100%; /* Ajustar al ancho del contenedor principal */
        }
        .consulta-card {
            background-color: #f0ebeb; /* Color de fondo */
            border-radius: 10px;
            padding: 10px;
            margin-bottom: 10px;
            position: relative;
        }
        .consultas-buttons {
            position: absolute;
            top: 5px;
            right: 5px;
            display: flex;
        }
        .view-image-button,
        .view-response-button {
            background-color: #3498db;
            color: #fff;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 5px; /* Espacio entre botones */
        }
        .view-image-button:hover,
        .view-response-button:hover {
            background-color: #2980b9; /* Color de imagen y respuesta al pasar el ratón */
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
        .popup-buttons button.cancel {
            background-color: #e74c3c;
            border: none;
        }
        .popup-buttons button.cancel:hover {
            background-color: #c0392b;
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
                <li><a href="consultas_tutor.php">Consultas</a></li>
                <li><button onclick="showPopup()">Salir</button></li>
            </ul>
        </div>
        <div class="main-content">
            <?php
            if (isset($_SESSION['nombre'])) {
                echo "<div class='welcome-message'>Bienvenido, " . $_SESSION['nombre'] . "</div>";
            } else {
                echo "<p>Por favor, inicia sesión para ver tus datos.</p>";
            }
            ?>
            <div class="consultas-title">Consultas de Alumnos</div>
            <div class="consultas-section">
                <?php
                // Consulta SQL para obtener las consultas asociadas al tutor actual
                        $stmt = $conn->prepare("SELECT c.id_consulta, c.asunto, c.contenido, c.fecha_creacion, c.imagen_path, u.nombre  
                        FROM consulta c 
                        LEFT JOIN usuarios u ON c.id_alumno = u.id 
                        WHERE c.cod_tutor = :cod_tutor
                        ORDER BY c.fecha_creacion DESC");
                        $stmt->bindParam(':cod_tutor', $cod_tutor);
                        $stmt->execute();
                        $consultas = $stmt->fetchAll(PDO::FETCH_ASSOC);


                 // Iterar sobre las consultas y mostrar cada una en una tarjeta
                 foreach ($consultas as $consulta) {
                    echo "<div class='consulta-card'>";
                    echo "<div class='consultas-buttons'>"; // Div para los botones
                    echo "<button class='view-image-button' onclick='viewImage(\"" . $consulta['imagen_path'] . "\")'>Ver Imagen</button>";
                    echo "<button class='view-response-button' onclick='showResponseForm(\"" . $consulta['id_consulta'] . "\")'>Responder</button>";
                    echo "</div>"; // Cierre del div para los botones
                    echo "<div class='consulta-content'><strong>Asunto:</strong> " . $consulta['asunto'] . "</div>";
                    echo "<div class='consulta-content'><strong>Contenido:</strong> " . $consulta['contenido'] . "</div>";
                    echo "<div class='consulta-date'><strong>Fecha de creación:</strong> " . $consulta['fecha_creacion'] . "</div>";
                    echo "<div class='consulta-alumno'><strong>Alumno:</strong> " . $consulta['nombre'] . "</div>";
                    echo "<form id='form-" . $consulta['id_consulta'] . "' class='response-form' style='display: none;' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "' method='POST' onsubmit='submitResponse(event, \"" . $consulta['id_consulta'] . "\")'>";
                    echo "<textarea id='respuesta-" . $consulta['id_consulta'] . "' name='respuesta' rows='4' cols='50' placeholder='Escribe tu respuesta aquí'></textarea><br>";
                    echo "<input type='hidden' name='consulta_id' value='" . $consulta['id_consulta'] . "'>"; // Agregar un campo oculto para enviar el ID de la consulta
                    echo "<input type='submit' value='Enviar respuesta'>";
                    echo "</form>";
                    

                    echo "</div>"; // Cierre del div de la tarjeta de consulta
                }
                
                ?>
            </div>
        </div>
    </div>

    <div class="popup" id="response-popup">
        <div class="popup-content">
            <textarea id="response-text" name="respuesta" rows="4" cols="50" placeholder="Escribe tu respuesta aquí"></textarea><br>
            <input type="hidden" id="consulta_id" name="consulta_id" value="">
            <button onclick="submitResponse()">Enviar respuesta</button>
            <button onclick="closeResponsePopup()">Cancelar</button>
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

    <div class="popup" id="image-popup">
        <div class="popup-content">
            <img id="popup-image" src="" alt="Imagen Consulta">
            <button onclick="closeImagePopup()">Cerrar</button>
        </div>
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

        function viewImage(imagePath) {
            var popupImage = document.getElementById('popup-image');
            popupImage.src = imagePath;
            document.getElementById('image-popup').style.display = 'block';
        }

        function closeImagePopup() {
            document.getElementById('image-popup').style.display = 'none';
        }

        function showResponseForm(consultaId) {
            document.getElementById('consulta_id').value = consultaId;
            document.getElementById('response-popup').style.display = 'block';
        }

        function submitResponse() {
            var respuesta = document.getElementById('response-text').value;
            var consultaId = document.getElementById('consulta_id').value;
            var formData = new FormData();
            formData.append('respuesta', respuesta);
            formData.append('consulta_id', consultaId);

            fetch('<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                alert(data);
                closeResponsePopup();
            })
            .catch(error => {
                console.error('Error:', error);
                closeResponsePopup();
            });
        }

        function closeResponsePopup() {
            document.getElementById('response-popup').style.display = 'none';
        }
    </script>
</body>
</html>
