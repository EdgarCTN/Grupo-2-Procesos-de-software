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

$id_usuario = obtenerIdUsuario($conn, $nombre_usuario);

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar que todos los campos del formulario están presentes
    if (isset($_POST['asunto'], $_POST['contenido'], $_POST['tutor']) && isset($_FILES['imagen'])) {
        // Obtener los valores del formulario
        $asunto = $_POST['asunto'];
        $contenido = $_POST['contenido'];
        $tutor = $_POST['tutor'];
        $fecha_actual = date("Y-m-d H:i:s"); // Fecha y hora actual
        
        // Obtener el id del usuario a partir del nombre en $_SESSION['nombre']
        $id_usuario = obtenerIdUsuario($conn, $_SESSION['nombre']);
        
        if ($id_usuario !== false) {
            // Procesar la imagen subida
            $imagen = $_FILES['imagen'];
            $imagen_nombre = $imagen['name'];
            $imagen_temporal = $imagen['tmp_name'];
            $imagen_ruta = 'uploads/' . $imagen_nombre; // Ruta donde guardarás la imagen en tu servidor

            // Mueve la imagen del directorio temporal al directorio de destino
            if (move_uploaded_file($imagen_temporal, $imagen_ruta)) {
                // Insertar los datos en la tabla de consultas
                $stmt = $conn->prepare("INSERT INTO consulta (id_alumno, cod_tutor, asunto, contenido, fecha_creacion, imagen_path) VALUES (:id_alumno, :cod_tutor, :asunto, :contenido, :fecha_creacion, :imagen_path)");
                $stmt->bindParam(':id_alumno', $id_usuario);
                $stmt->bindParam(':cod_tutor', $tutor); // Agregar el cod_tutor seleccionado
                $stmt->bindParam(':asunto', $asunto);
                $stmt->bindParam(':contenido', $contenido);
                $stmt->bindParam(':fecha_creacion', $fecha_actual);
                $stmt->bindParam(':imagen_path', $imagen_ruta); // Guardar la ruta de la imagen

                if ($stmt->execute()) {
                    // Consulta guardada exitosamente
                    echo "<script>alert('Consulta creada exitosamente.');</script>";
                } else {
                    // Error al crear la consulta
                    echo "<script>alert('Error al crear la consulta.');</script>";
                }

            } else {
                // Hubo un error al subir la imagen
                echo "<script>alert('Error al subir la imagen.');</script>";
            }
        } else {
            // Error al obtener el id del usuario
            echo "<script>alert('Error: No se pudo obtener el id del usuario.');</script>";
        }
    } else {
        // Faltan campos del formulario
        echo "<script>alert('Error: Por favor, complete todos los campos del formulario.');</script>";
    }
}

// Verificar si se ha enviado la solicitud para eliminar una consulta
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_consulta_id'])) {
    // Obtener el ID de la consulta a eliminar
    $consulta_id = $_POST['delete_consulta_id'];

    // Consulta SQL para eliminar la consulta
    $stmt = $conn->prepare("DELETE FROM consulta WHERE id_consulta = :consulta_id");
    $stmt->bindParam(':consulta_id', $consulta_id);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        echo "<script>alert('Consulta eliminada correctamente.');</script>";
    } else {
        echo "<script>alert('Error al eliminar la consulta.');</script>";
    }
}

