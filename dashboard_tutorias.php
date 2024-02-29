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
/*
try {
    // Preparar la consulta SQL para obtener los datos de la primera tutoría del tutor
    $stmt_tutoria = $conn->prepare("SELECT t.fecha, t.hora, t.tema, c.nombre_curso, a.apellidos, a.nombre
                                    FROM tutoría AS t
                                    INNER JOIN curso AS c ON t.codcurso = c.cod_curso
                                    INNER JOIN tutor AS tu ON t.codtutor = tu.cod_tutor
                                    INNER JOIN alumno AS a ON t.codalumno = a.cod_alumno
                                    INNER JOIN usuarios AS u ON tu.id_usuario = u.id
                                    WHERE u.nombre_usuario = :nombre_usuario");
    $stmt_tutoria->bindParam(':nombre_usuario', $nombre_usuario);
    $stmt_tutoria->execute();

    // Verificar si se encontró una tutoría para el tutor
    if ($stmt_tutoria->rowCount() > 0) {
        $resultado_tutoria = $stmt_tutoria->fetch(PDO::FETCH_ASSOC);
        $fecha_tutoria = "<span class='texto'><strong>Fecha:</strong></span> " . $resultado_tutoria['fecha'];
        $hora_tutoria = "<span class='texto'><strong>Hora:</strong></span> " . $resultado_tutoria['hora'];
        $tema_tutoria = "<span class='texto'><strong>Tema:</strong></span> " . $resultado_tutoria['tema'];
        $nombre_curso = $resultado_tutoria['nombre_curso'];
        $apellidos_tutor = "<span class='texto'><strong>Alumno:</strong></span> " . $resultado_tutoria['apellidos'];
        $nombre_tutor = "<span class='texto'><strong>Alumno:</strong></span> " . $resultado_tutoria['nombre'];
    } else {
        // Si no se encontró una tutoría, mostrar un mensaje o asignar valores por defecto
        $fecha_tutoria = "No hay tutorías programadas";
        $hora_tutoria = "";
        $tema_tutoria = "";
        $nombre_curso = "";
        $apellidos_tutor = "";
        $nombre_tutor = "";
    }

    // Preparar la consulta SQL para obtener la lista de tutorías del mismo tutor
    $stmt_lista_tutorias = $conn->prepare("SELECT t.fecha, t.hora, t.tema, c.nombre_curso, a.apellidos, a.nombre
                                           FROM tutoría AS t
                                           INNER JOIN curso AS c ON t.codcurso = c.cod_curso
                                           INNER JOIN tutor AS tu ON t.codtutor = tu.cod_tutor
                                           INNER JOIN alumno AS a ON t.codalumno = a.cod_alumno
                                           INNER JOIN usuarios AS u ON tu.id_usuario = u.id
                                           WHERE u.nombre_usuario = :nombre_usuario");
    $stmt_lista_tutorias->bindParam(':nombre_usuario', $nombre_usuario);
    $stmt_lista_tutorias->execute();

    // Guardar las tutorías del tutor en un array
    $tutorias = $stmt_lista_tutorias->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // Manejar errores de base de datos
    $fecha_tutoria = "Error al obtener las tutorías";
    $hora_tutoria = "";
    $tema_tutoria = "";
    $nombre_curso = "";
    $apellidos_tutor = "";
    $nombre_tutor = "";
}*/
/*
try {
    // Preparar la consulta SQL para obtener los datos de la primera tutoría del tutor
    $stmt_tutoria = $conn->prepare("SELECT t.fecha, t.hora, t.tema, c.nombre_curso, a.apellidos, a.nombre
                                    FROM tutoría AS t
                                    INNER JOIN curso AS c ON t.codcurso = c.cod_curso
                                    INNER JOIN tutor AS tu ON t.codtutor = tu.cod_tutor
                                    INNER JOIN alumno AS a ON t.codalumno = a.cod_alumno
                                    INNER JOIN usuarios AS u ON tu.id_usuario = u.id
                                    WHERE u.nombre_usuario = :nombre_usuario");
    $stmt_tutoria->bindParam(':nombre_usuario', $nombre_usuario);
    $stmt_tutoria->execute();

    // Verificar si se encontró una tutoría para el tutor
    if ($stmt_tutoria->rowCount() > 0) {
        $resultado_tutoria = $stmt_tutoria->fetch(PDO::FETCH_ASSOC);
        $fecha_tutoria = "<span class='texto'><strong>Fecha:</strong></span> " . $resultado_tutoria['fecha'];
        $hora_tutoria = "<span class='texto'><strong>Hora:</strong></span> " . $resultado_tutoria['hora'];
        $tema_tutoria = "<span class='texto'><strong>Tema:</strong></span> " . $resultado_tutoria['tema'];
        $nombre_curso = $resultado_tutoria['nombre_curso'];
        $apellidos_tutor = "<span class='texto'><strong>Alumno:</strong></span> " . $resultado_tutoria['apellidos'];
        $nombre_tutor = "<span class='texto'><strong>Alumno:</strong></span> " . $resultado_tutoria['nombre'];
    } else {
        // Si no se encontró una tutoría, mostrar un mensaje o asignar valores por defecto
        $fecha_tutoria = "No hay tutorías programadas";
        $hora_tutoria = "";
        $tema_tutoria = "";
        $nombre_curso = "";
        $apellidos_tutor = "";
        $nombre_tutor = "";
    }

    // Preparar la consulta SQL para obtener la lista de tutorías del mismo tutor
    $stmt_lista_tutorias = $conn->prepare("SELECT t.fecha, t.hora, t.tema, c.nombre_curso, a.apellidos, a.nombre
                                           FROM tutoría AS t
                                           INNER JOIN curso AS c ON t.codcurso = c.cod_curso
                                           INNER JOIN tutor AS tu ON t.codtutor = tu.cod_tutor
                                           INNER JOIN alumno AS a ON t.codalumno = a.cod_alumno
                                           INNER JOIN usuarios AS u ON tu.id_usuario = u.id
                                           WHERE u.nombre_usuario = :nombre_usuario");
    $stmt_lista_tutorias->bindParam(':nombre_usuario', $nombre_usuario);
    $stmt_lista_tutorias->execute();

    // Guardar las tutorías del tutor en un array
    $tutorias = $stmt_lista_tutorias->fetchAll(PDO::FETCH_ASSOC);

    // Modificar la consulta SQL para obtener la lista de tutorías del mismo alumno
    $stmt_lista_tutorias_alumno = $conn->prepare("SELECT t.fecha, t.hora, t.tema, c.nombre_curso, tu.apellidos, tu.nombre
                                                  FROM tutoría AS t
                                                  INNER JOIN curso AS c ON t.codcurso = c.cod_curso
                                                  INNER JOIN tutor AS tu ON t.codtutor = tu.cod_tutor
                                                  INNER JOIN alumno AS a ON t.codalumno = a.cod_alumno
                                                  INNER JOIN usuarios AS u ON a.id_usuario = u.id
                                                  WHERE u.nombre_usuario = :nombre_usuario");
    $stmt_lista_tutorias_alumno->bindParam(':nombre_usuario', $nombre_usuario);
    $stmt_lista_tutorias_alumno->execute();

    // Guardar las tutorías del alumno en un array
    $tutorias_alumno = $stmt_lista_tutorias_alumno->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // Manejar errores de base de datos
    $fecha_tutoria = "Error al obtener las tutorías";
    $hora_tutoria = "";
    $tema_tutoria = "";
    $nombre_curso = "";
    $apellidos_tutor = "";
    $nombre_tutor = "";
}*/
try {
    // ... (código anterior para obtener $cod_alumno)

    // Preparar la consulta SQL para obtener la primera tutoría del alumno
    $stmt_tutoria = $conn->prepare("SELECT t.fecha, t.hora, t.tema, c.nombre_curso, tu.apellidos, tu.nombre
                                    FROM tutoría AS t
                                    INNER JOIN curso AS c ON t.codcurso = c.cod_curso
                                    INNER JOIN tutor AS tu ON t.codtutor = tu.cod_tutor
                                    INNER JOIN alumno AS a ON t.codalumno = a.cod_alumno
                                    WHERE a.cod_alumno = :cod_alumno");
    $stmt_tutoria->bindParam(':cod_alumno', $cod_alumno);
    $stmt_tutoria->execute();

    // Verificar si se encontró una tutoría para el alumno
    if ($stmt_tutoria->rowCount() > 0) {
        $resultado_tutoria = $stmt_tutoria->fetch(PDO::FETCH_ASSOC);
        // Resto del código para mostrar la información de la tutoría
    } else {
        // Si no se encontró una tutoría, mostrar un mensaje o asignar valores por defecto
    }

    // Preparar la consulta SQL para obtener la lista de todas las tutorías del mismo alumno
    $stmt_lista_tutorias_alumno = $conn->prepare("SELECT t.fecha, t.hora, t.tema, c.nombre_curso, tu.apellidos, tu.nombre
                                                  FROM tutoría AS t
                                                  INNER JOIN curso AS c ON t.codcurso = c.cod_curso
                                                  INNER JOIN tutor AS tu ON t.codtutor = tu.cod_tutor
                                                  INNER JOIN alumno AS a ON t.codalumno = a.cod_alumno
                                                  WHERE a.cod_alumno = :cod_alumno");
    $stmt_lista_tutorias_alumno->bindParam(':cod_alumno', $cod_alumno);
    $stmt_lista_tutorias_alumno->execute();

    // Guardar las tutorías del alumno en un array
    $tutorias_alumno = $stmt_lista_tutorias_alumno->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // Manejar errores de base de datos
    // Resto del código para manejar el error
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
        .main-content {
            flex: 1;
            padding: 40px;
            width: 100%;          
        }
        .main-content h1 {
            color: #000; /* Color blanco para el encabezado h1 */
            width: 100%;
            text-align: center;
        }

        .titulo_agregar_tutoria{
            text-align: center;
            color: #000;
            background-color: rgba(0, 0, 0, 0.5);
            border-radius: 30px;            
        }
        form {
            max-width: 500px;
            margin: auto;
            padding: 30px;
            background-color: #fff; /* Color de fondo del formulario */
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Sombra del formulario */
        }

        form label {
            display: block;
            margin-bottom: 16px;
        }

        form input,
        form select,
        form button {
            font-size: 16px;
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
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

        .contenedor-general{
            width: 100%;
            background-color: #f5f5f5;
            padding: 20px;
        } 
        .tutorias-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .table-header, .table-cell {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .table-header {
            background-color: #f2f2f2;
            font-weight: bold;
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
                echo "<div class='welcome-message'>Bienvenido, " . $_SESSION['nombre']. "</div>";
                           
            } else {
                echo "<p>Por favor, inicia sesión para ver tus datos.</p>";
            }
            ?>
            <h1>Tutorías del Alumno</h1>                                  
            <div class="contenedor-general">               
            <?php
                // Ordenar las tutorías por fecha
                usort($tutorias_alumno, function ($a, $b) {
                    return strtotime($a['fecha'] . ' ' . $a['hora']) - strtotime($b['fecha'] . ' ' . $b['hora']);
                });

                // Mostrar la lista de tutorías del alumno en una tabla con estilos
                echo "<h2>Todas las Tutorías</h2>";
                echo "<table class='tutorias-table'>";
                echo "<tr>";
                echo "<th class='table-header'>Fecha</th>";
                echo "<th class='table-header'>Hora</th>";
                echo "<th class='table-header'>Tema</th>";
                echo "<th class='table-header'>Curso</th>";
                echo "<th class='table-header'>Tutor</th>";
                echo "</tr>";

                foreach ($tutorias_alumno as $tutoria_alumno) {
                    echo "<tr>";
                    echo "<td class='table-cell'>{$tutoria_alumno['fecha']}</td>";
                    echo "<td class='table-cell'>{$tutoria_alumno['hora']}</td>";
                    echo "<td class='table-cell'>{$tutoria_alumno['tema']}</td>";
                    echo "<td class='table-cell'>{$tutoria_alumno['nombre_curso']}</td>";
                    echo "<td class='table-cell'>{$tutoria_alumno['nombre']} {$tutoria_alumno['apellidos']}</td>";
                    echo "</tr>";
                }
                echo "</table>";
            ?>                                         
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

        document.addEventListener('DOMContentLoaded', function () {
            var fechaInput = document.querySelector('input[name="fecha"]');
            fechaInput.addEventListener('input', function () {
                var fechaSeleccionada = new Date(this.value);
                var hoy = new Date();
                var limiteFecha = new Date();
                limiteFecha.setDate(limiteFecha.getDate() + 2);

                if (fechaSeleccionada < hoy) {
                    alert("No se puede programar una tutoría para el mismo día o una fecha pasada. Por favor, elige una fecha futura.");
                    this.value = '';
                } else if (fechaSeleccionada < limiteFecha) {
                    alert("La tutoría debe programarse con al menos dos días de anticipación.");
                    this.value = '';
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            var horaInput = document.querySelector('input[name="hora"]');
            horaInput.addEventListener('input', function () {
                var horaSeleccionada = this.value;

                // Obtener las horas y minutos
                var [horas, minutos] = horaSeleccionada.split(':');
                horas = parseInt(horas, 10);

                // Validar el rango de horas permitido (de 8 am a 10 pm)
                if (horas < 8 || horas > 22) {
                    alert("La tutoría solo puede programarse entre las 8 am y las 10 pm. Por favor, selecciona una hora válida.");
                    this.value = '';
                }
            });
        });
    </script>
</body>
</html>
