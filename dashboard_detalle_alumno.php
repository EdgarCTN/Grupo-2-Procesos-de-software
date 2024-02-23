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

    // Consultar la base de datos para obtener los detalles del alumno
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
    $sql = "SELECT * FROM alumno WHERE cod_alumno = '$cod_alumno'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Mostrar los detalles del alumno
        $row = $result->fetch_assoc();
        $nombre = $row['nombre'];
        $apellidos = $row['apellidos'];
        $correo = $row['correo'];
        $facultad = $row['facultad'];
        $numero_celular = $row['numero_celular'];
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
        echo ".panel-estadisticas {";
        echo "  background-color: rgba(255, 255, 255, 0.8);"; // Agregar transparencia al panel
        echo "  padding: 20px;";
        echo "  border-radius: 10px;";
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

        echo "<div class='col-md-6'>"; // Utilizamos la otra mitad del ancho disponible para el nuevo panel
        echo "<div class='panel-estadisticas'>";
        echo "<h2 style='text-align: center; margin-bottom: 20px;'>Estadísticas del alumno</h2>"; // Título centrado
        // Aquí puedes agregar las estadísticas del alumno si las tienes
        echo "</div>";
        echo "</div>";

        echo "</div>"; // Cierre de la fila
        echo "</div>"; // Cierre del contenedor

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

