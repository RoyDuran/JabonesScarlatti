<?php
session_start();
include 'conexion.php'; // Conectar a la BD

// Verifica si el usuario está autenticado
if (!isset($_SESSION["email"])) {
    echo "<div class='alert alert-danger text-center mt-4'>Debes iniciar sesión para añadir productos a la cesta.</div>";
    exit();
}

$email = $_SESSION["email"];
$productoID = isset($_POST["productoID"]) ? (int)$_POST["productoID"] : 0;

if ($productoID === 0) {
    echo "<div class='alert alert-danger text-center mt-4'>Producto no válido.</div>";
    exit();
}

try {
    // 1. Buscar la cesta activa del usuario
    $stmt = $conexion->prepare("SELECT cestaID FROM cesta WHERE email = ? ORDER BY fechaCreacion DESC LIMIT 1");
    $stmt->execute([$email]);
    $cesta = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cesta) {
        // Si no hay cesta, se crea una nueva
        $stmt = $conexion->prepare("INSERT INTO cesta (email, fechaCreacion) VALUES (?, NOW())");
        $stmt->execute([$email]);
        $cestaID = $conexion->lastInsertId();
    } else {
        $cestaID = $cesta["cestaID"];
    }

    // 2. Contar cuántos ítems hay en la cesta
    $stmt = $conexion->prepare("SELECT COUNT(*) FROM itemcesta WHERE cestaID = ?");
    $stmt->execute([$cestaID]);
    $numItems = $stmt->fetchColumn();

    if ($numItems >= 2) {
        // Si ya hay 2 ítems, no se puede añadir más
        echo "<div class='alert alert-warning text-center mt-4'>No puedes añadir más de dos productos a la cesta.</div>";
        exit();
    }

    // 3. Agregar el producto a la cesta (independientemente de si es repetido o no)
    $stmt = $conexion->prepare("INSERT INTO itemcesta (cestaID, productoID, cantidad) VALUES (?, ?, 1)");
    $stmt->execute([$cestaID, $productoID]);

    // Redirigir a la página de productos
    header("Location: productos.php");
    exit();
} catch (PDOException $e) {
    echo "<div class='alert alert-danger text-center mt-4'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
}
?>
