<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Alumnos en Riesgo Académico</title>
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
    <h2 class="mt-4 mb-4">Alumnos en Riesgo Académico</h2>
    <div style="overflow-x:auto;">
      <table class="table table-striped" id="tablaAlumnos">
        <thead>
          <tr>
            <th>Código del Alumno</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Correo Electrónico</th>
            <th>Detalles</th>
          </tr>
        </thead>
        <tbody>
          <?php
          //  datos de prueba de los alumnos en riesgo académico
          $alumnos = array(
            array("codigo" => "A001", "nombre" => "Alan", "apellido" => "Garcia", "email" => "Vivo@example.com"),
            array("codigo" => "A001", "nombre" => "Edgar", "apellido" => "Tejeda ", "email" => "F@example.com"),
            array("codigo" => "A001", "nombre" => "Romani ", "apellido" => "Anthony ", "email" => "F@example.com"),
            array("codigo" => "A001", "nombre" => "Barrantes ", "apellido" => "Joshúa ", "email" => "F@example.com"),
            array("codigo" => "A001", "nombre" => "Tataje ", "apellido" => "Kenner ", "email" => "F@example.com"),
            array("codigo" => "A001", "nombre" => "Villanueva  ", "apellido" => "Cesar  ", "email" => "F@example.com"),
            array("codigo" => "A001", "nombre" => "Serna ", "apellido" => "Andrew  ", "email" => "F@example.com"),
            array("codigo" => "A004", "nombre" => "Mr", "apellido" => "Beast", "email" => "MrBeast@example.com"),
            array("codigo" => "A000", "nombre" => "Goku", "apellido" => "Son", "email" => "DBZ@TOEI.com"),
            array("codigo" => "A004", "nombre" => "NPC", "apellido" => "Nr1", "email" => "NPC1@example.com"),
            array("codigo" => "A004", "nombre" => "NPC", "apellido" => "Nr2", "email" => "NPC2@example.com"),
            array("codigo" => "A004", "nombre" => "NPC", "apellido" => "Nr3", "email" => "NP3@example.com")


          );

          //  tabla
          foreach ($alumnos as $alumno) {
            echo "<tr>";
            echo "<td>{$alumno['codigo']}</td>";
            echo "<td>{$alumno['nombre']}</td>";
            echo "<td>{$alumno['apellido']}</td>";
            echo "<td>{$alumno['email']}</td>";
            echo "<td><a href=\"detalle_alumno.php?codigo={$alumno['codigo']}\" class=\"btn btn-primary\">Ver Detalles</a></td>";
            echo "</tr>";
          }
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
        }
      ]
    });
  });
</script>

</body>
</html>
