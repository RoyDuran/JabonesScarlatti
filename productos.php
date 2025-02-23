<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "conexion.php";
echo '<head><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"></head>';
// Consulta para contar el total de registros
$sqlTotalRegistros = "SELECT COUNT(*) FROM productos";
$stmtTotal = $conexion->query($sqlTotalRegistros);
$totalRegistros = $stmtTotal->fetchColumn();

// Definir cuántos registros por página mostrar
$registrosPorPagina = 1;

// Calcular el número total de páginas
$totalPaginas = ceil($totalRegistros / $registrosPorPagina);

//3 CALCULAR EL DESPLAZAMIENTO (OFFSET)
// Obtener la página actual desde la URL (por defecto es la página 1)
$paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;

// Asegurarse de que la página no sea menor que 1
if ($paginaActual < 1) {
    $paginaActual = 1;
}

// Calcular el desplazamiento (offset)
$offset = ($paginaActual - 1) * $registrosPorPagina;

//4 OBTENER LOS RESULTADOS CON LIMIT Y OFFSET
// Consulta para obtener los registros de la página actual con LIMIT y OFFSET
$sql = "SELECT * FROM productos LIMIT :limite OFFSET :offset";
$stmt = $conexion->prepare($sql);

// Vincular los parámetros
$stmt->bindParam(':limite', $registrosPorPagina, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

// Ejecutar la consulta
$stmt->execute();

// Obtener los resultados
$resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

//5 MOSTRAR LOS RESULTADOS
if (count($resultado) > 0) {
    echo "<div class='container mt-4'>"; // Contenedor principal

    echo "<div class='row justify-content-center'>"; // Centrado de tarjetas

    foreach ($resultado as $fila) {
        echo "<div class='col-md-6 col-lg-4 mb-4'>"; // Columnas responsivas con margen inferior

        echo "<div class='card shadow-lg p-3 rounded text-center'>"; // Tarjeta con sombra y centrado
        echo "<div class='position-relative' style='height: 250px; overflow: hidden;'>"; // Contenedor de imagen
        echo "<img src='" . $fila["imagen"] . "' alt='Jabón' class='card-img-top d-block mx-auto' style='height: 100%; width: 100%; object-fit: contain;'>"; // Imagen ajustada
        echo "</div>"; // Cierra el contenedor de imagen

        echo "<div class='card-body'>"; // Cuerpo de la tarjeta
        echo "<h4 class='card-title text-primary'>" . $fila["nombre"] . "</h4>";
        echo "<p class='card-text'>" . $fila["descripcion"] . "</p>";
        echo "<p class='fw-bold'>Peso: " . $fila["peso"] . " kg</p>";
        echo "<p class='fw-bold text-success'>Precio: " . $fila["precio"] . " €</p>";
        
        echo "<form action='cesta.php' method='post'>";
        echo "<input type='hidden' name='productoID' value='" . $fila["productoID"] . "'>";
        if($_SESSION["admin"]==true){
            echo "<button type='submit' class='btn btn-success w-100'>añadir</button>";
            echo "<form action='mostrarcesta.php'>";
            echo "<button type='submit' class='btn btn-success w-100'>añadir</button>";
            echo "</form>";
        }
        // Botón ancho completo
        echo "</form>";

        echo "</div>"; // Cierra card-body
        echo "</div>"; // Cierra card
        echo "</div>"; // Cierra columna
    }

    echo "</div>"; // Cierra row
    echo "</div>"; // Cierra container
} else {
    echo "<p class='text-center text-danger mt-4'>No se encontraron productos.</p>";
}


//6 CREAR LOS ENLACES PARA LA PAGINACION
// Mostrar los enlaces de paginación
echo "<nav aria-label='Page navigation'>";
echo "<ul class='pagination justify-content-center'>";

// Enlace para la página anterior
if ($paginaActual > 1) {
    echo "<li class='page-item'><a class='page-link' href='?pagina=" . ($paginaActual - 1) . "'>&laquo; Anterior</a></li>";
} else {
    echo "<li class='page-item disabled'><span class='page-link'>&laquo; Anterior</span></li>";
}

// Enlaces para cada página
for ($i = 1; $i <= $totalPaginas; $i++) {
    if ($i == $paginaActual) {
        echo "<li class='page-item active'><span class='page-link'>$i</span></li>";
    } else {
        echo "<li class='page-item'><a class='page-link' href='?pagina=$i'>$i</a></li>";
    }
}

// Enlace para la página siguiente
if ($paginaActual < $totalPaginas) {
    echo "<li class='page-item'><a class='page-link' href='?pagina=" . ($paginaActual + 1) . "'>Siguiente &raquo;</a></li>";
} else {
    echo "<li class='page-item disabled'><span class='page-link'>Siguiente &raquo;</span></li>";
}

echo "</ul>";
echo "</nav>";


?>
