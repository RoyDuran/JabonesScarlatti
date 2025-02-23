<?php
$host = 'localhost'; // Dirección del servidor
$dbname = 'jabonesscarlatti'; // Nombre de la base de datos
$username = 'root'; // Usuario de la base de datos
$password = ''; // Contraseña del usuario

try {
    // Establecer conexión
    $conexion = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    
    // Configurar el manejo de errores de PDO
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
}
?>
