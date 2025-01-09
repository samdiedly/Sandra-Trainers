<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['id'];

$conexion = new mysqli("localhost", "root", "password123", "tienda_deportiva");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Manejar la eliminación de productos del carrito
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['eliminar_id'])) {
    $eliminar_id = intval($_POST['eliminar_id']);
    $sql = "DELETE FROM carrito WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $eliminar_id);
    $stmt->execute();
    $stmt->close();
}

// Manejar la confirmación de compra
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirmar_compra'])) {
    // Aquí puedes realizar las acciones necesarias, como registrar la compra en otra tabla
    $sql = "DELETE FROM carrito WHERE usuario_id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $stmt->close();
    $compra_realizada = true;
}

// Consultar los productos del carrito del usuario
$sql = "SELECT c.id AS carrito_id, p.nombre, p.descripcion, p.precio, c.cantidad 
        FROM carrito c
        JOIN productos p ON c.producto_id = p.id
        WHERE c.usuario_id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();

// Depuración: Verificar si hay productos en el carrito
if ($resultado->num_rows == 0 && !isset($compra_realizada)) {
    echo "<p style='color: red;'>No hay productos en tu carrito. Esto puede deberse a un problema en la base de datos o en el proceso de agregar productos.</p>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carrito de Compras</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
    </style>
    <script>
        function confirmarCompra() {
            return confirm("¿Estás seguro de que deseas confirmar la compra?");
        }
    </script>
</head>
<body>
    <h1>Carrito de Compras</h1>
    <a href="index.php">Volver al Catálogo</a>
    <hr>

    <?php if (isset($compra_realizada)): ?>
        <p style="color: green;">¡Compra realizada con éxito!</p>
    <?php endif; ?>

    <?php if ($resultado->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Total</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total_general = 0;
                while ($producto = $resultado->fetch_assoc()):
                    $total = $producto['precio'] * $producto['cantidad'];
                    $total_general += $total;
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($producto['descripcion']); ?></td>
                        <td>$<?php echo number_format($producto['precio'], 2); ?></td>
                        <td><?php echo $producto['cantidad']; ?></td>
                        <td>$<?php echo number_format($total, 2); ?></td>
                        <td>
                            <form method="POST" action="carrito.php">
                                <input type="hidden" name="eliminar_id" value="<?php echo $producto['carrito_id']; ?>">
                                <button type="submit">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <p><strong>Total General: $<?php echo number_format($total_general, 2); ?></strong></p>

        <!-- Botón para confirmar compra -->
        <form method="POST" action="carrito.php" onsubmit="return confirmarCompra();">
            <input type="hidden" name="confirmar_compra" value="1">
            <button type="submit">Confirmar Compra</button>
        </form>
    <?php else: ?>
        <p>Tu carrito está vacío.</p>
    <?php endif; ?>

    <?php
    $stmt->close();
    $conexion->close();
    ?>
</body>
</html>

