<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lista de alumnos observados</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.7.0/css/buttons.dataTables.min.css">
  <style>
    /*  fondo de la página */
    body {
        background-image: url('fondo_tutor.jpg');
        background-size: cover;
        background-repeat: no-repeat;
        background-attachment: fixed;
    }

    .container {
      background-color: rgba(255, 255, 255, 0.9); /*  fondo semi-transparente  */
      padding: 20px;
      margin-top: 20px;
      position: relative; /* posición de botón de regreso  */
    }

    .table {
      border: 1px solid #dee2e6;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    .dataTables_wrapper .dataTables_filter input {
      border: 1px solid #ced4da;
      border-radius: 5px;
      padding: 5px;
    }
    .dt-buttons {
      margin-bottom: 10px;
    }
    .btn-excel {
      color: #fff;
      background-color: #28a745;
      border-color: #28a745;
    }
    .btn-excel:hover {
      color: #fff;
      background-color: #218838;
      border-color: #1e7e34;
    }
    .btn-lista-alumnos {
      color: #fff;
      background-color: #007bff;
      border-color: #007bff;
    }
    .btn-lista-alumnos:hover {
      color: #fff;
      background-color: #0056b3;
      border-color: #004275;
    }
    .btn-regresar {
      position: absolute;
      top: 45px;
      right: 45px;
    }
  </style>
</head>
<body>

  <div class="container">
    <a href="dashboard_tutor.php" class="btn btn-primary btn-regresar">Volver</a> <!-- Botón de regresar -->
    <h2><br>Alumnos observados - Lista completa </h2><br>

    <div style="overflow-x:auto;">
      <table class="table table-striped" id="tablaAlumnos">
        <thead>
          <tr>
            <th>Código </th>
            <th>Nombre</th>
            <th>Apellidos</th>
            <th>Correo Electrónico</th>
            <th>Asignar alumno</th>
          </tr>
        </thead>
        <tbody>
          <?php
          // Conexión a la base de datos 
          $servername = "localhost";
          $username = "pma";
          $password = "";
          $dbname = "sma_unayoe";

          //  conexión de base de datos
          $conn = new mysqli($servername, $username, $password, $dbname);

          // Comprobación
          if ($conn->connect_error) {
              die("Conexión fallida: " . $conn->connect_error);
          }

          // Consulta para obtener los datos de los alumnos
          $sql = "SELECT cod_alumno, nombre, apellidos, correo FROM alumno";
          $result = $conn->query($sql);

          // Mostrar los datos en la tabla
          if ($result->num_rows > 0) {
              while($row = $result->fetch_assoc()) {
                  echo "<tr>";
                  echo "<td>{$row['cod_alumno']}</td>";
                  echo "<td>{$row['nombre']}</td>";
                  echo "<td>{$row['apellidos']}</td>";
                  echo "<td>{$row['correo']}</td>";
                  echo "<td><a href=\"dashboard_detalle_alumno.php?codigo={$row['cod_alumno']}\" class=\"btn btn-primary\">Ver Detalles</a></td>";
                  echo "</tr>";
              }
          } else {
              echo "0 resultados";
          }
          $conn->close();
          ?>
        </tbody>
      </table>
    </div>
  </div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
<script>

  $(document).ready(function(){
    $('#tablaAlumnos').DataTable({
      "paging": true, //  numero de pagina de la tabla
      "language": {
        "sProcessing":     "Procesando...",
        "sLengthMenu":     "Mostrar _MENU_ registros",
        "sZeroRecords":    "No se encontraron resultados",
        "sEmptyTable":     "Ningún dato disponible en esta tabla",
        "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
        "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
        "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
        "sInfoPostFix":    "",
        "sSearch":         "Buscar:",
        "sUrl":            "",
        "sInfoThousands":  ",",
        "sLoadingRecords": "Cargando...",
        "oPaginate": {
          "sFirst":    "Primero",
          "sLast":     "Último",
          "sNext":     "Siguiente",
          "sPrevious": "Anterior"
        },
        "oAria": {
          "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
          "sSortDescending": ": Activar para ordenar la columna de manera descendente"
        }
      },
      "dom": 'Bfrtip',
      "buttons": [
        {
          extend: 'excel',
          className: 'btn btn-excel',
          text: 'Exportar a Excel'
        },
        {
          extend: 'excel',
          className: 'btn btn-excel',
          text: 'Alumnos asignados',
          action: function (e, dt, node, config) {
            window.location.href = 'dashboard_tabla_profesor.php'; // Cambiar la URL según sea necesario
          }
        }
      ]
    });
  });
</script>

</body>
</html>