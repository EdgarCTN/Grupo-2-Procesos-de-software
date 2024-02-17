<?php
// Iniciar la sesión
session_start();

// Incluir el archivo de conexión a la base de datos
include 'conn/connection.php';

// Variable para almacenar el mensaje de error
$error_message = "";

// Verificar si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_usuario = $_POST['nombre_usuario'];
    $contraseña = $_POST['contraseña'];

    try {
        // Preparar la consulta SQL para obtener usuario y rol
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE nombre_usuario = :nombre_usuario");
        $stmt->bindParam(':nombre_usuario', $nombre_usuario);

        // Ejecutar la consulta
        $stmt->execute();

        // Verificar si se encontró un usuario
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($contraseña == $user['contraseña']) {
                // Iniciar la sesión y redirigir al usuario
                $_SESSION['nombre'] = $nombre_usuario;
                $_SESSION['loggedin'] = true;

                // Obtener el rol del usuario
                $rol = $user['rol'];

                // Redirigir al usuario según su rol
                switch ($rol) {
                    case 'Alumno':
                        header("Location: dashboard_alumno.php");
                        break;
                    case 'Tutor':
                        header("Location: dashboard_tutor.php");
                        break;
                    case 'Administrador':
                        header("Location: dashboard_administrador.php");
                        break;
                    // Agrega más casos según los roles que tengas en tu sistema
                    default:
                        // Redirigir a una página por defecto si el rol no se reconoce
                        header("Location: default_dashboard.php");
                        break;
                }
            } else {
                $error_message = "Nombre de usuario o contraseña incorrectos.";
            }
        } else {
            $error_message = "Nombre de usuario o contraseña incorrectos.";
        }
    } catch(PDOException $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <!-- Vincular al archivo CSS -->
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="image-container">
            <!-- Aquí puedes agregar tu imagen -->
            <img src="/FISI.jpg" alt="Imagen descriptiva">
        </div>
        <div class="form-container">
            <h1>Sistema de Monitoreo de Tutorías</h1>

            <?php
            if (isset($error_message)) {
                echo "<p class='error' style='color: red;'>$error_message</p>";
            }
            ?>

            <!-- Formulario de inicio de sesión -->
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <label for="nombre">Nombre de usuario:</label>
                <input type="text" id="nombre" name="nombre_usuario">
                <label for="contraseña">Contraseña:</label>
                <input type="password" id="contraseña" name="contraseña">
                <input type="submit" value="Iniciar sesión">
            </form>
        </div>
    </div>
</body>
</html>
