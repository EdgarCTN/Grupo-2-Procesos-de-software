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
            <!-- Aquí va tu formulario de inicio de sesión -->
            <?php
            // Iniciar la sesión
            session_start();

            // Incluir el archivo de conexión a la base de datos
            include 'conn/connection.php';

            // Verificar si el formulario ha sido enviado
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $nombre_usuario = $_POST['nombre_usuario'];
                $contraseña = $_POST['contraseña'];

                try {
                    // Preparar la consulta SQL
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
                            $_SESSION['loggedin'] = true; // Añadir esta línea
                            header("Location: dashboard.php");
                        } else {
                            echo "<p class='error' style='color: red;'>Nombre de usuario o contraseña incorrectos.</p>";
                        }
                    } else {
                        echo "<p class='error' style='color: red;'>Nombre de usuario o contraseña incorrectos.</p>";
                    }
                } catch(PDOException $e) {
                    echo "<p class='error' style='color: red;'>Error: " . $e->getMessage() . "</p>";
                }
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
