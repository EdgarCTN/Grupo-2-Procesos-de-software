<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Verificar si se ha proporcionado el código del alumno en la URL
if (isset($_GET['codigo'])) {
    // Obtener el código del alumno desde la URL
    $cod_alumno = $_GET['codigo'];

    // Consultar la base de datos para obtener los detalles del alumno y sus cursos
    $servername = "localhost";
    $username = "pma";
    $password = "";
    $dbname = "sma_unayoe";

    // Crear conexión
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Comprobar la conexión
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Consulta SQL para obtener los detalles del alumno
    $sql_alumno = "SELECT * FROM alumno WHERE cod_alumno = '$cod_alumno'";
    $result_alumno = $conn->query($sql_alumno);

    // Consulta SQL para obtener los cursos del alumno
    $sql_cursos = "SELECT curso.* FROM curso
                    JOIN tutoría ON curso.cod_curso = tutoría.codcurso
                    WHERE tutoría.codalumno = '$cod_alumno'";
    $result_cursos = $conn->query($sql_cursos);

    if ($result_alumno->num_rows > 0) {
        // Mostrar los detalles del alumno
        $row_alumno = $result_alumno->fetch_assoc();
        $nombre = $row_alumno['nombre'];
        $apellidos = $row_alumno['apellidos'];
        $correo = $row_alumno['correo'];
        $facultad = $row_alumno['facultad'];
        $numero_celular = $row_alumno['numero_celular'];

        // Mostrar los detalles del alumno en HTML
        echo "<!DOCTYPE html>";
        echo "<html lang='es'>";
        echo "<head>";
        echo "<meta charset='UTF-8'>";
        echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
        echo "<title>Detalles del alumno</title>";
        echo "<link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css'>";
        echo "<style>";
        echo "body {";
        echo "  background-image: url('fondo_tutor.jpg');";
        echo "  background-size: cover;";
        echo "  background-repeat: no-repeat;";
        echo "  background-attachment: fixed;";
        echo "}";
        echo ".container {";
        echo "  margin-top: 80px;"; // Ajustar la posición vertical
        echo "}";
        echo ".panel-alumno {";
        echo "  background-color: rgba(255, 255, 255, 0.8);"; // Agregar transparencia al panel
        echo "  padding: 20px;";
        echo "  border-radius: 10px;";
        echo "  margin-left: -50px;"; // Mover el panel a la izquierda
        echo "  width: calc(100% - 40px);"; // Restar el doble del margen izquierdo
        echo "}";
        echo ".panel-curso {";
        echo "  background-color: rgba(255, 255, 255, 0.8);"; // Agregar transparencia al panel
        echo "  padding: 20px;";
        echo "  border-radius: 10px;";
        echo "}";
        echo ".volver-btn {";
        echo "  margin-top: 20px;";
        echo "  text-align: center;";
        echo "}";
        echo "</style>";
        echo "</head>";
        echo "<body>";

        echo "<div class='container'>";
        echo "<div class='row'>";
        echo "<div class='col-md-6'>"; // Utilizamos la mitad del ancho disponible para el panel del alumno
        echo "<div class='panel-alumno'>";
        echo "<h2 style='text-align: center; margin-bottom: 20px;'>Detalles del alumno</h2>"; // Alineación central y espacio debajo del título
        echo "<p><strong>Código del alumno:</strong> $cod_alumno</p>";
        echo "<p><strong>Nombre:</strong> $nombre</p>";
        echo "<p><strong>Apellidos:</strong> $apellidos</p>";
        echo "<p><strong>Correo Electrónico:</strong> $correo</p>";
        echo "<p><strong>Facultad:</strong> $facultad</p>";
        echo "<p><strong>Número de Celular:</strong> $numero_celular</p>";
        echo "</div>";
        echo "</div>";

        echo "<div class='col-md-6'>"; // Utilizamos la otra mitad del ancho disponible para el panel de cursos
        echo "<div class='panel-curso'>";
        echo "<h2 style='text-align: center; margin-bottom: 20px;'>Detalles de curso</h2>"; // Título centrado
        // Mostrar los detalles de los cursos del alumno en una tabla
        if ($result_cursos->num_rows > 0) {
            echo "<table class='table'>";
            echo "<thead>";
            echo "<tr>";
            echo "<th>Código de Curso</th>";
            echo "<th>Nombre de Curso</th>";
            echo "<th>Ciclo</th>";
            echo "<th>Créditos</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";
            while ($row_curso = $result_cursos->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row_curso['cod_curso'] . "</td>";
                echo "<td>" . $row_curso['nombre_curso'] . "</td>";
                echo "<td>" . $row_curso['ciclo'] . "</td>";
                echo "<td>" . $row_curso['creditos'] . "</td>";
                echo "</tr>";
            }
            echo "</tbody>";
            echo "</table>";
        } else {
            echo "El alumno no está inscrito en ningún curso.";
        }
        echo "</div>";
        echo "</div>";

        echo "</div>"; // Cierre de la fila
        echo "</div>"; // Cierre del contenedor

        // Botón Volver
        echo "<div class='volver-btn'>";
        echo "<a href='dashboard_tabla_profesor.php' class='btn btn-primary'>Volver</a>";
        echo "</div>";

        echo "</body>";
        echo "</html>";
    } else {
        echo "No se encontraron detalles para este alumno.";
    }
    $conn->close();
} else {
    echo "No se proporcionó el código del alumno.";
}
?>

