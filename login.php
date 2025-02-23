<?php
include 'conexion.php'; //si la sesion no ha sido iniciada comprobamos las credenciales
if (!empty($_POST["email"]) && !empty($_POST["clave"])) {
    $email = $_POST["email"];
    $clave = $_POST["clave"];
}
try {
    //creamos la consulta preparada
    $consulta = "SELECT * FROM clientes WHERE email LIKE ? AND clave LIKE ?";
    $stmt = $conexion->prepare($consulta);

    //le pasamos los parametros
    $stmt->bindParam(1, $email, PDO::PARAM_STR);
    $stmt->bindParam(2, $clave, PDO::PARAM_STR);

    //ejecutamos la consulta
    $stmt->execute();
    $fila = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar si hay resultados
    if ($fila) {
        session_start();
        $_SESSION["email"] = $email;
        $_SESSION["admin"] = $fila["admin"];
        header("location:productos.php");
    } else echo "";
} catch (PDOException $e) {
    echo "<p class='mensaje-error'>Error en la conexión: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
