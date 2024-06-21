<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/instituto/Adman/includes/header.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/instituto/Includes/load.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/instituto/Adman/modals/save_event.php';

if ($pdo) {
    // Consulta SQL para obtener eventos
    $sql = "SELECT id, title, description, date FROM eventos";
    $stmt = $pdo->query($sql);

    // Array para almacenar eventos
    $eventos = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $evento = array();
        $evento['id'] = $row['id'];
        $evento['title'] = $row['title'];
        $evento['description'] = $row['description'];
        $evento['start'] = $row['date']; // 'start' debe ser una fecha en formato ISO8601

        $eventos[] = $evento;
    }
} else {
    echo "Error: No se pudo establecer la conexión a la base de datos.";
}

?>
<!-- Agrega estilos para el contenedor del calendario -->
<style>
  

.fc-daygrid-body {
    background-color: #ffffff;
 
    
}
#calendar {
    max-width: 3100px;
    margin: 0 auto;
    padding: 20px;
    background-color: #ffffff; /* Color de fondo del calendario */
    border: 4px solid #ccc; /* Borde del calendario */
    border-radius: 40px; /* Bordes redondeados */
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Sombra para darle un poco de profundidad */
}

.fc-event {
    padding: 10px;
    font-size: 16px;
    background-color: #5cb85c;
    color: #ffffff;
    border: 1px solid #4cae4c;
    border-radius: 8px;
}
    
</style>




<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="fa fa-dashboard"></i>Turnos Clientes</h1>
        </div>
    </div>

    <div id="calendar" ></div>
    <div id="message"></div> <!-- Elemento para mostrar mensajes -->
</main>

<!-- Modal -->
<div id="eventModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Evento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="eventForm">
                    <div class="form-group">
                        <label for="eventTitle">Título del Evento</label>
                        <input type="text" class="form-control" id="eventTitle" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="eventDescription">Descripción del Evento</label>
                        <textarea class="form-control" id="eventDescription" name="description" required></textarea>
                    </div>
                    <input type="hidden" id="eventDate" name="date">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="saveEvent">Guardar</button>
            </div>

        
        </div>
    </div>
</div>

<!-- Modal para ver detalles del evento -->
<!-- Modal para ver y editar detalles del evento -->
<div id="eventDetailModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles del Turno</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="detailEventTitle">Título del Evento</label>
                    <input type="text" class="form-control" id="detailEventTitle">
                </div>
                <div class="form-group">
                    <label for="detailEventDescription">Descripción del Evento</label>
                    <textarea class="form-control" id="detailEventDescription"></textarea>
                </div>
                <div class="form-group">
                    <label for="detailEventDate">Fecha del Evento</label>
                    <div class="input-group date">
                        <input type="text" class="form-control" id="detailEventDate">
                        <span class="input-group-append">
                            <span class="input-group-text bg-white">
                                <i class="fa fa-calendar"></i>
                            </span>
                        </span>
                    </div>
                </div>
                <input type="hidden" id="detailEventId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="deleteEvent">Eliminar</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="updateEvent">Guardar</button>
            </div>
        </div>
    </div>
</div>

</div>

<link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.10.1/main.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.10.1/main.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.10.1/locales-all.min.js"></script>
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.0/main.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.0/main.js"></script>>


<script>

$('.modal-footer .btn-secondary').on('click', function() {
        $('#eventDetailModal').modal('hide');
});
// Cerrar el modal manualmente cuando se hace clic en "Cancelar"
 $('.modal-footer .btn-secondary').on('click', function() {
        $('#eventModal').modal('hide');
});

      // Cerrar el modal manualmente cuando se hace clic en "Guardar"
$('.modal-footer .btn-primary').on('click', function() {
        $('#eventModal').modal('hide');
});




