<?php
session_start();
include 'conexion.php'; // Conectar a la BD

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION["email"])) {
    echo "<div class='alert alert-danger text-center mt-4'>Debes iniciar sesión para ver tu cesta.</div>";
    exit();
}

$email = $_SESSION["email"];

try {
    // 1. Buscar la cesta activa del usuario
    $stmt = $conexion->prepare("SELECT cestaID FROM cesta WHERE email = ? ORDER BY fechaCreacion DESC LIMIT 1");
    $stmt->execute([$email]);
    $cesta = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cesta) {
        echo "<div class='alert alert-warning text-center mt-4'>Tu cesta está vacía.</div>";
        exit();
    }

    $cestaID = $cesta["cestaID"];

    // 2. Obtener los productos de la cesta
    $stmt = $conexion->prepare("
        SELECT p.nombre, p.imagen, p.descripcion, p.precio, i.cantidad 
        FROM itemcesta i
        JOIN productos p ON i.productoID = p.productoID
        WHERE i.cestaID = ?
    ");
    $stmt->execute([$cestaID]);
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$productos) {
        echo "<div class='alert alert-warning text-center mt-4'>Tu cesta está vacía.</div>";
        exit();
    }

    // 3. Mostrar la cesta con estilos de Bootstrap
    echo "<div class='container mt-5'>";
    echo "<h2 class='text-primary text-center mb-4'>Tu Cesta</h2>";
    echo "<div class='row justify-content-center'>";
    
    foreach ($productos as $producto) {
        echo "<div class='col-md-4'>";
        echo "<div class='card shadow mb-4'>";
        echo "<img src='" . $producto["imagen"] . "' class='card-img-top' style='height: 200px; object-fit: cover;' alt='Imagen del producto'>";
        echo "<div class='card-body'>";
        echo "<h5 class='card-title text-primary'>" . htmlspecialchars($producto["nombre"]) . "</h5>";
        echo "<p class='card-text'>" . htmlspecialchars($producto["descripcion"]) . "</p>";
        echo "<p class='fw-bold'>Precio: " . htmlspecialchars($producto["precio"]) . " €</p>";
        echo "<p class='fw-bold'>Cantidad: " . htmlspecialchars($producto["cantidad"]) . "</p>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }

    echo "</div>"; // Cierra row
    echo "</div>"; // Cierra container

} catch (PDOException $e) {
    echo "<div class='alert alert-danger text-center mt-4'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
}
?>
