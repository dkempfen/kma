<?php
// Incluye SweetAlert2
echo '
    <head>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.15.5/dist/sweetalert2.min.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10.15.5/dist/sweetalert2.min.css">
    </head>
';

// Incluye los archivos necesarios y carga la conexión a la base de datos
require_once $_SERVER['DOCUMENT_ROOT'] . '/instituto/Includes/load.php';

// Función para insertar un evento en la base de datos
function insertarEvento($title, $description, $date) {
    global $pdo;
    $response = array();

    try {
        // Verificar que los datos no estén vacíos
        if (empty($title) || empty($date)) {
            $response['success'] = false;
            $response['message'] = 'El título y la fecha son requeridos.';
        } else {
            // Preparar la consulta para insertar el evento
            $query = "INSERT INTO eventos (title, description, date) VALUES (:title, :description, :date)";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':date', $date);

            // Ejecutar la consulta
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Evento insertado correctamente.';
                
                // Obtener el ID del evento insertado
                $event_id = $pdo->lastInsertId();
                
                // Consulta para obtener el evento insertado
                $query_select = "SELECT id, title, description, date AS start FROM eventos WHERE id = :event_id";
                $stmt_select = $pdo->prepare($query_select);
                $stmt_select->bindParam(':event_id', $event_id, PDO::PARAM_INT);
                $stmt_select->execute();
                $evento_insertado = $stmt_select->fetch(PDO::FETCH_ASSOC);
                
                $response['evento'] = $evento_insertado; // Agregar el evento insertado a la respuesta
            } else {
                // Obtener información de error
                $errorInfo = $stmt->errorInfo();
                $response['success'] = false;
                $response['message'] = 'Error al insertar el evento: ' . $errorInfo[2];
            }
        }
    } catch (PDOException $e) {
        // En caso de un error en la base de datos
        $response['success'] = false;
        $response['message'] = 'Error en la base de datos al insertar el evento: ' . $e->getMessage();
    }

    return $response;
}

// Manejo de solicitud POST para insertar evento
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['title']) && isset($_POST['date'])) {
    $title = $_POST['title'];
    $description = isset($_POST['description']) ? $_POST['description'] : '';
    $date = $_POST['date'];

    $response = insertarEvento($title, $description, $date);

    // Devolver la respuesta al cliente
    echo json_encode($response);
    exit();
} 


// Función para actualizar un evento en la base de datos
function actualizarEvento($eventId, $eventTitle, $eventDescription, $eventDate)
{
    global $pdo; // Asegúrate de tener la conexión PDO establecida de manera global

    $response = array();

    try {
        // Preparar la consulta SQL para actualizar el evento
        $stmt = $pdo->prepare("UPDATE eventos SET title = :title, description = :description, date = :date WHERE id = :id");
        $stmt->bindParam(':id', $eventId);
        $stmt->bindParam(':title', $eventTitle);
        $stmt->bindParam(':description', $eventDescription);
        $stmt->bindParam(':date', $eventDate);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Evento actualizado correctamente.';
        } else {
            $response['success'] = false;
            $response['message'] = 'Error al actualizar el evento.';
        }

    } catch (PDOException $e) {
        // Capturar cualquier error de PDO y devolver una respuesta JSON de error
        $response['success'] = false;
        $response['message'] = 'Error al actualizar el evento: ' . $e->getMessage();
    }

    return json_encode($response);
}


// Verificar si se recibió una solicitud POST válida para actualizar el evento
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'updateEvent') {
    // Obtener los datos del POST
    $eventId = $_POST['eventId'];
    $eventTitle = $_POST['eventTitle'];
    $eventDescription = $_POST['eventDescription'];
    $eventDate = $_POST['eventDate'];

    // Llamar a la función para actualizar el evento y devolver la respuesta JSON
    echo actualizarEvento($eventId, $eventTitle, $eventDescription, $eventDate);
}

function eliminarEvento($eventId)
{
    global $pdo;

    $response = array();

    try {
        // Preparar la consulta SQL para eliminar el evento
        $stmt = $pdo->prepare("DELETE FROM eventos WHERE id = :id");
        $stmt->bindParam(':id', $eventId);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Evento eliminado correctamente.';
        } else {
            $response['success'] = false;
            $response['message'] = 'Error al eliminar el evento.';
        }

    } catch (PDOException $e) {
        // Capturar cualquier error de PDO y devolver una respuesta JSON de error
        $response['success'] = false;
        $response['message'] = 'Error al eliminar el evento: ' . $e->getMessage();
    }

    return json_encode($response);
}

// Verificar si se recibió una solicitud POST válida para eliminar el evento
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'deleteEvent') {
    // Obtener los datos del POST
    $eventId = $_POST['eventId'];

    // Llamar a la función para eliminar el evento y devolver la respuesta JSON
    echo eliminarEvento($eventId);
}