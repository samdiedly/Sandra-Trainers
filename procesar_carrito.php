<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['producto_id'])) {
    $usuario_id = $_SESSION['id'];
    $producto_id = intval($_POST['producto_id']);

    // Conexión a la base de datos
    $conexion = new mysqli("localhost", "root", "password123", "tienda_deportiva");
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    // Verificar si el producto ya está en el carrito
    $sql = "SELECT cantidad FROM carrito WHERE usuario_id = ? AND producto_id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ii", $usuario_id, $producto_id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        // Si ya está en el carrito, aumentar la cantidad
        $sql = "UPDATE carrito SET cantidad = cantidad + 1 WHERE usuario_id = ? AND producto_id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ii", $usuario_id, $producto_id);
    } else {
        // Si no está en el carrito, agregarlo
        $sql = "INSERT INTO carrito (usuario_id, producto_id, cantidad) VALUES (?, ?, 1)";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ii", $usuario_id, $producto_id);
    }

    $stmt->execute();

    // Verifica si la operación fue exitosa
    if ($stmt->affected_rows > 0) {
        // Si se insertó o actualizó correctamente, redirige al carrito
        header("Location: carrito.php");
    } else {
        // Si ocurrió un error
        echo "Hubo un problema al agregar el producto al carrito.";
    }

    $stmt->close();
    $conexion->close();
    exit();
}
?>
