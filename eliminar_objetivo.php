<?php
session_start();
include 'conn/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['nombre_objetivo'])) {
        $nombre_objetivo = $_POST['nombre_objetivo'];
        $stmt = $conn->prepare("DELETE FROM objetivo WHERE nombre_objetivo = :nombre_objetivo");
        $stmt->bindParam(':nombre_objetivo', $nombre_objetivo);
        if ($stmt->execute()) {
            // Éxito al eliminar el objetivo
            echo "Success";
        } else {
            // Error al eliminar el objetivo
            echo "Error";
        }
    }
}
?>