<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/instituto/Includes/load.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/instituto/Adman/Pantallas/alumnos-cuotas.php';


$DatosAlumnoNota = DatosAlumnoNota();
$DatosMes = DatosMes();
$DatosCarrera = DatosCarrera();


?>
<!-- Agregar jQuery y Select2 -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Modal de Alta de Nota -->

<div class="modal fade" id="modalCrearNota" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header headerNota">
                <h5 class="modal-title" id="tituloModalCrearPago">Alta de Nota</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formCrearPago" name="formCrearPago" action="/instituto/Includes/sqluser.php" method="POST">

                    <input type="hidden" name="idPlancrearPago" id="idPlancrearPago"
                        value="<?php  echo $materia['id_Cursada']?>">


                    <div class="form-group">
                        <label for="alumnoPago">Alumno</label>
                        <select id="alumnoPago" name="alumnoPago" class="form-control">
                            <option value="">--Seleccione--</option>
                            <?php foreach ($DatosAlumnoNota as $alumno) : ?>
                            <option value="<?= $alumno['User'] ?>">
                                <?= $alumno['Nombre'] . ' ' . $alumno['Apellido'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group" style="display: none;">
                        <label for="LegajoPago">Legajo</label>
                        <input type="text" id="LegajoPago" name="LegajoPago" class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label for="mes">Mes</label>
                        <select id="mes" name="mes" class="form-control">
                            <option value="">--Seleccione--</option>
                            <?php foreach ($DatosMes as $mes) : ?>
                            <option value="<?= $mes['id'] ?>"><?= $mes['nombre'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>


                    <div class="form-group">
                        <label for="">Curso</label>
                        <select id="carrera" name="carrera" class="form-control">
                            <option value="">--Seleccione--</option>
                            <?php foreach ($DatosCarrera as $carrera) : ?>
                            <option value="<?= $carrera['cod_Plan'] ?>"><?= $carrera['Carrera'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="montoCuota">Monto</label>
                        <input type="number" class="form-control" id="montoCuota" name="montoCuota">
                    </div>

                   

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button id="btnActionAltaPlan" class="btn btn-primary btn-open-modal" type="submit"
                            name="btnmCrearPago">
                            <span id="btnCrearPago">Guardar</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function isValidInput(value) {
    return value.trim() !== '';
}

// Función para abrir el modal de Alta de Nota
function openModalPago() {
    console.log('Abrir modal');

    document.getElementById('idPlancrearPago').value = "";
    document.querySelector('.modal-header').classList.replace("headerUpdate", "headerNota");
    document.getElementById('btnCrearPago').classList.replace("btn-info", "btn-open-modal");
    document.getElementById('btnCrearPago').innerHTML = 'Guardar';
    document.getElementById('tituloModalCrearPago').innerHTML = 'Alta Primer Pago';
    document.getElementById('formCrearPago').reset();

    $('#modalCrearNota').modal('show');


}
(document).ready(function() {

    $('#formCrearPago').on('submit', function(event) {
        event.preventDefault(); //
        console.log('Botón Guardar clickeado');
        var idPlancrearPago = $("#idPlancrearPago").text(); // Usar "dniUser" en lugar de "dni"
        var mes = $("#mes").val();
        var carrera = $("#carrera").val();
        var anioMateria = $("#anioMateria").val();
        var estadoMateria = $("#estadoMateria").val();
        var montoCuota = $("#montoCuota").val();
        var recuperatorio1 = $("#recuperatorio1").val();
        var parcial2 = $("#parcial2").val();
        var recuperatorio2 = $("#recuperatorio2").val();
        var promedio = $("#promedio").val();
        var finalnota = $("#finalnota").val();

        


        // Realizar la petición AJAX para insertar o actualizar datos
        $.ajax({
            url: "/instituto/Includes/sqluser.php", // Reemplaza con la ruta correcta a tu archivo PHP
            type: "POST",
            data: {
                alumnoPago: alumnoPago,
                LegajoPago: LegajoPago,
                mes: mes,
                carrera: carrera,
                anioMateria: anioMateria,
                estadoMateria: estadoMateria,
                montoCuota: montoCuota,
                recuperatorio1: recuperatorio1,
                parcial2: parcial2,
                recuperatorio2: recuperatorio2,
                promedio: promedio,
                finalnota: finalnota,

                btnmCrearPago: 0
            },

            success: function(response) {
                // Verificar la respuesta del servidor
                if (response.success) {
                    // Cerrar el modal
                    $('#modalNotaCrear').modal('hide');

                    // Mostrar mensaje de éxito
                    Swal.fire({
                        icon: 'success',
                        title: 'Datos guardados exitosamente',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        window.location.reload();
                    });
                } else {
                    // Mostrar mensaje de error
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al guardar los datos',
                        text: response.message
                    });
                }
            },
            error: function(error) {
                console.log("Error en la solicitud AJAX:", error);
            }
        });
    });




});
</script>







<script>
$(document).ready(function() {
    $('#alumnoPago').selectize({
        options: <?php echo json_encode($DatosAlumnoNota); ?>,
        labelField: 'NombreApellido',
        valueField: 'Id_Usuario',
        searchField: ['Nombre', 'Apellido'],
        create: false,
        onChange: function(value) {
            obtenerLegajoPorAlumno(value);
        }
    });

    function obtenerLegajoPorAlumno(alumnoId) {
        $.ajax({
            url: '/instituto/Includes/funcionesLegajo.php', // Ruta correcta al archivo PHP
            type: 'POST',
            data: {
                id: alumnoId, // El ID del alumno que deseas consultar
                accion: 'legajo' // Agrega un parámetro de acción para obtener el legajo
            },
            success: function(response) {
                var legajoSinEspacios = response.trim();

                // Llena el campo "LegajoNota" con la respuesta del servidor
                $('#LegajoPago').val(legajoSinEspacios);
            },
            error: function(error) {
                console.log('Error al obtener el legajo: ' + error);
            }
        });

    }


});

$(document).ready(function() {
    $('#materia').selectize({
        options: <?php echo json_encode($DatosMateria); ?>,
        labelField: 'Descripcion', // Nombre del campo a mostrar
        valueField: 'id_Materia', // Nombre del campo a utilizar como valor
        searchField: ['Descripcion'], // Campos para buscar
        render: {
            option: function(item) {
                return '<div>' + item.Descripcion + '</div>';
            },
        },
        create: false, // No permitir crear nuevas opciones
    });
});


$(document).ready(function() {
    $('#materia').change(function() {
        var materiaId = $(this).val();

        if (materiaId !== '') {
            obtenerAnioMateria(materiaId);
        }
    });

    function obtenerAnioMateria(materiaId) {
        $.ajax({
            url: '/instituto/Includes/funcionesLegajo.php', // Ruta correcta al archivo PHP
            type: 'POST',
            data: {
                idAnio: materiaId, // El ID de la materia que deseas consultar
                accion: 'anio' // Agrega un parámetro de acción para obtener el año de la carrera
            },
            success: function(response) {
                var anioSinEspacios = response.trim();

                // Llena el campo "anioMateria" con la respuesta del servidor
                $('#anioMateria').val(anioSinEspacios);
            },
            error: function(error) {
                console.log('Error al obtener el año de la materia: ' + error);
            }
        });
    }
});


$(document).ready(function() {
    $('#materia').change(function() {
        var estadoId = $(this).val();

        if (estadoId !== '') {
            obtenerEstadoMateria(estadoId);
        }
    });

    function obtenerEstadoMateria(estadoId) {
        $.ajax({
            url: '/instituto/Includes/funcionesLegajo.php', // Ruta correcta al archivo PHP
            type: 'POST',
            data: {
                idEstado: estadoId, // El ID de la materia que deseas consultar
                accion: 'estado' // Agrega un parámetro de acción para obtener el año de la carrera
            },
            success: function(response) {
                var estadoSinEspacios = response.trim();

                // Llena el campo "anioMateria" con la respuesta del servidor
                $('#estadoMateria').val(estadoSinEspacios);
            },
            error: function(error) {
                console.log('Error al obtener el estado de la materia: ' + error);
            }
        });
    }
});
</script>