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

$id_usuario = obtenerIdUsuario($conn, $nombre_usuario);

// Función para obtener las tutorías del usuario logeado
function obtenerTutoriasUsuario($conexion, $idUsuario) {
    $tutorias = [];
    // Obtener el código de alumno del usuario
    $stmt = $conexion->prepare("SELECT cod_alumno FROM alumno WHERE id_usuario = :idUsuario");
    $stmt->bindParam(':idUsuario', $idUsuario);
    $stmt->execute();
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($resultado && isset($resultado['cod_alumno'])) {
        $codAlumno = $resultado['cod_alumno'];
        // Obtener las tutorías del alumno
        $stmt = $conexion->prepare("SELECT * FROM tutoría WHERE codalumno = :codAlumno");
        $stmt->bindParam(':codAlumno', $codAlumno);
        $stmt->execute();
        $tutorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    return $tutorias;
}

// Obtener las tutorías del usuario logeado
$tutoriasUsuario = obtenerTutoriasUsuario($conn, $id_usuario);
if ($tutoriasUsuario === false) {
    echo "No se encontraron tutorías para este usuario.";
} else {
    // Procede a procesar las tutorías
}

// Función para obtener los objetivos del usuario logeado
function obtenerObjetivosUsuario($conexion, $idUsuario) {
    $objetivos = [];
    $stmt = $conexion->prepare("SELECT * FROM objetivo WHERE id_usuario = :idUsuario");
    $stmt->bindParam(':idUsuario', $idUsuario);
    $stmt->execute();
    $objetivos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $objetivos;
}

// Obtener los objetivos del usuario logeado
$objetivosUsuario = obtenerObjetivosUsuario($conn, $id_usuario);

// Función para obtener las tutorías de una hora específica
function obtenerTutoriasHora($tutorias, $fecha, $hora) {
    $tutorias_hora = [];
    foreach ($tutorias as $tutoria) {
        $hora_inicio_tutoria = (int) explode(':', $tutoria['hora'])[0];
        if ($tutoria['fecha'] == $fecha->format('Y-m-d') && $hora >= $hora_inicio_tutoria && $hora < $hora_inicio_tutoria + 2) {
            $tutorias_hora[] = $tutoria;
        }
    }
    return $tutorias_hora;
}

// Función para obtener los objetivos de una hora específica
function obtenerObjetivosHora($objetivos, $fecha, $hora) {
    $objetivos_hora = [];
    foreach ($objetivos as $objetivo) {
        $fecha_objetivo = new DateTime($objetivo['fecha']);
        $hora_objetivo_inicio = (int) explode(':', $objetivo['hora'])[0];
        $hora_objetivo_fin = $hora_objetivo_inicio + $objetivo['duracion'];
        if ($fecha_objetivo->format('Y-m-d') == $fecha->format('Y-m-d') && $hora >= $hora_objetivo_inicio && $hora < $hora_objetivo_fin) {
            $objetivos_hora[] = $objetivo;
            // Si la duración es mayor que 1, marcar también las celdas siguientes
            for ($i = $hora_objetivo_inicio + 1; $i < $hora_objetivo_fin; $i++) {
                $objetivos_hora[] = [
                    'fecha' => $objetivo['fecha'],
                    'hora' => sprintf("%02d", $i) . ":00",
                    'nombre_objetivo' => $objetivo['nombre_objetivo'],
                ];
            }
        }
    }
    return $objetivos_hora;
}



// Función para resaltar las celdas con tutorías u objetivos
function resaltarCelda($tutorias, $objetivos, $fecha, $hora) {
    foreach ($tutorias as $tutoria) {
        $hora_inicio_tutoria = (int) explode(':', $tutoria['hora'])[0];
        if ($tutoria['fecha'] == $fecha->format('Y-m-d') && $hora >= $hora_inicio_tutoria && $hora < $hora_inicio_tutoria + 2) {
            return 'class="celeste-pastel"';
        }
    }
    
    foreach ($objetivos as $objetivo) {
        $fecha_objetivo = new DateTime($objetivo['fecha']);
        $hora_objetivo = (int) explode(':', $objetivo['hora'])[0];
        if ($fecha_objetivo->format('Y-m-d') == $fecha->format('Y-m-d') && $hora == $hora_objetivo) {
            return 'class="naranja-pastel"';
        }
    }
    
    return '';
}

// Obtener la fecha actual
$fecha_actual = new DateTime();

// Definir el número de días en el horario
$num_dias = 7;

// Definir el número de horas en el horario
$num_horas = 24;

// Definir el número de semanas a mostrar
$num_semanas = 1;

// Si hay una fecha específica en la URL, usar esa fecha
if (isset($_GET['fecha'])) {
    $fecha_actual = new DateTime($_GET['fecha']);
}

// Generar fechas para los días de la semana
$fechas_semana = [];
for ($i = 0; $i < $num_dias * $num_semanas; $i++) {
    $fechas_semana[] = clone $fecha_actual;
    $fecha_actual->modify('+1 day');
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
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
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        .controls {
            margin-bottom: 20px;
        }
        .celeste-pastel {
            background-color: #cce5ff;
        }
        .naranja-pastel {
            background-color: #ffdab3;
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
            
            <div class="controls">
                <button onclick="irSemanaAnterior()">Semana Anterior</button>
                <?php echo $fechas_semana[0]->format('Y-m-d'); ?> - <?php echo end($fechas_semana)->format('Y-m-d'); ?>
                <button onclick="irSemanaSiguiente()">Semana Siguiente</button>
            </div>
            <table>
                <tr>
                    <th>Hora</th>
                    <?php foreach ($fechas_semana as $fecha) { ?>
                        <th><?php echo $fecha->format('D d/m'); ?></th>
                    <?php } ?>
                </tr>
                <?php
                // Generar tabla de horario
                for ($hora = 0; $hora < $num_horas; $hora++) {
                    echo '<tr>';
                    echo '<td>' . sprintf("%02d", $hora) . ':00</td>';
                    foreach ($fechas_semana as $fecha) {
                        $tutorias_hora = obtenerTutoriasHora($tutoriasUsuario, $fecha, $hora);
                        $objetivos_hora = obtenerObjetivosHora($objetivosUsuario, $fecha, $hora);
                        $clase_celda = resaltarCelda($tutorias_hora, $objetivos_hora, $fecha, $hora);
                        echo '<td ' . $clase_celda . '>';
                        // Mostrar las tutorías en esta celda
                        foreach ($tutorias_hora as $tutoria) {
                            echo $tutoria['tema'] . '<br>';
                        }
                        // Mostrar los objetivos en esta celda
                        $objetivo_impreso = false; // Inicializar la bandera
                        foreach ($objetivos_hora as $objetivo) {
                            // Imprimir solo una vez el objetivo para esta hora
                            if (!$objetivo_impreso) {
                                echo $objetivo['nombre_objetivo'] . '<br>';
                                $objetivo_impreso = true; // Cambiar el valor de la bandera
                            }
                        }
                        // Si no hay tutorías ni objetivos, dejar la celda vacía
                        if (empty($tutorias_hora) && empty($objetivos_hora)) {
                            echo '-';
                        }
                        echo '</td>';
                    }
                    echo '</tr>';
                }
                ?>
            </table>
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
            window.location.href = 'login.php';
        }

        function irSemanaAnterior() {
            window.location.href = 'horario.php?fecha=<?php echo $fechas_semana[0]->modify('-7 day')->format('Y-m-d'); ?>';
        }

        function irSemanaSiguiente() {
            window.location.href = 'horario.php?fecha=<?php echo end($fechas_semana)->modify('+1 day')->format('Y-m-d'); ?>';
        }
    </script>
</body>
</html>
