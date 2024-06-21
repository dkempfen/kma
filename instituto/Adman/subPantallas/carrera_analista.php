<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/instituto/Adman/includes/header.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/instituto/Includes/load.php';
?>


<main class="app-content">
    <div class="container">

        <section class="single-hero" style="background-image: url('../../Imagenes/portada_baberia.jpeg');
                background-size: cover;
                background-position: center calc(100% - 80%); /* Movemos la imagen hacia arriba en un 20% de la altura */
                height: 320px;">
            <div class="container-sistemas">
                <div class="row">
                    <div class="col-lg-7 col-sm-7 col-xs-12">
                        <div class="texts">
                            <h1 style="color: white; font-weight: bold; font-size: 36px; margin-top: 100px;">Barberia</h1>

                        </div>
                    </div>
                </div>
            </div>
        </section>



        <!-- Sección de Información del Programa -->
        <section class="container program-info" style="margin-bottom: 30px;">

            <div class="row">
                <div class="col-md-6">
                    <h3><i class="fas fa-graduation-cap"></i> Certificado:</h3>
                    <p>Barberia</p>

                    <h3><i class="far fa-clock"></i> Duración:</h3>
                    <p>-</p>
                </div>
                <div class="col-md-6">
                    <h3><i class="fas fa-book-open"></i> Temas:</h3>
                    <p>-</p>

                    <h3><i class="fas fa-certificate"></i> Acreditación:</h3>
                    <p>KMA</p>
                </div>
            </div>
        </section>
        <div class="row">
            <div class="col-md-6">
                <section class="container program-info" style="background-color: #fff">
                    <h2
                        style="border-bottom: 1px solid #ddd; padding-bottom: 10px; margin-top: 20px; display: inline-block;">
                        PLAN DE ESTUDIOS</h2>
                    <?php
                    // Realiza una conexión a la base de datos (debes configurar tus propios detalles de conexión)

                    $sql = "SELECT * FROM Materia m INNER JOIN Detalle_Plan dp ON m.id_Materia = dp.fk_Materia
                        WHERE dp.fk_Plan = 'CURBAR' ORDER BY Anio_Carrera ASC";
                    $resultado = $pdo->query($sql);

                    // Recorrer los resultados y generar la estructura HTML
                    echo '<div class="plan-de-estudios-container" style="max-height: 250px; overflow-y: auto;">';
                    foreach ($resultado as $fila) {
                        echo '<div class="col-lg-10 margen-inferior">';
                        echo '<input type="hidden" class="codigo-plan" value="' . $fila['id_Materia'] . '">';
                        echo '<div>' . '• ' . $fila['Descripcion'] . '</div>';
                        echo '</div>';
                    }
                    echo '</div>';

                    // Cierra la conexión a la base de datos
                    $pdo = null;
                    ?>
                </section>
            </div>
            <div class="col-md-6">
                <section class="container program-info">
                    <div class="pdf-container">
                        <iframe
                            src="https://docs.google.com/viewer?url=lujanbuenviaje.edu.ar/sistema/instituto/documentos/analista_sistemas.pdf&embedded=true"
                            style="width: 100%; height: 100%; border: none;"></iframe>
                    </div>
                </section>
            </div>
        </div>
    </div>

                    

</main>

<?php
require_once '../includes/footer.php';
?>