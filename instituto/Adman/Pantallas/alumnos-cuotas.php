
<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/instituto/Adman/includes/header.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/instituto/Includes/load.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/instituto/Adman/modals/primerPago.php';

$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y'); // Año actual por defecto
$datos_json_curso = obtenerDatosParaGraficoCurso($year); 

$yearCursoMes = isset($_GET['yearCursoMes']) ? intval($_GET['yearCursoMes']) : date('Y'); // Año actual por defecto

$datos_json_curso_mes= obtenerDatosParaGraficoCursoMes($yearCursoMes); 
function obtenerClaseRojo($valor) {
    return ($valor === NULL || $valor === '0.00') ? 'text-danger' : '';
}
if ($pdo) {
    // Query para obtener los datos de la tabla 'usuarios'
    $sql = "SELECT * FROM barberiakmp.cuotaalumno ca 
    INNER JOIN usuario u ON ca.user=u.User
    INNER JOIN persona p ON p.DNI=u.fk_DNI
    INNER JOIN estado e  ON e.Id_Estado=ca.estado_pago
    INNER JOIN plan pl  ON pl.cod_Plan=ca.cod_Plan";
    $resultCuota = $pdo->query($sql);
    // Check if there's a message in the session

    if (isset($_SESSION['messagePago'])) {
        $messagePago = $_SESSION['messagePago'];
        unset($_SESSION['messagePago']); // Clear the session variable after displaying the message
        showConfirmationMessagesPago($messagePago);
    }
    
    
?>
<style>

.input-large {
    width: 85px; /* Ajusta el ancho según sea necesario */
    padding: 5px; /* Elimina todo el relleno */
    margin-right: 0px;
}
.canvas-container {
            max-width: 600px; /* Ajusta el tamaño máximo del contenedor */
            margin: 0 auto; /* Centra el contenedor */
        }
.export-button {
            margin-bottom: 20px; /* Margen inferior para los botones */
}

.canvas-container-mes{
            max-width: 900px; /* Ajusta el tamaño máximo del contenedor */
            margin: 0 auto; /* Centra el contenedor */
        }

</style>

<main class="app-content">
        <div class="app-title">
            <div>
                <h1><i class="fa fa-dashboard"></i>Cuotas Alumno</h1>

            </div>



        </div>

        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form id="busquedaForm" class="form-row align-items-end mx-auto">
                            <div class="form-group col-md-4 mb-2">
                                <label for="dni" class="text-center">DNI:</label>
                                <input type="number" class="form-control mx-auto" id="dniBusqueda">
                            </div>
                            <div class="form-group col-md-4 mb-2">
                                <label for="nombreUser" class="text-center">Nombre:</label>
                                <input type="text" class="form-control mx-auto" id="nombreUserBusqueda">
                            </div>
                            <div class="form-group col-md-4 mb-2">
                                <label for="apellidoUser" class="text-center">Apellido:</label>
                                <input type="text" class="form-control mx-auto" id="apellidoUserBusqueda">
                            </div>


                        </form>
                    </div>
                </div>
            </div>
        </div>



        <div class="row mt-4">
            <div class="col-lg-6 text-left">
                <!-- Divide la fila en 2 columnas y alinea a la izquierda -->
                <a id="generarPDFBtn" href="#" onclick="descargarMateriaPDF(); return false;" class="planpdf-button">
                    <i class="fas fa-file-pdf"></i> Descargar PDF
                </a>

                <a id="generarEXCELBtn" href="#" onclick="descargarMateriaEXCEL(); return false;" class="planexcel-button">
                    <i class="fas fa-file-excel"></i> Descargar Excel
                </a>
            </div>

            <div class="col-lg-6 text-right">
                <!-- Divide la fila en 2 columnas y alinea a la derecha -->
                <button class="Usernalta-button" id="crearNuevaCarreraBtn" type="button" data-toggle="modal"
                    onclick="openModalPago()"><i class="fas fa-plus"></i> Nuevo Pago</button>
            </div>


        </div>
        <div class="mt-4">
            <div class="row">


                <div class="col-md-12">
                    <div class="tile">
                        <div class="tile-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered" id="tableUsuarios">
                                    <thead>
                                        <tr>                                  
                                            <th>NOMBRE</th>
                                            <th>Apellido</th>
                                            <th>Enero</th>
                                            <th>Febrero</th>
                                            <th>Marzo</th>
                                            <th>Abril</th>
                                            <th>Mayo</th>
                                            <th>Junio</th>
                                            <th>Julio</th>
                                            <th>Agosto</th>
                                            <th>Septiembre</th>
                                            <th>Octubre</th>      
                                            <th>Noviembre</th>
                                            <th>Diciembre</th>
                                            <th>Curso</th>
                                            <th>Editar Pago</th>
                                        </tr>
                                    </thead>
                                    <tbody id="message">
                                        <?php
                                if ($resultCuota) {
                                    while ($row = $resultCuota->fetch(PDO::FETCH_ASSOC)) {
                                        echo '<tr>';
                                        echo '<td>' . $row['Nombre'] . '</td>';
                                        echo '<td>' . $row['Apellido'] . '</td>';
                                        $meses = ['enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

                                        foreach ($meses as $mes) {
                                            $nombreInput = strtolower($mes); // Convertir el nombre del mes a minúsculas para el atributo name
                                            $claseRojo = obtenerClaseRojo($row[$mes]);
                                        
                                            // Imprimir la celda de entrada HTML con el atributo name en minúsculas
                                            echo '<td><input name="' . $nombreInput . '" type="text" class="form-control input-large ' . $claseRojo . '" value="$' . number_format($row[$mes], 0, ',', '.') . '" disabled></td>';
                                        }
                                        echo '<td>' . $row['Carrera'] . '</td>';
                                        echo '<td style="width: 100px;">'; // Ancho específico para los botones
                                        echo '<button class="btn-icon editar-btn" data-id="' . $row['id'] . '"><i class="edit-btn"></i>✏️</button>';
                                        echo '<button name="guardar-btn" class="btn-icon guardar-btn" style="display:none;" data-id="' . $row['id'] . '"><i class="save-btn"></i>💾</button>';
                                        echo '</td>';
                                        echo '</tr>';
                                        
                                    }
                                } else {
                                    echo "Error: " . $sql . "<br>" . $pdo->errorInfo()[2];
                                }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="container-fluid bg-light py-4">
            <div class="row justify-content-center">
                <div class="col-md-6 mb-4">
                    <!-- Filtro de año para el gráfico de pastel -->
                    <form method="GET" class="mb-4">
                        <div class="form-group">
                            <label for="year">Seleccione el año:</label>
                            <select name="year" id="year" class="form-control" onchange="this.form.submit()">
                                <?php
                                // Generar opciones de años (últimos 10 años)
                                $currentYear = date('Y');
                                for ($i = $currentYear; $i >= $currentYear - 10; $i--) {
                                    echo "<option value=\"$i\"" . ($year == $i ? " selected" : "") . ">$i</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </form>

                    <button class="export-button" onclick="exportarExcel('miGraficoPastelCurso', 'GraficoPastel')">Exportar Pastel a Excel</button>
                    <div class="canvas-container">
                        <canvas id="miGraficoPastelCurso"></canvas>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <!-- Filtro de año para el gráfico de barras -->
                    <form method="GET" class="mb-4">
                        <div class="form-group">
                            <label for="yearCursoMes">Seleccione el año:</label>
                            <select name="yearCursoMes" id="yearCursoMes" class="form-control" onchange="this.form.submit()">
                                <?php
                                // Generar opciones de años (últimos 10 años)
                                $currentYear = date('Y');
                                for ($i = $currentYear; $i >= $currentYear - 10; $i--) {
                                    echo "<option value=\"$i\"" . ($yearCursoMes == $i ? " selected" : "") . ">$i</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </form>
                    <button class="export-button" onclick="exportarExcel('miGraficoBarrasMes', 'GraficoBarras')">Exportar Barras a Excel</button>
                    <div class="canvas-container-mes">
                        <canvas id="miGraficoBarrasMes"></canvas>
                    </div>
                </div>
            </div>
         </div>

</main>

<?php
} else {
    echo "Error: No se pudo establecer la conexión a la base de datos.";
}
require_once '../includes/footer.php';

    
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/TableExport/5.2.0/js/tableexport.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    var tableUsuarios = $('#tableUsuarios').DataTable();
    // Cuando se hace clic en el botón de editar
    $('.editar-btn').click(function() {
        var $tr = $(this).closest('tr'); // Obtener la fila actual

        // Habilitar los campos de entrada para edición
        $tr.find('input[type="text"]').prop('disabled', false);

        $tr.find('input[type="text"]').each(function() {
            var valor = $(this).val();
            // Eliminar el símbolo $ (si existe)
            valor = valor.replace('$', '');
            // Establecer el nuevo valor sin $
            $(this).val(valor);
        });
        $tr.find('input[type="text"]').each(function() {
            var valor = $(this).val();
            // Eliminar el símbolo $ (si existe)
            valor = valor.replace('.', '');
            // Establecer el nuevo valor sin $
            $(this).val(valor);
        });


        // Ocultar el botón de editar y mostrar el botón de guardar
        $(this).hide();
        $tr.find('.guardar-btn').show();
    });

    // Cuando se hace clic en el botón de guardar
    $('.guardar-btn').click(function() {
        var id = $(this).data('id'); // Obtener el ID de la cuota desde el atributo data-id
        var $tr = $(this).closest('tr'); // Obtener la fila actual

        // Recopilar los datos de los campos de entrada
        var mesesData = {};
        $tr.find('input[type="text"]').each(function() {
            var name = $(this).attr('name');
            var value = $(this).val();
            mesesData[name] = value;
        });

        // Enviar los datos al servidor
        $.ajax({
            url: "/instituto/Includes/sqluser.php", // Ruta correcta a tu archivo PHP
            type: 'POST',
            data: {
                id: id,
                meses: mesesData
            },
            success: function(response) {
                if (response.type === 'success') {
                    // Deshabilitar los campos de entrada después de guardar
                    $tr.find('input[type="text"]').prop('disabled', true);

                    // Ocultar el botón de guardar y mostrar el botón de editar
                    $tr.find('.guardar-btn').hide();
                    $tr.find('.editar-btn').show();

                    // Opcional: Mostrar un mensaje de éxito
                    alert(response.text);

                    // Opcional: Recargar la página para reflejar los cambios
                    location.reload();
                } else {
                    // Mostrar un mensaje de error si la actualización falló
                    location.reload();
                }
            },
            error: function(xhr, status, error) {
                // Mostrar un mensaje de error genérico si ocurre un problema con la solicitud
                location.reload();
            }
        });
    });
});
</script>

<script>
// Recupera los datos JSON generados por PHP
var datosCurso = <?php echo $datos_json_curso; ?>;

// Obtén el contexto del lienzo
var contextoPastel = document.getElementById('miGraficoPastelCurso').getContext('2d');

// Crea el gráfico de pastel
var miGraficoPastelCurso = new Chart(contextoPastel, {
    type: 'pie',
    data: {
        labels: datosCurso.Carrera,
        datasets: [{
            data: datosCurso.cantidades,
            backgroundColor: ["#FF6384", "#36A2EB", "#FFCE56", "#4BC0C0"]
        }]
    }
});

function actualizarGraficoMes() {
    var year = document.getElementById('yearCursoMes').value;
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'alumnos-cuotas.php?yearCursoMes=' + yearCursoMes, true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            var datosCursoMes = JSON.parse('<?php echo $datos_json_curso_mes; ?>');

            // Actualizar el gráfico de barras
            var contextoBarras = document.getElementById('miGraficoBarrasMes').getContext('2d');
            var miGraficoBarrasMes = new Chart(contextoBarras, {
                type: 'bar',
                data: {
                    labels: datosCursoMes.meses,
                    datasets: [{
                        label: 'Cantidad de Pagos por Mes',
                        data: datosCursoMes.cantidades,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Cantidad de Pagos'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Mes'
                            }
                        }
                    }
                }
            });
        }
    };
    xhr.send();
    return false;
}

// Inicializar el gráfico al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    actualizarGraficoMes();
});
</script>