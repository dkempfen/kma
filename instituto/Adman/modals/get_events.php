<?php
// Incluir carga de configuraciones y conexión a la base de datos

// Función para obtener eventos desde la base de datos y devolverlos como JSON
function obtenerEventosDesdeBaseDeDatos() {
    global $pdo;

    try {
        // Preparar la consulta para obtener eventos
        $query = "SELECT id, title, description, date AS start FROM eventos"; // Renombrar `date` como `start` para que FullCalendar lo entienda como fecha de inicio
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $eventos; // Devolver eventos como un arreglo asociativo
    } catch (PDOException $e) {
        // Manejo de errores
        http_response_code(500); // Código de error 500
        die(json_encode(array('error' => 'Error al obtener eventos: ' . $e->getMessage())));
    }
}

// Establecer encabezado para JSON
header('Content-Type: application/json');

// Devolver los eventos como respuesta JSON
echo json_encode(obtenerEventosDesdeBaseDeDatos());