// Consulta SQL para obtener los tutores disponibles
$stmt_tutores = $conn->prepare("SELECT cod_tutor, nombre, apellidos FROM tutor");
$stmt_tutores->execute();
$tutores = $stmt_tutores->fetchAll(PDO::FETCH_ASSOC);
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@700&display=swap">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultas - Dashboard</title>
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
        .popup-buttons button.cancel {
            background-color: #e74c3c;
            border: none;
        }
        .popup-buttons button.cancel:hover {
            background-color: #c0392b;
        }

        .consultas-title {
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #455a64;
        }

        .create-consulta-button {
            color: #fff;
            text-decoration: none;
            font-size: 20px;
            font-weight: bold;
            display: block;
            padding: 15px 30px; /* Aumentado el espaciado */
            border-radius: 10px;
            transition: background-color 0.3s;
            background-color: #455a64;
            margin-bottom: 20px;
            width: 100%;
            text-align: center;
        }

        .create-consulta-button:hover {
            background-color: #607d8b;
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

        .delete-consulta-button {
            background-color: #ffbf00; /* Color de ojo */
            color: #fff;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 5px; /* Espacio entre botones */
        }

        .delete-consulta-button:hover {
            background-color: #ffdf00; /* Color de ojo al pasar el ratón */
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
                <li><a href="dashboard_tutorias.php">Tutorías</a></li>
                <li><a href="horario.php">Horario</a></li>
                <li><a href="objetivos.php">Objetivos</a></li>
                <li><a href="consultas.php">Consultas</a></li>
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
            <div class="consultas-title">Consultas</div>
            <a href="#" class="create-consulta-button" onclick="showConsultaPopup()">Crear consulta</a>
            <div class="consultas-section">
                <?php
                 // Consulta SQL para obtener las consultas del usuario
                 $stmt = $conn->prepare("SELECT c.id_consulta, c.asunto, c.contenido, c.fecha_creacion, c.imagen_path, t.nombre, t.apellidos FROM consulta c LEFT JOIN tutor t ON c.cod_tutor = t.cod_tutor WHERE c.id_alumno = :id_alumno ORDER BY c.fecha_creacion DESC");
                 $stmt->bindParam(':id_alumno', $id_usuario);
                 $stmt->execute();
                 $consultas = $stmt->fetchAll(PDO::FETCH_ASSOC);

                 // Iterar sobre las consultas y mostrar cada una en una tarjeta
                 foreach ($consultas as $consulta) {
                    echo "<div class='consulta-card'>";
                    echo "<div class='consultas-buttons'>"; // Div para los botones
                    echo "<button class='delete-consulta-button' onclick='deleteConsulta(" . $consulta['id_consulta'] . ")'>Eliminar</button>";
                    echo "<button class='view-image-button' onclick='viewImage(\"" . $consulta['imagen_path'] . "\")'>Ver Imagen</button>";
                    echo "<button class='view-response-button'>Ver Respuesta</button>";
                    echo "</div>"; // Cierre del div para los botones
                    echo "<div class='consulta-content'><strong>Asunto:</strong> " . $consulta['asunto'] . "</div>";
                    echo "<div class='consulta-content'><strong>Contenido:</strong> " . $consulta['contenido'] . "</div>";
                    echo "<div class='consulta-date'><strong>Fecha de creación:</strong> " . $consulta['fecha_creacion'] . "</div>";
                    if ($consulta['nombre'] && $consulta['apellidos']) {
                        echo "<div class='consulta-tutor'><strong>Tutor:</strong> " . $consulta['nombre'] . " " . $consulta['apellidos'] . "</div>";
                    } else {
                        echo "<div class='consulta-tutor'><strong>Tutor:</strong> Desconocido</div>";
                    }
                    echo "</div>"; // Cierre del div de la tarjeta de consulta
                }
            
                ?>
            </div>
        </div>
    </div>

    <!-- Pop-up para crear consulta -->
    <div class="popup" id="consulta-popup">
        <div class="popup-content">
            <label for="consulta-asunto">Asunto:</label><br>
            <input type="text" id="consulta-asunto" name="consulta-asunto"><br>
            <label for="consulta-contenido">Contenido de la consulta:</label><br>
            <textarea id="consulta-contenido" name="consulta-contenido" rows="4" cols="50"></textarea><br>
            <label for="imagen">Imagen:</label><br>
            <input type="file" id="imagen" name="imagen"><br><br>

            <!-- Sección para seleccionar el tutor -->
            <label for="tutor">Seleccionar tutor:</label><br>
            <select id="tutor" name="tutor">
                <?php foreach ($tutores as $tutor): ?>
                    <option value="<?php echo $tutor['cod_tutor']; ?>"><?php echo $tutor['nombre'] . ' ' . $tutor['apellidos']; ?></option>
                <?php endforeach; ?>
            </select><br>



            <div class="popup-buttons">
                <button class="cancel" onclick="hideConsultaPopup()">Cancelar</button>
                <button onclick="saveConsulta()">Guardar</button>
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

    function showConsultaPopup() {
        // Restablecer el contenido del pop-up
        document.getElementById('consulta-asunto').value = '';
        document.getElementById('consulta-contenido').value = '';

        // Mostrar el pop-up
        document.getElementById('consulta-popup').style.display = 'block';
    }

    function hideConsultaPopup() {
        document.getElementById('consulta-popup').style.display = 'none';
    }

    function saveConsulta() {
        // Obtener los valores del formulario
        var asunto = document.getElementById('consulta-asunto').value;
        var contenido = document.getElementById('consulta-contenido').value;
        var imagen = document.getElementById('imagen').files[0]; // Obtener la imagen seleccionada
        var tutor = document.getElementById('tutor').value; // Obtener el valor del tutor seleccionado

        // Crear un objeto FormData para enviar datos de formulario y archivos
        var formData = new FormData();
        formData.append('asunto', asunto);
        formData.append('contenido', contenido);
        formData.append('imagen', imagen);
        formData.append('tutor', tutor); // Agregar el tutor seleccionado al formulario

        // Crear una solicitud AJAX
        var xhr = new XMLHttpRequest();
        xhr.open('POST', window.location.href, true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                // Respuesta del servidor
                var response = xhr.responseText;
                if (response.includes('Consulta creada exitosamente')) {
                    // Ocultar el pop-up de creación de consulta
                    hideConsultaPopup();
                    // Recargar la página para mostrar la nueva consulta
                    window.location.reload();
                }
            }
        };
        // Enviar los datos del formulario y la imagen al servidor
        xhr.send(formData);
    }

    function deleteConsulta(consultaId) {
    if (confirm('¿Estás seguro de que quieres eliminar esta consulta?')) {
        // Crear una solicitud AJAX
        var xhr = new XMLHttpRequest();
        xhr.open('POST', window.location.href, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                // Respuesta del servidor
                var response = xhr.responseText;
                if (response.includes('Consulta eliminada correctamente')) {
                    // Recargar la página para reflejar los cambios
                    window.location.reload();
                }
            }
        };
        // Enviar el ID de la consulta a eliminar al servidor
        xhr.send('delete_consulta_id=' + consultaId);
    }
}

    function viewImage(imagePath) {
        var popupImage = document.getElementById('popup-image');
        popupImage.src = imagePath;
        document.getElementById('image-popup').style.display = 'block';
    }

    function closeImagePopup() {
        document.getElementById('image-popup').style.display = 'none';
    }
</script>
</body>
</html>
