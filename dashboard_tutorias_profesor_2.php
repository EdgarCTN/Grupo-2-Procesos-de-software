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
    $stmt_usuario->bindParam(':nombre_usuario', $_SESSION['nombre']);
    $stmt_usuario->execute();

    // Verificar si se encontró el usuario
    if ($stmt_usuario->rowCount() > 0) {
        $resultado_usuario = $stmt_usuario->fetch(PDO::FETCH_ASSOC);
        $id_usuario = $resultado_usuario['id'];

        // Preparar la consulta SQL para obtener el código del tutor usando el ID del usuario
        $stmt_tutor = $conn->prepare("SELECT cod_tutor FROM tutor WHERE id_usuario = :id_usuario");
        $stmt_tutor->bindParam(':id_usuario', $id_usuario);
        $stmt_tutor->execute();

        // Verificar si se encontró el tutor
        if ($stmt_tutor->rowCount() > 0) {
            $resultado_tutor = $stmt_tutor->fetch(PDO::FETCH_ASSOC);
            $codigo_tutor = $resultado_tutor['cod_tutor'];
        } else {
            // Si el tutor no se encuentra, mostrar un mensaje de error o asignar un valor predeterminado
            $codigo_tutor = "No disponible";
        }
    } else {
        // Si no se encuentra el usuario, mostrar un mensaje de error o asignar valores por defecto
        $codigo_tutor = "No disponible";
    }
} catch(PDOException $e) {
    // Manejar errores de base de datos
    $codigo_tutor = "Error";
    echo "Error al obtener el código del tutor: " . $e->getMessage();
}

// Procesamiento del formulario de agregar tutoría
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recuperar y procesar los datos del formulario
    $codalumno = $_POST["codalumno"];
    $codtutor = $_POST["codtutor"];
    $codcurso = $_POST["codcurso"];
    $fecha = $_POST["fecha"];
    $hora = $_POST["hora"];
    $tema = $_POST["tema"];

    // Conectar a la base de datos
    $conexion = new mysqli("localhost", "pma", "root", "sma_unayoe");

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
        header("Location: dashboard_tutor.php");
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
    <title>Inicio - Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #1e272e; /* Cambiar el color de fondo a azul oscuro */
        }

        /* Resto de estilos del documento... */
        .container {
            display: flex;
            min-height: 100vh;
            margin-left: auto; /* Mover el contenido hacia la derecha */
            padding-right: 20px; /* Ajustar el espacio en la derecha */
        }

        .main-content {
            background-color: #1e272e; /* Cambiar el color de fondo a azul oscuro */
            padding: 20px;
            flex: 1;
        }

        .contenedor-form {
            width: 50%;
            align-items: center;
            justify-content: center;
            background-color: #fff;
            margin: 0px 10px 0px 20px;
            padding: 20px 20px 40px 20px;
            border-radius: 30px;
            color: #000; /* Color del texto */
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3); /* Sombra */
        }

        .titulo_agregar_tutoria {
            background-color: #2c3e50;
            color: #fff;
            padding: 10px;
            border-radius: 10px;
            margin-top: 0; /* Eliminar el margen superior */
            text-align: center; /* Centrar el texto */
        }

        .contenedor-form label {
            display: block;
            margin-bottom: 10px;
            color: #1e272e;
        }

        .contenedor-form input[type="text"],
        .contenedor-form input[type="date"],
        .contenedor-form input[type="time"],
        .contenedor-form select,
        .contenedor-form input[type="submit"] {
            width: calc(100% - 22px); /* Ajustar el ancho para evitar desbordamiento */
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
            color: #555;
            margin-left: 11px; /* Centrar los campos de entrada */
        }

        .contenedor-form input[type="submit"] {
            width: 100%; /* Utilizar todo el ancho disponible */
            padding: 15px; /* Aumentar el relleno */
            background-color: #2ecc71; /* Cambiar el color de fondo del botón */
            color: #fff;
            cursor: pointer;
            margin-left: 0; /* Eliminar el margen izquierdo */
        }

        .contenedor-form input[type="submit"]:hover {
            background-color: #27ae60; /* Cambiar el color de fondo del botón al pasar el ratón */
        }
        body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #1e272e; /* Cambiar el color de fondo a azul oscuro */
}

/* Estilos del botón "Agregar Tutoría" */
.contenedor-form button[type="submit"] {
    padding: 10px 20px;
    margin-top: 20px;
    background-color: #2ecc71; /* Cambiar el color de fondo a verde */
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
    width: 100%; /* Utilizar todo el ancho disponible */
    font-size: 25px; /* Tamaño de fuente */
    font-weight: bold; /* Negrita */
}

.contenedor-form button[type="submit"]:hover {
    background-color: #27ae60; /* Cambiar el color de fondo al pasar el ratón */
}

/* Resto de estilos del documento... */
.container {
    display: flex;
    min-height: 100vh;
    margin-left: auto; /* Mover el contenido hacia la derecha */
    padding-right: 20px; /* Ajustar el espacio en la derecha */
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
    margin-bottom: 15px;
}

