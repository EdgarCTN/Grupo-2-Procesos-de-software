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

// Función para generar un color pastel aleatorio
function randomPastelColor() {
    $letters = str_split('BCDEF');
    $color = '#';
    for ($i = 0; $i < 3; $i++) {
        $color .= $letters[rand(0, count($letters) - 1)];
    }
    return $color;
}

$id_usuario = obtenerIdUsuario($conn, $nombre_usuario);

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar que todos los campos del formulario están presentes
    if (isset($_POST['nombre_objetivo'], $_POST['fecha'], $_POST['hora'], $_POST['duracion'])) {
        // Obtener los valores del formulario
        $nombre_objetivo = $_POST['nombre_objetivo'];
        $fecha = $_POST['fecha'];
        $hora = $_POST['hora'];
        $duracion = $_POST['duracion'];
        
        // Obtener el id del usuario a partir del nombre en $_SESSION['nombre']
        $id_usuario = obtenerIdUsuario($conn, $_SESSION['nombre']);
        
        if ($id_usuario !== false) {
            // Insertar los datos en la tabla de objetivos
            $stmt = $conn->prepare("INSERT INTO objetivo (id_usuario, nombre_objetivo, fecha, hora, duracion) VALUES (:id_usuario, :nombre_objetivo, :fecha, :hora, :duracion)");
            $stmt->bindParam(':id_usuario', $id_usuario);
            $stmt->bindParam(':nombre_objetivo', $nombre_objetivo);
            $stmt->bindParam(':fecha', $fecha);
            $stmt->bindParam(':hora', $hora);
            $stmt->bindParam(':duracion', $duracion);
            
            if ($stmt->execute()) {
                // Objeto guardado exitosamente
                echo "<script>alert('Objetivo creado exitosamente.');</script>";
            } else {
                // Error al crear el objetivo
                echo "<script>alert('Error al crear el objetivo.');</script>";
            }
        } else {
            // Error al obtener el id del usuario
            echo "<script>alert('Error: No se pudo obtener el id del usuario.');</script>";
        }
    } else {
        // Faltan campos del formulario
        echo "<script>alert('Error: No se pudo obtener el id del usuario.');</script>";
    }
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
        .popup-buttons button.cancel {
            background-color: #e74c3c;
            border: none;
        }
        .popup-buttons button.cancel:hover {
            background-color: #c0392b;
        }

        .objectives-title {
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #455a64;
        }

        .create-objective-button {
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

        .create-objective-button:hover {
            background-color: #607d8b;
        }

        .objectives-section {
            margin-top: 20px;
            overflow-y: auto;
            max-height: 400px; /* Altura máxima de la sección de objetivos */
            width: 100%; /* Ajustar al ancho del contenedor principal */
        }

        .objective-card {
            background-color: #f0ebeb; /* Color de fondo */
            border-radius: 10px;
            padding: 10px;
            margin-bottom: 10px;
        }

        .objective-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }

    /* Estilo para el botón "Finalizar objetivo" */
    .finish-objective-button {
        background-color: #455a64;
        color: #fff;
        font-weight: bold;
        padding: 10px 15px;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        transition: background-color 0.3s;
        float: right; /* Alineación a la derecha */
        margin-top: -40px; /* Ajuste para subir el botón */
        margin-right: 40px; /* Espaciado derecho */
    }

    .finish-objective-button:hover {
        background-color: #607d8b;
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
                <li><a href="cursos.php">Cursos</a></li>
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
            <div class="objectives-title">Objetivos</div>
            <a href="#" class="create-objective-button" onclick="showObjectivePopup()">Crear objetivo</a>
            <div class="objectives-section">
                <?php
                // Consulta SQL para obtener los objetivos del usuario
                $stmt = $conn->prepare("SELECT nombre_objetivo FROM objetivo WHERE id_usuario = :id_usuario");
                $stmt->bindParam(':id_usuario', $id_usuario);
                $stmt->execute();
                $objetivos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Iterar sobre los objetivos y mostrar cada uno en una tarjeta
                foreach ($objetivos as $objetivo) {
                    echo "<div class='objective-card' style='background-color: " . randomPastelColor() . ";'>";
                    echo "<div class='objective-title'>" . $objetivo['nombre_objetivo'] . "</div>";
                    // Consultar los detalles adicionales del objetivo
                    $stmt = $conn->prepare("SELECT fecha, hora, duracion FROM objetivo WHERE nombre_objetivo = :nombre_objetivo");
                    $stmt->bindParam(':nombre_objetivo', $objetivo['nombre_objetivo']);
                    $stmt->execute();
                    $detalles = $stmt->fetch(PDO::FETCH_ASSOC);
                    // Mostrar los detalles debajo del título
                    echo "<div class='objective-details'>";
                    echo "<b>Fecha:</b>&nbsp;&nbsp;&nbsp;";
                    echo $detalles['fecha'] . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                    echo "<b>Hora:</b>&nbsp;&nbsp";
                    echo $detalles['hora'] . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                    echo "<b>Duración:</b>&nbsp;&nbsp;&nbsp;";
                    echo $detalles['duracion'] . " horas<br>";
                    echo "</div>";
                    // Botón "Finalizar objetivo"
                    echo "<button class='finish-objective-button' onclick='showFinishObjectivePopup(\"{$objetivo['nombre_objetivo']}\")'>Finalizar objetivo</button>";
                    echo "</div>"; // Cierre del div de la tarjeta de objetivo
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Pop-up para crear objetivo -->
    <div class="popup" id="objective-popup">
        <div class="popup-content">
            <label for="objective-name">Nombre del objetivo:</label><br>
            <input type="text" id="objective-name" name="objective-name"><br>
            <label for="objective-date">Fecha:</label><br>
            <input type="date" id="objective-date" name="objective-date"><br>
            <label for="objective-time">Hora:</label><br>
            <input type="time" id="objective-time" name="objective-time"><br>
            <label for="objective-duration">Duración (en horas):</label><br>
            <input type="number" id="objective-duration" name="objective-duration"><br><br>
            <div class="popup-buttons">
                <button class="cancel" onclick="hideObjectivePopup()">Cancelar</button>
                <button onclick="saveObjective()">Guardar</button>
            </div>
        </div>
    </div>
    
    <div class="popup" id="success-popup">
        <div class="popup-content">
            <p>Objetivo creado exitosamente.</p>
            <div class="popup-buttons">
                <button onclick="hideSuccessPopup()">Cerrar</button>
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

    <div class="popup" id="finish-objective-popup">
        <div class="popup-content">
            <p>¿Finalizar objetivo?</p>
            <div class="popup-buttons">
                <button onclick="hideFinishObjectivePopup()">Cancelar</button>
                <button onclick="finishObjective()">Finalizar</button>
            </div>
        </div>
    </div>

<script>

    // Función para ocultar el pop-up de éxito
    function hideSuccessPopup() {
        document.getElementById('success-popup').style.display = 'none';
    }

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

    function showObjectivePopup() {
        // Restablecer el contenido del pop-up
        document.getElementById('objective-name').value = '';
        document.getElementById('objective-date').value = '';
        document.getElementById('objective-time').value = '';
        document.getElementById('objective-duration').value = '';

        // Mostrar el pop-up
        document.getElementById('objective-popup').style.display = 'block';
    }

    function hideObjectivePopup() {
        document.getElementById('objective-popup').style.display = 'none';
    }

    function saveObjective() {
        // Obtener los valores del formulario
        var nombreObjetivo = document.getElementById('objective-name').value;
        var fecha = document.getElementById('objective-date').value;
        var hora = document.getElementById('objective-time').value;
        var duracion = document.getElementById('objective-duration').value;

        // Verificar que todos los campos estén llenos
        if (nombreObjetivo !== '' && fecha !== '' && hora !== '' && duracion !== '') {
            // Crear una solicitud AJAX
            var xhr = new XMLHttpRequest();
            xhr.open('POST', window.location.href, true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Respuesta del servidor
                    var response = xhr.responseText;
                    if (response.includes('Objetivo creado exitosamente')) {
                        // Ocultar el pop-up de registro de objetivo
                        document.getElementById('objective-popup').style.display = 'none';
                        // Mostrar el pop-up de éxito
                        document.getElementById('success-popup').style.display = 'block';
                        // Recargar la página para mostrar el nuevo objetivo
                        window.location.reload();
                    }
                }
            };
            // Enviar los datos del formulario al servidor
            xhr.send('nombre_objetivo=' + encodeURIComponent(nombreObjetivo) + '&fecha=' + encodeURIComponent(fecha) + '&hora=' + encodeURIComponent(hora) + '&duracion=' + encodeURIComponent(duracion));
        } else {
            // Mostrar mensaje de error si algún campo está vacío
            alert('Por favor, complete todos los campos del formulario.');
        }
    }

    function showFinishObjectivePopup(nombreObjetivo) {
        // Guardar el nombre del objetivo en una variable global
        window.nombreObjetivo = nombreObjetivo;
        document.getElementById('finish-objective-popup').style.display = 'block';
    }

    function hideFinishObjectivePopup() {
        document.getElementById('finish-objective-popup').style.display = 'none';
    }

    function finishObjective() {
        var nombreObjetivo = window.nombreObjetivo;
        // Crear una solicitud AJAX para eliminar el objetivo de la base de datos
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'eliminar_objetivo.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                // Recargar la página para actualizar la lista de objetivos
                window.location.reload();
            }
        };
        xhr.send('nombre_objetivo=' + encodeURIComponent(nombreObjetivo));
    }  
</script>
</body>
</html>