$(document).ready(function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        themeSystem: 'bootstrap', // Cambia el tema del calendario (puedes usar 'standard', 'bootstrap', 'jquery-ui', etc.)
        events: <?php echo json_encode($eventos); ?>,
        locale: 'es',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        height: 'auto',
        contentHeight: 'auto',
        dateClick: function(info) {
            $('#eventDate').val(info.dateStr);
            $('#eventModal').modal('show');
        },
        eventClick: function(info) {
            // Llenar los campos del modal con la información del evento
            $('#detailEventTitle').val(info.event.title);
            $('#detailEventDescription').val(info.event.extendedProps.description);
            $('#detailEventDate').val(info.event.start.toISOString().slice(0, 10));

            // Mostrar el modal con los detalles del evento
            $('#eventDetailModal').modal('show');
        }
    });

    calendar.render();

    $('#saveEvent').on('click', function() {
        var title = $('#eventTitle').val();
        var description = $('#eventDescription').val();
        var date = $('#eventDate').val();

        if (title && date) {
            $.ajax({
                url: '/instituto/Adman/modals/save_event.php',
                type: 'POST',
                data: {
                    title: title,
                    description: description,
                    date: date
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#eventModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Evento guardado exitosamente',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(function() {
                            calendar.refetchEvents();
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Hubo un problema al guardar el evento',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                },
                error: function(error) {
                    Swal.fire({
                            icon: 'success',
                            title: 'Evento guardado exitosamente',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(function() {
                            calendar.refetchEvents();
                            window.location.reload();
                        });
                    console.log("Error en la solicitud AJAX:", error);
                }
            });
        } else {
            alert('Por favor, complete todos los campos');
        }
    });

    $('#updateEvent').on('click', function() {
        var eventId = $('#detailEventId').val();
        var eventTitle = $('#detailEventTitle').val();
        var eventDescription = $('#detailEventDescription').val();
        var eventDate = $('#detailEventDate').val();

        // Verificar que los campos obligatorios no estén vacíos
        if (eventTitle && eventDate) {
            $.ajax({
                url: '/instituto/Adman/modals/save_event.php',
                type: 'POST',
                data: {
                    action: 'updateEvent', // Indica la acción a realizar en PHP
                    eventId: eventId,
                    eventTitle: eventTitle,
                    eventDescription: eventDescription,
                    eventDate: eventDate
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#eventDetailModal').modal('hide'); // Ocultar el modal
                        Swal.fire({
                            icon: 'success',
                            title: 'Evento actualizado exitosamente',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(function() {
                            // Refrescar el calendario y recargar la página
                            calendar.refetchEvents();
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Hubo un problema al actualizar el evento',
                            text: response.message, // Mostrar mensaje de error si existe
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error en la solicitud AJAX:", error);
                    Swal.fire({
                            icon: 'success',
                            title: 'Evento actualizado exitosamente',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(function() {
                            // Refrescar el calendario y recargar la página
                            calendar.refetchEvents();
                            window.location.reload();
                        });
                }
            });
        } else {
            alert('Por favor, complete todos los campos');
        }
    });

    // Mostrar los detalles del evento al hacer clic en un evento en el calendario
    calendar.on('eventClick', function(info) {
        $('#detailEventId').val(info.event.id);
        $('#detailEventTitle').val(info.event.title);
        $('#detailEventDescription').val(info.event.extendedProps.description);
        $('#detailEventDate').val(info.event.start.toISOString().slice(0, 10));

        // Mostrar el modal con los detalles del evento
        $('#eventDetailModal').modal('show');
    });

    $('#deleteEvent').on('click', function() {
        var eventId = $('#detailEventId').val();

        // Confirmar la eliminación con un cuadro de diálogo
        Swal.fire({
            title: '¿Estás seguro?',
            text: 'No podrás revertir esto!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminarlo!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Realizar la solicitud AJAX para eliminar el evento
                $.ajax({
                    url: '/instituto/Adman/modals/save_event.php',
                    type: 'POST',
                    data: {
                        action: 'deleteEvent', // Indica la acción de eliminación
                        eventId: eventId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#eventDetailModal').modal('hide'); // Ocultar el modal
                            Swal.fire({
                                icon: 'success',
                                title: 'Evento eliminado exitosamente',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(function() {
                                // Actualizar el calendario y recargar la página
                                calendar.refetchEvents();
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Hubo un problema al eliminar el evento',
                                text: response.message, // Mostrar mensaje de error si existe
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error en la solicitud AJAX:", error);
                        Swal.fire({
                                icon: 'success',
                                title: 'Evento eliminado exitosamente',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(function() {
                                // Actualizar el calendario y recargar la página
                                calendar.refetchEvents();
                                window.location.reload();
                            });
                    }
                });
            }
        });
    });

    // Mostrar los detalles del evento al hacer clic en un evento en el calendario
    calendar.on('eventClick', function(info) {
        $('#detailEventId').val(info.event.id);
        $('#detailEventTitle').val(info.event.title);
        $('#detailEventDescription').val(info.event.extendedProps.description);
        $('#detailEventDate').val(info.event.start.toISOString().slice(0, 10));

        // Mostrar el modal con los detalles del evento
        $('#eventDetailModal').modal('show');
    });

   
});


</script>


<?php

require_once '../includes/footer.php';
?>