.sidebar a {
    color: #fff;
    text-decoration: none;
    font-size: 20px;
    font-weight: bold;
    display: block;
    padding: 10px 15px;
    border-radius: 10px;
    transition: background-color 0.3s;
    background-color: #455a64;
}

.sidebar a:hover {
    background-color: #607d8b;
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
    background-color: #1e272e; /* Cambiar el color de fondo a azul oscuro */
    padding: 20px;
    flex: 1;
}

.welcome-message {
    margin-bottom: 20px;
    font-size: 24px;
    border-bottom: 2px solid #2c3e50;
    padding-bottom: 10px;
    color: #fff; /* Cambiar el color del texto a blanco */
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

.contenedor-general {
    width: 100%;
    display: flex;
    flex-direction: row;
}

.cont_general {
    width: 100%;
    display: flex;
    flex-direction: row;
    justify-content: space-between; /* Alinear los contenedores al extremo */                      
}

.contenedor-datos {
    width: 60%; /* Ajustar el ancho de la tabla */
    align-items: center;
    justify-content: center;
    background-color: #fff;
    margin: 0px 20px 10px 0px;
    border: 2px solid #ffffff;
    padding: 20px;
    text-align: center;
    border-radius: 30px;
    color: #000; /* Color del texto */
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3); /* Sombra */
}

.contenedor-datos h2 {
    margin-bottom: 10px;
}

.contenedor-datos table {
    width: 100%;
    border-collapse: collapse;
}

        /* Estilo para el botón */
        .btn-blue {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none; /* Eliminar subrayado */
            display: inline-block; /* Mostrar como bloque en línea */
        }

        .btn-blue:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

</head>
<body>
    <div class="container">
        <div class="main-content">
<!-- Menú -->
<div class="container">
    <div class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <img src="fisi.png" alt="Logo Facultad">
        </div>
        <ul>
            <li><a href="dashboard_tutor.php">Inicio</a></li>
            <li><a href="dashboard_tabla_profesor.php">Alumnos</a></li>
            <li><a href="dashboard_tutorias_profesor.php">Tutorias</a></li>
            <li><button onclick="showPopup()">Salir</button></li>
        </ul>
    </div>



            <!-- Formulario para agregar tutoría -->
            <div class="contenedor-form">
                <h1 class="titulo_agregar_tutoria">Agregar Tutoría</h1>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

                    <!-- Campo para el Código Tutor -->
                    <label for="codtutor">Código Tutor:</label>
                    <?php
                    // Mostrar el código del tutor si está disponible
                    if (!empty($codigo_tutor)) {
                        // Mostrar el código del tutor en un campo de solo lectura
                        echo '<input type="text" name="codtutor" value="' . htmlspecialchars($codigo_tutor) . '" readonly required>';
                    } else {
                        // Si no se encuentra el código del tutor, mostrar un mensaje indicando que no está disponible
                        echo '<input type="text" name="codtutor" value="Código no disponible" readonly required>';
                    }
                    ?>

                    <!-- Otros campos del formulario -->
                    <label for="codalumno">Código Alumno:</label>
                    <select name="codalumno" required>
                        <!-- Opciones para seleccionar el código del alumno -->
                        <?php
                        try {
                            // Preparar la consulta SQL para obtener los códigos de todos los alumnos
                            $stmt_alumnos = $conn->query("SELECT cod_alumno FROM alumno");

                            // Verificar si se encontraron alumnos
                            if ($stmt_alumnos->rowCount() > 0) {
                                while ($fila = $stmt_alumnos->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<option value='" . $fila['cod_alumno'] . "'>" . $fila['cod_alumno'] . "</option>";
                                }
                            } else {
                                // Si no se encuentran alumnos, mostrar un mensaje de error
                                echo "<option value=''>No hay alumnos disponibles</option>";
                            }
                        } catch (PDOException $e) {
                            // Manejar errores de base de datos
                            echo "<option value=''>Error al obtener los alumnos</option>";
                        }
                        ?>
                    </select>

                    <label for="codcurso">Código Curso:</label>
                    <select name="codcurso" required>
                        <!-- Opciones para seleccionar el código del curso -->
                        <?php
                        // Consulta para obtener los códigos de los cursos ordenados alfabéticamente
                        $consulta_cursos = $conn->query("SELECT cod_curso FROM curso ORDER BY nombre_curso");

                        while ($fila = $consulta_cursos->fetch(PDO::FETCH_ASSOC)) {
                            echo "<option value='" . $fila['cod_curso'] . "'>" . $fila['cod_curso'] . "</option>";
                        }
                        ?>
                    </select>

                    <label for="fecha">Fecha:</label>
                    <input type="date" name="fecha" required>

                    <label for="hora">Hora:</label>
                    <input type="time" name="hora" required>

                    <label for="tema">Tema (mínimo 10 letras):</label>
                    <input type="text" name="tema" required minlength="10">

                    <button type="submit">Agregar Tutoría</button>
                </form>
            </div>
            <div>
                    <a href="dashboard_tutorias_profesor.php" class="btn-blue">Ir a tabla de tutorias</a>
                </div>

        </div>
    </div>
</body>
</html>
